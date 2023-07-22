<?php
/**
 * File containing the eZSetupFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSetupFunctionCollection ezsetupfunctioncollection.php
  \brief The class eZSetupFunctionCollection does

*/

class eZSetupFunctionCollection
{
    function fetchFullVersionString()
    {
        return ['result' => eZPublishSDK::version()];
    }

    function fetchVersionAlias()
    {
        return ['result' => eZPublishSDK::version( false, true )];
    }

    function fetchMajorVersion()
    {
        return ['result' => eZPublishSDK::majorVersion()];
    }

    function fetchMinorVersion()
    {
        return ['result' => eZPublishSDK::minorVersion()];
    }

    function fetchRelease()
    {
        return ['result' => eZPublishSDK::release()];

    }

    function fetchState()
    {
        return ['result' => eZPublishSDK::state()];
    }

    function fetchIsDevelopment()
    {
        return ['result' => eZPublishSDK::developmentVersion() ? true : false];
    }

    function fetchDatabaseVersion( $withRelease = true )
    {
        return ['result' => eZPublishSDK::databaseVersion( $withRelease )];
    }

    function fetchDatabaseRelease()
    {
        return ['result' => eZPublishSDK::databaseRelease()];
    }

    function fetchEdition()
    {
        return ['result' => eZPublishSDK::EDITION];
    }
}

?>
