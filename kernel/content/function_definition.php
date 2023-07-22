<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];

$FunctionList['object'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchContentObject'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'default' => false, 'required' => false], ['name' => 'remote_id', 'type' => 'string', 'default' => false, 'required' => false]]];
$FunctionList['version'] = ['name' => 'version', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchContentVersion'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'version_id', 'type' => 'integer', 'default' => false, 'required' => true]]];
$FunctionList['node'] = ['name' => 'node', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchContentNode'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'node_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'node_path', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'language_code', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'remote_id', 'type' => 'string', 'default' => false, 'required' => false]]];
$FunctionList['locale_list'] = ['name' => 'locale_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchLocaleList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'with_variations', 'type' => 'boolean', 'required' => false, 'default' => true]]];
$FunctionList['locale'] = ['name' => 'locale', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchLocale'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'locale_code', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['prioritized_languages'] = ['name' => 'prioritized_languages', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchPrioritizedLanguages'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['prioritized_language_codes'] = ['name' => 'prioritized_language_codes', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchPrioritizedLanguageCodes'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['translation_list'] = ['name' => 'translation_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchTranslationList'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['non_translation_list'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchNonTranslationList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'version', 'type' => 'integer', 'required' => true]]];
$FunctionList['class'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchClass'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer,string', 'required' => true]]];
$FunctionList['class_attribute_list'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchClassAttributeList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer', 'required' => true], ['name' => 'version_id', 'type' => 'integer', 'required' => false, 'default' => 0]]];
$FunctionList['class_attribute'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchClassAttribute'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'attribute_id', 'type' => 'integer', 'required' => true], ['name' => 'version_id', 'type' => 'integer', 'required' => false, 'default' => 0]]];
$FunctionList['calendar'] = ['name' => 'calendar', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'calendar'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'integer', 'required' => false, 'default' => 1], ['name' => 'depth_operator', 'type' => 'string', 'required' => false, 'default' => 'le'], ['name' => 'class_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'extended_attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'class_filter_type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'group_by', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'main_node_only', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null]]];

$FunctionList['list'] = ['name' => 'list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectTree'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []], ['name' => 'only_translated', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'language', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'integer', 'required' => false, 'default' => 1], ['name' => 'depth_operator', 'type' => 'string', 'required' => false, 'default' => 'le'], ['name' => 'class_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'extended_attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'class_filter_type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'group_by', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'main_node_only', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null], ['name' => 'as_object', 'type' => 'bool', 'required' => false, 'default' => null], ['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null], ['name' => 'load_data_map', 'type' => 'bool', 'required' => false, 'default' => null]]];
$FunctionList['list_count'] = ['name' => 'list_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectTreeCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'only_translated', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'language', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'string', 'required' => false, 'default' => 1], ['name' => 'depth_operator', 'type' => 'string', 'required' => false, 'default' => 'le'], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null], ['name' => 'main_node_only', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'extended_attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null]]];
$FunctionList['tree'] = ['name' => 'tree', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectTree'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []], ['name' => 'only_translated', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'language', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'depth_operator', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'class_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'extended_attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'class_filter_type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'group_by', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'main_node_only', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null], ['name' => 'as_object', 'type' => 'bool', 'required' => false, 'default' => null], ['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null], ['name' => 'load_data_map', 'type' => 'bool', 'required' => false, 'default' => null]]];

$FunctionList['tree_count'] = ['name' => 'tree_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectTreeCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'only_translated', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'language', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_type', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'class_filter_array', 'type' => 'array', 'required' => false, 'default' => false], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'depth_operator', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null], ['name' => 'main_node_only', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'extended_attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null]]];

$FunctionList['search'] = ['name' => 'search', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchContentSearch'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'text', 'type' => 'string', 'required' => true], ['name' => 'subtree_array', 'type' => 'array', 'default' => false, 'required' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'publish_timestamp', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'publish_date', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'section_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'class_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'class_attribute_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'bool', 'required' => false, 'default' => null], ['name' => 'limitation', 'type' => 'array', 'required' => false, 'default' => null], ['name' => 'sort_by', 'type' => 'mixed', 'required' => false, 'default' => false]]];

