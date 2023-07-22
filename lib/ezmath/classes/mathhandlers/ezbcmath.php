<?php
/**
 * File containing the eZBCMath class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZBCMath ezbcmath.php
  \brief Handles calculation using bcmath library.
*/

class eZBCMath extends eZPHPMath
{
    final public const DEFAULT_SCALE = 10;

    public function __construct( $params = [] )
    {
        if( isset( $params['scale'] ) && is_numeric( $params['scale'] ) )
            $this->setScale( $params['scale'] );
        else
            $this->setScale( self::DEFAULT_SCALE );
    }

    function scale()
    {
        return $this->Scale;
    }

    function setScale( $scale )
    {
        $this->Scale = $scale;
    }

    function add( $a, $b )
    {
        return ( bcadd( (string) $a, (string) $b, $this->Scale ) );
    }

    function sub( $a, $b )
    {
        return ( bcsub( (string) $a, (string) $b, $this->Scale ) );
    }

    function mul( $a, $b )
    {
        return ( bcmul( (string) $a, (string) $b, $this->Scale ) );
    }

    function div( $a, $b )
    {
        return ( bcdiv( (string) $a, (string) $b, $this->Scale ) );
    }

    function pow( $base, $exp )
    {
        return ( bcpow( (string) $base, (string) $exp, $this->Scale ) );
    }

    function ceil( $value, $precision, $target )
    {
        $result = eZPHPMath::ceil( $value, $precision, $target );
        $result = rtrim( (string) $result, '0' );
        $result = rtrim( $result, '.' );
        return $result;
    }

    function floor( $value, $precision, $target )
    {
        $result = eZPHPMath::floor( $value, $precision, $target );
        $result = rtrim( (string) $result, '0' );
        $result = rtrim( $result, '.' );
        return $result;
    }

    function round( $value, $precision, $target )
    {
        $result = $value;
        $fractPart = $this->fractval( $value, $precision + 1 );
        if ( strlen( (string) $fractPart ) > $precision )
        {
            $lastDigit = (int)substr( (string) $fractPart, -1, 1 );
            $fractPart = substr( (string) $fractPart, 0, $precision );
            if ( $lastDigit >= 5 )
                $fractPart = $this->add( $fractPart, 1 );

            $fractPart = $this->div( $fractPart, $this->pow( 10, $precision ) );

            $result = $this->add( $this->intval( $value ), $fractPart );
            $result = $this->adjustFractPart( $result, $precision, $target );

            $result = rtrim( (string) $result, '0' );
            $result = rtrim( $result, '.' );
        }

        return $result;
    }


    /// \privatesection
    public $Scale;
}

?>
