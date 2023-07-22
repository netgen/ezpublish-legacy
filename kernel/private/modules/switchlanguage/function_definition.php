<?php
/**
 * File containing function definition for LanguageSwitcher module
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = [];
$FunctionList['url_alias'] = ['name' => 'url_alias', 'call_method' => ['class' => 'ezpLanguageSwitcherFunctionCollection', 'method' => 'fetchUrlAlias'], 'parameters' => [['name' => 'node_id', 'type' => 'integer', 'default' => false, 'required' => false], ['name' => 'path', 'type' => 'string', 'default' => false, 'required' => false], ['name' => 'locale', 'type' => 'string', 'default' => false, 'required' => true]]];

?>
