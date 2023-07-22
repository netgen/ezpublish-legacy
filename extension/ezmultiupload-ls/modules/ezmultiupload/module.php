<?php
/**
 * File containing the eZ Publish module definition.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version 1.0.0
 * @package ezmultiupload
 */

$Module = ['name' => 'eZ Multiupload', 'variable_params' => true];

$ViewList = [];
$ViewList['upload'] = ['script' => 'upload.php', 'single_post_actions' => ['UploadButton' => 'Upload'], 'params' => ['ParentNodeID']];

?>
