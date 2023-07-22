<?php
/**
 * File containing the  class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */
/**
 * Struct used to represent a datatype for testing
 */
class ezpDatatypeTestDataSet extends ezcBaseStruct
{
    /**
     * Content the datatype should return
     * @var mixed
     */
    public $content;

    /**
     * Constructs a new ezpDatatypeTestDataSet with initial values.
     *
     * @param string $fromString
     * @param int $dataInt
     * @param string $dataText
     * @param float $dataFloat
     * @param
     */
    public function __construct(public $fromString = '', public $dataInt = null, public $dataText = '', public $dataFloat = '0')
    {
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezpDatatypeTestDataSet
     */
    static public function __set_state( array $array )
    {
        return new self( $array['fromString'], $array['data_int'], $array['data_string'], $array['data_float'] );
    }
}
?>
