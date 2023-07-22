<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];

$FunctionList['workflow_statuses'] = ['name' => 'workflow_statuses', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZWorkflowFunctionCollection', 'method' => 'fetchWorkflowStatuses'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['workflow_type_statuses'] = ['name' => 'workflow_type_statuses', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZWorkflowFunctionCollection', 'method' => 'fetchWorkflowTypeStatuses'], 'parameter_type' => 'standard', 'parameters' => []];

?>
