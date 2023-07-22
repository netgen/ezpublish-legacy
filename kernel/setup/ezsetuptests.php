<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// This file holds the test functions that are used by step 1

define( 'EZ_SETUP_TEST_SUCCESS', 1 );
define( 'EZ_SETUP_TEST_FAILURE', 2 );

function eZSetupTestTable()
{
    return ['phpversion' => ['eZSetupTestPhpVersion'], 'variables_order' => ['eZSetupTestVariablesOrder'], 'php_session' => ['eZSetupTestExtension'], 'directory_permissions' => ['eZSetupTestDirectoryPermissions'], 'settings_permission' => ['eZSetupTestFilePermissions'], 'database_extensions' => ['eZSetupTestExtension'], 'database_all_extensions' => ['eZSetupTestExtension'], 'php_magicquotes' => ['eZSetupCheckMagicQuotes'], 'magic_quotes_runtime' => ['eZSetupCheckMagicQuotesRuntime'], 'php_register_globals' => ['eZSetupCheckRegisterGlobals'], 'mbstring_extension' => ['eZSetupMBStringExtension'], 'curl_extension' => ['eZSetupTestExtension'], 'zlib_extension' => ['eZSetupTestExtension'], 'dom_extension' => ['eZSetupTestExtension'], 'iconv_extension' => ['eZSetupTestExtension'], 'intl_extension' => ['eZSetupTestExtension'], 'xsl_extension' => ['eZSetupTestExtension'], 'file_upload' => ['eZSetupTestFileUpload'], 'open_basedir' => ['eZSetupTestOpenBasedir'], 'safe_mode' => ['eZSetupTestSafeMode'], 'image_conversion' => ['eZSetupCheckTestFunctions'], 'imagegd_extension' => ['eZSetupCheckGDVersion'], 'texttoimage_functions' => ['eZSetupTestFunctionExists'], 'imagemagick_program' => ['eZSetupCheckExecutable'], 'memory_limit' => ['eZSetupTestMemLimit'], 'execution_time' => ['eZSetupTestExecutionTime'], 'allow_url_fopen' => ['eZSetupTestAllowURLFOpen'], 'timezone' => ['eZSetupTestTimeZone'], 'ezcversion' => ['eZSetupTestComponentsVersion']];
}

function eZSetupConfigVariable( $type, $name )
{
    $config = eZINI::instance( 'setup.ini' );
    return ( $config->hasVariable( $type, $name ) ) ? $config->variable( $type, $name ) : false;
}

function eZSetupImageConfigVariableArray( $type, $name )
{
    $config = eZINI::instance( 'image.ini' );
    return ( $config->hasVariable( $type, $name ) ) ? $config->variableArray( $type, $name ) : false;
}

function eZSetupConfigVariableArray( $type, $name )
{
    $config = eZINI::instance( 'setup.ini' );
    return ( $config->hasVariable( $type, $name ) ) ? $config->variableArray( $type, $name ) : false;
}

function eZSetupRunTests( $testList, $client, &$givenPersistentList )
{
    eZSetupPrvtExtractExtraPaths( $givenPersistentList );

    $testTable = eZSetupTestTable();

    $testResults = [];
    $persistenceResults = [];
    $testResult = EZ_SETUP_TEST_SUCCESS;
    $successCount = 0;
    $http = eZHTTPTool::instance();
    foreach ( $testList as $testItem )
    {
        $testName = $testItem;
        $testElement = [];
        $testElement[0] = EZ_SETUP_TEST_FAILURE;
        if ( !isset( $testTable[$testItem] ) )
        {
            eZDebug::writeError( "The setup test '$testName' is not defined", $client );
            continue;
        }
        if ( $http->hasPostVariable( $testItem . '_Ignore' ) and
             $http->postVariable( $testItem . '_Ignore' ) != 0 )
        {
            continue;
        }
        $testInfo = $testTable[$testItem];
        $testFunction = $testInfo[0];
        if ( !function_exists( $testFunction ) )
            continue;
        $testResultArray = $testFunction( $testName );
        if ( $testResultArray['result'] )
        {
            $testElement[0] = EZ_SETUP_TEST_SUCCESS;
            ++$successCount;
        }
        else
            $testResult = EZ_SETUP_TEST_FAILURE;
        if ( isset( $testResultArray['persistent_data'] ) )
        {
            $persistenceResults[] = [$testName, $testResultArray['persistent_data']];
        }
        else if ( isset( $testResultArray['persistence_list'] ) )
        {
            $persistenceResults = array_merge( $persistenceResults, $testResultArray['persistence_list'] );
        }
        $testElement[1] = $testName;
        $testElement[2] = $testResultArray;
        $testResults[] = $testElement;
    }
    return ['result' => $testResult, 'results' => $testResults, 'persistence_list' => $persistenceResults, 'success_count' => $successCount];
}