$FunctionList['trash_count'] = ['name' => 'trash_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchTrashObjectCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false]]];

$FunctionList['trash_object_list'] = ['name' => 'trash_object_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchTrashObjectList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'objectname_filter', 'type' => 'string', 'required' => false, 'default' => null], ['name' => 'attribute_filter', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'as_object', 'type' => 'bool', 'required' => false, 'default' => true]]];

$FunctionList['draft_count'] = ['name' => 'draft_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchDraftVersionCount'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['draft_version_list'] = ['name' => 'draft_version_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchDraftVersionList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['pending_count'] = ['name' => 'pending_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchPendingCount'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['pending_list'] = ['name' => 'pending_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchPendingList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['version_count'] = ['name' => 'version_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchVersionCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'contentobject', 'type' => 'object', 'required' => true]]];

$FunctionList['version_list'] = ['name' => 'version_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchVersionList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'contentobject', 'type' => 'object', 'required' => true], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'sorts', 'type' => 'array', 'required' => false, 'default' => null]]];



$FunctionList['can_instantiate_class_list'] = ['name' => 'can_instantiate_class_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'canInstantiateClassList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'group_id', 'type' => 'array', 'required' => false, 'default' => 0], ['name' => 'parent_node', 'type' => 'object', 'required' => false, 'default' => 0], ['name' => 'filter_type', 'type' => 'string', 'required' => false, 'default' => 'include'], ['name' => 'fetch_id', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'as_object', 'type' => 'bool', 'required' => false, 'default' => true], ['name' => 'group_by_class_group', 'type' => 'bool', 'required' => false, 'default' => false]]];

$FunctionList['can_instantiate_classes'] = ['name' => 'can_instantiate_classes', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'canInstantiateClasses'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'parent_node', 'type' => 'object', 'required' => false, 'default' => 0]]];
$FunctionList['contentobject_attributes'] = ['name' => 'contentobject_attributes', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'contentobjectAttributes'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'version', 'type' => 'object', 'required' => false, 'default' => 0], ['name' => 'language_code', 'type' => 'string', 'required' => false, 'default' => '']]];

$FunctionList['bookmarks'] = ['name' => 'bookmarks', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchBookmarks'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['recent'] = ['name' => 'recent', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchRecent'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['section_list'] = ['name' => 'section_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchSectionList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['tipafriend_top_list'] = ['name' => 'tipafriend_top_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchTipafriendTopList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'start_time', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'end_time', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'duration', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'ascending', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'extended', 'type' => 'boolean', 'required' => false, 'default' => false]]];

$FunctionList['view_top_list'] = ['name' => 'view_top_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchMostViewedTopList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'section_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['collected_info_count'] = ['name' => 'collected_info_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchCollectedInfoCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_attribute_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'object_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'value', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'creator_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'user_identifier', 'type' => 'string', 'required' => false, 'default' => false]]];

$FunctionList['collected_info_count_list'] = ['name' => 'collected_info_count_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchCollectedInfoCountList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_attribute_id', 'type' => 'integer', 'required' => true, 'default' => false]]];


$FunctionList['collected_info_collection'] = ['name' => 'collected_info_collection', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchCollectedInfoCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'collection_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'contentobject_id', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['collected_info_list'] = ['name' => 'collected_info_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchCollectionsList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'creator_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'user_identifier', 'type' => 'string', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []]]];

$FunctionList['object_by_attribute'] = ['name' => 'object_by_attribute', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectByAttribute'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'identifier', 'type' => 'string', 'required' => true, 'default' => false]]];

$FunctionList['object_count_by_user_id'] = ['name' => 'object_count_by_user_id', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchObjectCountByUserID'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'class_id', 'type' => 'integer', 'required' => true, 'default' => false], ['name' => 'user_id', 'type' => 'integer', 'required' => true, 'default' => false], ['name' => 'status', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['same_classattribute_node'] = ['name' => 'same_classattribute_node', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchSameClassAttributeNodeList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'classattribute_id', 'type' => 'integer', 'required' => true], ['name' => 'value', 'type' => 'mixed', 'required' => true], ['name' => 'datatype', 'type' => 'string', 'required' => true]]];

$FunctionList['keyword'] = ['name' => 'keyword', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchKeyword'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'alphabet', 'type' => 'string', 'required' => true], ['name' => 'classid', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'owner', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []], ['name' => 'parent_node_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'include_duplicates', 'type' => 'bool', 'required' => false, 'default' => true], ['name' => 'strict_matching', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'integer', 'required' => false, 'default' => 1]]];


$FunctionList['keyword_count'] = ['name' => 'keyword_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchKeywordCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'alphabet', 'type' => 'string', 'required' => true], ['name' => 'classid', 'type' => 'mixed', 'required' => false, 'default' => false], ['name' => 'owner', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'parent_node_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'include_duplicates', 'type' => 'bool', 'required' => false, 'default' => true], ['name' => 'strict_matching', 'type' => 'bool', 'required' => false, 'default' => false], ['name' => 'depth', 'type' => 'integer', 'required' => false, 'default' => 1]]];

