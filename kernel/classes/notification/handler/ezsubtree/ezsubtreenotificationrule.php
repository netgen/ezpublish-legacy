<?php
/**
 * File containing the eZSubtreeNotificationRule class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSubtreeNotificationRule ezsubtreenotificationrule.php
  \brief The class eZSubtreeNotificationRule does

*/
class eZSubtreeNotificationRule extends eZPersistentObject
{
    static function definition()
    {
        return ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], "user_id" => ['name' => "UserID", 'datatype' => 'integer', 'default' => '', 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], "use_digest" => ['name' => "UseDigest", 'datatype' => 'integer', 'default' => 0, 'required' => true], "node_id" => ['name' => "NodeID", 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZContentObjectTreeNode', 'foreign_attribute' => 'node_id', 'multiplicity' => '1..*']], "keys" => ["id"], "function_attributes" => ['node' => 'node'], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZSubtreeNotificationRule", "name" => "ezsubtree_notification_rule"];
    }


    static function create( $nodeID, $userID, $useDigest = 0 )
    {
        $rule = new eZSubtreeNotificationRule( ['user_id' => $userID, 'use_digest' => $useDigest, 'node_id' => $nodeID] );
        return $rule;
    }

    static function fetchNodesForUserID( $userID, $asObject = true )
    {
        $nodeIDList = eZPersistentObject::fetchObjectList( eZSubtreeNotificationRule::definition(),
                                                            ['node_id'], ['user_id' => $userID],
                                                            null,null,false );
        $nodes = [];
        if ( $asObject )
        {
            foreach ( $nodeIDList as $nodeRow )
            {
                $nodes[] = eZContentObjectTreeNode::fetch( $nodeRow['node_id'] );
            }
        }
        else
        {
            foreach ( $nodeIDList as $nodeRow )
            {
                $nodes[] = $nodeRow['node_id'];
            }
        }
        return $nodes;
    }

    static function fetchList( $userID, $asObject = true, $offset = false, $limit = false )
    {
        return eZPersistentObject::fetchObjectList( eZSubtreeNotificationRule::definition(),
                                                            null, ['user_id' => $userID],
                                                            null, ['offset' => $offset, 'length' => $limit], $asObject );
    }

    static function fetchListCount( $userID )
    {
        $countRes = eZPersistentObject::fetchObjectList( eZSubtreeNotificationRule::definition(),
                                                         [],
                                                         ['user_id' => $userID],
                                                         false,
                                                         null,
                                                         false,
                                                         false,
                                                         [['operation' => 'count( id )', 'name' => 'count']] );
        return $countRes[0]['count'];
    }

