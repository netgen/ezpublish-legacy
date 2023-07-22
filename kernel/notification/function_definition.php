<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['handler_list'] = ['name' => 'handler_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'handlerList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['digest_handlers'] = ['name' => 'digest_handlers', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'digestHandlerList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'date', 'type' => 'integer', 'required' => true], ['name' => 'address', 'type' => 'string', 'required' => true]]];


$FunctionList['digest_items'] = ['name' => 'digest_items', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'digestItems'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'date', 'type' => 'integer', 'required' => true], ['name' => 'address', 'type' => 'string', 'required' => true], ['name' => 'handler', 'type' => 'string', 'required' => true]]];


$FunctionList['event_content'] = ['name' => 'event_content', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'eventContent'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'event_id', 'type' => 'integer', 'required' => true]]];

$FunctionList['subscribed_nodes'] = ['name' => 'subscribed_nodes', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'subscribedNodes'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'offset', 'type' => 'integer', 'default' => false, 'required' => false], ['name' => 'limit', 'type' => 'integer', 'default' => false, 'required' => false]]];

$FunctionList['subscribed_nodes_count'] = ['name' => 'subscribed_nodes_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZNotificationFunctionCollection', 'method' => 'subscribedNodesCount'], 'parameter_type' => 'standard', 'parameters' => []];

?>
