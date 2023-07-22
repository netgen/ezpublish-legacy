#!/usr/bin/env php
<?php
/**
 * File containing the ezexec.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( ['description' => ( "eZ Publish Script Executor\n\n" .
                                                        "Allows execution of simple PHP scripts which use eZ Publish functionality,\n" .
                                                        "when the script is called all necessary initialization is done\n" .
                                                        "\n" .
                                                        "ezexec.php myscript.php" ), 'use-session' => false, 'use-modules' => true, 'use-extensions' => true] );

$script->startup();

$options = $script->getOptions( "",
                                "[scriptfile]",
                                [] );
$script->initialize();

if ( (is_countable($options['arguments']) ? count( $options['arguments'] ) : 0) < 1 )
{
    $script->shutdown( 1, "Missing script file" );
}

$scriptFile = $options['arguments'][0];

if ( !file_exists( $scriptFile ) )
    $script->shutdown( 1, "Could not execute the script '$scriptFile', file was not found" );

$retCode = include( $scriptFile );

if ( $retCode != 1 )
    $script->setExitCode( 1 );

$script->shutdown();

?>
