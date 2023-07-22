#!/usr/bin/env php
<?php
/**
 * File containing the ezflowupgrade.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// eZ Flow upgrade Script
// file  bin/php/ezflowupgrade.php


/*!
 define constans
*/

/*!
 define global vars
*/

/*!
 includes
*/
require_once 'autoload.php';
include_once( 'bin/php/ezwebincommon.php' );


function execUpdateFunction( $funcName, $toVersion )
{
    $funcName = "$funcName" . "_" . preg_replace( "/[.-]/", "_", (string) $toVersion );

    if ( function_exists( $funcName ) )
    {
        $funcName();
    }
}

function upgradePackageListByFlowVersion( $version )
{
    $packageList = false;

    switch ( $version )
    {
        case '1.1-0':
            {
                $packageList = ['ezflow_extension', 'ezflow_classes', 'ezwebin_extension', 'ezflow_design', 'ezflow_site'];
            } break;
        default:
            break;
    }

    return $packageList;

}

function isValidFlowUpgradeVersion( $version )
{
    $isValid = false;

    switch( $version )
    {
        case '1.1-0':
            {
                $isValid = true;
            } break;
        default:
            break;
    }

    return $isValid;
}

function updateINI_1_1_0()
{
    showMessage2( "Updating INI-files..." );

    $siteaccessList = getUserInput( "Please specify the eZ Flow siteaccesses on your site (separated with space, for example eng nor): ");
    $siteaccessList = explode( ' ', (string) $siteaccessList );

    $translationSA = [];
    foreach( $siteaccessList as $siteaccess )
    {
        if( !file_exists( 'settings/siteaccess/' . $siteaccess ) )
            continue;

        /* Update override.ini.append.php part */
        $settings = ['full_silverlight' => ['Source' => 'node/view/full.tpl', 'MatchFile' => 'full/silverlight.tpl', 'Subdir' => 'templates', 'Match' => ['class_identifier' => 'silverlight']], 'line_silverlight' => ['Source' => 'node/view/line.tpl', 'MatchFile' => 'line/silverlight.tpl', 'Subdir' => 'templates', 'Match' => ['class_identifier' => 'silverlight']], 'edit_ezsubtreesubscription_forum_topic' => ['Source' => 'content/datatype/edit/ezsubtreesubscription.tpl', 'MatchFile' => 'datatype/edit/ezsubtreesubscription/forum_topic.tpl', 'Subdir' => 'templates', 'Match' => ['class_identifier' => 'forum_topic']], 'block_item_article' => ['Source' => 'node/view/block_item.tpl', 'MatchFile' => 'block_item/article.tpl', 'Subdir' => 'templates', 'Match' => ['class_identifier' => 'article']], 'block_item_image' => ['Source' => 'node/view/block_item.tpl', 'MatchFile' => 'block_item/image.tpl', 'Subdir' => 'templates', 'Match' => ['class_identifier' => 'image']], 'dynamic_3_items1' => ['Source' => 'block/view/view.tpl', 'MatchFile' => 'block/dynamic_3_items1.tpl', 'Subdir' => 'templates', 'Match' => ['type' => 'Dynamic3Items', 'view' => '3_items1']]];
        $ini = eZINI::instance( 'override.ini', 'settings/siteaccess/' . $siteaccess, null, null, false, true );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariables( $settings );
        $ini->save( false, '.append.php', false, false, 'settings/siteaccess/' . $siteaccess, false );

        /* Update menu.ini.append.php part */
        $settings = ['SelectedMenu' => ['CurrentMenu' => 'DoubleTop', 'TopMenu' => 'double_top', 'LeftMenu' => '']];

        $ini = eZINI::instance( 'menu.ini', 'settings/siteaccess/' . $siteaccess, null, null, false, true );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariables( $settings );
        $ini->save( false, '.append.php', false, false, 'settings/siteaccess/' . $siteaccess, false );

        /* Get site.ini for ContentObjectLocale code */
        $ini = eZINI::instance( 'site.ini', 'settings/siteaccess/' . $siteaccess, null, null, false, true );
        $contentObjectLocale = explode( '-', (string) $ini->variable( 'RegionalSettings', 'ContentObjectLocale' ) );

        $translationSA[$siteaccess] = ucfirst( $contentObjectLocale[0] );
    }

    $settings = [['name' => 'site.ini', 'settings' => ['RegionalSettings' => ['TranslationSA' => $translationSA]]], ['name' => 'content.ini', 'settings' => ['table' => ['CustomAttributes' => ['0' => 'summary', '1' => 'caption']], 'td' => ['CustomAttributes' => ['0' => 'valign']], 'th' => ['CustomAttributes' => ['0' => 'scope', '1' => 'abbr', '2' => 'valign']], 'CustomTagSettings' => ['AvailableCustomTags' => ['0' => 'underline'], 'IsInline' => ['underline' => 'true']], 'embed-type_images' => ['AvailableClasses' => []]]], ['name' => 'ezoe_attributes.ini', 'settings' => ['CustomAttribute_table_summary' => ['Name' => 'Summary (WAI)', 'Required' => 'true'], 'CustomAttribute_scope' => ['Name' => 'Scope', 'Title' => 'The scope attribute defines a way to associate header cells and data cells in a table.', 'Type' => 'select', 'Selection' => ['0' => '', 'col' => 'Column', 'row' => 'Row']], 'CustomAttribute_valign' => ['Title' => 'Lets you define the vertical alignment of the table cell/ header.', 'Type' => 'select', 'Selection' => ['0' => '', 'top' => 'Top', 'middle' => 'Middle', 'bottom' => 'Bottom', 'baseline' => 'Baseline']], 'Attribute_table_border' => ['Type' => 'htmlsize', 'AllowEmpty' => 'true'], 'CustomAttribute_embed_offset' => ['Type' => 'int', 'AllowEmpty' => 'true'], 'CustomAttribute_embed_limit' => ['Type' => 'int', 'AllowEmpty' => 'true']]], ['name' => 'ezxml.ini', 'settings' => ['TagSettings' => ['TagPresets' => ['0' => '', 'mini' => 'Simple formatting']]]]];
    foreach ( $settings as $setting )
    {
        $iniName = $setting['name'];
        $onlyModified = false;
        if ( file_exists( 'settings/override/' . $iniName . '.append' ) ||
             file_exists( 'settings/override/' . $iniName . '.append.php' ) )
        {

            $ini = eZINI::instance( $iniName, 'settings/override', null, null, false, true );
        }
        else
        {
            $ini = eZINI::create( $iniName, 'settings/override' );
            $onlyModified = true;
        }
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariables( $setting['settings'] );
        $ini->save( false, '.append.php', false, $onlyModified, 'settings/override', false );
    }
}

