<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['basket'] = ['name' => 'basket', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchBasket'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['best_sell_list'] = ['name' => 'best_sell_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchBestSellList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'top_parent_node_id', 'type' => 'integer', 'required' => true], ['name' => 'limit', 'type' => 'integer', 'required' => false], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'start_time', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'end_time', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'duration', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'ascending', 'type' => 'boolean', 'required' => false, 'default' => false], ['name' => 'extended', 'type' => 'boolean', 'required' => false, 'default' => false]]];

$FunctionList['related_purchase'] = ['name' => 'related_purchase', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchRelatedPurchaseList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'contentobject_id', 'type' => 'integer', 'required' => true], ['name' => 'limit', 'type' => 'integer', 'required' => true]]];

$FunctionList['wish_list'] = ['name' => 'wish_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchWishList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'production_id', 'type' => 'integer', 'required' => true], ['name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => false], ['name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => false]]];

$FunctionList['wish_list_count'] = ['name' => 'wish_list_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchWishListCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'production_id', 'type' => 'integer', 'required' => true]]];

$FunctionList['current_wish_list'] = ['name' => 'current_wish_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchCurrentWishList'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['order'] = ['name' => 'order', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchOrder'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'order_id', 'type' => 'integer', 'required' => true]]];
$FunctionList['order_status_history_count'] = ['name' => 'order_status_history_count', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchOrderStatusHistoryCount'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'order_id', 'type' => 'integer', 'required' => true]]];
$FunctionList['order_status_history'] = ['name' => 'order_status_history', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchOrderStatusHistory'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'order_id', 'type' => 'integer', 'required' => true]]];
$FunctionList['currency_list'] = ['name' => 'currency_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchCurrencyList'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'status', 'type' => 'integer,string', 'required' => false]]];

$FunctionList['currency'] = ['name' => 'currency', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchCurrency'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'code', 'type' => 'string', 'required' => true]]];

$FunctionList['preferred_currency_code'] = ['name' => 'preferred_currency_code', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchPreferredCurrencyCode'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['user_country'] = ['name' => 'user_country', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchUserCountry'], 'parameter_type' => 'standard', 'parameters' => []];

$FunctionList['product_category_list'] = ['name' => 'product_category_list', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchProductCategoryList'], 'parameter_type' => 'standard', 'parameters' => []];


$FunctionList['product_category'] = ['name' => 'product_category', 'operation_types' => ['read'], 'call_method' => ['class' => 'eZShopFunctionCollection', 'method' => 'fetchProductCategory'], 'parameter_type' => 'standard', 'parameters' => [['name' => 'category_id', 'type' => 'integer,string', 'required' => true]]];
?>
