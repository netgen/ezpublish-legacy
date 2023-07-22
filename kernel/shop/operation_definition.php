<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$OperationList = [];
// This operation is used when a user tries to add an object to the basket
// It will be called from content/add
$OperationList['addtobasket'] = ['name' => 'addtobasket', 'default_call_method' => ['include_file' => 'kernel/shop/ezshopoperationcollection.php', 'class' => 'eZShopOperationCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'object_id', 'type' => 'integer', 'required' => true], ['name' => 'option_list', 'type' => 'array', 'required' => true], ['name' => 'quantity', 'type' => 'integer', 'required' => false], ['name' => 'basket_id', 'type' => 'integer', 'required' => true]], 'keys' => ['basket_id', 'object_id'], 'body' => [['type' => 'trigger', 'name' => 'pre_addtobasket', 'keys' => ['object_id']], ['type' => 'method', 'name' => 'add-to-basket', 'frequency' => 'once', 'method' => 'addToBasket'], ['type' => 'method', 'name' => 'update-shipping-info', 'frequency' => 'once', 'method' => 'updateShippingInfo'], ['type' => 'trigger', 'name' => 'post_addtobasket', 'keys' => ['object_id']]]];

$OperationList['confirmorder'] = ['name' => 'confirmorder', 'default_call_method' => ['include_file' => 'kernel/shop/ezshopoperationcollection.php', 'class' => 'eZShopOperationCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'order_id', 'type' => 'integer', 'required' => true]], 'keys' => ['order_id'], 'body' => [['type' => 'trigger', 'name' => 'pre_confirmorder', 'keys' => ['order_id']], ['type' => 'method', 'name' => 'handle-user-country', 'frequency' => 'once', 'method' => 'handleUserCountry'], ['type' => 'method', 'name' => 'handle-shipping', 'frequency' => 'once', 'method' => 'handleShipping'], ['type' => 'method', 'name' => 'fetch-order', 'frequency' => 'once', 'method' => 'fetchOrder']]];

$OperationList['updatebasket'] = ['name' => 'updatebasket', 'default_call_method' => ['include_file' => 'kernel/shop/ezshopoperationcollection.php', 'class' => 'eZShopOperationCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'item_count_list', 'type' => 'array', 'required' => true], ['name' => 'item_id_list', 'type' => 'array', 'required' => true]], 'keys' => ['item_count_list', 'item_id_list'], 'body' => [['type' => 'trigger', 'name' => 'pre_updatebasket', 'keys' => []], ['type' => 'method', 'name' => 'update-basket', 'frequency' => 'once', 'method' => 'updateBasket'], ['type' => 'trigger', 'name' => 'post_updatebasket', 'keys' => []]]];

$OperationList['checkout'] = ['name' => 'checkout', 'default_call_method' => ['include_file' => 'kernel/shop/ezshopoperationcollection.php', 'class' => 'eZShopOperationCollection'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'order_id', 'type' => 'integer', 'required' => true]], 'keys' => ['order_id'], 'body' => [['type' => 'method', 'name' => 'check-currency', 'frequency' => 'once', 'method' => 'checkCurrency'], ['type' => 'trigger', 'name' => 'pre_checkout', 'keys' => ['order_id']], ['type' => 'method', 'name' => 'activate-order', 'frequency' => 'once', 'method' => 'activateOrder'], ['type' => 'method', 'name' => 'send-order-email', 'frequency' => 'once', 'method' => 'sendOrderEmails'], ['type' => 'trigger', 'name' => 'post_checkout', 'keys' => ['order_id']]]];
?>
