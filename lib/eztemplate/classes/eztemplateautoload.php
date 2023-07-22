<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

// Operator autoloading

$eZTemplateOperatorArray = [];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateArrayOperator', 'operator_names' => [
    'array',
    'hash',
    'array_prepend',
    // DEPRECATED/OBSOLETE
    'prepend',
    // New,replaces array_prepend.
    'array_append',
    // DEPRECATED/OBSOLETE
    'append',
    // New,replaces array_append.
    'array_merge',
    // DEPRECATED/OBSOLETE
    'merge',
    // New,replaces array_merge.
    'contains',
    'compare',
    'extract',
    'extract_left',
    'extract_right',
    'begins_with',
    'ends_with',
    'implode',
    'explode',
    'repeat',
    'reverse',
    'insert',
    'remove',
    'replace',
    'unique',
    'array_sum',
]];



$eZTemplateOperatorArray[] = ['class' => 'eZTemplateExecuteOperator', 'operator_names' => ['fetch', 'fetch_alias']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateLocaleOperator', 'operator_names' => ['l10n', 'locale', 'datetime', 'currentdate', 'maketime', 'makedate', 'gettime']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateAttributeOperator', 'operator_names' => ['attribute', 'dump']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateNl2BrOperator', 'operator_names' => ['nl2br']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateTextOperator', 'operator_names' => ['concat', 'indent']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateUnitOperator', 'operator_names' => ['si']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateLogicOperator', 'operator_names' => ['lt', 'gt', 'le', 'ge', 'eq', 'ne', 'null', 'not', 'true', 'false', 'or', 'and', 'choose']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateTypeOperator', 'operator_names' => ['is_array', 'is_boolean', 'is_integer', 'is_float', 'is_numeric', 'is_string', 'is_object', 'is_class', 'is_null', 'is_set', 'is_unset', 'get_type', 'get_class']];
$eZTemplateOperatorArray[] = ['class' => 'eZTemplateControlOperator', 'operator_names' => ['cond', 'first_set']];

$eZTemplateOperatorArray[] = ['class' => 'eZTemplateArithmeticOperator', 'operator_names' => ['sum', 'sub', 'inc', 'dec', 'div', 'mod', 'mul', 'max', 'min', 'abs', 'ceil', 'floor', 'round', 'int', 'float', 'count', 'roman', 'rand']];

$eZTemplateOperatorArray[] = ['class' => 'eZTemplateImageOperator', 'operator_names' => ['texttoimage', 'image', 'imagefile']];


$eZTemplateOperatorArray[] = ['class' => 'eZTemplateStringOperator', 'operator_names' => ['upcase', 'downcase', 'count_words', 'count_chars', 'trim', 'break', 'wrap', 'upfirst', 'upword', 'simplify', 'trim', 'wash', 'chr', 'ord', 'shorten', 'pad']];

$eZTemplateOperatorArray[] = ['class' => 'eZTemplateDigestOperator', 'operator_names' => ['crc32', 'md5', 'rot13']];



// Function autoloading

$eZTemplateFunctionArray = [];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateSectionFunction', 'function_names' => ['section'], 'function_attributes' => ['delimiter', 'section-exclude', 'section-include', 'section-else']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateDelimitFunction', 'function_names' => ['ldelim', 'rdelim']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateIncludeFunction', 'function_names' => ['include']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateSwitchFunction', 'function_names' => ['switch'], 'function_attributes' => ['case']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateSequenceFunction', 'function_names' => ['sequence']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateSetFunction', 'function_names' => ['set', 'let', 'default']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateBlockFunction', 'function_names' => ['set-block', 'append-block', 'run-once']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateDebugFunction', 'function_names' => ['debug-timing-point', 'debug-accumulator', 'debug-log', 'debug-trace']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateCacheFunction', 'function_names' => ['cache-block']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateToolbarFunction', 'function_names' => ['tool_bar']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateMenuFunction', 'function_names' => ['menu']];

// should we add 'break', 'continue' and 'skip' to the {if} attribute list?
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateIfFunction', 'function_names' => ['if'], 'function_attributes' => ['elseif', 'else']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateWhileFunction', 'function_names' => ['while'], 'function_attributes' => ['delimiter', 'break', 'continue', 'skip']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateForFunction', 'function_names' => ['for'], 'function_attributes' => ['delimiter', 'break', 'continue', 'skip']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateForeachFunction', 'function_names' => ['foreach'], 'function_attributes' => ['delimiter', 'break', 'continue', 'skip']];
$eZTemplateFunctionArray[] = ['class' => 'eZTemplateDoFunction', 'function_names' => ['do'], 'function_attributes' => ['delimiter', 'break', 'continue', 'skip']];

$eZTemplateFunctionArray[] = ['class' => 'eZTemplateDefFunction', 'function_names' => ['def', 'undef']];


// eZTemplatePHPOperator is not autoload due to it's generic use
// it's up to the users of eZTemplate to initiate a proper usage
// for this operator class.

?>
