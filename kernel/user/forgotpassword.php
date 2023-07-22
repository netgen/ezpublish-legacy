<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$tpl = eZTemplate::factory();
$tpl->setVariable( 'generated', false );
$tpl->setVariable( 'wrong_email', false );
$tpl->setVariable( 'link', false );
$tpl->setVariable( 'wrong_key', false );

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$hashKey = $Params["HashKey"];
$ini = eZINI::instance();

if ( strlen( (string) $hashKey ) == 32 )
{
    $forgotPasswdObj = eZForgotPassword::fetchByKey( $hashKey );
    if ( $forgotPasswdObj )
    {
        $userID = $forgotPasswdObj->attribute( 'user_id' );
        $user   = eZUser::fetch( $userID  );
        $email  = $user->attribute( 'email' );

        $ini = eZINI::instance();
        $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
        $newPassword = eZUser::createPassword( $passwordLength );

        $userToSendEmail = $user;

        $db = eZDB::instance();
        $db->begin();

        // Change user password
        if ( eZOperationHandler::operationIsAvailable( 'user_password' ) )
        {
            $operationResult = eZOperationHandler::execute( 'user',
                                                            'password', ['user_id'    => $userID, 'new_password'  => $newPassword] );
        }
        else
        {
            eZUserOperationCollection::password( $userID, $newPassword );
        }

        $receiver = $email;
        $mail = new eZMail();
        if ( !$mail->validate( $receiver ) )
        {
        }

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'user', $userToSendEmail );
        $tpl->setVariable( 'object', $userToSendEmail->attribute( 'contentobject' ) );
        $tpl->setVariable( 'password', $newPassword );

        $templateResult = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $emailSender );
        $mail->setReceiver( $receiver );
        $subject = ezpI18n::tr( 'kernel/user/register', 'Registration info' );
        if ( $tpl->hasVariable( 'subject' ) )
            $subject = $tpl->variable( 'subject' );
        if ( $tpl->hasVariable( 'content_type' ) )
            $mail->setContentType( $tpl->variable( 'content_type' ) );
        $mail->setSubject( $subject );
        $mail->setBody( $templateResult );
        $mailResult = eZMailTransport::send( $mail );
        $tpl->setVariable( 'generated', true );
        $tpl->setVariable( 'email', $email );
        $forgotPasswdObj->remove();
        $db->commit();
    }
    else
    {
        $tpl->setVariable( 'wrong_key', true );
    }
}
else if ( strlen( (string) $hashKey ) > 4 )
{
    $tpl->setVariable( 'wrong_key', true );
}

if ( $module->isCurrentAction( "Generate" ) )
{
    $ini = eZINI::instance();
    $passwordLength = $ini->variable( "UserSettings", "GeneratePasswordLength" );
    $password = eZUser::createPassword( $passwordLength );
    $passwordConfirm = $password;

//    $http->setSessionVariable( "GeneratedPassword", $password );

    if ( $module->hasActionParameter( "Email" ) )
    {
        $email = $module->actionParameter( "Email" );
        if ( trim( (string) $email ) != "" )
        {
            $users = eZPersistentObject::fetchObjectList( eZUser::definition(),
                                                       null,
                                                       ['email' => $email],
                                                       null,
                                                       null,
                                                       true );
        }

        $random_bytes = false;
        if ( function_exists( "openssl_random_pseudo_bytes" ) )
        {
            $is_crypto_strong = false;
            $random_bytes = openssl_random_pseudo_bytes( 32, $is_crypto_strong );
            if ( $random_bytes === false )
            {
                eZDebug::writeWarning('openssl_random_pseudo_bytes() cannot generate random data, falling back to insecure mt_rand(). ' .
                    'Please check your PHP installation.' );
            }
            else if ( $is_crypto_strong === false )
            {
                eZDebug::writeWarning('openssl_random_pseudo_bytes() could not use a cryptographically strong algorithm. ' .
                    'Please check your PHP installation.' );
            }
        }
        else
        {
            eZDebug::writeWarning('openssl_random_pseudo_bytes() is not available, falling back to insecure mt_rand(). ' .
                'Please install the openssl PHP extension.' );
        }
        if ( $random_bytes === false )
        {
            $random_bytes = random_int(0, mt_getrandmax()); // Not secure, but should not happen since SSL is required, and anyway admins have been warned.
        }

        if ( isset($users) && (is_countable($users) ? count($users) : 0) > 0 )
        {
            $user   = $users[0];
            $time   = time();
            $userID = $user->id();
            $hashKey = md5($userID . ':' . microtime() . ':' . $random_bytes );

            // Create forgot password object
            if ( eZOperationHandler::operationIsAvailable( 'user_forgotpassword' ) )
            {
                $operationResult = eZOperationHandler::execute( 'user',
                                                                'forgotpassword', ['user_id'    => $userID, 'password_hash'  => $hashKey, 'time' => $time] );
            }
            else
            {
                eZUserOperationCollection::forgotpassword( $userID, $hashKey, $time );
            }

            $userToSendEmail = $user;
            $receiver = $email;

            $mail = new eZMail();
            if ( !$mail->validate( $receiver ) )
            {
            }

            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'user', $userToSendEmail );
            $tpl->setVariable( 'object', $userToSendEmail->attribute( 'contentobject' ) );
            $tpl->setVariable( 'password', $password );
            $tpl->setVariable( 'link', true );
            $tpl->setVariable( 'hash_key', $hashKey );
            $templateResult = $tpl->fetch( 'design:user/forgotpasswordmail.tpl' );
            if ( $tpl->hasVariable( 'content_type' ) )
                $mail->setContentType( $tpl->variable( 'content_type' ) );
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$emailSender )
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
            $mail->setSender( $emailSender );
            $mail->setReceiver( $receiver );
            $subject = ezpI18n::tr( 'kernel/user/register', 'Registration info' );
            if ( $tpl->hasVariable( 'subject' ) )
                $subject = $tpl->variable( 'subject' );
            $mail->setSubject( $subject );
            $mail->setBody( $templateResult );
            $mailResult = eZMailTransport::send( $mail );
            $tpl->setVariable( 'email', $email );

        }
        else
        {
            $tpl->setVariable( 'wrong_email', $email );
        }
    }
}

$Result = [];
$Result['content'] = $tpl->fetch( 'design:user/forgotpassword.tpl' );
$Result['path'] = [['text' => ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false], ['text' => ezpI18n::tr( 'kernel/user', 'Forgot password' ), 'url' => false]];

if ( $ini->variable( 'SiteSettings', 'LoginPage' ) == 'custom' )
{
    $Result['pagelayout'] = 'loginpagelayout.tpl';
}

?>
