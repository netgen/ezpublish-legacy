<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['role'] = ['name' => 'role', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZRoleFunctionCollection', 'method' => 'fetchRole'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'role_id', 'type' => 'integer', 'required' => true]]];

?>
