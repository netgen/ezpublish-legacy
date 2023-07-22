<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];

$FunctionList['object'] = ['name' => 'object', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchSectionObject'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'section_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'identifier', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['list'] = ['name' => 'list', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchSectionList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['object_list'] = ['name' => 'object_list', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchObjectList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'section_id', 'type' => 'integer', 'required' => true], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'sort_order', 'type' => 'variant', 'required' => false, 'default' => false], ['name' => 'status', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['object_list_count'] = ['name' => 'object_list_count', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchObjectListCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'section_id', 'type' => 'integer', 'required' => true], ['name' => 'status', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['roles'] = ['name' => 'roles', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchRoles'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'section_id', 'type' => 'integer', 'required' => true]]];

$FunctionList['user_roles'] = ['name' => 'user_roles', 'call_method' => ['class' => 'eZSectionFunctionCollection', 'method' => 'fetchUserRoles'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'section_id', 'type' => 'integer', 'required' => true]]];

?>
