<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class eZFlowPoolItem extends eZPersistentObject
{
    /**
     * Constructor
     * 
     * @param array $row
     */
    function __construct( $row )
    {
        parent::__construct( $row );
    }

    /**
     * Return the definition for the object
     * 
     * @static
     * @return array
     */
    public static function definition()
    {
        return ['fields' => ['block_id' => ['name' => 'BlockID', 'datatype' => 'string', 'default' => '', 'required' => true, 'foreign_class' => 'eZFlowBlock', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'object_id' => ['name' => 'ObjectID', 'datatype' => 'integer', 'default' => '0', 'required' => true], 'node_id' => ['name' => 'NodeID', 'datatype' => 'integer', 'default' => '0', 'required' => true], 'priority' => ['name' => 'Priority', 'datatype' => 'integer', 'default' => '0', 'required' => false], 'ts_publication' => ['name' => 'TSPublication', 'datatype' => 'integer', 'default' => '0', 'required' => false], 'ts_visible' => ['name' => 'TSVisible', 'datatype' => 'integer', 'default' => '0', 'required' => false], 'ts_hidden' => ['name' => 'TSHidden', 'datatype' => 'integer', 'default' => '0', 'required' => false], 'rotation_until' => ['name' => 'RotationUntil', 'datatype' => 'integer', 'default' => '0', 'required' => false], 'moved_to' => ['name' => 'MovedTo', 'datatype' => 'string', 'default' => '', 'required' => false, 'foreign_class' => 'eZFlowBlock', 'foreign_attribute' => 'id', 'multiplicity' => '1..*']], 'keys' => ['block_id', 'object_id'], 'function_attributes' => ['block' => 'block'], 'class_name' => 'eZFlowPoolItem', 'sort' => ['block_id' => 'asc'], 'name' => 'ezm_pool'];
    }

    /**
     * Fetch pool items by content object ID
     *
     * @param $objectId
     * @return array
     */
    static function fetchListByContentObjectId( $objectId )
    {
        $conds = ['object_id' => $objectId];
        $objectList = eZPersistentObject::fetchObjectList( self::definition(), null, $conds );
        return $objectList;
    }

    /**
     * Returns eZFlowBlock object for current pool item
     *
     * @return eZFlowBlock|null
     */
    public function block()
    {
        return eZFlowBlock::fetch( $this->attribute( 'block_id' ) );
    }
}

?>
