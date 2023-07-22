<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$GroupID = null;
if ( isset( $Params["GroupID"] ) )
    $GroupID = $Params["GroupID"];
$http = eZHTTPTool::instance();
$deleteIDArray = $http->hasSessionVariable( 'DeleteClassIDArray' ) ? $http->sessionVariable( 'DeleteClassIDArray' ) : [];
$DeleteResult = [];
$alreadyRemoved = [];

if ( !$http->hasPostVariable( 'ConfirmButton' ) && !$http->hasPostVariable( 'CancelButton' ) && $GroupID != null )
{
    // we will remove class - group relations rather than classes if they belongs to more than 1 group:
    $updateDeleteIDArray = true;
    foreach ( $deleteIDArray as $key => $classID )
    {
        // for each classes tagged for deleting:
        $class = eZContentClass::fetch( $classID );
        if ( $class )
        {
            // find out to how many groups the class belongs:
            $classInGroups = $class->attribute( 'ingroup_list' );
            if ( (is_countable($classInGroups) ? count( $classInGroups ) : 0) != 1 )
            {
                // remove class - group relation:
                eZClassFunctions::removeGroup( $classID, null, [$GroupID] );
                $alreadyRemoved[] = ['id' => $classID, 'name' => $class->attribute( 'name' )];
                $updateDeleteIDArray = true;
                unset( $deleteIDArray[$key] );
            }
        }
    }
    if ( $updateDeleteIDArray )
    {
        // we aren't going to remove classes already processed:
        $http->setSessionVariable( 'DeleteClassIDArray', $deleteIDArray );
    }
    if ( (is_countable($deleteIDArray) ? count( $deleteIDArray ) : 0) == 0 )
    {
        // we don't need anything to confirm:
        return $Module->redirectTo( '/class/classlist/' . $GroupID );
    }
}

if ( $http->hasPostVariable( "ConfirmButton" ) )
{
    foreach ( $deleteIDArray as $deleteID )
    {
        eZContentClassOperations::remove( $deleteID );
        ezpEvent::getInstance()->notify( 'content/class/cache', [$deleteID] );
    }
    return $Module->redirectTo( '/class/classlist/' . $GroupID );
}
if ( $http->hasPostVariable( "CancelButton" ) )
{
    return $Module->redirectTo( '/class/classlist/' . $GroupID );
}

$canRemoveCount = 0;
foreach ( $deleteIDArray as $deleteID )
{
    $ClassObjectsCount = 0;
    $class = eZContentClass::fetch( $deleteID );
    if ( $class != null )
    {
        $class = eZContentClass::fetch( $deleteID );
        $ClassID = $class->attribute( 'id' );
        $ClassName = $class->attribute( 'name' );
        if ( !$class->isRemovable() )
        {
            $item = ["className" => $ClassName, 'objectCount' => 0, "is_removable" => false, 'reason' => $class->removableInformation()];
            $DeleteResult[] = $item;
            continue;
        }
        ++$canRemoveCount;
        $classObjects = eZContentObject::fetchSameClassList( $ClassID );
        $ClassObjectsCount = count( (array) $classObjects );
        $item = ["className" => $ClassName, "is_removable" => true, "objectCount" => $ClassObjectsCount];
        $DeleteResult[] = $item;
    }
}

$canRemove = ( $canRemoveCount > 0 );

$Module->setTitle( ezpI18n::tr( 'kernel/class', 'Remove classes %class_id', null, ['%class_id' => $ClassID] ) );
$tpl = eZTemplate::factory();

$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'GroupID', $GroupID );
$tpl->setVariable( 'DeleteResult', $DeleteResult );
$tpl->setVariable( 'already_removed', $alreadyRemoved );
$tpl->setVariable( 'can_remove', $canRemove );

$Result = [];
$Result['content'] = $tpl->fetch( "design:class/removeclass.tpl" );
$Result['path'] = [['url' => '/class/grouplist/', 'text' => ezpI18n::tr( 'kernel/class', 'Class groups' )]];
$Result['path'][] = ['url' => false, 'text' => ezpI18n::tr( 'kernel/class', 'Remove classes' )];
?>
