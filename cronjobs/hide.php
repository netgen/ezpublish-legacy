<?php
/**
 * File containing the hide.php cronjob.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$ini = eZINI::instance( 'content.ini' );
$rootNodeIDList = $ini->variable( 'HideSettings','RootNodeList' );
$hideAttributeArray = $ini->variable( 'HideSettings', 'HideDateAttributeList' );

$currentDate = time();

eZINI::instance()->setVariable( 'SiteAccessSettings', 'ShowHiddenNodes', 'false' );

$hiddenNodesParams = ['LoadDataMap' => false, 'Limit' => 50, 'SortBy' => [['published', true]]];

foreach ( $rootNodeIDList as $nodeID )
{
    $rootNode = eZContentObjectTreeNode::fetch( $nodeID );
    $cli->output( 'Hiding content of node "' . $rootNode->attribute( 'name' ) . '" (' . $nodeID . ')' );
    $cli->output();

    foreach ( $hideAttributeArray as $hideClass => $attributeIdentifier )
    {
        $countParams = ['ClassFilterType' => 'include', 'ClassFilterArray' => [$hideClass], 'Limitation' => [], 'AttributeFilter' => ['and', ["{$hideClass}/{$attributeIdentifier}", '<=', $currentDate], ["{$hideClass}/$attributeIdentifier", '>', 0]]];

        $nodeArrayCount = $rootNode->subTreeCount( $countParams );
        if ( $nodeArrayCount > 0 )
        {
            $cli->output( "Hiding {$nodeArrayCount} node(s) of class {$hideClass}." );

            do
            {
                $nodeArray = $rootNode->subTree( $hiddenNodesParams + $countParams );

                foreach ( $nodeArray as $node )
                {
                    $cli->output( 'Hiding node: "' . $node->attribute( 'name' ) . '" (' . $node->attribute( 'node_id' ) . ')' );
                    eZContentObjectTreeNode::hideSubTree( $node );

                    //call appropriate method from search engine
                    eZSearch::updateNodeVisibility( $node->attribute( 'node_id' ), 'hide' );
                }
                // clear memory after every batch
                eZContentObject::clearCache();
            } while ( is_array( $nodeArray ) && !empty( $nodeArray ) );

            $cli->output();
        }
        else
        {
            $cli->output( "Nothing to hide." );
        }
    }

    $cli->output();
}

?>
