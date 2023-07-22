<?php
/**
 * File containing the eZTemplateDigestOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateDigestOperator eztemplatedigestoperator.php
  \ingroup eZTemplateOperators
\code

\endcode

*/

class eZTemplateDigestOperator
{
    public function __construct()
    {
        if ( function_exists( 'sha1' ) )
        {
            $this->Operators[] = 'sha1';
        }
        foreach ( $this->Operators as $operator )
        {
            $name = $operator . 'Name';
            $name[0] = $name[0] & "\xdf";
            $this->$name = $operator;
        }
    }

    /*!
     Returns the template operators.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    function operatorTemplateHints()
    {
        return [$this->Crc32Name => ['input' => true, 'output' => true, 'parameters' => false, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation-func' => 'hashTransformation'], $this->Md5Name => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation-func' => 'hashTransformation'], $this->Sha1Name => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation-func' => 'hashTransformation'], $this->Rot13Name => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation-func' => 'hashTransformation']];
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
        return false;
    }

    function hashTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        if ( ( (is_countable($parameters) ? count( $parameters ) : 0) != 1) )
        {
            return false;
        }

        $code = '';
        $function = '';
        $newElements = [];
        $values = [$parameters[0]];

        $function = match ($operatorName) {
            'crc32' => "eZSys::ezcrc32",
            'rot13' => 'str_rot13',
            default => $operatorName,
        };

        $code .= "%output% = $function( %1% );\n";

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    /*!
     Display the variable.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters,
                     $placement )
    {
        $digestData = $operatorValue;
        switch ( $operatorName )
        {
            // Calculate and return crc32 polynomial.
            case $this->Crc32Name:
            {
                $operatorValue = eZSys::ezcrc32( $digestData );
            }break;

            // Calculate the MD5 hash.
            case $this->Md5Name:
            {
                $operatorValue = md5( (string) $digestData );
            }break;

            // Calculate the SHA1 hash.
            case $this->Sha1Name:
            {
                $operatorValue = sha1( (string) $digestData );
            }break;

            // Preform rot13 transform on the string.
            case $this->Rot13Name:
            {
                $operatorValue = str_rot13( (string) $digestData );
            }break;

            // Default case: something went wrong - unknown things...
            default:
            {
                $tpl->warning( $operatorName, "Unknown input type '$operatorName'", $placement );
            } break;
        }
    }

    /// The array of operators, used for registering operators
    public $Operators = ['crc32', 'md5', 'rot13'];
}

?>
