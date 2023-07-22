<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $Module );

$offset = $Params['Offset'];

if( eZPreferences::value( 'admin_section_list_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_section_list_limit' )) {
        '2' => 25,
        '3' => 50,
        default => 10,
    };
}
else
{
    $limit = 10;
}

if ( $http->hasPostVariable( 'CreateSectionButton' ) )
{
    $Module->redirectTo( $Module->functionURI( "edit" ) . '/0/' );
    return;
}

if ( $http->hasPostVariable( 'RemoveSectionButton' ) )
{
    $currentUser = eZUser::currentUser();
    $accessResult = $currentUser->hasAccessTo( 'section', 'edit' );
    if ( $accessResult['accessWord'] == 'yes' )
    {
        if ( $http->hasPostVariable( 'SectionIDArray' ) )
        {
            $sectionIDArray = $http->postVariable( 'SectionIDArray' );

            $sections = [];
            $sectionIDs = [];
            $sectionsUnallowed = [];
            foreach ( $sectionIDArray as $sectionID )
            {
                $section = eZSection::fetch( $sectionID );
                if ( is_object( $section ) )
                {
                    if ( $section->canBeRemoved() )
                    {
                        $sections[] = $section;
                        $sectionIDs[] = $sectionID;
                    }
                    else
                    {
                        $sectionsUnallowed[] = $section;
                    }
                }
            }

            if ( count( $sections) > 0 or
                 count( $sectionsUnallowed ) > 0 )
            {
                $http->setSessionVariable( 'SectionIDArray', $sectionIDs );
                $tpl->setVariable( 'delete_result', $sections ); // deprecated, left for BC
                $tpl->setVariable( 'allowed_sections', $sections );
                $tpl->setVariable( 'unallowed_sections', $sectionsUnallowed );

                $Result = [];
                $Result['content'] = $tpl->fetch( "design:section/confirmremove.tpl" );
                $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Sections' )]];
                return;
            }
        }
    }
    else
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

if ( $http->hasPostVariable( 'ConfirmRemoveSectionButton' ) )
{
    $currentUser = eZUser::currentUser();
    $accessResult = $currentUser->hasAccessTo( 'section', 'edit' );
    if ( $accessResult['accessWord'] == 'yes' )
    {
        if ( $http->hasSessionVariable( 'SectionIDArray' ) )
        {
            $sectionIDArray = $http->sessionVariable( 'SectionIDArray' );

            $db = eZDB::instance();
            $db->begin();
            foreach ( $sectionIDArray as $sectionID )
            {
                $section = eZSection::fetch( $sectionID );
                if ( is_object( $section ) and
                     $section->canBeRemoved() )
                {
                    // Clear content cache if needed
                    eZContentCacheManager::clearContentCacheIfNeededBySectionID( $sectionID );
                    $section->remove();
                    ezpEvent::getInstance()->notify( 'content/section/cache', [$sectionID] );
                }
            }
            $db->commit();
        }
    }
    else
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$viewParameters = ['offset' => $offset];
$sectionArray = eZSection::fetchByOffset( $offset, $limit );
$sectionCount = eZSection::sectionCount();

$currentUser = eZUser::currentUser();
$allowedAssignSectionList = $currentUser->canAssignSectionList();

$tpl->setVariable( "limit", $limit );
$tpl->setVariable( 'section_array', $sectionArray );
$tpl->setVariable( 'section_count', $sectionCount );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'allowed_assign_sections', $allowedAssignSectionList );

$Result = [];
$Result['content'] = $tpl->fetch( "design:section/list.tpl" );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Sections' )]];

?>
