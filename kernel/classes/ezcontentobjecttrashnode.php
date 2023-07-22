<?php
/**
 * File containing the eZContentObjectTrashNode class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Encapsulates data about and methods to work with content objects which reside in the trash
 */
class eZContentObjectTrashNode extends eZContentObjectTreeNode
{
    /**
     * @inheritdoc
     */
    static function definition()
    {
        return ['fields' => ['node_id' => ['name' => 'NodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'parent_node_id' => ['name' => 'ParentNodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'main_node_id' => ['name' => 'MainNodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'contentobject_id' => ['name' => 'ContentObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObject', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'contentobject_version' => ['name' => 'ContentObjectVersion', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'depth' => ['name' => 'Depth', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'sort_field' => ['name' => 'SortField', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'sort_order' => ['name' => 'SortOrder', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'priority' => ['name' => 'Priority', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'modified_subnode' => ['name' => 'ModifiedSubNode', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'path_string' => ['name' => 'PathString', 'datatype' => 'string', 'default' => '', 'required' => true], 'path_identification_string' => ['name' => 'PathIdentificationString', 'datatype' => 'text', 'default' => '', 'required' => true], 'remote_id' => ['name' => 'RemoteID', 'datatype' => 'string', 'default' => '', 'required' => true], 'is_hidden' => ['name' => 'IsHidden', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'is_invisible' => ['name' => 'IsInvisible', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'trashed' => ['name' => 'Trashed', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['node_id'], 'function_attributes' => [
            // functional attributes derived from ezcontentobjecttreenode
            'name' => 'getName',
            'data_map' => 'dataMap',
            'object' => 'object',
            'contentobject_version_object' => 'contentObjectVersionObject',
            'sort_array' => 'sortArray',
            'can_read' => 'canRead',
            'can_create' => 'canCreate',
            'can_edit' => 'canEdit',
            'can_remove' => 'canRemove',
            'creator' => 'creator',
            'path_array' => 'pathArray',
            'parent' => 'fetchParent',
            'class_identifier' => 'classIdentifier',
            'class_name' => 'className',
            // new functional attributes
            'original_parent' => 'originalParent',
            'original_parent_path_id_string' => 'originalParentPathIdentificationString',
        ], 'class_name' => 'eZContentObjectTrashNode', 'name' => 'ezcontentobject_trash'];
    }

    /**
     * Creates a new eZContentObjectTrashNode based on an eZContentObjectTreeNode
     *
     * @param eZContentObjectTreeNode $node
     * @return eZContentObjectTrashNode
     */
    static function createFromNode( $node )
    {
        $row = ['node_id' => $node->attribute( 'node_id' ), 'parent_node_id' => $node->attribute( 'parent_node_id' ), 'main_node_id' => $node->attribute( 'main_node_id' ), 'contentobject_id' => $node->attribute( 'contentobject_id' ), 'contentobject_version' => $node->attribute( 'contentobject_version' ), 'contentobject_is_published' => $node->attribute( 'contentobject_is_published' ), 'depth' => $node->attribute( 'depth' ), 'sort_field' => $node->attribute( 'sort_field' ), 'sort_order' => $node->attribute( 'sort_order' ), 'priority' => $node->attribute( 'priority' ), 'modified_subnode' => $node->attribute( 'modified_subnode' ), 'path_string' => $node->attribute( 'path_string' ), 'path_identification_string' => $node->attribute( 'path_identification_string' ), 'remote_id' => $node->attribute( 'remote_id' ), 'is_hidden' => $node->attribute( 'is_hidden' ), 'is_invisible' => $node->attribute( 'is_invisible' ), 'trashed' => time()];

        $trashNode = new eZContentObjectTrashNode( $row );
        return $trashNode;
    }

    /**
     * Stores this object to the trash
     *
     * Loops through all attributes of the object and stores them to the trash
     *
     * @see eZDataType::trashStoredObjectAttribute()
     */
    function storeToTrash()
    {
        $this->store();

        $db = eZDB::instance();
        $db->begin();

        /** @var eZContentObject $contentObject */
        $contentObject = $this->attribute( 'object' );
        $offset = 0;
        $limit = 20;
        while (
            $contentobjectAttributes = $contentObject->allContentObjectAttributes(
                $contentObject->attribute( 'id' ), true,
                ['limit' => $limit, 'offset' => $offset]
            )
        )
        {
            foreach ( $contentobjectAttributes as $contentobjectAttribute )
            {
                $dataType = $contentobjectAttribute->dataType();
                if ( !$dataType )
                    continue;
                $dataType->trashStoredObjectAttribute( $contentobjectAttribute );
            }
            $offset += $limit;
        }

        $db->commit();
    }

    /**
     * Purges an object from the trash, effectively deleting it from the database
     *
     * @param int $contentObjectID
     * @return bool
     */
    static function purgeForObject( $contentObjectID )
    {
        if ( !is_numeric( $contentObjectID ) )
            return false;
        $db = eZDB::instance();
        $db->begin();
        $db->query( "DELETE FROM ezcontentobject_trash WHERE contentobject_id='$contentObjectID'" );
        $db->commit();
    }

    /**
     * Returns a list or the number of nodes from the trash
     *
     * @see eZContentObjectTreeNode::subTreeByNodeID()
     *
     * @param array|bool $params
     * @param bool $asCount If true, returns the number of items in the trash
     * @return array|int|null
     */
    static function trashList( $params = false, $asCount = false )
    {
        if ( $params === false )
        {
            $params = ['Offset'                   => false, 'Limit'                    => false, 'SortBy'                   => false, 'AttributeFilter'          => false, 'Trashed'                  => false];
        }

        $offset           = ( isset( $params['Offset'] ) && is_numeric( $params['Offset'] ) ) ? $params['Offset']             : false;
        $limit            = ( isset( $params['Limit']  ) && is_numeric( $params['Limit']  ) ) ? $params['Limit']              : false;
        $asObject         = $params['AsObject'] ?? true;
        $objectNameFilter = $params['ObjectNameFilter'] ?? false;
        $sortBy           = ( isset( $params['SortBy']  ) && is_array( $params['SortBy']  ) ) ? $params['SortBy']              : [['name']];
        $trashed          = ( isset( $params['Trashed']  ) && is_int( $params['Trashed'] )  ) ? " AND trashed <= {$params['Trashed']}"   : '';

        if ( $asCount )
        {
            $sortingInfo = eZContentObjectTreeNode::createSortingSQLStrings( false );
        }
        else
        {
            $sortingInfo = eZContentObjectTreeNode::createSortingSQLStrings( $sortBy, 'ezcot' );
        }

        $attributeFilter         = eZContentObjectTreeNode::createAttributeFilterSQLStrings( $params['AttributeFilter'], $sortingInfo );
        if ( $attributeFilter === false )
        {
            return null;
        }

        $objectNameFilterSQL = eZContentObjectTreeNode::createObjectNameFilterConditionSQLString( $objectNameFilter );

        $limitation = ( isset( $params['Limitation']  ) && is_array( $params['Limitation']  ) ) ? $params['Limitation']: false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $sqlPermissionChecking = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList, 'ezcontentobject_trash', 'ezcot' );

        if ( $asCount )
        {
            $query = "SELECT count(*) as count ";
        }
        else
        {
            $query = "SELECT
                        ezcontentobject.*,
                        ezcot.*,
                        ezcontentclass.serialized_name_list as class_serialized_name_list,
                        ezcontentclass.identifier as class_identifier,
                        ezcontentobject_name.name as name,
                        ezcontentobject_name.real_translation
                        $sortingInfo[attributeTargetSQL] ";
        }
        $query .= "FROM
                        ezcontentobject_trash ezcot
                        INNER JOIN ezcontentobject ON ezcot.contentobject_id = ezcontentobject.id
                        INNER JOIN ezcontentclass ON ezcontentclass.version = 0 AND ezcontentclass.id = ezcontentobject.contentclass_id
                        INNER JOIN ezcontentobject_name ON (
                            ezcot.contentobject_id = ezcontentobject_name.contentobject_id AND
                            ezcot.contentobject_version = ezcontentobject_name.content_version
                        )
                        $sortingInfo[attributeFromSQL]
                        $attributeFilter[from]
                        $sqlPermissionChecking[from]
                   WHERE
                        $sortingInfo[attributeWhereSQL]
                        $attributeFilter[where]
                        " . eZContentLanguage::sqlFilter( 'ezcontentobject_name', 'ezcontentobject' ) . "
                        $sqlPermissionChecking[where]
                        $objectNameFilterSQL
                        AND " . eZContentLanguage::languagesSQLFilter( 'ezcontentobject' )
                        . $trashed;

        if ( !$asCount && $sortingInfo['sortingFields'] && strlen( (string) $sortingInfo['sortingFields'] ) > 5  )
            $query .= " ORDER BY $sortingInfo[sortingFields]";

        $db = eZDB::instance();
        if ( !$offset && !$limit )
            $trashRowsArray = $db->arrayQuery( $query );
        else
            $trashRowsArray = $db->arrayQuery( $query, ['offset' => $offset, 'limit'  => $limit] );

        // cleanup temp tables
        $db->dropTempTableList( $sqlPermissionChecking['temp_tables'] );

        if ( $asCount )
        {
            return $trashRowsArray[0]['count'];
        }
        else if ( $asObject )
        {
            $retTrashNodes = [];
            foreach ( array_keys( $trashRowsArray ) as $key )
            {
                $trashRow =& $trashRowsArray[ $key ];
                $retTrashNodes[] = new eZContentObjectTrashNode( $trashRow );
            }
            return $retTrashNodes;
        }
        else
        {
            return $trashRowsArray;
        }
    }

    /**
     * Returns the number of nodes in the trash
     *
     * @param array|bool $params
     * @return int
     */
    static function trashListCount( $params = false )
    {
        return eZContentObjectTrashNode::trashList( $params, true );
    }

    /**
     * Returns the parent of the current node in the tree before it has been moved to the trash or null when the
     * original parent couldn't be retrieved (e.g. because it has been deleted, too, or moved)
     *
     * @return eZContentObjectTreeNode|null
     */
    function originalParent()
    {
        if ( $this->originalNodeParent === 0 )
            $this->originalNodeParent = eZContentObjectTreeNode::fetch( $this->attribute( 'parent_node_id' ) );

        if ( $this->pathArray === 0 && $this->originalNodeParent instanceof eZContentObjectTreeNode )
            $this->pathArray = $this->attribute( 'path_array' );

        if ( $this->pathArray && count( $this->pathArray ) > 0 )
        {
            $realParentPathArray = $this->originalNodeParent->attribute( 'path_array' );
            $realParentPath = implode( '/', $realParentPathArray );

            $thisParentPathArray = array_slice( $this->pathArray, 0, -1 );
            $thisParentPath = implode( '/', $thisParentPathArray );

            if ( $thisParentPath == $realParentPath )
            {
                // original parent exists at the same placement
                return $this->originalNodeParent;
            }
        }
        // original parent was moved or deleted
        return null;
    }

    /**
     * Returns the path identification string of the node's parent, if available. Otherwise returns
     * the node's path identification string
     *
     * @see originalParent()
     * @return string
     */
    function originalParentPathIdentificationString()
    {
        $originalParent = $this->originalParent();
        if ( $originalParent )
        {
            return $originalParent->attribute( 'path_identification_string' );
        }
        // original parent was moved or does not exist, return original parent path
        $path = $this->attribute( 'path_identification_string' );
        $path = substr( (string) $path, 0, strrpos( (string) $path, '/') );
        return $path;
    }

    /**
     * Fetches a trash node by its content object id
     *
     * @param int $contentObjectID
     * @param bool $asObject
     * @param int|bool $contentObjectVersion
     * @return eZContentObjectTrashNode|null
     */
    public static function fetchByContentObjectID( $contentObjectID, $asObject = true, $contentObjectVersion = false )
    {
        $conds = ['contentobject_id' => $contentObjectID];
        if ( $contentObjectVersion !== false )
        {
            $conds['contentobject_version'] = $contentObjectVersion;
        }

        return self::fetchObject(
            self::definition(),
            null,
            $conds,
            $asObject
        );
    }

    /**
     * @var eZContentObjectTreeNode|int|null The current trash node's original parent in the node tree
     */
    protected $originalNodeParent = 0;

    /**
     * @var array
     */
    protected $pathArray = 0;
}

?>
