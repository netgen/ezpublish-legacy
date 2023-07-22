<?php
/**
 * File containing the eZSMTPTransport class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZSMTPTransport ezsmtptransport.php
  \brief The class eZSMTPTransport does

*/

class eZSMTPTransport extends eZMailTransport
{
    function sendMail( eZMail $mail )
    {
        $ini = eZINI::instance();
        $parameters = [];
        $parameters['host'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['helo'] = $ini->variable( 'MailSettings', 'SenderHost' );
        $parameters['port'] = $ini->variable( 'MailSettings', 'TransportPort' );
        $parameters['connectionType'] = $ini->variable( 'MailSettings', 'TransportConnectionType' );
        $user = $ini->variable( 'MailSettings', 'TransportUser' );
        $password = $ini->variable( 'MailSettings', 'TransportPassword' );
        if ( $user and
             $password )
        {
            $parameters['auth'] = true;
            $parameters['user'] = $user;
            $parameters['pass'] = $password;
        }

        /* If email sender hasn't been specified or is empty
         * we substitute it with either MailSettings.EmailSender or AdminEmail.
         */
        if ( !$mail->senderText() )
        {
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$emailSender )
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );

            eZMail::extractEmail( $emailSender, $emailSenderAddress, $emailSenderName );

            if ( !eZMail::validate( $emailSenderAddress ) )
                $emailSender = false;

            if ( $emailSender )
                $mail->setSenderText( $emailSender );
        }

        $excludeHeaders = $ini->variable( 'MailSettings', 'ExcludeHeaders' );
        if ( (is_countable($excludeHeaders) ? count( $excludeHeaders ) : 0) > 0 )
            $mail->Mail->appendExcludeHeaders( $excludeHeaders );

        $options = new ezcMailSmtpTransportOptions();
        if( $parameters['connectionType'] )
        {
            $options->connectionType = $parameters['connectionType'];
        }
        $smtp = new ezcMailSmtpTransport( $parameters['host'], $user, $password,
        $parameters['port'], $options );

        // If in debug mode, send to debug email address and nothing else
        if ( $ini->variable( 'MailSettings', 'DebugSending' ) == 'enabled' )
        {
            $mail->Mail->to = [new ezcMailAddress( $ini->variable( 'MailSettings', 'DebugReceiverEmail' ) )];
            $mail->Mail->cc = [];
            $mail->Mail->bcc = [];
        }

        // send() from ezcMailSmtpTransport doesn't return anything (it uses exceptions in case
        // something goes bad)
        try
        {
            $smtp->send( $mail->Mail );
        }
        catch ( ezcMailException $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return false;
        }

        // return true in case of no exceptions
        return true;
    }
}

?>
