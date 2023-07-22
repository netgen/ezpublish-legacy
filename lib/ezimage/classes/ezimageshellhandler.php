<?php
/**
 * File containing the eZImageShellHandler class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZImageShellHandler ezimageshellhandler.php
  \ingroup eZImage
  \brief The class eZImageShellHandler does

*/

class eZImageShellHandler extends eZImageHandler
{
    public function __construct( $handlerName, $isEnabled = true, $outputRewriteType = self::REPLACE_SUFFIX,
                                  $supportedInputMIMETypes = false, $supportedOutputMIMETypes = false,
                                  $conversionRules = false, $filters = false, $mimeTagMap = false)
    {
        parent::__construct( $handlerName, $isEnabled, $outputRewriteType,
                               $supportedInputMIMETypes, $supportedOutputMIMETypes,
                               $conversionRules, $filters, $mimeTagMap );
    }

    /*!
     Creates the shell string and runs the executable.
    */
    function convert( $manager, $sourceMimeData, &$destinationMimeData, $filters = false )
    {
        $argumentList = [];
        $executable = $this->Executable;
        if ( eZSys::osType() == 'win32' and $this->ExecutableWin32 )
            $executable = $this->ExecutableWin32;
        else if ( eZSys::osType() == 'mac' and $this->ExecutableMac )
            $executable = $this->ExecutableMac;
        else if ( eZSys::osType() == 'unix' and $this->ExecutableUnix )
            $executable = $this->ExecutableUnix;
        if ( $this->Path )
            $executable = $this->Path . eZSys::fileSeparator() . $executable;
        if ( eZSys::osType() == 'win32' )
            $executable = "\"$executable\"";

        $argumentList[] = $executable;

        if ( $this->PreParameters )
            $argumentList[] = $this->PreParameters;

        $frameRangeParameters = $this->FrameRangeParameters;
        if ( $frameRangeParameters && isset( $frameRangeParameters[$sourceMimeData['name']] ) )
        {
            $sourceMimeData['url'] .= $frameRangeParameters[$sourceMimeData['name']];
        }

        // Issue EZP-21357:
        // ImageMagick has it's own meta-characters support, hence:
        //     $ convert 'File*.jpg'' ...
        // Still expand File*.jpg as the shell would do, however, this is only true for the file's basename part and not
        // for the whole path.
        $argumentList[] = eZSys::escapeShellArgument(
            $sourceMimeData['dirpath'] . DIRECTORY_SEPARATOR . addcslashes(
                (string) $sourceMimeData['filename'],
                // ImageMagick meta-characters
                '~*?[]{}<>'
            )
        );

        $qualityParameters = $this->QualityParameters;
        if ( $qualityParameters and
             isset( $qualityParameters[$destinationMimeData['name']] ) )
        {
            $qualityParameter = $qualityParameters[$destinationMimeData['name']];
            $outputQuality = $manager->qualityValue( $destinationMimeData['name'] );
            if ( $outputQuality )
            {
                $qualityArgument = eZSys::createShellArgument( $qualityParameter, ['%1' => $outputQuality] );
                $argumentList[] = $qualityArgument;
            }
        }

        if ( $filters !== false )
        {
            foreach ( $filters as $filterData )
            {
                $argumentList[] = $this->textForFilter( $filterData );
            }
        }

        $destinationURL = $destinationMimeData['url'];
        if ( $this->UseTypeTag )
            $destinationURL = $this->tagForMIMEType( $destinationMimeData ) . $this->UseTypeTag . $destinationURL;
        $argumentList[] = eZSys::escapeShellArgument( $destinationURL );

        if ( $this->PostParameters )
            $argumentList[] = $this->PostParameters;

        $systemString = implode( ' ', $argumentList );

        system( $systemString, $returnCode );

        if ( $returnCode == 0 )
        {
            if ( !file_exists( $destinationMimeData['url'] ) )
            {
                eZDebug::writeError( 'Unknown destination file: ' . $destinationMimeData['url'] . " when executing '$systemString'", 'eZImageShellHandler(' . $this->HandlerName . ')' );
                return false;
            }
            static::changeFilePermissions($destinationMimeData['url']);
            return true;
        }
        else
        {
            eZDebug::writeWarning( "Failed executing: $systemString, Error code: $returnCode", __METHOD__ );
            return false;
        }

    }

