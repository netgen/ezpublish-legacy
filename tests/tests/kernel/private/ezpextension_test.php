<?php
/**
 * File containing the ezpTopologicalSortTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpExtensionTest extends ezpTestCase
{
    protected $data = [];

    public function setUp()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', 'tests/tests/kernel/classes/extensions' );
        self::clearActiveExtensionsCache();
        parent::setUp();
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        eZCache::clearByID( 'active_extensions' );
        parent::tearDown();
    }

    /**
     * @dataProvider providerForGetLoadingOrderTest
     */
    public function testGetLoadingOrder( $extensionName, $expectedResult )
    {
        $extension = ezpExtension::getInstance( $extensionName );
        $loadingOrder = $extension->getLoadingOrder();
        static::assertSame($expectedResult, $loadingOrder);
    }

    public static function providerForGetLoadingOrderTest()
    {
        return [
            // valid extension.xml
            ['ezfind', ['before' => ['ezwebin', 'ezflow'], 'after' => ['ezjscore']]],
            // only 'requires' dependency
            ['ezdeprequires', ['before' => [], 'after' => ['ezjscore']]],
            // only 'uses' dependency
            ['ezdepuses', ['before' => [], 'after' => ['ezjscore']]],
            // only 'extends' dependency
            ['ezdepextends', ['before' => ['ezwebin', 'ezflow'], 'after' => []]],
            // invalid XML
            ['ezdepinvalid', null],
        ];
    }

    /**
     * @dataProvider providerForGetInfoTest
     */
    public function testGetInfo( $extensionName, $expectedResult )
    {
        $extension = ezpExtension::getInstance( $extensionName );
        $info = $extension->getInfo();
        static::assertSame($expectedResult, $info);
    }

    public static function providerForGetInfoTest()
    {
        $ezInfoNewArray = ['name' => "New eZ Info", 'version' => '2.0', 'copyright' => "Copyright © 2010 eZ Systems AS.", 'license' => "GNU General Public License v2.0", 'info_url' => "http://ez.no", 'Includes the following third-party software' => ['name' => 'Software 1', 'version' => '1.1', 'copyright' => 'Some company.', 'license' => 'Apache License, Version 2.0', 'info_url' => 'http://company.com'], 'Includes the following third-party software (2)' => ['name' => 'Software 2', 'version' => '2.0', 'copyright' => 'Some other company.', 'license' => 'GNU Public license V2.0']];

        $ezInfoOldArray = ['Name' => "Old eZ Info", 'Version' => '1.0', 'Copyright' => "Copyright © 2010 eZ Systems AS.", 'Info_url' => "http://ez.no", 'License' => "GNU General Public License v2.0", 'Includes the following third-party software' => ['name' => 'Software 1', 'Version' => '1.1', 'copyright' => 'Some company.', 'license' => 'Apache License, Version 2.0', 'info_url' => 'http://company.com'], 'Includes the following third-party software (2)' => ['name' => 'Software 2', 'Version' => '2.0', 'copyright' => 'Some other company.', 'license' => 'GNU Public license V2.0']];

        return [
            // valid and complete extension.xml
            ['ezinfonew', $ezInfoNewArray],
            // invalid extension.xml
            ['ezinfoinvalid', null],
            // extension using ezinfo.php
            ['ezinfoold', $ezInfoOldArray],
        ];
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
