<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];

if ( !eZPackage::canUsePolicyFunction( 'import' ) )
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

$package = false;
$installElements = false;
$errorList = [];

if ( $module->isCurrentAction( 'UploadPackage' ) )
{
    if ( eZHTTPFile::canFetch( 'PackageBinaryFile' ) )
    {
        $file = eZHTTPFile::fetch( 'PackageBinaryFile' );
        if ( $file )
        {
            $packageFilename = $file->attribute( 'filename' );

            $package = eZPackage::import( $packageFilename, $packageName );
            if ( $package instanceof eZPackage )
            {
                if ( $package->attribute( 'install_type' ) != 'install' or
                     !$package->attribute( 'can_install' ) )
                {
                    return $module->redirectToView( 'view', ['full', $package->attribute( 'name' )] );
                }
                else if ( $package->attribute( 'install_type' ) == 'install' )
                {
                    return $module->redirectToView( 'install', [$package->attribute( 'name' )] );
                }
            }
            else if ( $package == eZPackage::STATUS_ALREADY_EXISTS )
            {
                $errorList[] = ['description' => ezpI18n::tr( 'kernel/package', 'Package %packagename already exists, cannot import the package', false, ['%packagename' => $packageName] )];
            }
            else if ( $package == eZPackage::STATUS_INVALID_NAME )
            {
                $errorList[] = ['description' => ezpI18n::tr( 'kernel/package', 'The package name %packagename is invalid, cannot import the package', false, ['%packagename' => $packageName] )];
            }
            else
            {
                eZDebug::writeError( "Uploaded file is not an eZ Publish package" );
            }
        }
        else
        {
            eZDebug::writeError( "Failed fetching upload package file" );
        }
    }
    else
    {
        eZDebug::writeError( "No uploaded package file was found" );
    }
}
else if ( $module->isCurrentAction( 'UploadCancel' ) )
{
    $module->redirectToView( 'list' );
    return;
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'package', $package );
$tpl->setVariable( 'error_list', $errorList );

$Result = [];
$Result['content'] = $tpl->fetch( "design:package/upload.tpl" );
$Result['path'] = [['url' => 'package/list', 'text' => ezpI18n::tr( 'kernel/package', 'Packages' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/package', 'Upload' )]];

?>
