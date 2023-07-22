<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];
$urlID = $Params['ID'];

if( eZPreferences::value( 'admin_url_view_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_url_view_limit' )) {
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

$url = eZURL::fetch( $urlID );
if ( !$url )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$link = $url->attribute( 'url' );
if ( preg_match("/^(http:)/i", (string) $link ) or
     preg_match("/^(ftp:)/i", (string) $link ) or
     preg_match("/^(https:)/i", (string) $link ) or
     preg_match("/^(file:)/i", (string) $link ) or
     preg_match("/^(mailto:)/i", (string) $link ) )
{
    // No changes
}
else
{
    $domain = getenv( 'HTTP_HOST' );
    $protocol = eZSys::serverProtocol();

    $preFix = $protocol . "://" . $domain;
    $preFix .= eZSys::wwwDir();

    $link = preg_replace("/^\//", "", (string) $link );
    $link = $preFix . "/" . $link;
}

$viewParameters = ['offset' => $offset, 'limit'  => $limit];
$http = eZHTTPTool::instance();
$objectList = eZURLObjectLink::fetchObjectVersionList( $urlID, $viewParameters );
$urlViewCount= eZURLObjectLink::fetchObjectVersionCount( $urlID );

if ( $Module->isCurrentAction( 'EditObject' ) )
{
    if ( $http->hasPostVariable( 'ObjectList' ) )
    {
        $versionID = $http->postVariable( 'ObjectList' );
        $version = eZContentObjectVersion::fetch( $versionID );
        $contentObjectID = $version->attribute( 'contentobject_id' );
        $versionNr = $version->attribute( 'version' );
        $Module->redirect( 'content', 'edit', [$contentObjectID, $versionNr] );
    }
}


$tpl = eZTemplate::factory();

$tpl->setVariable( 'Module', $Module );
$tpl->setVariable( 'url_object', $url );
$tpl->setVariable( 'full_url', $link );
$tpl->setVariable( 'object_list', $objectList );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'url_view_count', $urlViewCount );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:url/view.tpl' );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/url', 'URL' )], ['url' => false, 'text' => ezpI18n::tr( 'kernel/url', 'View' )]];

?>