function eZSetupCheckTestFunctions( $type )
{
    $extensionList = eZSetupConfigVariableArray( $type, 'Extensions' );
    $testList = eZSetupConfigVariableArray( $type, 'TestList' );
    $requireType = eZSetupConfigVariable( $type, 'Require' );

    $dummy = null;
    $runResult = eZSetupRunTests( $testList, 'eZSetupCheckTestFunctions', $dummy );
    $testResults = $runResult['results'];
    $testResult = $runResult['result'];
    $successCount = $runResult['success_count'];
    $persistenceData = $runResult['persistence_list'];

    $result = true;
    if ( $requireType == 'one' )
    {
        if ( $successCount == 0 )
            $result = false;
    }
    else if ( is_array( $extensionList ) && $successCount < count( $extensionList ) )
    {
        $result = false;
    }

    return ['result' => $result, 'persistence_list' => $persistenceData, 'test_results' => $testResults];
}

function eZSetupTestFileUpload( $type )
{
    $uploadEnabled = ini_get( 'file_uploads' ) != 0;
    $uploadDir = ini_get( 'upload_tmp_dir' );
    $uploadDirExists = true;
    $uploadDirWriteable = true;
    $uploadDirCreateFile = true;
    $uploadIsRoot = false;
    // Empty upload_tmp_dir variable means that the system
    // default is used. However the system default variable is hidden
    // from PHP code and must be guessed.
    // See: http://www.php.net/manual/en/ini.sect.file-uploads.php#ini.upload-tmp-dir
    if ( strlen( trim( $uploadDir ) ) == 0 )
    {
        $osType = eZSys::osType();
        if ( $osType == 'win32' )
        {
            // Windows machines use the TEMP and TMP env variable.
            // TEMP is checked first.
            $uploadDir = eZSys::hasEnvironmentVariable( 'TEMP' ) ? eZSys::environmentVariable( 'TEMP' ) : '';
            if ( $uploadDir === '' )
            {
                $uploadDir = eZSys::hasEnvironmentVariable( 'TMP' ) ? eZSys::environmentVariable( 'TMP' ) : '';
            }
            // When TEMP/TMP is not set we have to guess the directory
            // The only valid guess is %SYSTEMROOT%/TEMP
            // If %SYSTEMROOT% is missing we keep the string empty
            if ( $uploadDir === '' )
            {
                if ( eZSys::hasEnvironmentVariable( 'SYSTEMROOT' ) )
                {
                    $uploadDir = eZSys::environmentVariable( 'SYSTEMROOT' ) . '/TEMP';
                }
            }
        }
        else if ( $osType == 'unix' or
                  $osType == 'mac' )
        {
            $uploadDir = eZSys::hasEnvironmentVariable( 'TMPDIR' ) ? eZSys::environmentVariable( 'TMPDIR' ) : '';
            // When TMPDIR is not set we have to guess the directory
            // On Unix systems we expect /tmp to be used
            if ( strlen( $uploadDir ) == 0 )
            {
                $uploadDir = '/tmp';
            }
        }
    }
    $uploadDirs = [];
    if ( strlen( $uploadDir ) > 0 )
    {
        $uploadDirExists = file_exists( $uploadDir );
        $uploadDirWriteable = eZDir::isWriteable( $uploadDir );
        if ( $uploadDirExists and $uploadDirWriteable )
        {
            $uploadDirCreateFile = false;
            $tmpFile = 'ezsetuptmp_' . md5( microtime() ) . '.tmp';
            $tmpFilePath = $uploadDir . '/' . $tmpFile;
            if ( $fd = @fopen( $tmpFilePath, 'w' ) )
            {
                $uploadDirCreateFile = true;
                @fclose( $fd );
                unlink( $tmpFilePath );
            }
        }
        $splitDirs = explode( '/', trim( $uploadDir, '/' ) );
        $dirPath = '';
        foreach ( $splitDirs as $splitDir )
        {
            $dirPath .= '/' . $splitDir;
            $uploadDirs[] = $dirPath;
        }
        if ( str_starts_with($uploadDir, '/root') )
        {
            $uploadIsRoot = true;
        }
    }
    $result = ( $uploadEnabled and $uploadDirExists and
                $uploadDirWriteable and $uploadDirCreateFile );

    $userInfo = eZSetupPrvPosixExtension();
    return ['result' => $result, 'php_upload_is_enabled' => $uploadEnabled, 'php_upload_is_root' => $uploadIsRoot, 'php_upload_dir' => $uploadDir, 'php_upload_split_dirs' => $uploadDirs, 'upload_dir_exists' => $uploadDirExists, 'upload_dir_writeable' => $uploadDirWriteable, 'upload_dir_create_file' => $uploadDirCreateFile, 'user_info' => $userInfo, 'persistent_data' => ['result' => ['value' => $result]]];
}

