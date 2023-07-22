<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

if ( eZPreferences::value( 'admin_search_stats_limit' ) )
{
    $limit = match (eZPreferences::value( 'admin_search_stats_limit' )) {
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

$http = eZHTTPTool::instance();
$module = $Params['Module'];

if ( $module->isCurrentAction( 'ResetSearchStats' ) )
{
    eZSearchLog::removeStatistics();
}

$viewParameters = ['offset' => $offset, 'limit'  => $limit];
$tpl = eZTemplate::factory();

$db = eZDB::instance();
$query = "SELECT count(*) as count FROM ezsearch_search_phrase";
$searchListCount = $db->arrayQuery( $query );

$mostFrequentPhraseArray = eZSearchLog::mostFrequentPhraseArray( $viewParameters );

$tpl->setVariable( "view_parameters", $viewParameters );
$tpl->setVariable( "most_frequent_phrase_array", $mostFrequentPhraseArray );
$tpl->setVariable( "search_list_count", $searchListCount[0]['count'] );

$Result = [];
$Result['content'] = $tpl->fetch( "design:search/stats.tpl" );
$Result['path'] = [['text' => ezpI18n::tr( 'kernel/search', 'Search stats' ), 'url' => false]];

?>
