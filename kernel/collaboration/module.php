<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZCollaboration'];

$ViewList = [];
$ViewList['action'] = ['script' => 'action.php', "default_navigation_part" => 'ezmynavigationpart', 'default_action' => [['name' => 'Custom', 'type' => 'post', 'parameters' => ['CollaborationActionCustom', 'CollaborationTypeIdentifier', 'CollaborationItemID']]], 'post_action_parameters' => ['Custom' => ['TypeIdentifer' => 'CollaborationTypeIdentifier', 'ItemID' => 'CollaborationItemID']], 'params' => []];
$ViewList['view'] = ['script' => 'view.php', "default_navigation_part" => 'ezmynavigationpart', 'params' => ['ViewMode'], "unordered_params" => ["language" => "Language", "offset" => "Offset"]];
$ViewList['item'] = ['script' => 'item.php', "default_navigation_part" => 'ezmynavigationpart', 'params' => ['ViewMode', 'ItemID'], "unordered_params" => ["language" => "Language", "offset" => "Offset"]];
$ViewList['group'] = ['script' => 'group.php', "default_navigation_part" => 'ezmynavigationpart', 'params' => ['ViewMode', 'GroupID'], "unordered_params" => ["language" => "Language", "offset" => "Offset"]];

?>
