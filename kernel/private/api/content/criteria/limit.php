<?php
/**
 * File containing ezpContentLimitCriteria class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This class allows for configuration of an offset/limit based criteria
 * @package API
 */
class ezpContentLimitCriteria implements ezpContentCriteriaInterface, \Stringable
{
    /**
     * Current offset
     */
    private int $offset = 0;

    /**
     * Current limit
     */
    private ?int $limit = null;

    public function __construct()
    {
    }

    /**
     * Sets the offset criteria
     * @param $offset
     * @return ezpContentLimitCriteria Current limit criteria object
     */
    public function offset( $offset )
    {
        $offset = (int)$offset;
        if( $offset >= 0 )
            $this->offset = $offset;

        return $this;
    }

    /**
     * Sets the limit criteria
     * @param $limit
     * @return ezpContentLimitCriteria Current limit criteria object
     */
    public function limit( $limit )
    {
        $limit = (int)$limit;
        if( $limit > 0 )
            $this->limit = $limit;

        return $this;
    }

    public function translate()
    {
        $aTranslation = ['type'      => 'param', 'name'      => ['Offset', 'Limit'], 'value'     => [$this->offset, $this->limit]];

        return $aTranslation;
    }

    public function __toString(): string
    {
        return 'With offset : '.$this->offset.' / limit : '.$this->limit;
    }
}
?>
