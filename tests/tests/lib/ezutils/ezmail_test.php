<?php
/**
 * File containing the eZMailTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZMailTest extends ezpTestCase
{
    public static function imapIsEnabled()
    {
        return function_exists( 'imap_open' );
    }

    public function setUp()
    {
        parent::setUp();

        // Setup default settings, change these in each test when needed
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'Transport', 'sendmail' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportServer', 'localhost' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPort', 25 );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportUser', '' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPassword', '' );
        $ini = eZINI::instance( 'test_ezmail_plain.ini' );
        $adminEmail = $ini->variable( 'TestAccounts', 'AdminEmail' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'AdminEmail', $adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'EmailSender', $adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'EmailReplyTo', $adminEmail );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugSending', 'disabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugReceiverEmail', $adminEmail );
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();

        parent::tearDown();
    }

    public static function providerTestValidate()
    {
        return [
            ['kc@ez.no', 1],
            ['kc+list@ez.no', 1],
            ["kc'@ez.no", 1],
            ["k..c'@ez.no", 0],
            [".kc@ez.no", 0],
            ['johndoe@example.com', 1],
            ['johndoe@example.org', 1],
            ['johndoe@example.gov', 1],
            ['johndoe@example.biz', 1],
            ['johndoe@example.net', 1],
            ['johndoe@example.mil', 1],
            ['johndoe@example.xxx', 1],
            ['johndoe@example.info', 1],
            ['johndoe@example.aero', 1],
            ['johndoe@example.jobs', 1],
            ['johndoe@example.name', 1],
            ['johndoe@example.museum', 1],
            ['johndoe@example.solarspace', 1],
            ['johndoe@example.co.uk', 1],
            ['johndoe@e.x.a.m.p.l.e.com', 1],
            ['johndoe@e-x-a-m-p-l-e.com', 1],
            ['johndoe@e.x-a.m-p.l-e.com', 1],
            ['johndoe@example.xx', 1],
            ['johndoe@-example.com', 1],
            ['johndoe@example-.com', 1],
            ['johndoe@1example.com', 1],
            ['johndoe@example.c-m', 1],
            ['johndoe@1.aa', 1],
            // doma'in part as IP address
            ['johndoe@0.0.0.0', 1],
            ['johndoe@11.11.11.11', 1],
            ['johndoe@111.111.111.111', 1],
            ['johndoe@127.0.0.1', 1],
            ['johndoe@[127.0.0.1]', 1],
            ['johndoe@1.12.123.1', 1],
            ['johndoe@255.255.255.255', 1],
            ['a@example.com', 1],
            ['A@example.com', 1],
            ['1@example.com', 1],
            ['+@example.com', 1],
            ['*@example.com', 1],
            ['{@example.com', 1],
            ['}@example.com', 1],
            ['|@example.com', 1],
            ['~@example.com', 1],
            ['/@example.com', 1],
            ['\'@example.com', 1],
            ['-@example.com', 1],
            ['_@example.com', 1],
            ['`@example.com', 1],
            ['^@example.com', 1],
            ['$@example.com', 1],
            ['%@example.com', 1],
            ['&@example.com', 1],
            ['!@example.com', 1],
            ['john+doe@example.com', 1],
            ['john*doe@example.com', 1],
            ['john{doe@example.com', 1],
            ['john}doe@example.com', 1],
            ['john|doe@example.com', 1],
            ['john~doe@example.com', 1],
            ['john/doe@example.com', 1],
            ['john\'doe@example.com', 1],
            ['john-doe@example.com', 1],
            ['john_doe@example.com', 1],
            ['john`doe@example.com', 1],
            ['john^doe@example.com', 1],
            ['john$doe@example.com', 1],
            ['john%doe@example.com', 1],
            ['john&doe@example.com', 1],
            ['john!doe@example.com', 1],
            ['johndoe+@example.com', 1],
            ['johndoe*@example.com', 1],
            ['johndoe{@example.com', 1],
            ['johndoe}@example.com', 1],
            ['johndoe|@example.com', 1],
            ['johndoe~@example.com', 1],
            ['johndoe/@example.com', 1],
            ['johndoe\'@example.com', 1],
            ['johndoe-@example.com', 1],
            ['johndoe_@example.com', 1],
            ['johndoe`@example.com', 1],
            ['johndoe^@example.com', 1],
            ['johndoe$@example.com', 1],
            ['johndoe%@example.com', 1],
            ['johndoe&@example.com', 1],
            ['johndoe!@example.com', 1],
            ['j.o.h.n.d.o.e@example.com', 1],
            ['john-doe@example.com', 1],
            ['j-o-h-n-d-o-e@example.com', 1],
            ['j._-oh.n---d.__--o.___---e@example.com', 1],
            ['john_doe@example.com', 1],
            ['john/doe@example.com', 1],
            ['j_o_h_n_d_o_e@example.com', 1],
            ['j.o_h-n.d_o-e@example.com', 1],
            ['johndoe1@example.com', 1],
            ['j1o2h3n4d5o6e7@example.com', 1],
            ['j1.o2_h3-n4.d5_o6-e7@example.com', 1],
            ['1johndoe@example.com', 1],
            ['+1~1+@example.com', 1],
            ['{johndoe}@example.com', 1],
            ['{_johndoe_}@example.com', 1],
            ['|johndoe|@example.com', 1],
            ['-johndoe-@example.com', 1],
            ['`johndoe`@example.com', 1],
            ['\'johndoe\'@example.com', 1],
            ['"[[ johndoe ]]"@example.com', 1],
            ['"john.\'doe\'"@example.com', 1],
            ['"john doe"@example.com', 1],
            ['"john\doe"@example.com', 1],
            ['"john?doe"@example.com', 1],
            ['"john,doe"@example.com', 1],
            ['"john@doe"@example.com', 1],
            ['"john=doe"@example.com', 1],
            ['"john<doe"@example.com', 1],
            ['"john>doe"@example.com', 1],
            ['"john;doe"@example.com', 1],
            ['"john:doe"@example.com', 1],
            ['"john¢doe"@example.com', 1],
            ['"john±doe"@example.com', 1],
            ['"john³doe"@example.com', 1],
            ['"johnµdoe"@example.com', 1],
            ['"john¶doe"@example.com', 1],
            ['"john·doe"@example.com', 1],
            ['"john¸doe"@example.com', 1],
            ['"john¹doe"@example.com', 1],
            ['"john°doe"@example.com', 1],
            ['"john½doe"@example.com', 1],
            ['"john»doe"@example.com', 1],
            ['"john§doe"@example.com', 1],
            ['"john®doe"@example.com', 1],
            ['"john¯doe"@example.com', 1],
            ['"john¬doe"@example.com', 1],
            ['"john¼doe"@example.com', 1],
            ['"johnþdoe"@example.com', 1],
            ['"john¡doe"@example.com', 1],
            ['"john£doe"@example.com', 1],
            ['"john¤doe"@example.com', 1],
            ['"john¥doe"@example.com', 1],
            ['"johnÞdoe"@example.com', 1],
            ['"john¦doe"@example.com', 1],
            ['"johnªdoe"@example.com', 1],
            ['"john¨doe"@example.com', 1],
            ['"john©doe"@example.com', 1],
            ['"john¿doe"@example.com', 1],
            ['"john¾doe"@example.com', 1],
            ['"john¼doe"@example.com', 1],
            ['"john«doe"@example.com', 1],
            // incorrect addresses
            ['name', 0],
            ['johndoe', 0],
            ['johndoe@', 0],
            ['johndoe@.', 0],
            ['johndoe@a.a', 0],
            ['johndoe@1.a', 0],
            ['johndoe@example', 0],
            ['johndoe@example.x', 0],
            ['johndoe@example.0', 0],
            ['johndoe@example.00', 0],
            ['johndoe@example.000', 0],
            ['johndoe@example,com', 0],
            ['johndoe@e$ample.com', 0],
            ['johndoe@e!ample.com', 0],
            ['johndoe@e?ample.com', 0],
            ['johndoe@e\'ample.com', 0],
            ['johndoe@e"ample.com', 0],
            ['johndoe@e^ample.com', 0],
            ['johndoe@e%ample.com', 0],
            ['johndoe@e~ample.com', 0],
            ['johndoe@e`ample.com', 0],
            ['johndoe@examp|e.com', 0],
            ['johndoe@e#ample.com', 0],
            ['johndoe@e ample.com', 0],
            ['johndoe@e_ample.com', 0],
            ['johndoe@e%20ample.com', 0],
            ['johndoe@example.c m', 0],
            ['johndoe@{example}.com', 0],
            ['johndoe@(example).com', 0],
            ['johndoe@[example].com', 0],
            ['johndoe@"example".com', 0],
            ['johndoe@\'example\'.com', 0],
            ['johndoe@example.$$$', 0],
            ['johndoe@example.!!!', 0],
            ['johndoe@example.???', 0],
            ['johndoe@example.###', 0],
            ['johndoe@example....', 0],
            ['johndoe@example.,,,', 0],
            ['johndoe@example.[]', 0],
            ['johndoe@example.{}', 0],
            ['johndoe@example.()', 0],
            ['johndoe@example.""', 0],
            ['johndoe@example.\'\'', 0],
            ['johndoe@example.||', 0],
            ['johndoe@`example.com', 0],
            ['johndoe@|example.com', 0],
            ['johndoe@,example.com', 0],
            ['johndoe@.example.com', 0],
            ['@', 0],
            ['@.', 0],
            // domain part as IP address
            ['johndoe@1111.111.11.1', 0],
            ['johndoe@256.256.256.256', 0],
            ['johndoe@FF.0F.FF.7A', 0],
            ['johndoe@1-7.0.0.1', 0],
            ['johndoe@127.0.0.[1]', 0],
            ['johndoe@(127.0.0.1)', 0],
            ['johndoe@{127.0.0.1}    ', 0],
            ['johndoe@"127.0.0.1"', 0],
            ['johndoe@\'127.0.0.1\'', 0],
            ['johndoe@|127.0.0.1|', 0],
            ['johndoe@`127.0.0.1`', 0],
            ['johndoe@127.0.0', 0],
            ['johndoe@127.0', 0],
            ['johndoe@127', 0],
            ['johndoe@0.00', 0],
            // localpart
            ['example.com', 0],
            ['@example.com', 0],
            ['.@example.com', 0],
            [',@example.com', 0],
            ['\@example.com', 0],
            ['"@example.com', 0],
            ['=@example.com', 0],
            ['?@example.com', 0],
            ['<@example.com', 0],
            ['>@example.com', 0],
            [':@example.com', 0],
            [';@example.com', 0],
            ['¢@example.com', 0],
            ['±@example.com', 0],
            ['³@example.com', 0],
            ['µ@example.com', 0],
            ['¶@example.com', 0],
            ['·@example.com', 0],
            ['¸@example.com', 0],
            ['¹@example.com', 0],
            ['°@example.com', 0],
            ['½@example.com', 0],
            ['»@example.com', 0],
            ['§@example.com', 0],
            ['®@example.com', 0],
            ['¯@example.com', 0],
            ['¬@example.com', 0],
            ['¼@example.com', 0],
            ['þ@example.com', 0],
            ['¡@example.com', 0],
            ['£@example.com', 0],
            ['¤@example.com', 0],
            ['¥@example.com', 0],
            ['Þ@example.com', 0],
            ['¦@example.com', 0],
            ['ª@example.com', 0],
            ['¨@example.com', 0],
            ['©@example.com', 0],
            ['¿@example.com', 0],
            ['¾@example.com', 0],
            ['¼@example.com', 0],
            ['«@example.com', 0],
            ['.johndoe@example.com', 0],
            ['johndoe.@example.com', 0],
            ['johndoe,@example.com', 0],
            ['johndoe\@example.com', 0],
            ['johndoe"@example.com', 0],
            ['johndoe=@example.com', 0],
            ['johndoe?@example.com', 0],
            ['johndoe<@example.com', 0],
            ['johndoe>@example.com', 0],
            ['johndoe:@example.com', 0],
            ['johndoe;@example.com', 0],
            ['johndoe¢@example.com', 0],
            ['johndoe±@example.com', 0],
            ['johndoe³@example.com', 0],
            ['johndoeµ@example.com', 0],
            ['johndoe¶@example.com', 0],
            ['johndoe·@example.com', 0],
            ['johndoe¸@example.com', 0],
            ['johndoe¹@example.com', 0],
            ['johndoe°@example.com', 0],
            ['johndoe½@example.com', 0],
            ['johndoe»@example.com', 0],
            ['johndoe§@example.com', 0],
            ['johndoe®@example.com', 0],
            ['johndoe¯@example.com', 0],
            ['johndoe¬@example.com', 0],
            ['johndoe¼@example.com', 0],
            ['johndoeþ@example.com', 0],
            ['johndoe¡@example.com', 0],
            ['johndoe£@example.com', 0],
            ['johndoe¤@example.com', 0],
            ['johndoe¥@example.com', 0],
            ['johndoeÞ@example.com', 0],
            ['johndoe¦@example.com', 0],
            ['johndoeª@example.com', 0],
            ['johndoe¨@example.com', 0],
            ['johndoe©@example.com', 0],
            ['johndoe¿@example.com', 0],
            ['johndoe¾@example.com', 0],
            ['johndoe¼@example.com', 0],
            ['johndoe«@example.com', 0],
            ['john doe@example.com', 0],
            ['john,doe@example.com', 0],
            ['john"doe@example.com', 0],
            ['john@doe@example.com', 0],
            ['john\doe@example.com', 0],
            ['john=doe@example.com', 0],
            ['john?doe@example.com', 0],
            ['john<doe@example.com', 0],
            ['john>doe@example.com', 0],
            ['john;doe@example.com', 0],
            ['john:doe@example.com', 0],
            ['john¢doe@example.com', 0],
            ['john±doe@example.com', 0],
            ['john³doe@example.com', 0],
            ['johnµdoe@example.com', 0],
            ['john¶doe@example.com', 0],
            ['john·doe@example.com', 0],
            ['john¸doe@example.com', 0],
            ['john¹doe@example.com', 0],
            ['john°doe@example.com', 0],
            ['john½doe@example.com', 0],
            ['john»doe@example.com', 0],
            ['john§doe@example.com', 0],
            ['john®doe@example.com', 0],
            ['john¯doe@example.com', 0],
            ['john¬doe@example.com', 0],
            ['john¼doe@example.com', 0],
            ['johnþdoe@example.com', 0],
            ['john¡doe@example.com', 0],
            ['john£doe@example.com', 0],
            ['john¤doe@example.com', 0],
            ['john¥doe@example.com', 0],
            ['johnÞdoe@example.com', 0],
            ['john¦doe@example.com', 0],
            ['johnªdoe@example.com', 0],
            ['john¨doe@example.com', 0],
            ['john©doe@example.com', 0],
            ['john¿doe@example.com', 0],
            ['john¾doe@example.com', 0],
            ['john¼doe@example.com', 0],
            ['john«doe@example.com', 0],
            ['- johndoe -@example.com', 0],
            ['[johndoe]@example.com', 0],
            ['(johndoe)@example.com', 0],
            ['<johndoe>@example.com', 0],
        ];
    }

    public static function providerTestExtractEmail()
    {
        return [['John Doe <jdoe+subaddr@test.example.com>', 'John Doe', 'jdoe+subaddr@test.example.com'], ['John Doe <jdoe@test.example.com>', 'John Doe', 'jdoe@test.example.com'], ['John Doe <jdoe@example.com>', 'John Doe', 'jdoe@example.com'], ['豆豆龍 <jdoe@example.com>', '豆豆龍', 'jdoe@example.com'], ['小丁噹 <"小丁噹"@example.com>', '小丁噹', '"小丁噹"@example.com']];
    }

    public static function providerTestStripEmail()
    {
        return [['Bla bla bla bla "楊大葶" <user@example.com>  test test <anotheruser@example.com> test', 'user@example.com'], ['Bla bla bla bla "楊大葶"@example.com test <anotheruser@example.com> test test', '"楊大葶"@example.com'], ['Bla bla bla bla John Doe <jdoe+subaddr@test.example.com> test <anotheruser@example.com> test test', 'jdoe+subaddr@test.example.com']];
    }

    public static function getTestAccounts()
    {
        $ini = eZINI::instance( 'test_ezmail_plain.ini' );
        $testAccounts = $ini->hasVariable( 'TestAccounts', 'Account' ) ? $ini->variable( 'TestAccounts', 'Account' ) : [];
        $accountResult = [];
        foreach( $testAccounts as $account )
        {
            $user = $ini->variable( 'TestAccounts', $account );
            $accountResult[ $user['index'] ] = $user;
        }
        return $accountResult;
    }

    public static function providerTestSendEmail()
    {
        $users = self::getTestAccounts();
        $endl = "\r\n";

        if ( empty( $users ) )
            return [['', '']];

        /*
            Each entry in this array is an array consisting of two arrays.
            The first is the data that will be mailed.
            The second is the expected result. Since the result may be different for each recipient,
            this array is per recipient, using the email as array key.
        */
        return [[
            // Testing simple mail
            ['to' => [$users['01']], 'replyTo' => null, 'sender' => $users['02'], 'cc' => null, 'bcc' => null, 'subject' => 'Luke', 'body' => 'Told you, I did. Reckless, is he. Now, matters are worse.'],
            [$users['01']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'from' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'subject' => 'Luke'], 'body' => 'Told you, I did. Reckless, is he. Now, matters are worse.' . $endl]],
        ], [
            // Testing multiple CC recipients
            ['to' => [$users['01']], 'replyTo' => null, 'sender' => $users['02'], 'cc' => [$users['03'], $users['04']], 'bcc' => null, 'subject' => 'Mos Eisley', 'body' => 'You will never find a more wretched hive of scum and villainy.'],
            [$users['01']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'from' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'cc' => [['email' => $users['03']['email'], 'name' => $users['03']['name']], ['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'Mos Eisley'], 'body' => 'You will never find a more wretched hive of scum and villainy.' . $endl], $users['03']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'from' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'cc' => [['email' => $users['03']['email'], 'name' => $users['03']['name']], ['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'Mos Eisley'], 'body' => 'You will never find a more wretched hive of scum and villainy.' . $endl], $users['04']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'from' => [['email' => $users['02']['email'], 'name' => $users['02']['name']]], 'cc' => [['email' => $users['03']['email'], 'name' => $users['03']['name']], ['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'Mos Eisley'], 'body' => 'You will never find a more wretched hive of scum and villainy.' . $endl]],
        ], [
            // Testing multiple BCC recipients
            ['to' => [$users['01']], 'replyTo' => null, 'sender' => $users['01'], 'cc' => null, 'bcc' => [$users['04'], $users['05']], 'subject' => 'Death Star', 'body' => 'Now witness the firepower of this fully armed and operational battle station!'],
            [$users['01']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'subject' => 'Death Star'], 'body' => 'Now witness the firepower of this fully armed and operational battle station!' . $endl], $users['04']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'subject' => 'Death Star'], 'body' => 'Now witness the firepower of this fully armed and operational battle station!' . $endl], $users['05']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'subject' => 'Death Star'], 'body' => 'Now witness the firepower of this fully armed and operational battle station!' . $endl]],
        ], [
            // Testing DebugSending = enabled with sendmail (cc and bcc headers must be stripped)
            ['to' => [$users['02'], $users['03']], 'replyTo' => null, 'sender' => $users['01'], 'cc' => [$users['04']], 'bcc' => [$users['05']], 'subject' => 'That ancient religion', 'body' => 'I find your lack of faith disturbing.', 'DebugSending' => true],
            [$users['01']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl], $users['02']['email'] => ['messageCount' => 0], $users['03']['email'] => ['messageCount' => 0], $users['04']['email'] => ['messageCount' => 0], $users['05']['email'] => ['messageCount' => 0]],
        ], [
            // Testing DebugSending = enabled with SMTP (cc is empty in debug mode)
            ['to' => [$users['02'], $users['03']], 'replyTo' => null, 'sender' => $users['01'], 'cc' => [$users['04']], 'bcc' => [$users['05']], 'subject' => 'That ancient religion', 'body' => 'I find your lack of faith disturbing.', 'DebugSending' => true, 'Transport' => 'SMTP'],
            [$users['01']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['01']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl], $users['02']['email'] => ['messageCount' => 0], $users['03']['email'] => ['messageCount' => 0], $users['04']['email'] => ['messageCount' => 0], $users['05']['email'] => ['messageCount' => 0]],
        ], [
            // Testing DebugSending = disabled with SMTP (cc headers are kept, bcc headers may be kept)
            ['to' => [$users['02'], $users['03']], 'replyTo' => null, 'sender' => $users['01'], 'cc' => [$users['04']], 'bcc' => [$users['05']], 'subject' => 'That ancient religion', 'body' => 'I find your lack of faith disturbing.', 'DebugSending' => false, 'Transport' => 'SMTP'],
            [$users['02']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['02']['email']], ['email' => $users['03']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'cc' => [['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl], $users['03']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['02']['email']], ['email' => $users['03']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'cc' => [['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl], $users['04']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['02']['email']], ['email' => $users['03']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'cc' => [['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl], $users['05']['email'] => ['messageCount' => 1, 'headers' => ['to' => [['email' => $users['02']['email']], ['email' => $users['03']['email']]], 'replyTo' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'from' => [['email' => $users['01']['email'], 'name' => $users['01']['name']]], 'cc' => [['email' => $users['04']['email'], 'name' => $users['04']['name']]], 'subject' => 'That ancient religion'], 'body' => 'I find your lack of faith disturbing.' . $endl]],
        ]];
    }

    /**
     * @dataProvider providerTestValidate
     */
    public function testValidate( $email, $valid )
    {
        static::assertEquals($valid, eZMail::validate( $email ));
    }

    /**
     * @dataProvider providerTestExtractEmail
     */
    public function testExtractEmail( $recipient, $name, $email )
    {
        eZMail::extractEmail( $recipient, $extractedEmail, $extractedName );
        self::assertEquals( $extractedEmail, $email );
        self::assertEquals( $extractedName, $name );
    }

    /**
     * @dataProvider providerTestStripEmail
     */
    public function testStripEmail( $text, $firstEmailAddress )
    {
    }

    /**
     * @dataProvider providerTestSendEmail
     */
    public function testSendEmail( $sendData, $expectedResult )
    {
        if( empty( $sendData ) )
        {
            static::markTestSkipped('No $sendData from data provider.');
        }
        if ( !self::imapIsEnabled() )
        {
            static::markTestSkipped('IMAP is not loaded');
            return;
        }

        $emailINI = eZINI::instance( 'test_ezmail_plain.ini' );
        $mboxString = $emailINI->variable( 'TestAccounts', 'MBoxString' );
        $recipients = array_merge( (array)$sendData['to'], (array)$sendData['cc'], (array)$sendData['bcc'] );

        if ( isset( $sendData['Transport'] ) and $sendData['Transport'] == 'SMTP' )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'Transport', 'SMTP' );
            $mailINI = eZINI::instance( 'test_ezmail_plain.ini' );
            $mailSetting = $mailINI->group( 'MailSettings' );
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportServer', $mailSetting['TransportServer'] );
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPort', $mailSetting['TransportPort'] );
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportUser', $mailSetting['TransportUser'] );
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'TransportPassword', $mailSetting['TransportPassword'] );

        }

        if ( isset( $sendData['DebugSending'] ) and $sendData['DebugSending'] == true )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugSending', 'enabled' );
            $users = self::getTestAccounts();
            $recipients[] = $users['01'];
        }
        else
            ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'DebugSending', 'disabled' );

        foreach ( $recipients as $recipient )
        {
            // Accept only testing accounts as recipients
            if ( preg_match( '/^ezp-unittests-\d\d\@mail\.ez\.no$/', (string) $recipient['email'] ) != 1 )
            {
                static::markTestSkipped('Refusing to use other than testing accounts');
                return;
            }

            // Open mailbox and delete all existing emails in the account
            $mbox = @imap_open( $mboxString, $recipient['username'], $recipient['password'] );
            if ( !$mbox )
            {
                static::markTestSkipped('Cannot open mailbox for ' . $recipient['username'] . ': ' . imap_last_error());
                return;
            }

            $status = imap_status( $mbox, $mboxString, SA_MESSAGES );
            for ( $i = 1; $i <= $status->messages; $i++ )
            {
                imap_delete( $mbox, $i );
            }
            imap_expunge( $mbox );

            imap_close( $mbox );
        }

        // Create and send email
        $mail = new eZMail();

        if ( (is_countable($sendData['to']) ? count( $sendData['to'] ) : 0) == 1 )
            $mail->setReceiver( $sendData['to'][0]['email'], $sendData['to'][0]['name'] );
        else
            $mail->setReceiverElements( $sendData['to'] );

        if ( $sendData['replyTo'] )
        {
            $mail->setReplyTo( $sendData['replyTo']['email'], $sendData['replyTo']['name'] );
        }

        $mail->setSender( $sendData['sender']['email'], $sendData['sender']['name'] );

        if ( $sendData['cc'] )
        {
            if ( (is_countable($sendData['cc']) ? count( $sendData['cc'] ) : 0) == 1 )
                $mail->addCc( $sendData['cc'][0]['email'], $sendData['cc'][0]['name'] );
            else
                $mail->setCcElements( $sendData['cc'] );
        }

        if ( $sendData['bcc'] )
        {
            if ( (is_countable($sendData['bcc']) ? count( $sendData['bcc'] ) : 0) == 1 )
                $mail->addBcc( $sendData['bcc'][0]['email'], $sendData['bcc'][0]['name'] );
            else
                $mail->setBccElements( $sendData['bcc'] );
        }

        $mail->setSubject( $sendData['subject'] );
        $mail->setBody( $sendData['body'] );

        $sendResult = eZMailTransport::send( $mail );
        static::assertEquals(true, $sendResult);

        // Wait for it...
        sleep( 2 );

        // Read emails
        foreach ( $recipients as $recipient )
        {
            $mbox = @imap_open( $mboxString, $recipient['username'], $recipient['password'] );
            if ( !$mbox )
            {
                static::markTestSkipped('Cannot open mailbox for ' . $recipient['username'] . ': ' . imap_last_error());
                return;
            }

            // Check message count before we try to open anything, in case nothing is there
            $status = imap_status( $mbox, $mboxString, SA_MESSAGES );
            static::assertEquals($expectedResult[ $recipient['email'] ]['messageCount'], $status->messages);

            // Build actual result array, and check against the expected result
            $actualResult = ['messageCount' => $status->messages];
            for ( $i = 1; $i <= $status->messages; $i++ )
            {
                $headers = imap_headerinfo( $mbox, $i );
                $actualResult['headers'] = [];

                $actualResult['headers']['to'] = [];
                foreach ( $headers->to as $item )
                {
                    $actualResult['headers']['to'][] = ['email' => $item->mailbox . '@' . $item->host];
                }

                $actualResult['headers']['replyTo'] = [];
                foreach ( $headers->reply_to as $item )
                {
                    $actualResult['headers']['replyTo'][] = ['email' => $item->mailbox . '@' . $item->host, 'name' => $item->personal];
                }

                $actualResult['headers']['from'] = [];
                foreach ( $headers->from as $item )
                {
                    $actualResult['headers']['from'][] = ['email' => $item->mailbox . '@' . $item->host, 'name' => $item->personal];
                }

                if ( isset( $headers->cc ) )
                {
                    $actualResult['headers']['cc'] = [];
                    foreach ( $headers->cc as $item )
                    {
                        $actualResult['headers']['cc'][] = ['email' => $item->mailbox . '@' . $item->host, 'name' => $item->personal];
                    }
                }

                $actualResult['headers']['subject'] = $headers->subject;

                $body = imap_body( $mbox, $i );
                $actualResult['body'] = $body;

                static::assertEquals($expectedResult[ $recipient['email'] ], $actualResult);
            }
            imap_close( $mbox );
        }
    }

    /**
     * See site.ini [MailSettings] ExcludeHeaders
     */
    public function testExcludeHaders()
    {
        self::markTestSkipped( "Tests needs to use other email addresses" );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'Transport', 'SMTP' );
        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'ExcludeHeaders', ['bcc'] );

        $mail = new eZMail();
        $mail->setReceiver( 'johndoe@example.com', 'John Doe' );
        $mail->setSender( 'janedoe@example.com', 'Jane Doe' );
        $mail->addBcc( 'jimdoe@example.com', 'Jim Doe' );
        $mail->setSubject( 'Testing ExcludeHeaders' );
        $mail->setBody( 'Jim should not get this email.' );

        // BCC should be set at this point
        static::assertTrue(strpos( $mail->Mail->generateHeaders(), 'Bcc: Jim Doe <jimdoe@example.com>' ) > 0);

        // We don't care if the mail gets sent. What's important is what happens to the headers.
        eZMailTransport::send( $mail );

        // BCC should not be set anymore at this point, because of ExcludeHeaders
        static::assertFalse(strpos( $mail->Mail->generateHeaders(), 'Bcc: Jim Doe <jimdoe@example.com>' ) > 0);
    }

    public function testSSLSending()
    {
        // test SSL
        $ini = eZINI::instance( 'test_ezmail_ssl.ini' );
        $mailSetting = $ini->group( 'MailSettings' );
        //if SSL information is not set, skip this test
        if( !$mailSetting['TransportServer'] )
        {
            return;
        }
        $siteINI = eZINI::instance();
        $backupSetting = $siteINI->group( 'MailSettings' );
        $siteINI->setVariables( ['MailSettings' => $mailSetting] );

        $mail = new eZMail();

        $mail->setReceiver( $mailSetting['TransportUser'], 'TEST RECEIVER' );
        $mail->setSender( $mailSetting['TransportUser'], 'TEST SENDER' );
        $mail->setSubject( 'SSL EMAIL TESTING' );
        $mail->setBody( 'This is a mail testing. TEST SSL in ' . __METHOD__ );
        $result = eZMailTransport::send( $mail );
        static::assertTrue($result);

        $siteINI->setVariables( ['MailSettings' => $backupSetting] );

        //todo: delete the received mails in teardown.
    }
}

?>
