<?php
/**
 * File containing the eZTemplateNl2BrOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateNl2BrOperator eztemplatenl2broperator.php
  \ingroup eZTemplateOperators
\code

\endcode

*/

class eZTemplateNl2BrOperator
{
    /**
     * Initializes the object with the name $name, default is "nl2br".
     */
    public function __construct()
    {
        $this->Nl2brName = 'nl2br';
    }

    /*!
     Returns the template operators.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return [];
    }

    function operatorTemplateHints()
    {
        return [$this->Nl2brName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'nl2brTransformation']];
    }

    function nl2brTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;

        if ( ( (is_countable($parameters) ? count( $parameters ) : 0) != 1) )
        {
            return false;
        }
        $newElements = [];

        $values[] = $parameters[0];
        $code = "%output% = nl2br( %1% );\n";

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    /*!
     Display the variable.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = str_replace( "\n",
                                      "<br />",
                                      (string) $operatorValue );
    }

    /// The array of operators, used for registering operators
    public $Operators = ['nl2br'];
}

?>
