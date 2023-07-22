<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZRSS'];

$ViewList = [];
$ViewList['list'] = ['script' => 'list.php', 'functions' => ['edit'], 'default_navigation_part' => 'ezsetupnavigationpart', 'unordered_params' => ['language' => 'Language']];

$ViewList['edit_export'] = ['script' => 'edit_export.php', 'functions' => ['edit'], 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'Update_Item_Class' => 'UpdateItem', 'AddSourceButton' => 'AddItem', 'RemoveButton' => 'Cancel', 'BrowseImageButton' => 'BrowseImage', 'RemoveImageButton' => 'RemoveImage'], 'params' => ['RSSExportID', 'RSSExportItemID', 'BrowseType']];

$ViewList['edit_import'] = ['script' => 'edit_import.php', 'functions' => ['edit'], 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['StoreButton' => 'Store', 'RemoveButton' => 'Cancel', 'AnalyzeFeedButton' => 'AnalyzeFeed', 'Update_Class' => 'UpdateClass', 'DestinationBrowse' => 'BrowseDestination', 'UserBrowse' => 'BrowseUser'], 'params' => ['RSSImportID', 'BrowseType']];


$ViewList['feed'] = ['script' => 'feed.php', 'functions' => ['feed'], 'params' => ['RSSFeed']];


$FunctionList = [];
$FunctionList['feed'] = [];
$FunctionList['edit'] = [];

?>
