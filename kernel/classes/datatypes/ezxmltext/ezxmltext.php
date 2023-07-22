<?php
/**
 * File containing the eZXMLText class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZXMLText ezxmltext.php
  \ingroup eZDatatype
  \brief The class eZXMLText handles XML text data type instances

*/

class eZXMLText
{
    /**
     * Constructor
     *
     * @param string $XMLData
     * @param eZContentObjectAttribute $ContentObjectAttribute
     */
    public function __construct(
        /// Contains the XML data
        public $XMLData,
        public $ContentObjectAttribute
    )
    {
    }

    function attributes()
    {
        return ['input', 'output', 'pdf_output', 'xml_data', 'is_empty'];
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function attribute( $name )
    {
        switch ( $name )
        {
            case 'input' :
            {
                if ( $this->XMLInputHandler === null )
                {
                    $this->XMLInputHandler = static::inputHandler($this->XMLData, false, true, $this->ContentObjectAttribute);
                }
                return $this->XMLInputHandler;
            }break;

            case 'output' :
            {
                if ( $this->XMLOutputHandler === null )
                {
                    $this->XMLOutputHandler = static::outputHandler($this->XMLData, false, true, $this->ContentObjectAttribute);
                }
                return $this->XMLOutputHandler;
            }break;

            case 'pdf_output' :
            {
                if ( $this->PDFOutputHandler === null )
                {
                    $this->PDFOutputHandler = static::outputHandler($this->XMLData, 'ezpdf', true, $this->ContentObjectAttribute);
                }
                return $this->PDFOutputHandler;
            }break;

            case 'xml_data' :
            {
                return $this->XMLData;
            }break;

            case 'is_empty' :
            {
                $isEmpty = true;
                $dom = new DOMDocument( '1.0', 'utf-8' );
                if ( !$this->XMLData )
                {
                    return $isEmpty;
                }
                $success = $dom->loadXML( $this->XMLData );
                if ( $success )
                {
                    $sectionNode = $dom->documentElement;

                    if ( $sectionNode->childNodes->length > 0 )
                    {
                        $isEmpty = false;
                    }
                }
                return $isEmpty;
            }break;

            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                $retValue = null;
                return $retValue;
            }break;
        }
    }

    /// \static
    static function inputHandler( &$xmlData, $type = false, $useAlias = true, $contentObjectAttribute = false )
    {
        $optionArray = ['iniFile'       => 'ezxml.ini', 'iniSection'    => 'InputSettings', 'iniVariable'   => 'HandlerClass', 'callMethod'    => 'isValid', 'handlerParams' => [$xmlData, $type, $contentObjectAttribute], 'aliasVariable' => ( $useAlias ? 'AliasClasses' : null ), 'aliasOptionalIndex' => ( $type ?: null )];

        $options = new ezpExtensionOptions( $optionArray );

        $inputHandler = eZExtension::getHandlerClass( $options );

        if ( $inputHandler === null || $inputHandler === false )
        {
            $inputHandler = new eZSimplifiedXMLInput( $xmlData, false, $contentObjectAttribute );
        }
        return $inputHandler;
    }

    /// \static
    static function outputHandler( &$xmlData, $type = false, $useAlias = true, $contentObjectAttribute = false )
    {
        $optionArray = ['iniFile'       => 'ezxml.ini', 'iniSection'    => 'OutputSettings', 'iniVariable'   => 'HandlerClass', 'callMethod'    => 'isValid', 'handlerParams' => [$xmlData, $type, $contentObjectAttribute], 'aliasVariable' => ( $useAlias ? 'AliasClasses' : null ), 'aliasOptionalIndex' => ( $type ?: null )];

        $options = new ezpExtensionOptions( $optionArray );

        $outputHandler = eZExtension::getHandlerClass( $options );

        if ( $outputHandler === null || $outputHandler === false )
        {
            $outputHandler = new eZXHTMLXMLOutput( $xmlData, false, $contentObjectAttribute );
        }
        return $outputHandler;
    }

    public $XMLInputHandler = null;
    public $XMLOutputHandler = null;
    protected $PDFOutputHandler = null;
    public $XMLAttributeID;
}

?>