// script initializing
$cli = eZCLI::instance();
$script = eZScript::instance( ['description' => ( "\n" .
                                                        "This script will upgrade eZ Flow." ), 'use-session' => false, 'use-modules' => true, 'use-extensions' => true, 'user' => true] );
$script->startup();

$scriptOptions = $script->getOptions( "[to-version:][repository:][package:][package-dir:][url:][auto-mode:]",
                                      "",
                                      ['to-version' => "Specify what upgrade path to use. \n" .
                                                             " available options: '1.1-0' - upgrade 1.0-0 to 1.1-0", 'repository' => "Path to repository where unpacked(unarchived) packages are \n" .
                                                  "placed. it's relative to 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]' \n".
                                                  "(default is 'var/storage/packages/eZ-systems')", 'package' => "Package(s) to install, f.e. 'ezflow_classes'", 'package-dir' => "Path to directory with packed(ezpkg) packages(default is '/tmp/ezflow') ", 'url' => "URL to download packages, f.e. 'http://packages.ez.no/ezpublish/3.9'.\n" .
                                               "'package-dir' can be specified to store uploaded packages on local computer.\n" .
                                               "if 'package-dir' is not specified then default dir('/tmp/ezflow') will be used.", 'auto-mode' => "[on/off]. Do not ask what to do in case of confilicts. By default is 'on'"],
                                      false,
                                      ['user' => true]
                                     );


