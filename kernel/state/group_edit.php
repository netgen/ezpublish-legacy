<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$GroupIdentifier = $Params['GroupIdentifier'];
$Module = $Params['Module'];

$group = $GroupIdentifier === null ? new eZContentObjectStateGroup() : eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );

if ( !is_object( $group ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

if ( $group->isInternal() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}



$tpl = eZTemplate::factory();

$currentAction = $Module->currentAction();

if ( $currentAction == 'Cancel' )
{
    return $Module->redirectTo( 'state/groups' );
}
else if ( $currentAction == 'Store' )
{
    $group->fetchHTTPPersistentVariables();

    $messages = [];
    $isValid = $group->isValid( $messages );

    if ( $isValid )
    {
        $group->store();
        ezpEvent::getInstance()->notify( 'content/state/group/cache', [$group->attribute( 'id' )] );
        if ( $GroupIdentifier === null )
        {
            return $Module->redirectTo( 'state/group/' . $group->attribute( 'identifier' ) );
        }
        else
        {
            return $Module->redirectTo( 'state/groups' );
        }
    }

    $tpl->setVariable( 'is_valid', $isValid );
    $tpl->setVariable( 'validation_messages', $messages );
}

$tpl->setVariable( 'group', $group );

if ( $GroupIdentifier === null )
{
    $path = [['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'State' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'New group' )]];
}
else
{
    $path = [['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'State' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/state', 'Group edit' )], ['url' => false, 'text' => $group->attribute( 'identifier' )]];
}

$Result = ['content' => $tpl->fetch( 'design:state/group_edit.tpl' ), 'path'    => $path];

?>
