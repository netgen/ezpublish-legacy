<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];


$FunctionList['collected_info_count'] = ['name' => 'collected_info_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZInfocollectorFunctionCollection', 'method' => 'fetchCollectedInfoCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_attribute_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'object_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'value', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'creator_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'user_identifier', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['collected_info_count_list'] = ['name' => 'collected_info_count_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZInfocollectorFunctionCollection', 'method' => 'fetchCollectedInfoCountList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_attribute_id', 'type' => 'integer', 'required' => true, 'default' => false]]];


$FunctionList['collected_info_collection'] = ['name' => 'collected_info_collection', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZInfocollectorFunctionCollection', 'method' => 'fetchCollectedInfoCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'collection_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'contentobject_id', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['collected_info_list'] = ['name' => 'collected_info_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZInfocollectorFunctionCollection', 'method' => 'fetchCollectionsList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'creator_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'user_identifier', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []]]];


?>