function eZSetupCheckMagicQuotesRuntime( $type )
{
    if ( version_compare( PHP_VERSION, '7.4' ) >= 0 ) {
        $magicQuote = 0;
    } else {
        $magicQuote = get_magic_quotes_runtime();
    }
    $result = ( $magicQuote == 0 );
    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result]]];
}

function eZSetupCheckMagicQuotes( $type )
{
    if ( version_compare( PHP_VERSION, '7.4' ) >= 0 ) {
        $magicQuote = 0;
    } else {
        $magicQuote = get_magic_quotes_gpc();
    }

    $result = ( $magicQuote == 0 );
    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result]]];
}

/*!
    Test if eZ Components version is greater than required version
    This is currently done by checking existence of class and function
    since ezc does not have an api to get version information atm.
*/
function eZSetupTestComponentsVersion( $type )
{
    $minVersion = eZSetupConfigVariable( $type, 'MinimumVersion' );
    $testClass = eZSetupConfigVariable( $type, 'TestClass' );
    $testFunction = eZSetupConfigVariable( $type, 'TestFunction' );
    $result = true;
    $ezcExists = true;

    if ( $testClass && class_exists( $testClass ) )
    {
        if ( $testFunction && !is_callable( [$testClass, $testFunction] ) )
        {
            $result = false;
        }
    }
    else if ( $testClass )
    {
        $result = false;
        $ezcExists = false;
    }

    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result], 'required' => ['value' => $minVersion]], 'needed_version' => $minVersion, 'class_exists' => !$ezcExists];
}


