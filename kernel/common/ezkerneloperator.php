<?php
/**
 * File containing the eZKernelOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZKerneloperator ezkerneloperator.php
  \brief The class eZKernelOperator does handles eZ Publish preferences

*/
class eZKernelOperator
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name = "ezpreference" )
    {
        $this->Operators = [$name];
    }

    /*!
      Returns the template operators.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list exists per operator type.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return ['ezpreference' => ['name' => ['type' => 'string', 'required' => true, 'default' => false]]];
    }

    function operatorTemplateHints()
    {
        return ['ezpreference' => ['input' => false, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => false, 'element-transformation-func' => 'preferencesTransformation']];
    }

    function preferencesTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                        $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        if ( (is_countable($parameters[0]) ? count( $parameters[0] ) : 0) == 0 )
            return false;
        $values = [];
        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $name = eZTemplateNodeTool::elementConstantValue( $parameters[0] );
            $nameText = eZPHPCreator::variableText( $name, 0, 0, false );
        }
        else
        {
            $nameText = '%1%';
            $values[] = $parameters[0];
        }
        return [eZTemplateNodeTool::createCodePieceElement( "%output% = eZPreferences::value( $nameText );\n",
                                                                  $values )];
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        switch ( $operatorName )
        {
            case 'ezpreference':
            {
                $name = $namedParameters['name'];
                $value = eZPreferences::value( $name );
                $operatorValue = $value;
            }break;

            default:
            {
                eZDebug::writeError( "Unknown kernel operator: $operatorName" );
            }break;
        }
    }
    public $Operators;
}
?>
