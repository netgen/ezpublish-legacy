#!/usr/bin/env php
<?php
/**
 * File containing the ezcache.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

$helper = new eZCacheHelper(
    $cli = eZCLI::instance(),
    $script = eZScript::instance(
        ['description' => "eZ Publish Cache Handler\n" .
                         "Allows for easy clearing of Cache files\n" .
                         "\n" .
                         "./bin/php/ezcache.php --clear-tag=content", 'use-session' => false, 'use-modules' => false, 'use-extensions' => true]
    )
);

$script->startup();

$options = $script->getOptions( "[clear-tag:][clear-id:][clear-all]" . /*[purge-tag:][purge-id:][purge-all]*/ "[iteration-sleep:][iteration-max:][expiry:][list-tags][list-ids][purge]",
                                "",
                                ['clear-tag' => 'Clears all caches related to a given tag, separate multiple tags with a comma', 'clear-id' => 'Clears all caches related to a given id, separate multiple ids with a comma', 'clear-all' => 'Clears all caches', 'purge' => 'Enforces purging of cache items which ensures that specified entries are physically removed (Useful for saving diskspace). Used together with the clear-* options.', 'iteration-sleep' => 'Amount of seconds to sleep between each iteration when performing a purge operation, can be a float.', 'iteration-max' => 'Amount of items to remove in each iteration when performing a purge operation.', 'expiry' => 'Date or relative time which specifies when cache items are to be considered expired, e.g \'now\', \'-2 days\' or \'last monday\'. Can only be used together with --purge.', 'list-tags' => 'Lists all available tags', 'list-ids' => 'Lists all available ids'] );

$script->initialize();

$purgeSleep = false;
if ( $options['iteration-sleep'] )
{
    $purgeSleep = (int)($options['iteration-sleep']*1_000_000); // Turn into microseconds
}
$purgeMax = false;
if ( $options['iteration-max'] )
{
    $purgeMax = (int)$options['iteration-max'];
}
$purgeExpiry = false;
if ( $options['expiry'] )
{
    if ( !$options['purge'] )
    {
        $cli->error( "--expiry can only be used together with --purge" );
        $script->shutdown( 1 );
    }
    $expiryText = trim( (string) $options['expiry'] );
    $purgeExpiry = strtotime( $expiryText );
    if ( $purgeExpiry == -1 || $purgeExpiry === false )
    {
        $cli->error( "Invalid date supplied to --expiry, '$expiryText'" );
        $script->shutdown( 1 );
    }
}
$purge = false;
if ( $options['purge'] )
{
    if ( !$options['expiry'] ) // If --expiry is not specified, purge everything
        $purgeExpiry = time();
    $purge = true;
}
$noAction = true;

$cacheList = eZCache::fetchList();

if ( $options['list-tags'] )
{
    $noAction = false;
    $tagList = eZCache::fetchTagList( $cacheList );
    if ( $script->verboseOutputLevel() > 0 )
    {
        $cli->output( "The following tags are defined:" );
        $column = 0;
        foreach ( $tagList as $tagName )
        {
            $len = strlen( (string) $tagName );
            if ( $len > $column )
                $column = $len;
        }
        $column += 2;
        foreach ( $tagList as $tagName )
        {
            $cacheEntries = eZCache::fetchByTag( $tagName, $cacheList );
            $cli->output( $cli->stylize( 'emphasize', $tagName ) . ':', false );
            $i = 0;
            foreach ( $cacheEntries as $cacheEntry )
            {
                if ( $i > 0 )
                    $cli->output( str_repeat( ' ', $column ), false );
                else
                    $cli->output( str_repeat( ' ', $column - strlen( (string) $tagName ) - 1 ), false );
                $cli->output( $cacheEntry['name'] );
                ++$i;
            }
        }
    }
    else
    {
        $cli->output( "The following tags are defined: (use --verbose for more details)" );
        $cli->output( $cli->stylize( 'emphasize', implode( ', ', $tagList ) ) );
    }
    $script->shutdown( 0 );
}

if ( $options['list-ids'] )
{
    $noAction = false;
    if ( $script->verboseOutputLevel() > 0 )
    {
        $cli->output( "The following ids are defined:" );
        $column = 0;
        foreach ( $cacheList as $cacheInfo )
        {
            $len = strlen( (string) $cacheInfo['id'] );
            if ( $len > $column )
                $column = $len;
        }
        $column += 2;
        foreach ( $cacheList as $cacheInfo )
        {
            $cli->output( $cli->stylize( 'emphasize', $cacheInfo['id'] ) . ':', false );
            $cli->output( str_repeat( ' ', $column - strlen( (string) $cacheInfo['id'] ) - 1 ), false );
            $cli->output( $cacheInfo['name'] );
        }
    }
    else
    {
        $cli->output( "The following ids are defined: (use --verbose for more details)" );
        $cli->output( $cli->stylize( 'emphasize', implode( ', ', eZCache::fetchIDList( $cacheList ) ) ) );
    }
    $script->shutdown( 0 );
}

if ( $options['clear-all'] )
{
    $noAction = false;
    if ( $purge )
        $helper->purgeItems( $cacheList, false, $purgeSleep, $purgeMax, $purgeExpiry );
    else
        $helper->clearItems( $cacheList, false );
    $script->shutdown( 0 );
}

if ( $options['clear-tag'] )
{
    $noAction = false;
    $tagName = $options['clear-tag'];
    $cacheEntries = eZCache::fetchByTag( $tagName, $cacheList );
    if ( $purge )
        $helper->purgeItems( $cacheEntries, $tagName, $purgeSleep, $purgeMax, $purgeExpiry );
    else
        $helper->clearItems( $cacheEntries, $tagName );
}

if ( $options['clear-id'] )
{
    $noAction = false;
    $idName = $options['clear-id'];
    $missingIDList = [];
    $cacheEntries = [];
    foreach ( explode( ',', (string) $idName ) as $id )
    {
        $cacheEntry = eZCache::fetchByID( $id, $cacheList );
        if ( $cacheEntry )
        {
            $cacheEntries[] = $cacheEntry;
        }
        else
        {
            $missingIDList[] = $id;
        }
    }
    if ( count( $missingIDList ) > 0 )
    {
        $cli->warning( 'No such cache ID: ' . $cli->stylize( 'emphasize', implode( ', ', $missingIDList ) ) );
        $script->shutdown( 1 );
    }
    if ( $options['clear-id'] )
    {
        if ( $purge )
            $helper->purgeItems( $cacheEntries, $idName, $purgeSleep, $purgeMax, $purgeExpiry );
        else
            $helper->clearItems( $cacheEntries, $idName );
    }
    else
    {
        $script->shutdown( 1, "Internal error" );
    }
}

if ( $noAction )
{
    $cli->warning( "To clear caches use one of the options --clear-id, --clear-tag or --clear-all. Use --help option for more details." );
}

$script->shutdown();
