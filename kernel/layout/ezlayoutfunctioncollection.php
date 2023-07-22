<?php
/**
 * File containing the eZLayoutFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZLayoutFunctionCollection ezlayoutfunctioncollection.php
  \brief The class eZLayoutFunctionCollection does

*/

class eZLayoutFunctionCollection
{
    function fetchSitedesignList()
    {
        $sitedesignList = null;
        $contentINI = eZINI::instance( 'content.ini' );
        if ( $contentINI->hasVariable( 'VersionView', 'AvailableSiteDesigns' ) )
        {
            $sitedesignList = $contentINI->variableArray( 'VersionView', 'AvailableSiteDesigns' );
        }
        else if ( $contentINI->hasVariable( 'VersionView', 'AvailableSiteDesignList' ) )
        {
            $sitedesignList = $contentINI->variable( 'VersionView', 'AvailableSiteDesignList' );
        }
        if ( !$sitedesignList )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        return ['result' => $sitedesignList];
    }

}

?>
