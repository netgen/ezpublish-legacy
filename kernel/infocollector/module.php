<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZInfoCollector'];

$ViewList = [];
$ViewList['overview'] = ['script' => 'overview.php', 'functions' => ['read'], 'default_navigation_part' => 'ezsetupnavigationpart', 'ui_context' => 'view', 'unordered_params' => ['offset' => 'Offset'], 'single_post_actions' => ['RemoveObjectCollectionButton' => 'RemoveObjectCollection', 'ConfirmRemoveButton' => 'ConfirmRemoval', 'CancelRemoveButton' => 'CancelRemoval']];

$ViewList['collectionlist'] = ['script' => 'collectionlist.php', 'functions' => ['read'], 'default_navigation_part' => 'ezsetupnavigationpart', 'ui_context' => 'view', 'params' => ['ObjectID'], 'unordered_params' => ['offset' => 'Offset'], 'single_post_actions' => ['RemoveCollectionsButton' => 'RemoveCollections', 'ConfirmRemoveButton' => 'ConfirmRemoval', 'CancelRemoveButton' => 'CancelRemoval']];

$ViewList['view'] = ['script' => 'view.php', 'functions' => ['read'], 'default_navigation_part' => 'ezsetupnavigationpart', 'ui_context' => 'view', 'params' => ['CollectionID']];


$FunctionList = [];
$FunctionList['read'] = [];

?>