/*!
    Test if PHP version is equal or greater than required version
*/
function eZSetupTestPhpVersion( $type )
{
    $minVersion = eZSetupConfigVariable( $type, 'MinimumVersion' );
    $unstableVersionArray = eZSetupConfigVariableArray( $type, 'UnstableVersions' );

    /*
     // Get the operating systems name
    $operatingSystem = explode( ' ', php_uname() );
    $operatingSystem = strtolower( $operatingSystem[0] );

    // Find out if there is an os specific version needed
    if ( isset( $argArray["req"][$operatingSystem] ) )
        $neededVersion = $argArray["req"][$operatingSystem];
    else if ( isset( $argArray["req"] ) )
        $neededVersion = $argArray["req"];
    else
        $neededVersion = $argArray["req"];
    */

    $neededVersion = $minVersion;

    // compare the versions
    $currentVersion = phpversion();
    $currentVersionArray = explode( '.', $currentVersion );
    $neededVersionArray = explode( '.', (string) $neededVersion );

    $warningVersion = false;
    $result = eZSetupPrvtVersionCompare( $currentVersionArray, $neededVersionArray ) >= 0;

    if ( $result )
    {
        foreach ( array_keys( $unstableVersionArray ) as $key )
        {
            $unstableVersion = explode( '.', (string) $unstableVersionArray[$key] );
            if ( eZSetupPrvtVersionCompare( $currentVersionArray, $unstableVersion ) == 0 )
            {
                $result = false;
                $warningVersion = true;
                break;
            }
        }
    }

    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result], 'found' => ['value' => $currentVersion], 'required' => ['value' => $neededVersion]], 'needed_version' => $neededVersion, 'current_version' => $currentVersion, 'warning_version' => $warningVersion];
}

function eZSetupTestVariablesOrder( $type )
{
    $variablesOrder = ini_get( 'variables_order' );
    $result = str_contains( $variablesOrder, 'E' );
    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result], 'found' => ['value' => $variablesOrder]]];
}

/*!
  Test if allowed to open URLs using fopen
*/
function eZSetupTestAllowURLFOpen( $type )
{
    $allowFOpen = ini_get( 'allow_url_fopen' ) != 0;
    return ['result' => $allowFOpen, 'persistent_data' => ['result' => ['value' => $allowFOpen]]];
}

function eZSetupTestFunctionExists( $type )
{
    $functionList = eZSetupConfigVariableArray( $type, 'Functions' );
    $requireType = eZSetupConfigVariable( $type, 'Require' );
    $foundFunctions = [];
    $failedFunctions = [];
    foreach ( $functionList as $function )
    {
        $function = strtolower( (string) $function );
        if ( function_exists( $function ) )
        {
            $foundFunctions[] = $function;
        }
        else
        {
            $failedFunctions[] = $function;
        }
    }
    $result = true;
    if ( $requireType == 'one' )
    {
        if ( count( $foundFunctions ) == 0 )
            $result = false;
    }
    else if ( count( $foundFunctions ) < (is_countable($functionList) ? count( $functionList ) : 0) )
        $result = false;

    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result], 'found' => ['value' => $foundFunctions, 'merge' => false, 'unique' => true], 'checked' => ['value' => $functionList, 'merge' => true, 'unique' => true]], 'require_type' => $requireType, 'extension_list' => $functionList, 'failed_extensions' => $failedFunctions, 'found_extensions' => $foundFunctions];
}

/*!
    Test if the extensios are loaded
*/
function eZSetupTestExtension( $type )
{
    $extensionList = eZSetupConfigVariableArray( $type, 'Extensions' );
    $requireType = eZSetupConfigVariable( $type, 'Require' );
    $foundExtensions = [];
    $failedExtensions = [];
    foreach ( $extensionList as $extension )
    {
        if ( extension_loaded( $extension ) )
        {
            $foundExtensions[] = $extension;
        }
        else
        {
            $failedExtensions[] = $extension;
        }
    }
    $result = true;
    if ( $requireType == 'one' )
    {
        if ( count( $foundExtensions ) == 0 )
            $result = false;
    }
    else if ( count( $foundExtensions ) < (is_countable($extensionList) ? count( $extensionList ) : 0) )
        $result = false;

    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result], 'found' => ['value' => $foundExtensions, 'merge' => false, 'unique' => true], 'checked' => ['value' => $extensionList, 'merge' => true, 'unique' => true]], 'require_type' => $requireType, 'extension_list' => $extensionList, 'failed_extensions' => $failedExtensions, 'found_extensions' => $foundExtensions];
}


/*!
    Test file permissions
*/

