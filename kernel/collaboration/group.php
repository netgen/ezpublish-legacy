<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$ViewMode = $Params['ViewMode'];
$GroupID = $Params['GroupID'];

$Offset = $Params['Offset'];
if ( !is_numeric( $Offset ) )
    $Offset = 0;

$collabGroup = eZCollaborationGroup::fetch( $GroupID );
if ( $collabGroup === null )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( !eZCollaborationViewHandler::groupExists( $ViewMode ) )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$view = eZCollaborationViewHandler::instance( $ViewMode, eZCollaborationViewHandler::TYPE_GROUP );

$template = $view->template();

$collabGroupTitle = $collabGroup->attribute( 'title' );

$viewParameters = ['offset' => $Offset];

$tpl = eZTemplate::factory();

$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'collab_group', $collabGroup );

$Result = [];
$Result['content'] = $tpl->fetch( $template );
$Result['path'] = [['url' => 'collaboration/view/summary', 'text' => ezpI18n::tr( 'kernel/collaboration', 'Collaboration' )], ['url' => false, 'text' => 'Group'], ['url' => false, 'text' => $collabGroupTitle]];

?>
