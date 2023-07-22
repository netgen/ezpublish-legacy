<?php
/**
 * File containing the eZSiteDataTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZSiteDataTest extends ezpDatabaseTestCase
{
    /**
     * Unit test for eZPersistentObject implementation
     */
    public function testPersistentObjectInterface()
    {
        static::assertTrue(is_subclass_of( 'eZSiteData', 'eZPersistentObject' ));
        static::assertTrue(method_exists( 'eZSiteData', 'definition' ));
    }

    /**
     * Unit test for good eZPersistentObject (ORM) implementation for ezsite_data table
     */
    public function testORMImplementation()
    {
        $def = eZSiteData::definition();
        static::assertEquals('eZSiteData', $def['class_name']);
        static::assertEquals('ezsite_data', $def['name']);

        $fields = $def['fields'];
        static::assertArrayHasKey('name', $fields);
        static::assertArrayHasKey('value', $fields);
    }

    /**
     * Unit test for fetchByName() method
     */
    public function testFetchByName()
    {
        $name = 'foo';
        $row = ['name'      => $name, 'value'     => 'bar'];

        $obj = new eZSiteData( $row );
        $obj->store();
        unset( $obj );

        $res = eZSiteData::fetchByName( $name );
        static::assertInstanceOf('eZSiteData', $res);

        $res->remove();
    }

    /**
     * Unit test for testCreate() method
     */
    public function testCreate()
    {
        $obj = eZSiteData::create( 'foo', 'bar' );
        $obj->store();
        unset( $obj );

        $res = eZSiteData::fetchByName( 'foo' );
        static::assertInstanceOf('eZSiteData', $res);

        $res->remove();
    }

}

?>