function eZSetupTestDirectoryPermissions( $type )
{
    $dirList = eZSetupConfigVariableArray( $type, 'CheckList' );

    $ini = eZINI::instance();
    $dirPermission = $ini->variable( 'FileSettings', 'StorageDirPermissions' );

    $result = true;
    $resultElements = [];
    $resultElementsByErrorCode = [];

    $rootDir = eZSys::rootDir();
    $dirPermOctal = octdec( (string) $dirPermission );

    foreach ( $dirList as $dir )
    {
        if ( empty( $dir ) )
            continue;

        $resultElement = [];
        $resultElement['file']       = $dir;
        $resultElement['result']     = 1; // ok by default
        $resultElement['permission'] = false;

        $dir = eZDir::cleanPath( $dir );

        if ( !file_exists( $dir ) )
        {
            // if directory does not exist then try to create it
            if ( empty( $rootDir ) )
                $dirPath = './' . $dir;
            else
                $dirPath = $rootDir . '/' . $dir;
            $res = eZDir::mkdir( $dirPath, $dirPermOctal );
            if ( $res )
            {
                $resultElement['permission'] = $dirPermission;
                $resultElement['result'] = 1;
            }
            else
            {
                $result = false;
                $resultElement['result'] = 2; // unable to create unexistent dir
            }
        }
        else if ( is_dir( $dir ) )
        {
            $resultElement['permission'] = $dirPermission;
            if ( !eZSetupPrvtAreDirAndFilesWritable( $dir ) )
            {
                $result = false;
                $resultElement['result'] = 3; // dir has wrong permissions
            }
        }
        else if ( is_file( $dir ) )
        {
            $result = false;
            $resultElement['result'] = 4; // dir exists but it is a file
        }
        $resultElements[] = $resultElement;
        $resultElementsByErrorCode[ $resultElement['result'] ][] = $resultElement;
    }
    $safeMode = ini_get( 'safe_mode' ) != 0;
    $userInfo = eZSetupPrvPosixExtension();

    return ['result'          => $result, 'safe_mode'       => $safeMode, 'user_info'       => $userInfo, 'persistent_data' => ['result' => ['value' => $result]], 'current_path'    => realpath( '.' ), 'result_elements' => $resultElements, 'result_elements_by_error_code' => $resultElementsByErrorCode];
}

function eZSetupTestFilePermissions( $type )
{
    $fileList = eZSetupConfigVariableArray( $type, 'CheckList' );
    $ini = eZINI::instance();
    $dirPermission = $ini->variable( 'FileSettings', 'StorageDirPermissions' );
    $filePermission = $ini->variable( 'FileSettings', 'StorageFilePermissions' );

    $result = true;
    $resultElements = [];
    foreach ( $fileList as $file )
    {
        if ( empty( $file ) )
            continue;

        $resultElement = [];
        $resultElement['file'] = $file;
        $resultElements[] = $resultElement;

        $file = eZDir::cleanPath( $file );
        if ( !file_exists( $file ) )
        {
            continue;
        }
        if ( is_dir( $file ) )
        {
            $resultElement['permission'] = $dirPermission;
            $dir = $file;

            if ( !eZSetupPrvtAreDirAndFilesWritable( $dir ) )
            {
                $result     = false;
                $resultElement['result'] = false;
            }
        }
        else if ( is_file( $file ) )
        {
            $resultElement['permission'] = $filePermission;

            if ( !eZFile::isWriteable( $file ) )
            {
                $result = false;
                $resultElement['result'] = false;
            }
        }
    }
    $safeMode = ini_get( 'safe_mode' ) != 0;
    $userInfo = eZSetupPrvPosixExtension();

    return ['result' => $result, 'safe_mode' => $safeMode, 'user_info' => $userInfo, 'persistent_data' => ['result' => ['value' => $result]], 'current_path' => realpath( '.' ), 'result_elements'   => $resultElements];
}

