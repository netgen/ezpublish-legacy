<?php
/**
 * File containing the eZTemplateArithmeticOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateArithmeticOperator eztemplatearithmeticoperator.php
  \brief The class eZTemplateArithmeticOperator does

  sum
  sub
  inc
  dec

  div
  mod
  mul

  max
  min

  abs
  ceil
  floor
  round

  int
  float

  count

*/

class eZTemplateArithmeticOperator
{
    public function __construct()
    {
        foreach ( $this->Operators as $operator )
        {
            $name = $operator . 'Name';
            $name[0] = $name[0] & "\xdf";
            $this->$name = $operator;
        }
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    function operatorTemplateHints()
    {
        return [$this->SumName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'basicTransformation'], $this->SubName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'basicTransformation'], $this->MulName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'basicTransformation'], $this->DivName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'basicTransformation'], $this->IncName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'decIncTransformation'], $this->DecName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'decIncTransformation'], $this->ModName => ['input' => true, 'output' => true, 'parameters' => 2, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'modTransformation'], $this->MaxName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'minMaxTransformation'], $this->MinName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'minMaxTransformation'], $this->AbsName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'roundTransformation'], $this->CeilName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'roundTransformation'], $this->FloorName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'roundTransformation'], $this->RoundName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'roundTransformation'], $this->IntName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'castTransformation'], $this->FloatName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'castTransformation'], $this->RomanName => ['input' => true, 'output' => true, 'parameters' => 1, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'romanTransformation'], $this->CountName => ['input' => true, 'output' => true, 'parameters' => 1], $this->RandName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'randTransformation']];
    }

    function basicTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;
        $divOperation = false;
        if ( $function == $this->SumName )
        {
            $operator = '+';
        }
        else if ( $function == $this->SubName )
        {
            $operator = '-';
        }
        else if ( $function == $this->MulName )
        {
            $operator = '*';
        }
        else
        {
            $divOperation = true;
            $operator = '/';
        }

        if ( (is_countable($parameters) ? count( $parameters ) : 0) == 0 )
            return false;
        $newElements = [];

        // Reorder parameters, dynamic elements first then static ones
        // Also combine multiple static ones into a single element
        $notInitialised = true;
        $staticResult = 0;
        $isStaticFirst = false;
        $allNumeric = true;
        $newParameters = [];
        $endParameters = [];
        $parameterIndex = 0;
        foreach ( $parameters as $parameter )
        {
            if ( !eZTemplateNodeTool::isConstantElement( $parameter ) )
            {
                $allNumeric = false;
                $endParameters[] = $parameter;
            }
            else
            {
                $staticValue = (int) eZTemplateNodeTool::elementConstantValue( $parameter );
                if ( $notInitialised )
                {
                    $staticResult = $staticValue;
                    if ( $parameterIndex == 0 )
                        $isStaticFirst = true;
                    $notInitialised = false;
                }
                else
                {
                    if ( $function == 'sum' )
                    {
                        $staticResult += $staticValue;
                    }
                    else if ( $function == 'sub' )
                    {
                        if ( $isStaticFirst )
                            $staticResult -= $staticValue;
                        else
                            $staticResult += $staticValue;
                    }
                    else if ( $function == 'mul' )
                    {
                        $staticResult *= $staticValue;
                    }
                    else
                    {
                        if ( $isStaticFirst )
                            $staticResult /= $staticValue;
                        else
                            $staticResult *= $staticValue;
                    }
                }
                $isPreviousStatic = true;
            }
            ++$parameterIndex;
        }

        if ( $allNumeric )
        {
            $newElements[] = eZTemplateNodeTool::createNumericElement( $staticResult );
            return $newElements;
        }
        else
        {
            if ( !$notInitialised )
            {
                if ( $isStaticFirst )
                    $newParameters[] = [eZTemplateNodeTool::createNumericElement( $staticResult )];
                else
                    $endParameters[] = [eZTemplateNodeTool::createNumericElement( $staticResult )];
            }
            $newParameters = array_merge( $newParameters, $endParameters );

            $code = '';
            if ( $divOperation )
            {
                $code .= '@';
            }
            $code .= '%output% =';
            $counter = 1;
            $index = 0;

            foreach ( $newParameters as $parameter )
            {
                if ( $index > 0 )
                {
                    $code .= " $operator";
                }
                if ( eZTemplateNodeTool::isConstantElement( $parameter ) )
                {
                    $staticValue = eZTemplateNodeTool::elementConstantValue( $parameter );
                    if ( !is_numeric( $staticValue ) )
                        $staticValue = (int)$staticValue;
                    $code .= sprintf(" %F", $staticValue);
                }
                else
                {
                    $code .= " %$counter%";
                    $values[] = $parameter;
                    ++$counter;
                }
                ++$index;
            }
            $code .= ";\n";
        }
        $knownType = 'integer';
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values, false, false, $knownType );
        return $newElements;
    }

    function minMaxTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                   $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;

        if ( (is_countable($parameters) ? count( $parameters ) : 0) == 0 )
            return false;
        $newElements = [];

        /* Check if all variables are integers. This is for optimization */
        $staticResult = [];
        $allNumeric = true;
        foreach ( $parameters as $parameter )
        {
            if ( !eZTemplateNodeTool::isConstantElement( $parameter ) )
            {
                $allNumeric = false;
            }
            else
            {
                $staticResult[] = eZTemplateNodeTool::elementConstantValue( $parameter );
            }
        }

        if ( $allNumeric )
        {
            $staticResult = $function( $staticResult );
            return [eZTemplateNodeTool::createNumericElement( $staticResult )];
        }
        else
        {
            $code = "%output% = $function(";
            $counter = 1;
            foreach ( $parameters as $parameter )
            {
                if ( $counter > 1 )
                {
                    $code .= ', ';
                }
                $code .= " %$counter%";
                $values[] = $parameter;
                ++$counter;
            }
            $code .= ");\n";
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function modTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        if ( (is_countable($parameters) ? count( $parameters ) : 0) != 2 )
            return false;
        $newElements = [];

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) && eZTemplateNodeTool::isConstantElement( $parameters[1] ) )
        {
            $staticResult = eZTemplateNodeTool::elementConstantValue( $parameters[0] ) % eZTemplateNodeTool::elementConstantValue( $parameters[1] );
            return [eZTemplateNodeTool::createNumericElement( $staticResult )];
        }
        else
        {
            $code = "%output% = %1% % %2%;\n";
            $values[] = $parameters[0];
            $values[] = $parameters[1];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function roundTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;

        if ( (is_countable($parameters) ? count( $parameters ) : 0) != 1 )
            return false;
        $newElements = [];

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = $function( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) );
            return [eZTemplateNodeTool::createNumericElement( $staticResult )];
        }
        else
        {
            $code = "%output% = $function( %1% );\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function decIncTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;
        $direction = $this->DecName == $function ? -1 : 1;

        if ( (is_countable($parameters) ? count( $parameters ) : 0) < 1 )
            return false;
        $newElements = [];

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            return [eZTemplateNodeTool::createNumericElement( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) + $direction )];
        }
        else
        {
            $code = "%output% = %1% + $direction;\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function castTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        if ( (is_countable($parameters) ? count( $parameters ) : 0) != 1 )
            return false;
        $newElements = [];

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = ( $operatorName == $this->IntName ) ? (int) eZTemplateNodeTool::elementConstantValue( $parameters[0] ) : (float) eZTemplateNodeTool::elementConstantValue( $parameters[0] );
            return [eZTemplateNodeTool::createNumericElement( $staticResult )];
        }
        else
        {
            $code = "%output% = ($operatorName)%1%;\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function randTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $paramCount = is_countable($parameters) ? count( $parameters ) : 0;
        if ( $paramCount != 0 ||
             $paramCount != 2 )
        {
            return false;
        }
        $values = [];
        $newElements = [];

        if ( $paramCount == 2 )
        {
            $code = "%output% = mt_rand( %1%, %2% );\n";
            $values[] = $parameters[0];
            $values[] = $parameters[1];
        }
        else
        {
            $code = "%output% = mt_rand();\n";
        }

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function romanTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        if ( (is_countable($parameters) ? count( $parameters ) : 0) != 1 )
            return false;
        $newElements = [];

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = $this->buildRoman( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) );
            return [eZTemplateNodeTool::createNumericElement( $staticResult )];
        }
        else
        {
            return false;
        }
    }

    /*!
     \return true to tell the template engine that the parameter list exists per operator type.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return [$this->IncName => ['value' => ['type' => 'mixed', 'required' => false, 'default' => false]], $this->DecName => ['value' => ['type' => 'mixed', 'required' => false, 'default' => false]], $this->RomanName => ['value' => ['type' => 'mixed', 'required' => false, 'default' => false]]];
    }

    /*!
     \private
     \obsolete This function adds too much complexity, don't use it anymore
    */
    function numericalValue( $mixedValue )
    {
        if ( is_array( $mixedValue ) )
        {
            return count( $mixedValue );
        }
        else if ( is_object( $mixedValue ) )
        {
            if ( method_exists( $mixedValue, 'attributes' ) )
                return is_countable($mixedValue->attributes()) ? count( $mixedValue->attributes() ) : 0;
            else if ( method_exists( $mixedValue, 'numericalValue' ) )
                return $mixedValue->numericalValue();
        }
        else if ( is_numeric( $mixedValue ) )
            return $mixedValue;
        else
            return 0;
    }

    /*!
     Examines the input value and outputs a boolean value. See class documentation for more information.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters,
                     $placement )
    {
        switch ( $operatorName )
        {
            case $this->RomanName:
            {
                if ( $namedParameters['value'] !== false )
                    $value = $namedParameters['value'];
                else
                    $value = $operatorValue;

                $operatorValue = $this->buildRoman( $value );
            } break;
            case $this->CountName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) == 0 )
                    $mixedValue =& $operatorValue;
                else
                    $mixedValue = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) > 1 )
                    $tpl->extraParameters( $operatorName, is_countable($operatorParameters) ? count( $operatorParameters ) : 0, 1 );
                if ( is_array( $mixedValue ) )
                    $operatorValue = count( $mixedValue );
                else if ( is_object( $mixedValue ) and
                          method_exists( $mixedValue, 'attributes' ) )
                    $operatorValue = is_countable($mixedValue->attributes()) ? count( $mixedValue->attributes() ) : 0;
                else if ( is_string( $mixedValue ) )
                    $operatorValue = strlen( $mixedValue );
                else
                    $operatorValue = 0;
            } break;
            case $this->SumName:
            {
                $value = 0;
                if ( $operatorValue !== null )
                    $value = (int) $operatorValue;
                for ( $i = 0; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    $value += (int) $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->SubName:
            {
                $values = [];
                if ( $operatorValue !== null )
                    $values[] = (int) $operatorValue;
                for ( $i = 0; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $values[] = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                }
                $value = 0;
                if ( count( $values ) > 0 )
                {
                    $value = $values[0];
                    for ( $i = 1; $i < count( $values ); ++$i )
                    {
                        $value -= (int) $values[$i];
                    }
                }
                $operatorValue = $value;
            } break;
            case $this->IncName:
            case $this->DecName:
            {
                if ( $operatorValue !== null )
                    $value = $operatorValue;
                else
                    $value = $namedParameters['value'];
                if ( $operatorName == $this->DecName )
                    --$value;
                else
                    ++$value;
                $operatorValue = $value;
            } break;
            case $this->DivName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $i = 0;
                if ( $operatorValue !== null )
                    $value = (int) $operatorValue;
                else
                    $value = (int) $tpl->elementValue( $operatorParameters[$i++], $rootNamespace, $currentNamespace, $placement );
                for ( ; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( (int) $tmpValue == 0 )
                        $value = 0;
                    else
                        @$value /= (int) $tmpValue;


                }
                $operatorValue = $value;
            } break;
            case $this->ModName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->warning( $operatorName, 'Missing dividend and divisor', $placement );
                    return;
                }
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) == 1 )
                {
                    $dividend = $operatorValue;
                    $divisor = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                }
                else
                {
                    $dividend = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                    $divisor = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace, $placement );
                }
                $operatorValue = $dividend % $divisor;
            } break;
            case $this->MulName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $i = 0;
                if ( $operatorValue !== null )
                    $value = $operatorValue;
                else
                    $value = $tpl->elementValue( $operatorParameters[$i++], $rootNamespace, $currentNamespace, $placement );
                for ( ; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    $value *= (float) $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->MaxName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $i = 0;
                if ( $operatorValue !== null )
                {
                    $value = $operatorValue;
                }
                else
                {
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                    ++$i;
                }
                for ( ; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( $tmpValue > $value )
                        $value = $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->MinName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                for ( $i = 1; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( $tmpValue < $value )
                        $value = $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->AbsName:
            case $this->CeilName:
            case $this->FloorName:
            case $this->RoundName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                    $value = $operatorValue;
                else
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                switch ( $operatorName )
                {
                    case $this->AbsName:
                    {
                        $operatorValue = abs( $value );
                    } break;
                    case $this->CeilName:
                    {
                        $operatorValue = ceil( $value );
                    } break;
                    case $this->FloorName:
                    {
                        $operatorValue = floor( $value );
                    } break;
                    case $this->RoundName:
                    {
                        $operatorValue = round( $value );
                    } break;
                }
            } break;
            case $this->IntName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) > 0 )
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                else
                    $value = $operatorValue;
                $operatorValue = (int)$value;
            } break;
            case $this->FloatName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) > 0 )
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                else
                    $value = $operatorValue;
                $operatorValue = (float)$value;
            } break;
            case $this->RandName:
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) == 2 )
                {
                    $operatorValue = random_int( $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement ),
                                              $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace, $placement ) );
                }
                else
                {
                    $operatorValue = random_int(0, mt_getrandmax());
                }
            } break;
        }
    }

    /// \privatesection

    /*!
     \private

     Recursive function for calculating roman numeral from integer

     \param integer value
     \return next chars for for current value
    */
    function buildRoman( $value )
    {
        if ( $value >= 1000 )
            return 'M'.$this->buildRoman( $value - 1000 );
        if ( $value >= 500 )
        {
            if ( $value >= 900 )
                return 'CM'.$this->buildRoman( $value - 900 );
            else
                return 'D'.$this->buildRoman( $value - 500 );
        }
        if ( $value >= 100 )
        {
            if( $value >= 400 )
                return 'CD'.$this->buildRoman( $value - 400 );
            else
                return 'C'.$this->buildRoman( $value - 100 );
        }
        if ( $value >= 50 )
        {
            if( $value >= 90 )
                return 'XC'.$this->buildRoman( $value - 90 );
            else
                return 'L'.$this->buildRoman( $value - 50 );
        }
        if ( $value >= 10 )
        {
            if( $value >= 40 )
                return 'XL'.$this->buildRoman( $value - 40 );
            else
                return 'X'.$this->buildRoman( $value - 10 );
        }
        if ( $value >= 5 )
        {
            if( $value == 9 )
                return 'IX'.$this->buildRoman( $value - 9 );
            else
                return 'V'.$this->buildRoman( $value - 5 );
        }
        if ( $value >= 1 )
        {
            if( $value == 4 )
                return 'IV'.$this->buildRoman( $value - 4 );
            else
                return 'I'.$this->buildRoman( $value - 1 );
        }
        return '';
    }

    public $Operators = ['sum', 'sub', 'inc', 'dec', 'div', 'mod', 'mul', 'max', 'min', 'abs', 'ceil', 'floor', 'round', 'int', 'float', 'count', 'roman', 'rand'];
    public $SumName;
    public $SubName;
    public $IncName;
    public $DecName;

    public $DivName;
    public $ModName;
    public $MulName;

    public $MaxName;
    public $MinName;

    public $AbsName;
    public $CeilName;
    public $FloorName;
    public $RoundName;

    public $IntName;
    public $FloatName;

    public $CountName;

    public $RomanName;

    public $RandName;
}

?>
