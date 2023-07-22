<?php
/**
 * File containing the eZRegExpValidator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZRegExpValidator ezregexpvalidator.php
  \brief Input validation using regexps

*/

class eZRegExpValidator extends eZInputValidator
{
    /**
     * Constructor
     *
     * @param string $RegExpRule
     */
    public function __construct(
        /// \privatesection
        public $RegExpRule = null
    )
    {
    }

    function setRegExpRule( $rule )
    {
        $this->RegExpRule = $rule;
    }

    function validate( $text )
    {
        if ( !is_array( $this->RegExpRule ) )
            return eZInputValidator::STATE_INVALID;
        $accepted =& $this->RegExpRule["accepted"];
        if ( preg_match( $accepted, (string) $text ) )
            return eZInputValidator::STATE_ACCEPTED;
        $intermediate =& $this->RegExpRule["intermediate"];
        if ( preg_match( $intermediate, (string) $text ) )
            return eZInputValidator::STATE_INTERMEDIATE;
        return eZInputValidator::STATE_INVALID;
    }

    function fixup( $text )
    {
        if ( !is_array( $this->RegExpRule ) )
            return $text;
        $intermediate =& $this->RegExpRule["intermediate"];
        $fixup =& $this->RegExpRule["fixup"];
        if ( is_array( $fixup ) )
        {
            $intermediate = $fixup["match"];
            $fixup = $fixup["replace"];
        }
        $text = preg_replace( $intermediate, (string) $fixup, (string) $text );
        return $text;
    }
}

?>
