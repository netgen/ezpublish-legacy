<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZShop", "variable_params" => true];

$ViewList = [];
$ViewList["add"] = ["functions" => ['buy'], "script" => "add.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => ["ObjectID", "Quantity"]];

$ViewList["orderview"] = ["functions" => ['buy'], "script" => "orderview.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => ["OrderID"]];

$ViewList['updatebasket'] = ['functions' => ['buy'], 'script' => 'updatebasket.php', 'default_navigation_part' => 'ezshopnavigationpart', 'params' => []];

$ViewList["basket"] = ["functions" => ['buy'], "script" => "basket.php", "default_navigation_part" => 'ezmynavigationpart', 'unordered_params' => ['error' => 'Error'], "params" => []];

$ViewList["register"] = ["functions" => ['buy'], "script" => "register.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'CancelButton' => 'Cancel'], "params" => []];

$ViewList["userregister"] = ["functions" => ['buy'], "script" => "userregister.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'CancelButton' => 'Cancel']];

$ViewList["wishlist"] = ["functions" => ['buy'], "script" => "wishlist.php", "default_navigation_part" => 'ezmynavigationpart', 'unordered_params' => ['offset' => 'Offset'], "params" => []];

$ViewList["orderlist"] = ["functions" => ['administrate'], "script" => "orderlist.php", "default_navigation_part" => 'ezshopnavigationpart', "unordered_params" => ["offset" => "Offset"], "params" => []];

$ViewList["archivelist"] = ["functions" => ['administrate'], "script" => "archivelist.php", "default_navigation_part" => 'ezshopnavigationpart', "unordered_params" => ["offset" => "Offset"], "params" => []];

$ViewList["removeorder"] = ["functions" => ['administrate'], "script" => "removeorder.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["archiveorder"] = ["functions" => ['administrate'], "script" => "archiveorder.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["unarchiveorder"] = ["functions" => ['administrate'], "script" => "unarchiveorder.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', "params" => []];


$ViewList["customerlist"] = ["functions" => ['administrate'], "script" => "customerlist.php", "default_navigation_part" => 'ezshopnavigationpart', "unordered_params" => ['offset' => 'Offset'], "params" => []];

$ViewList["customerorderview"] = ["functions" => ['administrate'], "script" => "customerorderview.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => ["CustomerID", "Email"]];

$ViewList["statistics"] = ["functions" => ['administrate'], "script" => "orderstatistics.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => ['Year', 'Month']];

$ViewList["confirmorder"] = ["functions" => ['buy'], "script" => "confirmorder.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["checkout"] = ["functions" => ['buy'], "script" => "checkout.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["vattype"] = ["functions" => ['setup'], "script" => "vattype.php", "default_navigation_part" => 'ezshopnavigationpart', 'single_post_actions' => ['RemoveVatTypeButton'  => 'Remove', 'AddVatTypeButton'     => 'Add', 'SaveVatTypeButton'    => 'SaveChanges', 'ConfirmRemovalButton' => 'ConfirmRemoval'], 'post_action_parameters' => ['Remove'         => ['vatTypeIDList' => 'vatTypeIDList'], 'ConfirmRemoval' => ['vatTypeIDList' => 'vatTypeIDList', 'VatReplacement' => 'VatReplacement']], "params" => []];

$ViewList["vatrules"] = ["functions" => ['setup'], "script" => "vatrules.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["editvatrule"] = ["functions" => ['setup'], "script" => "editvatrule.php", "default_navigation_part" => 'ezshopnavigationpart', 'single_post_actions' => ['CancelButton' => 'Cancel', 'CreateButton' => 'Create', 'StoreChangesButton' => 'StoreChanges'], 'post_action_parameters' => ['Create' => ['Country' => 'Country', 'Categories' => 'Categories', 'VatType' => 'VatType'], 'StoreChanges' => ['RuleID' => 'RuleID', 'Country' => 'Country', 'Categories' => 'Categories', 'VatType' => 'VatType']], 'params' => ['ruleID'], 'unordered_params' => ['currency' => 'Currency']];


$ViewList["productcategories"] = ["functions" => ['setup'], "script" => "productcategories.php", "default_navigation_part" => 'ezshopnavigationpart', 'single_post_actions' => [
    'AddCategoryButton'    => 'Add',
    'RemoveCategoryButton' => 'Remove',
    'ConfirmRemovalButton' => 'ConfirmRemoval',
    // remove dialog
    'CancelRemovalButton'  => 'CancelRemoval',
    // remove dialog
    'SaveCategoriesButton' => 'StoreChanges',
], 'post_action_parameters' => ['Remove'         => ['CategoryIDList' => 'CategoryIDList'], 'ConfirmRemoval' => ['CategoryIDList' => 'CategoryIDList']], "params" => []];

$ViewList["discountgroup"] = ["functions" => ['setup'], "script" => "discountgroup.php", "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList["discountgroupedit"] = ["functions" => ['setup'], "script" => "discountgroupedit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', "params" => ['DiscountGroupID']];

$ViewList["discountruleedit"] = ["functions" => ['setup'], "script" => "discountruleedit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezshopnavigationpart', 'post_actions' => ['BrowseActionName'], "params" => ['DiscountGroupID', 'DiscountRuleID']];

$ViewList["discountgroupview"] = ['functions' => ['setup'], "script" => "discountgroupmembershipview.php", "default_navigation_part" => 'ezshopnavigationpart', 'post_actions' => ['BrowseActionName'], "params" => ['DiscountGroupID']];

$ViewList['status'] = ["functions" => ['edit_status'], "script" => 'status.php', "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList['setstatus'] = ["functions" => ['setstatus'], "script" => 'setstatus.php', "default_navigation_part" => 'ezshopnavigationpart', "params" => []];

$ViewList['currencylist'] = ['functions' => ['setup'], 'script' => 'currencylist.php', 'default_navigation_part' => 'ezshopnavigationpart', 'unordered_params' => ['offset' => 'Offset'], 'single_post_actions' => ['NewCurrencyButton' => 'NewCurrency', 'RemoveCurrencyButton' => 'RemoveCurrency', 'ApplyChangesButton' => 'ApplyChanges', 'UpdateAutopricesButton' => 'UpdateAutoprices', 'UpdateAutoRatesButton' => 'UpdateAutoRates'], 'post_action_parameters' => ['RemoveCurrency' => ['DeleteCurrencyList' => 'DeleteCurrencyList'], 'ApplyChanges' => ['CurrencyList' => 'CurrencyList', 'Offset' => 'Offset'], 'UpdateAutoprices' => ['Offset' => 'Offset'], 'UpdateAutoRates' => ['Offset' => 'Offset']], 'params' => []];

$ViewList['editcurrency'] = ['functions' => ['setup'], 'script' => 'editcurrency.php', 'default_navigation_part' => 'ezshopnavigationpart', 'single_post_actions' => ['CancelButton' => 'Cancel', 'CreateButton' => 'Create', 'StoreChangesButton' => 'StoreChanges'], 'post_action_parameters' => ['Create' => ['CurrencyData' => 'CurrencyData'], 'StoreChanges' => ['CurrencyData' => 'CurrencyData', 'OriginalCurrencyCode' => 'OriginalCurrencyCode']], 'params' => [], 'unordered_params' => ['currency' => 'Currency']];

$ViewList['preferredcurrency'] = ['functions' => ['buy'], 'script' => 'preferredcurrency.php', 'default_navigation_part' => 'ezshopnavigationpart', 'params' => []];

$ViewList['productsoverview'] = ['functions' => ['administrate'], 'script' => 'productsoverview.php', 'default_navigation_part' => 'ezshopnavigationpart', 'single_post_actions' => ['ShowProductsButton' => 'ShowProducts', 'SortButton' => 'Sort'], 'post_action_parameters' => ['ShowProducts' => ['ProductClass' => 'ProductClass'], 'Sort' => ['ProductClass' => 'ProductClass', 'SortingField' => 'SortingField', 'SortingOrder' => 'SortingOrder']], 'params' => [], 'unordered_params' => ['product_class' => 'ProductClass', 'offset' => 'Offset']];

$ViewList['setpreferredcurrency'] = ['functions' => ['buy'], 'script' => 'setpreferredcurrency.php', 'default_navigation_part' => 'ezshopnavigationpart', 'single_post_actions' => ['SetButton' => 'Set'], 'post_action_parameters' => ['Set' => ['Currency' => 'Currency']], 'unordered_params' => ['currency' => 'Currency'], 'params' => []];

$ViewList['setusercountry'] = ['functions' => ['buy'], 'script' => 'setusercountry.php', 'default_navigation_part' => 'ezshopnavigationpart', 'single_post_actions' => ['ApplyButton' => 'Set'], 'post_action_parameters' => ['Set' => ['Country' => 'Country']], 'unordered_params' => ['country' => 'Country'], 'params' => []];


$FromStatus = ['name' => 'FromStatus', 'values' => [], 'path' => 'classes/', 'file' => 'ezorderstatus.php', 'class' => 'eZOrderStatus', 'function' => 'fetchPolicyList', 'parameter' => [false]];

$ToStatus = ['name' => 'ToStatus', 'values' => [], 'path' => 'classes/', 'file' => 'ezorderstatus.php', 'class' => 'eZOrderStatus', 'function' => 'fetchPolicyList', 'parameter' => [false]];

$FunctionList = [];
$FunctionList['setup'] = [];
$FunctionList['administrate'] = [];
$FunctionList['buy'] = [];
$FunctionList['edit_status'] = [];
$FunctionList['setstatus'] = ['FromStatus' => $FromStatus, 'ToStatus' => $ToStatus];

?>
