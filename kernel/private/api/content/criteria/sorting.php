<?php
/**
 * File containing ezpContentSortingCriteria class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Sorting criteria
 * @package API
 */
class ezpContentSortingCriteria implements ezpContentCriteriaInterface, \Stringable
{
    /**
     * Sort order
     * @var bool true means ASC, false means DESC, just like in the fetch content/list function
     */
    private readonly bool $sortOrder;

    /**
     * @param string $sortKey
     */
    public function __construct( /**
     * Any of the non-attribute supported fields
     * @see http://goo.gl/xvJMM
     */
    private $sortKey, $sortOrder )
    {
        $this->sortOrder = ( $sortOrder == 'asc' ) ? true : false;
    }

    public function translate()
    {
        return ['type'      => 'param', 'name'      => ['SortBy'], 'value'     => [[$this->sortKey, $this->sortOrder]]];
    }

    public function __toString(): string
    {
        $sortOrderString = $this->sortOrder ? 'asc' : 'desc';
        return 'With sortKey '.$this->sortKey.' and '.$sortOrderString.' sortOrder';
    }
}
?>
