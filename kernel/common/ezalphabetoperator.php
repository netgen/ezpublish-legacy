<?php
/**
 * File containing the eZAlphabetOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

//!! eZKernel
//! The class eZAlphabetOperator does
/*!

*/


class eZAlphabetOperator
{
    /**
     * Constructor
     *
     * @param string $alphabet
     */
    public function __construct( $alphabet = 'alphabet' )
    {
        $this->Operators = [$alphabet];
        $this->Alphabet = $alphabet;
    }

    /*!
     Returns the operators in this class.
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

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$value, $namedParameters, $placement )
    {
        switch ( $operatorName )
        {
            case $this->Alphabet:
            {
                $alphabet = eZAlphabetOperator::fetchAlphabet();
                $value = $alphabet;
            } break;
        }
    }

    /*!
      Static
      Returns alphabet.
    */
    static function fetchAlphabet()
    {
        $contentINI = eZINI::instance( 'content.ini' );

        $alphabetRangeList = $contentINI->hasVariable( 'AlphabeticalFilterSettings', 'AlphabetList' )
                             ? $contentINI->variable( 'AlphabeticalFilterSettings', 'AlphabetList' )
                             : [];

        $alphabetFromArray = $contentINI->hasVariable( 'AlphabeticalFilterSettings', 'ContentFilterList' )
                             ? $contentINI->variable( 'AlphabeticalFilterSettings', 'ContentFilterList' )
                             : ['default'];

        // If alphabet list is empty
        if ( (is_countable($alphabetFromArray) ? count( $alphabetFromArray ) : 0) == 0 )
            return false;

        $alphabetRangeList = array_merge( $alphabetRangeList, ['default' => '97-122'] );
        $alphabet = [];
        foreach ( $alphabetFromArray as $alphabetFrom )
        {
            // If $alphabetFrom exists in range array $alphabetRangeList
            if ( isset( $alphabetRangeList[$alphabetFrom] ) )
            {
                $lettersArray = explode( ',', (string) $alphabetRangeList[$alphabetFrom] );
                foreach ( $lettersArray as $letter )
                {
                    $rangeArray =  explode( '-', $letter );
                    if ( isset( $rangeArray[1] ) )
                    {
                        $alphabet = [...$alphabet, ...range( trim( $rangeArray[0] ), trim( $rangeArray[1] ) )];
                    }
                    else
                        $alphabet = [...$alphabet, trim( $letter )];
                }
            }
        }
        // Get alphabet by default (eng-GB)
        if ( count( $alphabet ) == 0 )
        {
            $rangeArray = explode( '-', (string) $alphabetRangeList['default'] );
            $alphabet = range( $rangeArray[0], $rangeArray[1] );
        }
        $resAlphabet = [];
        $i18nINI = eZINI::instance( 'i18n.ini' );
        $charset = $i18nINI->variable( 'CharacterSettings', 'Charset' );

        $codec = eZTextCodec::instance( 'utf-8', $charset );

        $utf8_codec = eZUTF8Codec::instance();
        // Convert all letters of alphabet from unicode to utf-8 and from utf-8 to current locale
        foreach ( $alphabet as $item )
        {
            $utf8Letter = $utf8_codec->toUtf8( $item );
            $resAlphabet[] = $codec ? $codec->convertString( $utf8Letter ) : $utf8Letter;
        }

        return $resAlphabet;
    }

    /// \privatesection
    public $Operators;
    public $Alphabet;
}

?>
