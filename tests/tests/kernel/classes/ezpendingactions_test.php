<?php
/**
 * File containing the eZPendingActions class tests
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZPendingActionsTest extends ezpDatabaseTestCase
{
    /**
     * Unit test for eZPersistentObject implementation
     */
    public function testPersistentObjectInterface()
    {
        static::assertTrue(is_subclass_of( 'eZPendingActions', 'eZPersistentObject' ));
        static::assertTrue(method_exists( 'eZPendingActions', 'definition' ));
    }

    /**
     * Unit test for good eZPersistentObject (ORM) implementation for ezsite_data table
     */
    public function testORMImplementation()
    {
        $def = eZPendingActions::definition();
        static::assertEquals('eZPendingActions', $def['class_name']);
        static::assertEquals('ezpending_actions', $def['name']);

        $fields = $def['fields'];
        static::assertArrayHasKey('action', $fields);
        static::assertArrayHasKey('created', $fields);
        static::assertArrayHasKey('param', $fields);
    }

    /**
     * Unit test for fetchByAction() method
     */
    public function testFetchByAction()
    {
        // Insert several fixtures at one time. Can't use @dataProvider to do that
        $fixtures = $this->providerForTestFecthByAction();
        foreach( $fixtures as $fixture )
        {
            $this->insertPendingAction( $fixture[0], $fixture[1], $fixture[2] );
        }

        $res = eZPendingActions::fetchByAction( 'test' );
        static::assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res);
        foreach($res as $row)
        {
            static::assertInstanceOf('eZPendingActions', $row);
        }

        unset($res);

        $dateFilter = ['<=', time()];
        $res = eZPendingActions::fetchByAction( 'test', $dateFilter );
        static::assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res);
    }

    /**
     * Data provider for self::testFetchByAction()
     * @see testFetchByAction()
     */
    public function providerForTestFecthByAction()
    {
        $time = time();

        return [['test', $time, 'Some params'], ['test', $time+10, 'Other params'], ['test', $time+20, '']];
    }

    /**
     * Inserts a pending action
     * @param $action
     * @param $created
     * @param $params
     */
    private function insertPendingAction( $action, $created, $params )
    {
        $row = ['action'      => $action, 'created'     => $created, 'param'       => $params];

        $obj = new eZPendingActions( $row );
        $obj->store();
        unset( $obj );
    }

    /**
     * Test for bad date filter token in eZPendingActions::fetchByAction()
     * @param $badFilter
     * @dataProvider providerForTestBadDateFilter
     */
    public function testBadDateFilter( $badFilter )
    {
        $res = eZPendingActions::fetchByAction( 'test', $badFilter );
        static::assertNull($res);
    }

    /**
     * Provider for self::testBadDateFilter()
     * @see testBadDateFilter()
     */
    public function providerForTestBadDateFilter()
    {
        return [
            [[time(), '=']],
            // Wrong order
            [['<=', time(), 'foobar']],
            // Wrong entries count
            [['<>', time()]],
        ];
    }

    /**
     * Test for eZPendingActions::removeByAction()
     */
    public function testRemoveByAction()
    {
        // Insert several fixtures at one time. Can't use @dataProvider to do that
        $fixtures = $this->providerForTestFecthByAction();
        foreach( $fixtures as $fixture )
        {
            $this->insertPendingAction( $fixture[0], $fixture[1], $fixture[2] );
        }

        eZPendingActions::removeByAction( 'test' );
        $res = eZPendingActions::fetchByAction( 'test' );
        static::assertTrue(empty( $res ));
    }
}

?>
