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

include_once( 'extension/ezflow/classes/ezflowpool.php' );

class eZFlowFunctionCollection
{
    function fetchWaiting( $blockID )
    {
        $result = ['result' => eZFlowPool::waitingItems( $blockID )];
        return $result;
    }

    function fetchValid( $blockID )
    {
        $result = ['result' => eZFlowPool::validItems( $blockID )];
        return $result;
    }

    function fetchArchived( $blockID )
    {
        $result = ['result' => eZFlowPool::archivedItems( $blockID )];
        return $result;
    }

    function fetchValidNodes( $blockID )
    {
        $result = ['result' => eZFlowPool::validNodes( $blockID )];
        return $result;
    }
    
    function fetchBlock( $blockID )
    {
        $result = ['result' => eZPageBlock::fetch( $blockID )];
        return $result;
    }

    function fetchAllowedZones()
    {
        $res = [];
        $ini = eZINI::instance( 'zone.ini' );
        $allowedZoneTypes = $ini->variable( 'General', 'AllowedTypes' );

        foreach ( $allowedZoneTypes as $allowedZoneType )
        {
            $row = ['type' => $allowedZoneType, 'name' => '', 'thumbnail' => '', 'classes' => [], 'zones' => []];

            $row['name'] = $ini->variable( $allowedZoneType, 'ZoneTypeName' );
            $row['thumbnail'] = $ini->variable( $allowedZoneType, 'ZoneThumbnail' );
            $row['classes'] = $ini->variable( $allowedZoneType, 'AvailableForClasses' );
            $zones =& $row['zones'];
            
            $allowedZones = $ini->variable( $allowedZoneType, 'Zones' );
            $allowedZoneNames = $ini->variable( $allowedZoneType, 'ZoneName' );
            
            foreach ( $allowedZones as $allowedZone )
            {
                $zones[] = ['id' => $allowedZone, 'name' => $allowedZoneNames[$allowedZone]];
            }
            
            $res[] = $row;
        }

        $result = ['result' => $res];
        return $result;
    }
}

?>