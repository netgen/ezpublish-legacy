<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$tpl    = eZTemplate::factory();
$http   = eZHTTPTool::instance();


$Offset = $Params['Offset'];

if ( !is_numeric( $Offset ) )
    $Offset = 0;

$parents = [];

// Make sure user has session (if not, then this can't possible be a valid browse request)
if ( !eZSession::userHasSessionCookie() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// Check that Browse parameters exists
if ( !$http->hasSessionVariable( 'BrowseParameters' ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

// Check if node parameters exist
$browse = new eZContentBrowse();
if ( !isset( $Params['NodeID'] ) && !isset( $Params['NodeList'] ) && !$browse->hasAttribute( 'start_node' ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

// We get node list when browse is execiuted from search engine ( "search in browse" functionality )
if ( isset( $Params['NodeList'] ) )
{
    $nodeList = $Params['NodeList']['SearchResult'];
    $nodeListCount = $Params['NodeList']['SearchCount'];
    $requestedURI = $Params['NodeList']['RequestedURI'];
    $requestedURISuffix = $Params['NodeList']['RequestedURISuffix'];

    if ( isset( $Params['NodeID'] ) && is_numeric( $Params['NodeID'] ) )
    {
        $NodeID = $Params['NodeID'];
    }
}
else
{

    if ( isset( $Params['NodeID'] ) && is_numeric( $Params['NodeID'] ) )
    {
        $NodeID = $Params['NodeID'];
        $browse->setStartNode( $NodeID );
    }

    $NodeID = $browse->attribute( 'start_node' );
}

if ( isset( $NodeID ) )
{
    $node = eZContentObjectTreeNode::fetch( $NodeID );
    if ( !$node )
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

    if ( $node->attribute( 'is_invisible' ) && !eZContentObjectTreeNode::showInvisibleNodes() )
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }

    $object = $node->attribute( 'object' );
    if ( !$object )
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

    if ( !$object->attribute( 'can_read' ) || !$node->attribute( 'can_read' ) )
    {
        if ( !$node->attribute( 'children_count' ) )
        {
            return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        }
    }
    $parents = $node->attribute( 'path' );
}

$cancelAction = trim( (string) $browse->attribute( 'cancel_page' ) );
if ( $cancelAction == trim( (string) $browse->attribute( 'from_page' ) ) )
{
    $cancelAction = false;
}

//setting keys for override
$res = eZTemplateDesignResource::instance();

$keyArray = [];
if ( $browse->hasAttribute( 'keys' ) )
{
    $attributeKeys = $browse->attribute( 'keys' );
    if ( is_array( $attributeKeys ) )
    {
        foreach ( $attributeKeys as $attributeKey => $attributeValue )
        {
            $keyArray[] = [$attributeKey, $attributeValue];
        }
    }
    $res->setKeys( $keyArray );
}


$tpl->setVariable( 'browse', $browse );
$tpl->setVariable( 'csm_menu_item_click_action', '/content/browse' );
$tpl->setVariable( 'cancel_action', $cancelAction );

if ( isset( $nodeList ) )
{
    $tpl->setVariable( 'node_list', $nodeList );
    $tpl->setVariable( 'node_list_count', $nodeListCount );
    $tpl->setVariable( 'requested_uri', $requestedURI );
    $tpl->setVariable( 'requested_uri_suffix', $requestedURISuffix );
}
else
{
    $tpl->setVariable( 'main_node', $node );
    $tpl->setVariable( 'node_id', $NodeID );
    $tpl->setVariable( 'parents', $parents );
}

if ( isset( $Params['UserParameters'] ) )
{
    $UserParameters = $Params['UserParameters'];
}
else
{
    $UserParameters = [];
}
$viewParameters = ['offset' => $Offset, 'namefilter' => false];
$viewParameters = array_merge( $viewParameters, $UserParameters );

$tpl->setVariable( 'view_parameters', $viewParameters );

$tpl->setVariable( 'path', false );

if (isset( $GLOBALS['eZDesignKeys']['section'] ))
{
    $globalSectionID = $GLOBALS['eZDesignKeys']['section'];
    unset($GLOBALS['eZDesignKeys']['section']);
}

$Result = [];
$Result['view_parameters'] = $viewParameters;

// Fetch the navigation part from the section information
$Result['navigation_part'] = 'ezcontentnavigationpart';
if ( isset( $object ) && isset( $node ) )
{
    $globalSectionID = $object->attribute( 'section_id' );
    $section = eZSection::fetch( $object->attribute( 'section_id' ) );
    if ( $section )
    {
        $Result['navigation_part'] = $section->attribute( 'navigation_part_identifier' );
    }
    $Result['node_id'] = $node->attribute( 'node_id' );

    $res->setKeys( [
        ['object', $object->attribute( 'id' )],
        // Object ID
        ['node', $node->attribute( 'node_id' )],
        // Node ID
        ['parent_node', $node->attribute( 'parent_node_id' )],
        // Parent Node ID
        ['class', $object->attribute( 'contentclass_id' )],
        // Class ID
        ['depth', $node->attribute( 'depth' )],
        ['remote_id', $object->attribute( 'remote_id' )],
        ['node_remote_id', $node->attribute( 'remote_id' )],
        ['url_alias', $node->attribute( 'url_alias' )],
        ['class_identifier', $node->attribute( 'class_identifier' )],
        ['section', $object->attribute('section_id')],
        ['class_group', $object->attribute( 'match_ingroup_id_list' )],
        ['state', $object->attribute( 'state_id_array' )],
        ['state_identifier', $object->attribute( 'state_identifier_array' )],
    ] );

}

$res->setKeys( [['view_offset', $Offset], ['navigation_part_identifier', $Result['navigation_part']]] );

//$Result['path'] = $path;
$Result['content'] = $tpl->fetch( 'design:content/browse.tpl' );

if (isset( $globalSectionID ))
{
    $GLOBALS['eZDesignKeys']['section'] = $globalSectionID;
}

$templatePath = $tpl->variable( 'path' );
if ( $templatePath )
{
    $Result['path'] = $templatePath;
}
elseif ( isset( $nodeList ) && !( isset( $object ) && isset( $node ) ) )
{
    $Result['path'] = [['text' => ezpI18n::tr( 'kernel/content', 'Search' ), 'url' => false]];
}
else
{
    $path = [];
    foreach ( $parents as $parent )
    {
        $path[] = ['text' => $parent->attribute( 'name' ), 'url' => '/content/browse/' . $parent->attribute( 'node_id' ) . '/', 'node_id' => $parent->attribute( 'node_id' )];
    }
    $path[] = ['text' => $object->attribute( 'name' ), 'url' => false, 'node_id' => $node->attribute( 'node_id' )];
    $Result['path'] = $path;
}


?>
