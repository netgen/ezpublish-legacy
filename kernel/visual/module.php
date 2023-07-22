<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ["name" => "eZVisual", "variable_params" => true, 'ui_component_match' => 'view'];

$ViewList = [];
$ViewList["toolbarlist"] = ["script" => "toolbarlist.php", "default_navigation_part" => 'ezvisualnavigationpart', "params" => ['SiteAccess']];

$ViewList["toolbar"] = ["script" => "toolbar.php", 'ui_context' => 'edit', "default_navigation_part" => 'ezvisualnavigationpart', 'post_actions' => ['BrowseActionName'], 'single_post_actions' => ['BackToToolbarsButton' => 'BackToToolbars', 'NewToolButton' => 'NewTool', 'UpdatePlacementButton' => 'UpdatePlacement', 'BrowseButton' => 'Browse', 'RemoveButton' => 'Remove', 'StoreButton' => 'Store'], "params" => ['SiteAccess', 'Position']];

$ViewList["menuconfig"] = ["script" => "menuconfig.php", 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess'], "params" => []];

$ViewList["templatelist"] = ["script" => "templatelist.php", "default_navigation_part" => 'ezvisualnavigationpart', "params" => [], "unordered_params" => ["offset" => "Offset"]];

$ViewList["templateview"] = ["script" => "templateview.php", "default_navigation_part" => 'ezvisualnavigationpart', 'single_post_actions' => ['SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess', 'RemoveOverrideButton' => 'RemoveOverride', 'UpdateOverrideButton' => 'UpdateOverride', 'NewOverrideButton' => 'NewOverride'], "params" => []];

$ViewList['templateedit'] = ['script' => 'templateedit.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezvisualnavigationpart', 'single_post_actions' => ['SaveButton' => 'Save', 'DiscardButton' => 'Discard'], 'params' => [], 'unordered_params' => ['siteAccess' => 'SiteAccess']];

$ViewList['templatecreate'] = ['script' => 'templatecreate.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezvisualnavigationpart', 'single_post_actions' => ['CreateOverrideButton' => 'CreateOverride', 'CancelOverrideButton' => 'CancelOverride'], 'params' => [], 'unordered_params' => ['siteAccess' => 'SiteAccess', 'classID' => 'ClassID', 'nodeID' => 'NodeID']];

?>
