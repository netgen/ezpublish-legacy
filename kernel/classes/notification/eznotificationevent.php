<?php
/**
 * File containing the eZNotificationEvent class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationEvent eznotificationevent.php
  \brief The class eZNotificationEvent does

*/
class eZNotificationEvent extends eZPersistentObject
{
    final public const STATUS_CREATED = 0;
    final public const STATUS_HANDLED = 1;

    public function __construct( $row = [] )
    {
        parent::__construct( $row );
        $this->TypeString = $this->attribute( 'event_type_string' );
    }

    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "status" => ['name' => 'Status', 'datatype' => 'integer', 'default' => 0, 'required' => true], "event_type_string" => ['name' => "EventTypeString", 'datatype' => 'string', 'default' => '', 'required' => true], "data_int1" => ['name' => "DataInt1", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int2" => ['name' => "DataInt2", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int3" => ['name' => "DataInt3", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int4" => ['name' => "DataInt4", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_text1" => ['name' => "DataText1", 'datatype' => 'text', 'default' => '', 'required' => true], "data_text2" => ['name' => "DataText2", 'datatype' => 'text', 'default' => '', 'required' => true], "data_text3" => ['name' => "DataText3", 'datatype' => 'text', 'default' => '', 'required' => true], "data_text4" => ['name' => "DataText4", 'datatype' => 'text', 'default' => '', 'required' => true]], "keys" => ["id"], "function_attributes" => ['content' => 'content'], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZNotificationEvent", "name" => "eznotificationevent"];
    }

    static function create( $type, $params = [] )
    {
        $row = ["id" => null, 'event_type_string' => $type, 'data_int1' => 0, 'data_int2' => 0, 'data_int3' => 0, 'data_int4' => 0, 'data_text1' => '', 'data_text2' => '', 'data_text3' => '', 'data_text4' => ''];
        $event = new eZNotificationEvent( $row );
        eZDebugSetting::writeDebug( 'kernel-notification', $event, "event" );
        $event->initializeEventType( $params );
        return $event;
    }

    function initializeEventType( $params = [] )
    {
        $eventType = $this->eventType();
        $eventType->initializeEvent( $this, $params );
        eZDebugSetting::writeDebug( 'kernel-notification', $this, 'event after initialization' );
    }

    function eventType()
    {
        if ( ! isset ( $this->EventType ) )
        {
            $this->EventType = eZNotificationEventType::create( $this->TypeString );
        }
        return $this->EventType;
    }


    /*!
     Returns the content for this event.
    */
    function content()
    {
        if ( $this->Content === null )
        {
            $eventType = $this->eventType();
            $this->Content = $eventType->eventContent( $this );
        }
        return $this->Content;
    }

    /*!
     Sets the content for the current event
    */
    function setContent( $content )
    {
        $this->Content = $content;
    }

    /**
     * Fetches notification events as objects, and returns them in an array.
     *
     * The optional $limit can be used to set an offset and a limit for the fetch. It is
     * passed to {@link eZPersistentObject::fetchObjectList()} and should be used in the same way.
     *
     * @static
     * @param array $limit An associative array with limitiations, can contain
     *                     - offset - Numerical value defining the start offset for the fetch
     *                     - length - Numerical value defining the max number of items to return
     * @return array An array of eZNotificationEvent objects
     */
    static function fetchList( $limit = null )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(),
                                                    null,  null, null, $limit,
                                                    true );
    }

    static function fetch( $eventID )
    {
        return eZPersistentObject::fetchObject( eZNotificationEvent::definition(),
                                                null,
                                                ['id' => $eventID] );
    }

    /**
     * Fetches unhandled notification events as objects, and returns them in an array.
     *
     * The optional $limit can be used to set an offset and a limit for the fetch. It is
     * passed to {@link eZPersistentObject::fetchObjectList()} and should be used in the same way.
     *
     * @static
     * @param array $limit An associative array with limitiations, can contain
     *                     - offset - Numerical value defining the start offset for the fetch
     *                     - length - Numerical value defining the max number of items to return
     * @return array An array of eZNotificationEvent objects
     */
    static function fetchUnhandledList( $limit = null )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(),
                                                    null, ['status' => self::STATUS_CREATED], null, $limit,
                                                    true );
    }

    /*!
     \static
     Removes all notification events.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM eznotificationevent" );
    }

    public $Content = null;
}

?>
