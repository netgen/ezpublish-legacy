<?php
/**
 * File containing the eZPublishSDK class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \brief contains the eZ Publish SDK version.
*/

class eZPublishSDK
{
    final public const VERSION_MAJOR = 5;
    final public const VERSION_MINOR = 99;
    final public const VERSION_RELEASE = 99;
    final public const VERSION_STATE = 'alpha1';
    final public const VERSION_DEVELOPMENT = true;
    final public const VERSION_ALIAS = '2020.99';
    final public const EDITION = 'eZ Publish legacy';

    /*!
      \return the SDK version as a string
      \param withRelease If true the release version is appended
      \param withAlias If true the alias is used instead
    */
    static function version( $withRelease = true, $asAlias = false, $withState = true )
    {
        if ( $asAlias )
        {
            $versionText = eZPublishSDK::alias();
            if ( $withState && eZPublishSDK::state() )
                $versionText .= "-" . eZPublishSDK::state();
        }
        else
        {
            $versionText = eZPublishSDK::majorVersion() . '.' . eZPublishSDK::minorVersion();
//            $development = eZPublishSDK::developmentVersion();
//            if ( $development !== false )
//                $versionText .= '.' . $development;
            if ( $withRelease )
                $versionText .= "." . eZPublishSDK::release();
            if ( $withState )
                $versionText .= eZPublishSDK::state();
        }
        return $versionText;
    }

    /*!
     \return the major version
    */
    static function majorVersion()
    {
        return eZPublishSDK::VERSION_MAJOR;
    }

    /*!
     \return the minor version
    */
    static function minorVersion()
    {
        return eZPublishSDK::VERSION_MINOR;
    }

    /*!
     \return the state of the release
    */
    static function state()
    {
        return eZPublishSDK::VERSION_STATE;
    }

    /*!
     \return the development version or \c false if this is not a development version
    */
    static function developmentVersion()
    {
        return eZPublishSDK::VERSION_DEVELOPMENT;
    }

    /*!
     \return the release number
    */
    static function release()
    {
        return eZPublishSDK::VERSION_RELEASE;
    }

    /*!
     \return the alias name for the release, this is often used for beta releases and release candidates.
    */
    static function alias()
    {
        return eZPublishSDK::VERSION_ALIAS;
    }

    /*!
      \return the version of the database.
      \param withRelease If true the release version is appended
    */
    static function databaseVersion( $withRelease = true )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as version FROM ezsite_data WHERE name='ezpublish-version'" );
        $version = false;
        if ( count( $rows ) > 0 )
        {
            $version = $rows[0]['version'];
            if ( $withRelease )
            {
                $release = eZPublishSDK::databaseRelease();
                $version .= '-' . $release;
            }
        }
        return $version;
    }

    /*!
      \return the release of the database.
    */
    static function databaseRelease()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as release FROM ezsite_data WHERE name='ezpublish-release'" );
        $release = false;
        if ( count( $rows ) > 0 )
        {
            $release = $rows[0]['release'];
        }
        return $release;
    }
}

?>
