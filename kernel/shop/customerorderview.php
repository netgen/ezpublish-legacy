<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$CustomerID = $Params['CustomerID'];
$Email = $Params['Email'];
$module = $Params['Module'];


$http = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$Email = urldecode( (string) $Email );
$productList = eZOrder::productList( $CustomerID, $Email );
$orderList = eZOrder::orderList( $CustomerID, $Email );

$tpl->setVariable( "product_list", $productList );

$tpl->setVariable( "order_list", $orderList );

$Result = [];
$Result['content'] = $tpl->fetch( "design:shop/customerorderview.tpl" );
$path = [];
$path[] = ['url' => '/shop/orderlist', 'text' => ezpI18n::tr( 'kernel/shop', 'Order list' )];
$path[] = ['url' => false, 'text' => ezpI18n::tr( 'kernel/shop', 'Customer order view' )];
$Result['path'] = $path;

?>
