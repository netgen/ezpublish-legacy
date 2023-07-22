<?php
/**
 * File containing the ezpRestHttpResponse status object.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpRestHttpResponse implements ezcMvcResultStatusObject
{
    public function __construct(public $code = null, public $message = null)
    {
    }

    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers["HTTP/1.1 " . $this->code] = $this->message;
        }

        if ( $this->message !== null )
        {
            $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
            $writer->response->body = json_encode( ['error_message' => $this->message], JSON_THROW_ON_ERROR );
        }
    }
}
?>
