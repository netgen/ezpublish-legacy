<?php
/**
 * File containing the eZBinaryFile class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMedia ezmedia.php
  \ingroup eZDatatype
  \brief The class eZMedia handles registered media files

*/

class eZMedia extends eZPersistentObject
{
    static function definition()
    {
        static $definition = ["fields" => ["contentobject_attribute_id" => ['name' => "ContentObjectAttributeID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObjectAttribute', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "version" => ['name' => "Version", 'datatype' => 'integer', 'default' => 0, 'required' => true], "filename" => ['name' => "Filename", 'datatype' => 'string', 'default' => '', 'required' => true], "original_filename" => ['name' => "OriginalFilename", 'datatype' => 'string', 'default' => '', 'required' => true], "mime_type" => ['name' => "MimeType", 'datatype' => 'string', 'default' => '', 'required' => true], "width" => ['name' => "Width", 'datatype' => 'integer', 'default' => 0, 'required' => true], "height" => ['name' => "Height", 'datatype' => 'integer', 'default' => 0, 'required' => true], "has_controller" => ['name' => "HasController", 'datatype' => 'integer', 'default' => 0, 'required' => true], "controls" => ['name' => "Controls", 'datatype' => 'string', 'default' => '', 'required' => true], "is_autoplay" => ['name' => "IsAutoplay", 'datatype' => 'integer', 'default' => 0, 'required' => true], "pluginspage" => ['name' => "Pluginspage", 'datatype' => 'string', 'default' => '', 'required' => true], "quality" => ['name' => 'Quality', 'datatype' => 'string', 'default' => '', 'required' => true], "is_loop" => ['name' => "IsLoop", 'datatype' => 'integer', 'default' => 0, 'required' => true]], "keys" => ["contentobject_attribute_id", "version"], 'function_attributes' => ['filesize' => 'filesize', 'filepath' => 'filepath', 'mime_type_category' => 'mimeTypeCategory', 'mime_type_part' => 'mimeTypePart'], "relations" => ["contentobject_attribute_id" => ["class" => "ezcontentobjectattribute", "field" => "id"], "version" => ["class" => "ezcontentobjectattribute", "field" => "version"]], "class_name" => "eZMedia", "name" => "ezmedia"];
        return $definition;
    }

    function fileSize()
    {
        $fileInfo = $this->storedFileInfo();

        $file = eZClusterFileHandler::instance( $fileInfo['filepath'] );

        if ( $file->exists() )
        {
            return $file->size();
        }

        return 0;
    }

    function filePath()
    {
        $fileInfo = $this->storedFileInfo();
        return $fileInfo['filepath'];
    }

    function mimeTypeCategory()
    {
        $types = explode( "/", (string) $this->attribute( "mime_type" ) );
        return $types[0];
    }

    function mimeTypePart()
    {
        $types = explode( "/", (string) $this->attribute( "mime_type" ) );
        return $types[1];
    }

    static function create( $contentObjectAttributeID, $version )
    {
        $row = ["contentobject_attribute_id" => $contentObjectAttributeID, "version" => $version, "filename" => "", "original_filename" => "", "mime_type" => "", "width" => "0", "height" => "0", "controller" => true, "autoplay" => true, "pluginspage" => "", "is_loop" => false, "quality" => "", "controls" => ""];
        return new eZMedia( $row );
    }

    static function fetch( $id, $version, $asObject = true )
    {
        if( $version == null )
        {
            return eZPersistentObject::fetchObjectList( eZMedia::definition(),
                                                        null,
                                                        ["contentobject_attribute_id" => $id],
                                                        null,
                                                        null,
                                                        $asObject );
        }
        else
        {
            return eZPersistentObject::fetchObject( eZMedia::definition(),
                                                    null,
                                                    ["contentobject_attribute_id" => $id, "version" => $version],
                                                    $asObject );
        }
    }

    static function fetchByFileName( $filename, $version = null, $asObject = true )
    {
        if ( $version == null )
        {
            return eZPersistentObject::fetchObjectList( eZMedia::definition(),
                                                        null,
                                                        ['filename' => $filename],
                                                        null,
                                                        null,
                                                        $asObject );
        }
        else
        {
            return eZPersistentObject::fetchObject( eZMedia::definition(),
                                                    null,
                                                    ['filename' => $filename, 'version' => $version],
                                                    $asObject );
        }
    }

    /**
     * Fetch media objects by content object id
     * @param int $contentObjectID contentobject id
     * @param string $languageCode language code
     * @param boolean $asObject if return object
     * @return array
     */
    static function fetchByContentObjectID( $contentObjectID, $languageCode = null, $asObject = true )
    {
        $condition = [];
        $condition['contentobject_id'] = $contentObjectID;
        $condition['data_type_string'] = 'ezmedia';
        if ( $languageCode != null )
        {
            $condition['language_code'] = $languageCode;
        }
        $custom = [['operation' => 'DISTINCT id', 'name' => 'id']];
        $ids = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(),
                                             [],
                                             $condition,
                                             null,
                                             null,
                                             false,
                                             false,
                                             $custom );
        $mediaFiles = [];
        foreach ( $ids as $id )
        {
            $mediaFileObjectAttribute = eZMedia::fetch( $id['id'], null, $asObject );
            $mediaFiles = array_merge( $mediaFiles, $mediaFileObjectAttribute );
        }
        return $mediaFiles;
    }

    static function removeByID( $id, $version )
    {
        if( $version == null )
        {
            eZPersistentObject::removeObject( eZMedia::definition(),
                                              ["contentobject_attribute_id" => $id] );
        }
        else
        {
            eZPersistentObject::removeObject( eZMedia::definition(),
                                              ["contentobject_attribute_id" => $id, "version" => $version] );
        }
    }

    function storedFileInfo()
    {
        $fileName = $this->attribute( 'filename' );
        $mimeType = $this->attribute( 'mime_type' );
        $originalFileName = $this->attribute( 'original_filename' );

        $storageDir = eZSys::storageDirectory();

        $group = '';
        $type = '';
        if ( $mimeType )
            [$group, $type] = explode( '/', (string) $mimeType );

        $filePath = $storageDir . '/original/' . $group . '/' . $fileName;

        return ['filename' => $fileName, 'original_filename' => $originalFileName, 'filepath' => $filePath, 'mime_type' => $mimeType];
    }

    public $ContentObjectAttributeID;
    public $Filename;
    public $OriginalFilename;
    public $MimeType;
    public $Width;
    public $Height;
    public $HasController;
    public $Controls;
    public $IsLoop;
    public $IsAutoplay;
    public $Pluginspage;
    public $Quality;
}

?>
