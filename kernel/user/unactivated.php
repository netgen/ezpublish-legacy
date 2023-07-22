<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */


$Offset = (int)$Params['Offset'];
$Module = $Params['Module'];

$success = [];
$errors = [];

$tpl = eZTemplate::factory();
if ( $Module->isCurrentAction( 'ActivateUsers' ) )
{
    $UserIDs = (array)$Module->actionParameter( 'UserIDs' );
    foreach ( $UserIDs as $id )
    {
        $accountKey = eZUserAccountKey::fetchByUserID( $id );
        if ( $accountKey instanceof eZUserAccountKey )
        {
            // run the activation as in kernel/user/activate.php
            if ( eZOperationHandler::operationIsAvailable( 'user_activation' ) )
            {
                $operationResult = eZOperationHandler::execute(
                    'user',
                    'activation',
                    ['user_id'    => $id, 'user_hash'  => $accountKey->attribute( 'hash_key' ), 'is_enabled' => true]
                );
            }
            else
            {
                eZUserOperationCollection::activation(
                    $id, $accountKey->attribute( 'hash_key' ), true
                );
            }
            eZOperationHandler::execute(
                'user', 'register', ['user_id' => $id]
            );
            $success[] = $id;
        }
        else
        {
            eZDebug::writeError(
                "Unable to load the eZUserAccountKey object for user #{$id}",
                'user/unactivated'
            );
            $errors[] = $id;
        }
    }
    if ( !empty( $success ) )
        eZContentObject::clearCache( $success );

    $tpl->setVariable( 'success_activate', empty( $success ) ? false : $success );
    $tpl->setVariable( 'errors_activate', empty( $errors ) ? false : $errors );
}
else if ( $Module->isCurrentAction( 'RemoveUsers' ) )
{
    $UserIDs = (array)$Module->actionParameter( 'UserIDs' );
    foreach ( $UserIDs as $id )
    {
        $object = eZContentObject::fetch( $id );
        if ( $object instanceof eZContentObject
                && eZUserAccountKey::fetchByUserID( $id ) )
        {
            $success[] = $object->attribute( 'name' );
            $object->purge();
        }
        else
        {
            eZDebug::writeError(
                "Unable to load the object and/or the eZUserAccountKey object for user #{$id}",
                'user/unactivated'
            );
            $errors[] = $id;
        }
    }
    $tpl->setVariable( 'success_remove', empty( $success ) ? false : $success );
    $tpl->setVariable( 'errors_remove', empty( $errors ) ? false : $errors );
}


$limitPreference = 'admin_user_actions_list_limit';
$limit = match (eZPreferences::value( $limitPreference )) {
    2 => 25,
    3 => 50,
    default => 10,
};


$unactivatedCount = eZUserAccountKey::count( eZUserAccountKey::definition() );
$unactivated = [];

$availableSortFields = ['time' => 'time', 'login' => 'login', 'email' => 'email'];

$availableSortOrders = ['asc' => 'asc', 'desc' => 'desc'];

// default sort field/sort order
$SortField = 'time';
$SortOrder = 'asc';

if ( isset( $Params['SortField'] ) && $availableSortFields[$Params['SortField']] )
{
    $SortField = $Params['SortField'];
}

if ( isset( $Params['SortOrder'] ) && $availableSortOrders[$Params['SortOrder']] )
{
    $SortOrder = $Params['SortOrder'];
}



if ( $unactivatedCount > 0 )
{
    $unactivated = eZUser::fetchUnactivated(
        [$SortField => $SortOrder], $limit, $Offset
    );
}

$tpl->setVariable( 'unactivated_count', $unactivatedCount );
$tpl->setVariable( 'unactivated_users', $unactivated );
$tpl->setVariable( 'sort_field', $SortField );
$tpl->setVariable( 'sort_order', $SortOrder );
$tpl->setVariable( 'limit_preference', $limitPreference );
$tpl->setVariable( 'number_of_items', $limit );
$tpl->setVariable( 'view_parameters', ['offset' => $Offset] );
$tpl->setVariable( 'module', $Module );

$functions = $Module->attribute( 'functions' );
$Result['path'] = [['text' => ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false], ['text' => ezpI18n::tr( 'kernel/user', 'Unactivated users' ), 'url' => $functions['unactivated']['uri']]];
$Result['content'] = $tpl->fetch( 'design:user/unactivated.tpl' );

return $Result;
?>
