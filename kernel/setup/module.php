<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZSetup", "variable_params" => true, 'ui_component_match' => 'view', "function" => ["script" => "setup.php", "params" => []]];

$ViewList = [];
$ViewList["init"] = ['functions' => ['install'], "script" => "ezsetup.php", 'single_post_actions' => ['ChangeStepAction' => 'ChangeStep'], 'post_value_action_parameters' => ['ChangeStep' => ['Step' => 'StepButton']], "params" => []];

$ViewList["cache"] = ["script" => "cache.php", 'functions' => ['managecache'], 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['ClearCacheButton' => 'ClearCache', 'ClearAllCacheButton' => 'ClearAllCache', 'ClearContentCacheButton' => 'ClearContentCache', 'ClearINICacheButton' => 'ClearINICache', 'ClearTemplateCacheButton' => 'ClearTemplateCache', 'RegenerateStaticCacheButton' => 'RegenerateStaticCache'], 'post_action_parameters' => ['ClearCache' => ['CacheList' => 'CacheList']], "params" => []];

$ViewList['cachetoolbar'] = ['script' => 'cachetoolbar.php', 'functions' => ['managecache'], 'single_post_actions' => ['ClearCacheButton' => 'ClearCache'], 'post_action_parameters' => ['ClearCache' => ['CacheType' => 'CacheTypeValue', 'NodeID' => 'NodeID', 'ObjectID' => 'ObjectID']], 'params' => []];

$ViewList['settingstoolbar'] = ['functions' => ['setup'], 'script' => 'settingstoolbar.php', 'single_post_actions' => ['SetButton' => 'Set'], 'post_action_parameters' => ['Set' => ['SiteAccess' => 'SiteAccess', 'AllSettingsList' => 'AllSettingsList', 'SelectedList' => 'SelectedList']], 'params' => []];

$ViewList['session'] = ['functions' => ['administrate'], 'script'                  => 'session.php', 'ui_context'              => 'administration', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions'     => ['RemoveAllSessionsButton' => 'RemoveAllSessions', 'ShowAllUsersButton' => 'ShowAllUsers', 'ChangeFilterButton' => 'ChangeFilter', 'RemoveTimedOutSessionsButton' => 'RemoveTimedOutSessions', 'RemoveSelectedSessionsButton' => 'RemoveSelectedSessions'], 'post_action_parameters' => ['ChangeFilter' => ['FilterType' => 'FilterType', 'ExpirationFilterType' => 'ExpirationFilterType', 'InactiveUsersCheck' => 'InactiveUsersCheck', 'InactiveUsersCheckExists' => 'InactiveUsersCheckExists']], 'params' => ['UserID']];

$ViewList["info"] = ['functions' => ['system_info'], "script" => "info.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ['Mode']];

$ViewList["rad"] = ['functions' => ['setup'], "script" => "rad.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', "params" => []];

$ViewList["datatype"] = ['functions' => ['setup'], "script" => "datatype.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['CreateOverrideButton' => 'CreateOverride'], "params" => []];

$ViewList["templateoperator"] = ['functions' => ['setup'], "script" => "templateoperator.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['CreateOverrideButton' => 'CreateOverride'], "params" => []];

$ViewList["extensions"] = ['functions' => ['setup'], "script" => "extensions.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['ActivateExtensionsButton' => 'ActivateExtensions', 'GenerateAutoloadArraysButton' => 'GenerateAutoloadArrays'], "params" => []];

$ViewList['menu'] = ['functions' => ['setup'], 'script' => 'setupmenu.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'params' => []];

$ViewList['systemupgrade'] = ['functions' => ['setup'], 'script' => 'systemupgrade.php', 'ui_context' => 'administration', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['MD5CheckButton' => 'MD5Check', 'DBCheckButton' => 'DBCheck'], 'params' => []];


/*! Provided for backwards compatibility */
$ViewList["toolbarlist"] = ['functions' => ['setup'], "script" => "toolbarlist.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ['SiteAccess']];

$ViewList["toolbar"] = ['functions' => ['setup'], "script" => "toolbar.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', 'post_actions' => ['BrowseActionName'], "params" => ['SiteAccess', 'Position']];

$ViewList["menuconfig"] = ['functions' => ['setup'], "script" => "menuconfig.php", 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess'], "params" => []];

$ViewList["templatelist"] = ['functions' => ['setup'], 'script' => 'templatelist.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList["templateview"] = ['functions' => ['setup'], "script" => "templateview.php", "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess', 'RemoveOverrideButton' => 'RemoveOverride', 'UpdateOverrideButton' => 'UpdateOverride', 'NewOverrideButton' => 'NewOverride'], "params" => []];

$ViewList["templateedit"] = ['functions' => ['setup'], "script" => "templateedit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['SaveButton' => 'Save', 'DiscardButton' => 'Discard'], "params" => []];

$ViewList["templatecreate"] = ['functions' => ['setup'], "script" => "templatecreate.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['CreateOverrideButton' => 'CreateOverride', 'CancelOverrideButton' => 'CancelOverride'], "params" => []];


$FunctionList = [];
$FunctionList['administrate'] = [];
$FunctionList['install'] = [];
$FunctionList['managecache'] = [];
$FunctionList['setup'] = [];
$FunctionList['system_info'] = [];

?>
