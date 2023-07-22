<?php
/**
 * File containing the eZCollaborationNotificationRule class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationNotificationRule ezcollaborationnotificationrule.php
  \brief The class eZCollaborationNotificationRule does

*/
class eZCollaborationNotificationRule extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "user_id" => ['name' => "UserID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], "collab_identifier" => ['name' => "CollaborationIdentifier", 'datatype' => 'string', 'default' => '', 'required' => true]], "keys" => ["id"], "function_attributes" => ['user' => 'user'], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZCollaborationNotificationRule", "name" => "ezcollab_notification_rule"];
    }

    function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }

    static function create( $collaborationIdentifier, $userID = false )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();
        return new eZCollaborationNotificationRule( ['user_id' => $userID, 'collab_identifier' => $collaborationIdentifier] );
    }

    static function fetchList( $userID = false, $asObject = true )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();
        return eZPersistentObject::fetchObjectList( eZCollaborationNotificationRule::definition(),
                                                    null, ['user_id' => $userID],
                                                    null, null, $asObject );
    }

    static function fetchItemTypeList( $collaborationIdentifier, $userIDList, $asObject = true )
    {
        if ( is_array( $collaborationIdentifier ) )
            $collaborationIdentifier = [$collaborationIdentifier];
        return eZPersistentObject::fetchObjectList( eZCollaborationNotificationRule::definition(),
                                                    null, ['user_id' => [$userIDList], 'collab_identifier' => $collaborationIdentifier],
                                                    null, null, $asObject );
    }

    static function removeByIdentifier( $collaborationIdentifier, $userID = false )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();
        eZPersistentObject::removeObject( eZCollaborationNotificationRule::definition(),
                                          ['collab_identifier' => $collaborationIdentifier, 'user_id' => $userID] );
    }

    /*!
     \static

     Remove notifications by user id

     \param userID
    */
    static function removeByUserID( $userID )
    {
        eZPersistentObject::removeObject( eZCollaborationNotificationRule::definition(), ['user_id' => $userID] );
    }

    /*!
     \static
     Removes all notification rules for all collaboration items for all users.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezcollab_notification_rule" );
    }
}

?>
