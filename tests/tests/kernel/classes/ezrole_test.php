<?php
/**
 * File containing the eZRoleTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZRoleTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZRole Unit Tests" );
    }

    /**
     * Unit test for eZRole::fetchByUser
     */
    public function testFetchByUser()
    {
        // test with an empty array
        $roles = eZRole::fetchByUser( [] );
        static::assertInternalType('array', $roles, "eZRole::fetchByUser with an empty array should have returned an array");
        static::assertEquals(0, count( $roles ), "eZRole::fetchByUser with an empty array should have returned an empty array");

        // test with users with no direct role assignment (group assignment)
        // should return an empty array since anonymous/admin role is assigned
        // by group
        $parameter = [self::$anonymousUserID, self::$adminUserID];
        $roles = eZRole::fetchByUser( $parameter );
        static::assertInternalType('array', $roles, "eZRole::fetchByUser with admin & anonymous should have returned an array");
        static::assertEquals(0, count( $roles ), "eZRole::fetchByUser with admin & anonymous should have returned an empty array");
        foreach( $roles as $role )
        {
            static::assertInstanceOf('eZRole', $role, "Returned items should be roles");
        }

        // test with the same users, but recursively: should return anonymous
        // and administrator roles
        $parameter = [self::$anonymousUserID, self::$adminUserID];
        $roles = eZRole::fetchByUser( $parameter, true );
        static::assertInternalType('array', $roles, "recursive eZRole::fetchByUser with admin & anonymous should have returned an array");
        static::assertEquals(2, count( $roles ), "recursive eZRole::fetchByUser with admin & anonymous should have returned an empty array");
        foreach( $roles as $role )
        {
            static::assertInstanceOf('eZRole', $role, "Returned items should be roles");
        }
    }

    /**
     * Unit test for eZRole::fetchIDListByUser()
     */
    public function testFetchIDListByUser()
    {
        // fetch roles ID for anonymous group
        $roles = eZRole::fetchIDListByUser( [self::$anonymousGroupID] );
        static::assertInternalType('array', $roles, "The method should have returned an array");
        static::assertEquals(1, count( $roles ), "The array should contain one item");
        static::assertEquals(1, $roles[0], "The returned role ID should be 1 (anonymous role)");
    }

    /**
     * Test for eZRole::policyList()
     * Checks that policyList doesn't return temporary policies
     */
    public function testPolicyList()
    {
        $roleList = eZRole::fetchByUser( [self::$anonymousUserID], true );
        $policyList = current( $roleList )->policyList();
        $policy = current( $policyList );

        // create a temporary copy of one of the role's policies
        $policy->createTemporaryCopy();

        // check that the role's policies all are final (not temporary)
        foreach ( $policyList as $policy )
        {
            static::assertInstanceOf('eZPolicy', $policy);
            static::assertEquals(0, $policy->attribute( 'original_id' ));
        }
    }

    static $anonymousGroupID = 42;
    static $anonymousUserID = 10;
    static $adminUserID = 14;
}
?>
