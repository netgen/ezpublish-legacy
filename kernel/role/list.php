<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();


$Module = $Params['Module'];

$offset = $Params['Offset'];

if( eZPreferences::value( 'admin_role_list_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_role_list_limit' )) {
        '2' => 25,
        '3' => 50,
        default => 10,
    };
}
else
{
    $limit = 10;
}

if ( $http->hasPostVariable( 'RemoveButton' )  )
{
   if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $deleteIDArray as $deleteID )
        {
            eZRole::removeRole( $deleteID );
        }
        // Clear role caches.
        eZRole::expireCache();

        // Clear all content cache.
        eZContentCacheManager::clearAllContentCache();

        $db->commit();
    }
}
// Redirect to content node browse in the user tree
// Assign the role for a user or group
if ( $Module->isCurrentAction( 'AssignRole' ) )
{
    $selectedObjectIDArray = eZContentBrowse::result( 'AssignRole' );

    foreach ( $selectedObjectIDArray as $objectID )
    {
        $role->assignToUser( $objectID );
    }
    // Clear role caches.
    eZRole::expireCache();

    // Clear all content cache.
    eZContentCacheManager::clearAllContentCache();
}

if ( $http->hasPostVariable( 'NewButton' )  )
{
    $role = eZRole::createNew( );
    return $Module->redirectToView( 'edit', [$role->attribute( 'id' )] );
}

$viewParameters = ['offset' => $offset];
$tpl = eZTemplate::factory();

$roles = eZRole::fetchByOffset( $offset, $limit, $asObject = true, $ignoreTemp = true );
$roleCount = eZRole::roleCount();
$tempRoles = eZRole::fetchList( $temporaryVersions = true );
$tpl->setVariable( 'roles', $roles );
$tpl->setVariable( 'role_count', $roleCount );
$tpl->setVariable( 'temp_roles', $tempRoles );
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );


$Result = [];
$Result['content'] = $tpl->fetch( 'design:role/list.tpl' );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/role', 'Role list' )]];
?>
