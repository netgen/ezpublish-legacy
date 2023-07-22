<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$ViewMode = $Params['ViewMode'];

if( eZPreferences::value( 'admin_url_list_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_url_list_limit' )) {
        '2' => 25,
        '3' => 50,
        default => 10,
    };
}
else
{
    $limit = 10;
}

$offset = $Params['Offset'];
if ( !is_numeric( $offset ) )
{
    $offset = 0;
}

if( $ViewMode != 'all' && $ViewMode != 'invalid' && $ViewMode != 'valid')
{
    $ViewMode = 'all';
}

if ( $Module->isCurrentAction( 'SetValid' ) )
{
    $urlSelection = $Module->actionParameter( 'URLSelection' );
    eZURL::setIsValid( $urlSelection, true );
}
else if ( $Module->isCurrentAction( 'SetInvalid' ) )
{
    $urlSelection = $Module->actionParameter( 'URLSelection' );
    eZURL::setIsValid( $urlSelection, false );
}


if( $ViewMode == 'all' )
{
    $listParameters = ['is_valid'       => null, 'offset'         => $offset, 'limit'          => $limit, 'only_published' => true];

    $countParameters = ['only_published' => true];
}
elseif( $ViewMode == 'valid' )
{
    $listParameters = ['is_valid'       => true, 'offset'         => $offset, 'limit'          => $limit, 'only_published' => true];

    $countParameters = ['is_valid' => true, 'only_published' => true];
}
elseif( $ViewMode == 'invalid' )
{
    $listParameters = ['is_valid'       => false, 'offset'         => $offset, 'limit'          => $limit, 'only_published' => true];

    $countParameters = ['is_valid' => false, 'only_published' => true];
}

$list = eZURL::fetchList( $listParameters );
$listCount = eZURL::fetchListCount( $countParameters );

$viewParameters = ['offset' => $offset, 'limit'  => $limit];


$tpl = eZTemplate::factory();

$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'url_list', $list );
$tpl->setVariable( 'url_list_count', $listCount );
$tpl->setVariable( 'view_mode', $ViewMode );

$Result = [];
$Result['content'] = $tpl->fetch( "design:url/list.tpl" );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/url', 'URL' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/url', 'List' )]];
?>
