<?php
/**
 * File containing the eZCollaborationItemTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZCollaborationItemTest extends ezpDatabaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $participantsList = [$this->createParticipantLinkPartialMock(
            ['collaboration_id' => 1, 'participant_id' => 1, 'participant_type' => eZCollaborationItemParticipantLink::TYPE_USER]
        ), $this->createParticipantLinkPartialMock(
            ['collaboration_id' => 1, 'participant_id' => 2, 'participant_type' => eZCollaborationItemParticipantLink::TYPE_USERGROUP]
        )];

        $this->getCollaborationItemPartialMock()
            ->expects( static::any() )
            ->method( 'participantList' )
            ->will( static::returnValue($participantsList) );
    }

    /**
     * @dataProvider providerForIsParticipant
     */
    public function testIsParticipant( $expectedResult, $userId, array $groupIdArray = [] )
    {
        $user = $this->createUserMock( $userId, $groupIdArray );
        self::assertEquals(
            $expectedResult,
            $this->getCollaborationItemPartialMock()->userIsParticipant( $user )
        );
    }

    public function providerForIsParticipant()
    {
        return [[true, 1], [false, 2], [true, 3, [2]], [true, 3, [2, 3]], [false, 3, [3]]];
    }


    /**
     * @return eZUser|PHPUnit_Framework_MockObject_MockObject
     */
    protected function createUserMock( $userId, array $groupIdArray = [] )
    {
        $mock = $this->getMockBuilder( 'eZUser' )
            ->setMethods( ['groups', 'attribute'] )
            ->setConstructorArgs( ['contentobject_id' => $userId] )
            ->getMock();

        $groups = [];
        foreach ( $groupIdArray as $groupId )
        {
            $groups[] = new eZContentObject( ['id' => $groupId] );
        }

        $mock->expects( static::any() )
            ->method( 'groups' )
            ->will( static::returnValue($groups) );

        $mock->expects( static::any() )
            ->method( 'attribute' )
            ->with( 'contentobject_id' )
            ->will( static::returnValue($userId) );

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|eZCollaborationItem
     */
    protected function getCollaborationItemPartialMock()
    {
        if ( !isset( $this->collaborationItemPartialMock ) )
        {
            $this->collaborationItemPartialMock = $this->getMockBuilder( 'eZCollaborationItem' )
                ->setMethods( ['participantList'] )
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $this->collaborationItemPartialMock;
    }

    /**
     * @return eZCollaborationItemParticipantLink|PHPUnit_Framework_MockObject_MockObject
     */
    protected function createParticipantLinkPartialMock( $row )
    {
        $mock = $this->getMockBuilder( 'eZCollaborationItemParticipantLink' )
            ->setMethods( ['participant'] )
            ->setConstructorArgs( [$row] )
            ->getMock();

        $returnValue = $row['participant_type'] == eZCollaborationItemParticipantLink::TYPE_USER
            ? new eZUser( ['contentobject_id' => $row['participant_id']] )
            : new eZContentObject( ['id' => $row['participant_id']] );

        $mock->expects( static::any() )
            ->method( 'participant' )
            ->will( static::returnValue($returnValue) );

        return $mock;
    }

    /** @var $collaborationItemPartialMock PHPUnit_Framework_MockObject_MockObject|eZCollaborationItem */
    protected $collaborationItemPartialMock;
}
