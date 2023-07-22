<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZURL'];

$ViewList = [];
$ViewList['list'] = ['script' => 'list.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['SetValid' => 'SetValid', 'SetInvalid' => 'SetInvalid'], 'post_action_parameters' => ['SetValid' => ['URLSelection' => 'URLSelection'], 'SetInvalid' => ['URLSelection' => 'URLSelection']], 'params' => ['ViewMode'], "unordered_params" => ["offset" => "Offset"]];
$ViewList['view'] = ['script' => 'view.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['EditObject' => 'EditObject'], 'params' => ['ID'], 'unordered_params'=> ['offset' => 'Offset']];
$ViewList['edit'] = ['script' => 'edit.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['Cancel' => 'Cancel', 'Store' => 'Store'], 'params' => ['ID']];
?>
