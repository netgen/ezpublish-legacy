<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZNotification", "variable_params" => true];


$ViewList = [];
$ViewList["settings"] = ["functions" => ['use'], "script" => "settings.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezmynavigationpart', "params" => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList["runfilter"] = ["functions" => ['administrate'], "script" => "runfilter.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezsetupnavigationpart', "params" => []];

$ViewList["addtonotification"] = ["functions" => ['use'], "script" => "addtonotification.php", 'ui_context' => 'administration', "default_navigation_part" => 'ezcontentnavigationpart', "params" => ['ContentNodeID']];

$FunctionList['use'] = [];
$FunctionList['administrate'] = [];


?>
