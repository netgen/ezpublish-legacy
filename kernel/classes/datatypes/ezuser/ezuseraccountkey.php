<?php
/**
 * File containing the eZUserAccountKey class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZUserAccountKey ezuseraccountkey.php
  \ingroup eZDatatype
  \brief The class eZUserAccountKey does

*/

class eZUserAccountKey extends eZPersistentObject
{
    static function definition()
    {
        return ['fields' => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'user_id' => ['name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '0..1'], 'hash_key' => ['name' => 'HashKey', 'datatype' => 'string', 'default' => '', 'required' => true], 'time' => ['name' => 'Time', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['id'], 'increment_key' => 'id', 'sort' => ['id' => 'asc'], 'class_name' => 'eZUserAccountKey', 'name' => 'ezuser_accountkey'];
    }

    static function createNew( $userID, $hashKey, $time)
    {
        return new eZUserAccountKey( ['user_id' => $userID, 'hash_key' => $hashKey, 'time' => $time] );
    }

    static function fetchByKey( $hashKey )
    {
        return eZPersistentObject::fetchObject( eZUserAccountKey::definition(),
                                                null,
                                                ['hash_key' => $hashKey],
                                                true );
    }

    /**
     * Return the eZUserAccountKey object associated to a user id
     *
     * @param int $userID
     * @return eZUserAccountKey
     */
    static public function fetchByUserID( $userID )
    {
        return eZPersistentObject::fetchObject(
            eZUserAccountKey::definition(),
            null,
            ['user_id' => $userID],
            true
        );
    }

    /*!
     Remove account keys belonging to user \a $userID
    */
    static function removeByUserID( $userID )
    {
        eZPersistentObject::removeObject( eZUserAccountKey::definition(),
                                          ['user_id' => $userID] );
    }

}

?>
