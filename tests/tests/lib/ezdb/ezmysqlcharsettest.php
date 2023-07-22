<?php
/**
 * File containing the eZMySQLCharsetTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 * @subpackage Tests
 */

class eZMySQLCharsetTest extends ezpTestCase
{
    public function testKnownCharsetMapTo01()
    {
        static::assertSame("utf8", eZMySQLCharset::mapTo( "utf-8" ));
    }

    public function testKnownCharsetMapTo02()
    {
        static::assertSame("latin1", eZMySQLCharset::mapTo( "iso-8859-1" ));
    }

    public function testKnownUppercaseCharsetMapTo()
    {
        static::assertSame("utf8", eZMySQLCharset::mapTo( "UTF-8" ));
    }

    public function testUnknownCharsetMapTo()
    {
        static::assertSame("unknown", eZMySQLCharset::mapTo( "unknown" ));
    }

    public function testUnknownUppercaseCharsetMapTo()
    {
        static::assertSame("uNknOwN", eZMySQLCharset::mapTo( "uNknOwN" ));
    }

    public function testKnownCharsetMapFrom01()
    {
        static::assertSame("utf-8", eZMySQLCharset::mapFrom( "utf8" ));
    }

    public function testKnownCharsetMapFrom02()
    {
        static::assertSame("iso-8859-1", eZMySQLCharset::mapFrom( "latin1" ));
    }

    public function testKnownUppercaseCharsetMapFrom()
    {
        static::assertSame("utf-8", eZMySQLCharset::mapFrom( "UTF8" ));
    }

    public function testUnknownCharsetMapFrom()
    {
        static::assertSame("unknown", eZMySQLCharset::mapFrom( "unknown" ));
    }

    public function testUnknownUppercaseCharsetMapFrom()
    {
        static::assertSame("uNknOwN", eZMySQLCharset::mapFrom( "uNknOwN" ));
    }
}
?>
