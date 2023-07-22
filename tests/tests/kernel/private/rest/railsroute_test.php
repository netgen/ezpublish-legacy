<?php
/**
 * File containing ezpRestControllerTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * Unit tests for REST Rails Route
 */
class ezpRestRailsRouteTest extends ezpRestTestCase
{
    /**
     * Test that 4.6 syntax still works
     */
    public function testBC46()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar';
        $request->protocol = 'http-post';

        $route = new ezpMvcRailsRoute( '/foo/bar', 'fooController', 'barAction', [], 'http-post' );
        $info = $route->matches( $request );

        static::assertInstanceOf('ezcMvcRoutingInformation', $info);
        static::assertEquals($info->controllerClass, 'fooController');
        static::assertEquals($info->action, 'barAction');

        $request->uri = '/foo/bar2';
        $info = $route->matches( $request );
        static::assertNull($info);

        $request->uri = '/foo/bar';
        $request->protocol = 'http-get';
        $route = new ezpMvcRailsRoute( '/foo/bar', 'fooController', 'barAction' );
        $info = $route->matches( $request );
        static::assertInstanceOf('ezcMvcRoutingInformation', $info);
        static::assertEquals($info->controllerClass, 'fooController');
        static::assertEquals($info->action, 'barAction');

        $request->uri = '/foo/bar2';
        $info = $route->matches( $request );
        static::assertNull($info);
    }

    /**
     * test that with an unexpected used HTTP verb, ezpRestRailsRouteTest
     * throws a ezpRouteMethodNotAllowedException
     *
     * @expectedException ezpRouteMethodNotAllowedException
     */
    public function testNotAllowedMethod()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar';
        $request->protocol = 'http-get';

        $route = new ezpMvcRailsRoute(
            '/foo/bar', 'fooController',
            ['http-post' => 'barPostAction', 'http-put' => 'barPutAction']
        );
        $info = $route->matches( $request );
    }

    /**
     * Test the ezpRestRailsRouteTest::matches() returns null if it does not
     * find the route.
     */
    public function testNotMatch()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar/foo/bar';
        $request->protocol = 'http-post';

        $route = new ezpMvcRailsRoute(
            '/foo/bar', 'fooController',
            ['http-post' => 'barPostAction', 'http-put' => 'barPutAction']
        );
        $info = $route->matches( $request );
        static::assertNull($info);
    }

    /**
     * Test that ezpRestRailsRouteTest finds its way :-)
     */
    public function testMatchOK()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar';
        $request->protocol = 'http-put';

        $route = new ezpMvcRailsRoute(
            '/foo/bar', 'fooController',
            ['http-post' => 'barPostAction', 'http-put' => 'barPutAction']
        );
        $info = $route->matches( $request );

        static::assertInstanceOf('ezcMvcRoutingInformation', $info);
        static::assertEquals($info->controllerClass, 'fooController');
        static::assertEquals($info->action, 'barPutAction');
    }

    /**
     * Test that an OPTIONS request always works
     */
    public function testOptionsVerb()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar';
        $request->protocol = 'http-options';

        $route = new ezpMvcRailsRoute(
            '/foo/bar', 'fooController',
            ['http-post' => 'barPostAction', 'http-put' => 'barPutAction']
        );
        $info = $route->matches( $request );

        static::assertInstanceOf('ezcMvcRoutingInformation', $info);
        static::assertEquals($info->controllerClass, 'fooController');
        static::assertEquals($info->action, 'httpOptions');
        static::assertTrue(isset( $request->variables['supported_http_methods'] ));
        static::assertSame(['POST', 'PUT', 'OPTIONS'], $request->variables['supported_http_methods']);
    }

    /**
     * Test that it's possible to handle OPTIONS request with a custom action
     * instead of the default one.
     */
    public function testCustomActionForOptionsVerb()
    {
        $request = new ezcMvcRequest;
        $request->uri = '/foo/bar';
        $request->protocol = 'http-options';

        $route = new ezpMvcRailsRoute(
            '/foo/bar', 'fooController',
            ['http-post' => 'barPostAction', 'http-options' => 'barCustomOptionsAction']
        );
        $info = $route->matches( $request );

        static::assertInstanceOf('ezcMvcRoutingInformation', $info);
        static::assertEquals($info->controllerClass, 'fooController');
        static::assertEquals($info->action, 'barCustomOptionsAction');
    }

}
