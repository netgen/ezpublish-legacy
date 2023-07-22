<?php
/**
 * File containing the eZSearchEngineRegression class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZSearchEngineRegression extends ezpTestCase
{
    private ?\eZSearchEngine $searchEngine = null;

    public function setUp()
    {
        $this->searchEngine = new eZSearchEngine;
    }

    /**
     * Test scenario for issue EZP-19684
     * @dataProvider providerForTestSplitString
     */
    public function testSplitString( $originalString, $convertedString )
    {
        static::assertSame($convertedString, $this->searchEngine->splitString( $originalString ));
    }

    public function providerForTestSplitString()
    {
        return [["", []], ["   ", []], ["L'avertissement", ["L", "avertissement"]], ["L’avertissement", ["L", "avertissement"]], ["Hello world", ["Hello", "world"]], ["  Hello   world  ", ["Hello", "world"]], ["  'Hello''world'  ", ["Hello", "world"]], ["  'Hello'   'world'  ", ["Hello", "world"]], ["  'Hello' ''  'world'  ", ["Hello", "world"]], ['  "Hello""world"  ', ["Hello", "world"]], ['  "Hello"   "world"  ', ["Hello", "world"]], ['  "Hello" ""  "world"  ', ["Hello", "world"]], ['  ‘Hello’   ‘world’  ', ["Hello", "world"]], ['  ‘Hello’ ‘’  ‘world’  ', ["Hello", "world"]], ['  ‟Hello„‟world„  ', ["Hello", "world"]], ['  ‟Hello„   ‟world„  ', ["Hello", "world"]], ['  ‟Hello„ ‟„  ’world„  ', ["Hello", "world"]]];
    }
}
