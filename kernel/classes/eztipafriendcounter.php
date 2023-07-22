<?php
/**
 * File containing the eZTipafriendCounter class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

//!! eZKernel
//! The class eZTipafriendCounter
/*!

*/

class eZTipafriendCounter extends eZPersistentObject
{
    static function definition()
    {
        return ['fields' => ['node_id' => ['name' => 'NodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObjectTreeNode', 'foreign_attribute' => 'node_id', 'multiplicity' => '1..*'], 'count' => [
            'name' => 'Count',
            // deprecated column, must not be used
            'datatype' => 'integer',
            'default' => 0,
            'required' => true,
        ], 'requested' => ['name' => 'Requested', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['node_id', 'requested'], 'relations' => ['node_id' => ['class' => 'eZContentObjectTreeNode', 'field' => 'node_id']], 'class_name' => 'eZTipafriendCounter', 'sort' => ['count' => 'desc'], 'name' => 'eztipafriend_counter'];
    }

    static function create( $nodeID )
    {
        return new eZTipafriendCounter( ['node_id' => $nodeID, 'count' => 0, 'requested' => time()] );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function removeForNode( $nodeID )
    {
        eZPersistentObject::removeObject( eZTipafriendCounter::definition(),
                                          ['node_id' => $nodeID] );
    }

    static function fetch( $nodeID, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZTipafriendCounter::definition(),
                                                null,
                                                ['node_id' => $nodeID],
                                                $asObject );
    }

    /*!
     \static
     Removes all counters for tipafriend functionality.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM eztipafriend_counter" );
    }

    /// \privatesection
    public $NodeID;
    public $Count;
}

?>
