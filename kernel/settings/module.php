<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "Settings management", "variable_params" => true];

$ViewList = [];
$ViewList["view"] = ["script" => "view.php", "default_navigation_part" => "ezsetupnavigationpart", "params" => ['SiteAccess', 'INIFile']];
$ViewList["edit"] = ["script" => "edit.php", 'ui_context' => 'edit', "default_navigation_part" => "ezsetupnavigationpart", "params" => ['SiteAccess', 'INIFile', 'Block', 'Setting', 'Placement']];

?>
