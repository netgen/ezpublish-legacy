<?php
/**
 * File containing the eZContentObjectTranslation class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZContentObjectTranslation ezcontentobjecttranslation.php
  \brief eZContentObjectTranslation handles translation a translation of content objects
  \ingroup eZKernel

  \sa eZContentObject eZContentObjectVersion eZContentObjectTranslation
*/

class eZContentObjectTranslation
{
    /**
     * Constructor
     *
     * @param int $ContentObjectID
     * @param int $Version
     * @param string $LanguageCode
     */
    public function __construct( /// The content object identifier
    public $ContentObjectID, /// Contains the content object
    public $Version, /// Contains the language code for the current translation
    public $LanguageCode )
    {
        $this->Locale = null;
    }

    function languageCode()
    {
        return $this->LanguageCode;
    }

    function attributes()
    {
        return ['contentobject_id', 'version', 'language_code', 'locale'];
    }

    function hasAttribute( $attribute )
    {
        return in_array( $attribute, $this->attributes() );
    }

    function attribute( $attribute )
    {
        if ( $attribute == 'contentobject_id' )
            return $this->ContentObjectID;
        else if ( $attribute == 'version' )
            return $this->Version;
        else if ( $attribute == 'language_code' )
            return $this->LanguageCode;
        else if ( $attribute == 'locale' )
            return $this->locale();
        else
        {
            eZDebug::writeError( "Attribute '$attribute' does not exist", __METHOD__ );
            return null;
        }
    }

    function locale()
    {
        if ( $this->Locale !== null )
            return $this->Locale;
        $this->Locale = eZLocale::instance( $this->LanguageCode );
        return $this->Locale;
    }

    /*!
     Returns the attributes for the current content object translation.
    */
    function objectAttributes( $asObject = true )
    {
        return eZContentObjectVersion::fetchAttributes( $this->Version, $this->ContentObjectID, $this->LanguageCode, $asObject );
    }
}
?>
