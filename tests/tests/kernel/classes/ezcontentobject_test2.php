<?php
/**
 * File containing the eZContentObjectTest2 class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZContentObjectTest2 extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;
    protected $article;

    public function setUp()
    {
        parent::setUp();

        $this->article = new ezpObject( "article", 2, eZUser::fetchByName( 'anonymous' )->attribute( 'contentobject_id' ) );
        $this->article->title = "Article for " . self::class;
        $this->article->publish();
        $this->article->addTranslation( "nor-NO", ["title" => "Norsk title of article for " . self::class] );
    }

    public function tearDown()
    {
        $this->article->remove();
        parent::tearDown();
    }

    /**
     * Test for eZContentObject::versions(), fetching all of them
     */
    public function testFetchAllVersionsAsObject()
    {
        $versions = $this->article->object->versions();
        static::assertEquals(2, is_countable($versions) ? count( $versions ) : 0);
        static::assertInstanceOf('eZContentObjectVersion', $versions[0]);
        static::assertInstanceOf('eZContentObjectVersion', $versions[1]);
    }

    /**
     * Test for eZContentObject::versions(), fetching all of them, returns rows
     */
    public function testFetchAllVersionsAsRows()
    {
        $versions = $this->article->object->versions( false );
        static::assertEquals(2, is_countable($versions) ? count( $versions ) : 0);
        static::assertInternalType('array', $versions[0]);
        static::assertInternalType('array', $versions[1]);
    }

    /**
     * Test for eZContentObject::versions(), fetching versions with 'published' status
     */
    public function testFetchVersionsWithPublishedStatus()
    {
        $versions = $this->article->object->versions( true, ['conditions' => ['status' => eZContentObjectVersion::STATUS_PUBLISHED]]);
        static::assertEquals(1, is_countable($versions) ? count( $versions ) : 0);
        static::assertEquals(eZContentObjectVersion::STATUS_PUBLISHED, $versions[0]->Status);
        static::assertEquals(2, $versions[0]->Version);
    }

    /**
     * Test for eZContentObject::versions(), fetching versions with creator (matching)
     */
    public function testFetchVersionsWithMatchingCreator()
    {
        $creatorID = eZUser::fetchByName( 'anonymous' )->attribute( 'contentobject_id' );
        $versions = $this->article->object->versions( true, ['conditions' => ['creator_id' => $creatorID]] );
        static::assertEquals(2, is_countable($versions) ? count( $versions ) : 0);
        static::assertEquals($creatorID, $versions[0]->CreatorID);
        static::assertEquals($creatorID, $versions[1]->CreatorID);
    }

    /**
     * Test for eZContentObject::versions(), fetching versions with creator (not matching)
     */
    public function testFetchVersionsWithNonMatchingCreator()
    {
        $creatorID = eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $versions = $this->article->object->versions( true, ['conditions' => ['creator_id' => $creatorID]] );
        static::assertTrue(empty( $versions ));
    }

    /**
     * Test for eZContentObject::versions(), fetching versions with 'archived' status
     */
    public function testFetchVersionsWithArchivedStatus()
    {
        $versions = $this->article->object->versions( true, ['conditions' => ['status' => eZContentObjectVersion::STATUS_ARCHIVED]]);
        static::assertEquals(1, is_countable($versions) ? count( $versions ) : 0);
        static::assertEquals(eZContentObjectVersion::STATUS_ARCHIVED, $versions[0]->Status);
        static::assertEquals(1, $versions[0]->Version);
    }

    /**
     * Test for eZContentObject::fetchList(), returning objects
     */
    public function testFetchListAsObjects()
    {
        $eZContentObjectDefinition = eZContentObject::definition();
        $objects = eZContentObject::fetchList(
            true,
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id]
        );
        static::assertSame(1, count( (array) $objects ));
        static::assertInstanceOf('eZContentObject', $objects[0]);
        static::assertEquals($this->article->id, $objects[0]->attribute( 'id' ));
    }

    /**
     * Test for eZContentObject::fetchList(), returning rows
     */
    public function testFetchListAsRows()
    {
        $eZContentObjectDefinition = eZContentObject::definition();
        $objects = eZContentObject::fetchList(
            false,
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id]
        );
        static::assertSame(1, count( (array) $objects ));
        static::assertInternalType('array', $objects[0]);
        static::assertEquals($this->article->id, $objects[0]['id']);
    }

    /**
     * Test for eZContentObject::fetchList(), returning objects with published status
     */
    public function testFetchListWithPublishedStatus()
    {
        $eZContentObjectDefinition = eZContentObject::definition();
        $objects = eZContentObject::fetchList(
            true,
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id, 'status' => eZContentObject::STATUS_PUBLISHED]
        );
        static::assertSame(1, count( (array) $objects ));
        static::assertInstanceOf('eZContentObject', $objects[0]);
        static::assertEquals($this->article->id, $objects[0]->attribute( 'id' ));
    }

    /**
     * Test for eZContentObject::fetchList(), returning objects with archived status

     */
    public function testFetchListWithArchivedStatus()
    {
        $this->article->setAttribute( 'status', eZContentObject::STATUS_ARCHIVED );
        $this->article->store();

        $eZContentObjectDefinition = eZContentObject::definition();
        $objects = eZContentObject::fetchList(
            true,
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id, 'status' => eZContentObject::STATUS_ARCHIVED]
        );
        static::assertSame(1, count( (array) $objects ));
    }

    /**
     * Test for eZContentObject::fetchListCount(), using content object ID
     */
    public function testFetchListCountOnObjectID()
    {
        $eZContentObjectDefinition = eZContentObject::definition();
        $count = eZContentObject::fetchListCount(
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id]
        );
        static::assertEquals(1, $count);
    }

    /**
     * Test for eZContentObject::fetchListCount(), using content object ID + published status
     */
    public function testFetchListCountOnObjectIDAndPublishedStatus()
    {
        $eZContentObjectDefinition = eZContentObject::definition();
        $count = eZContentObject::fetchListCount(
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id, 'status' => eZContentObject::STATUS_PUBLISHED]
        );
        static::assertEquals(1, $count);
    }

    /**
     * Test for eZContentObject::fetchListCount(), using content object ID + archived status
     */
    public function testFetchListCountOnObjectIDAndArchivedStatus()
    {
        $this->article->setAttribute( 'status', eZContentObject::STATUS_ARCHIVED );
        $this->article->store();

        $eZContentObjectDefinition = eZContentObject::definition();
        $count = eZContentObject::fetchListCount(
            [$eZContentObjectDefinition['name'] . ".id" => $this->article->id, 'status' => eZContentObject::STATUS_ARCHIVED]
        );
        static::assertEquals(1, $count);
    }
}

?>
