<?php
//
// Definition of ezjscServerFunctionsNode class
//
// Created on: <01-Jun-2010 00:00:00 ls>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ JSCore extension for eZ Publish
// SOFTWARE RELEASE: 1.x
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

/**
 * ezjscServerFunctionsNode class definition that provide node fetch functions
 *
 */
class ezjscServerFunctionsNode extends ezjscServerFunctions
{
    /**
     * Returns a subtree node items for given parent node
     *
     * Following parameters are supported:
     * ezjscnode::subtree::parent_node_id::limit::offset::sort::order
     *
     * @since 1.2
     * @param mixed $args
     * @return array
     */
    public static function subTree( mixed $args )
    {
        $parentNodeID = $args[0] ?? null;
        $limit = $args[1] ?? 25;
        $offset = $args[2] ?? 0;
        $sort = isset( $args[3] ) ? self::sortMap( $args[3] ) : 'published';
        $order = $args[4] ?? false;
        $objectNameFilter = $args[5] ?? '';

        if ( !$parentNodeID )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Fetch node list', 'Parent node id is not valid' );
        }

        $node = eZContentObjectTreeNode::fetch( $parentNodeID );
        if ( !$node instanceOf eZContentObjectTreeNode )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Fetch node list', "Parent node '$parentNodeID' is not valid" );
        }

        $ezjscoreIni = eZINI::instance( 'ezjscore.ini' );
        $hardLimit = (int)$ezjscoreIni->variable( 'ezjscServer_ezjscnode', 'HardLimit' );

        if ( $hardLimit > 0 && $limit > $hardLimit )
        {
            $limit = $hardLimit;
        }

        $params = ['Depth' => 1, 'Limit' => $limit, 'Offset' => $offset, 'SortBy' => [[$sort, $order]], 'DepthOperator' => 'eq', 'ObjectNameFilter' => $objectNameFilter, 'AsObject' => true];

       // fetch nodes and total node count
        $count = $node->subTreeCount( $params );
        if ( $count )
        {
            $nodeArray = $node->subTree( $params );
        }
        else
        {
            $nodeArray = [];
        }
        unset( $node );// We have on purpose not checked permission on $node itself, so it should not be used

        // generate json response from node list
        if ( $nodeArray )
        {
            $list = ezjscAjaxContent::nodeEncode( $nodeArray, ['formatDate' => 'shortdatetime', 'fetchThumbPreview' => true, 'fetchSection' => true, 'fetchCreator' => true, 'fetchClassIcon' => true], 'raw' );
        }
        else
        {
            $list = [];
        }

        return ['parent_node_id' => $parentNodeID, 'count' => count( (array) $nodeArray ), 'total_count' => (int)$count, 'list' => $list, 'limit' => $limit, 'offset' => $offset, 'sort' => $sort, 'order' => $order];
    }

    /**
     * Returns a node data for given object / node id
     *
     * Following parameters are supported:
     * ezjscnode::load::embed_id[::attribute[::load_image_size]]
     *
     * eg: ezjscnode::load::ezobject_46::image::large
     * eg: ezjscnode::load::eznode_44::summary
      *eg: ezjscnode::load::44::summary (44 is in this case node id)
     *
     * @since 1.2
     * @param mixed $args
     * @throws InvalidArgumentException
     * @return array
     */
    public static function load( mixed $args )
    {
        $embedObject = false;
        if ( isset( $args[0] ) && $args[0] )
        {
            $embedType = 'eznode';
            if (  is_numeric( $args[0] ) )
                $embedId = $args[0];
            else
                [$embedType, $embedId] = explode('_', (string) $args[0]);

            if ( $embedType === 'eznode' || strcasecmp( $embedType  , 'eznode'  ) === 0 )
                $embedObject = eZContentObject::fetchByNodeID( $embedId );
            else
                $embedObject = eZContentObject::fetch( $embedId );
        }

        if ( !$embedObject instanceof eZContentObject )
        {
           throw new InvalidArgumentException( "Argument 1: '$embedType\_$embedId' does not map to a valid content object" );
        }
        else if ( !$embedObject->canRead() )
        {
            throw new InvalidArgumentException( "Argument 1: '$embedType\_$embedId' is not available" );
        }

        // Params for node to json encoder
        $params    = ['loadImages' => true];
        $params['imagePreGenerateSizes'] = ['small', 'original'];

        // look for attribute parameter ( what attribute we should load )
        if ( isset( $args[1] ) && $args[1] )
            $params['dataMap'] = [$args[1]];

        // what image sizes we want returned with full data ( url++ )
        if ( isset( $args[2] ) && $args[2] )
            $params['imagePreGenerateSizes'][] = $args[2];

        // Simplify and load data in accordance to $params
        return ezjscAjaxContent::simplify( $embedObject, $params );
    }

    /**
     * Updating priority sorting for given node
     *
     * @since 1.2
     * @return array
     */
    public static function updatePriority( mixed $args )
    {
        $http = eZHTTPTool::instance();

        if ( !$http->hasPostVariable('ContentNodeID')
                || !$http->hasPostVariable('PriorityID')
                    || !$http->hasPostVariable('Priority') )
        {
            return [];
        }

        $contentNodeID = $http->postVariable('ContentNodeID');
        $priorityArray = $http->postVariable('Priority');
        $priorityIDArray = $http->postVariable('PriorityID');

        $contentNode = eZContentObjectTreeNode::fetch( $contentNodeID );
        if ( !$contentNode instanceof eZContentObjectTreeNode )
        {
           throw new InvalidArgumentException( "Argument ContentNodeID: '$contentNodeID' does not exist" );
        }
        else if ( !$contentNode->canEdit() )
        {
            throw new InvalidArgumentException( "Argument ContentNodeIDs: '$contentNodeID' is not available" );
        }

        if ( eZOperationHandler::operationIsAvailable( 'content_updatepriority' ) )
        {
            $operationResult = eZOperationHandler::execute( 'content', 'updatepriority',
                                                             ['node_id' => $contentNodeID, 'priority' => $priorityArray, 'priority_id' => $priorityIDArray], null, true );
        }
        else
        {
            eZContentOperationCollection::updatePriority( $contentNodeID, $priorityArray, $priorityIDArray );
        }

        if ( $http->hasPostVariable( 'ContentObjectID' ) )
        {
            $objectID = $http->postVariable( 'ContentObjectID' );
            eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
        }
    }

    /**
     * A helper function which maps sort keys from encoded JSON node
     * to supported values
     *
     * @since 1.2
     * @param string $sort
     * @return string
     */
    protected static function sortMap( $sort )
    {
        $sortKey = match ($sort) {
            'modified_date' => 'modified',
            'published_date' => 'published',
            'hidden_status_string' => 'visibility',
            default => $sort,
        };

        return $sortKey;
    }
}

?>