if ( !$scriptOptions['siteaccess'] )
{
    showNotice( "No siteaccess provided, will use default siteaccess" );
}
else
{
    $siteAccessExists = checkSiteaccess( $scriptOptions['siteaccess'] );

    if ( $siteAccessExists )
    {
        showNotice( "Using siteaccess " . $scriptOptions['siteaccess'] );
        $script->setUseSiteAccess( $scriptOptions['siteaccess'] );
    }
    else
    {
        showError( "Siteaccess '" . $scriptOptions['siteaccess'] . "' does not exist. Exiting..." );
    }
}
$script->initialize();


/**************************************************************
* process options                                             *
***************************************************************/

$toVersion = '1.1-0';
if ( $scriptOptions['to-version'] )
{
    $version = $scriptOptions['to-version'];
    if ( isValidFlowUpgradeVersion( $version ) )
    {
        $toVersion = $version;
    }
    else
    {
        showError( "invalid '--to-version' option" );
    }
}

//
// 'repository' option
//
$packageRepository = $scriptOptions['repository'];
if ( !$packageRepository )
{
    $packageRepository = repositoryByVendor( defaultVendor() );
}


//
// 'package' option
//
$packageList = $scriptOptions['package'];
if ( !$packageList )
{
    $packageList = upgradePackageListByFlowVersion( $toVersion );
}
else
{
    $packageList = explode( ' ', (string) $packageList );
}

//
// 'package-dir' option
//
$packageDir = $scriptOptions['package-dir'] ?: "/tmp/ezflow";

//
// 'url' option
//
$packageURL = $scriptOptions['url'];
if ( !$packageURL )
{
    $packageINI = eZINI::instance( 'package.ini' );
    $packageURL = $packageINI->variable( 'RepositorySettings', 'RemotePackagesIndexURL' );
}

//
// 'auto-mode' option
//
global $autoMode;
$autoMode = $scriptOptions['auto-mode'];
if( $autoMode != 'off' )
{
    $autoMode = 'on';
    $importDir = eZPackage::repositoryPath() . "/$packageRepository";
    showWarning( "Processing in auto-mode: \n".
                 "- packages will be downloaded to '$packageDir';\n" .
                 "- packages will be imported to '$importDir';\n" .
                 "- installing of existing classes will be skipped;\n" .
                 "- all files(extesion, design, downloaded and imported packages) will be overwritten;" );
    $action = getUserInput( "Continue? [y/n]: ");
    if( !str_starts_with((string) $action, 'y') )
        $script->shutdown( 0, 'Done' );
}

/**************************************************************
* do the work                                                 *
***************************************************************/

if( downloadPackages( $packageList, $packageURL, $packageDir, $packageRepository ) )
{
    // install
    installPackages( $packageList );
}

if( file_exists( installScriptDir( $packageRepository, 'ezflow_site' ) ) )
{
    include_once( installScriptDir( $packageRepository, 'ezflow_site' ) . "/settings/ezflowinstaller.php" );
    include_once( installScriptDir( $packageRepository, 'ezflow_site' ) . "/settings/ini-site.php" );
    include_once( installScriptDir( $packageRepository, 'ezflow_site' ) . "/settings/ini-common.php" );

    showMessage2( "Updating content classes..." );
    execUpdateFunction( "updateClasses", $toVersion );

    showMessage2( "Updating content objects..." );
    execUpdateFunction( "updateObjects", $toVersion );

    showMessage2( "Updating INI-files..." );
    execUpdateFunction( "updateINI", $toVersion );
}
else
{
    showWarning( "no data for updating content classes, objects, roles, ini" );
}

showMessage2( "Upgrade complete" );
$script->shutdown( 0 );

?>
