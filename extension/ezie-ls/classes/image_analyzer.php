<?php
/**
 * File containing the eZIEImageAnalyzer class
 * This class overrides ezcImageAnalyzer in order to support the ezpublish cluster constraints
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */
class eZIEImageAnalyzer extends ezcImageAnalyzer
{
    /**
     * Constructor overload
     * Creates a local copy of the image so that it can be analyzed
     *
     * @param string $file
     */
    public function __construct( $file, protected $deleteLocal = true )
    {
        $clusterHandler = eZClusterFileHandler::instance( $file );
        $clusterHandler->fetch();

        parent::__construct( $file );
        if( $this->deleteLocal )
        {
            $clusterHandler->deleteLocal();
        }
    }

    /**
     * Overload of ezcImageAnalyzer::analyzeImage()
     * Creates a temporary local copy of the image file so that it can be analyzed
     *
     * @return void
     */
    public function analyzeImage()
    {
        $clusterHandler = eZClusterFileHandler::instance( $this->filePath );
        $clusterHandler->fetch();

        parent::analyzeImage();

        if( $this->deleteLocal )
        {
            $clusterHandler->deleteLocal();
        }
    }
}

?>