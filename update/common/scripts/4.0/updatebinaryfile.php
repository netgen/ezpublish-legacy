#!/usr/bin/env php
<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

$cli = eZCLI::instance();

$script = eZScript::instance( ['description' => ( "\nAdds the file extension suffix to the files stored by the binary file datatype\n" .
                                                        "where it is currently missing.\n" ), 'use-session' => false, 'use-modules' => false, 'use-extensions' => true] );

$script->startup();

$options = $script->getOptions( '', '', [] );
$script->initialize();

$limit = 20;
$offset = 0;

$db = eZDB::instance();

$script->setIterationData( '.', '~' );

$updateDoneForId = [];

while ( $binaryFiles = eZPersistentObject::fetchObjectList( eZBinaryFile::definition(), null, null, null, ['offset' => $offset, 'limit' => $limit] ) )
{
    foreach ( $binaryFiles as $binaryFile )
    {
        $fileName = $binaryFile->attribute( 'filename' );

        if ( str_contains( (string) $fileName, '.' ) )
        {
            $text = "skipping $fileName, it contains a suffix";
            $script->iterate( $cli, true, $text );
            continue;
        }

        $suffix = eZFile::suffix( $binaryFile->attribute( 'original_filename' ) );

        if ( $suffix )
        {
            $newFileName = $fileName . '.' . $suffix;

            $db->begin();

            $oldFilePath = $binaryFile->attribute( 'filepath' );

            $binaryFile->setAttribute( 'filename', $newFileName );
            $binaryFile->store();

            $newFilePath = $binaryFile->attribute( 'filepath' );

            $file = eZClusterFileHandler::instance( $oldFilePath );
            $newFile = eZClusterFileHandler::instance( $newFilePath );
            if ( isset( $updateDoneForId[$binaryFile->attribute( 'contentobject_attribute_id' )] ) ||
                 $newFile->exists() )
            {
                // The file has been renamed already, do nothing here.
            }
            else if ( $file->exists() )
            {
                $text = "renamed $fileName to $newFileName";
                $file->move( $newFilePath );
                $updateDoneForId[$binaryFile->attribute( 'contentobject_attribute_id' )] = true;
            }
            else
            {
                $text = "file not found: $oldFilePath";
                $script->iterate( $cli, false, $text );
                $db->rollback();
                continue;
            }

            $db->commit();
        }
        else
        {
            $text = "skipping $fileName, original file name does not contain a suffix";
        }

        $script->iterate( $cli, true, $text );
    }

    $offset += $limit;
}

$script->shutdown();

?>
