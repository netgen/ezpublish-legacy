<?php
/**
 * File containing the ezxFormToken class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezformtoken
 */

/**
 * This class listens to interal kernel events in eZ Publish to validate forms using pr session tokens
 *
 * @See settings/site.ini.append.php for events used.
 * @See doc/Readme.rst for info about extension and about how to modify your ajax code to work with it.
 *
 * @internal
 * @since 4.5.0
 * @version //autogentag//
 * @package ezformtoken
 */
class ezxFormToken
{
    final public const SESSION_KEY = self::class;

    final public const FORM_FIELD = 'ezxform_token';

    final public const REPLACE_KEY = '@$ezxFormToken@';

    /**
     * @var string|null
     */
    static protected $secret;

    /**
     * @var string
     */
    static protected $intention = 'legacy';

    /**
     * @var string Custom Form field, by default set to system default form field (self::FORM_FIELD).
     */
    static protected $formField = self::FORM_FIELD;

    /**
     * @var string
     */
    static protected $token;

    /**
     * @var bool
     */
    static protected $isEnabled = true;

    /**
     * @return string
     */
    static protected function getSecret()
    {
        if ( self::$secret === null )
        {
            self::$secret = eZINI::instance( 'site.ini' )->variable( 'HTMLForms', 'Secret' );
        }

        return self::$secret;
    }

    /**
     * @param string $secret
     */
    static public function setSecret( $secret )
    {
        self::$secret = $secret;
    }

    /**
     * @return string
     */
    static protected function getIntention()
    {
        return self::$intention;
    }

    /**
     * @param string $intention
     */
    static public function setIntention( $intention )
    {
        self::$intention = $intention;
    }

    /**
     * Get the custom form field.
     *
     * @return string
     */
    static protected function getFormField()
    {
        return self::$formField;
    }

    /**
     * Set the custom form field.
     *
     * @param string $formField
     */
    static public function setFormField( $formField )
    {
        self::$formField = $formField;
    }

