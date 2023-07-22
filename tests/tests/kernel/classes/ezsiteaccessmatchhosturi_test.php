<?php
/**
 * File containing the eZSiteAccessMatchHostUriTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZSiteAccessMatchHostUriTest extends ezpTestCase
{
    public function setUp()
    {
        parent::setUp();
        ezpINIHelper::setINISetting( "site.ini", "SiteAccessSettings", "MatchOrder", "host_uri" );
        ezpINIHelper::setINISetting(
            "site.ini",
            "SiteAccessSettings",
            "HostUriMatchMapItems",
            ["www.example.com;abcd/foo;abcd_foo", "www.example.com;abcdef/foo;abcdef_foo", "www.example.com;abc/foo;abc_foo", "www.example.com;abcdefg/foo;abcdefg_foo", "www.example.com;abcde/foo;abcde_foo", "www.example.com;abcd;abcd", "www.example.com;abcdef;abcdef", "www.example.com;abc;abc", "www.example.com;abcdefg;abcdefg", "www.example.com;abcde;abcde", "www.example.com;admin;admin", "www.example.com;engæ/foo;eng_foo", "www.example.com;engæ;eng"]
        );
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    /**
     * Test for eZContentObject::versions(), fetching all of them
     * @dataProvider providerForTestMatchHostUri
     */
    public function testMatchHostUri( $uri, $name, $type, $uriPart )
    {
        static::assertEquals(["name" => $name, "type" => $type, "uri_part" => $uriPart], eZSiteAccess::match(
            new eZURI( $uri ),
            "www.example.com"
        ));
    }

    public function providerForTestMatchHostUri()
    {
        return [["", "admin", eZSiteAccess::TYPE_DEFAULT, []], ["foo", "admin", eZSiteAccess::TYPE_DEFAULT, []], ["admin", "admin", eZSiteAccess::TYPE_HTTP_HOST_URI, ["admin"]], ["admin/", "admin", eZSiteAccess::TYPE_HTTP_HOST_URI, ["admin"]], ["/admin", "admin", eZSiteAccess::TYPE_HTTP_HOST_URI, ["admin"]], ["/admin/", "admin", eZSiteAccess::TYPE_HTTP_HOST_URI, ["admin"]], ["admin/Foo", "admin", eZSiteAccess::TYPE_HTTP_HOST_URI, ["admin"]], ["abc", "abc", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abc"]], ["abcd", "abcd", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcd"]], ["abcde", "abcde", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcde"]], ["abcdef", "abcdef", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdef"]], ["abcdefg", "abcdefg", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdefg"]], ["abc/foo", "abc_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abc", "foo"]], ["abcd/foo", "abcd_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcd", "foo"]], ["abcde/foo", "abcde_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcde", "foo"]], ["abcdef/foo", "abcdef_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdef", "foo"]], ["abcdefg/foo", "abcdefg_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdefg", "foo"]], ["abc/foo/", "abc_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abc", "foo"]], ["/abcd/foo", "abcd_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcd", "foo"]], ["/abcde/foo/", "abcde_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcde", "foo"]], ["abcdef/foo/bar", "abcdef_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdef", "foo"]], ["abcdefg/foo/abc", "abcdefg_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["abcdefg", "foo"]], ["engæ", "eng", eZSiteAccess::TYPE_HTTP_HOST_URI, ["engæ"]], ["engæøå", "admin", eZSiteAccess::TYPE_DEFAULT, []], ["æeng", "admin", eZSiteAccess::TYPE_DEFAULT, []], ["engæ/foo", "eng_foo", eZSiteAccess::TYPE_HTTP_HOST_URI, ["engæ", "foo"]], ["engæ/bar", "eng", eZSiteAccess::TYPE_HTTP_HOST_URI, ["engæ"]], ["engæøå/foo", "admin", eZSiteAccess::TYPE_DEFAULT, []], ["engæ/fooæ", "eng", eZSiteAccess::TYPE_HTTP_HOST_URI, ["engæ"]], ["engæøå/fooæ", "admin", eZSiteAccess::TYPE_DEFAULT, []]];
    }

}
