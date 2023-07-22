<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['current_user'] = ['name' => 'current_user', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchCurrentUser'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['is_logged_in'] = ['name' => 'is_logged_in', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchIsLoggedIn'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'user_id', 'type' => 'integer', 'required' => true]]];

$FunctionList['logged_in_count'] = ['name' => 'logged_in_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchLoggedInCount'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['anonymous_count'] = ['name' => 'anonymous_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchAnonymousCount'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['logged_in_list'] = ['name' => 'logged_in_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchLoggedInList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'sort_by', 'type' => 'mixed', 'required' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false]]];

$FunctionList['logged_in_users'] = ['name' => 'logged_in_users', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchLoggedInUsers'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'sort_by', 'type' => 'mixed', 'required' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false]]];
$FunctionList['user_role'] = ['name' => 'user_role', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchUserRole'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'user_id', 'type' => 'integer', 'required' => true]]];


$FunctionList['member_of'] = ['name' => 'member_of', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'fetchMemberOf'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'id', 'type' => 'integer', 'required' => true]]];

$FunctionList['has_access_to'] = ['name' => 'has_access_to', 'operation_types' => [], 'call_method' => ['class' => 'eZUserFunctionCollection', 'method' => 'hasAccessTo'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'module', 'type' => 'string', 'required' => true], ['name' => 'function', 'type' => 'string', 'required' => true], ['name' => 'user_id', 'type' => 'integer', 'required' => false]]];

?>