/*!
  Figures out current user and group running the system by
  using the \c posix extension. If this is not available
  \c has_extension is set to \c false.
  \return An array with information, if no extension is found only \c has_extension is set.
*/
function eZSetupPrvPosixExtension()
{
    $userInfo = ['has_extension' => false];
    if ( extension_loaded( 'posix' ) )
    {
        $userInfo['has_extension'] = true;
        $uinfo = posix_getpwuid( posix_getuid() );
        $ginfo = posix_getgrgid( posix_getgid() );
        $userInfo['user_name'] = $uinfo['name'];
        $userInfo['user_id'] = $uinfo['uid'];
        $userInfo['group_name'] = $ginfo['name'];
        $userInfo['group_id'] = $ginfo['gid'];
        $userInfo['group_members'] = $ginfo['members'];
        $userInfo['script_user_id'] = getmyuid();
        $userInfo['script_group_id'] = getmygid();
    }
    return $userInfo;
}


/*!
    Test if a program can be found in our path and is executable
*/
function eZSetupCheckExecutable( $type )
{
    $http = eZHTTPTool::instance();

    $filesystemType = eZSys::filesystemType();
    $envSeparator = eZSys::envSeparator();
    $programs = eZSetupConfigVariableArray( $type, $filesystemType . '_Executable' );
    $systemSearchPaths = explode( $envSeparator, (string) eZSys::path( true ) );
    $additionalSearchPaths = eZSetupConfigVariableArray( $type, $filesystemType . '_SearchPaths' );
    $excludePaths = eZSetupConfigVariableArray( $type, $filesystemType . '_ExcludePaths' );
    $imageIniPath = eZSetupImageConfigVariableArray( 'ShellSettings', 'ConvertPath' );

    /*
     We save once entered extra path in the persistent data list
     to keep it within setup steps.

     This trick is needed, for example, in "registration" step,
     where user has no chance to enter extra path again
     due to missing input field for this purpose.
     */

    // compute extra path
    $extraPath = [];
    if ( $http->hasPostVariable( $type . '_ExtraPath' ) )
    {
        $GLOBALS['eZSetupCheckExecutable_'.$type.'_ExtraPath'] = $http->postVariable( $type . '_ExtraPath' );
        $extraPath = explode( $envSeparator, (string) $http->postVariable( $type . '_ExtraPath' ) );
    }
    else if ( isset( $GLOBALS['eZSetupCheckExecutable_'.$type.'_ExtraPath'] ) )
        $extraPath = explode( $envSeparator, (string) $GLOBALS['eZSetupCheckExecutable_'.$type.'_ExtraPath'] );

    // if extra path was given in any way
    if ( $extraPath )
    {
        // remove program from path name if entered
        foreach ( $extraPath as $path )
        {
            foreach ( $programs as $program )
            {
                if ( strpos( $path, (string) $program) == strlen( $path ) - strlen( (string) $program ) )
                {
                    $extraPath[] = substr( $path, strpos( $path, (string) $program) );
                }
            }
        }
    }

    $searchPaths = array_merge( $systemSearchPaths, $additionalSearchPaths, $extraPath, $imageIniPath );

    $result = false;
    $correctPath = false;
    $correctProgram = false;
    foreach ( $programs as $program )
    {
        foreach( $searchPaths as $path )
        {
            $pathProgram = eZDir::path( [$path, $program] );
            if ( file_exists( $pathProgram ) )
            {
                if ( $filesystemType == 'unix' )
                {
                    $relativePath = $path;
                    if ( preg_match( "#^/(.+)$#", (string) $path, $matches ) )
                        $relativePath = $matches[1];
                    $relativePath = eZDir::cleanPath( $relativePath );
                }
                else // windows
                {
                    $relativePath = $path;
                    if ( preg_match( "#^[a-zA-Z]:[/\\\\](.+)$#", (string) $path, $matches ) )
                        $relativePath = $matches[1];
                    $relativePath = eZDir::cleanPath( $relativePath );
                }
                $exclude = false;
                foreach ( $excludePaths as $excludePath )
                {
                    $excludePath = strtolower( (string) $excludePath );
                    $match = strtolower( $program . "@" . $relativePath );
                    if ( $match == $excludePath )
                    {
                        $exclude = true;
                        break;
                    }
                    else if ( $relativePath == $excludePath )
                    {
                        $exclude = true;
                        break;
                    }
                }
                if ( $exclude )
                    continue;
                if ( function_exists( "is_executable" ) )
                {
                    if ( is_executable( $pathProgram ) )
                    {
                        $result = true;
                        $correctPath = $path;
                        $correctProgram = $program;
                        break;
                    }
                }
                else
                {
                    // Windows system
                    $result = true;
                    $correctPath = $path;
                    $correctProgram = $program;
                    break;
                }
            }
        }
        if ( $result )
            break;
    }

    $extraPathAsString = implode( $envSeparator, $extraPath );

    return ['result' => $result, 'persistent_data' => ['path' => ['value' => $correctPath], 'program' => ['value' => $correctProgram], 'extra_path' => ['value' => $extraPathAsString, 'merge' => TRUE], 'result' => ['value' => $result]], 'env_separator' => $envSeparator, 'filesystem_type' => $filesystemType, 'extra_path' => $extraPath, 'correct_path' => $correctPath, 'system_search_path' => $systemSearchPaths, 'additional_search_path' => $additionalSearchPaths];
}



