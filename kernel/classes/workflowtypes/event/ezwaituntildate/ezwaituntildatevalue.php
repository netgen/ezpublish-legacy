<?php
/**
 * File containing the eZWaitUntilDateValue class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZWaitUntilDateValue ezwaituntildatevalue.php
  \brief The class eZWaitUntilDateValue does

*/

class eZWaitUntilDateValue extends eZPersistentObject
{
    public function __construct( $row )
    {
        parent::__construct( $row );

    }

    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "workflow_event_id" => ['name' => "WorkflowEventID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZWorkflowEvent', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "workflow_event_version" => ['name' => "WorkflowEventVersion", 'datatype' => 'integer', 'default' => 0, 'required' => true], "contentclass_id" => ['name' => "ContentClassID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentClass', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "contentclass_attribute_id" => ['name' => "ContentClassAttributeID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentClassAttribute', 'foreign_attribute' => 'id', 'multiplicity' => '1..*']], "keys" => ["id", "workflow_event_id", "workflow_event_version"], "function_attributes" => ["class_name" => "className", "classattribute_name" => "classAttributeName"], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZWaitUntilDateValue", "name" => "ezwaituntildatevalue"];
    }

    function className()
    {
        if ( $this->ClassName === null )
        {
            $contentClass = eZContentClass::fetch( $this->attribute( 'contentclass_id' ) );
            if ( !$contentClass instanceof eZContentClass )
            {
                eZDebug::writeError( 'Unable to find eZContentClass #' . $this->attribute( 'contentclass_id' ), __METHOD__ );
                return null;
            }
            $this->ClassName = $contentClass->attribute( 'name' );
        }
        return $this->ClassName;
    }

    function classAttributeName()
    {
        if ( $this->ClassAttributeName === null )
        {
            $contentClassAttribute = eZContentClassAttribute::fetch( $this->attribute( 'contentclass_attribute_id' ) );
            if ( !$contentClassAttribute instanceof eZContentClassAttribute )
            {
                eZDebug::writeError( 'Unable to find eZContentClassAttribute #' . $this->attribute( 'contentclass_attribute_id' ), __METHOD__ );
                return null;
            }
            $this->ClassAttributeName = $contentClassAttribute->attribute( 'name' );
        }
        return $this->ClassAttributeName;
    }

    function __clone()
    {
        unset( $this->ClassName );
        unset( $this->ClassAttributeName );
    }

    static function create( $workflowEventID, $workflowEventVersion, $contentClassAttributeID, $contentClassID )
    {
        $row = ["id" => null, "workflow_event_id" => $workflowEventID, "workflow_event_version" => $workflowEventVersion, "contentclass_id" => $contentClassID, "contentclass_attribute_id" => $contentClassAttributeID];
        return new eZWaitUntilDateValue( $row );
    }

    static function createCopy( $id, $workflowEventID, $workflowEventVersion,  $contentClassID , $contentClassAttributeID )
    {
        $row = ["id" => $id, "workflow_event_id" => $workflowEventID, "workflow_event_version" => $workflowEventVersion, "contentclass_id" => $contentClassID, "contentclass_attribute_id" => $contentClassAttributeID];
        return new eZWaitUntilDateValue( $row );
    }


    static function removeAllElements( $workflowEventID, $version )
    {
        eZPersistentObject::removeObject( eZWaitUntilDateValue::definition(),
                                          ["workflow_event_id" => $workflowEventID, "workflow_event_version" => $version] );
    }

    static function removeByID( $id , $version )
    {
        eZPersistentObject::removeObject( eZWaitUntilDateValue::definition(),
                                          ["id" => $id, "workflow_event_version" => $version] );
    }

    function fetch( $id, $version, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZWaitUntilDateValue::definition(),
                                                null,
                                                ["id" => $id, "workflow_event_version" => $version],
                                                $asObject );
    }

    static function fetchAllElements( $workflowEventID, $version, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZWaitUntilDateValue::definition(),
                                                    null,
                                                    ["workflow_event_id" => $workflowEventID, "workflow_event_version" => $version],
                                                    null,
                                                    null,
                                                    $asObject );
    }

    public $ClassName = null;
    public $ClassAttributeName = null;
}

?>
