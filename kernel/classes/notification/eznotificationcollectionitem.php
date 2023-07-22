<?php
/**
 * File containing the eZNotificationCollectionItem class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationCollectionItem eznotificationcollectionitem.php
  \brief The class eZNotificationCollectionItem does

*/

class eZNotificationCollectionItem extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "collection_id" => ['name' => "CollectionID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZNotificationCollection', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "event_id" => ['name' => "EventID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZNotificationEvent', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "address" => ['name' => "Address", 'datatype' => 'string', 'default' => '', 'required' => true], "send_date" => ['name' => "SendDate", 'datatype' => 'integer', 'default' => 0, 'required' => true]], "keys" => ["id"], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZNotificationCollectionItem", "name" => "eznotificationcollection_item"];
    }

    static function create( $collectionID, $eventID, $address, $sendDate = 0 )
    {
        return new eZNotificationCollectionItem( ['collection_id' => $collectionID, 'event_id' => $eventID, 'address' => $address, 'send_date' => $sendDate] );
    }

    static function fetchByDate( $date )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationCollectionItem::definition(),
                                                    null, ['send_date' => ['<', $date], 'send_date' => ['!=', 0]] , null, null,
                                                    true );
    }

    static function fetchCountForEvent( $eventID )
    {
        $result = eZPersistentObject::fetchObjectList( eZNotificationCollectionItem::definition(),
                                                       [],
                                                       ['event_id' => $eventID],
                                                       false,
                                                       null,
                                                       false,
                                                       false,
                                                       [['operation' => 'count( * )', 'name' => 'count']] );
        return $result[0]['count'];
    }

    /*!
     \static
     Removes all notification collection items.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM eznotificationcollection_item" );
    }
}

?>
