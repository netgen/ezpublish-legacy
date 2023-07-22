<?php
//
// Created on: <17-Aug-2004 12:57:54 bf>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 3.9.x
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

$Module = ['name' => 'eZODF', 'variable_params' => true];

$ViewList = [];
$ViewList['import'] = ['script' => 'import.php', 'params' => [], 'functions' => ['import'], 'post_actions' => ['BrowseActionName'], 'unordered_params' => ['node_id' => 'NodeID', 'import_type' => 'ImportType']];

$ViewList['export'] = ['script' => 'export.php', 'params' => [], 'functions' => ['export'], 'post_actions' => ['BrowseActionName'], 'unordered_params' => ['node_id' => 'NodeID', 'export_type' => 'ExportType']];


/*
$ViewList['upload_import'] = array(
    'script' => 'upload_import.php',
    'params' => array() );

$ViewList['authenticate'] = array(
    'script' => 'authenticate.php',
    'params' => array() );

$ViewList['upload_export'] = array(
    'script' => 'upload_export.php',
    'params' => array() );
*/

$FunctionList = [];
$FunctionList['import'] = [];
$FunctionList['export'] = [];

?>
