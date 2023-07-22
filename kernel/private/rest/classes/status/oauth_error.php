<?php
/**
 * File containing the ezpRestOauthErrorStatus class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpRestOauthErrorStatus implements ezcMvcResultStatusObject
{
    public function __construct(public $errorType = null, public $message = null)
    {
    }

    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers["HTTP/1.1 " . ezpOauthErrorType::httpCodeForError( $this->errorType )] = "";
        }

        if ( $this->message !== null )
        {
            $writer->response->body = $this->message;
        }
    }
}
?>
