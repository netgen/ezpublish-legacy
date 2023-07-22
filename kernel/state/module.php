<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZContentObjectState', 'variable_params' => false];

$ViewList = [];

$ViewList['assign'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'assign.php', 'params' => ['ObjectID', 'SelectedStateID'], 'functions' => ['assign'], 'single_post_actions' => ['AssignButton' => 'Assign'], 'post_action_parameters' => ['Assign' => ['ObjectID'            => 'ObjectID', 'SelectedStateIDList' => 'SelectedStateIDList', 'RedirectRelativeURI' => 'RedirectRelativeURI']]];

$ViewList['groups'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'groups.php', 'params' => [], 'functions' => ['administrate'], 'unordered_params' => ['offset' => 'Offset'], 'single_post_actions' => ['CreateButton' => 'Create', 'RemoveButton' => 'Remove'], 'post_action_parameters' => ['Remove' => ['RemoveIDList' => 'RemoveIDList']]];

$ViewList['group'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'group.php', 'params' => ['GroupIdentifier', 'Language'], 'functions' => ['administrate'], 'single_post_actions' => ['CreateButton' => 'Create', 'UpdateOrderButton' => 'UpdateOrder', 'EditButton' => 'Edit', 'RemoveButton' => 'Remove'], 'post_action_parameters' => ['UpdateOrder' => ['Order' => 'Order'], 'Remove' => ['RemoveIDList' => 'RemoveIDList']]];

$ViewList['group_edit'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'group_edit.php', 'ui_context' => 'edit', 'params' => ['GroupIdentifier'], 'functions' => ['administrate'], 'single_post_actions' => ['StoreButton' => 'Store', 'CancelButton' => 'Cancel']];

$ViewList['view'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'view.php', 'params' => ['GroupIdentifier', 'StateIdentifier', 'Language'], 'functions' => ['administrate'], 'single_post_actions' => ['EditButton' => 'Edit']];

$ViewList['edit'] = ['default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'edit.php', 'ui_context' => 'edit', 'params' => ['GroupIdentifier', 'StateIdentifier'], 'functions' => ['administrate'], 'single_post_actions' => ['StoreButton' => 'Store', 'CancelButton' => 'Cancel']];

$ClassID = ['name'=> 'Class', 'values'=> [], 'class' => 'eZContentClass', 'function' => 'fetchList', 'parameter' => [0, false, false, ['name' => 'asc']]];

$SectionID = ['name'=> 'Section', 'values'=> [], 'class' => 'eZSection', 'function' => 'fetchList', 'parameter' => [false]];

$Assigned = ['name'=> 'Owner', 'values'=> [['Name' => 'Self', 'value' => '1']]];

$AssignedGroup = ['name'=> 'Group', 'single_select' => true, 'values'=> [['Name' => 'Self', 'value' => '1']]];

$Node = ['name'=> 'Node', 'values'=> []];

$Subtree = ['name'=> 'Subtree', 'values'=> []];

$stateLimitations = eZContentObjectStateGroup::limitations();

$NewState = ['name' => 'NewState', 'values' => [], 'class' => 'eZContentObjectState', 'function' => 'limitationList', 'parameter' => []];

$FunctionList = [];

$FunctionList['administrate'] = [];

$FunctionList['assign'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Group' => $AssignedGroup, 'Node' => $Node, 'Subtree' => $Subtree];

$FunctionList['assign'] = array_merge( $FunctionList['assign'], $stateLimitations, ['NewState' => $NewState] );

?>
