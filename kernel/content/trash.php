<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$Offset = $Params['Offset'];
if ( isset( $Params['UserParameters'] ) )
{
    $UserParameters = $Params['UserParameters'];
}
else
{
    $UserParameters = [];
}
$viewParameters = ['offset' => $Offset, 'namefilter' => false];
$viewParameters = array_merge( $viewParameters, $UserParameters );

$http = eZHTTPTool::instance();

$user = eZUser::currentUser();
$userID = $user->id();

if ( $http->hasPostVariable( 'RemoveButton' )  )
{
    if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        $access = $user->hasAccessTo( 'content', 'cleantrash' );
        if ( $access['accessWord'] == 'yes' || $access['accessWord'] == 'limited' )
        {
            $deleteIDArray = $http->postVariable( 'DeleteIDArray' );

            foreach ( $deleteIDArray as $deleteID )
            {

                $objectList = eZPersistentObject::fetchObjectList( eZContentObject::definition(),
                                                                   null,
                                                                   ['id' => $deleteID],
                                                                   null,
                                                                   null,
                                                                   true );
                foreach ( $objectList as $object )
                {
                    $object->purge();
                }
            }
        }
        else
        {
            return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        }
    }
}
else if ( $http->hasPostVariable( 'EmptyButton' )  )
{
    $access = $user->hasAccessTo( 'content', 'cleantrash' );
    if ( $access['accessWord'] == 'yes' || $access['accessWord'] == 'limited' )
    {
        while ( true )
        {
            // Fetch 100 objects at a time, to limit transaction size
            $objectList = eZPersistentObject::fetchObjectList( eZContentObject::definition(),
                                                               null,
                                                               ['status' => eZContentObject::STATUS_ARCHIVED],
                                                               null,
                                                               100,
                                                               true );
            if ( count( (array) $objectList ) < 1 )
                break;

            foreach ( $objectList as $object )
            {
                $object->purge();
            }
        }
    }
    else
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:content/trash.tpl' );
$Result['path'] = [['text' => ezpI18n::tr( 'kernel/content', 'Trash' ), 'url' => false]];


?>
