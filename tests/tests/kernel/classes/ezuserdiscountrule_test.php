<?php
/**
 * File containing the eZUserDiscountRule class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZUserDiscountRuleTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZDiscountRule Unit Tests" );
    }

    /**
     * Unit test for eZDiscountRule::fetchByUserIDArray()
     */
    public function testFetchByUserIDArray()
    {
        // Create 5 few discount rules
        $discountRules = [];
        for( $i = 0; $i < 5; $i++ )
        {
            $row = ['name' => __FUNCTION__ . " #{$i}"];
            $rule = new eZDiscountRule( $row );
            $rule->store();
            $discountRules[] = $rule;
        }

        // Create 5 user discount rules for 3 different user IDs
        $usersDiscountRules = [];
        foreach( [1, 3] as $userID )
        {
            $usersDiscountRules[$userID] = [];
            $userDiscountRules =& $usersDiscountRules[$userID];
            foreach( $discountRules as $discountRule )
            {
                $row = ['discountrule_id' => $discountRule->attribute( 'id' ), 'contentobject_id' => $userID];
                $userDiscountRule = new eZUserDiscountRule( $row );
                $userDiscountRule->store();
                $userDiscountRules[] = $userDiscountRule;
            }
        }

        // fetch the discount rules for user #1 and #2. This will match 10
        // eZUserDiscountRule, and return the 5 rules, since no duplicates will
        // be returned
        $rules = eZUserDiscountRule::fetchByUserIDArray( [1, 2] );
        static::assertInternalType('array', $rules, "Return value should have been an array");
        static::assertEquals(5, count( $rules ), "Return value should contain 5 items");
        foreach( $rules as $rule )
        {
            static::assertInstanceOf('eZDiscountRule', $rule);
        }

    }
}
?>
