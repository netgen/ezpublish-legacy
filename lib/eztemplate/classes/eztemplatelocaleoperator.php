<?php
/**
 * File containing the eZTemplateLocaleOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateLocaleOperator eztemplatelocaleoperator.php
  \ingroup eZTemplateOperators
  \brief Locale aware conversions and output using operator "l10n"

  This class takes care of converting variables and displaying them
  according to their locale settings.
  The class has one operator called l10n (short for localization) which
  takes one parameter which is localization type.
  Supported types are time, shorttime, date, shortdate, currency and number.

\code
// Example template code
{$curdate|l10n(date)}
{$cash|l10n(currency)}
\endcode
*/

class eZTemplateLocaleOperator
{
    /**
     * Initializes the object with the default locale.
     *
     * @todo Add support for specifying the locale object.
     */
    public function __construct()
    {
    }

    /*!
     Returns array with l10n.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     Returns a list with hints for the template compiler.
    */
    function operatorTemplateHints()
    {
        $hints = [$this->LocaleName      => ['input' => true, 'output' => true, 'parameters' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation' => true, 'element-transformation-func' => 'l10nTransformation'], $this->LocaleFetchName      => ['input' => true, 'output' => true, 'parameters' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation' => false], $this->DateTimeName    => ['input' => true, 'output' => true, 'parameters' => true, 'transform-parameters' => true, 'input-as-parameter' => 'always', 'element-transformation' => true, 'element-transformation-func' => 'dateTimeTransformation'], $this->CurrentDateName => ['input' => false, 'output' => true, 'parameters' => false, 'transform-parameters' => true, 'input-as-parameter' => false, 'element-transformation' => true, 'element-transformation-func' => 'currentDateTransformation'], $this->MakeTimeName    => ['input' => true, 'output' => true, 'parameters' => true, 'transform-parameters' => true, 'input-as-parameter' => false, 'element-transformation' => true, 'element-transformation-func' => 'makeDateTimeTransformation'], $this->MakeDateName    => ['input' => true, 'output' => true, 'parameters' => true, 'transform-parameters' => true, 'input-as-parameter' => false, 'element-transformation' => true, 'element-transformation-func' => 'makeDateTimeTransformation'], $this->GetTimeName     => ['input' => true, 'output' => true, 'parameters' => 1, 'transform-parameters' => true, 'input-as-parameter' => false, 'element-transformation' => true, 'element-transformation-func' => 'getTimeTransformation']];
        return $hints;
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
        return ['l10n' =>     ['type' =>      ['type' => 'string', 'required' => true, 'default' => false], 'locale' =>    ['type' => 'string', 'required' => false, 'default' => false], 'param' =>     ['type' => 'string', 'required' => false, 'default' => false]], 'datetime' => ['class' =>     ['type' => 'string', 'required' => true, 'default' => false], 'data' =>      ['type' => 'mixed', 'required' => false, 'default' => false]], 'gettime' =>  ['timestamp' => ['type' => 'integer', 'required' => false, 'default' => false]], 'maketime' => ['hour' =>      ['type' => 'integer', 'required' => false, 'default' => false], 'minute' =>    ['type' => 'integer', 'required' => false, 'default' => false], 'second' =>    ['type' => 'integer', 'required' => false, 'default' => false], 'month' =>     ['type' => 'integer', 'required' => false, 'default' => false], 'day' =>       ['type' => 'integer', 'required' => false, 'default' => false], 'year' =>      ['type' => 'integer', 'required' => false, 'default' => false], 'dst' =>       ['type' => 'integer', 'required' => false, 'default' => false]], 'makedate' => ['month' =>     ['type' => 'integer', 'required' => false, 'default' => false], 'day' =>       ['type' => 'integer', 'required' => false, 'default' => false], 'year' =>      ['type' => 'integer', 'required' => false, 'default' => false], 'dst' =>       ['type' => 'integer', 'required' => false, 'default' => false]]];
    }

