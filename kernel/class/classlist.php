<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$GroupID = false;
if ( isset( $Params["GroupID"] ) )
    $GroupID = $Params["GroupID"];

$http = eZHTTPTool::instance();
$http->setSessionVariable( 'FromGroupID', $GroupID );
if ( $http->hasPostVariable( "RemoveButton" ) )
{
    if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
        if ( $deleteIDArray !== null )
        {
            $http->setSessionVariable( 'DeleteClassIDArray', $deleteIDArray );
            $Module->redirectTo( $Module->functionURI( 'removeclass' ) . '/'  . $GroupID . '/' );
        }
    }
}

if ( $http->hasPostVariable( "NewButton" ) )
{
    if ( $http->hasPostVariable( "CurrentGroupID" ) )
        $GroupID = $http->postVariable( "CurrentGroupID" );
    if ( $http->hasPostVariable( "CurrentGroupName" ) )
        $GroupName = $http->postVariable( "CurrentGroupName" );
    if ( $http->hasPostVariable( "ClassLanguageCode" ) )
        $LanguageCode = $http->postVariable( "ClassLanguageCode" );
    $params = [null, $GroupID, $GroupName, $LanguageCode];
    $unorderedParams = ['Language' => $LanguageCode];
    $Module->run( 'edit', $params, $unorderedParams );
    return;
}

if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
{
    $TemplateData = [["name" => "groupclasses", "http_base" => "ContentClass", "data" => ["command" => "groupclass_list", "type" => "class"]]];
}

$Module->setTitle( ezpI18n::tr( 'kernel/class', 'Class list of group' ) . ' ' . $GroupID );
$tpl = eZTemplate::factory();

$user = eZUser::currentUser();
foreach( $TemplateData as $tpldata )
{
    $tplname = $tpldata["name"];

    $groupInfo =  eZContentClassGroup::fetch( $GroupID );

    if( !$groupInfo )
    {
       return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }

    $list = eZContentClassClassGroup::fetchClassList( 0, $GroupID, $asObject = true );
    $groupModifier = eZContentObject::fetch( $groupInfo->attribute( 'modifier_id') );
    $tpl->setVariable( $tplname, $list );
    $tpl->setVariable( "class_count", is_countable($list) ? count( $list ) : 0 );
    $tpl->setVariable( "GroupID", $GroupID );
    $tpl->setVariable( "group", $groupInfo );
    $tpl->setVariable( "group_modifier", $groupModifier );
}

$group = eZContentClassGroup::fetch( $GroupID );
$groupName = $group->attribute( 'name' );


$tpl->setVariable( "module", $Module );

$Result = [];
$Result['content'] = $tpl->fetch( "design:class/classlist.tpl" );
$Result['path'] = [['url' => '/class/grouplist/', 'text' => ezpI18n::tr( 'kernel/class', 'Class groups' )], ['url' => false, 'text' => $groupName]];
?>
