<?php
/**
 * File containing the RSS function definitions.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];

$FunctionList['has_export_by_node'] = ['name' => 'has_node_map', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZRSSFunctionCollection', 'method' => 'hasExportByNode'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'node_id', 'type' => 'integer', 'required' => true]]];
$FunctionList['export_by_node'] = ['name' => 'node_map', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZRSSFunctionCollection', 'method' => 'exportByNode'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'node_id', 'type' => 'integer', 'required' => true]]];
?>
