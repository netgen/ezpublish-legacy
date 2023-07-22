<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZSection'];

$ViewList = [];
$ViewList['list'] = ['functions' => ['view or edit or assign'], 'script' => 'list.php', 'default_navigation_part' => 'ezsetupnavigationpart', "unordered_params" => ["offset" => "Offset"], 'params' => []];

$ViewList['view'] = ['functions' => ['view or assign'], 'script' => 'view.php', 'ui_context' => 'view', 'default_navigation_part' => 'ezsetupnavigationpart', 'params' => ['SectionID'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['edit'] = ['functions' => ['edit'], 'script' => 'edit.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'params' => ['SectionID']];

$ViewList['assign'] = ['functions' => ['assign'], 'script' => 'assign.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'post_actions' => ['BrowseActionName'], 'params' => ['SectionID'], 'functions' => ['assign']];



$ClassID = ['name'=> 'Class', 'values'=> [], 'class' => 'eZContentClass', 'function' => 'fetchList', 'parameter' => [0, false, false, ['name' => 'asc']]];

$NewSectionID = ['name'=> 'NewSection', 'values'=> [], 'class' => 'eZSection', 'function' => 'fetchList', 'parameter' => [false]];

$SectionID = ['name'=> 'Section', 'values'=> [], 'class' => 'eZSection', 'function' => 'fetchList', 'parameter' => [false]];

$Assigned = ['name'=> 'Owner', 'values'=> [['Name' => 'Self', 'value' => '1']]];

$FunctionList = [];
$FunctionList['assign'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'NewSection' => $NewSectionID];
$FunctionList['edit'] = [];
$FunctionList['view'] = [];

?>
