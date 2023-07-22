<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

$Module = ['name' => 'eZ Flow', 'functions' => ['changelayout']];

$ViewList = [];
$ViewList['get'] = ['script' => 'get.php', 'functions' => ['edit']];

$ViewList['timeline'] = ['script' => 'timeline.php', 'functions' => ['timeline'], 'params' => ['NodeID', 'LanguageCode']];

$ViewList['preview'] = ['script' => 'preview.php', 'functions' => ['timeline'], 'params' => ['Time', 'NodeID']];

$ViewList['zone'] = ['script' => 'zone.php', 'functions' => ['edit'], 'params' => ['ContentObjectAttributeID', 'Version', 'ZoneID']];

$ViewList['request'] = ['script' => 'request.php', 'functions' => ['edit'], 'unordered_params' => ['items' => 'Items', 'block' => 'Block']];

$ViewList['push'] = ['script' => 'push.php', 'functions' => ['edit'], 'params' => ['NodeID'], 'single_post_actions' => ['PlacementStoreButton' => 'Store'], 'post_action_parameters' => ['Store' => ['PlacementList' => 'PlacementTSArray']]];

$ViewList['block'] = ['script' => 'block.php', 'functions' => ['call'], 'params' => ['BlockID', 'Output']];

$FunctionList = [];
$FunctionList['timeline'] = [];
$FunctionList['edit'] = [];
$FunctionList['call'] = [];
$FunctionList['changelayout'] = ['Class' => ['name'=> 'Class', 'values'=> [], 'path' => 'classes/', 'file' => 'ezcontentclass.php', 'class' => 'eZContentClass', 'function' => 'fetchList', 'parameter' => [0, false, false, ['name' => 'asc']]]];
?>