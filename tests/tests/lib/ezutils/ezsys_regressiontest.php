<?php
/**
 * File containing the eZSysRegressionTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * Main test class for eZSys regression tests
 */
class eZSysRegressionTest extends ezpRegressionTest
{
    /**
     * Setup webdav test siteaccess & fills $this->files with all the
     * .request files found in the regression directory, recursively.
     */
    public function __construct()
    {
        // load tests
        $this->readDirRecursively( __DIR__ . '/server', $this->files, 'php' );

        // call parent (including sorting $this->files as set above)
        parent::__construct();
    }

    /**
     * Called by PHPUnit to create this test suite.
     */
    public static function suite()
    {
        return new ezpTestRegressionSuite( self::class );
    }

    /**
     * Called by PHPUnit before each test, bakup eZSys instance
     */
    public function setUp()
    {
        parent::setUp();
        $this->eZSysInstanceBackup = eZSys::instance();
    }

    /**
     * Called by PHPUnit after each test, correct eZSys instance
     */
    public function tearDown()
    {
        eZSys::setInstance( $this->eZSysInstanceBackup );
        parent::tearDown();
    }

    /**
     * Skips the test $file if the directory name is uncommented in $skipTests
     * inside the function.
     *
     * @param string $file
     */
    protected function skip( $file )
    {
        // Uncomment the tests that you want to skip,  name = > pattern
        $skipTests = [];

        foreach ( $skipTests as $testName => $testPattern )
        {
            if ( str_contains( $file, $testPattern ) )
            {
                static::markTestSkipped("Test environment is configured to skip {$testName} tests.");
                return true;
            }
        }
        return false;
    }

    /**
     * Runs the $file (.request file from $this->files) as a PHPUnit test.
     *
     * Steps performed:
     *  - setUp() is called automatically before this function
     *  - skip the test $file if declared. See {@link skip()}
     *  - load data from file, create eZSys instance and run init
     *  - check that misc os / vh variables where as expected
     *  - tearDown() is called automatically after this function
     *
     * @param string $file
     */
    protected function testRunRegression( $file )
    {
        if ( $this->skip( $file ) )
            return;

        $testData = include $file;
        $instance = new eZSys( $testData );
        eZSys::setInstance( $instance );
        eZSys::init();// 'index.php', strpos( $file, 'server/vh/' ) !== false );

        // OS tests
        if ( $testData['PHP_OS'] === 'WINNT' )
        {
            $os = 'Windows';
            static::assertEquals("win32", $instance->OSType, "Did not get correct $os 'OSType' value");
            static::assertEquals("windows", $instance->OS, "Did not get correct $os 'OS' value");
            static::assertEquals("win32", $instance->FileSystemType, "Did not get correct $os 'FileSystemType' value");
            static::assertEquals("\\", $instance->FileSeparator, "Did not get correct $os 'FileSeparator' value");
            static::assertEquals("\r\n", $instance->LineSeparator, "Did not get correct $os 'LineSeparator' value");
            static::assertEquals(";", $instance->EnvSeparator, "Did not get correct $os 'EnvSeparator' value");
            static::assertEquals('"', $instance->ShellEscapeCharacter, "Did not get correct $os 'ShellEscapeCharacter' value");
            static::assertEquals('.bak', $instance->BackupFilename, "Did not get correct $os 'BackupFilename' value");
        }
        else // unix (incl Darwin)
        {
            $os = 'Unix';
            static::assertEquals("unix", $instance->OSType, "Did not get correct $os 'OSType' value");

            if ( $testData['PHP_OS'] === 'Linux' )
                static::assertEquals("linux", $instance->OS, "Did not get correct $os 'OS' value");
            else if (  $testData['PHP_OS'] === 'FreeBSD' )
                static::assertEquals("freebsd", $instance->OS, "Did not get correct $os 'OS' value");
            else if (  $testData['PHP_OS'] === 'Darwin' )
                static::assertEquals("darwin", $instance->OS, "Did not get correct $os 'OS' value");
            else
                static::assertEquals(false, $instance->OS, "Did not get correct $os 'OS' value");

            static::assertEquals("unix", $instance->FileSystemType, "Did not get correct $os 'FileSystemType' value");
            static::assertEquals("/", $instance->FileSeparator, "Did not get correct $os 'FileSeparator' value");
            static::assertEquals("\n", $instance->LineSeparator, "Did not get correct $os 'LineSeparator' value");
            static::assertEquals(":", $instance->EnvSeparator, "Did not get correct $os 'EnvSeparator' value");
            static::assertEquals("'", $instance->ShellEscapeCharacter, "Did not get correct $os 'ShellEscapeCharacter' value");
            static::assertEquals('~', $instance->BackupFilename, "Did not get correct $os 'BackupFilename' value");
        }

        // Uri test: vh / nvh part
        if ( strpos( $file, 'server/nvh/' ) )
            $expected = '/index.php';
        else
            $expected = '';

        static::assertEquals($expected, $instance->IndexFile, "The IndexFile was not expected value");


        // Uri test: sub path part
        if ( isset( $testData['__out']['WWWDir'] ) )
            $wwwDir = $testData['__out']['WWWDir'];
        elseif ( str_contains( (string) $testData['_SERVER']['SCRIPT_NAME'], 'index.php' ) )// .htaccess or nvh
            $wwwDir = rtrim( str_replace( 'index.php', '', (string) $testData['_SERVER']['SCRIPT_NAME'] ), '\/' );
        else
            $wwwDir = '';

        static::assertEquals($wwwDir, $instance->WWWDir, "The WWWDir was not expected value");
        static::assertEquals(rtrim( str_replace( 'index.php', '', (string) $testData['_SERVER']['SCRIPT_FILENAME'] ), '\/' ) . '/', $instance->SiteDir, "The SiteDir was not expected value");


        // Uri test: uri part
        if ( isset( $testData['__out']['RequestURI'] ) )
            $expected = $testData['__out']['RequestURI'];
        else if ( strpos( $file, 'vh/utf8' ) )
            $expected = '/News/Blåbær-Øl-med-d\'or-新闻军事社会体育中超';
        elseif ( strpos( $file, 'vh/view' ) )
            $expected = '/content/view/full/44';
        else
            $expected = '';

        static::assertEquals($expected, $instance->RequestURI, "The RequestURI was not expected value");
    }
}
?>
