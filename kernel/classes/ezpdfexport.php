<?php
/**
 * File containing the eZPDFExport class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPDFExport ezpdfexport.php
  \brief class for storing PDF exports

  eZPDFExport is used to create PDF exports from published content. See kernel/pdf for more files.
*/

class eZPDFExport extends eZPersistentObject
{
    final public const VERSION_VALID = 0;
    final public const VERSION_DRAFT = 1;

    final public const CREATE_ONCE = 1;
    final public const CREATE_ONFLY = 2;

    static function definition()
    {
        return ['fields' => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'title' => ['name' => 'Title', 'datatype' => 'string', 'default' => ezpI18n::tr( 'kernel/pdfexport', 'New PDF Export' ), 'required' => true], 'show_frontpage' => ['name' => 'DisplayFrontpage', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'intro_text' => ['name' => 'IntroText', 'datatype' => 'text', 'default' => '', 'required' => false], 'sub_text' => ['name' => 'SubText', 'datatype' => 'text', 'default' => '', 'required' => false], 'source_node_id' => ['name' => 'SourceNodeID', 'datatype' => 'int', 'default' => '', 'required' => true, 'foreign_class' => 'eZContentObjectTreeNode', 'foreign_attribute' => 'node_id', 'multiplicity' => '1..*'], 'site_access' => ['name' => 'SiteAccess', 'datatype' => 'string', 'default' => '', 'required' => true], 'modified' => ['name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'modifier_id' => ['name' => 'ModifierID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], 'created' => ['name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'creator_id' => ['name' => 'CreatorID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], 'export_structure' => ['name' => 'ExportStructure', 'datatype' => 'string', 'default' => 'tree', 'required' => false], 'export_classes' => ['name' => 'ExportClasses', 'datatype' => 'string', 'default' => 0, 'required' => false], 'pdf_filename' => ['name' => 'PDFFileName', 'datatype' => 'string', 'default' => 'file.pdf', 'required' => true], 'status' => ['name' => 'Status', 'datatype' => 'integer', 'default' => eZPDFExport::CREATE_ONCE, 'required' => true], 'version' => ['name' => 'Version', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['id', 'version'], 'function_attributes' => ['modifier' => 'modifier', 'source_node' => 'sourceNode', 'filepath' => 'filepath', 'export_classes_array' => 'exportClassesArray'], 'increment_key' => 'id', 'sort' => ['title' => 'asc'], 'class_name' => 'eZPDFExport', 'name' => 'ezpdf_export'];
    }

    /*!
     \static
     Creates a new PDF Export
     \param User ID
    */
    static function create( $user_id )
    {
        $config = eZINI::instance( 'site.ini' );
        $dateTime = time();
        $row = ['id' => null, 'title' => ezpI18n::tr( 'kernel/pdfexport', 'New PDF Export' ), 'show_frontpage' => 1, 'intro_text' => '', 'sub_text' => '', 'source_node_id' => 0, 'export_structure' => 'tree', 'export_classes' => '', 'site_access' => '', 'pdf_filename' => 'file.pdf', 'modifier_id' => $user_id, 'modified' => $dateTime, 'creator_id' => $user_id, 'created' => $dateTime, 'status' => 0, 'version' => 1];
        return new eZPDFExport( $row );
    }

    /*!
     Store Object to database
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function store( $publish = false )
    {
        if ( $publish )
        {
            $originalVersion = $this->attribute( 'version' );
            $this->setAttribute( 'version', eZPDFExport::VERSION_VALID );
        }
        $user = eZUser::currentUser();
        $this->setAttribute( 'modified', time() );
        $this->setAttribute( 'modifier_id', $user->attribute( 'contentobject_id' ) );

        $db = eZDB::instance();
        $db->begin();
        parent::store();
        if ( $publish )
        {
            $this->setAttribute( 'version', eZPDFExport::VERSION_DRAFT );
            $this->remove();
            $this->setAttribute( 'version', $originalVersion );
        }
        $db->commit();
    }

    /*!
     \static
      Fetches the PDF Export by ID.

     \param PDF Export ID
    */
    static function fetch( $id, $asObject = true, $version = eZPDFExport::VERSION_VALID )
    {
        return eZPersistentObject::fetchObject( eZPDFExport::definition(),
                                                null,
                                                ['id' => $id, 'version' => $version],
                                                $asObject );
    }

    /*!
      transaction unsafe.
    */
    function remove( $conditions = null, $extraConditions = null )
    {
        if ( $this->attribute( 'version' ) == eZPDFExport::VERSION_VALID &&
             $this->attribute( 'status' ) != eZPDFExport::CREATE_ONFLY )
        {
            $sys = eZSys::instance();
            $storage_dir = $sys->storageDirectory();

            $filename = $storage_dir . '/pdf/' . $this->attribute( 'pdf_filename' );
            if ( file_exists( $filename ) )
            {
                unlink( $filename );
            }
        }
        parent::remove( $conditions, $extraConditions);
    }

    /*!
     \static
      Fetches complete list of PDF Exports.
    */
    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZPDFExport::definition(),
                                                    null,
                                                    ['version' => eZPDFExport::VERSION_VALID],
                                                    null,
                                                    null,
                                                    $asObject );
    }

    function modifier()
    {
        if ( isset( $this->ModifierID ) and $this->ModifierID )
        {
            return eZUser::fetch( $this->ModifierID );
        }

        return null;
    }

    function sourceNode()
    {
        if ( isset( $this->SourceNodeID ) and $this->SourceNodeID )
        {
            return eZContentObjectTreeNode::fetch( $this->SourceNodeID );
        }

        return null;
    }

    function filepath()
    {
        $sys = eZSys::instance();
        $storage_dir = $sys->storageDirectory();
        return $storage_dir . '/pdf/' . $this->attribute( 'pdf_filename' );
    }

    function exportClassesArray()
    {
        return explode( ':',  (string) $this->attribute( 'export_classes' ) );
    }

    function countGeneratingOnceExports( $filename = '' )
    {
        $conditions = ['version' => eZPDFExport::VERSION_VALID, 'status' =>  eZPDFExport::CREATE_ONCE, 'pdf_filename' => $filename];

        if ( $filename === '' && isset( $this ) )
        {
            $conditions['pdf_filename'] = $this->attribute( 'pdf_filename' );
            $conditions['id'] = ['<>', $this->attribute( 'id' )];
        }

        $queryResult = eZPersistentObject::fetchObjectList( eZPDFExport::definition(),
                                                            [],
                                                            $conditions,
                                                            false,
                                                            null,
                                                            false,
                                                            null,
                                                            [['operation' => 'count( * )', 'name' => 'count']] );
        if ( isset( $queryResult[0]['count'] ) )
        {
            return ( int ) $queryResult[0]['count'];
        }
        return 0;

    }

}

?>
