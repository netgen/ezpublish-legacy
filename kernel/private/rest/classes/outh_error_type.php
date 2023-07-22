<?php
/**
 * File containing the ezpOauthErrorType
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpOauthErrorType
{
    final public const INVALID_REQUEST = 'invalid_request';
    final public const INVALID_TOKEN = 'invalid_token';
    final public const EXPIRED_TOKEN = 'expired_token';
    final public const INSUFFICIENT_SCOPE = 'insufficient_scope';

    public static function httpCodeforError( $error )
    {
        return match ($error) {
            self::INVALID_REQUEST => ezpHttpResponseCodes::BAD_REQUEST,
            self::INVALID_TOKEN, self::EXPIRED_TOKEN => ezpHttpResponseCodes::UNAUTHORIZED,
            self::INSUFFICIENT_SCOPE => ezpHttpResponseCodes::FORBIDDEN,
            default => ezpHttpResponseCodes::SERVER_ERROR,
        };
    }
}
?>
