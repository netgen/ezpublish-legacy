<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['list'] = ['name' => 'list', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'fetchList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'default' => false, 'required' => false], ['name' => 'limit', 'type' => 'integer', 'default' => false, 'required' => false], ['name' => 'repository_id', 'type' => 'string', 'default' => false, 'required' => false]]];
$FunctionList['maintainer_role_list'] = ['name' => 'maintainer_role_list', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'fetchMaintainerRoleList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'check_roles', 'type' => 'boolean', 'required' => false, 'default' => false]]];
$FunctionList['can_create'] = ['name' => 'can_create', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canCreate'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_edit'] = ['name' => 'can_edit', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canEdit'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_import'] = ['name' => 'can_import', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canImport'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_install'] = ['name' => 'can_install', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canInstall'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_export'] = ['name' => 'can_export', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canExport'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_read'] = ['name' => 'can_read', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canRead'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_list'] = ['name' => 'can_list', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['can_remove'] = ['name' => 'can_remove', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'canRemove'], 'parameter_type' => 'standard', 'parameters' => []];


$FunctionList['item'] = ['name' => 'item', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'fetchPackage'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'package_name', 'type' => 'string', 'required' => true], ['name' => 'repository_id', 'type' => 'string', 'default' => false, 'required' => false]]];

$FunctionList['dependent_list'] = ['name' => 'dependent_list', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'fetchDependentPackageList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'package_name', 'type' => 'string', 'required' => true], ['name' => 'parameters', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'repository_id', 'type' => 'string', 'default' => false, 'required' => false]]];
$FunctionList['repository_list'] = ['name' => 'repository_list', 'call_method' => ['class' => 'eZPackageFunctionCollection', 'method' => 'fetchRepositoryList'], 'parameter_type' => 'standard', 'parameters' => []];
?>