    /**
     * request/input event listener
     * Checks if form token is valid if user is logged in.
     */
    static public function input( eZURI $uri )
    {
        if ( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST' && empty( $_POST ) )
        {
            eZDebugSetting::writeDebug( 'ezformtoken', 'Input not protected (not POST)', __METHOD__ );
            return null;
        }

        if ( !self::shouldProtectUser() )
        {
            eZDebugSetting::writeDebug( 'ezformtoken', 'Input not protected (not logged in user)', __METHOD__ );
            return null;
        }

        /* Not a safe assumtion, just kept for reference
        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
          && trim( strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) === 'xmlhttprequest' )
        {
            eZDebugSetting::writeDebug( 'ezformtoken', 'Input not protected (ajax request)', __METHOD__ );
            return null;
        }*/

        if ( !empty( $_POST[self::getFormField()] ) )
        {
            $token = $_POST[self::getFormField()];
        }
        // For historical reasons also check the system default form field
        else if ( !empty( $_POST[self::FORM_FIELD] ) )
        {
            $token = $_POST[self::FORM_FIELD];
        }
        // allow ajax calls using POST with other formats than forms (such as
        // json or xml) to still validate using a custom http header
        else if ( !empty( $_SERVER['HTTP_X_CSRF_TOKEN'] ) )
        {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        else
        {
            throw new Exception( 'Missing form token from Request', 404 );
        }

        if ( $token !== self::getToken() )
            throw new Exception( 'Wrong form token found in Request!', 404 );

        eZDebugSetting::writeDebug( 'ezformtoken', 'Input validated, token verified and was correct', __METHOD__ );
    }

    /**
     * response/output event filter
     * Appends tokens to  POST forms if user is logged in.
     *
     * @param string $templateResult ByRef
     * @param bool $filterForms For use when the output has already been filtered, but not for the whole layout.
     *
     * @return mixed|string
     */
    static public function output( $templateResult, $filterForms = true )
    {
        if ( !self::shouldProtectUser() )
        {
            eZDebugSetting::writeDebug( 'ezformtoken', 'Output not protected (not logged in user)', __METHOD__ );
            return $templateResult;
        }

        // We only rewrite pages served with an html/xhtml content type
        $sentHeaders = headers_list();
        foreach ( $sentHeaders as $header )
        {
            // Search for a content-type header that is NOT HTML
            // Note the Content-Type header will not be included in
            // headers_list() unless it has been explicitly set from PHP.
            if (stripos( $header, 'Content-Type:' ) === 0 &&
                !str_contains( $header, 'text/html' ) &&
                !str_contains( $header, 'application/xhtml+xml' )   )
           {
               eZDebugSetting::writeDebug( 'ezformtoken', 'Output not protected (Content-Type is not html/xhtml)', __METHOD__ );
               return $templateResult;
            }
        }

        $token = self::getToken();
        $customfield = self::getFormField();
        $defaultField = self::FORM_FIELD;
        $replaceKey = self::REPLACE_KEY;

        eZDebugSetting::writeDebug( 'ezformtoken', 'Output protected (all forms will be modified)', __METHOD__ );

        // Inject token for programmatical use (also system default for historical reasons)
        // If document has head tag, insert in a html5 valid and semi standard way
        if ( str_contains( $templateResult, '<head>' ) )
        {
            $templateResult = str_replace(
                '<head>',
                "<head>\n"
                . "<meta name=\"csrf-param\" content=\"{$customfield}\" />\n"
                . "<meta name=\"csrf-token\" id=\"{$customfield}_js\" title=\"{$token}\" content=\"{$token}\" />\n"
                . ($defaultField !== $customfield ? "<meta name=\"csrf-token-x\" id=\"{$defaultField}_js\" title=\"{$token}\" content=\"{$token}\" />\n" : ''),
                $templateResult
            );
        }
        // else fallback to hidden span inside body
        else
        {
            $templateResult = preg_replace(
                '/(<body[^>]*>)/i',
                '\\1' . "\n<span style='display:none;' id=\"{$customfield}_js\" title=\"{$token}\"></span>\n"
                . ($defaultField !== $customfield ? "\n<span style='display:none;' id=\"{$defaultField}_js\" title=\"{$token}\"></span>\n" : ''),
                $templateResult
            );
        }

        // For forms we set the custom field which will be sent back to this class and evaluated
        if ( $filterForms )
        {
            $templateResult = preg_replace(
                '/(<form\W[^>]*\bmethod=(\'|"|)POST(\'|"|)\b[^>]*>)/i',
                '\\1' . "\n<input type=\"hidden\" name=\"{$customfield}\" value=\"{$token}\" />\n",
                $templateResult
            );
        }

        return str_replace( $replaceKey, $token, $templateResult );
    }

    /**
     * session/regenerate event handler, clears form token when users
     * logs out / in.
     */
    static public function reset()
    {
        eZDebugSetting::writeDebug( 'ezformtoken', 'Reset form token', __METHOD__ );
        self::$token = null;
    }

    /**
     * Gets the user token from session if it exists or create+store
     * it in session.
     *
     * @return string|null
     */
    static public function getToken()
    {
        if ( self::$token === null )
        {
            self::$token = sha1( self::getSecret() . self::getIntention() . session_id() );
        }

        return self::$token;
    }

    /**
     * Enables/Disables CSRF protection.
     *
     * @param bool $isEnabled
     */
    static public function setIsEnabled( $isEnabled )
    {
        self::$isEnabled = (bool)$isEnabled;
    }

    static public function isEnabled()
    {
        return (bool)self::$isEnabled;
    }

    /**
     * Figures out if current user should be protected or not
     * based on if (s)he has a session and is logged in.
     *
     * @return bool
     */
    static protected function shouldProtectUser()
    {
        if ( !self::$isEnabled )
            return false;

        if ( !eZSession::hasStarted() )
            return false;

        if ( !eZUser::isCurrentUserRegistered() )
            return false;

        return true;
    }
}

?>
