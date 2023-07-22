<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZContentClass"];

$ViewList = [];
$ViewList["edit"] = ["script" => "edit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["ClassID", "GroupID", "GroupName"], 'unordered_params' => ['language' => 'Language']];
$ViewList["view"] = ["script" => "view.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["ClassID"], 'unordered_params' => ['language' => 'Language', 'scriptid' => 'ScheduledScriptID']];
$ViewList["copy"] = ["script" => "copy.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["ClassID"]];
$ViewList["down"] = ["script" => "edit.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["ClassID", "AttributeID"]];
$ViewList["up"] = ["script" => "edit.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["ClassID", "AttributeID"]];
$ViewList["removeclass"] = ["script" => "removeclass.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["GroupID"]];
$ViewList["removegroup"] = ["script" => "removegroup.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => []];
$ViewList["classlist"] = ["script" => "classlist.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["GroupID"]];
$ViewList["grouplist"] = ["script" => "grouplist.php", "default_navigation_part" => 'ezsetupnavigationpart', "params" => []];
$ViewList["groupedit"] = ["script" => "groupedit.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezsetupnavigationpart', "params" => ["GroupID"]];
$ViewList['translation'] = ['script' => 'translation.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'ui_context' => 'edit', 'params' => [], 'single_post_actions' => ['CancelButton' => 'Cancel', 'UpdateInitialLanguageButton' => 'UpdateInitialLanguage', 'RemoveTranslationButton' => 'RemoveTranslation'], 'post_action_parameters' => ['Cancel' => ['ClassID' => 'ContentClassID', 'LanguageCode' => 'ContentClassLanguageCode'], 'UpdateInitialLanguage' => ['ClassID' => 'ContentClassID', 'LanguageCode' => 'ContentClassLanguageCode', 'InitialLanguageID' => 'InitialLanguageID'], 'RemoveTranslation' => ['ClassID' => 'ContentClassID', 'LanguageCode' => 'ContentClassLanguageCode', 'LanguageID' => 'LanguageID', 'ConfirmRemoval' => 'ConfirmRemoval']]];

?>
