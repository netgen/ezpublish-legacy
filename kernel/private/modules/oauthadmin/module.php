<?php
/**
 * File containing the oauthadmin module definition.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

include_once 'kernel/private/rest/classes/lazy.php';

$Module = ['name' => 'Rest client admin', 'variable_params' => true];

$ViewList = [];

$ViewList['list'] = ['script' => 'list.php', 'default_navigation_part' => 'ezsetupnavigationpart'];

$ViewList['edit'] = ['script' => 'edit.php', 'params' => ['ApplicationID'], 'single_post_actions' => ['StoreButton' => 'Store', 'DiscardButton' => 'Discard'], 'post_action_parameters' => ['Store' => ['Name' => 'Name', 'EndPointURI' => 'EndPointURI', 'Description' => 'Description']], 'default_navigation_part' => 'ezsetupnavigationpart'];

$ViewList['action'] = ['script' => 'action.php', 'single_post_actions' => ['NewApplicationButton' => 'NewApplication', 'DeleteApplicationListButton' => 'DeleteApplicationList'], 'post_action_parameters' => ['DeleteApplicationList' => ['ApplicationIDList' => 'DeleteIDArray', 'ConfirmDelete' => 'ConfirmDelete']], 'default_navigation_part' => 'ezsetupnavigationpart'];

$ViewList['view'] = ['script' => 'view.php', 'params' => ['ApplicationID'], 'default_navigation_part' => 'ezsetupnavigationpart'];

$FunctionList = [];
?>
