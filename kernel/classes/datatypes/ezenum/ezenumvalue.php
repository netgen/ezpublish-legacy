<?php
/**
 * File containing the eZEnum class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZEnumValue ezenumvalue.php
  \ingroup eZDatatype
  \brief The class eZEnumValue does

*/

class eZEnumValue extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "contentclass_attribute_id" => ['name' => "ContentClassAttributeID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentClassAttribute', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "contentclass_attribute_version" => ['name' => "ContentClassAttributeVersion", 'datatype' => 'integer', 'default' => 0, 'required' => true], "enumelement" => ['name' => "EnumElement", 'datatype' => 'string', 'default' => '', 'required' => true], "enumvalue" => ['name' => "EnumValue", 'datatype' => 'string', 'default' => '', 'required' => true], "placement" => ['name' => "Placement", 'datatype' => 'integer', 'default' => 0, 'required' => true]], "keys" => ["id", "contentclass_attribute_id", "contentclass_attribute_version"], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZEnumValue", "name" => "ezenumvalue"];
    }

    function __clone()
    {
        unset( $this->ID );
    }

    static function create( $contentClassAttributeID, $contentClassAttributeVersion, $element )
    {
        $row = ["id" => null, "contentclass_attribute_id" => $contentClassAttributeID, "contentclass_attribute_version" => $contentClassAttributeVersion, "enumvalue" => "", "enumelement" => $element, "placement" => eZPersistentObject::newObjectOrder( eZEnumValue::definition(),
                                                           "placement",
                                                           ["contentclass_attribute_id" => $contentClassAttributeID, "contentclass_attribute_version" => $contentClassAttributeVersion] )];
        return new eZEnumValue( $row );
    }

    static function createCopy( $id, $contentClassAttributeID, $contentClassAttributeVersion, $element, $value, $placement )
    {
        $row = ["id" => $id, "contentclass_attribute_id" => $contentClassAttributeID, "contentclass_attribute_version" => $contentClassAttributeVersion, "enumvalue" => $value, "enumelement" => $element, "placement" => $placement];
        return new eZEnumValue( $row );
    }

    static function removeAllElements( $contentClassAttributeID, $version )
    {
        eZPersistentObject::removeObject( eZEnumValue::definition(),
                                          ["contentclass_attribute_id" => $contentClassAttributeID, "contentclass_attribute_version" => $version] );
    }

    static function removeByID( $id , $version )
    {
        eZPersistentObject::removeObject( eZEnumValue::definition(),
                                          ["id" => $id, "contentclass_attribute_version" => $version] );
    }

    static function fetch( $id, $version, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZEnumValue::definition(),
                                                null,
                                                ["id" => $id, "contentclass_attribute_version" => $version],
                                                $asObject );
    }

    static function fetchAllElements( $classAttributeID, $version, $asObject = true )
    {
        if ( $classAttributeID === null )
        {
            return [];
        }

        return eZPersistentObject::fetchObjectList( eZEnumValue::definition(),
                                                    null,
                                                    ["contentclass_attribute_id" => $classAttributeID, "contentclass_attribute_version" => $version],
                                                    null,
                                                    null,
                                                    $asObject );
    }

    public $ID;
    public $ContentClassAttributeID;
    public $ContentClassAttributeVersion;
    public $EnumElement;
    public $EnumValue;
    public $Placement;
}

?>
