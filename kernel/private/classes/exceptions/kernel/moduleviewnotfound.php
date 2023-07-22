<?php
/**
 * File containing the ezpModuleViewNotFound exception.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Exception occuring when a module/view is not found.
 *
 * @package kernel
 */
class ezpModuleViewNotFound extends Exception
{
    /**
     * Constructor
     *
     * @param string $moduleName
     * @param string $viewName
     */
    public function __construct(public $moduleName, public $viewName)
    {
    }
}
