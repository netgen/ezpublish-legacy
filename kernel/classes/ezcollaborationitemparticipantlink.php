<?php
/**
 * File containing the eZCollaborationItemParticipantLink class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationItemParticipantLink ezcollaborationitemparticipantlink.php
  \brief The class eZCollaborationItemParticipantLink does

*/

class eZCollaborationItemParticipantLink extends eZPersistentObject
{
    final public const TYPE_USER = 1;
    final public const TYPE_USERGROUP = 2;

    // Everything from 1024 and above is considered custom and is specific per collaboration handler.
    final public const TYPE_CUSTOM = 1024;

    final public const ROLE_STANDARD = 1;
    final public const ROLE_OBSERVER = 2;
    final public const ROLE_OWNER = 3;
    final public const ROLE_APPROVER = 4;
    final public const ROLE_AUTHOR = 5;

    // Everything from 1024 and above is considered custom and is specific per collaboration handler.
    final public const ROLE_CUSTOM = 1024;

    static function definition()
    {
        return ['fields' => ['collaboration_id' => ['name' => 'CollaborationID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZCollaborationItem', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'participant_id' => ['name' => 'ParticipantID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObject', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'participant_type' => ['name' => 'ParticipantType', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'participant_role' => ['name' => 'ParticipantRole', 'datatype' => 'integer', 'default' => 1, 'required' => true], 'last_read' => ['name' => 'LastRead', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'created' => ['name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'modified' => ['name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['collaboration_id', 'participant_id'], 'function_attributes' => ['collaboration_item' => 'collaborationItem', 'participant' => 'participant', 'participant_type_string' => 'participantTypeString', 'participant_role_string' => 'participantRoleString', 'is_builtin_type' => 'isBuiltinType', 'is_builtin_role' => 'isBuiltinRole'], 'class_name' => 'eZCollaborationItemParticipantLink', 'name' => 'ezcollab_item_participant_link'];
    }

    static function create( $collaborationID, $participantID,
                      $participantRole = self::ROLE_STANDARD, $participantType = self::TYPE_USER )
    {
        $dateTime = time();
        $row = ['collaboration_id' => $collaborationID, 'participant_id' => $participantID, 'participant_role' => $participantRole, 'participant_type' => $participantType, 'created' => $dateTime, 'modified' => $dateTime];
        return new eZCollaborationItemParticipantLink( $row );
    }

    /*!
     \note transaction unsafe
     */
    static function setLastRead( $collaborationID, $userID = false, $timestamp = false )
    {
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }
        if ( $timestamp === false )
        {
            $timestamp = time();
        }
        $db = eZDB::instance();
        $userID = (int) $userID;
        $timestamp = (int) $timestamp;
        $sql = "UPDATE ezcollab_item_participant_link set last_read='$timestamp'
                WHERE  collaboration_id='$collaborationID' AND participant_id='$userID'";
        $db->query( $sql );
        if ( !empty( $GLOBALS["eZCollaborationItemParticipantLinkCache"][$collaborationID][$userID] ) )
            $GLOBALS["eZCollaborationItemParticipantLinkCache"][$collaborationID][$userID]->setAttribute( 'last_read', $timestamp );
    }

    static function fetch( $collaborationID, $participantID, $asObject = true )
    {
        if ( empty( $GLOBALS["eZCollaborationItemParticipantLinkCache"][$collaborationID][$participantID] ) )
        {
            $GLOBALS["eZCollaborationItemParticipantLinkCache"][$collaborationID][$participantID] =
                eZPersistentObject::fetchObject( eZCollaborationItemParticipantLink::definition(),
                                                 null,
                                                 ["collaboration_id" => $collaborationID, 'participant_id' => $participantID],
                                                 $asObject );
        }
        return $GLOBALS["eZCollaborationItemParticipantLinkCache"][$collaborationID][$participantID];
    }

    static function fetchParticipantList( $parameters = [] )
    {
        $parameters = array_merge( ['as_object' => true, 'item_id' => false, 'offset' => false, 'limit' => false, 'sort_by' => false],
                                   $parameters );

        $cacheHashKey = md5( serialize( $parameters ) );

        if ( isset( $GLOBALS['eZCollaborationItemParticipantLinkListCache'][$cacheHashKey] ) )
        {
            return $GLOBALS['eZCollaborationItemParticipantLinkListCache'][$cacheHashKey];
        }

        $itemID = $parameters['item_id'];
        $asObject = $parameters['as_object'];
        $offset = $parameters['offset'];
        $limit = $parameters['limit'];
        $linkList = null;
        $limitArray = null;

        if ( $offset and $limit )
        {
            $limitArray = ['offset' => $offset, 'length' => $limit];
        }

        $linkList = eZPersistentObject::fetchObjectList( eZCollaborationItemParticipantLink::definition(),
                                                          null,
                                                          ["collaboration_id" => $itemID],
                                                          null, $limitArray,
                                                          $asObject );

        foreach( $linkList as $linkItem )
        {
            if ( $asObject )
            {
                $participantID = $linkItem->attribute( 'participant_id' );
            }
            else
            {
                $participantID = $linkItem['participant_id'];
            }
            if ( !isset( $GLOBALS["eZCollaborationItemParticipantLinkCache"][$itemID][$participantID] ) )
            {
                $GLOBALS["eZCollaborationItemParticipantLinkCache"][$itemID][$participantID] = $linkItem;
            }
        }

        return $GLOBALS['eZCollaborationItemParticipantLinkListCache'][$cacheHashKey] = $linkList;
    }

    static function fetchParticipantMap( $originalParameters = [] )
    {
        $parameters = array_merge( ['sort_field' => 'role'],
                                   $originalParameters );
        $itemID = $parameters['item_id'];
        $sortField = $parameters['sort_field'];
        $list = eZCollaborationItemParticipantLink::fetchParticipantList( $originalParameters );
        if ( $list === null )
        {
            $listMap = null;
            return $listMap;
        }

        $listMap = [];
        foreach ( $list as $listItem )
        {
            $sortKey = null;
            if ( $sortField == 'role' )
            {
                $sortKey = $listItem->attribute( 'participant_role' );
            }
            if ( $sortKey !== null )
            {
                if ( !isset( $listMap[$sortKey] ) )
                {
                    if ( $sortField == 'role' )
                    {
                        $sortName = eZCollaborationItemParticipantLink::roleName( $itemID, $sortKey );
                    }
                    $listMap[$sortKey] = ['name' => $sortName, 'items' => []];
                }
                $listMap[$sortKey]['items'][] = $listItem;
            }
        }
        return $listMap;
    }

    static function typeString( $participantType )
    {
        if ( !isset( $GLOBALS['eZCollaborationParticipantTypeMap'] ) )
        {
            $GLOBALS['eZCollaborationParticipantTypeMap'] = [self::TYPE_USER => 'user', self::TYPE_USERGROUP => 'usergroup'];
        }
        return $GLOBALS['eZCollaborationParticipantTypeMap'][$participantType] ?? null;
    }

    static function roleString( $participantRole )
    {
        if ( empty( $GLOBALS['eZCollaborationParticipantRoleMap'] ) )
        {
            $GLOBALS['eZCollaborationParticipantRoleMap'] =
                [self::ROLE_STANDARD => 'standard', self::ROLE_OBSERVER => 'observer', self::ROLE_OWNER => 'owner', self::ROLE_APPROVER => 'approver', self::ROLE_AUTHOR => 'author'];
        }
        $roleMap = $GLOBALS['eZCollaborationParticipantRoleMap'];

        return $roleMap[$participantRole] ?? null;
    }

    static function roleName( $collaborationID, $roleID )
    {
        if ( $roleID < self::TYPE_CUSTOM )
        {
            if ( empty( $GLOBALS['eZCollaborationParticipantRoleNameMap'] ) )
            {

                $GLOBALS['eZCollaborationParticipantRoleNameMap'] =
                    [self::ROLE_STANDARD => ezpI18n::tr( 'kernel/classes', 'Standard' ), self::ROLE_OBSERVER => ezpI18n::tr( 'kernel/classes', 'Observer' ), self::ROLE_OWNER => ezpI18n::tr( 'kernel/classes', 'Owner' ), self::ROLE_APPROVER => ezpI18n::tr( 'kernel/classes', 'Approver' ), self::ROLE_AUTHOR => ezpI18n::tr( 'kernel/classes', 'Author' )];
            }
            $roleNameMap = $GLOBALS['eZCollaborationParticipantRoleNameMap'];
            return $roleNameMap[$roleID] ?? null;
        }

        $item = eZCollaborationItem::fetch( $collaborationID );
        return $item->handler()->roleName( $collaborationID, $roleID );
    }

    function collaborationItem()
    {
        return eZCollaborationItem::fetch( $this->CollaborationID );
    }

    function participant()
    {
        if ( $this->ParticipantType == self::TYPE_USER )
        {
            return eZUser::fetch( $this->ParticipantID );
        }
        else if ( $this->ParticipantType == self::TYPE_USERGROUP )
        {
            return eZContentObject::fetch( $this->ParticipantID );
        }
        return null;
    }

    function participantTypeString()
    {
        if ( $this->ParticipantType < self::TYPE_CUSTOM )
        {
            return  eZCollaborationItemParticipantLink::typeString( $this->ParticipantType );
        }

        $item = eZCollaborationItem::fetch( $this->CollaborationID );
        return $item->attribute( 'type_identifier' ) . '_' . $item->handler()->participantTypeString( $this->ParticipantType );
    }

    function participantRoleString()
    {
        if ( $this->ParticipantRole < self::ROLE_CUSTOM )
        {
            return  eZCollaborationItemParticipantLink::roleString( $this->ParticipantRole );
        }

        $item = eZCollaborationItem::fetch( $this->CollaborationID );
        return $item->attribute( 'type_identifier' ) . '_' . $item->handler()->participantRoleString( $this->ParticipantRole );
    }

    function isBuiltinType()
    {
        return $this->ParticipantType < self::TYPE_CUSTOM;
    }

    function isBuiltinRole()
    {
        return $this->ParticipantRole < self::ROLE_CUSTOM;
    }

    /// \privatesection
    public $CollaborationID;
    public $ParticipantID;
    public $ParticipantType;
    public $IsRead;
    public $IsActive;
    public $Created;
    public $Modified;
}

?>