    /*!
     Creates a new image handler for shell executable from INI settings.
     The INI settings are read from ini file \a $iniFilename and group \a $iniGroup.
     If \a $iniFilename is not supplied \c image.ini is used.
    */
    static function createFromINI( $iniGroup, $iniFilename = false )
    {
        if ( !$iniFilename )
            $iniFilename = 'image.ini';

        $handler = false;
        $ini = eZINI::instance( $iniFilename );
        if ( !$ini )
        {
            eZDebug::writeError( "Failed loading ini file $iniFilename", __METHOD__ );
            return $handler;
        }

        if ( $ini->hasGroup( $iniGroup ) )
        {
            $name = $iniGroup;
            if ( $ini->hasVariable( $iniGroup, 'Name' ) )
                $name = $ini->variable( $iniGroup, 'Name' );
            $inputMimeList = false;
            $outputMimeList = false;
            if ( $ini->hasVariable( $iniGroup, 'InputMIMEList' ) )
                $inputMimeList = $ini->variable( $iniGroup, 'InputMIMEList' );
            if ( $ini->hasVariable( $iniGroup, 'OutputMIMEList' ) )
                $outputMimeList = $ini->variable( $iniGroup, 'OutputMIMEList' );
            $qualityParameters = [];
            if ( $ini->hasVariable( $iniGroup, 'QualityParameters' ) )
            {
                $qualityParametersRaw = $ini->variable( $iniGroup, 'QualityParameters' );
                foreach ( $qualityParametersRaw as $qualityParameterRaw )
                {
                    $elements = explode( ';', (string) $qualityParameterRaw );
                    $qualityParameters[$elements[0]] = $elements[1];
                }
            }
            $frameRangeParameters = [];
            if ( $ini->hasVariable( $iniGroup, 'FrameRangeParameters' ) )
            {
                foreach ( $ini->variable( $iniGroup, 'FrameRangeParameters' ) as $frameRangeParameter )
                {
                    $elements = explode( ';', (string) $frameRangeParameter );
                    $frameRangeParameters[$elements[0]] = $elements[1];
                }
            }

            $conversionRules = false;
            if ( $ini->hasVariable( $iniGroup, 'ConversionRules' ) )
            {
                $conversionRules = [];
                $rules = $ini->variable( $iniGroup, 'ConversionRules' );
                foreach ( $rules as $ruleString )
                {
                    $ruleItems = explode( ';', (string) $ruleString );
                    if ( count( $ruleItems ) >= 2 )
                    {
                        $conversionRules[] = ['from' => $ruleItems[0], 'to' => $ruleItems[1]];
                    }
                }
            }
            $isEnabled = $ini->variable( $iniGroup, 'IsEnabled' ) == 'true';
            $path = false;
            $executable = false;
            $preParameters = false;
            $postParameters = false;
            if ( $ini->hasVariable( $iniGroup, 'ExecutablePath' ) )
                $path = $ini->variable( $iniGroup, 'ExecutablePath' );
            if ( !$ini->hasVariable( $iniGroup, 'Executable' ) )
            {
                eZDebug::writeError( "No Executable setting for group $iniGroup in ini file $iniFilename", __METHOD__ );
                return $handler;
            }
            $executable = $ini->variable( $iniGroup, 'Executable' );
            $executableWin32 = false;
            $executableMac = false;
            $executableUnix = false;
            $ini->assign( $iniGroup, 'ExecutableWin32', $executableWin32 );
            $ini->assign( $iniGroup, 'ExecutableMac', $executableMac );
            $ini->assign( $iniGroup, 'ExecutableUnix', $executableUnix );

            if ( $ini->hasVariable( $iniGroup, 'ExecutablePath' ) )
                $path = $ini->variable( $iniGroup, 'ExecutablePath' );
            if ( $ini->hasVariable( $iniGroup, 'PreParameters' ) )
                $preParameters = $ini->variable( $iniGroup, 'PreParameters' );
            if ( $ini->hasVariable( $iniGroup, 'PostParameters' ) )
                $postParameters = $ini->variable( $iniGroup, 'PostParameters' );
            $useTypeTag = false;
            if ( $ini->hasVariable( $iniGroup, 'UseTypeTag' ) )
            {
                $useTypeTag = $ini->variable( $iniGroup, 'UseTypeTag' );
            }
            $outputRewriteType = self::REPLACE_SUFFIX;
            $filters = false;
            if ( $ini->hasVariable( $iniGroup, 'Filters' ) )
            {
                $filterRawList = $ini->variable( $iniGroup, 'Filters' );
                $filters = [];
                foreach ( $filterRawList as $filterRawItem )
                {
                    $filter = eZImageHandler::createFilterDefinitionFromINI( $filterRawItem );
                    $filters[] = $filter;
                }
            }
            $mimeTagMap = false;
            if ( $ini->hasVariable( $iniGroup, 'MIMETagMap' ) )
            {
                $mimeTagMapList = $ini->variable( $iniGroup, 'MIMETagMap' );
                $mimeTagMap = [];
                foreach ( $mimeTagMapList as $mimeTagMapItem )
                {
                    $mimeTagMapArray = explode( ';', (string) $mimeTagMapItem );
                    if ( count( $mimeTagMapArray ) >= 2 )
                        $mimeTagMap[$mimeTagMapArray[0]] = $mimeTagMapArray[1];
                }
            }
            $handler = new eZImageShellHandler( $name, $isEnabled,
                                                $outputRewriteType,
                                                $inputMimeList, $outputMimeList,
                                                $conversionRules, $filters, $mimeTagMap );
            $handler->Path = $path;
            $handler->Executable = $executable;
            $handler->ExecutableWin32 = $executableWin32;
            $handler->ExecutableMac = $executableMac;
            $handler->ExecutableUnix = $executableUnix;
            $handler->PreParameters = $preParameters;
            $handler->PostParameters = $postParameters;
            $handler->UseTypeTag = $useTypeTag;
            $handler->QualityParameters = $qualityParameters;
            $handler->FrameRangeParameters = $frameRangeParameters;
            return $handler;
        }
        return $handler;
    }

    /// \privatesection
    public $Path = false;
    public $Executable = false;
    public $PreParameters = false;
    public $PostParameters = false;

    public $ExecutableWin32;
    public $ExecutableMac;
    public $ExecutableUnix;
    public $HandlerName;
    public $SupportedInputMIMETypes;
    public $SupportedOutputMIMETypes;
    public $ConversionRules;
    public $OutputRewriteType;
    public $Filters;
    public $FilterMap;
    public $SupportImageFilters;
    public $MIMETagMap;
    public $IsEnabled;
    public $UseTypeTag = false;
    public $QualityParameters = false;
    public $FrameRangeParameters = false;
}

?>
