<?php
/**
 * Definition of hashing benchmark
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$MarkDefinition = ['name' => 'Hashing', 'marks' => []];

$MarkDefinition['marks'][] = ['name' => 'MD5-CRC32', 'file' => 'ezmarkhashing.php', 'class' => 'eZMarkHashing'];

?>
