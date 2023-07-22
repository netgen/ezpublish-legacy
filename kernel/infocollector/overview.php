<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$offset = $Params['Offset'];

if( !is_numeric( $offset ) )
{
    $offset = 0;
}


if( $module->isCurrentAction( 'RemoveObjectCollection' ) && $http->hasPostVariable( 'ObjectIDArray' ) )
{
    $objectIDArray = $http->postVariable( 'ObjectIDArray' );
    $http->setSessionVariable( 'ObjectIDArray', $objectIDArray );

    $collections = 0;

    foreach( $objectIDArray as $objectID )
    {
        $collections += eZInformationCollection::fetchCollectionCountForObject( $objectID );
    }

    $tpl = eZTemplate::factory();
    $tpl->setVariable( 'module', $module );
    $tpl->setVariable( 'collections', $collections );
    $tpl->setVariable( 'remove_type', 'objects' );

    $Result = [];
    $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
    $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/infocollector', 'Collected information' )]];
    return;
}


if( $module->isCurrentAction( 'ConfirmRemoval' ) )
{

    $objectIDArray = $http->sessionVariable( 'ObjectIDArray' );

    if( is_array( $objectIDArray) )
    {
        foreach( $objectIDArray as $objectID )
        {
            eZInformationCollection::removeContentObject( $objectID );
        }
    }
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


$db = eZDB::instance();
$objects = $db->arrayQuery( 'SELECT DISTINCT ezcontentobject.id AS contentobject_id,
                                             ezcontentobject.name,
                                             ezcontentobject_tree.main_node_id,
                                             ezcontentclass.serialized_name_list,
                                             ezcontentclass.identifier AS class_identifier
                             FROM ezcontentobject,
                                  ezcontentobject_tree,
                                  ezcontentclass
                             WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
                                   AND ezcontentobject.contentclass_id = ezcontentclass.id
                                   AND ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED . '
                                   AND ezcontentobject.id IN
                                   ( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
                             ORDER BY ezcontentobject.name ASC',
                             ['limit'  => (int)$limit, 'offset' => (int)$offset] );

$infoCollectorObjectsQuery = $db->arrayQuery( 'SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
                                               FROM ezinfocollection,
                                                    ezcontentobject,
                                                    ezcontentobject_tree
                                               WHERE
                                                    ezinfocollection.contentobject_id=ezcontentobject.id
                                                    AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id' );
$numberOfInfoCollectorObjects = 0;

if ( $infoCollectorObjectsQuery )
{
    $numberOfInfoCollectorObjects = $infoCollectorObjectsQuery[0]['count'];
}

foreach ( array_keys( $objects ) as $i )
{
    $firstCollections = eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], /* object id */
                                                                       false, /* creator id */
                                                                       false, /* user identifier */
                                                                       ['limit' => 1, 'offset' => 0], /* limitArray */
                                                                       ['created', true], /* sortArray */
                                                                       false  /* asObject */
                                                                     );
    $objects[$i]['first_collection'] = $firstCollections[0]['created'];

    $lastCollections = eZInformationCollection::fetchCollectionsList( (int)$objects[$i]['contentobject_id'], /* object id */
                                                                      false, /* creator id */
                                                                      false, /* user identifier */
                                                                      ['limit' => 1, 'offset' => 0], /* limitArray */
                                                                      ['created', false], /* sortArray */
                                                                      false  /* asObject */
                                                                    );
    $objects[$i]['last_collection'] = $lastCollections[0]['created'];

    $objects[$i]['class_name'] = eZContentClassNameList::nameFromSerializedString( $objects[$i]['serialized_name_list'] );
    $objects[$i]['collections']= eZInformationCollection::fetchCollectionCountForObject( $objects[$i]['contentobject_id'] );
}

$viewParameters = ['offset' => $offset];

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'object_array', $objects );
$tpl->setVariable( 'object_count', $numberOfInfoCollectorObjects );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:infocollector/overview.tpl' );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/infocollector', 'Collected information' )]];

?>
