<?php
/**
 * File containing the eZOrderRegression class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZOrderRegression extends ezpDatabaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZOrder Regression Tests" );
    }

    public function testIssue18233()
    {
        // insert orders
        $orderData = ['account_identifier' => 'ez', 'created' => 130_252_369, 'data_text_1' => '\<?xml ?\>', 'email' => 'xc@ez.no', 'productcollection_id' => '5', 'status_modifier_id' => '14', 'user_id' => 14];
        $order = new eZOrder( $orderData );
        $order->store();
        $oldOrderNR = $order->attribute( 'order_nr' );
        $order->activate();
        static::assertEquals($oldOrderNR+1, $order->attribute( 'id' ));
        static::assertEquals(0, $order->attribute( 'is_temporary' ));
    }

}
?>
