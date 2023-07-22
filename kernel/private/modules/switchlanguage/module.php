<?php
/**
 * File containing the switchlanguage module definition
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "SwitchLanguage", "var_params" => false];

$ViewList = [];
$ViewList['to'] = ["script" => "to.php", "params" => ["SiteAccess"]];

$FunctionList = [];

?>
