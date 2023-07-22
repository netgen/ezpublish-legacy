<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];
$viewMode = $Params['ViewMode'];
$packageName = $Params['PackageName'];
$repositoryID = false;
if ( isset( $Params['RepositoryID'] ) and $Params['RepositoryID'] )
    $repositoryID = $Params['RepositoryID'];

$package = eZPackage::fetch( $packageName, false, $repositoryID );
if ( !is_object( $package ) )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( !$package->attribute( 'can_read' ) )
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );


if ( $module->isCurrentAction( 'Export' ) )
{
    return $module->run( 'export', [$packageName] );
}
else if ( $module->isCurrentAction( 'Install' ) )
{
    return $module->redirectToView( 'install', [$packageName] );
}
else if ( $module->isCurrentAction( 'Uninstall' ) )
{
    return $module->redirectToView( 'uninstall', [$packageName] );
}

$repositoryInformation = $package->currentRepositoryInformation();

$tpl = eZTemplate::factory();

$tpl->setVariable( 'package_name', $packageName );
$tpl->setVariable( 'repository_id', $repositoryID );

$Result = [];
$Result['content'] = $tpl->fetch( "design:package/view/$viewMode.tpl" );
$path = [['url' => 'package/list', 'text' => ezpI18n::tr( 'kernel/package', 'Packages' )]];
if ( $repositoryInformation and $repositoryInformation['id'] != 'local' )
{
    $path[] = ['url' => 'package/list/' . $repositoryInformation['id'], 'text' => $repositoryInformation['name']];
}
$path[] = ['url' => false, 'text' => $package->attribute( 'name' )];
$Result['path'] = $path;

?>
