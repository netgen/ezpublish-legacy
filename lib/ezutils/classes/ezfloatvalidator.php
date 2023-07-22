<?php
/**
 * File containing the eZFloatValidator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZFloatValidator ezintegervalidator.php
  \brief The class eZFloatValidator does

*/

class eZFloatValidator extends eZRegExpValidator
{
    /**
     * Constructor
     *
     * @param float|bool $MinValue
     * @param float|bool $MaxValue
     */
    public function __construct( /// \privatesection
    public $MinValue = false, public $MaxValue = false )
    {
        $rule = ["accepted" => "/^-?[0-9]+([.][0-9]+)?$/", "intermediate" => "/(-?[0-9]+([.][0-9]+)?)/", "fixup" => ""];
        parent::__construct( $rule );
        if ( $MaxValue !== false and $MinValue !== false )
            $this->MaxValue = max( $MinValue, $MaxValue );
    }

    function setRange( $min, $max )
    {
        $this->MinValue = $min;
        $this->MaxValue = $max;
        if ( $max !== false and $min !== false )
            $this->MaxValue = max( $min, $max );
    }

    function validate( $text )
    {
        $state = eZRegExpValidator::validate( $text );
        if ( $state == eZInputValidator::STATE_ACCEPTED )
        {
            if ( ( $this->MinValue !== false and $text < $this->MinValue ) or
                 ( $this->MaxValue !== false and $text > $this->MaxValue ) )
                $state = eZInputValidator::STATE_INTERMEDIATE;
        }
        return $state;
    }

    function fixup( $text )
    {
        if ( preg_match( $this->RegExpRule["intermediate"], (string) $text, $regs ) )
            $text = $regs[1];
        if ( $this->MinValue !== false and $text < $this->MinValue )
            $text = $this->MinValue;
        else if ( $this->MaxValue !== false and $text > $this->MaxValue )
            $text = $this->MaxValue;
        return $text;
    }
}

?>
