<?php
/**
 * File containing the eZXMLInputParserTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZXMLInputParserTest extends ezpTestCase
{
    /**
     * Parser used for the tests.
     *
     * @var eZXMLInputParser
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new eZXMLInputParser;
    }

    /**
     * Test for argument parsing with double quotes
     */
    public function testDoubleQuotedAttributeParsing()
    {
        static::assertEquals(["foo" => "bar"], $this->parser->parseAttributes( 'foo="bar"' ));
    }

    /**
     * Test for argument parsing with single quotes
     */
    public function testSingleQuotedAttributeParsing()
    {
        static::assertEquals(["foo" => "bar"], $this->parser->parseAttributes( "foo='bar'" ));
    }

    /**
     * Test for argument parsing with no quotes
     */
    public function testNoQuoteAttributeParsing()
    {
        static::assertEquals(["foo" => "bar"], $this->parser->parseAttributes( "foo=bar" ));
    }

    /**
     * Test for argument parsing with uppercase name
     */
    public function testUppercaseNameAttributeParsing()
    {
        static::assertEquals(["FOO" => "bar"], $this->parser->parseAttributes( "FOO='bar'" ));
    }

    /**
     * Test for argument parsing with empty values
     *
     * @dataProvider providerForTestEmptyAttributeParsing
     */
    public function testEmptyAttributeParsing( $string )
    {
        static::assertEquals([], $this->parser->parseAttributes( $string ));
    }

    public static function providerForTestEmptyAttributeParsing()
    {
        return [[null], [""], ["foo=''"], ['foo=""'], ['foo="    "'], [' foo="" bar baz']];
    }

    /**
     * Test for argument parsing with various additional spaces
     *
     * @dataProvider providerForTestAdditionalSpacesAttributeParsing
     */
    public function testAdditionalSpacesAttributeParsing( $string )
    {
        static::assertEquals(["foo" => "bar"], $this->parser->parseAttributes( $string ));
    }

    public static function providerForTestAdditionalSpacesAttributeParsing()
    {
        return [
            // Single quotes
            [" foo='bar'"],
            ["foo ='bar'"],
            ["foo= 'bar'"],
            ["foo=' bar'"],
            ["foo='bar '"],
            ["foo='bar' "],
            ["  foo='bar' "],
            ["  foo  =  '  bar  ' "],
            // Single quotes
            [' foo="bar"'],
            ['foo ="bar"'],
            ['foo= "bar"'],
            ['foo=" bar"'],
            ['foo="bar "'],
            ['foo="bar" '],
            ['  foo="bar" '],
            ['  foo  =  "  bar  " '],
            // No quotes
            [' foo=bar'],
            ['foo =bar'],
            ['foo= bar'],
            ['foo=bar '],
            ['  foo=bar '],
            ['  foo  =    bar   '],
        ];
    }

    /**
     * Test for argument parsing with various special characters
     *
     * @dataProvider providerForTestSpecialCharactersAttributeParsing
     */
    public function testSpecialCharactersAttributeParsing( $string, $expected )
    {
        static::assertEquals($expected, $this->parser->parseAttributes( $string ));
    }

    public static function providerForTestSpecialCharactersAttributeParsing()
    {
        return [['héllowørld="héllowørld"', ['héllowørld' => 'héllowørld']], ['2foo="bar"', []], ['foo2="bar"', ['foo2' => 'bar']], ['·foo="bar"', []], ['foo·="foo·"', ['foo·' => 'foo·']], ['Ωþà‿="Ωþà‿"', ['Ωþà‿' => 'Ωþà‿']], ['神="神"', ['神' => '神']], ['الله="الله"', ['الله' => 'الله']]];
    }

    /**
     * Test for argument parsing with many attributes
     *
     * @dataProvider providerForTestManyAttributesParsing
     */
    public function testManyAttributesParsing( $string, $expected )
    {
        static::assertEquals($expected, $this->parser->parseAttributes( $string ));
    }

    public static function providerForTestManyAttributesParsing()
    {
        $result = ['ie' => 'MS9.0', 'firefox' => '16.0.2', 'chrome' => 'Something', 'opera' => 'twelve'];

        return [['ie=MS9.0 firefox=16.0.2 chrome=Something opera=twelve', $result], ['ie="MS9.0" firefox="16.0.2" chrome="Something" opera="twelve"', $result], ['ie=\'MS9.0\' firefox=\'16.0.2\' chrome=\'Something\' opera=\'twelve\'', $result], ['ie=MS9.0 firefox="16.0.2" chrome="Something" opera="twelve"', $result], ['ie=MS9.0 firefox=\'16.0.2\' chrome=\'Something\' opera=\'twelve\'', $result], ['ie="MS9.0" firefox=16.0.2 chrome=\'Something\' opera=twelve', $result]];
    }

    /**
     * Test for argument parsing with empty values
     *
     * @dataProvider providerForTestConvertNumericEntities
     */
    public function testConvertNumericEntities( $string, $expected )
    {
        static::assertEquals($expected, $this->parser->convertNumericEntities( $string ));
    }

    public static function providerForTestConvertNumericEntities()
    {
        $convmap = [0x0, 0x2FFFF, 0, 0xFFFF];

        return [
            // BC values, these were working even before EZP-25243:
            // 1. Nothing to convert here
            [42, 42],
            ['', ''],
            ["Ph'nglui mglw'nafh Cthulhu R'lyeh wgah'nagl fhtagn.", "Ph'nglui mglw'nafh Cthulhu R'lyeh wgah'nagl fhtagn."],
            // 2. Character entity references, should not be touched
            ["Ph&quot;nglui mglw&quot;nafh Cthulhu R&quot;lyeh wgah&quot;nagl fhtagn.", "Ph&quot;nglui mglw&quot;nafh Cthulhu R&quot;lyeh wgah&quot;nagl fhtagn."],
            ["A&amp;B&sect;C&copy;D&auml;E&oslash;&fnof; &Psi;", "A&amp;B&sect;C&copy;D&auml;E&oslash;&fnof; &Psi;"],
            // 3. Numeric character references (decimal)
            ["Ph&#039;nglui mglw&#39;nafh Cthulhu R&#039;lyeh wgah&#0039;nagl fhtagn.", "Ph'nglui mglw'nafh Cthulhu R'lyeh wgah'nagl fhtagn."],
            ["&#38;A&#0228; &#255;Bc&#0042;&#191;-&#0197;/&#185;\&#173;", mb_decode_numericentity( "&#38;A&#0228; &#255;Bc&#0042;&#191;-&#0197;/&#185;\&#173;", $convmap, 'UTF-8' )],
            // These are working only after EZP-25243:
            // 4. Numeric character references (hexadecimal)
            ["I&#xe4;! I&#0xE4;! Cthulhu Fhtagn!", mb_decode_numericentity( "I&#xe4;! I&#0xE4;! Cthulhu Fhtagn!", $convmap, 'UTF-8' )],
            ["snafu &#x1;&#x0F1; &#x004Ef; &#x460; &#xd0B; &#xFaF; &#x;069c", mb_decode_numericentity( "snafu &#x1;&#x0F1; &#x004Ef; &#x460; &#xd0B; &#xFaF; &#x;069c", $convmap, 'UTF-8' )],
        ];
    }
}

?>
