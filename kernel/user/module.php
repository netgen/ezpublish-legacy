<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'User management', 'variable_params' => true];

$ViewList = [];
$ViewList['logout'] = ['functions' => ['login'], 'script' => 'logout.php', 'ui_context' => 'authentication', 'params' => []];

$ViewList['login'] = ['functions' => ['login'], 'script' => 'login.php', 'ui_context' => 'authentication', 'default_action' => [['name' => 'Login', 'type' => 'post', 'parameters' => ['Login', 'Password']]], 'single_post_actions' => ['LoginButton' => 'Login'], 'post_action_parameters' => ['Login' => ['UserLogin' => 'Login', 'UserPassword' => 'Password', 'UserRedirectURI' => 'RedirectURI']], 'params' => []];

$ViewList['setting'] = ['functions' => ['preferences'], 'default_navigation_part' => 'ezusernavigationpart', 'script' => 'setting.php', 'params' => ['UserID']];

$ViewList['preferences'] = ['functions' => ['login'], 'script' => 'preferences.php', 'params' => ['Function', 'Key', 'Value']];

$ViewList['password'] = ['functions' => ['password'], 'script' => 'password.php', 'ui_context' => 'administration', 'default_navigation_part' => 'ezmynavigationpart', 'params' => ['UserID']];

$ViewList['forgotpassword'] = ['functions' => ['password'], 'script' => 'forgotpassword.php', 'params' => [], 'ui_context' => 'administration', 'single_post_actions' => ['GenerateButton' => 'Generate'], 'post_action_parameters' => ['Generate' => ['Login' => 'UserLogin', 'Email' => 'UserEmail']], 'params' => ['HashKey']];

/// \deprecated Use normal content edit view instead
$ViewList['edit'] = ['functions' => ['login'], 'script' => 'edit.php', 'ui_context' => 'edit', 'single_post_actions' => ['ChangePasswordButton' => 'ChangePassword', 'ChangeSettingButton' => 'ChangeSetting', 'CancelButton' => 'Cancel', 'EditButton' => 'Edit'], 'params' => ['UserID']];

$ViewList['register'] = ['functions' => ['register'], 'script' => 'register.php', 'params' => ['redirect_number'], 'ui_context' => 'edit', 'default_navigation_part' => 'ezmynavigationpart', 'single_post_actions' => ['PublishButton' => 'Publish', 'CancelButton' => 'Cancel', 'CustomActionButton' => 'CustomAction']];

$ViewList['activate'] = ['functions' => ['login'], 'script' => 'activate.php', 'ui_context' => 'authentication', 'default_navigation_part' => 'ezmynavigationpart', 'params' => ['Hash', 'MainNodeID']];

$ViewList['success'] = ['functions' => ['register'], 'script' => 'success.php', 'ui_context' => 'authentication', 'default_navigation_part' => 'ezmynavigationpart', 'params' => []];

$ViewList['unactivated'] = ['functions' => ['activation'], 'script' => 'unactivated.php', 'ui_context' => 'administration', 'default_navigation_part' => 'ezusernavigationpart', 'unordered_params' => ['offset' => 'Offset'], 'single_post_actions' => ['ActivateButton' => 'ActivateUsers', 'RemoveButton' => 'RemoveUsers'], 'post_action_parameters' => ['ActivateUsers' => ['UserIDs' => 'DeleteIDArray'], 'RemoveUsers' => ['UserIDs' => 'DeleteIDArray']], 'params' => ['SortField', 'SortOrder']];

$SiteAccess = ['name'=> 'SiteAccess', 'values'=> [], 'class' => 'eZSiteAccess', 'function' => 'siteAccessList', 'parameter' => []];

$FunctionList = [];
$FunctionList['login'] = ['SiteAccess' => $SiteAccess];
$FunctionList['password'] = [];
$FunctionList['preferences'] = [];
$FunctionList['register'] = [];
$FunctionList['selfedit'] = [];
$FunctionList['activation'] = [];

?>
