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

$Module = ['name' => 'Rest client authorization', 'variable_params' => true];

$ViewList = [];

$ViewList['authorize'] = ['script' => 'authorize.php'];

$FunctionList = [];
?>
