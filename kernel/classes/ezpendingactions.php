<?php
/**
 * File containing the eZPendingActions class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZPendingActions extends eZPersistentObject
{
    /**
     * Schema definition
     * eZPersistentObject implementation for ezpending_actions table
     * @see kernel/classes/ezpersistentobject.php
     * @return array
     */
    public static function definition()
    {
        return ['fields' => ['id' => ['name' => 'id', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'action' => ['name' => 'action', 'datatype' => 'string', 'default' => null, 'required' => true], 'created' => ['name' => 'created', 'datatype' => 'integer', 'default' => null, 'required' => false], 'param' => ['name' => 'param', 'datatype' => 'string', 'default' => null, 'required' => false]], 'keys' => ['id'], 'class_name' => 'eZPendingActions', 'name' => 'ezpending_actions', 'function_attributes' => []];
    }

    /**
     * Fetches a pending actions list by action name
     * @param string $action
     * @param array $aCreationDateFilter Created date filter array (default is empty array). Must be a 2 entries array.
     *                                   First entry is the filter token (can be '=', '<', '<=', '>', '>=')
     *                                   Second entry is the filter value (timestamp)
     * @return array|null Array of eZPendingActions or null if no entry has been found
     */
    public static function fetchByAction( $action, array $aCreationDateFilter = [] )
    {
        $filterConds = ['action' => $action];

        // Handle creation date filter
        if( !empty( $aCreationDateFilter ) )
        {
            if( count( $aCreationDateFilter ) != 2 )
            {
                eZDebug::writeError( self::class.'::'.__METHOD__.' : Wrong number of entries for Creation date filter array' );
                return null;
            }

            [$filterToken, $filterValue] = $aCreationDateFilter;
            $aAuthorizedFilterTokens = ['=', '<', '>', '<=', '>='];
            if( !is_string( $filterToken ) || !in_array( $filterToken, $aAuthorizedFilterTokens ) )
            {
                eZDebug::writeError( self::class.'::'.__METHOD__.' : Wrong filter type for creation date filter' );
                return null;
            }

            $filterConds['created'] = [$filterToken, $filterValue];
        }

        $result = parent::fetchObjectList( self::definition(), null, $filterConds );

        return $result;
    }

    /**
     * Remove entries by action
     * @param string $action
     * @param array $filterConds Additional filter conditions, as supported by {@link eZPersistentObject::fetchObjectList()} ($conds param).
     *                           For consistency sake, if an 'action' key is set here, it won't be taken into account
     */
    public static function removeByAction( $action, array $filterConds = [] )
    {
        parent::removeObject( self::definition(), ['action' => $action] + $filterConds );
    }
}

?>
