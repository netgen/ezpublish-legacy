<?php
/**
 * File containing the ezpOauthTokenEndpointErrorType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpOauthTokenEndpointErrorType
{
    final public const INVALID_REQUEST = "invalid_request";
    final public const INVALID_CLIENT = "invalid_client";
    final public const UNAUTHORIZED_CLIENT = "unauthorized_client";
    final public const INVALID_GRANT = "invalid_grant";
    final public const UNSUPPORTED_GRANT_TYPE = "unsupported_grant_type";
    final public const INVALID_SCOPE = "invalid_scope";

    public static function httpCodeForError( $error )
    {
        return match ($error) {
            self::UNAUTHORIZED_CLIENT => ezpHttpResponseCodes::UNAUTHORIZED,
            default => ezpHttpResponseCodes::BAD_REQUEST,
        };
    }
}
