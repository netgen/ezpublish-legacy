<?php
/**
 * File containing the eZDirTestInsideRoot class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZDirTestInsideRoot extends ezpTestCase
{
    private string $rootDir = 'var/tests/eZDirTestInsideRoot/';

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZDirTestInsideRoot" );
    }

    public function setUp()
    {
        parent::setUp();
        file_exists( $this->rootDir ) or @mkdir( $this->rootDir, 0777, true );
        file_exists( $this->rootDir . 'a/b/c/' ) or @mkdir( $this->rootDir . 'a/b/c/', 0777, true );
        touch( $this->rootDir . 'a/fileA' );
        touch( $this->rootDir . 'a/b/fileB' );
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
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a/b/c/', true ));
        static::assertFalse(file_exists( $this->rootDir . 'a/b/c' ));
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
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a/', true ));
        static::assertFalse(file_exists( $this->rootDir . 'a' ));
    }

    public function testRemoveRecursivelyWithCheckNoTrailingSlash()
    {
        static::assertTrue(eZDir::recursiveDelete( $this->rootDir . 'a', true ));
        static::assertFalse(file_exists( $this->rootDir . 'a' ));
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
