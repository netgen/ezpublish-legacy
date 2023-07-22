<?php
/**
 * Definition of eZTemplate benchmark
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$MarkDefinition = ['name' => 'eZTemplate', 'marks' => []];

$MarkDefinition['marks'][] = ['name' => 'Template Compiler', 'file' => 'ezmarktemplatecompiler.php', 'class' => 'eZMarkTemplateCompiler'];

?>
