<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['list'] = ['name' => 'list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZURLFunctionCollection', 'method' => 'fetchList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'is_valid', 'required' => false, 'default' => null], ['name' => 'offset', 'required' => false, 'default' => false], ['name' => 'limit', 'required' => false, 'default' => false], ['name' => 'only_published', 'required' => false, 'default' => false]]];
$FunctionList['list_count'] = ['name' => 'list_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZURLFunctionCollection', 'method' => 'fetchListCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'is_valid', 'required' => false, 'default' => null], ['name' => 'only_published', 'required' => false, 'default' => false]]];

?>
