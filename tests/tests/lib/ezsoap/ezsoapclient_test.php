<?php
/**
 * File containing the eZSOAPClientTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZSOAPClientTest extends ezpTestCase
{
    public static function providerTestSoapClientConstructorUseSSL()
    {
        return [[80, false], [80, false, false], [80, true, true], [443, true], [443, false, false], [443, true, true], ['ssl', true], ['ssl', true, true], ['ssl', false, false]];
    }

    /**
     * @dataProvider providerTestSoapClientConstructorUseSSL
     */
    public function testSoapClientConstructorUseSSL( $port, $expectedUseSSLResult, $useSSL = null )
    {
        $client = new eZSOAPClient( 'soap.example.com', '/', $port, $useSSL );
        static::assertEquals(static::readAttribute($client, 'UseSSL'), $expectedUseSSLResult);
    }

    public static function providerTestSoapClientConstructorPort()
    {
        return [[80, 80], [443, 443], ['ssl', 443]];
    }

    /**
     * @dataProvider providerTestSoapClientConstructorPort
     */
    public function testSoapClientConstructorPort( $port, $expectedPortResult )
    {
        $client = new eZSOAPClient( 'soap.example.com', '/', $port );
        static::assertEquals(static::readAttribute($client, 'Port'), $expectedPortResult);
    }

    /**
     * Provider for testSoapClientSend
     *
     * NB: This relies on network connection to soap.critmon1.ez.no
     */
    public static function providerTestSoapClientSend()
    {
        return [['bb4a091369e40cbf682a278cfd35f04a', 'soap.critmon1.ez.no', '/', 80, 'hostID', 'network_namespace'], ['bb4a091369e40cbf682a278cfd35f04a', 'soap.critmon1.ez.no', '/', 443, 'hostID', 'network_namespace']];
    }

    /**
     * @dataProvider providerTestSoapClientSend
     */
    public function testSoapClientSend( $expectedSendResult, $server, $path, $port, $name, $namespace, $parameters = [] )
    {
        self::markTestSkipped( "Test disabled as critmon has been shut down. Needs a different server or way of doing this." );

        $client = new eZSOAPClient( $server, $path, $port );
        $request = new eZSOAPRequest( $name, $namespace, $parameters );
        $response = $client->send( $request );
        static::assertEquals($response->value(), $expectedSendResult);
    }
}

?>