    /*!
     Transforms
     */
    function l10nTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $newElements = [];

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( '// l10nTransformation begin' . "\n" );
        $values[] = $parameters[0];

        if ( (is_countable($parameters) ? count( $parameters ) : 0) > 2 )
        {
            $values[] = $parameters[2];
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( "\$locale = eZLocale::instance( %2% );\n", $values );
        }
        else
        {
            $values[] = false;
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( "\$locale = eZLocale::instance();\n" );
        }

        if ( !eZTemplateNodeTool::isConstantElement( $parameters[1] ) )
        {
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( '// l10nTransformation: not static' . "\n" );
            $values[] = $parameters[1];

            $code = "%tmp1% = \$locale->getFormattingFunction( %3% );\n";
            $code .= "if ( %tmp1% )\n";
            $code .= "{\n";
            $code .= "    if ( %3% === 'currency' )\n";
            if ( (is_countable($parameters) ? count( $parameters ) : 0) > 3 )
            {
                $values[] = $parameters[3];
                $code .= "        if( %4% === false )\n";
                $code .= "            %output% = \$locale->%tmp1%( %1%, \$locale->attribute( 'currency_symbol' ) );\n";
                $code .= "        else\n";
                $code .= "            %output% = \$locale->%tmp1%( %1%, %4% );\n";

            }
            else
            {
                $code .= "        %output% = \$locale->%tmp1%( %1%, \$locale->attribute( 'currency_symbol' ) );\n";
            }
            $code .= "    else\n";
            $code .= "        %output% = \$locale->%tmp1%( %1% );\n";
            $code .= "}\n";
            $code .= "else\n";
            $code .= "    %output% = %1%;\n";

            $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values, false, 1 );


