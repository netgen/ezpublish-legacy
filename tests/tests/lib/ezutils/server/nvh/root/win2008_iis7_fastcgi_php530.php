<?php
/**
 * General test data for httpd server unit testing
 *
 * Provides data for testing routing, encoding, virtual host mode detection & more
 * on most httpd servers with different configurations.
 *
 * More info available on:
 * http://github.com/ezsystems/ezpublish-legacy/tree/master/tests/tests/lib/ezutils/server
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 * @subpackage lib
 *
 */

return ['PHP_VERSION' => '5.3.0', 'PHP_OS' => 'WINNT', 'PHP_SAPI' => 'cgi-fcgi', 'php_uname' => 'Windows NT WIN-55DFDS 6.0 build 6001 (Windows Server 2008 Standard Edition Service Pack 1) i586', 'DIRECTORY_SEPARATOR' => '\\', 'PHP_SHLIB_SUFFIX' => 'dll', 'PATH_SEPARATOR' => ';', 'DEFAULT_INCLUDE_PATH' => '.;C:\\php5\\pear', 'include_path' => '.;C:\\php5\\pear', 'PHP_MAXPATHLEN' => 260, 'PHP_EOL' => '
', 'PHP_INT_MAX' => 2_147_483_647, 'PHP_INT_SIZE' => 4, 'getcwd' => 'C:\\www\\qa.ms.ez.no', '_SERVER' =>
['PHP_FCGI_MAX_REQUESTS' => '0', 'PHP_FCGI_CHILDREN' => '0', 'ALLUSERSPROFILE' => 'C:\\ProgramData', 'APPDATA' => 'C:\\Windows\\system32\\config\\systemprofile\\AppData\\Roaming', 'APP_POOL_ID' => 'qa.ms.ez.no', 'CommonProgramFiles' => 'C:\\Program Files\\Common Files', 'COMPUTERNAME' => 'WIN-55DFDS', 'ComSpec' => 'C:\\Windows\\system32\\cmd.exe', 'FP_NO_HOST_CHECK' => 'NO', 'LOCALAPPDATA' => 'C:\\Windows\\system32\\config\\systemprofile\\AppData\\Local', 'NUMBER_OF_PROCESSORS' => '2', 'OS' => 'Windows_NT', 'Path' => 'C:\\Windows\\system32;C:\\Windows;C:\\Windows\\System32\\Wbem;C:\\Program Files\\MySQL\\MySQL Server 5.0\\bin', 'PATHEXT' => '.COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC', 'PROCESSOR_ARCHITECTURE' => 'x86', 'PROCESSOR_IDENTIFIER' => 'x86 Family 6 Model 15 Stepping 8, GenuineIntel', 'PROCESSOR_LEVEL' => '6', 'PROCESSOR_REVISION' => '0f08', 'ProgramData' => 'C:\\ProgramData', 'ProgramFiles' => 'C:\\Program Files', 'PUBLIC' => 'C:\\Users\\Public', 'SystemDrive' => 'C:', 'SystemRoot' => 'C:\\Windows', 'TEMP' => 'C:\\Windows\\TEMP', 'TMP' => 'C:\\Windows\\TEMP', 'USERDOMAIN' => 'AD', 'USERNAME' => 'WIN-55DFDS$', 'USERPROFILE' => 'C:\\Windows\\system32\\config\\systemprofile', 'windir' => 'C:\\Windows', 'FCGI_ROLE' => 'RESPONDER', 'HTTP_CONNECTION' => 'keep-alive', 'HTTP_KEEP_ALIVE' => '115', 'HTTP_CONTENT_LENGTH' => '0', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'HTTP_ACCEPT_ENCODING' => 'gzip,deflate', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_COOKIE' => '', 'HTTP_HOST' => 'qa.ms.ez.no', 'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11', 'DOCUMENT_ROOT' => 'C:\\www\\qa.ms.ez.no', 'REQUEST_URI' => '/?get=value', 'SCRIPT_FILENAME' => 'C:\\www\\qa.ms.ez.no\\index.php', 'IIS_UrlRewriteModule' => '1', 'APPL_MD_PATH' => '/LM/W3SVC/3/ROOT', 'APPL_PHYSICAL_PATH' => 'C:\\www\\qa.ms.ez.no\\', 'AUTH_TYPE' => '', 'AUTH_PASSWORD' => '', 'AUTH_USER' => '', 'CERT_COOKIE' => '', 'CERT_FLAGS' => '', 'CERT_ISSUER' => '', 'CERT_SERIALNUMBER' => '', 'CERT_SUBJECT' => '', 'CONTENT_LENGTH' => '0', 'CONTENT_TYPE' => '', 'GATEWAY_INTERFACE' => 'CGI/1.1', 'HTTPS' => 'off', 'HTTPS_KEYSIZE' => '', 'HTTPS_SECRETKEYSIZE' => '', 'HTTPS_SERVER_ISSUER' => '', 'HTTPS_SERVER_SUBJECT' => '', 'INSTANCE_ID' => '3', 'INSTANCE_META_PATH' => '/LM/W3SVC/3', 'LOCAL_ADDR' => '127.0.0.1', 'LOGON_USER' => '', 'PATH_TRANSLATED' => 'C:\\www\\qa.ms.ez.no\\index.php', 'QUERY_STRING' => 'get=value', 'REMOTE_ADDR' => '127.0.0.1', 'REMOTE_HOST' => '127.0.0.1', 'REMOTE_PORT' => '50573', 'REMOTE_USER' => '', 'REQUEST_METHOD' => 'GET', 'SCRIPT_NAME' => '/index.php', 'SERVER_NAME' => 'qa.ms.ez.no', 'SERVER_PORT' => '80', 'SERVER_PORT_SECURE' => '0', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'SERVER_SOFTWARE' => 'Microsoft-IIS/7.0', 'URL' => '/index.php', 'ORIG_PATH_INFO' => '/index.php', 'PHP_SELF' => '/index.php', 'REQUEST_TIME' => 1_287_751_653, 'argv' =>
[0 => 'get=value'], 'argc' => 1], '_ENV' =>
['PHP_FCGI_MAX_REQUESTS' => '0', 'PHP_FCGI_CHILDREN' => '0', 'ALLUSERSPROFILE' => 'C:\\ProgramData', 'APPDATA' => 'C:\\Windows\\system32\\config\\systemprofile\\AppData\\Roaming', 'APP_POOL_ID' => 'qa.ms.ez.no', 'CommonProgramFiles' => 'C:\\Program Files\\Common Files', 'COMPUTERNAME' => 'WIN-55DFDS', 'ComSpec' => 'C:\\Windows\\system32\\cmd.exe', 'FP_NO_HOST_CHECK' => 'NO', 'LOCALAPPDATA' => 'C:\\Windows\\system32\\config\\systemprofile\\AppData\\Local', 'NUMBER_OF_PROCESSORS' => '2', 'OS' => 'Windows_NT', 'Path' => 'C:\\Windows\\system32;C:\\Windows;C:\\Windows\\System32\\Wbem;C:\\Program Files\\MySQL\\MySQL Server 5.0\\bin', 'PATHEXT' => '.COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC', 'PROCESSOR_ARCHITECTURE' => 'x86', 'PROCESSOR_IDENTIFIER' => 'x86 Family 6 Model 15 Stepping 8, GenuineIntel', 'PROCESSOR_LEVEL' => '6', 'PROCESSOR_REVISION' => '0f08', 'ProgramData' => 'C:\\ProgramData', 'ProgramFiles' => 'C:\\Program Files', 'PUBLIC' => 'C:\\Users\\Public', 'SystemDrive' => 'C:', 'SystemRoot' => 'C:\\Windows', 'TEMP' => 'C:\\Windows\\TEMP', 'TMP' => 'C:\\Windows\\TEMP', 'USERDOMAIN' => 'AD', 'USERNAME' => 'WIN-55DFDS$', 'USERPROFILE' => 'C:\\Windows\\system32\\config\\systemprofile', 'windir' => 'C:\\Windows', 'FCGI_ROLE' => 'RESPONDER', 'HTTP_CONNECTION' => 'keep-alive', 'HTTP_KEEP_ALIVE' => '115', 'HTTP_CONTENT_LENGTH' => '0', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'HTTP_ACCEPT_ENCODING' => 'gzip,deflate', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_COOKIE' => '', 'HTTP_HOST' => 'qa.ms.ez.no', 'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11', 'DOCUMENT_ROOT' => 'C:\\www\\qa.ms.ez.no', 'REQUEST_URI' => '/?get=value', 'SCRIPT_FILENAME' => 'C:\\www\\qa.ms.ez.no\\index.php', 'IIS_UrlRewriteModule' => '1', 'APPL_MD_PATH' => '/LM/W3SVC/3/ROOT', 'APPL_PHYSICAL_PATH' => 'C:\\www\\qa.ms.ez.no\\', 'AUTH_TYPE' => '', 'AUTH_PASSWORD' => '', 'AUTH_USER' => '', 'CERT_COOKIE' => '', 'CERT_FLAGS' => '', 'CERT_ISSUER' => '', 'CERT_SERIALNUMBER' => '', 'CERT_SUBJECT' => '', 'CONTENT_LENGTH' => '0', 'CONTENT_TYPE' => '', 'GATEWAY_INTERFACE' => 'CGI/1.1', 'HTTPS' => 'off', 'HTTPS_KEYSIZE' => '', 'HTTPS_SECRETKEYSIZE' => '', 'HTTPS_SERVER_ISSUER' => '', 'HTTPS_SERVER_SUBJECT' => '', 'INSTANCE_ID' => '3', 'INSTANCE_META_PATH' => '/LM/W3SVC/3', 'LOCAL_ADDR' => '127.0.0.1', 'LOGON_USER' => '', 'PATH_TRANSLATED' => 'C:\\www\\qa.ms.ez.no\\index.php', 'QUERY_STRING' => 'get=value', 'REMOTE_ADDR' => '127.0.0.1', 'REMOTE_HOST' => '127.0.0.1', 'REMOTE_PORT' => '50573', 'REMOTE_USER' => '', 'REQUEST_METHOD' => 'GET', 'SCRIPT_NAME' => '/index.php', 'SERVER_NAME' => 'qa.ms.ez.no', 'SERVER_PORT' => '80', 'SERVER_PORT_SECURE' => '0', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'SERVER_SOFTWARE' => 'Microsoft-IIS/7.0', 'URL' => '/index.php', 'ORIG_PATH_INFO' => '/index.php', 'PHP_SELF' => '/index.php', 'REQUEST_TIME' => 1_287_751_653, 'argv' =>
[0 => 'get=value'], 'argc' => 1]];
