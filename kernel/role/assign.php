<?php
//
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();

$Module = $Params['Module'];
$roleID = $Params['RoleID'];
$limitIdent = $Params['LimitIdent'];
$limitValue = $Params['LimitValue'];

if ( $http->hasPostVariable( 'AssignSectionCancelButton' ) )
{
    $Module->redirectTo( '/role/view/' . $roleID );
}

if ( $http->hasPostVariable( 'BrowseCancelButton' ) )
{
    if ( $http->hasPostVariable( 'BrowseCancelURI' ) )
    {
        return $Module->redirectTo( $http->postVariable( 'BrowseCancelURI' ) );
    }
}

if ( $http->hasPostVariable( 'AssignSectionID' ) &&
     $http->hasPostVariable( 'SectionID' ) )
{
    $Module->redirectTo( '/role/assign/' . $roleID . '/' . $limitIdent . '/' . $http->postVariable( 'SectionID' ) );
}
else if ( $http->hasPostVariable( 'BrowseActionName' ) and
          $http->postVariable( 'BrowseActionName' ) == 'SelectObjectRelationNode' )
{
    $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
    if ( (is_countable($selectedNodeIDArray) ? count( $selectedNodeIDArray ) : 0) == 1 )
    {
        $limitValue = $selectedNodeIDArray[0];
    }
    $Module->redirectTo( '/role/assign/' . $roleID . '/' . $limitIdent . '/' . $limitValue );
}
else if ( $http->hasPostVariable( 'BrowseActionName' ) and
          $http->postVariable( 'BrowseActionName' ) == 'AssignRole' )
{
    $selectedObjectIDArray = $http->postVariable( 'SelectedObjectIDArray' );
    $role = eZRole::fetch( $roleID );

    $db = eZDB::instance();
    $db->begin();
    foreach ( $selectedObjectIDArray as $objectID )
    {
        $role->assignToUser( $objectID, $limitIdent, $limitValue );
    }
    // Clear role caches.
    eZRole::expireCache();

    $db->commit();
    if ( (is_countable($selectedObjectIDArray) ? count( $selectedObjectIDArray ) : 0) > 0 )
    {
        eZContentCacheManager::clearAllContentCache();
    }

    /* Clean up policy cache */
    eZUser::cleanupCache();

    $Module->redirectTo( '/role/view/' . $roleID );
}
else if ( is_string( $limitIdent ) && !isset( $limitValue ) )
{
    switch( $limitIdent )
    {
        case 'subtree':
        {
            eZContentBrowse::browse( $Module,
                                     ['action_name' => 'SelectObjectRelationNode', 'from_page' => '/role/assign/' . $roleID . '/' . $limitIdent, 'cancel_page' => '/role/view/' . $roleID] );
            return;
        } break;

        case 'section':
        {
            $sectionArray = eZSection::fetchList( );
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'section_array', $sectionArray );
            $tpl->setVariable( 'role_id', $roleID );
            $tpl->setVariable( 'limit_ident', $limitIdent );

            $Result = [];
            $Result['content'] = $tpl->fetch( 'design:role/assign_limited_section.tpl' );
            $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/role', 'Limit on section' )]];
            return;
        } break;

        default:
        {
            eZDebug::writeWarning( 'Unsupported assign limitation: ' . $limitIdent );
            $Module->redirectTo( '/role/view/' . $roleID );
        } break;
    }
}
else if ( is_numeric( $roleID ) )
{
    eZContentBrowse::browse( $Module,
                             ['action_name' => 'AssignRole', 'from_page' => '/role/assign/' . $roleID . '/' . $limitIdent . '/' . $limitValue, 'cancel_page' => '/role/view/' . $roleID] );

    return;
}

?>
