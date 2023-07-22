<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params["Module"];

$offset = $Params['Offset'];
$limit = 15;

$tpl = eZTemplate::factory();

$http = eZHTTPTool::instance();

$customerArray = eZOrder::customerList( $offset, $limit );

$customerCount = eZOrder::customerCount();

$tpl->setVariable( "customer_list", $customerArray );
$tpl->setVariable( "customer_list_count", $customerCount );
$tpl->setVariable( "limit", $limit );

$viewParameters = ['offset' => $offset];
$tpl->setVariable( "module", $module );
$tpl->setVariable( 'view_parameters', $viewParameters );

$path = [];
$path[] = ['text' => ezpI18n::tr( 'kernel/shop', 'Customer list' ), 'url' => false];

$Result = [];
$Result['path'] = $path;

$Result['content'] = $tpl->fetch( "design:shop/customerlist.tpl" );

?>
