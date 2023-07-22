<?php
/**
 * File containing the ezpDsn class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpDsn
{
    public function __construct( $dsn )
    {
        $this->dsn = $dsn;
    }

    public function __get( $name )
    {
        return match ($name) {
            'parts' => $this->parts,
            default => $this->parts[$name],
        };
    }

    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'dsn':
                $this->dsn = $value;
                $this->parts = self::parseDSN( $value );
                break;
            default:
                $this->$name = $value;
        }
    }

    static public function parseDSN( $dsn )
    {
        $parts = ezcDbFactory::parseDSN( $dsn );
        if ( isset( $parts['hostspec'] ) )
        {
            $parts['host'] = $parts['hostspec'];
            $parts['server'] = $parts['hostspec'];
        }

        if ( isset( $parts['username'] ) )
            $parts['user'] = $parts['username'];

        return $parts;
    }
}

?>
