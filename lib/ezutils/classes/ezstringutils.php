<?php
/**
 * File containing the eZStringUtils class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZStringUtils ezstringutils.php
  \brief The class eZStringUtils does

*/

class eZStringUtils
{
    static function  explodeStr( $str, $delimiter = '|' )
    {
        $offset = 0;
        $array = [];

        while( ( $pos = strpos( (string) $str, (string) $delimiter, $offset )  ) !== false )
        {
            $strPart = substr( (string) $str, 0, $pos );
            if ( preg_match( '/(\\\\+)$/', $strPart, $matches ) )
            {
                if ( strlen( $matches[0] ) % 2 !== 0 )
                {
                    $offset = $pos+1;
                    continue;
                }
            }
            $array[] = str_replace( '\\\\', '\\', str_replace("\\$delimiter", $delimiter, $strPart ) );
            $str = substr( (string) $str, $pos + 1 );
            $offset = 0;

        }
        $array[] = str_replace( '\\\\', '\\', str_replace("\\$delimiter", $delimiter, (string) $str ) );
        return $array;
    }

    static function implodeStr( $values, $delimiter = '|' )
    {
        $str = '';
        foreach ( $values as $key => $value )
        {
            $values[$key] = str_replace( $delimiter, "\\$delimiter", str_replace( '\\', '\\\\', (string) $value ) );
        }
        return implode( $delimiter, $values );
    }


}

?>
