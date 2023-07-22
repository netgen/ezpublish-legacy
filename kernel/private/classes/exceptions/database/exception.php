<?php
/**
 * File containing the eZDBException class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Class representing a the top class for any
 * database related exception
 *
 * @version //autogentag//
 * @package kernel
 */
class eZDBException extends ezcBaseException
{
    /**
     * Constructs a new eZDBException with $message and $code
     *
     * @param string $originalMessage
     * @param int $code
     */
    public function __construct( /**
     * Original message, before escaping
     */
    public $originalMessage, $code = 0 )
    {
        $this->code = $code;

        if ( php_sapi_name() == 'cli' )
        {
            $this->message = $originalMessage;
        }
        else
        {
            $this->message = htmlspecialchars( $originalMessage, ENT_QUOTES, 'UTF-8' );
        }
    }
}
?>
