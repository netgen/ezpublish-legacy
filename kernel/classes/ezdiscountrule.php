<?php
/**
 * File containing the eZDiscountRule class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZDiscountRule ezdiscountrule.php
  \brief The class eZDiscountRule does

*/

class eZDiscountRule extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "name" => ['name' => "Name", 'datatype' => 'string', 'default' => '', 'required' => true]], "keys" => ["id"], "increment_key" => "id", "class_name" => "eZDiscountRule", "name" => "ezdiscountrule"];
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZDiscountRule::definition(),
                                                null,
                                                ["id" => $id],
                                                $asObject );
    }

    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZDiscountRule::definition(),
                                                    null, null, null, null,
                                                    $asObject );
    }

    static function create()
    {
        $row = ["id" => null, "name" => ezpI18n::tr( "kernel/shop/discountgroup", "New discount group" )];
        return new eZDiscountRule( $row );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function removeByID( $id )
    {
        eZPersistentObject::removeObject( eZDiscountRule::definition(),
                                          ["id" => $id] );
    }
}
?>
