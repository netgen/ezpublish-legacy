<?php
/**
 * File containing the eZContentObjectTreeNodeTest2 class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZContentObjectTreeNodeTest2 extends ezpDatabaseTestCase
{
    public function providerForTestIssue23528()
    {
        return [
            // integer
            ['published', "                            ( ezcontentobject.published IN (1,3,0,1,0)  ) AND "],
            ['modified', "                            ( ezcontentobject.modified IN (1,3,0,1,0)  ) AND "],
            ['modified_subnode', "                            ( ezcontentobject_tree.modified_subnode IN (1,3,0,1,0)  ) AND "],
            ['node_id', "                            ( ezcontentobject_tree.node_id IN (1,3,0,1,0)  ) AND "],
            ['contentobject_id', "                            ( ezcontentobject_tree.contentobject_id IN (1,3,0,1,0)  ) AND "],
            ['section', "                            ( ezcontentobject.section_id IN (1,3,0,1,0)  ) AND "],
            ['state', "                            ( ezcontentobject.id IN (SELECT contentobject_id FROM ezcobj_state_link WHERE contentobject_state_id IN ( 1, 3, 0, 1, 0 )) ) AND "],
            ['depth', "                            ( ezcontentobject_tree.depth IN (1,3,0,1,0)  ) AND "],
            ['priority', "                            ( ezcontentobject_tree.priority IN (1,3,0,1,0)  ) AND "],
            ['owner', "                            ( ezcontentobject.owner_id IN (1,3,0,1,0)  ) AND "],
            ['visibility', "                            ( ezcontentobject_tree.is_invisible IN (1,3,0,1,0)  ) AND "],
            // string
            ['path', "                            ( ezcontentobject_tree.path_string IN ('1','3','foo','1foo','foo_1')  ) AND "],
            ['class_identifier', "                            ( ezcontentclass.identifier IN ('1','3','foo','1foo','foo_1')  ) AND "],
            ['class_name', "                            ( ezcontentclass_name.name IN ('1','3','foo','1foo','foo_1')  ) AND "],
            ['name', "                            ( ezcontentobject_name.name IN ('1','3','foo','1foo','foo_1')  ) AND "],
            // custom
            [1, "
                                  a0.contentobject_id = ezcontentobject.id AND
                                  a0.contentclassattribute_id = 1 AND
                                  a0.version = ezcontentobject_name.content_version AND 
 ( a0.language_id & ezcontentobject.language_mask > 0 AND
     ( (   ezcontentobject.language_mask - ( ezcontentobject.language_mask & a0.language_id ) ) & 1 )
   + ( ( ( ezcontentobject.language_mask - ( ezcontentobject.language_mask & a0.language_id ) ) & 2 ) )
   <
     ( a0.language_id & 1 )
   + ( ( a0.language_id & 2 ) )
 ) 
 AND                             ( a0.sort_key_string IN ('1','3','foo','1foo','foo_1')  ) AND "],
        ];
    }

    /**
     * eZContentObjectTreeNode::createAttributeFilterSQLStrings() returns
     * invalid 'in'/'not in' SQL statements
     *
     * @link http://issues.ez.no/23528
     * @dataProvider providerForTestIssue23528
     */
    public function testIssue23528( $name, $expected )
    {
        $params = [1, '3', 'foo', '1foo', 'foo_1'];
        $attributeFilterParams = [[$name, 'in', $params]];
        $attributeFilter = eZContentObjectTreeNode::createAttributeFilterSQLStrings( $attributeFilterParams );
        static::assertEquals($expected, $attributeFilter['where']);
    }

    public function testSortingByTrashed()
    {
        // test sorting by trashed
        $sortList = [['trashed', true]];
        $result = eZContentObjectTreeNode::createSortingSQLStrings( $sortList, 'ezcot' );
        static::assertEquals('ezcot.trashed asc', strtolower( (string) $result[ 'sortingFields' ] ));
    }
}
