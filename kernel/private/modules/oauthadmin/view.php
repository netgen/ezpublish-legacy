<?php
/**
 * File containing the oauthadmin/edit view definition
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$session = ezcPersistentSessionInstance::get();

$module = $Params['Module'];

// @todo Instanciate the session maybe ?
$applicationId = $Params['ApplicationID'];
$application = $session->load( 'ezpRestClient', $applicationId );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'application', $application );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/oauthadmin', 'REST application: %application_name%', null,
    ['%application_name%' => $application->name] )]];

$Result['content'] = $tpl->fetch( 'design:oauthadmin/view.tpl' );
return $Result;
?>
