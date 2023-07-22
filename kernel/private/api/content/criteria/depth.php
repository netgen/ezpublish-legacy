<?php
/**
 * File containing ezpContentDepthCriteria class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Depth criteria
 * @package API
 */
class ezpContentDepthCriteria implements ezpContentCriteriaInterface, \Stringable
{
    /**
     * Maximum depth to dig while fetching
     */
    private readonly int $depth;

    public function __construct( $depth )
    {
        $this->depth = (int)$depth;
    }

    public function translate()
    {
        return ['type'      => 'param', 'name'      => ['Depth'], 'value'     => [$this->depth]];
    }

    public function __toString(): string
    {
        return 'With depth '.$this->depth;
    }
}
?>
