<?php
/**
 * File containing the eZCollaborationItemMessageLink class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationItemMessageLink ezcollaborationitemmessagelink.php
  \brief The class eZCollaborationItemMessageLink does

*/

class eZCollaborationItemMessageLink extends eZPersistentObject
{
    static function definition()
    {
        return ['fields' => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'collaboration_id' => ['name' => 'CollaborationID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZCollaborationItem', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'message_id' => ['name' => 'MessageID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZCollaborationSimpleMessage', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'message_type' => ['name' => 'MessageType', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'participant_id' => ['name' => 'ParticipantID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], 'created' => ['name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'modified' => ['name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['id'], 'function_attributes' => ['collaboration_item' => 'collaborationItem', 'participant' => 'participant', 'simple_message' => 'simpleMessage'], 'increment_key' => 'id', 'class_name' => 'eZCollaborationItemMessageLink', 'name' => 'ezcollab_item_message_link'];
    }

    static function create( $collaborationID, $messageID, $messageType, $participantID )
    {
        $dateTime = time();
        $row = ['collaboration_id' => $collaborationID, 'message_id' => $messageID, 'message_type' => $messageType, 'participant_id' => $participantID, 'created' => $dateTime, 'modified' => $dateTime];
        return new eZCollaborationItemMessageLink( $row );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function addMessage( $collaborationItem, $message, $messageType, $participantID = false )
    {
        $messageID = $message->attribute( 'id' );

        if ( !$messageID )
        {
            eZDebug::writeError( 'No message ID, cannot create link', __METHOD__ );
            $retValue = null;
            return $retValue;
        }
        if ( $participantID === false )
        {
            $user = eZUser::currentUser();
            $participantID = $user->attribute( 'contentobject_id' );
        }
        $collaborationID = $collaborationItem->attribute( 'id' );
        $timestamp = time();
        $collaborationItem->setAttribute( 'modified', $timestamp );

        $db = eZDB::instance();
        $db->begin();
        $collaborationItem->sync();
        $link = eZCollaborationItemMessageLink::create( $collaborationID, $messageID, $messageType, $participantID );
        $link->store();
        $db->commit();

        return $link;
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZCollaborationItemMessageLink::definition(),
                                                null,
                                                ["id" => $id],
                                                null, null,
                                                $asObject );
    }

    static function fetchItemCount( $parameters )
    {
        $parameters = array_merge( ['item_id' => false, 'conditions' => null],
                                   $parameters );
        $itemID = $parameters['item_id'];
        $conditions = $parameters['conditions'];
        if ( $conditions === null )
            $conditions = [];
        $conditions['collaboration_id'] = $itemID;

        $objectList = eZPersistentObject::fetchObjectList( eZCollaborationItemMessageLink::definition(),
                                                           [],
                                                           $conditions,
                                                           false,
                                                           null,
                                                           false,
                                                           false,
                                                           [['operation' => 'count( id )', 'name' => 'count']] );
        return $objectList[0]['count'];
    }

    static function fetchItemList( $parameters )
    {
        $parameters = array_merge( ['as_object' => true, 'item_id' => false, 'offset' => false, 'limit' => false, 'sort_by' => false],
                                   $parameters );
        $itemID = $parameters['item_id'];
        $asObject = $parameters['as_object'];
        $offset = $parameters['offset'];
        $limit = $parameters['limit'];
        $limitArray = null;
        if ( $offset and $limit )
        {
            $limitArray = ['offset' => $offset, 'limit' => $limit];
        }

        return eZPersistentObject::fetchObjectList( eZCollaborationItemMessageLink::definition(),
                                                    null,
                                                    ["collaboration_id" => $itemID],
                                                    null, $limitArray,
                                                    $asObject );
    }


    function collaborationItem()
    {
        if ( isset( $this->CollaborationID ) and $this->CollaborationID )
        {
            return eZCollaborationItem::fetch( $this->CollaborationID );
        }

        return null;
    }

    function participant()
    {
        return eZCollaborationItemParticipantLink::fetch( $this->CollaborationID, $this->ParticipantID );
    }

    function simpleMessage()
    {
        if ( isset( $this->MessageID ) and $this->MessageID )
        {
            return eZCollaborationSimpleMessage::fetch( $this->MessageID );
        }

        return null;
    }

    /// \privatesection
    public $CollaborationID;
    public $MessageID;
    public $ParticipantID;
    public $Created;
    public $Modified;
}

?>