$FunctionList['access'] = ['name' => 'access', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'checkAccess'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'access', 'type' => 'string', 'required' => true], [
    'name' => 'contentobject',
    'type' => 'object',
    // eZContentObject or eZContentObjectTreeNode
    'required' => true,
], ['name' => 'contentclass_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'parent_contentclass_id', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'language', 'type' => 'string', 'required' => false, 'default' => false]]];

// Fetches all navigation parts as an array
$FunctionList['navigation_parts'] = ['name' => 'navigation_parts', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchNavigationParts'], 'parameter_type' => 'standard', 'parameters' => []];

// Fetches one navigation part by identifier
$FunctionList['navigation_part'] = ['name' => 'navigation_part', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchNavigationPart'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'identifier', 'type' => 'string', 'required' => true]]];

// Fetches related objects array
$FunctionList['related_objects'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchRelatedObjects'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false, 'default' => '0'], ['name' => 'all_relations', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'group_by_attribute', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'as_object', 'type' => 'boolean', 'required' => false, 'default' => true], ['name' => 'load_data_map', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'boolean', 'required' => false, 'default' => null], ['name' => 'related_class_identifiers', 'type' => 'array', 'required' => false, 'default' => null]]];

$FunctionList['related_objects_count'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchRelatedObjectsCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false, 'default' => '0'], ['name' => 'all_relations', 'type' => 'boolean', 'required' => false, 'default' => false]]];
// Fetches reverse related objects array
$FunctionList['reverse_related_objects'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchReverseRelatedObjects'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false], ['name' => 'all_relations', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'group_by_attribute', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'sort_by', 'type' => 'array', 'required' => false, 'default' => []], ['name' => 'ignore_visibility', 'type' => 'boolean', 'required' => false, 'default' => null], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'as_object', 'type' => 'boolean', 'required' => false, 'default' => true], ['name' => 'load_data_map', 'type' => 'boolean', 'required' => false, 'default' => false]]];

$FunctionList['reverse_related_objects_count'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchReverseRelatedObjectsCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false], ['name' => 'all_relations', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'ignore_visibility', 'type' => 'boolean', 'required' => false, 'default' => null]]];

$FunctionList['available_sort_fields'] = ['name' => 'available_sort_fields', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchAvailableSortFieldList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['country_list'] = ['name' => 'country', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchCountryList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'filter', 'type' => 'string', 'required' => false], ['name' => 'value', 'type' => 'string', 'required' => false]]];

$FunctionList['related_objects_ids'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchRelatedObjectsID'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false, 'default' => '0'], ['name' => 'all_relations', 'type' => 'array', 'required' => false, 'default' => false]]];

$FunctionList['reverse_related_objects_ids'] = ['name' => 'object', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchReverseRelatedObjectsID'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'attribute_identifier', 'type' => 'string', 'required' => false, 'default' => '0'], ['name' => 'all_relations', 'type' => 'array', 'required' => false, 'default' => false]]];

$FunctionList['content_tree_menu_expiry'] = ['name' => 'content_tree_menu_expiry', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZContentFunctionCollection', 'method' => 'fetchContentTreeMenuExpiry'], 'parameter_type' => 'standard', 'parameters' => []];

?>
