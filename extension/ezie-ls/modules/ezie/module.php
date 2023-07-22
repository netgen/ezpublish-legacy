<?php
/**
 * File containing the ezie module definition
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */

$Module = ['name' => 'ezie'];

$ViewList = [];

$ViewList['prepare'] = ['script' => 'prepare.php', 'params' => ['object_id', 'edit_language', 'attribute_id', 'version']];

// FILTERS
$ViewList['filter_bw']         = ['script' => 'filter_bw.php'];
$ViewList['filter_sepia']      = ['script' => 'filter_sepia.php'];
$ViewList['filter_blur']       = ['script' => 'filter_blur.php'];
$ViewList['filter_contrast']   = ['script' => 'filter_contrast.php'];
$ViewList['filter_brightness'] = ['script' => 'filter_brightness.php'];

// TOOLS
$ViewList['tool_flip_hor']   = ['script' => 'tool_flip_hor.php'];
$ViewList['tool_flip_ver']   = ['script' => 'tool_flip_ver.php'];
$ViewList['tool_rotation']   = ['script' => 'tool_rotation.php'];
$ViewList['tool_levels']     = ['script' => 'tool_levels.php'];
$ViewList['tool_saturation'] = ['script' => 'tool_saturation.php'];
$ViewList['tool_pixelate']   = ['script' => 'tool_pixelate.php'];
$ViewList['tool_crop']       = ['script' => 'tool_crop.php'];
$ViewList['tool_watermark']  = ['script' => 'tool_watermark.php'];

// MENU ACTIONS
$ViewList['no_save_and_quit'] = ['script' => 'no_save_and_quit.php'];
$ViewList['save_and_quit']    = ['script' => 'save_and_quit.php'];

$FunctionList = [];
?>
