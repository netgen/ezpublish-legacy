<?php
/**
 * File containing the eZContentObjectStateGroupTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZContentObjectStateGroupTest extends ezpDatabaseTestCase
{
    public function providerCreateWithInvalidIdentifier()
    {
        return [['WithUpperCaseChars'], [str_repeat( 'x', 46 )]];
    }

    /**
     * @dataProvider providerCreateWithInvalidIdentifier
     */
    public function testCreateWithInvalidIdentifier( $identifier )
    {
        $row = ['identifier' => $identifier];
        $stateGroup = new eZContentObjectStateGroup( $row );
        $trans = $stateGroup->translationByLocale( 'eng-GB' );
        $trans->setAttribute( 'name', $identifier );
        $messages = [];
        static::assertFalse($stateGroup->isValid( $messages ), "Invalid state group identifier '$identifier' was accepted");
    }

    public function providerCreateWithvalidIdentifier()
    {
        return [['lowercasechars'], [str_repeat( 'x', 45 )]];
    }

    /**
     * @dataProvider providerCreateWithValidIdentifier
     */
    public function testCreateWithvalidIdentifier( $identifier )
    {
        $row = ['identifier' => $identifier];
        $stateGroup = new eZContentObjectStateGroup( $row );
        $trans = $stateGroup->translationByLocale( 'eng-GB' );
        $trans->setAttribute( 'name', $identifier );
        $messages = [];
        static::assertTrue($stateGroup->isValid( $messages ), "Valid state group identifier '$identifier' was refused, " . var_export( $messages, true ));
    }

    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( "eZContentObjectStateGroupTest" );
    }
}

?>
