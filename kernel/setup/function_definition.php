<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];

$FunctionList['version'] = ['name' => 'version', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchFullVersionString'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['alias'] = ['name' => 'alias', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchVersionAlias'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['major_version'] = ['name' => 'major_version', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchMajorVersion'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['minor_version'] = ['name' => 'minor_version', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchMinorVersion'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['release'] = ['name' => 'release', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchRelease'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['state'] = ['name' => 'state', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchState'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['is_development'] = ['name' => 'is_development', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchIsDevelopment'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['database_version'] = ['name' => 'database_version', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchDatabaseVersion'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'with_release', 'type' => 'bool', 'required' => false, 'default' => true]]];
$FunctionList['database_release'] = ['name' => 'database_release', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchDatabaseRelease'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['edition'] = ['name' => 'edition', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSetupFunctionCollection', 'method' => 'fetchEdition'], 'parameter_type' => 'standard', 'parameters' => []];
?>
