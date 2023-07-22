<?php
/**
 * File containing the eZSOAPEnvelope class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*! \defgroup eZSOAP SOAP communication library */

/*!
  \class eZSOAPEnvelope ezsoapenvelope.php
  \ingroup eZSOAP
  \brief SOAP envelope handling and definition

*/

class eZSOAPEnvelope
{
    final public const ENV = "http://schemas.xmlsoap.org/soap/envelope/";
    final public const ENC = "http://schemas.xmlsoap.org/soap/encoding/";
    final public const SCHEMA_INSTANCE = "http://www.w3.org/2001/XMLSchema-instance";
    final public const SCHEMA_DATA = "http://www.w3.org/2001/XMLSchema";

    final public const ENV_PREFIX = "SOAP-ENV";
    final public const ENC_PREFIX = "SOAP-ENC";
    final public const XSI_PREFIX = "xsi";
    final public const XSD_PREFIX = "xsd";

    final public const INT = 1;
    final public const STRING = 2;

    /**
     * Constructs a new SOAP envelope object.
     */
    public function __construct( )
    {
        $this->Header = new eZSOAPHeader();
        $this->Body = new eZSOAPBody();
    }

    /// Contains the header object
    public $Header;

    /// Contains the body object
    public $Body;
}

?>
