<?php
/**
 * File containing the userpaex module definition
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ezmbpaex
 */

$Module = ['name' => 'User with Password Expiry management', 'variable_params' => true];

$ViewList = [];

$ViewList['password'] = ['functions' => ['password'], 'script' => 'password.php', 'ui_context' => 'administration', 'default_navigation_part' => 'ezmynavigationpart', 'params' => ['UserID']];

$ViewList['forgotpassword'] = ['functions' => ['password'], 'script' => 'forgotpassword.php', 'params' => [], 'ui_context' => 'administration', 'single_post_actions' => ['GenerateButton' => 'Generate', 'ChangePasswdButton' => 'ChangePassword'], 'post_action_parameters' => ['Generate' => ['Login' => 'UserLogin', 'Email' => 'UserEmail'], 'ChangePassword' => ['NewPassword' => 'NewPassword', 'NewPasswordConfirm' => 'NewPasswordConfirm']], 'params' => ['HashKey']];

$FunctionList = [];
$FunctionList['password'] = [];
$FunctionList['editpaex'] = [];

?>
