<?php
/**
 * File containing the eZCurrencyData class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZCurrencyData extends eZPersistentObject
{
    final public const DEFAULT_AUTO_RATE_VALUE = '0.0000';
    final public const DEFAULT_CUSTOM_RATE_VALUE = '0.0000';
    final public const DEFAULT_RATE_FACTOR_VALUE = '1.0000';

    final public const ERROR_OK = 0;
    final public const ERROR_UNKNOWN = 1;
    final public const ERROR_INVALID_CURRENCY_CODE = 2;
    final public const ERROR_CURRENCY_EXISTS = 3;

    final public const STATUS_ACTIVE = '1';
    final public const STATUS_INACTIVE = '2';

    public function __construct( $row )
    {
        parent::__construct( $row );
    }

    static function definition()
    {
        return ['fields' => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'code' => ['name' => 'Code', 'datatype' => 'string', 'default' => '', 'required' => true], 'symbol' => ['name' => 'Symbol', 'datatype' => 'string', 'default' => '', 'required' => false], 'locale' => ['name' => 'Locale', 'datatype' => 'string', 'default' => '', 'required' => false], 'status' => ['name' => 'Status', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'auto_rate_value' => ['name' => 'AutoRateValue', 'datatype' => 'string', 'default' => self::DEFAULT_AUTO_RATE_VALUE, 'required' => false], 'custom_rate_value' => ['name' => 'CustomRateValue', 'datatype' => 'string', 'default' => self::DEFAULT_CUSTOM_RATE_VALUE, 'required' => false], 'rate_factor' => ['name' => 'RateFactor', 'datatype' => 'string', 'default' => self::DEFAULT_RATE_FACTOR_VALUE, 'required' => false]], 'keys' => ['id'], 'increment_key' => 'id', 'function_attributes' => ['rate_value' => 'rateValue'], 'class_name' => "eZCurrencyData", 'sort' => ['code' => 'asc'], 'name' => "ezcurrencydata"];
    }

    /*!
     \static
     \params codeList can be a single code like 'USD' or an array like array( 'USD', 'NOK' )
     or 'false' (means all currencies).
    */
    static function fetchList( $conditions = null, $asObjects = true, $offset = false, $limit = false, $asHash = true )
    {
        $currencyList = [];
        $sort = null;
        $limitation = null;
        if ( $offset !== false or $limit !== false )
            $limitation = ['offset' => $offset, 'length' => $limit];

        $rows = eZPersistentObject::fetchObjectList( eZCurrencyData::definition(),
                                                     null,
                                                     $conditions,
                                                     $sort,
                                                     $limitation,
                                                     $asObjects );

        if ( count( (array) $rows ) > 0 )
        {
            if ( $asHash )
            {
                $keys = array_keys( $rows );
                foreach ( $keys as $key )
                {
                    if ( $asObjects )
                        $currencyList[$rows[$key]->attribute( 'code' )] = $rows[$key];
                    else
                        $currencyList[$rows[$key]['code']] = $rows[$key];
                }
            }
            else
            {
                $currencyList = $rows;
            }
        }

        return $currencyList;
    }

    /*!
     \static
    */
    static function fetchListCount( $conditions = null )
    {
        $rows = eZPersistentObject::fetchObjectList( eZCurrencyData::definition(),
                                                     [],
                                                     $conditions,
                                                     false,
                                                     null,
                                                     false,
                                                     false,
                                                     [['operation' => 'count( * )', 'name' => 'count']] );
        return $rows[0]['count'];
    }

    /*!
     \static
    */
    static function fetch( $currencyCode, $asObject = true )
    {
        if ( $currencyCode )
        {
            $currency = eZCurrencyData::fetchList( ['code' => $currencyCode], $asObject );
            if ( is_array( $currency ) && count( $currency ) > 0 )
                return $currency[$currencyCode];
        }

        return null;
    }

    /*!
     functional attribute
    */
    function rateValue()
    {
        if ( $this->RateValue === false )
        {
            /*
            $rateValue = '0.00000';
            if ( $this->attribute( 'custom_rate_value' ) > 0 )
            {
                $rateValue = $this->attribute( 'custom_rate_value' );
            }
            else
            {
                $rateValue = $this->attribute( 'auto_rate_value' );
                $rateValue = $rateValue * $this->attribute( 'rate_factor' );
                $rateValue = sprintf( "%7.5f", $rateValue );
            }
            */

            $rateValue = '0.00000';
            if ( $this->attribute( 'custom_rate_value' ) > 0 )
                $rateValue = $this->attribute( 'custom_rate_value' );
            else
                $rateValue = $this->attribute( 'auto_rate_value' );

            if ( $rateValue > 0 )
                $rateValue = $rateValue * $this->attribute( 'rate_factor' );

            $rateValue = sprintf( "%7.5f", $rateValue );

            $this->RateValue = $rateValue;
        }

        return $this->RateValue;
    }

    function invalidateRateValue()
    {
        $this->RateValue = false;
    }

    /*!
     \static
    */
    static function create( $code, $symbol, $locale, $autoRateValue, $customRateValue, $rateFactor, $status = self::STATUS_ACTIVE )
    {
        $code = strtoupper( (string) $code );
        $errCode = eZCurrencyData::canCreate( $code );
        if ( $errCode === self::ERROR_OK )
        {
            $currency = new eZCurrencyData( ['code' => $code, 'symbol' => $symbol, 'locale' => $locale, 'status' => $status, 'auto_rate_value' => $autoRateValue, 'custom_rate_value' => $customRateValue, 'rate_factor' => $rateFactor] );
            $currency->setHasDirtyData( true );
            return $currency;
        }

        return $errCode;
    }

    /*!
     \static
   */
    static function canCreate( $code )
    {
        $errCode = eZCurrencyData::validateCurrencyCode( $code );
        if ( $errCode === self::ERROR_OK && eZCurrencyData::currencyExists( $code ) )
            $errCode = self::ERROR_CURRENCY_EXISTS;

        return $errCode;
    }

    /*!
     \static
    */
    static function validateCurrencyCode( $code )
    {
        if ( !preg_match( "/^[A-Z]{3}$/", (string) $code ) )
            return self::ERROR_INVALID_CURRENCY_CODE;

        return self::ERROR_OK;
    }

    /*!
     \static
    */
    static function currencyExists( $code )
    {
        return ( eZCurrencyData::fetch( $code ) !== null );
    }

    /*!
     \static
    */
    static function removeCurrencyList( $currencyCodeList )
    {
        if ( is_array( $currencyCodeList ) && count( $currencyCodeList ) > 0 )
        {
            $db = eZDB::instance();
            $db->begin();
                eZPersistentObject::removeObject( eZCurrencyData::definition(),
                                                  ['code' => [$currencyCodeList]] );
            $db->commit();
        }
    }

    function setStatus( $status )
    {
        $statusNumeric = eZCurrencyData::statusStringToNumeric( $status );
        if ( $statusNumeric !== false )
        {
            $this->setAttribute( 'status', $statusNumeric );
        }
        else
        {
            eZDebug::writeError( "Unknow currency's status '$status'", __METHOD__ );
        }
    }

    static function statusStringToNumeric( $statusString )
    {
        $status = false;
        if ( is_numeric( $statusString ) )
        {
            $status = $statusString;
        }
        if ( is_string( $statusString ) )
        {
            $statusString = strtoupper( $statusString );
            if ( defined( "self::STATUS_{$statusString}" ) )
                $status = constant( "self::STATUS_{$statusString}" );
        }

        return $status;
    }

    /*!
     \static
    */
    static function errorMessage( $errorCode )
    {
        return match ($errorCode) {
            self::ERROR_INVALID_CURRENCY_CODE => ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Invalid characters in currency code.' ),
            self::ERROR_CURRENCY_EXISTS => ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Currency already exists.' ),
            default => ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Unknown error.' ),
        };
    }

    function store( $fieldFilters = null )
    {
        // data changed => reset RateValue
        $this->invalidateRateValue();
        parent::store( $fieldFilters );
    }

    function isActive()
    {
        return ( $this->attribute( 'status' ) == self::STATUS_ACTIVE );
    }

    public $RateValue = false;
}

?>
