<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZWorkflow"];

$ViewList = [];
$ViewList["view"] = ["script" => "view.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowID"]];
$ViewList["edit"] = ["script" => "edit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowID", "GroupID", "GroupName"]];
$ViewList["groupedit"] = ["script" => "groupedit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowGroupID"]];
$ViewList["down"] = ["script" => "edit.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowID", "EventID"]];
$ViewList["up"] = ["script" => "edit.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowID", "EventID"]];
$ViewList["workflowlist"] = ["script" => "workflowlist.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["GroupID"]];
$ViewList["grouplist"] = ["script" => "grouplist.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => []];
$ViewList["process"] = ["script" => "process.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowProcessID"]];
$ViewList["run"] = ["script" => "run.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowProcessID"]];
$ViewList["event"] = ["script" => "event.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["WorkflowID", "EventID"]];
$ViewList["processlist"] = ["script" => "processlist.php", "default_navigation_part" => 'ezsetupnavigationpart', 'unordered_params' => ['offset' => 'Offset'], "params" => []];

?>
