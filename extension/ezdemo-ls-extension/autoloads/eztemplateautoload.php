<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish Website Interface
// SOFTWARE RELEASE: 1.4-0
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

$eZTemplateOperatorArray = [];

$eZTemplateOperatorArray[] = ['script' => 'extension/ezdemo/autoloads/ezkeywordlist.php', 'class' => 'eZKeywordList', 'operator_names' => ['ezkeywordlist']];
$eZTemplateOperatorArray[] = ['script' => 'extension/ezdemo/autoloads/ezarchive.php', 'class' => 'eZArchive', 'operator_names' => ['ezarchive']];
$eZTemplateOperatorArray[] = ['script' => 'extension/ezdemo/autoloads/eztagcloud.php', 'class' => 'eZTagCloud', 'operator_names' => ['eztagcloud']];
$eZTemplateOperatorArray[] = ['script' => 'extension/ezdemo/autoloads/ezpagedata.php', 'class' => 'eZPageData', 'operator_names' => ['ezpagedata', 'ezpagedata_set', 'ezpagedata_append']];
?>
