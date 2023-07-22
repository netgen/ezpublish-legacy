<?php
/**
 * File containing the eZGeneralDigestUserSettings class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZGeneralDigestUserSettings ezgeneraldigestusersettings.php
  \brief The class eZGeneralDigestUserSettings does

*/

class eZGeneralDigestUserSettings extends eZPersistentObject
{
    final public const TYPE_NONE = 0;
    final public const TYPE_WEEKLY = 1;
    final public const TYPE_MONTHLY = 2;
    final public const TYPE_DAILY = 3;

    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "user_id" => ['name' => "UserID", 'datatype' => 'integer', 'default' => 0, 'required' => true], "receive_digest" => ['name' => "ReceiveDigest", 'datatype' => 'integer', 'default' => 0, 'required' => true], "digest_type" => ['name' => "DigestType", 'datatype' => 'integer', 'default' => 0, 'required' => true], "day" => ['name' => "Day", 'datatype' => 'string', 'default' => '', 'required' => true], "time" => ['name' => "Time", 'datatype' => 'string', 'default' => '', 'required' => true]], "keys" => ["id"], "function_attributes" => ["address" => "address"], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZGeneralDigestUserSettings", "name" => "ezgeneral_digest_user_settings"];
    }

    /**
     * Returns the email address of the user associated with the digest
     * settings.
     *
     * @return string
     */
    protected function address()
    {
        $user = eZUser::fetch( $this->UserID );
        if ( $user instanceof eZUser )
        {
            return $user->attribute( 'email' );
        }
        return '';
    }


    static function create( $userID, $receiveDigest = 0, $digestType = self::TYPE_NONE, $day = '', $time = '' )
    {
        return new eZGeneralDigestUserSettings( ['user_id' => $userID, 'receive_digest' => $receiveDigest, 'digest_type' => $digestType, 'day' => $day, 'time' => $time] );
    }

    /**
     * @deprecated Since 5.0, please use fetchByUserId()
     * @param $address
     * @param bool $asObject
     *
     * @return array|eZPersistentObject|null
     */
    static function fetchForUser( $address, $asObject = true )
    {
        eZDebug::writeStrict(
            'Method ' . __METHOD__ . ' has been deprecated in 5.0',
            'Deprecation'
        );
        $user = eZUser::fetchByEmail( $address );
        if ( $user instanceof eZUser )
        {
            return self::fetchByUserId( $user->attribute( 'contentobject_id' ), $asObject );
        }
        return null;
    }

    /**
     * Returns the digest settings object for the user
     *
     * @since 5.0
     * @param int $userId the user id
     * @param bool $asObject
     * @return eZGeneralDigestUserSettings
     */
    static function fetchByUserId( $userId, $asObject = true )
    {
        return eZPersistentObject::fetchObject(
            self::definition(), null,
            ['user_id' => $userId], $asObject
        );
    }

    /**
     * @deprecated Since 5.0, please use removeByUserID()
     * @param string $address
     */
    static function removeByAddress( $address )
    {
        eZDebug::writeStrict(
            'Method ' . __METHOD__ . ' has been deprecated in 5.0',
            'Deprecation'
        );
        $user = eZUser::fetchByEmail( $address );
        if ( $user instanceof eZUser )
        {
            self::removeByUserID( $user->attribute( 'contentobject_id' ) );
        }
    }

    /**
     * Removes the digest settings for a user
     *
     * @since 5.0
     * @param int $id the user id
     */
    static function removeByUserId( $id )
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezgeneral_digest_user_settings WHERE user_id=" . (int)$id );
    }

    /*!
     \static
     Removes all general digest settings for all users.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezgeneral_digest_user_settings" );
    }
}

?>
