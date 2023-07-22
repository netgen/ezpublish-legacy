<?php
/**
 * File containing the eZPolicyLimitation class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPolicyLimitation ezpolicylimitation.php
  \ingroup eZRole
  \brief Defines a limitation for a policy in the permission system

*/
class eZPolicyLimitation extends eZPersistentObject
{
    /*!
     Constructor
    */
    public function __construct( $row )
    {
          parent::__construct( $row );
          $this->NodeID = 0;
    }

    static function definition()
    {
        static $definition = ["fields" => ["id" => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'policy_id' => ['name' => 'PolicyID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZPolicy', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'identifier' => ['name' => 'Identifier', 'datatype' => 'string', 'default' => '', 'required' => true]], "keys" => ["id"], "function_attributes" => ['policy' => 'policy', 'values' => 'valueList', 'values_as_array' => 'allValues', 'values_as_string' => 'allValuesAsString', 'values_as_array_with_names' => 'allValuesAsArrayWithNames', 'limit_value' => 'limitValue'], "increment_key" => "id", "sort" => ["id" => "asc"], "class_name" => "eZPolicyLimitation", "name" => "ezpolicy_limitation"];
        return $definition;
    }

    function limitValue()
    {
        return $this->LimitValue;
    }

    /*!
     Get policy object of this policy limitation
    */
    function policy()
    {
        return eZPolicy::fetch( $this->attribute( 'policy_id' ) );
    }

    function setAttribute( $attr, $val )
    {
        switch( $attr )
        {
            case 'limit_value':
            {
                $this->LimitValue = $val;
            } break;

            default:
            {
                parent::setAttribute( $attr, $val );
            } break;
        }
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function createNew( $policyID, $identifier )
    {
        $policyParameter = new eZPolicyLimitation( [] );
        $policyParameter->setAttribute( 'policy_id', $policyID );
        $policyParameter->setAttribute( 'identifier', $identifier );
        $policyParameter->store();

        return $policyParameter;
    }

    /*!
     \static
     Create a new policy limitation for the policy \a $policyID with the identifier \a $identifier.
     \note The limitation is not stored.
    */
    static function create( $policyID, $identifier )
    {
        $row = ['id' => null, 'policy_id' => $policyID, 'identifier' => $identifier];
        return new eZPolicyLimitation( $row );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function removeSelected( $ID )
    {
        eZPersistentObject::removeObject( eZPolicyLimitation::definition(),
                                          ["id" => $ID] );
    }

    static function fetchByIdentifier( $policyID, $identifier, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZPolicyLimitation::definition(),
                                                null,
                                                ["policy_id" => $policyID, "identifier" => $identifier],
                                                $asObject );
    }

    static function fetchByPolicyID( $policyID, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZPolicyLimitation::definition(),
                                                    null,
                                                    ["policy_id" => $policyID],
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function copy( $policyID )
    {
        $newParameter = eZPolicyLimitation::createNew( $policyID, $this->attribute( 'identifier' ) );
        foreach( $this->attribute( 'values' ) as $value )
        {
            $value->copy( $newParameter->attribute( 'id' ) );
        }
    }

    /*!
     \sa removeThis
    */
    static function removeByID( $id )
    {
        $db = eZDB::instance();

        $idString = $db->escapeString( $id );
        $db->begin();

        $db->query( "DELETE FROM ezpolicy_limitation_value
                     WHERE ezpolicy_limitation_value.limitation_id = '$idString'" );

        $db->query( "DELETE FROM ezpolicy_limitation
                     WHERE ezpolicy_limitation.id = '$idString' " );
        $db->commit();
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function removeThis()
    {
        eZPolicyLimitation::removeByID( $this->attribute( 'id' ) );
    }

    function allValuesAsString()
    {
        $str='';
        foreach ( $this->attribute( 'values' ) as $value )
        {
            if ( $str == '' )
            {
                $str .= $value->attribute( 'value' );
            }else
            {
                $str .= ',' . $value->attribute( 'value' );
            }
        }
        return $str;
    }

    function allValuesAsArrayWithNames()
    {
        $returnValue = null;
        $valueList   = $this->attribute( 'values_as_array' );
        $names       = [];
        $policy      = $this->attribute( 'policy' );
        if ( !$policy )
        {
            return $returnValue;
        }

        $currentModule = $policy->attribute( 'module_name' );
        $mod = eZModule::exists( $currentModule );
        if ( !is_object( $mod ) )
        {
            eZDebug::writeError( 'Failed to fetch instance for module ' . $currentModule );
            return $returnValue;
        }
        $functions = $mod->attribute( 'available_functions' );
        $functionNames = array_keys( $functions );

        $currentFunction = $policy->attribute( 'function_name' );
        $limitationValueArray = [];

        $limitation = $functions[$currentFunction ][$this->attribute( 'identifier' )];

        if ( $limitation &&
             isset( $limitation['class'] ) &&
             (is_countable($limitation[ 'values' ]) ? count( $limitation[ 'values' ] ) : 0) == 0  )
        {
            $obj = new $limitation['class']( [] );
            $limitationValueList = call_user_func_array ( [$obj, $limitation['function']] , $limitation['parameter'] );
            foreach( $limitationValueList as $limitationValue )
            {
                $limitationValuePair = [];
                $limitationValuePair['Name'] = $limitationValue[ 'name' ];
                $limitationValuePair['value'] = $limitationValue[ 'id' ];
                $limitationValueArray[] = $limitationValuePair;
            }
        }
        else if ( $limitation['name'] === 'Node' )
        {
            foreach ( $valueList as $value )
            {
                $node = eZContentObjectTreeNode::fetch( $value, false, false );
                if ( $node == null )
                    continue;
                $limitationValuePair = [];
                $limitationValuePair['Name'] = $node['name'];
                $limitationValuePair['value'] = $value;
                $limitationValuePair['node_data'] = $node;
                $limitationValueArray[] = $limitationValuePair;
            }
        }
        else if ( $limitation['name'] === 'Subtree' )
        {
            foreach ( $valueList as $value )
            {
                $subtreeObject = eZContentObjectTreeNode::fetchByPath( $value, false );
                if ( $subtreeObject != null )
                {
                    $limitationValuePair = [];
                    $limitationValuePair['Name'] = $subtreeObject['name'];
                    $limitationValuePair['value'] = $value;
                    $limitationValuePair['node_data'] = $subtreeObject;
                    $limitationValueArray[] = $limitationValuePair;
                }
            }
        }
        else
        {
            $limitationValueArray = $limitation[ 'values' ];
        }
        $limitationValuesWithNames = [];
        foreach ( array_keys( $valueList ) as $key )
        {
            $value = $valueList[$key];
            if ( isset( $limitationValueArray ) )
            {
                reset( $limitationValueArray );
                foreach ( array_keys( $limitationValueArray ) as $ckey )
                {
                    if ( $value == $limitationValueArray[$ckey]['value'] )
                    {
                        $limitationValuesWithNames[] = $limitationValueArray[$ckey];
                    }
                }
            }
        }

        return $limitationValuesWithNames;
    }

    /*!
     Get limitation array

     \return access limitation array
    */
    function limitArray()
    {
        $limitValues = $this->attribute( 'values' );

        $valueArray = [];

        foreach ( array_keys( $limitValues ) as $valueKey )
        {
            $valueArray[] = $limitValues[$valueKey]->attribute( 'value' );
        }

        return [$this->attribute( 'identifier' ) => $valueArray];
    }

    function allValues()
    {
        $values = [];
        foreach ( $this->attribute( 'values' ) as $value )
        {
                $values[] = $value->attribute( 'value' );
        }

        return $values;
    }

    function valueList()
    {
        if ( !isset( $this->Values ) )
        {
            $values = eZPersistentObject::fetchObjectList( eZPolicyLimitationValue::definition(),
                                                           null, ['limitation_id' => $this->attribute( 'id')], null, null,
                                                           true);

            if ( $this->LimitValue )
            {
                $values[] = new eZPolicyLimitationValue( ['id' => -1, 'value' => $this->LimitValue] );
            }

            $this->Values = $values;
        }

        return $this->Values;
    }

    static function findByType( $type, $value, $asObject = true, $useLike = true )
    {
        $cond = '';
        $db = eZDB::instance();
        $value = $db->escapeString( $value );
        $type = $db->escapeString( $type );
        if ( $useLike === true )
        {
            $cond = "ezpolicy_limitation_value.value like '$value%' ";
        }
        else
        {
            $cond = "ezpolicy_limitation_value.value = '$value' ";
        }

        $query = "SELECT DISTINCT ezpolicy_limitation.*
                  FROM ezpolicy_limitation,
                       ezpolicy_limitation_value
                  WHERE
                       ezpolicy_limitation.identifier = '$type' AND
                       $cond AND
                       ezpolicy_limitation_value.limitation_id =  ezpolicy_limitation.id";

        $dbResult = $db->arrayQuery( $query );
        $resultArray = [];
        $resultCount = count( $dbResult );
        for( $i = 0; $i < $resultCount; $i++ )
        {
            if ( $asObject )
            {
                $resultArray[] = new eZPolicyLimitation( $dbResult[$i] );
            }
            else
            {
                $resultArray[] = $dbResult[$i]['id'];
            }
        }
        return $resultArray;
    }

    // Used for assign subtree matching
    public $LimitValue;

    public $PolicyID;
    public $LimitationID;
    public $Value;
    public $Values;

}

?>
