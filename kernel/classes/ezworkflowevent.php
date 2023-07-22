<?php
/**
 * File containing the eZWorkflowEvent class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

//!! eZKernel
//! The class eZWorkflowEvent does
/*!

*/

class eZWorkflowEvent extends eZPersistentObject
{
    public function __construct( $row )
    {
        parent::__construct( $row );
    }

    /**
     * @deprecated Use eZWorkflowEvent::__construct() instead
     * @param array $row
     */
    public function eZWorkflowEvent( $row )
    {
        self::__construct( $row );
    }

    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "version" => ['name' => "Version", 'datatype' => 'integer', 'default' => 0, 'required' => true], "workflow_id" => ['name' => "WorkflowID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZWorkflow', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "workflow_type_string" => ['name' => "TypeString", 'datatype' => 'string', 'default' => '', 'required' => true, 'max_length' => 50], "description" => ['name' => "Description", 'datatype' => 'string', 'default' => '', 'required' => true, 'max_length' => 50], "data_int1" => ['name' => "DataInt1", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int2" => ['name' => "DataInt2", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int3" => ['name' => "DataInt3", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_int4" => ['name' => "DataInt4", 'datatype' => 'integer', 'default' => 0, 'required' => true], "data_text1" => ['name' => "DataText1", 'datatype' => 'text', 'default' => '', 'required' => true, 'max_length' => 50], "data_text2" => ['name' => "DataText2", 'datatype' => 'text', 'default' => '', 'required' => true, 'max_length' => 50], "data_text3" => ['name' => "DataText3", 'datatype' => 'text', 'default' => '', 'required' => true, 'max_length' => 50], "data_text4" => ['name' => "DataText4", 'datatype' => 'text', 'default' => '', 'required' => true, 'max_length' => 50], "data_text5" => ['name' => "DataText5", 'datatype' => 'text', 'default' => '', 'required' => true], "placement" => ['name' => "Placement", 'datatype' => 'integer', 'default' => 0, 'required' => true]], "keys" => ["id", "version"], "function_attributes" => ['content' => 'content', 'workflow_type' => 'eventType'], "increment_key" => "id", "sort" => ["placement" => "asc"], "class_name" => "eZWorkflowEvent", "name" => "ezworkflow_event"];
    }

    /**
     * Creates a new workflow event
     *
     * @param int $workflowID
     * @param string $typeString
     *
     * @return eZWorkflowEvent
     */
    static function create( $workflowID, $typeString )
    {
        $row = ["id" => null, "version" => 1, "workflow_id" => $workflowID, "workflow_type_string" => $typeString, "description" => "", "placement" => eZPersistentObject::newObjectOrder( eZWorkflowEvent::definition(),
                                                           "placement",
                                                           ["version" => 1, "workflow_id" => $workflowID] )];
        return new eZWorkflowEvent( $row );
    }

    static function fetch( $id, $asObject = true, $version = 0, $field_filters = null )
    {
        return eZPersistentObject::fetchObject( eZWorkflowEvent::definition(),
                                                $field_filters,
                                                ["id" => $id, "version" => $version],
                                                $asObject );
    }

    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZWorkflowEvent::definition(),
                                                    null, null, null, null,
                                                    $asObject );
    }

    static function fetchFilteredList( $cond, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZWorkflowEvent::definition(),
                                                    null, $cond, null, null,
                                                    $asObject );
    }

    /*!
     Moves the object down if $down is true, otherwise up.
     If object is at either top or bottom it is wrapped around.
    */
    function move( $down, $params = null )
    {
        if ( is_array( $params ) )
        {
            $pos = $params["placement"];
            $wid = $params["workflow_id"];
            $version = $params["version"];
        }
        else
        {
            $pos = $this->Placement;
            $wid = $this->WorkflowID;
            $version = $this->Version;
        }
        eZPersistentObject::reorderObject( eZWorkflowEvent::definition(),
                                           ["placement" => $pos],
                                           ["workflow_id" => $wid, "version" => $version],
                                           $down );
    }

    function attributes()
    {
        return array_merge( parent::attributes(), $this->eventType()->typeFunctionalAttributes() );
    }

    function hasAttribute( $attr )
    {
        $eventType = $this->eventType();
        return parent::hasAttribute( $attr ) or
               in_array( $attr, $eventType->typeFunctionalAttributes() );
    }

    function attribute( $attr, $noFunction = false )
    {
        $eventType = $this->eventType();
        if ( is_object( $eventType ) and in_array( $attr, $eventType->typeFunctionalAttributes( ) ) )
        {
            return $eventType->attributeDecoder( $this, $attr );
        }

        return parent::attribute( $attr );
    }

    function eventType()
    {
        if ( ! isset( $this->EventType ) )
        {
            $this->EventType = eZWorkflowType::createType( $this->TypeString );
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
            $this->Content = $eventType->workflowEventContent( $this );
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


    /*!
     Executes the custom HTTP action
    */
    function customHTTPAction( $http, $action )
    {
        $eventType = $this->eventType();
        $eventType->customWorkflowEventHTTPAction( $http, $action, $this );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function store( $fieldFilters = null )
    {
        $db = eZDB::instance();
        $db->begin();
        $stored = parent::store( $fieldFilters );

        $eventType = $this->eventType();
        if ( $eventType instanceof eZWorkflowEventType )
        {
            $eventType->storeEventData( $this, $this->attribute( 'version' ) );
        }
        else // Can't find eventype. Most likely deactivated while workflow has not been cleaneds up
        {
            eZDebug::writeError( "Couldn't load eventype '{$this->attribute( 'workflow_type_string' )}' for workflow. Is it activated ?", __METHOD__ );
        }
        $db->commit();

        return $stored;
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function storeDefined( $fieldFilters = null )
    {
        $db = eZDB::instance();
        $db->begin();
        $stored = parent::store( $fieldFilters );

        $eventType = $this->eventType();
        if ( $eventType instanceof eZWorkflowEventType )
        {
            $eventType->storeDefinedEventData( $this );
        }
        else // Can't find eventype. Most likely deactivated while workflow has not been cleaned up
        {
            eZDebug::writeError( "Couldn't load eventype '{$this->attribute( 'workflow_type_string' )}' for workflow. Is it activated ?", __METHOD__ );
        }

        $db->commit();

        return $stored;
    }

    /// \privatesection
    public $ID;
    public $Version;
    public $WorkflowID;
    public $TypeString;
    public $Description;
    public $Placement;
    public $DataInt1;
    public $DataInt2;
    public $DataInt3;
    public $DataInt4;
    public $DataText1;
    public $DataText2;
    public $DataText3;
    public $DataText4;
    public $Content = null;
}

?>
