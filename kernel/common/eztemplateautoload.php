<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// Operator autoloading

$eZTemplateOperatorArray = [];

$eZTemplateOperatorArray[] = ['class' => 'eZURLOperator', 'operator_names' => ['ezurl', 'ezroot', 'ezdesign', 'ezimage', 'exturl', 'ezsys', 'ezhttp', 'ezhttp_hasvariable', 'ezini', 'ezini_hasvariable']];

$eZTemplateOperatorArray[] = ['class' => 'eZi18nOperator', 'operator_names' => ['i18n', 'x18n', 'd18n']];

$eZTemplateOperatorArray[] = ['class' => 'eZAlphabetOperator', 'operator_names' => ['alphabet']];

$eZTemplateOperatorArray[] = ['class' => 'eZDateOperatorCollection', 'operator_names' => ['month_overview']];

$eZTemplateOperatorArray[] = ['class' => 'eZAutoLinkOperator', 'operator_names' => ['autolink']];

$eZTemplateOperatorArray[] = ['class' => 'eZSimpleTagsOperator', 'operator_names' => ['simpletags']];

$eZTemplateOperatorArray[] = ['class' => 'eZTreeMenuOperator', 'operator_names' => ['treemenu']];

$eZTemplateOperatorArray[] = ['class' => 'eZContentStructureTreeOperator', 'operator_names' => ['content_structure_tree']];

$eZTemplateOperatorArray[] = ['class' => 'eZWordToImageOperator', 'operator_names' => ['wordtoimage', 'mimetype_icon', 'class_icon', 'classgroup_icon', 'action_icon', 'icon', 'flag_icon', 'icon_info']];

$eZTemplateOperatorArray[] = ['class' => 'eZKernelOperator', 'operator_names' => ['ezpreference']];


$eZTemplateOperatorArray[] = ['function' => 'eZPHPOperatorInit', 'operator_names_function' => 'eZPHPOperatorNameInit'];

$eZTemplateOperatorArray[] = ['class' => 'eZModuleParamsOperator', 'operator_names' => ['module_params']];

$eZTemplateOperatorArray[] = ['class' => 'eZTopMenuOperator', 'operator_names' => ['topmenu']];

$eZTemplateOperatorArray[] = ['class' => 'eZPackageOperator', 'operator_names' => ['ezpackage']];

$eZTemplateOperatorArray[] = ['class' => 'eZTOCOperator', 'operator_names' => ['eztoc']];

$eZTemplateOperatorArray[] = ['class' => 'eZModuleOperator', 'operator_names' => ['ezmodule']];

// Function autoloading

$eZTemplateFunctionArray = [];
$eZTemplateFunctionArray[] = ['function' => 'eZObjectForwardInit', 'function_names' => ['attribute_edit_gui', 'attribute_view_gui', 'attribute_result_gui', 'attribute_pdf_gui', 'attribute_diff_gui', 'related_view_gui', 'node_view_gui', 'content_view_gui', 'content_pdf_gui', 'shop_account_view_gui', 'content_version_view_gui', 'collaboration_view_gui', 'collaboration_icon', 'collaboration_simple_message_view', 'collaboration_participation_view', 'event_edit_gui', 'event_view_gui', 'class_attribute_view_gui', 'class_attribute_edit_gui']];

if ( !function_exists( 'eZPHPOperatorInit' ) )
{
    function eZPHPOperatorInit()
        {
            $ini = eZINI::instance( 'template.ini' );
            $operatorList = $ini->variable( 'PHP', 'PHPOperatorList' );
            return new eZTemplatePHPOperator( $operatorList );
        }
}

if ( !function_exists( 'eZPHPOperatorNameInit' ) )
{
    function eZPHPOperatorNameInit()
        {
            $ini = eZINI::instance( 'template.ini' );
            $operatorList = $ini->variable( 'PHP', 'PHPOperatorList' );
            return array_keys( $operatorList );
        }
}