/*!
    Test php ini settings
*/
function testPHPIni( $parameters )
{
    $setting = $parameters["setting"];
    $state = $parameters["state"];

    if ( (bool) ini_get( $setting ) == $state )
        $pass = true;
    else
        $pass = false;

    $status = $pass;
    return ["status" => $status, "pass" => $pass];
}


/*!
  Test GD version
*/
function eZSetupCheckGDVersion( $type )
{
    $result = function_exists( 'imagegd2' );
    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result]]];
}

/*!
    Test if mbstring is available
*/
function eZSetupMBStringExtension( $type )
{
    $result = eZMBStringMapper::hasMBStringExtension();
    $charsetList = eZMBStringMapper::charsetList();
    return ['result' => $result, 'persistent_data' => ['result' => ['value' => $result]], 'charset_list' => $charsetList];
}


function eZSetupCheckRegisterGlobals( $type )
{
    $registerGlobals = ini_get( 'register_globals' ) != 0;
    $result = !$registerGlobals;
    return ['result' => $result, 'persistent_data' => []];
}

/*!
 Check the php.ini file to get timeout limit
*/
function eZSetupTestExecutionTime( $type )
{
    $minExecutionTime = eZSetupConfigVariable( $type, 'MinExecutionTime' );
    $execTimeLimit = ini_get( 'max_execution_time' );

    if ( $execTimeLimit == false )
    {
        return ['result' => true, 'persistent_data' => ['result' => ['value' => true]]];
    }

    if ( $minExecutionTime <= $execTimeLimit )
        return ['result' => true, 'persistent_data' => ['result' => ['value' => true]]];

    return ['result' => false, 'persistent_data' => ['result' => ['value' => false]], 'required_execution_time' => $minExecutionTime, 'current_execution_time' => $execTimeLimit];
}

/*!
 Checks the php.ini file to see if the memory limit is set high enough
*/
function eZSetupTestMemLimit( $type )
{
    $minMemory = eZSetupConfigVariable( $type, 'MinMemoryLimit' );
    $memoryLimit = ini_get( 'memory_limit' );
    if ( $memoryLimit === '' || $memoryLimit == -1 )
    {
        return ['result' => true, 'persistent_data' => ['result' => ['value' => true]]];
    }

    $byteMinMem = intval( $minMemory );
    switch ( $minMemory[strlen( (string) $minMemory ) - 1] )
    {
        case 'G':
            $byteMinMem *= 1024;
        case 'M':
            $byteMinMem *= 1024;
        case 'K':
            $byteMinMem *= 1024;
    }

    $byteMemLimit = intval( $memoryLimit );
    switch ( $memoryLimit[strlen( $memoryLimit ) - 1] )
    {
        case 'G':
            $byteMemLimit *= 1024;
        case 'M':
            $byteMemLimit *= 1024;
        case 'K':
            $byteMemLimit *= 1024;
    }

    if ( $byteMinMem <= $byteMemLimit )
        return ['result' => true, 'persistent_data' => ['result' => ['value' => true]]];

    return ['result' => false, 'persistent_data' => ['result' => ['value' => false]], 'required_memory' => $minMemory, 'current_memory' => $memoryLimit];
}

