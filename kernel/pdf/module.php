<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZContentObject', 'variable_params' => true];

$ViewList['edit'] = ['script' => 'edit.php', 'functions' => ['edit'], 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['ExportPDFBrowse' => 'BrowseSource', 'ExportPDFButton' => 'Export', 'DiscardButton'   => 'Discard', 'CreateExport' => 'CreateExport'], 'post_action_parameters' => ['Export' => ['Title' => 'Title', 'DisplayFrontpage' => 'DisplayFrontpage', 'IntroText' => 'IntroText', 'SubText' => 'SubText', 'SourceNode' => 'SourceNode', 'ExportType' => 'ExportType', 'ClassList' => 'ClassList', 'SiteAccess' => 'SiteAccess', 'DestinationType' => 'DestinationType', 'DestinationFile' => 'DestinationFile'], 'BrowseSource' => ['Title' => 'Title', 'DisplayFrontpage' => 'DisplayFrontpage', 'IntroText' => 'IntroText', 'SubText' => 'SubText', 'ExportType' => 'ExportType', 'ClassList' => 'ClassList', 'SiteAccess' => 'SiteAccess', 'DestinationType' => 'DestinationType', 'DestinationFile' => 'DestinationFile']], 'unordered_params' => ['language' => 'Language'], 'params' => ['PDFExportID', 'PDFGenerate']];

$ViewList['list'] = ['script' => 'list.php', 'functions' => ['edit'], 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['NewPDFExport' => 'NewExport', 'RemoveExportButton' => 'RemoveExport'], 'post_action_parameters' => ['RemoveExport' => ['DeleteIDArray' => 'DeleteIDArray']], 'unordered_params' => ['language' => 'Language']];


$FunctionList = [];
$FunctionList['create'] = [];
$FunctionList['edit'] = [];

?>
