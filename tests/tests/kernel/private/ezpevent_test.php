<?php
/**
 * File containing the ezpTopologicalSortTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpEventTest extends ezpTestCase
{
    protected $event = null;

    public function setUp()
    {
        parent::setUp();
        ezpEvent::resetInstance();
        ezpINIHelper::setINISetting( 'site.ini', 'Event', 'Listeners', ['test/notify@ezpEventTest::helperNotify', 'test/filter@ezpEventTest::helperFilterNotNull'] );
        $this->event = ezpEvent::getInstance();
        $this->event->registerEventListeners();
    }
    
    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        $this->event = null;
        ezpEvent::resetInstance();
        parent::tearDown();
    }

    /**
     * Test misc aspects of attach() and detach()
     */
    public function testAttachDetach()
    {
        // test attach (returned value, and that function is used)
        $id = $this->event->attach( 'test/attach', 'ezpEventTest::helperFilterInc' );
        static::assertTrue(is_numeric( $id ));
        static::assertEquals(2, $this->event->filter( 'test/attach', 1 ));

        // test attach again with different callback format
        $id2 = $this->event->attach( 'test/attach', ['ezpEventTest', 'helperFilterInc'] );
        static::assertTrue(is_numeric( $id2 ));
        static::assertTrue($id < $id2);
        static::assertEquals(3, $this->event->filter( 'test/attach', 1 ));
        
        // test detach on $id
        static::assertTrue($this->event->detach( 'test/attach', $id ));
        static::assertEquals(2, $this->event->filter( 'test/attach', 1 ));
        
        // test detach on invalid id
        static::assertFalse($this->event->detach( 'test/attach', 404 ));

        // test detach on last $id
        static::assertTrue($this->event->detach( 'test/attach', $id2 ));
        static::assertEquals(1, $this->event->filter( 'test/attach', 1 ));
    }

    /**
     * Test that new instance does not inherit events
     * (when not reading from ini settings)
     */
    public function testNewInstance()
    {
        $event = new ezpEvent( false );

        // test filter
        static::assertEquals(null, $event->filter( 'test/filter', null ));

        // test notify
        static::assertFalse($event->notify( 'test/notify' ));
    }

    /**
     * Test misc aspects of filter()
     */
    public function testFilter()
    {
        // test that events w/o listeners return value
        static::assertFalse($this->event->filter( 'test/filter_not_here', false ));
        static::assertTrue($this->event->filter( 'test/filter_not_here', true ));
        static::assertEquals(null, $this->event->filter( 'test/filter_not_here', null ));

        // test that events with listeners return true and that value gets set
        static::assertFalse($this->event->filter( 'test/filter', false ));
        static::assertTrue($this->event->filter( 'test/filter', true ));
        static::assertTrue($this->event->filter( 'test/filter', null ));
    }

    /**
     * Test misc aspects of notify()
     */
    public function testNotify()
    {
        // make sure static var is null
        self::$internalTestNotify = null;

        // test that events w/o listeners return false
        static::assertFalse($this->event->notify( 'test/notify_not_here' ));

        // test that events with listeners return true and that value gets set
        static::assertTrue($this->event->notify( 'test/notify', [true] ));
        static::assertTrue(self::$internalTestNotify);

        // same test with different value
        static::assertTrue($this->event->notify( 'test/notify', [false] ));
        static::assertFalse(self::$internalTestNotify);

        // cleanup
        self::$internalTestNotify = null;
    }

    /**
     * Helper function used by testNotify(), sets recived param on static member so test can check it
     *
     * @param mixed $variable
     */
    public static function helperNotify( $param1 )
    {
        self::$internalTestNotify = $param1;
    }

    /**
     * Helper function used by testFilter(), returns value unless it's null, then return true
     */
    public static function helperFilterNotNull( mixed $variable )
    {
        if ( $variable === null )
            return true;
        return $variable;
    }

    /**
     * Helper function used by testAttachDetach() increases value by 1
     *
     * @param int $variable
     */
    public static function helperFilterInc( $variable )
    {
        return ++$variable;
    }

    protected static $internalTestNotify = null;
}
?>
