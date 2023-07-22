<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
 Get search limit
 */
function pageLimit( $searchPageLimit )
{
    return match ($searchPageLimit) {
        1 => 5,
        3 => 20,
        4 => 30,
        5 => 50,
        default => 10,
    };
}

$http = eZHTTPTool::instance();

$Module = $Params['Module'];
$Offset = $Params['Offset'];

if ( !is_numeric( $Offset ) )
    $Offset = 0;

$searchPageLimit = 2;
$tpl = eZTemplate::factory();
$ini = eZINI::instance();
$useSearchCode = $ini->variable( 'SearchSettings', 'SearchViewHandling' ) == 'default';
$logSearchStats = $ini->variable( 'SearchSettings', 'LogSearchStats' ) == 'enabled';

if ( $http->hasVariable( 'BrowsePageLimit' ) )
{
    $pageLimit = (int)$http->variable( 'BrowsePageLimit' );
}
else
{
    if ( $http->hasVariable( 'SearchPageLimit' ) )
    {
        $searchPageLimit = (int)$http->variable( 'SearchPageLimit' );
    }
    $pageLimit = pageLimit( $searchPageLimit );
}

$maximumSearchLimit = $ini->variable( 'SearchSettings', 'MaximumSearchLimit' );
if ( $pageLimit > $maximumSearchLimit )
    $pageLimit = $maximumSearchLimit;

$searchText = '';
if ( $http->hasVariable( "SearchText" ) )
{
    $searchText = $http->variable( "SearchText" );
}

$searchSectionID = -1;
if ( $http->hasVariable( "SectionID" ) )
{
    $searchSectionID = (int)$http->variable( "SectionID" );
}

$searchTimestamp = false;
if ( $http->hasVariable( 'SearchTimestamp' ) and
     $http->variable( 'SearchTimestamp' ) )
{
    $searchTimestamp = (int)$http->variable( 'SearchTimestamp' );
}

$searchType = "fulltext";
if ( $http->hasVariable( "SearchType" ) )
{
    $searchType = $http->variable( "SearchType" );
}

$subTreeArray = [];
if ( $http->hasVariable( "SubTreeArray" ) )
{
    if ( is_array( $http->variable( "SubTreeArray" ) ) )
        $subTreeList = $http->variable( "SubTreeArray" );
    else
        $subTreeList = [$http->variable( "SubTreeArray" )];
    foreach ( $subTreeList as $subTreeItem )
    {
        if ( is_numeric( $subTreeItem ) && $subTreeItem > 0 )
            $subTreeArray[] = $subTreeItem;
    }
}

$Module->setTitle( "Search for: $searchText" );

if ( $useSearchCode )
{
    $sortArray = [['attribute', true, 153], ['priority', true]];
    $searchResult = eZSearch::search( $searchText, ["SearchType" => $searchType, "SearchSectionID" => $searchSectionID, "SearchSubTreeArray" => $subTreeArray, 'SearchTimestamp' => $searchTimestamp, "SearchLimit" => $pageLimit, "SearchOffset" => $Offset] );
}

if ( $searchSectionID != -1 )
{
    $res = eZTemplateDesignResource::instance();
    $section = eZSection::fetch( $searchSectionID );
    $keyArray = [['section', $searchSectionID], ['section_identifier', $section->attribute( 'identifier' )]];
    $res->setKeys( $keyArray );
}

$viewParameters = ['offset' => $Offset];

$searchData = false;
$tpl->setVariable( "search_data", $searchData );
$tpl->setVariable( "search_section_id", $searchSectionID );
$tpl->setVariable( "search_subtree_array", $subTreeArray );
$tpl->setVariable( 'search_timestamp', $searchTimestamp );
$tpl->setVariable( "search_text", $searchText );
$tpl->setVariable( 'search_page_limit', $searchPageLimit );

$tpl->setVariable( "view_parameters", $viewParameters );
$tpl->setVariable( 'use_template_search', !$useSearchCode );

if ( $http->hasVariable( 'Mode' ) && $http->variable( 'Mode' ) == 'browse' )
{
    if( !isset( $searchResult ) )
        $searchResult = eZSearch::search( $searchText, ["SearchType" => $searchType, "SearchSectionID" => $searchSectionID, "SearchSubTreeArray" => $subTreeArray, 'SearchTimestamp' => $searchTimestamp, "SearchLimit" => $pageLimit, "SearchOffset" => $Offset] );
    $sys = eZSys::instance();
    $searchResult['RequestedURI'] = "content/search";
//    $searchResult['RequestedURISuffix'] = $sys->serverVariable( "QUERY_STRING" );


    $searchResult['RequestedURISuffix'] = 'SearchText=' . urlencode ( (string) $searchText ) . ( isset( $subTreeArray[0] ) ? '&SubTreeArray=' . $subTreeArray[0] : '' ) . ( ( $searchTimestamp > 0 ) ?  '&SearchTimestamp=' . $searchTimestamp : '' ) . '&BrowsePageLimit=' . $pageLimit . '&Mode=browse';
    return $Module->run( 'browse',[],["NodeList" => $searchResult, "Offset" => $Offset, "NodeID" => isset( $subTreeArray[0] ) && $subTreeArray[0] != 1 ? $subTreeArray[0] : null] );
}

// --- Compatibility code start ---
if ( $useSearchCode )
{
    $tpl->setVariable( "offset", $Offset );
    $tpl->setVariable( "page_limit", $pageLimit );
    $tpl->setVariable( "search_text_enc", urlencode( (string) $searchText ) );
    $tpl->setVariable( "search_result", $searchResult["SearchResult"] );
    $tpl->setVariable( "search_count", $searchResult["SearchCount"] );
    $tpl->setVariable( "stop_word_array", $searchResult["StopWordArray"] );
    if ( isset( $searchResult["SearchExtras"] ) )
    {
        $tpl->setVariable( "search_extras", $searchResult["SearchExtras"] );
    }
}
else
{
    $tpl->setVariable( "offset", false );
    $tpl->setVariable( "page_limit", false );
    $tpl->setVariable( "search_text_enc", false );
    $tpl->setVariable( "search_result", false );
    $tpl->setVariable( "search_count", false );
    $tpl->setVariable( "stop_word_array", false );
}
// --- Compatibility code end ---

$Result = [];
$Result['content'] = $tpl->fetch( "design:content/search.tpl" );
$Result['path'] = [['text' => ezpI18n::tr( 'kernel/content', 'Search' ), 'url' => false]];

$searchData = false;
if ( !$useSearchCode )
{
    if ( $tpl->hasVariable( "search_data" ) )
    {
        $searchData = $tpl->variable( "search_data" );
    }
}
else
{
    $searchData = $searchResult;
}

if ( $logSearchStats and
     trim( (string) $searchText ) != "" and
     is_array( $searchData ) and
     array_key_exists( 'SearchCount', $searchData ) and
     is_numeric( $searchData['SearchCount'] ) )
{
    eZSearchLog::addPhrase( $searchText, $searchData["SearchCount"] );
}

?>
