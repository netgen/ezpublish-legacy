<?php
/**
 * File containing the eZNamePatternResolverRegression class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZNamePatternResolverRegression extends ezpTestCase
{
    /**
     * @return array
     */
    public function providerForTestNamePatternResolver()
    {
        return [[
            // test ASCII string split, no trimming
            "This is a Very Long Name isn't it?",
            20,
            "..",
            "<name>",
            "name",
            "This is a Very Lon..",
        ], [
            // test ASCII string split, whitespace trimming
            "This is a Very Long \n\t\r\0Name isn't it?",
            25,
            "..",
            "<name>",
            "name",
            "This is a Very Long..",
        ], [
            // test ASCII string split, comma and dot trimming
            "This is a Very Long,.Name isn't it?",
            22,
            "..",
            "<name>",
            "name",
            "This is a Very Long,..",
        ], [
            // test uft-8 string split, no trimming
            "私は簡単にパブリッシュの記事で使用することができるようなもの、何でも、記述する必要が、それはそう、私はただ何を書き留めて、何を参照してくださいますね、あなたが実際にTIに考えている心の中で何かが出てくるのが難しいのようなものだ登場。ポイントは、私が百五文字が必要だということです。これで十分です。くそー、もっと2",
            31,
            "..",
            "<name>",
            "name",
            "私は簡単にパブリッシュの記事で使用することができるようなも..",
        ], [
            // test uft-8 string split, with ideographic comma trimming
            "私は簡単にパブリッシュの記事で使用することができるようなもの、何でも、記述する必要が、それはそう、私はただ何を書き留めて、何を参照してくださいますね、あなたが実際にTIに考えている心の中で何かが出てくるのが難しいのようなものだ登場。ポイントは、私が百五文字が必要だということです。これで十分です。くそー、もっと2",
            32,
            "..",
            "<name>",
            "name",
            "私は簡単にパブリッシュの記事で使用することができるようなもの..",
        ], [
            // test a string that doesn't need to be modified
            "A string that doesn't need to be altered",
            0,
            "..",
            "<name>",
            "name",
            "A string that doesn't need to be altered",
        ]];
    }

    /**
     * Test to check fix for "object name limit does not support multibyte charset"
     *
     * @link https://jira.ez.no/browse/EZP-21410
     * @dataProvider providerForTestNamePatternResolver
     */
    public function testNamePatternResolver( $name, $limit, $sequence, $namePattern, $identifier, $expects )
    {
        $contentObjectMock = $this->getMock( "eZContentObject", [], [], '', false, false );
        $contentObjectAttributeMock = $this->getMock( "eZContentObjectAttribute", [], [], '', false, false );


        $contentObjectMock
            ->expects( static::once() )
            ->method( "fetchAttributesByIdentifier" )
            ->with( [$identifier], false, [false] )
            ->will( static::returnValue([$contentObjectAttributeMock]) );

        $contentObjectAttributeMock
            ->expects( static::once() )
            ->method( "contentClassAttributeIdentifier" )
            ->will( static::returnValue($identifier) );

        $contentObjectAttributeMock
            ->expects( static::once() )
            ->method( 'title' )
            ->will( static::returnValue($name) );


        $resolver = new eZNamePatternResolver( $namePattern, $contentObjectMock );
        $result = $resolver->resolveNamePattern( $limit, $sequence );

        static::assertEquals($expects, $result);
    }
}
