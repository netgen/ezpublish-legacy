<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$SectionID = $Params["SectionID"];
$Module = $Params['Module'];

if ( $http->hasPostVariable( 'BrowseCancelButton' ) )
{
    if ( $http->hasPostVariable( 'BrowseCancelURI' ) )
    {
        return $Module->redirectTo( $http->postVariable( 'BrowseCancelURI' ) );
    }
}
else
{
    $section = eZSection::fetch( $SectionID );
    if ( !is_object( $section ) )
    {
        eZDebug::writeError( "Cannot fetch section (ID = $SectionID).", 'section/assign' );
    }
    else
    {
        $currentUser = eZUser::currentUser();

        if ( $currentUser->canAssignSection( $SectionID ) )
        {
            if ( $Module->isCurrentAction( 'AssignSection' ) )
            {   // Assign section to subtree of node

                $selectedNodeIDArray = eZContentBrowse::result( 'AssignSection' );
                if ( is_array( $selectedNodeIDArray ) and count( $selectedNodeIDArray ) > 0 )
                {
                    $nodeList = eZContentObjectTreeNode::fetch( $selectedNodeIDArray );
                    if ( !is_array( $nodeList ) and is_object( $nodeList ) )
                    {
                        $nodeList = [$nodeList];
                    }

                    $allowedNodeIDList = [];
                    $deniedNodeIDList = [];
                    foreach ( $nodeList as $node )
                    {
                        $nodeID = $node->attribute( 'node_id' );
                        $object = $node->attribute( 'object' );
                        if ( $currentUser->canAssignSectionToObject( $SectionID, $object ) )
                        {
                            $allowedNodeIDList[] = $nodeID;
                        }
                        else
                        {
                            $deniedNodeIDList[] = $nodeID;
                        }
                    }

                    if ( count( $allowedNodeIDList ) > 0 )
                    {
                        $db = eZDB::instance();
                        $db->begin();
                        foreach ( $allowedNodeIDList as $nodeID )
                        {
                            eZContentObjectTreeNode::assignSectionToSubTree( $nodeID, $SectionID );
                        }
                        $db->commit();

                        // clear content caches
                        eZContentCacheManager::clearAllContentCache();
                    }
                    if ( count( $deniedNodeIDList ) > 0 )
                    {
                        $tpl = eZTemplate::factory();
                        $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
                        $tpl->setVariable( 'error_number', 1 );
                        $deniedNodes = eZContentObjectTreeNode::fetch( $deniedNodeIDList );
                        $tpl->setVariable( 'denied_node_list', $deniedNodes );

                        $Result = [];
                        $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
                        $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Sections' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Assign section' )]];
                        return;
                    }
                }
            }
            else
            {
                // Redirect to content node browse
                $classList = $currentUser->canAssignSectionToClassList( $SectionID );
                if ( (is_countable($classList) ? count( $classList ) : 0) > 0 )
                {
                    if ( in_array( '*', $classList ) )
                    {
                        $classList = false;
                    }
                    eZContentBrowse::browse( $Module,
                                             ['action_name' => 'AssignSection', 'keys' => [], 'description_template' => 'design:section/browse_assign.tpl', 'content' => ['section_id' => $SectionID], 'from_page' => '/section/assign/' . $SectionID . "/", 'cancel_page' => '/section/list', 'class_array' => $classList] );
                    return;
                }
                else
                {
                    $tpl = eZTemplate::factory();
                    $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
                    $tpl->setVariable( 'error_number', 2 );
                    $Result = [];
                    $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
                    $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Sections' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Assign section' )]];
                    return;
                }
            }
        }
        else
        {
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'section_name', $section->attribute( 'name' ) );
            $tpl->setVariable( 'error_number', 3 );
            $Result = [];
            $Result['content'] = $tpl->fetch( "design:section/assign_notification.tpl" );
            $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Sections' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/section', 'Assign section' )]];
            return;
        }
    }
}
$Module->redirectTo( '/section/list/' );

?>
