<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZSearch", "variable_params" => true];

$ViewList = [];

$ViewList["stats"] = ["script" => "stats.php", "default_navigation_part" => 'ezsetupnavigationpart', 'single_post_actions' => ['ResetSearchStatsButton' => 'ResetSearchStats'], "params" => [], "unordered_params" => ["offset" => "Offset"]];

?>
