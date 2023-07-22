<?php
/**
 * File containing the indexcontent.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$cli->output( "Starting processing pending search engine modifications" );

$contentObjects = [];
$db = eZDB::instance();

$offset = 0;
$limit = 50;

$searchEngine = eZSearch::getEngine();

if ( !$searchEngine instanceof ezpSearchEngine )
{
    $cli->error( "The configured search engine does not implement the ezpSearchEngine interface or can't be found." );
    $script->shutdown( 1 );
}

$needRemoveWithUpdate = $searchEngine->needRemoveWithUpdate();

while( true )
{
    $entries = $db->arrayQuery(
        "SELECT param, action FROM ezpending_actions WHERE action = 'index_object' OR action = 'index_moved_node' GROUP BY param, action ORDER BY min(created)",
        ['limit' => $limit, 'offset' => $offset]
    );

    if ( is_array( $entries ) && count( $entries ) != 0 )
    {
        foreach ( $entries as $entry )
        {
            $objectID = (int)$entry['param'];
            $action = $entry['action'];

            $cli->output( "\tIndexing object ID #$objectID" );
            $db->begin();
            $object = eZContentObject::fetch( $objectID );
            $removeFromPendingActions = true;
            if ( $object )
            {
                if ( $needRemoveWithUpdate )
                {
                    $searchEngine->removeObject( $object, false );
                }

                $removeFromPendingActions = $searchEngine->addObject( $object, false );

                // When moving content (and only, because of performances), reindex the subtree
                if ( $action == 'index_moved_node' )
                {
                    $nodeId = $object->attribute( 'main_node_id' );
                    $node = eZContentObjectTreeNode::fetch( $nodeId );

                    if ( !( $node instanceof eZContentObjectTreeNode ) )
                    {
                        $cli->error( "An error occured while trying fetching node $nodeId" );
                        $db->query( "DELETE FROM ezpending_actions WHERE action = '$action' AND param = '$objectID'" );
                        $db->commit();
                        continue;
                    }

                    $subtreeOffset = 0;
                    $subtreeLimit = 50;

                    $params = ['Limitation' => [], 'MainNodeOnly' => true];

                    $subtreeCount = $node->subTreeCount( $params );

                    while ( $subtreeOffset < $subtreeCount )
                    {
                        $subTree = $node->subTree(
                            [...$params, 'Offset' => $subtreeOffset, 'Limit' => $subtreeLimit, 'SortBy' => []]
                        );

                        if ( !empty( $subTree ) )
                        {
                            foreach ( $subTree as $innerNode )
                            {
                                /** @var $innerNode eZContentObjectTreeNode */
                                $childObject = $innerNode->attribute( 'object' );
                                if ( !$childObject )
                                {
                                    continue;
                                }

                                $searchEngine->addObject( $childObject, false );

                                // clear object cache to conserve memory
                                eZContentObject::clearCache();
                            }
                        }

                        $subtreeOffset += $subtreeLimit;

                        if ( $subtreeOffset >= $subtreeCount )
                        {
                            break;
                        }
                    }
                }
            }

            if ( $removeFromPendingActions )
            {
                $db->query( "DELETE FROM ezpending_actions WHERE action = '$action' AND param = '$objectID'" );
                eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
            }
            else
            {
                $cli->warning( "\tFailed indexing object ID #$objectID, keeping it in the queue." );
                // Increase the offset to skip failing objects
                ++$offset;
            }

            $db->commit();
        }

        $searchEngine->commit();
        // clear object cache to conserve memory
        eZContentObject::clearCache();
    }
    else
    {
        break; // No valid result from ezpending_actions
    }
}

$cli->output( "Done" );

?>
