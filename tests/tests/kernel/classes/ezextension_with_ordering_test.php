<?php
/**
 * File containing the eZExtensionWithOrderingTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZExtensionWithOrderingTest extends ezpTestCase
{
    public function setUp()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', 'tests/tests/kernel/classes/extensions/' );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionOrdering', 'enabled' );
        self::clearActiveExtensionsCache();
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        self::clearActiveExtensionsCache();
    }

    public function testUnrelatedKeepOrder1()
    {
        self::setExtensions( ['ezmultiupload', 'ezfind'] );
        static::assertSame(['ezmultiupload', 'ezfind'], eZExtension::activeExtensions());
    }

    public function testUnrelatedKeepOrder2()
    {
        self::setExtensions( ['ezfind', 'ezmultiupload'] );
        static::assertSame(['ezfind', 'ezmultiupload'], eZExtension::activeExtensions());
    }

    public function testSimpleNoReordering()
    {
        self::setExtensions( ['ezjscore', 'ezfind'] );
        static::assertSame(['ezjscore', 'ezfind'], eZExtension::activeExtensions());
    }

    public function testSimpleReordering()
    {
        self::setExtensions( ['ezfind', 'ezjscore'] );
        static::assertSame(['ezjscore', 'ezfind'], eZExtension::activeExtensions());
    }

    public function testSimpleReorderingKeepInitialDummies()
    {
        self::setExtensions( ['dummy1', 'dummy2', 'dummy3', 'ezfind', 'ezjscore'] );
        static::assertSame(['dummy1', 'dummy2', 'dummy3', 'ezjscore', 'ezfind'], eZExtension::activeExtensions());
    }

    public function testComplexReordering()
    {
        $ezwt = null;
        $ezmultiupload = null;
        $ezflow = null;
        $ezfind = null;
        $ezwebin = null;
        $ezjscore = null;
        $ezgmaplocation = null;
        $ezoe = null;
        self::setExtensions( ['ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt'] );
        $activeExtensions = eZExtension::activeExtensions();
        foreach ( ['ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt'] as $extension )
            ${$extension} = array_search( $extension, $activeExtensions );

        static::assertLessThan($ezwt, $ezmultiupload, 'ezwt should have had lower extension position than ezmultiupload');
        static::assertLessThan($ezflow, $ezfind, 'ezflow should have had lower extension position than ezfind');
        static::assertLessThan($ezwebin, $ezfind, 'ezwebin should have had lower extension position than ezfind');
        static::assertLessThan($ezwebin, $ezflow, 'ezwebin should have had lower extension position than ezflow');
        static::assertLessThan($ezwebin, $ezjscore, 'ezwebin should have had lower extension position than ezjscore');
        static::assertLessThan($ezgmaplocation, $ezjscore, 'ezgmaplocation should have had lower extension position than ezjscore');
        static::assertLessThan($ezoe, $ezjscore, 'ezoe should have had lower extension position than ezjscore');
        static::assertLessThan($ezfind, $ezjscore, 'ezfind should have had lower extension position than ezjscore');
    }

    public function testCycleInvolvesNoReordering1()
    {
        self::setExtensions( ['cycle1', 'cycle2'] );
        static::assertSame(['cycle1', 'cycle2'], eZExtension::activeExtensions());
    }

    public function testCycleInvolvesNoReordering2()
    {
        self::setExtensions( ['cycle2', 'cycle1'] );
        static::assertSame(['cycle2', 'cycle1'], eZExtension::activeExtensions());
    }


    /**
     * Sets the active extensions
     *
     * @param string $type ActiveExtensions or ActiveAccessExtensions
     * @param array $extensions Extensions to set as active ones
     */
    private static function setExtensions( $extensions, $type = 'ActiveExtensions' )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', $type, $extensions );
        self::clearActiveExtensionsCache();
    }

    /**
     * @todo Move to a common extension testing class
     */
    private static function clearActiveExtensionsCache()
    {
        eZCache::clearByID( 'active_extensions' );

        // currently required so that cache will actually be considered expired
        // this is a design issue in eZExpiryHandler we need to address soon as it deeply impacts testing any feature
        // that relies on it, and also impacts runtime on high-trafic sites.
        sleep( 2 );
    }
}
?>