if ( !function_exists( 'eZObjectForwardInit' ) )
{
    function eZObjectForwardInit()
        {
            $forward_rules = ['attribute_edit_gui' => ['template_root' => 'content/datatype/edit', 'input_name' => 'attribute', 'output_name' => 'attribute', 'namespace' => 'ContentAttribute', 'attribute_keys' => ['attribute_identifier' => ['contentclass_attribute_identifier'], 'attribute' => ['contentclassattribute_id'], 'class_identifier' => ['object', 'class_identifier'], 'class' => ['object', 'contentclass_id']], 'attribute_access' => [['edit_template']], 'use_views' => false], 'attribute_pdf_gui' => ['template_root' => 'content/datatype/pdf', 'input_name' => 'attribute', 'output_name' => 'attribute', 'namespace' => 'ContentAttribute', 'attribute_keys' => ['attribute_identifier' => ['contentclass_attribute_identifier'], 'attribute' => ['contentclassattribute_id'], 'class_identifier' => ['object', 'class_identifier'], 'class' => ['object', 'contentclass_id']], 'attribute_access' => [['view_template']], 'use_views' => false], 'attribute_view_gui' => ['template_root' => ['type' => 'multi_match', 'attributes' => ['is_information_collector'], 'matches' => [[false, 'content/datatype/view'], [true, 'content/datatype/collect']]], 'render_mode' => false, 'input_name' => 'attribute', 'output_name' => 'attribute', 'namespace' => 'ContentAttribute', 'attribute_keys' => ['attribute_identifier' => ['contentclass_attribute_identifier'], 'attribute' => ['contentclassattribute_id'], 'class_identifier' => ['object', 'class_identifier'], 'class' => ['object', 'contentclass_id']], 'attribute_access' => [['view_template']], 'optional_views' => true, 'use_views' => 'view'], 'attribute_diff_gui' => ['template_root' => 'content/datatype/diff', 'input_name' => 'attribute', 'output_name' => 'attribute', 'namespace' => 'ContentAttribute', 'attribute_keys' => ['attribute_identifier' => ['contentclass_attribute_identifier'], 'attribute' => ['contentclassattribute_id'], 'class_identifier' => ['object', 'class_identifier'], 'class' => ['object', 'contentclass_id']], 'attribute_access' => [['view_template']], 'use_views' => false], 'attribute_result_gui' => ['template_root' => 'content/datatype/result', 'render_mode' => false, 'input_name' => 'attribute', 'output_name' => 'attribute', 'namespace' => 'CollectionAttribute', 'attribute_keys' => ['attribute_identifier' => ['contentclass_attribute_identifier'], 'attribute' => ['contentclassattribute_id'], 'class_identifier' => ['object', 'class_identifier'], 'class' => ['object', 'contentclass_id']], 'attribute_access' => [['result_template']], 'optional_views' => true, 'use_views' => 'view'], 'related_view_gui' => ['template_root' => 'content/related', 'input_name' => 'related_object', 'output_name' => 'related_object', 'namespace' => 'RelatedView', 'attribute_keys' => ['object' => ['id'], 'class' => ['class_id'], 'section' => ['section_id'], 'remote_id' => ['remote_id'], 'class_identifier' => ['class_identifier']], 'attribute_access' => [], 'use_views' => 'view'], 'shop_account_view_gui' => ['template_root' => "shop/accounthandlers", 'input_name' => 'order', 'output_name' => 'order', 'namespace' => 'ShopAccount', 'attribute_access' => [['account_view_template']], 'use_views' => 'view'], 'content_view_gui' => ['template_root' => 'content/view', 'input_name' => 'content_object', 'output_name' => 'object', 'namespace' => 'ContentView', 'attribute_keys' => ['object' => ['id'], 'class_group' => ['match_ingroup_id_list'], 'class' => ['contentclass_id'], 'section' => ['section_id'], 'class_identifier' => ['class_identifier'], 'remote_id' => ['remote_id'], 'state' => ['state_id_array'], 'state_identifier' => ['state_identifier_array']], 'attribute_access' => [], 'use_views' => 'view'], 'content_pdf_gui' => ['template_root' => 'content/pdf', 'input_name' => 'content_object', 'output_name' => 'object', 'namespace' => 'ContentView', 'attribute_keys' => ['object' => ['id'], 'class' => ['contentclass_id'], 'section' => ['section_id'], 'remote_id' => ['remote_id'], 'class_identifier' => ['class_identifier']], 'attribute_access' => [], 'use_views' => 'view'], 'content_version_view_gui' => ['template_root' => 'content/version/view', 'input_name' => 'content_version', 'output_name' => 'version', 'namespace' => 'VersionView', 'attribute_keys' => ['object' => ['contentobject_id'], 'class' => ['contentobject', 'contentclass_id'], 'section' => ['contentobject', 'section_id'], 'class_identifier' => ['contentobject', 'class_identifier']], 'attribute_access' => [], 'use_views' => 'view'], 'node_view_gui' => ['template_root' => 'node/view', 'input_name' => 'content_node', 'output_name' => 'node', 'namespace' => 'NodeView', 'constant_template_variables' => ['view_parameters' => ['offset' => 0]], 'attribute_keys' => ['node' => ['node_id'], 'object' => ['contentobject_id'], 'class' => ['object', 'contentclass_id'], 'section' => ['object', 'section_id'], 'section_identifier' => ['object', 'section_identifier'], 'class_identifier' => ['object', 'class_identifier'], 'class_group' => ['object', 'match_ingroup_id_list'], 'state' => ['object', 'state_id_array'], 'state_identifier' => ['object', 'state_identifier_array'], 'parent_node' => ['parent_node_id'], 'depth' => ['depth'], 'url_alias' => ['url_alias'], 'remote_id' => ['object', 'remote_id'], 'node_remote_id' => ['remote_id'], 'parent_class_identifier' => ['parent', 'class_identifier'], 'parent_node_remote_id' => ['parent', 'remote_id'], 'parent_object_remote_id' => ['parent', 'object', 'remote_id']], 'attribute_access' => [], 'use_views' => 'view'], 'collaboration_view_gui' => ['template_root' => 'collaboration/handlers/view', 'input_name' => 'collaboration_item', 'output_name' => 'item', 'namespace' => 'Collaboration', 'attribute_keys' => [], 'attribute_access' => [['type_identifier']], 'use_views' => 'view'], 'collaboration_icon' => ['template_root' => 'collaboration/handlers/icon', 'input_name' => 'collaboration_item', 'output_name' => 'item', 'namespace' => 'Collaboration', 'attribute_keys' => [], 'attribute_access' => [['type_identifier']], 'use_views' => 'view'], 'collaboration_simple_message_view' => ['template_root' => 'collaboration/message/view', 'input_name' => 'collaboration_message', 'output_name' => 'item', 'namespace' => 'CollaborationMessage', 'attribute_keys' => [], 'attribute_access' => [['message_type']], 'use_views' => 'view'], 'collaboration_participation_view' => ['template_root' => ['type' => 'multi_match', 'attributes' => ['is_builtin_type'], 'matches' => [[true, 'collaboration/participation/view'], [false, ['collaboration/participation/view/custom', [['participant_type_string']]]]]], 'input_name' => 'collaboration_participant', 'output_name' => 'item', 'namespace' => 'CollaborationParticipant', 'attribute_keys' => [], 'attribute_access' => [['participant_type_string']], 'use_views' => 'view'], 'event_edit_gui' => ['template_root' => 'workflow/eventtype/edit', 'input_name' => 'event', 'output_name' => 'event', 'namespace' => 'WorkflowEvent', 'attribute_access' => [['workflow_type_string']], 'use_views' => false], 'event_view_gui' => ['template_root' => 'workflow/eventtype/view', 'input_name' => 'event', 'output_name' => 'event', 'namespace' => 'WorkflowEvent', 'attribute_access' => [['workflow_type_string']], 'use_views' => false], 'class_attribute_view_gui' => ['template_root' => 'class/datatype/view', 'input_name' => 'class_attribute', 'output_name' => 'class_attribute', 'namespace' => 'ClassAttribute', 'attribute_access' => [['data_type', 'information', 'string']], 'use_views' => false], 'class_attribute_edit_gui' => ['template_root' => 'class/datatype/edit', 'input_name' => 'class_attribute', 'output_name' => 'class_attribute', 'namespace' => 'ClassAttribute', 'attribute_access' => [['data_type', 'information', 'string']], 'use_views' => false]];
            return new eZObjectForwarder( $forward_rules );
        }
}

?>
