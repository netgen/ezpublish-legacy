#!/usr/bin/env php
<?php
/**
 * File containing the ezcsvexport.php bin script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( ['description' => ( "eZ Publish CSV export script\n" .
                                                        "\n" .
                                                        "ezcsvexport.php --storage-dir=export 2" ), 'use-session' => false, 'use-modules' => true, 'use-extensions' => true, 'user' => true] );

$script->startup();

$options = $script->getOptions( "[storage-dir:]",
                                "[node]",
                                ['storage-dir' => 'directory to place exported files in'],
                                false,
                                ['user' => true] );
$script->initialize();

if ( (is_countable($options['arguments']) ? count( $options['arguments'] ) : 0) < 1 )
{
    $cli->error( 'Specify a node to export' );
    $script->shutdown( 1 );
}

$nodeID = $options['arguments'][0];

if ( !is_numeric( $nodeID ) )
{
    $cli->error( 'Specify a numeric node ID' );
    $script->shutdown( 2 );
}

if ( $options['storage-dir'] )
{
    $storageDir = $options['storage-dir'];
}
else
{
    $storageDir = '';
}

$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( !$node )
{
    $cli->error( "No node with ID: $nodeID" );
    $script->shutdown( 3 );
}

$cli->output( "Going to export subtree from node $nodeID to directory $storageDir \n" );

$subTreeCount = $node->subTreeCount();

$script->setIterationData( '.', '~' );

$script->resetIteration( $subTreeCount );

$subTree = $node->subTree();
$openedFPs = [];

foreach ( $subTree as $key => $childNode )
{
    $status = true;

    $object = $childNode->attribute( 'object' );

    $classIdentifier = $object->attribute( 'class_identifier' );

    if ( !isset( $openedFPs[$classIdentifier] ) )
    {
        $tempFP = @fopen( $storageDir . '/' . $classIdentifier . '.csv', "w" );
        if ( $tempFP )
        {
            $openedFPs[$classIdentifier] = $tempFP;
        }
        else
        {
            $cli->error( "Can not open output file for $classIdentifier class" );
            $script->shutdown( 4 );
        }
    }
    else
    {
        if ( !$openedFPs[$classIdentifier] )
        {
            $cli->error( "Can not open output file for $classIdentifier class" );
            $script->shutdown( 4 );
        }
    }

    $fp = $openedFPs[$classIdentifier];

    $objectData = [];
    foreach ( $object->attribute( 'contentobject_attributes' ) as $attribute )
    {
        $attributeStringContent = $attribute->toString();

        if ( $attributeStringContent != '' )
        {
            switch ( $datatypeString = $attribute->attribute( 'data_type_string' ) )
            {
                case 'ezimage':
                {
                    $imagePathParts = explode( '/', (string) $attributeStringContent );
                    $imageFile = array_pop( $imagePathParts );
                    // here it would be nice to add a check if such file allready exists
                    $success = eZFileHandler::copy( $attributeStringContent, $storageDir . '/' . $imageFile );
                    if ( !$success )
                    {
                        $status = false;
                    }
                    $attributeStringContent = $imageFile;
                } break;

                case 'ezbinaryfile':
                case 'ezmedia':
                {
                    $binaryData = explode( '|', (string) $attributeStringContent );
                    $success = eZFileHandler::copy( $binaryData[0], $storageDir . '/' . $binaryData[1] );
                    if ( !$success )
                    {
                        $status = false;
                    }
                    $attributeStringContent = $binaryData[1];
                } break;

                default:
            }
        }

        $objectData[] = $attributeStringContent;
    }

    if ( !$fp )
    {
        $cli->error( "Can not open output file" );
        $script->shutdown( 5 );
    }

    if ( !fputcsv( $fp, $objectData, ';' ) )
    {
        $cli->error( "Can not write to file" );
        $script->shutdown( 6 );
    }

    $script->iterate( $cli, $status );
}

foreach ( $openedFPs as $fp )
{
    fclose( $fp );
}

$script->shutdown();

?>
