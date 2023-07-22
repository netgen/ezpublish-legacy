<?php
/**
 * Template autoload definition for eZ Starrating
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 */

$eZTemplateOperatorArray = [];


$eZTemplateOperatorArray[] = ['script' => 'extension/ezstarrating/autoloads/ezsrtemplateoperators.php', 'class' => 'ezsrTemplateOperators', 'operator_names' => ['fetch_starrating_data', 'fetch_starrating_stats', 'fetch_by_starrating']];

?>
