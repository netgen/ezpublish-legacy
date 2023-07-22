<?php
/**
 * File containing the eZHTTPToolTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZHTTPToolTest extends ezpTestCase
{
    /**
     * Provider for testGetDataByURL
     *
     * NB: This relies on network connection to soap.critmon1.ez.no
     */
    public static function providerTestGetDataByURL()
    {
        return [['Error: this web page does only understand POST methods', 'http://soap.critmon1.ez.no'], ['Error: this web page does only understand POST methods', 'https://soap.critmon1.ez.no'], [true, 'http://soap.critmon1.ez.no', true], [true, 'https://soap.critmon1.ez.no', true]];
    }

    /**
     * @dataProvider providerTestGetDataByURL
     */
    public function testGetDataByURL( $expectedDataResult, $url, $justCheckURL = false, $userAgent = false )
    {
        self::markTestSkipped( "Test disabled as critmon has been shut down. Needs a different server or way of doing this." );

        static::assertEquals(eZHTTPTool::getDataByURL( $url, $justCheckURL, $userAgent ), $expectedDataResult);

        // There's no way to test the whole method without refactoring it.
        if ( extension_loaded( 'curl' ) )
        {
            static::markTestIncomplete('cURL behaviour tested, not fopen()');
        }
        else
        {
            static::markTestIncomplete('fopen() behaviour tested, not cURL');
        }
    }
}

?>
