<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];

$offset = $Params['Offset'];

$doFiltration = false;
$filterString = '';

if ( !is_numeric( $offset ) )
    $offset = 0;

if ( $http->hasVariable( 'filterString' ) )
{
    $filterString = $http->variable('filterString');
    if ( ( strlen( trim( (string) $filterString ) ) > 0 ) )
        $doFiltration = true;
}


$ini = eZINI::instance();
$tpl = eZTemplate::factory();

$siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

$overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );

$mostUsedOverrideArray = [];
$filteredOverrideArray = [];
$mostUsedMatchArray = ['node/view/', 'content/view/embed', 'pagelayout.tpl', 'search.tpl', 'basket'];
foreach ( array_keys( $overrideArray ) as $overrideKey )
{
    foreach ( $mostUsedMatchArray as $mostUsedMatch )
    {
        if ( str_contains( (string) $overrideArray[$overrideKey]['template'], $mostUsedMatch ) )
        {
            $mostUsedOverrideArray[$overrideKey] = $overrideArray[$overrideKey];
        }
    }
    if ( $doFiltration ) {
        if ( str_contains( (string) $overrideArray[$overrideKey]['template'], (string) $filterString ) )
        {
            $filteredOverrideArray[$overrideKey] = $overrideArray[$overrideKey];
        }
    }
}

$tpl->setVariable( 'filterString', $filterString );

if ( $doFiltration )
{
    $tpl->setVariable( 'template_array', $filteredOverrideArray );
    $tpl->setVariable( 'template_count', count( $filteredOverrideArray ) );
}
else
{
    $tpl->setVariable( 'template_array', $overrideArray );
    $tpl->setVariable( 'template_count', count( $overrideArray ) );
}

$tpl->setVariable( 'most_used_template_array', $mostUsedOverrideArray );
$viewParameters = ['offset' => $offset];
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = [];
$Result['content'] = $tpl->fetch( "design:visual/templatelist.tpl" );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/design', 'Template list' )]];

?>
