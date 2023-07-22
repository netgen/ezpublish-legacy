<?php
/**
 * File containing the eZCollaborationItemStatus class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationItemStatus ezcollaborationitemstatus.php
  \brief The class eZCollaborationItemStatus does

*/

class eZCollaborationItemStatus extends eZPersistentObject
{
    static function definition()
    {
        return ['fields' => ['collaboration_id' => ['name' => 'CollaborationID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZCollaborationItem', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'user_id' => ['name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], 'is_read' => ['name' => 'IsRead', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'is_active' => ['name' => 'IsActive', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'last_read' => ['name' => 'LastRead', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['collaboration_id', 'user_id'], 'class_name' => 'eZCollaborationItemStatus', 'name' => 'ezcollab_item_status'];
    }

    static function create( $collaborationID, $userID = false )
    {
        if ( $userID === false )
            $userID = eZUser::currentUserID();
        $row = ['collaboration_id' => $collaborationID, 'user_id' => $userID, 'is_read' => false, 'is_active' => true, 'last_read' => 0];
        return $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID] = new eZCollaborationItemStatus( $row );
    }

    function store( $fieldFilters = null )
    {
        parent::store( $fieldFilters );
        $this->updateCache();
        return true;
    }

    function updateCache()
    {
        $userID = $this->UserID;
        $collaborationID = $this->CollaborationID;
        $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID] = $this;
    }

    static function fetch( $collaborationID, $userID = false, $asObject = true )
    {
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }
        if ( !isset( $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID] ) )
        {
            $conditions = ['collaboration_id' => $collaborationID, 'user_id' => $userID];
            $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID] = eZPersistentObject::fetchObject(
                eZCollaborationItemStatus::definition(),
                null,
                $conditions,
                $asObject );
        }
        return $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID];
    }

    static function setLastRead( $collaborationID, $userID = false, $timestamp = false )
    {
        if ( $timestamp === false )
            $timestamp = time();

        eZCollaborationItemStatus::updateFields( $collaborationID, ['last_read' => $timestamp, 'is_read' => 1], $userID );
    }

    static function updateFields( $collaborationID, $fields, $userID = false )
    {
        if ( $userID === false )
            $userID = eZUser::currentUserID();

        eZPersistentObject::updateObjectList( ['definition' => eZCollaborationItemStatus::definition(), 'update_fields' => $fields, 'conditions' => ['collaboration_id' => $collaborationID, 'user_id' => $userID]] );
        $statusObject =& $GLOBALS['eZCollaborationItemStatusCache'][$collaborationID][$userID];
        if ( isset( $statusObject ) )
        {
            foreach ( $fields as $field => $value )
            {
                $statusObject->setAttribute( $field, $value );
            }
        }
    }

}

?>
