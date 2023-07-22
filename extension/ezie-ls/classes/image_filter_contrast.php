<?php
/**
 * File containing the eZIEImageFilterContrast class.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
class eZIEImageFilterContrast extends eZIEImageAction
{
    /**
     * Creates a contrast filter
     * @param int $value Contrast value
     * @return array( ezcImageFilter )
     */
    static function filter( $value = 0, $region = null )
    {
        return [new ezcImageFilter(
            'contrast',
            ['value' => $value, 'region' => $region]
        )];
    }
}

?>