            $newElements[] = eZTemplateNodeTool::createCodePieceElement( '// l10nTransformation end' . "\n" );
            return $newElements;
        }
        else
        {
            $values[] = false;
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( '// l10nTransformation: static' . "\n" );
            if ( ( $function = eZTemplateNodeTool::elementConstantValue( $parameters[1] ) ) !== false )
            {
                $locale = eZLocale::instance();
                $method = $locale->getFormattingFunction( $function );

                if ( $method )
                {
                    switch( $function )
                    {
                        case 'currency':
                            {
                                if ( (is_countable($parameters) ? count( $parameters ) : 0) > 3 )
                                {
                                    $values[] = $parameters[3];
                                    $newElements[] = eZTemplateNodeTool::createCodePieceElement( "if( %4% === false)\n%output% = \$locale->$method( %1%, \$locale->attribute( 'currency_symbol' ) );\nelse\n%output% = \$locale->$method( %1%, %4% );\n", $values );
                                }
                                else
                                {
                                    $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = \$locale->$method( %1%, \$locale->attribute( 'currency_symbol' ) );\n", $values );
                                }

                            } break;
                        default:
                            {
                                $newElements[] = eZTemplateNodeTool::createCodePieceElement( "\n%output% = \$locale->$method( %1% );\n", $values );
                            } break;
                    }
                    return $newElements;
                }
            }
        }
    }

    function dateTimeTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                     $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $newElements = [];
        $paramCount = is_countable($parameters) ? count( $parameters ) : 0;
        if ( $paramCount < 2 )
        {
            return false;
        }
        if ( !eZTemplateNodeTool::isConstantElement( $parameters[1] ) )
        {
            return false;
        }
        else
        {
            $class = eZTemplateNodeTool::elementConstantValue( $parameters[1] );
        }
        if ( ( $class == 'custom' ) && ( $paramCount != 3 ) )
        {
            return false;
        }

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( '$locale = eZLocale::instance();' . "\n" );

        if ( $class == 'custom' )
        {
            $values[] = $parameters[0];
            $values[] = $parameters[2];
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = \$locale->formatDateTimeType( %2%, %1% );\n", $values );
            return $newElements;

        }
        else
        {
            $dtINI = eZINI::instance( 'datetime.ini' );
            $formats = $dtINI->variable( 'ClassSettings', 'Formats' );
            if ( array_key_exists( $class, $formats ) )
            {
                $classFormat = addcslashes( (string) $formats[$class], "'" );
                $values[] = $parameters[0];
                $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = \$locale->formatDateTimeType( '$classFormat', %1% );\n", $values );
                return $newElements;
            }
        }
        return false;
    }

    function currentDateTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                        $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $newElements = [];
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = time();\n" );
        return $newElements;
    }

    function makeDateTimeTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                         $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = [];
        $arguments = [];
        $newElements = [];
        $paramCount = is_countable($parameters) ? count( $parameters ) : 0;

        if ( $operatorName == 'makedate' )
        {
            $arguments = [0, 0, 0];
        }
        for ( $i = 0; $i < $paramCount; ++$i )
        {
            if ( $parameters[$i] === null )
            {
                break;
            }
            $values[] = $parameters[$i];
            $arguments[] = '%' . ($i + 1) . '%';
        }

        $code = count( $arguments ) == 0 ? "%output% = time();\n" : "%output% = mktime( " . implode( ', ', $arguments ) . " );\n";
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function getTimeTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                    $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $newElements = [];
        $values = [];
        $paramCount = is_countable($parameters) ? count( $parameters ) : 0;

        if ( $paramCount == 1 )
        {
            $values[] = $parameters[0];
            $code = "%tmp1% = %1%;\n";
        }
        else if ( $paramCount == 0 )
        {
            $code = "%tmp1% = time();\n";
        }
        else
        {
            return false;
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement(
            $code .
            "%tmp2% = getdate( %tmp1% );\n".
            "%tmp3% = date( 'W', %tmp1% );\n".
            "if ( %tmp2%['wday'] == 0 )\n{\n\t++%tmp3%;\n}\n".
            "%output% = array( 'seconds' => %tmp2%['seconds'],
              'minutes' => %tmp2%['minutes'],
              'hours' => %tmp2%['hours'],
              'day' => %tmp2%['mday'],
              'month' => %tmp2%['mon'],
              'year' => %tmp2%['year'],
              'weeknumber' => %tmp3%,
              'weekday' => %tmp2%['wday'],
              'yearday' => %tmp2%['yday'],
              'epoch' => %tmp2%[0] );\n", $values, false, 3);
        return $newElements;
    }

    /*!
     Converts the variable according to the locale type.
     Allowed types are:
     - time
     - shorttime
     - date
     - shortdate
     - currency
     - clean_currency
     - number
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters,
                     $placement )
    {
        if ( $operatorName == $this->LocaleFetchName )
        {
            if ( $operatorValue !== null )
            {
                $localeString = $operatorValue;
            }
            else
            {
                if ( (is_countable($operatorParameters) ? count( $operatorParameters ) : 0) < 1 )
                {
                    $tpl->missingParameter( $operatorName, 'localestring' );
                    return;
                }
                $localeString = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement, true );
            }
            $locale = eZLocale::instance( $localeString );
            $operatorValue = $locale;
            return;
        }
        $locale = eZLocale::instance();
        if ( $operatorName == $this->GetTimeName )
        {
            $timestamp = $operatorValue;
            if ( $timestamp === null )
                $timestamp = $namedParameters['timestamp'];

            if ( !$timestamp )
                $timestamp = time();

            $info = getdate( $timestamp );
            $week = date( 'W', $timestamp );
            if ( $info['wday'] == 0 )
                ++$week;
            $operatorValue = ['seconds' => $info['seconds'], 'minutes' => $info['minutes'], 'hours' => $info['hours'], 'day' => $info['mday'], 'month' => $info['mon'], 'year' => $info['year'], 'weeknumber' => $week, 'weekday' => $info['wday'], 'yearday' => $info['yday'], 'epoch' => $info[0]];
        }
        else if ( $operatorName == $this->MakeTimeName )
        {
            $parameters = [];
            if ( $namedParameters['hour'] !== false )
                $parameters[] = $namedParameters['hour'];
            if ( $namedParameters['minute'] !== false )
                $parameters[] = $namedParameters['minute'];
            if ( $namedParameters['second'] !== false )
                $parameters[] = $namedParameters['second'];
            if ( $namedParameters['month'] !== false )
                $parameters[] = $namedParameters['month'];
            {
                if ( $namedParameters['day'] !== false )
                    $parameters[] = $namedParameters['day'];
                {
                    if ( $namedParameters['year'] !== false )
                        $parameters[] = $namedParameters['year'];
                    {
                        if ( $namedParameters['dst'] !== false )
                            $parameters[] = $namedParameters['dst'];
                    }
                }
            }

            $operatorValue = count( $parameters ) == 0 ? time() : call_user_func_array( 'mktime', $parameters );
        }
        else if ( $operatorName == $this->MakeDateName )
        {
            $parameters = [0, 0, 0];
            if ( $namedParameters['month'] !== false )
                $parameters[] = $namedParameters['month'];
            {
                if ( $namedParameters['day'] !== false )
                    $parameters[] = $namedParameters['day'];
                {
                    if ( $namedParameters['year'] !== false )
                        $parameters[] = $namedParameters['year'];
                    {
                        if ( $namedParameters['dst'] !== false )
                            $parameters[] = $namedParameters['dst'];
                    }
                }
            }
            $operatorValue = call_user_func_array( 'mktime', $parameters );
        }
        else if ( $operatorName == $this->CurrentDateName )
        {
            $operatorValue = time();
        }
        else if ( $operatorName == $this->DateTimeName )
        {
            $class = $namedParameters['class'];
            if ( $class === null )
                return;
            if ( $class == 'custom' )
            {
                $operatorValue = $locale->formatDateTimeType( $namedParameters['data'], $operatorValue );
            }
            else
            {
                $dtINI = eZINI::instance( 'datetime.ini' );
                $formats = $dtINI->variable( 'ClassSettings', 'Formats' );
                if ( array_key_exists( $class, $formats ) )
                {
                    $classFormat = $formats[$class];
                    $operatorValue = $locale->formatDateTimeType( $classFormat, $operatorValue );
                }
                else
                    $tpl->error( $operatorName, "DateTime class '$class' is not defined", $placement );
            }
        }
        else if ( $operatorName == $this->LocaleName )
        {
            $type = $namedParameters['type'];
            if ( $type === null )
                return;

            $localeString = $namedParameters['locale'];
            $param = $namedParameters['param'];

            // change locale if need
            if ( $localeString )
                $locale = eZLocale::instance( $localeString );

            $method = $locale->getFormattingFunction( $type );
            if ( $method )
            {
                switch ( $type )
                {
                    case 'currency':
                        {
                            if ( $param === false )
                                $param = $locale->attribute( 'currency_symbol' );

                            $operatorValue = $locale->$method( $operatorValue, $param );
                        } break;

                    default:
                        {
                            $operatorValue = $locale->$method( $operatorValue );
                        } break;
                }
            }
            else
            {
                $tpl->error( $operatorName, "Unknown locale type: '$type'", $placement );
            }
        }
    }

    /// \privatesection
    /// The operator array
    public $Operators = ['l10n', 'locale', 'datetime', 'currentdate', 'maketime', 'makedate', 'gettime'];
    /// A reference to the locale object
    public $Locale;

    public $LocaleName = 'l10n';
    public $DateTimeName = 'datetime';
    public $CurrentDateName = 'currentdate';
    public $LocaleFetchName = 'locale';
    public $MakeTimeName = 'maketime';
    public $MakeDateName = 'makedate';
    public $GetTimeName = 'gettime';
}

?>
