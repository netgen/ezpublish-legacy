<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZInfo", "variable_params" => true];

$ViewList = [];
$ViewList["copyright"] = ["functions" => ['read'], "script" => "copyright.php", "params" => []];

$ViewList["about"] = ["functions" => ['read'], "script" => "about.php", "params" => []];

$ViewList["is_alive"] = ["functions" => ['read'], "script" => "isalive.php", "params" => []];

$FunctionList = [];
$FunctionList['read'] = [];

?>
