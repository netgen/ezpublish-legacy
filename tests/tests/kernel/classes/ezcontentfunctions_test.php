<?php
/**
 * File containing the eZContentFunctionsTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZContentFunctionsTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZContentFunctions Unit Tests" );
    }

    public function testUpdateAndPublishObject()
    {
        // create content object first
        $object = new ezpObject( "folder", 2 );
        $object->title = __FUNCTION__. '::' . __LINE__ . '::' . time();
        $object->publish();

        $contentObjectID = $object->attribute( 'id' );

        if( $object instanceof eZContentObject )
        {
            $now       = date( 'Y/m/d H:i:s', time() );
            $sectionID = 3;
            $remoteID  = md5( $now );

            $attributeList = ['name'              => 'name ' . $now, 'short_name'        => 'short_name ' . $now, 'short_description' => 'short_description' . $now, 'description'       => 'description' . $now, 'show_children'     => false];

            $params               = [];
            $params['attributes'] = $attributeList;
            $params['remote_id']  = $remoteID;
            $params['section_id'] = $sectionID;

            $result = eZContentFunctions::updateAndPublishObject( $object, $params );

            static::assertTrue($result);

            $object = eZContentObject::fetch( $contentObjectID );
            static::assertEquals($object->attribute( 'section_id' ), $sectionID);
            static::assertEquals($object->attribute( 'remote_id' ), $remoteID);

            $dataMap = $object->dataMap();
            static::assertEquals($attributeList['name'], $dataMap['name']->content());
            static::assertEquals($attributeList['short_name'], $dataMap['short_name']->content());
            static::assertEquals($attributeList['short_description'], $dataMap['short_description']->content());
            static::assertEquals($attributeList['description'], $dataMap['description']->content());
            static::assertEquals($attributeList['show_children'], (bool)$dataMap['show_children']->content());
        }
    }
}

?>
