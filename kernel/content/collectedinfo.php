<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];
$viewMode = 'full';
$nodeID = $Params['NodeID'];
$userParameters = [];

if ( isset( $Params['UserParameters'] ) )
{
    $userParameters = $Params['UserParameters'];
}

if ( !$nodeID )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( !$node )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( $node->attribute( 'is_invisible' ) && !eZContentObjectTreeNode::showInvisibleNodes() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$object = $node->attribute( 'object' );
if ( !$object )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
if ( !$object->attribute( 'can_read' ) )
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

$http = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$icID = false;
if ( $http->hasSessionVariable( 'InformationCollectionMap' ) ) {
    $icMap = $http->sessionVariable( 'InformationCollectionMap' );

    if ( isset( $icMap[$object->attribute( 'id' )] ) ) {
        $icID = $icMap[$object->attribute( 'id' )];
    }
}
if ( !$icID && eZInformationCollection::isCollectingSensitiveData( $object ) )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$informationCollectionTemplate = eZInformationCollection::templateForObject( $object );
$attributeHideList = eZInformationCollection::attributeHideList();

$tpl->setVariable( 'node_id', $nodeID );
$tpl->setVariable( 'collection_id', $icID );
$tpl->setVariable( 'node', $node );
$tpl->setVariable( 'object', $object );
$tpl->setVariable( 'viewmode', $viewMode );
$tpl->setVariable( 'view_parameters', $userParameters );
$tpl->setVariable( 'attribute_hide_list', $attributeHideList );
$tpl->setVariable( 'error', false );

$section = eZSection::fetch( $object->attribute( 'section_id' ) );
if ( $section )
    $navigationPartIdentifier = $section->attribute( 'navigation_part_identifier' );

$res = eZTemplateDesignResource::instance();
$res->setKeys( [['object', $object->attribute( 'id' )], ['node', $node->attribute( 'node_id' )], ['parent_node', $node->attribute( 'parent_node_id' )], ['class', $object->attribute( 'contentclass_id' )], ['class_identifier', $node->attribute( 'class_identifier' )], ['viewmode', $viewMode], ['remote_id', $object->attribute( 'remote_id' )], ['node_remote_id', $node->attribute( 'remote_id' )], ['navigation_part_identifier', $navigationPartIdentifier], ['depth', $node->attribute( 'depth' )], ['url_alias', $node->attribute( 'url_alias' )], ['class_group', $object->attribute( 'match_ingroup_id_list' )], ['state', $object->attribute( 'state_id_array' )], ['state_identifier', $object->attribute( 'state_identifier_array' )]] );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:content/collectedinfo/' . $informationCollectionTemplate . '.tpl' );
$Result['section_id'] = $object->attribute( 'section_id' );
$Result['node_id'] = $node->attribute( 'node_id' );
$Result['view_parameters'] = $userParameters;
$Result['navigation_part'] = $navigationPartIdentifier;

$title = $object->attribute( 'name' );
if ( $tpl->hasVariable( 'title' ) )
    $title = $tpl->variable( 'title' );

// create path
$parents = $node->attribute( 'path' );

$path = [];
foreach ( $parents as $parent )
{
    $path[] = ['text' => $parent->attribute( 'name' ), 'url' => '/content/view/full/' . $parent->attribute( 'node_id' ), 'url_alias' => $parent->attribute( 'url_alias' ), 'node_id' => $parent->attribute( 'node_id' )];
}

$titlePath = $path;
$path[] = ['text' => $object->attribute( 'name' ), 'url' => false, 'url_alias' => false, 'node_id' => $node->attribute( 'node_id' )];

$titlePath[] = ['text' => $title, 'url' => false, 'url_alias' => false];

$Result['path'] = $path;
$Result['title_path'] = $titlePath;

?>
