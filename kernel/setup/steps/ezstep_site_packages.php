<?php
/**
 * File containing the eZStepSitePackages class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepSitePackages ezstep_site_packages.php
  \brief The class eZStepSitePackages does

*/

class eZStepSitePackages extends eZStepInstaller
{
    /**
     * Constructor
     *
     * @param eZTemplate $tpl
     * @param eZHTTPTool $http
     * @param eZINI $ini
     * @param array $persistenceList
     */
    public function __construct( $tpl, $http, $ini, &$persistenceList )
    {
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'site_packages', 'Site packages' );
    }

    function processPostData()
    {
        if ( $this->Http->hasPostVariable( 'eZSetup_site_packages' ) )
        {
            $sitePackages = $this->Http->postVariable( 'eZSetup_site_packages' );
            $this->PersistenceList['site_packages'] = $sitePackages;

            if ( $this->Http->hasPostVariable( 'AdditionalPackages' ) )
            {
                $this->PersistenceList['additional_packages'] = $this->Http->postVariable( 'AdditionalPackages' );
            }
            else
            {
                $this->PersistenceList['additional_packages'] = [];
            }
        }
        else
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                      'No packages chosen.' );
            return false;
        }
        return true;
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            $siteTypes = $this->chosenSiteTypes();
            $siteType = $siteTypes[0]['identifier'];

            $typeFunctionality = eZSetupFunctionality( $siteType );
            $requiredPackageList = $typeFunctionality['required'];
            $requiredPackages = [];
            foreach ( $requiredPackageList as $requiredPackage )
            {
                $requiredPackages[] = strtolower( (string) $requiredPackage );
            }
            $this->PersistenceList['site_packages'] = $requiredPackages;

            $additionalPackages = $data['Packages'];
            foreach ( $additionalPackages as $key => $additionalPackage )
            {
                $additionalPackages[$key] = strtolower( (string) $additionalPackage );
            }
            $this->PersistenceList['additional_packages'] = $additionalPackages;

            if ( ( count( $requiredPackages ) + (is_countable($additionalPackages) ? count( $additionalPackages ) : 0) ) > 0 )
            {
                return $this->kickstartContinueNextStep();
            }
        }

        $this->ErrorMsg = false;
        return false; // Always show site package selection
    }

    function display()
    {
        $siteTypes = $this->chosenSiteTypes();
        $siteType = $siteTypes[0]['identifier'];

        $typeFunctionality = eZSetupFunctionality( $siteType );
        $requiredPackageList = $typeFunctionality['required'];
        $requiredPackages = [];
        foreach ( $requiredPackageList as $requiredPackage )
        {
            $requiredPackages[] = strtolower( (string) $requiredPackage );
        }

        $packageArray = eZPackage::fetchPackages( ['repository_id' => 'addons'] );

        $requiredPackageInfoArray = [];
        $packageInfoArray = [];
        foreach ( $packageArray as $package )
        {
            if ( in_array( strtolower( (string) $package->attribute( 'name' ) ), $requiredPackages ) )
            {
                $requiredPackageInfoArray[] = ['identifier' => $package->attribute( 'name' ), 'name' => $package->attribute( 'summary' ), 'description' => $package->attribute( 'description' )];
            }
            else
            {
                $packageInfoArray[] = ['identifier' => $package->attribute( 'name' ), 'name' => $package->attribute( 'summary' ), 'description' => $package->attribute( 'description' )];
            }
        }

        $recommended = [];
        if ( isset( $typeFunctionality['recommended'] ) )
            $recommended = $typeFunctionality['recommended'];

        if ( isset( $this->PersistenceList['additional_packages'] ) )
            $recommended = array_unique( array_merge( $recommended,
                                                      $this->PersistenceList['additional_packages'] ) );

        $this->Tpl->setVariable( 'site_types', $siteTypes );
        $this->Tpl->setVariable( 'recommended_package_array', $recommended );
        $this->Tpl->setVariable( 'error', $this->ErrorMsg );
        $this->Tpl->setVariable( 'required_package_array', $requiredPackageInfoArray );
        $this->Tpl->setVariable( 'package_array', $packageInfoArray );

        // Return template and data to be shown
        $result = [];
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/site_packages.tpl' );
        $result['path'] = [['text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Site functionality' ), 'url' => false]];
        return $result;

    }

    public $Error = 0;
}

?>
