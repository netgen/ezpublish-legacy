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
  \class eZEnumObjectValue ezenumobjectvalue.php
  \brief The class eZEnumObjectValue stores chosen enum values of an object attribute

*/

class eZEnumObjectValue extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["contentobject_attribute_id" => ['name' => "ContentObjectAttributeID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObjectAttribute', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], "contentobject_attribute_version" => ['name' => "ContentObjectAttributeVersion", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'short_name' => 'contentobject_attr_version'], "enumid" => ['name' => "EnumID", 'datatype' => 'integer', 'default' => 0, 'required' => true], "enumelement" => ['name' => "EnumElement", 'datatype' => 'string', 'default' => '', 'required' => true], "enumvalue" => ['name' => "EnumValue", 'datatype' => 'string', 'default' => '', 'required' => true]], "keys" => ["contentobject_attribute_id", "contentobject_attribute_version", "enumid"], "sort" => ["contentobject_attribute_id" => "asc"], "class_name" => "eZEnumObjectValue", "name" => "ezenumobjectvalue"];
    }

    static function create( $contentObjectAttributeID, $contentObjectAttributeVersion, $enumID, $enumElement, $enumValue )
    {
        $row = ["contentobject_attribute_id" => $contentObjectAttributeID, "contentobject_attribute_version" => $contentObjectAttributeVersion, "enumid" => $enumID, "enumelement" =>  $enumElement, "enumvalue" => $enumValue];
        return new eZEnumObjectValue( $row );
    }

    static function removeAllElements( $contentObjectAttributeID, $contentObjectAttributeVersion )
    {
        if( $contentObjectAttributeVersion == null )
        {
            eZPersistentObject::removeObject( eZEnumObjectValue::definition(),
                                              ["contentobject_attribute_id" => $contentObjectAttributeID] );
        }
        else
        {
            eZPersistentObject::removeObject( eZEnumObjectValue::definition(),
                                              ["contentobject_attribute_id" => $contentObjectAttributeID, "contentobject_attribute_version" => $contentObjectAttributeVersion] );
        }
    }

    function removeByOAID( $contentObjectAttributeID, $contentObjectAttributeVersion, $enumid )
    {
        eZPersistentObject::removeObject( eZEnumObjectValue::definition(),
                                          ["enumid" => $enumid, "contentobject_attribute_id" => $contentObjectAttributeID, "contentobject_attribute_version" => $contentObjectAttributeVersion] );
    }

    static function fetch( $contentObjectAttributeID, $contentObjectAttributeVersion, $enumid, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZEnumObjectValue::definition(),
                                                null,
                                                ["contentobject_attribute_id" => $contentObjectAttributeID, "contentobject_attribute_version" => $contentObjectAttributeVersion, "enumid" => $enumid],
                                                $asObject );
    }

    static function fetchAllElements( $contentObjectAttributeID, $contentObjectAttributeVersion, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZEnumObjectValue::definition(),
                                                    null,
                                                    ["contentobject_attribute_id" => $contentObjectAttributeID, "contentobject_attribute_version" => $contentObjectAttributeVersion],
                                                    null,
                                                    null,
                                                    $asObject );
    }

    public $ContentObjectAttributeID;
    public $ContentObjectAttributeVersion;
    public $EnumID;
    public $EnumElement;
    public $EnumValue;
}

?>
