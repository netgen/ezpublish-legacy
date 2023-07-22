<?php
/**
 * File containing the ezjscCacheManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class ezjscCacheManager
{
    /**
     * Recursively expires the ezjscore public cache folder
     */
    public static function clearCache( array $cacheItem )
    {
        eZClusterFileHandler::instance()->fileDeleteByDirList(
            ['javascript', 'stylesheets'],
            eZSys::cacheDirectory() . '/' . $cacheItem['path'], ''
        );
    }
}