function eZSetupTestOpenBasedir( $type )
{
    $openBasedir = ini_get( 'open_basedir' );
    $returnData = ['result' => true, 'persistent_data' => []];
    if ( $openBasedir != '' and
         $openBasedir != '.' )
    {
        $returnData['result'] = false;
        $returnData['warnings'] = [['name' => 'open_basedir', 'text' => ['open_basedir is in use and can give problems running eZ Publish due to bugs in some PHP versions.', 'It\'s recommended that it is turned off if you experience problems running eZ Publish.']]];
    }
    return $returnData;
}

/*!
 Check if setup is installed using windows or linux installer

 \return 'linux' if using linux installer,
         'windows' if using windows installer,
         false if not using any installer
*/
function eZSetupTestInstaller()
{
    if ( file_exists( '.linux' ) )
    {
        return 'linux';
    }
    else if ( file_exists( '.windows' ) )
    {
        return 'windows';
    }
    return false;
}

function eZSetupTestSafeMode( $type )
{
    $safeMode = ini_get( 'safe_mode' ) != 0;
    $result = !$safeMode;
    return ['result' => $result, 'current_path' => realpath( '.' ), 'persistent_data' => []];
}

/*!
 Check if two version arrays are equel, greater or less than each other

 \param user first version array
 \param second version array

 \return < 0 if 1. version less than 2. version
           0 if versions are equal
         > 0 if 1. version is greater than 2. version
*/
function eZSetupPrvtVersionCompare( $versionArray1, $versionArray2 )
{
    $equal = false;
    $count = min( is_countable($versionArray1) ? count( $versionArray1 ) : 0, is_countable($versionArray2) ? count( $versionArray2 ) : 0 );
    for ( $i = 0; $i < $count; ++$i )
    {
        $equal = false;
        if ( (int) $versionArray1[$i] > (int) $versionArray2[$i] )
        {
            return 1;
        }
        else if ( (int) $versionArray1[$i] < (int) $versionArray2[$i] )
        {
            return -1;
        }
        $equal = true;
    }
    if ( $equal )
        return 0;
}


/* Find previously saved extra paths and export them
 * to global variables.
 */
function eZSetupPrvtExtractExtraPaths( &$givenPersistentList )
{
    if( !$givenPersistentList ) // null or empty array
        return;

    foreach( $givenPersistentList as $key => $val )
    {
        if( isset( $val['extra_path'] ) )
            $GLOBALS['eZSetupCheckExecutable_'.$key.'_ExtraPath'] = $val['extra_path'];
    }
}

/*! Check if a given directory and all files within that directory
 * are writable
 */
function eZSetupPrvtAreDirAndFilesWritable( $dir )
{
    if ( !eZDir::isWriteable( $dir ) )
        return FALSE;

   // Check if all files within a given directory are writeable
   $files = eZDir::findSubitems( $dir, 'f' ); // find only files, skip dirs and symlinks
   $fileSeparator = eZSys::fileSeparator();

   foreach ( $files as $file )
   {
       if ( !eZFile::isWriteable( $dir . $fileSeparator . $file ) )
           return FALSE;
   }

   return TRUE;
}

function eZSetupTestTimeZone( $something )
{
    $result = true;
    if ( date_default_timezone_get() == "UTC" )
    {
        $result = false;
    }

    return ['result' => $result];
}

?>
