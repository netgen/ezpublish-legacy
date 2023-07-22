<?php
/**
 * File containing the eZAutoLinkOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZAutoLinkOperator
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name = 'autolink' )
    {
        $this->Operators = [$name];
    }

    /*!
     Returns the operators in this class.
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
        return ['max_chars' => ['type' => 'integer', 'required' => false, 'default' => null]];
    }

    function formatUri( $url, $max )
    {
        $text = $url;
        if (strlen((string) $text) > $max)
        {
            $text = substr((string) $text, 0, ($max / 2) - 3). '...'. substr((string) $text, strlen((string) $text) - ($max / 2));
        }
        return "<a href=\"$url\" title=\"$url\">$text</a>";
    }

    /*!
     \static
    */
    function addURILinks( $text, $max, $methods = 'http|https|ftp' )
    {
        return preg_replace_callback(
            "`(?<!href=\"|href='|src=\"|src='|value=\"|value=')($methods):\/\/[\w]+(.[\w]+)([\w\-\.,@?^=%&:\/~\+#;*\(\)\!]*[\w\-\@?^=%&\/~\+#;*\(\)\!])?`",
            fn($matches) => (new eZAutoLinkOperator())->formatUri($matches[0], $max),
            (string) $text
        );
    }


    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $ini = $tpl->ini();
        $max = $ini->variable( 'AutoLinkOperator', 'MaxCharacters' );
        if ( $namedParameters['max_chars'] !== null )
        {
            $max = $namedParameters['max_chars'];
        }

        $methods = $ini->variable( 'AutoLinkOperator', 'Methods' );
        $methodText = implode( '|', $methods );

        // Replace mail
        $operatorValue = preg_replace( "#(([a-zA-Z0-9_-]+\\.)*[a-zA-Z0-9_-]+@([a-zA-Z0-9_-]+\\.)*[a-zA-Z0-9_-]+)#", "<a href='mailto:\\1'>\\1</a>", (string) $operatorValue );

        // Replace http/ftp etc. links
        $operatorValue = (new eZAutoLinkOperator())->addURILinks($operatorValue, $max, $methodText);
    }

    /// \privatesection
    public $Operators;
}

?>
