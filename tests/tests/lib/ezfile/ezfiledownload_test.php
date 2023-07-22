<?php
/**
 * File containing the eZFileDownloadTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZFileDownloadTest extends ezpTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->file = __DIR__ . "/data/file.txt";
        $this->content = file_get_contents( $this->file );
    }

    public function testDownload()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file ));
        static::assertEquals($this->content, ob_get_clean());
    }

    public function testDownloadOffset()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 8000 ));
        static::assertEquals(substr( (string) $this->content, -192 ), ob_get_clean());
    }

    public function testDownloadSize()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 0, 100 ));
        static::assertEquals(substr( (string) $this->content, 0, 100 ), ob_get_clean());
    }

    public function testDownloadOffsetSize()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 8000, 100 ));
        static::assertEquals(substr( (string) $this->content, 8000, 100 ), ob_get_clean());
    }

    public function testDownloadOffsetSizeTooHigh()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 8000, 192 ));
        static::assertEquals(substr( (string) $this->content, 8000 ), ob_get_clean());
    }

    public function testDownloadOffsetSizeWayTooHigh()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 8000, 1e5 ));
        static::assertEquals(substr( (string) $this->content, 8000 ), ob_get_clean());
    }

    public function testDownloadOffsetTooBig()
    {
        ob_start();
        static::assertTrue(eZFile::downloadContent( $this->file, 8193 ));
        static::assertEquals("", ob_get_clean());
    }

    public function testDownloadNoFile()
    {
        ob_start();
        static::assertFalse(eZFile::downloadContent( "unexisting.txt" ));
        static::assertEquals("", ob_get_clean());
    }
}

?>