    /**
     * Fetch allowed subtreenotification rules based on node_id list and a
     * content object
     *
     * @param array $nodeIDList node id list for notification event
     * @param eZContentObject content object to add
     *
     * @return array matching subtree notification rule data
     */
    static function fetchUserList( $nodeIDList, $contentObject )
    {
        if ( count( $nodeIDList ) == 0 )
        {
            $retValue = [];
            return $retValue;
        }

        $db = eZDB::instance();
        $concatString = $db->concatString(  ['user_tree.path_string', "'%'"] );

        // Select affected users
        $sqlINString = $db->generateSQLINStatement( $nodeIDList, 'subtree_rule.node_id', false, false, 'int' );
        $sql = "SELECT DISTINCT subtree_rule.user_id,
                                user_node.node_id
                FROM ezsubtree_notification_rule subtree_rule,
                     ezcontentobject_tree user_node,
                     ezuser_setting
                WHERE $sqlINString AND
                      user_node.contentobject_id = subtree_rule.user_id AND
                      ezuser_setting.user_id = subtree_rule.user_id AND
                      user_node.is_invisible = 0 AND
                      ezuser_setting.is_enabled = 1";
        $userPart = $db->arrayQuery( $sql );

        // Remove duplicates
        $userNodeIDList = [];
        foreach ( $userPart as $row )
            $userNodeIDList[] = $row['node_id'];
        $userNodeIDList = array_unique( $userNodeIDList );

        if ( count( $userNodeIDList ) == 0 )
        {
            $retValue = [];
            return $retValue;
        }

        // Select affected nodes
        $sqlINString = $db->generateSQLINStatement( $userNodeIDList, 'user_node.node_id', false, false, 'int' );
        $sql = "SELECT DISTINCT user_node.node_id,
                                user_node.path_string,
                                user_tree.contentobject_id
                FROM ezcontentobject_tree user_node,
                     ezcontentobject_tree user_tree
                WHERE $sqlINString AND
                      user_node.path_string LIKE $concatString";
        $nodePart = $db->arrayQuery( $sql );

        // Remove duplicates
        $objectIDList = [];
        foreach ( $nodePart as $row )
            if ( $row['contentobject_id'] != '0' )
                $objectIDList[] = $row['contentobject_id'];
        $objectIDList = array_unique( $objectIDList );

        if ( count( $objectIDList ) == 0 )
        {
            $retValue = [];
            return $retValue;
        }

        // Select affected roles and policies
        $sqlINString = $db->generateSQLINStatement( $objectIDList, 'user_role.contentobject_id', false, false, 'int' );
        $sql = "SELECT DISTINCT user_role.contentobject_id,
                                policy.id AS policy_id,
                                user_role.limit_identifier AS limitation,
                                user_role.limit_value AS value
                FROM ezuser_role user_role,
                     ezpolicy policy
                WHERE $sqlINString AND
                      ( user_role.role_id=policy.role_id AND
                        ( policy.module_name='*' OR
                          ( policy.module_name='content' AND
                            ( policy.function_name='*' OR
                              policy.function_name='read'
                            )
                          )
                        )
                      )";
        $rolePart = $db->arrayQuery( $sql );

        // Build resultArray. Make sure there are no duplicates.
        $resultArray = [];
        foreach ( $userPart as $up )
        {
            foreach ( $nodePart as $np )
            {
                if ( $up['node_id'] == $np['node_id'] )
                {
                    foreach ( $rolePart as $rp )
                    {
                        if ( $np['contentobject_id'] == $rp['contentobject_id'] )
                        {
                            $key = $rp['policy_id'] . $up['user_id'] . $rp['limitation'] . $rp['value'];
                            $resultArray[$key] = ['policy_id' => $rp['policy_id'], 'user_id' => $up['user_id'], 'limitation' => $rp['limitation'], 'value' => $rp['value']];
                        }
                    }
                }
            }
        }

        $policyIDArray = [];
        $limitedPolicyIDArray = [];
        $userIDArray = [];
        foreach( $resultArray as $result )
        {
            $userIDArray[(string)$result['user_id']] = (int)$result['user_id'];
        }

        foreach( $resultArray as $result )
        {
            if ( $result['limitation'] == '' )
            {
                $policyIDArray[(string)$result['policy_id']][] =& $userIDArray[(string)$result['user_id']];
            }
            else
            {
                $limitedPolicyIDArray[] = ['user_id' => $userIDArray[(string)$result['user_id']], 'limitation' => $result['limitation'], 'value' => $result['value'], 'policyID' => $result['policy_id']];
            }
        }

        $acceptedUserArray = [];
        foreach( array_keys( $policyIDArray ) as $policyID )
        {
            foreach( array_keys( $policyIDArray[$policyID] ) as $key )
            {
                if ( $policyIDArray[$policyID][$key] === false )
                {
                    unset( $policyIDArray[$policyID][$key] );
                }
            }

            if ( count( $policyIDArray[$policyID] ) == 0 )
            {
                continue;
            }

            $userArray = eZSubtreeNotificationRule::checkObjectAccess( $contentObject, $policyID, $policyIDArray[$policyID] );
            $acceptedUserArray = array_merge( $acceptedUserArray, $userArray );

            foreach ( $userArray as $userID )
            {
                $userIDArray[(string)$userID] = false;
            }
        }

        foreach( $limitedPolicyIDArray as $policyEntry )
        {
            if ( $policyEntry['user_id'] === false )
            {
                continue;
            }

            $userArray = eZSubtreeNotificationRule::checkObjectAccess( $contentObject,
                                                                       $policyEntry['policyID'],
                                                                       [$policyEntry['user_id']],
                                                                       [$policyEntry['limitation'] => $policyEntry['value']] );

            $acceptedUserArray = array_merge( $acceptedUserArray, $userArray );
            foreach ( $userArray as $userID )
            {
                $userIDArray[(string)$userID] = false;
            }
        }
        $acceptedUserArray = array_unique( $acceptedUserArray );

        foreach( array_keys( $acceptedUserArray ) as $key )
        {
            if ( !is_int( $acceptedUserArray[$key] ) or $acceptedUserArray[$key] == 0 )
            {
                unset( $acceptedUserArray[$key] );
            }
        }

        if ( count( $acceptedUserArray ) == 0 )
        {
            $retValue = [];
            return $retValue;
        }

        $nodeIDWhereString = $db->generateSQLINStatement( $nodeIDList, 'rule.node_id', false, false, 'int' );
        $userIDWhereString = $db->generateSQLINStatement( $acceptedUserArray, 'rule.user_id', false, false, 'int' );
        $rules = $db->arrayQuery( "SELECT rule.user_id, rule.use_digest, ezuser.email as address
                                      FROM ezsubtree_notification_rule rule, ezuser
                                      WHERE rule.user_id=ezuser.contentobject_id AND
                                            $nodeIDWhereString AND
                                            $userIDWhereString" );
        return $rules;
    }

    /*!
     \private

     Check access for specified policy on object, and user list.

     \param Content object
     \param policyID
     \param userID array
     \param user limits

     \return array of user ID's which has access to object
    */
    static function checkObjectAccess( $contentObject, $policyID, $userIDArray, $userLimits = false )
    {
        $policy = eZPolicy::fetch( $policyID );
        if ( $userLimits )
        {
            $policy->setAttribute( 'limit_identifier', 'User_' . array_key_first( $userLimits ) );
            $policy->setAttribute( 'limit_value', current( $userLimits ) );
        }

        $limitationArray = $policy->accessArray();
        $limitationArray = current( current( $limitationArray ) );
        $accessUserIDArray = $userIDArray;

        if ( isset( $limitationArray['*'] ) &&
             $limitationArray['*'] == '*' )
        {
            $returnArray = [];
            foreach ( $accessUserIDArray as $userID )
            {
                $returnArray[] = $userID;
            }
            return $returnArray;
        }

        $limitationArray = current( $limitationArray );

        $user = eZUser::currentUser();
        $classID = $contentObject->attribute( 'contentclass_id' );
        $nodeArray = $contentObject->attribute( 'assigned_nodes' );

        if ( isset( $limitationArray['Subtree' ] ) )
        {
            $checkedSubtree = false;
        }
        else
        {
            $checkedSubtree = true;
            $nodeSubtree = true;
        }
        if ( isset( $limitationArray['Node'] ) )
        {
            $checkedNode = false;
        }
        else
        {
            $checkedNode = true;
            $nodeLimit = true;
        }

        foreach ( array_keys( $limitationArray ) as $key )
        {
            if ( (is_countable($accessUserIDArray) ? count( $accessUserIDArray ) : 0) == 0 )
            {
                return [];
            }
            switch( $key )
            {
                case 'Class':
                {
                    if ( !in_array( $contentObject->attribute( 'contentclass_id' ), $limitationArray[$key] )  )
                    {
                        return [];
                    }
                } break;

                case 'ParentClass':
                {

                    if ( !in_array( $contentObject->attribute( 'contentclass_id' ), $limitationArray[$key]  ) )
                    {
                        return [];
                    }
                } break;

                case 'Section':
                case 'User_Section':
                {
                    if ( !in_array( $contentObject->attribute( 'section_id' ), $limitationArray[$key]  ) )
                    {
                        return [];
                    }
                } break;

                case 'Owner':
                {
                    if ( in_array( $contentObject->attribute( 'owner_id' ), $userIDArray ) )
                    {
                        $accessUserIDArray = [$contentObject->attribute( 'owner_id' )];
                    }
                    else if ( in_array( $contentObject->attribute( 'id' ), $userIDArray ) )
                    {
                        $accessUserIDArray = [$contentObject->attribute( 'id' )];
                    }
                    else
                    {
                        return [];
                    }
                } break;

                case 'Node':
                {
                    $nodeLimit = true;
                    foreach ( $nodeArray as $node )
                    {
                        if( in_array( $node->attribute( 'node_id' ), $limitationArray[$key] ) )
                        {
                            $nodeLimit = false;
                            break;
                        }
                    }
                    if ( $nodeLimit && $checkedSubtree && $nodeSubtree )
                    {
                        return [];
                    }
                    $checkedNode = true;
                } break;

                case 'Subtree':
                {
                    $nodeSubtree = true;
                    foreach ( $nodeArray as $node )
                    {
                        $path = $node->attribute( 'path_string' );
                        $subtreeArray = $limitationArray[$key];
                        $validSubstring = false;
                        foreach ( $subtreeArray as $subtreeString )
                        {
                            if ( strstr( (string) $path, (string) $subtreeString ) )
                            {
                                $nodeSubtree = false;
                                break;
                            }
                        }
                        if ( !$nodeSubtree )
                        {
                            break;
                        }
                    }
                    if ( $nodeSubtree && $checkedNode && $nodeLimit )
                    {
                        return [];
                    }
                    $checkedSubtree = true;
                } break;

                case 'User_Subtree':
                {
                    $userSubtreeLimit = true;
                    foreach ( $nodeArray as $node )
                    {
                        $path = $node->attribute( 'path_string' );
                        $subtreeArray = $limitationArray[$key];
                        $validSubstring = false;
                        foreach ( $subtreeArray as $subtreeString )
                        {
                            if ( strstr( (string) $path, (string) $subtreeString ) )
                            {
                                $userSubtreeLimit = false;
                                break;
                            }
                        }
                        if ( !$userSubtreeLimit )
                        {
                            break;
                        }
                    }
                    if ( $userSubtreeLimit )
                    {
                        return [];
                    }
                } break;
                default:
                {
                    //check object state group limitation
                    if ( str_starts_with($key, 'StateGroup_') )
                    {
                        if ( count( array_intersect( $limitationArray[$key],
                                                     $contentObject->attribute( 'state_id_array' ) ) ) == 0 )
                        {
                            return [];
                        }
                    }
                }
            }
        }

        $returnArray = [];
        foreach ( $accessUserIDArray as $userID )
        {
            $returnArray[] = $userID;
        }
        return $returnArray;
    }

    function node()
    {
        if ( $this->Node == null )
        {
            $this->Node = eZContentObjectTreeNode::fetch( $this->attribute( 'node_id' ) );
        }
        return $this->Node;
    }

    static function removeByNodeAndUserID( $userID, $nodeID )
    {
        eZPersistentObject::removeObject( eZSubtreeNotificationRule::definition(), ['user_id' => $userID, 'node_id' => $nodeID] );
    }

    /*!
     \static

     Remove notifications by user id

     \param userID
    */
    static function removeByUserID( $userID )
    {
        eZPersistentObject::removeObject( eZSubtreeNotificationRule::definition(), ['user_id' => $userID] );
    }

    /*!
     \static
     Cleans up all notification rules for all users.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezsubtree_notification_rule" );
    }

    public $Node = null;
}

?>
