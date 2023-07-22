<?php
/**
 * File containing the eZDirTestOutsideRoot class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZDirTestOutsideRoot extends ezpTestCase
{
    protected $rootDir;

    public function __construct()
    {
        parent::__construct();
        $this->rootDir = sys_get_temp_dir() . '/tests/';
        $this->setName( "eZDirTestOutsideRoot" );
    }

    public function setUp()
    {
        file_exists( $this->rootDir ) or @mkdir( $this->rootDir, 0777, true ) or static::markTestSkipped('Cannot create temporary directories outside ezp root');
        file_exists( $this->rootDir . 'a/b/c/' ) or @mkdir( $this->rootDir . 'a/b/c/', 0777, true ) or static::markTestSkipped('Cannot create temporary directories outside ezp root');
        touch( $this->rootDir . 'a/fileA' ) or static::markTestSkipped('Cannot create temporary files outside ezp root');
        touch( $this->rootDir . 'a/b/fileB' ) or static::markTestSkipped('Cannot create temporary files outside ezp root');
        parent::setUp();
    }

    public function tearDown()
    {
        foreach ( [$this->rootDir . 'a/fileA', $this->rootDir . 'a/b/fileB'] as $file )
            file_exists( $file ) && unlink( $file );

        foreach ( [$this->rootDir . 'a/b/c/', $this->rootDir . 'a/b/', $this->rootDir . 'a/', $this->rootDir] as $dir )
            file_exists( $dir ) && rmdir( $dir );

        parent::tearDown();
    }

    public function testRemoveWithoutCheck()
    {
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a/b/c/', false ));
        static::assertFalse(file_exists( $this->rootDir . 'a/b/c' ));
    }

    public function testRemoveWithoutCheckNoTrailingSlash()
    {
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a/b/c', false ));
        static::assertFalse(file_exists( $this->rootDir . 'a/b/c' ));
    }

    public function testRemoveWithCheck()
    {
        static::assertFalse(eZDir::recursiveDelete( $this->rootDir . 'a/b/c/', true ));
        static::assertTrue(file_exists( $this->rootDir . 'a/b/c' ));
    }

    public function testRemoveRecursivelyWithoutCheck()
    {
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a/', false ));
        static::assertFalse(file_exists( $this->rootDir . 'a' ));
    }

    public function testRemoveRecursivelyWithoutCheckNoTrailingSlash()
    {
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a', false ));
        static::assertFalse(file_exists( $this->rootDir . 'a' ));
    }

    public function testRemoveRecursivelyWithCheck()
    {
        static::assertFalse(eZDir::recursiveDelete( $this->rootDir . 'a/', true ));
        static::assertTrue(file_exists( $this->rootDir . 'a' ));
    }

    public function testRemoveRecursivelyWithCheckNoTrailingSlash()
    {
        static::assertFalse(eZDir::recursiveDelete( $this->rootDir . 'a', true ));
        static::assertTrue(file_exists( $this->rootDir . 'a' ));
    }

    public function testRemoveWithoutCheckNotExisting()
    {
        static::assertFalse(eZDir::recursiveDelete( $this->rootDir . 'doesNotExist', false ));
    }

    public function testRemoveWithCheckNotExisting()
    {
        static::assertFalse(eZDir::recursiveDelete( $this->rootDir . 'doesNotExist', true ));
    }
}

?>
