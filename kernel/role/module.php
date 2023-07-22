<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZRole'];

$ViewList = [];
$ViewList['list'] = ['script' => 'list.php', 'default_navigation_part' => 'ezusernavigationpart', 'post_actions' => ['BrowseActionName'], 'unordered_params' => ['offset' => 'Offset'], 'params' => []];
$ViewList['edit'] = ['script' => 'edit.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezusernavigationpart', 'params' => ['RoleID']];
$ViewList['copy'] = ['script' => 'copy.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezusernavigationpart', 'params' => ['RoleID']];
$ViewList['policyedit'] = ['script' => 'policyedit.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezusernavigationpart', 'params' => ['PolicyID']];
$ViewList['view'] = ['script' => 'view.php', 'default_navigation_part' => 'ezusernavigationpart', 'post_actions' => ['BrowseActionName'], 'params' => ['RoleID']];
$ViewList['assign'] = ['script' => 'assign.php', 'default_navigation_part' => 'ezusernavigationpart', 'params' => ['RoleID', 'LimitIdent', 'LimitValue']];

?>
