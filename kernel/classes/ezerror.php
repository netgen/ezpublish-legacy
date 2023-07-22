<?php
/**
 * File containing the eZError class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  Contains all the basic kernel and kernel related error codes.
*/

class eZError
{

/*!
 Access denied to object or module.
*/
final public const KERNEL_ACCESS_DENIED = 1;
/*!
 The object could not be found.
*/
final public const KERNEL_NOT_FOUND = 2;
/*!
 The object is not available.
*/
final public const KERNEL_NOT_AVAILABLE = 3;
/*!
 The object is moved.
*/
final public const KERNEL_MOVED = 4;
/*!
 The language is not found.
*/
final public const KERNEL_LANGUAGE_NOT_FOUND = 5;

/*!
 The module could not be found.
*/
final public const KERNEL_MODULE_NOT_FOUND = 20;
/*!
 The module view could not be found.
*/
final public const KERNEL_MODULE_VIEW_NOT_FOUND = 21;
/*!
 The module or view is not enabled.
*/
final public const KERNEL_MODULE_DISABLED = 22;


/*!
 No database connection
*/
final public const KERNEL_NO_DB_CONNECTION = 50;

//Shop system error codes
final public const SHOP_OK = 0;
final public const SHOP_NOT_A_PRODUCT = 1;
final public const SHOP_BASKET_INCOMPATIBLE_PRODUCT_TYPE = 2;
final public const SHOP_PREFERRED_CURRENCY_DOESNOT_EXIST = 3;
final public const SHOP_PREFERRED_CURRENCY_INACTIVE = 4;


}

?>
