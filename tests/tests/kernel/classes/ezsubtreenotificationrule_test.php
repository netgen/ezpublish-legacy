<?php
/**
 * File containing the eZSubtreeNotificationRuleTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZSubtreeNotificationRuleTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZSubtreeNotificationRule Unit Tests" );
    }

    /**
     * Unit test for eZSubtreeNotificationRule::fetchUserList()
     */
    public function testFetchUserList()
    {
        // Add a notification rule for admin on root
        $adminUserID = eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $rule = new eZSubtreeNotificationRule( ['user_id' => $adminUserID, 'use_digest' => 0, 'node_id' => 2] );
        $rule->store();

        // Create a content object below node #2
        $article = new ezpObject( 'article', 2 );
        $article->title = __FUNCTION__;
        $article->publish();
        $articleContentObject = $article->object;

        $list = eZSubtreeNotificationRule::fetchUserList( [2, 43], $articleContentObject );
        static::assertInternalType('array', $list, "Return value should have been an array");
        static::assertEquals(1, count( $list ), "Return value should have one item");
        static::assertInternalType('array', $list[0]);
        static::assertArrayHasKey('user_id', $list[0]);
        static::assertArrayHasKey('use_digest', $list[0]);
        static::assertArrayHasKey('address', $list[0]);
        static::assertEquals(14, $list[0]['user_id']);
        static::assertEquals(0, $list[0]['use_digest']);
        static::assertEquals('nospam@ez.no', $list[0]['address']);
    }
}

?>
