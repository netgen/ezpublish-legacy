<?php
/**
 * File containing the eZSOAPFault class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZSOAPFault ezsoapfault.php
  \ingroup eZSOAP
  \brief eZSOAPFault handles SOAP fault messages

*/

class eZSOAPFault
{
    /**
     * Constructs a new eZSOAPFault object
     *
     * @param string $FaultCode
     * @param string $FaultString
     */
    public function __construct(
        /// Contains the fault code
        public $FaultCode = "",
        /// Contains the fault string
        public $FaultString = ""
    )
    {
    }

    /*!
     Returns the fauls code.
    */
    function faultCode()
    {
        return $this->FaultCode;
    }

    /*!
     Returns the fauls string.
    */
    function faultString()
    {
        return $this->FaultString;
    }
}

?>
