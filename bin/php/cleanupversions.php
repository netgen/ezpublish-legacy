#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup versions according content.ini/[VersionManagement]/DefaultVersionHistoryLimit
 * and VersionHistoryClass settings
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 */

require_once 'autoload.php';

$cli = eZCLI::instance();

$script = eZScript::instance(
    ['description' => "Remove archived content object versions according to "
        . "[VersionManagement/DefaultVersionHistoryLimit and "
        . "[VersionManagement]/VersionHistoryClass settings", 'use-session' => false, 'use-modules' => true, 'use-extensions' => true]
);
$script->startup();
$options = $script->getOptions( "[n]", "", ["n" => "Do not wait"] );
$script->initialize();

if ( !isset( $options['n'] ) )
{
    $cli->warning( "This cleanup script is going to remove archived versions according to the settings" );
    $cli->warning( "content.ini/[VersionManagement]/DefaultVersionHistoryLimit and content.ini/[VersionManagement]/VersionHistoryClass" );
    $cli->warning();
    $cli->warning( "You have 10 seconds to break the script (press Ctrl-C)" );
    sleep( 10 );
    $cli->output();
}

$subTreeParams = ['Limitation' => [], 'MainNodeOnly' => true, 'LoadDataMap' => false, 'IgnoreVisibility' => true];
$total = eZContentObjectTreeNode::subTreeCountByNodeID( $subTreeParams, 1 );
$cli->output( "{$total} objects to check... (In the progess bar, 'R' means that at least a version was removed)" );

$script->setIterationData( 'R', '.' );
$script->resetIteration( $total );

$subTreeParams['Offset'] = 0;
$subTreeParams['Limit'] = 100;
$db = eZDB::instance();

while ( true )
{
    $nodes = eZContentObjectTreeNode::subTreeByNodeID( $subTreeParams, 1 );
    if ( empty( $nodes ) )
    {
        break;
    }
    foreach( $nodes as $node )
    {
        $object = $node->attribute( 'object' );
        $versionCount = $object->getVersionCount();
        $versionLimit = eZContentClass::versionHistoryLimit( $object->attribute( 'content_class' ) );
        if ( $versionCount <= $versionLimit )
        {
            $script->iterate( $cli, false, "Nothing to do on object #{$object->attribute( 'id' )}" );
            continue;
        }

        $versionsToRemove = $versionCount - $versionLimit;
        $removedVersions = 0;
        $batchVersionsToRemove = 20;
        while ( $removedVersions < $versionsToRemove )
        {
            $versions = $object->versions( true, ['conditions' => ['status' => eZContentObjectVersion::STATUS_ARCHIVED], 'sort' => ['modified' => 'asc'], 'limit' => ['limit' => $batchVersionsToRemove, 'offset' => $removedVersions]] );

            $db->begin();
            foreach( $versions as $version )
            {
                $version->removeThis();
                $removedVersions++;
            }
            $db->commit();
        }

        if ( $removedVersions > 0 )
        {
            $script->iterate( $cli, true, "Removed {$removedVersions} archived versions of object #{$object->attribute( 'id' )}" );
        }
        else
        {
            $script->iterate( $cli, false, "No archived version of object #{$object->attribute( 'id' )} found" );
        }
    }
    $subTreeParams['Offset'] += $subTreeParams['Limit'];
    eZContentObject::clearCache();
}

$script->shutdown();

?>
