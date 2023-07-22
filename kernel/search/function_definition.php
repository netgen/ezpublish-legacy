<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['list_count'] = ['name' => 'list_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSearchFunctionCollection', 'method' => 'fetchSearchListCount'], 'parameter_type' => 'standard', 'parameters' => []];
$FunctionList['list'] = ['name' => 'list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZSearchFunctionCollection', 'method' => 'fetchSearchList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];
?>
