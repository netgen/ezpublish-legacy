<?php
/**
 * File containing the eZTemplateTextOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateTextOperator eztemplatetextoperator.php
  \brief The class eZTemplateTextOperator does

*/

class eZTemplateTextOperator
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
        return [$this->ConcatName => ['input' => true, 'output' => true, 'parameters' => true, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'concatTransformation'], $this->IndentName => ['input' => true, 'output' => true, 'parameters' => 3, 'element-transformation' => true, 'transform-parameters' => true, 'input-as-parameter' => true, 'element-transformation-func' => 'indentTransformation']] ;
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
        return [$this->IndentName => ['indent_count' => ['type' => 'integer', 'required' => true, 'default' => false], 'indent_type' => ['type' => 'identifier', 'required' => false, 'default' => 'space'], 'indent_filler' => ['type' => 'string', 'required' => false, 'default' => false]]];
    }

    function indentTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                   $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $count = $type = $filler = false;
        $paramCount = is_countable($parameters) ? count( $parameters ) : 0;

        if ( $paramCount == 4 )
        {
            if ( eZTemplateNodeTool::isConstantElement( $parameters[3] ) )
            {
                $filler = eZTemplateNodeTool::elementConstantValue( $parameters[3] );
            }
        }
        if ( $paramCount >= 3 )
        {
            if ( eZTemplateNodeTool::isConstantElement( $parameters[2] ) )
            {
                $type = eZTemplateNodeTool::elementConstantValue( $parameters[2] );
                if ( $type == 'space' )
                {
                    $filler = ' ';
                }
                else if ( $type == 'tab' )
                {
                    $filler = "\t";
                }
                else if ( $type != 'custom' )
                {
                    $filler = ' ';
                }
            }
        }
        if ( $paramCount >= 2 )
        {
            if ( eZTemplateNodeTool::isConstantElement( $parameters[1] ) )
            {
                $count = eZTemplateNodeTool::elementConstantValue( $parameters[1] );
            }
            if ( $paramCount < 3 )
            {
                $type = 'space';
                $filler = ' ';
            }
        }
        $newElements = [];

        if ( $count and $type and $filler )
        {
            $tmpCount = 0;
            $values[] = $parameters[0];
            if ( $count < 0 )
            {
                //$code = ( $tpl->error( "indent", "Count parameter can not be negative" );
                $code = ( "\$tpl->error( \"indent\", \"Count parameter can not be negative, string won't be indented\" );\n" .
                          "%output% = %1%;\n" );
            }
            else
            {
                $indentation = str_repeat( (string) $filler, $count );
                $code = ( "%output% = '$indentation' . str_replace( '\n', '\n$indentation', %1% );\n" );
            }
        }
        else if ( $filler and $type )
        {
            $tmpCount = 1;
            $values[] = $parameters[0];
            $values[] = $parameters[1];
            $code = ( "if ( %2% < 0 )\n{" .
                      "\$tpl->error( \"indent\", \"Count parameter can not be negative, string won't be indented\" );\n" .
                      "%output% = %1%;\n" .
                      "}else{\n" .
                      "%tmp1% = str_repeat( '$filler', %2% );\n" .
                      "%output% = %tmp1% . str_replace( '\n', '\n' . %tmp1%, %1% );\n" .
                      "}\n");
        }
        else
        {
            $tmpCount = 2;
            $code = ( "if ( %2% < 0 ){\n" .
                     "\$tpl->error( \"indent\", \"Count parameter can not be negative, string won't be indented\" );\n" .
                     "%output% = %1%;\n" .
                     "}else{" .
                     "if ( %3% == 'tab' )\n{\n\t%tmp1% = \"\\t\";\n}\nelse " .
                     "if ( %3% == 'space' )\n{\n\t%tmp1% = ' ';\n}\nelse\n" );
            if ( (is_countable($parameters) ? count ( $parameters ) : 0) == 4 )
            {
                $code .= "{\n\t%tmp1% = %4%;\n}\n";
            }
            else
            {
                $code.= "{\n\t%tmp1% = ' ';\n}\n";
            }
            $code .= ( "%tmp2% = str_repeat( %tmp1%, %2% );\n" .
                       "%output% = %tmp2% . str_replace( '\n', '\n' . %tmp2%, %1% );\n" );
            $code .= "}\n";
            foreach ( $parameters as $parameter )
            {
                $values[] = $parameter;
            }
        }

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values, 'false', $tmpCount );
        return $newElements;
    }

    function concatTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                   $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $function = $operatorName;

        if ( ( (is_countable($parameters) ? count( $parameters ) : 0) < 1 ) )
        {
            return false;
        }
        if ( ( (is_countable($parameters) ? count( $parameters ) : 0) == 1 ) and
             eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            return [eZTemplateNodeTool::createConstantElement( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) )];
        }
        $newElements = [];

        $counter = 1;
        $code = "%output% = ( ";
        foreach ( $parameters as $parameter )
        {
            $values[] = $parameter;
            if ( $counter > 1 )
            {
                $code .= ' . ';
            }
            $code .= "%$counter%";
            $counter++;
        }
        $code .= " );\n";

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    /*!
     Handles concat and indent operators.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters,
                     $placement )
    {
        switch ( $operatorName )
        {
            case $this->ConcatName:
            {
                $operands = [];
                if ( $operatorValue !== null )
                    $operands[] = $operatorValue;
                for ( $i = 0; $i < (is_countable($operatorParameters) ? count( $operatorParameters ) : 0); ++$i )
                {
                    $operand = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( !is_object( $operand ) )
                        $operands[] = $operand;
                }
                $operatorValue = implode( '', $operands );
            } break;
            case $this->IndentName:
            {
                if( $namedParameters['indent_count'] < 0 )
                {
                    eZDebug::writeError( 'The value of the "count" argument is negative, indent() will not be called' );
                    break;
                }

                $indentCount = $namedParameters['indent_count'];
                $indentType = $namedParameters['indent_type'];
                $filler = false;
                $filler = match ($indentType) {
                    'tab' => "\t",
                    'custom' => $namedParameters['indent_filler'],
                    default => ' ',
                };
                $fillText = str_repeat( (string) $filler, $indentCount );
                $operatorValue = $fillText . str_replace( "\n", "\n" . $fillText, (string) $operatorValue );
            } break;
        }
    }

    /// \privatesection
    public $ConcatName;
    public $Operators = ['concat', 'indent'];
    public $IndentName;
}

?>
