<?php
/**
 * File containing the eZUserSetting class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZUserSetting ezusersetting.php
  \ingroup eZDatatype

*/

class eZUserSetting extends eZPersistentObject
{
    static function definition()
    {
        static $definition = ['fields' => ['user_id' => ['name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '0..1'], 'is_enabled' => ['name' => 'IsEnabled', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'max_login' => ['name' => 'MaxLogin', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['user_id'], 'relations' => ['user_id' => ['class' => 'ezuser', 'field' => 'contentobject_id']], 'class_name' => 'eZUserSetting', 'name' => 'ezuser_setting'];
        return $definition;
    }

    static function create( $userID, $isEnabled )
    {
        $row = ['user_id' => $userID, 'is_enabled' => $isEnabled, 'max_login' => null];
        return new eZUserSetting( $row );
    }


    function setAttribute( $attr, $val )
    {
        switch( $attr )
        {
            case 'is_enabled':
            {
                if ( !$val )
                {
                    $user = eZUser::fetch( $this->UserID );
                    if ( $user )
                    {
                        eZUser::removeSessionData( $this->UserID );
                    }
                }
                eZUser::purgeUserCacheByUserId( $this->UserID );
            } break;
        }

        parent::setAttribute( $attr, $val );
    }

    /*!
     Fetch message object with \a $userID
    */
    static function fetch( $userID,  $asObject = true  )
    {
        return eZPersistentObject::fetchObject( eZUserSetting::definition(),
                                                    null,
                                                    ['user_id' => $userID],
                                                    $asObject );
    }

    /*!
     Fetch all settings from database
    */
    static function fetchAll( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZUserSetting::definition(),
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    $asObject );
    }

    static function removeByUserID( $userID )
    {
        eZPersistentObject::removeObject( eZUserSetting::definition(),
                                          ['user_id' => $userID] );
    }

    /// \privatesection
    public $UserID;
    public $IsEnabled;
    public $MaxLogin;
}

?>
