<?php
/**
 * File containing the ezpOauthRequired class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This result type is used to signal a HTTP basic auth header
 */
class ezpOauthRequired implements ezcMvcResultStatusObject
{
    final public const DEFAULT_REALM = 'eZ Publish REST';

    /**
     * @param string $realm
     * @param string $errorType
     * @param string $errorMessage
     */
    public function __construct(
        /**
         * The realm is the unique ID to identify a login area
         */
        public $realm,
        /**
         * The error type identifier as defined per section 5.2.1 of oauth2.0 #10
         */
        public $errorType = null,
        /**
         * An optional human-readable error message.
         */
        public $errorMessage = null
    )
    {
    }

    /**
     * Uses the passed in $writer to set the HTTP authentication header.
     *
     * @param ezcMvcResponseWriter $writer
     */
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers['HTTP/1.1 ' . ezpOauthErrorType::httpCodeforError( $this->errorType )] = "";
            $writer->headers['WWW-Authenticate'] = "OAuth realm='{$this->realm}'{$this->createErrorString()}";
        }

        if ( isset( $this->errorType) )
        {
            $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
            $body = ['error' => $this->errorType];

            if ( isset( $this->errorMessage ) )
                $body['error_description'] = $this->errorMessage;

            $writer->response->body = json_encode( $body, JSON_THROW_ON_ERROR );
        }
    }

    /**
     * Creates for use in authentcation challenge header
     *
     * @return string
     */
    protected function createErrorString()
    {
        $str = '';
        if ( $this->errorType !== null )
        {
            $str .= ", error='{$this->errorType}'";
        }

        if ( $this->errorMessage !== null )
        {
            $str .= ", error_description='{$this->errorMessage}'";
        }
        return $str;
    }
}
?>
