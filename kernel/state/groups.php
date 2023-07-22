<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$offset = $Params['Offset'];

$listLimitPreferenceName = 'admin_state_group_list_limit';
$listLimitPreferenceValue = eZPreferences::value( $listLimitPreferenceName );

$limit = match ($listLimitPreferenceValue) {
    '2' => 25,
    '3' => 50,
    default => 10,
};

$languages = eZContentLanguage::fetchList();



$tpl = eZTemplate::factory();

eZDebug::writeDebug( $Module->currentAction() );
if ( $Module->isCurrentAction( 'Remove' ) && $Module->hasActionParameter( 'RemoveIDList' ) )
{
    $removeIDList = $Module->actionParameter( 'RemoveIDList' );

    foreach ( $removeIDList as $removeID )
    {
        $group = eZContentObjectStateGroup::fetchById( $removeID );
        if ( $group && !$group->isInternal() )
        {
            eZContentObjectStateGroup::removeByID( $removeID );
            ezpEvent::getInstance()->notify( 'content/state/group/cache', [$removeID] );
        }
    }
}
else if ( $Module->isCurrentAction( 'Create' ) )
{
    return $Module->redirectTo( 'state/group_edit' );
}

$groups = eZContentObjectStateGroup::fetchByOffset( $limit, $offset );
$groupCount = eZPersistentObject::count( eZContentObjectStateGroup::definition() );

$viewParameters = ['offset' => $offset];

$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'list_limit_preference_name', $listLimitPreferenceName );
$tpl->setVariable( 'list_limit_preference_value', $listLimitPreferenceValue );
$tpl->setVariable( 'groups', $groups );
$tpl->setVariable( 'group_count', $groupCount );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'languages', $languages );

$Result = ['content' => $tpl->fetch( 'design:state/groups.tpl' ), 'path'    => [['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'State' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'Groups' )]]];

?>
