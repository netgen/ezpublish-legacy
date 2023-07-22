<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['list'] = ['name' => 'list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZClassFunctionCollection', 'method' => 'fetchClassList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_filter', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []]]];

$FunctionList['list_by_groups'] = ['name' => 'list_by_groups', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZClassFunctionCollection', 'method' => 'fetchClassListByGroups'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'group_filter', 'type' => 'array', 'required' => true, 'default' => false], ['name' => 'group_filter_type', 'type' => 'string', 'required' => false, 'default' => 'include']]];

$FunctionList['latest_list'] = ['operation_types' => ['read'], 'call_method' => ['class' => 'eZClassFunctionCollection', 'method' => 'fetchLatestClassList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['attribute_list'] = ['name' => 'attribute_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZClassFunctionCollection', 'method' => 'fetchClassAttributeList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer', 'required' => true]]];



$FunctionList['override_template_list'] = ['name' => 'override_template_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZClassFunctionCollection', 'method' => 'fetchOverrideTemplateList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer', 'required' => true]]];

?>
