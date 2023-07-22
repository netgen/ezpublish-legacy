<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$objectID = $Params['ObjectID'];
$offset = $Params['Offset'];

if( !is_numeric( $offset ) )
{
    $offset = 0;
}


if( $module->isCurrentAction( 'RemoveCollections' ) && $http->hasPostVariable( 'CollectionIDArray' ) )
{
    $collectionIDArray = $http->postVariable( 'CollectionIDArray' );
    $http->setSessionVariable( 'CollectionIDArray', $collectionIDArray );
    $http->setSessionVariable( 'ObjectID', $objectID );

    $collections = is_countable($collectionIDArray) ? count( $collectionIDArray ) : 0;

    $tpl = eZTemplate::factory();
    $tpl->setVariable( 'module', $module );
    $tpl->setVariable( 'collections', $collections );
    $tpl->setVariable( 'object_id', $objectID );
    $tpl->setVariable( 'remove_type', 'collections' );

    $Result = [];
    $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
    $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/infocollector', 'Collected information' )]];
    return;
}


if( $module->isCurrentAction( 'ConfirmRemoval' ) )
{
    $collectionIDArray = $http->sessionVariable( 'CollectionIDArray' );

    if( is_array( $collectionIDArray ) )
    {
        foreach( $collectionIDArray as $collectionID )
        {
            eZInformationCollection::removeCollection( $collectionID );
        }
    }

    $objectID = $http->sessionVariable( 'ObjectID' );
    $module->redirectTo( '/infocollector/collectionlist/' . $objectID );
}


if( eZPreferences::value( 'admin_infocollector_list_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_infocollector_list_limit' )) {
        '2' => 25,
        '3' => 50,
        default => 10,
    };
}
else
{
    $limit = 10;
}

$object = false;

if( is_numeric( $objectID ) )
{
    $object = eZContentObject::fetch( $objectID );
}

if( !$object )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$collections = eZInformationCollection::fetchCollectionsList( $objectID, /* object id */
                                                              false, /* creator id */
                                                              false, /* user identifier */
                                                              ['limit' => $limit, 'offset' => $offset] /* limit array */ );
$numberOfCollections = eZInformationCollection::fetchCollectionsCount( $objectID );

$viewParameters = ['offset' => $offset];
$objectName = $object->attribute( 'name' );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'object', $object );
$tpl->setVariable( 'collection_array', $collections );
$tpl->setVariable( 'collection_count', $numberOfCollections );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:infocollector/collectionlist.tpl' );
$Result['path'] = [['url' => '/infocollector/overview', 'text' => ezpI18n::tr( 'kernel/infocollector', 'Collected information' )], ['url' => false, 'text' => $objectName]];

?>
