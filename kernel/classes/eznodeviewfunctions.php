<?php
/**
 * File containing the eZNodeviewfunctions class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNodeviewfunctions eznodeviewfunctions.php
  \brief The class eZNodeviewfunctions does

*/

class eZNodeviewfunctions
{
    // Deprecated function for generating the view cache
    static function generateNodeView( $tpl, $node, $object, $languageCode, $viewMode, $offset,
                                      $cacheDir, $cachePath, $viewCacheEnabled,
                                      $viewParameters = ['offset' => 0, 'year' => false, 'month' => false, 'day' => false],
                                      $collectionAttributes = false, $validation = false )
    {
        $cacheFile = eZClusterFileHandler::instance( $cachePath );
        $args = compact( "tpl", "node", "object", "languageCode", "viewMode", "offset",
                         "viewCacheEnabled",
                         "viewParameters",
                         "collectionAttributes", "validation" );
        $Result = $cacheFile->processCache( null, // no retrieve, only generate is called
                                            ['eZNodeviewfunctions', 'generateCallback'],
                                            null,
                                            null,
                                            $args );
        return $Result;
    }

    // Note: This callback is needed to generate the array which is returned
    //       back to eZClusterFileHandler for processing.
    static function generateCallback( $file, $args )
    {
        $tpl = null;
        $node = null;
        $object = null;
        $languageCode = null;
        $viewMode = null;
        $offset = null;
        $viewParameters = null;
        $collectionAttributes = null;
        $validation = null;
        $viewCacheEnabled = null;
        extract( $args );

        $res = eZNodeViewFunctions::generateNodeViewData( $tpl, $node, $object, $languageCode, $viewMode, $offset,
                                                          $viewParameters, $collectionAttributes, $validation );


        // Check if cache time = 0 (viewcache disabled)
        $store = $res['cache_ttl'] != 0;
        // or if explicitly turned off
        if ( !$viewCacheEnabled )
            $store = false;
        $retval = ['content' => $res, 'scope'   => 'viewcache', 'store'   => $store];
        if ( $store )
            $retval['binarydata'] = serialize( $res );

        return $retval;
    }

    /**
     * Generate result data for a node view
     *
     * @param bool|string $languageCode
     * @param string $viewMode
     * @param int $offset
     * @param bool|array $collectionAttributes
     * @param bool $validation
     * @return array Result array for view
     */
    static function generateNodeViewData( eZTemplate $tpl, eZContentObjectTreeNode $node, eZContentObject $object, $languageCode, $viewMode, $offset,
                                          array $viewParameters = ['offset' => 0, 'year' => false, 'month' => false, 'day' => false],
                                          $collectionAttributes = false, $validation = false )
    {
        $section = eZSection::fetch( $object->attribute( 'section_id' ) );
        if ( $section )
        {
            $navigationPartIdentifier = $section->attribute( 'navigation_part_identifier' );
            $sectionIdentifier = $section->attribute( 'identifier' );
        }
        else
        {
            $navigationPartIdentifier = null;
            $sectionIdentifier = null;
        }

        $keyArray = [['object', $object->attribute( 'id' )], ['node', $node->attribute( 'node_id' )], ['parent_node', $node->attribute( 'parent_node_id' )], ['class', $object->attribute( 'contentclass_id' )], ['class_identifier', $node->attribute( 'class_identifier' )], ['view_offset', $offset], ['viewmode', $viewMode], ['remote_id', $object->attribute( 'remote_id' )], ['node_remote_id', $node->attribute( 'remote_id' )], ['navigation_part_identifier', $navigationPartIdentifier], ['depth', $node->attribute( 'depth' )], ['url_alias', $node->attribute( 'url_alias' )], ['class_group', $object->attribute( 'match_ingroup_id_list' )], ['state', $object->attribute( 'state_id_array' )], ['state_identifier', $object->attribute( 'state_identifier_array' )], ['section', $object->attribute( 'section_id' )], ['section_identifier', $sectionIdentifier]];

        $parentClassID = false;
        $parentClassIdentifier = false;
        $parentNodeRemoteID = false;
        $parentObjectRemoteID = false;
        $parentNode = $node->attribute( 'parent' );
        if ( is_object( $parentNode ) )
        {
            $parentNodeRemoteID = $parentNode->attribute( 'remote_id' );
            $keyArray[] = ['parent_node_remote_id', $parentNodeRemoteID];

            $parentObject = $parentNode->attribute( 'object' );
            if ( is_object( $parentObject ) )
            {
                $parentObjectRemoteID = $parentObject->attribute( 'remote_id' );
                $keyArray[] = ['parent_object_remote_id', $parentObjectRemoteID];

                $parentClass = $parentObject->contentClass();
                if ( is_object( $parentClass ) )
                {
                    $parentClassID = $parentClass->attribute( 'id' );
                    $parentClassIdentifier = $parentClass->attribute( 'identifier' );

                    $keyArray[] = ['parent_class', $parentClassID];
                    $keyArray[] = ['parent_class_identifier', $parentClassIdentifier];
                }
            }
        }

        $res = eZTemplateDesignResource::instance();
        $res->setKeys( $keyArray );

        if ( $languageCode )
        {
            $oldLanguageCode = $node->currentLanguage();
            $node->setCurrentLanguage( $languageCode );
        }

        if ( isset( $viewParameters['_custom'] ) )
        {
            foreach ( $viewParameters['_custom'] as $customVarName => $customValue )
            {
                $tpl->setVariable( $customVarName, $customValue );
            }

            unset( $viewParameters['_custom'] );
        }

        $tpl->setVariable( 'node', $node );
        $tpl->setVariable( 'viewmode', $viewMode );
        $tpl->setVariable( 'language_code', $languageCode );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'collection_attributes', $collectionAttributes );
        $tpl->setVariable( 'validation', $validation );
        $tpl->setVariable( 'persistent_variable', false );

        $parents = $node->attribute( 'path' );

        $path = [];
        $titlePath = [];
        foreach ( $parents as $parent )
        {
            $path[] = ['text' => $parent->attribute( 'name' ), 'url' => '/content/view/full/' . $parent->attribute( 'node_id' ), 'url_alias' => $parent->attribute( 'url_alias' ), 'node_id' => $parent->attribute( 'node_id' )];
        }

        $titlePath = $path;
        $path[] = ['text' => $object->attribute( 'name' ), 'url' => false, 'url_alias' => false, 'node_id' => $node->attribute( 'node_id' )];

        $titlePath[] = ['text' => $object->attribute( 'name' ), 'url' => false, 'url_alias' => false];

        $tpl->setVariable( 'node_path', $path );

        $event = ezpEvent::getInstance();
        $event->notify( 'content/pre_rendering', [$node, $tpl, $viewMode] );

        $Result = [];
        $Result['content']         = $tpl->fetch( 'design:node/view/' . $viewMode . '.tpl' );
        $Result['view_parameters'] = $viewParameters;
        $Result['path']            = $path;
        $Result['title_path']      = $titlePath;
        $Result['section_id']      = $object->attribute( 'section_id' );
        $Result['node_id']         = $node->attribute( 'node_id' );
        $Result['navigation_part'] = $navigationPartIdentifier;

        $contentInfoArray = [];
        $contentInfoArray['object_id']        = $object->attribute( 'id' );
        $contentInfoArray['node_id']          = $node->attribute( 'node_id' );
        $contentInfoArray['parent_node_id']   = $node->attribute( 'parent_node_id' );
        $contentInfoArray['class_id']         = $object->attribute( 'contentclass_id' );
        $contentInfoArray['class_identifier'] = $node->attribute( 'class_identifier' );
        $contentInfoArray['remote_id']        = $object->attribute( 'remote_id' );
        $contentInfoArray['node_remote_id']   = $node->attribute( 'remote_id' );
        $contentInfoArray['offset']           = $offset;
        $contentInfoArray['viewmode']         = $viewMode;
        $contentInfoArray['navigation_part_identifier'] = $navigationPartIdentifier;
        $contentInfoArray['node_depth']       = $node->attribute( 'depth' );
        $contentInfoArray['url_alias']        = $node->attribute( 'url_alias' );
        $contentInfoArray['current_language'] = $object->attribute( 'current_language' );
        $contentInfoArray['language_mask']    = $object->attribute( 'language_mask' );

        $contentInfoArray['main_node_id']   = $node->attribute( 'main_node_id' );
        $contentInfoArray['main_node_url_alias'] = false;
        // Add url alias for main node if it is not current node and user has access to it
        if ( !$node->isMain() )
        {
            $mainNode = $object->mainNode();
            if ( $mainNode->canRead() )
            {
                $contentInfoArray['main_node_url_alias'] = $mainNode->attribute( 'url_alias' );
            }
        }

        $contentInfoArray['persistent_variable'] = false;
        if ( $tpl->variable( 'persistent_variable' ) !== false )
        {
            $contentInfoArray['persistent_variable'] = $tpl->variable( 'persistent_variable' );
            $keyArray[] = ['persistent_variable', $contentInfoArray['persistent_variable']];
            $res->setKeys( $keyArray );
        }
        $contentInfoArray['class_group']             = $object->attribute( 'match_ingroup_id_list' );
        $contentInfoArray['state']                   = $object->attribute( 'state_id_array' );
        $contentInfoArray['state_identifier']        = $object->attribute( 'state_identifier_array' );
        $contentInfoArray['section_identifier']      = $sectionIdentifier;
        $contentInfoArray['parent_class_id']         = $parentClassID;
        $contentInfoArray['parent_class_identifier'] = $parentClassIdentifier;
        $contentInfoArray['parent_node_remote_id']   = $parentNodeRemoteID;
        $contentInfoArray['parent_object_remote_id'] = $parentObjectRemoteID;

        $Result['content_info'] = $contentInfoArray;

        // Store which templates were used to make this cache.
        $Result['template_list'] = $tpl->templateFetchList();

        // Check if time to live is set in template
        if ( $tpl->hasVariable( 'cache_ttl' ) )
        {
            $cacheTTL = $tpl->variable( 'cache_ttl' );
        }

        if ( !isset( $cacheTTL ) )
        {
            $cacheTTL = -1;
        }

        $Result['cache_ttl'] = $cacheTTL;

        // if cache_ttl is set to 0 from the template, we need to add a no-cache advice
        // to the node's data. That way, the retrieve callback on the next calls
        // will be able to determine earlier that no cache generation should be started
        // for this node
        if ( $cacheTTL == 0 )
        {
            $Result['no_cache'] = true;
        }

        if ( $languageCode )
        {
            $node->setCurrentLanguage( $oldLanguageCode );
        }

        return $Result;
    }

    static function generateViewCacheFile( $user, $nodeID, $offset, $layout, $language, $viewMode, $viewParameters = false, $cachedViewPreferences = false, $viewCacheTweak = '' )
    {
        $cacheNameExtra = '';
        $ini = eZINI::instance();

        if ( !$language )
        {
            $language = false;
        }

        if ( !$viewCacheTweak && $ini->hasVariable( 'ContentSettings', 'ViewCacheTweaks' ) )
        {
            $viewCacheTweaks = $ini->variable( 'ContentSettings', 'ViewCacheTweaks' );
            if ( isset( $viewCacheTweaks[$nodeID] ) )
            {
                $viewCacheTweak = $viewCacheTweaks[$nodeID];
            }
            else if ( isset( $viewCacheTweaks['global'] ) )
            {
                $viewCacheTweak = $viewCacheTweaks['global'];
            }
        }

        // should we use current siteaccess or let several siteaccesse share cache?
        if ( !str_contains( (string) $viewCacheTweak, 'ignore_siteaccess_name' ) )
        {
            $currentSiteAccess = $GLOBALS['eZCurrentAccess']['name'];
        }
        else
        {
            $currentSiteAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        }

        $cacheHashArray = [$nodeID, $viewMode, $language, $offset, $layout];

        // several user related cache tweaks
        if ( !str_contains( (string) $viewCacheTweak, 'ignore_userroles' ) )
        {
            $cacheHashArray[] = implode( '.', $user->roleIDList() );
        }

        if ( !str_contains( (string) $viewCacheTweak, 'ignore_userlimitedlist' ) )
        {
            $cacheHashArray[] = implode( '.', $user->limitValueList() );
        }

        if ( !str_contains( (string) $viewCacheTweak, 'ignore_discountlist' ) )
        {
            $cacheHashArray[] = implode( '.', eZUserDiscountRule::fetchIDListByUserID( $user->attribute( 'contentobject_id' ) ) );
        }

        $cacheHashArray[] = eZSys::indexFile();

        // Add access type to cache hash if current access is uri type (so uri and host doesn't share cache)
        if ( !str_contains( (string) $viewCacheTweak, 'ignore_siteaccess_type' ) && $GLOBALS['eZCurrentAccess']['type'] === eZSiteAccess::TYPE_URI )
        {
            $cacheHashArray[] = eZSiteAccess::TYPE_URI;
        }

        // Make the cache unique for every logged in user
        if ( str_contains( (string) $viewCacheTweak, 'pr_user' ) and !$user->isAnonymous() )
        {
            $cacheNameExtra = $user->attribute( 'contentobject_id' ) . '-';
        }

        // Add the request protocol to the cache key generation
        if ( str_contains( (string) $viewCacheTweak, 'protocol' ) )
        {
            $cacheHashArray[] = eZSys::isSSLNow();
        }

        // Make the cache unique for every case of view parameters
        if ( !str_contains( (string) $viewCacheTweak, 'ignore_viewparameters' ) && $viewParameters )
        {
            $vpString = '';
            ksort( $viewParameters );
            foreach ( $viewParameters as $key => $value )
            {
                if ( !$key || $key === '_custom' )
                    continue;
                $vpString .= 'vp:' . $key . '=' . $value;
            }
            $cacheHashArray[] = $vpString;
        }

        // Make the cache unique for every case of the preferences
        if ( $cachedViewPreferences === false )
        {
            $depPreferences = $ini->variable( 'ContentSettings', 'CachedViewPreferences' );
        }
        else
        {
            $depPreferences = $cachedViewPreferences;
        }

        if ( !str_contains( (string) $viewCacheTweak, 'ignore_userpreferences' ) && isset ( $depPreferences[$viewMode] ) )
        {
            $depPreferences = explode( ';', (string) $depPreferences[$viewMode] );
            $pString = '';
            // Fetch preferences for the specified user
            $preferences = eZPreferences::values( $user );
            foreach( $depPreferences as $pref )
            {
                $pref = explode( '=', $pref );
                if ( isset( $pref[0] ) )
                {
                    if ( isset( $preferences[$pref[0]] ) )
                        $pString .= 'p:' . $pref[0] . '='. $preferences[$pref[0]]. ';';
                    else if ( isset( $pref[1] ) )
                        $pString .= 'p:' . $pref[0] . '='. $pref[1]. ';';
                }
            }
            $cacheHashArray[] = $pString;
        }

        $cacheFile = $nodeID . '-' . $cacheNameExtra . md5( implode( '-', $cacheHashArray ) ) . '.cache';
        $extraPath = eZDir::filenamePath( $nodeID );
        $cacheDir = eZDir::path( [eZSys::cacheDirectory(), $ini->variable( 'ContentSettings', 'CacheDir' ), $currentSiteAccess, $extraPath] );
        $cachePath = eZDir::path( [$cacheDir, $cacheFile] );

        return ['cache_path' => $cachePath, 'cache_dir' => $cacheDir, 'cache_file' => $cacheFile];
    }

    /**
     * Retrieve content view data
     *
     * @see contentViewGenerate()
     *
     * @param string $file
     * @param int $mtime File modification time
     * @param array $args Hash containing arguments, the used ones are:
     *  - ini
     *
     * @return \eZClusterFileFailure
     */
    static public function contentViewRetrieve( $file, $mtime, $args )
    {
        $ini = null;
        extract( $args );

        $cacheExpired = false;

        // Read Cache file
        if ( !eZContentObject::isCacheExpired( $mtime ) )
        {
//        $contents = $cacheFile->fetchContents();
            $contents = file_get_contents( $file );
            $Result = unserialize( $contents );

            if( !is_array( $Result ) )
            {
                $expiryReason = 'Unexpected cache file content';
                $cacheExpired = true;
            }

            // Check if a no_cache key has been set in the viewcache, and
            // return an eZClusterFileFailure if it has
            if ( isset( $Result['no_cache'] ) )
            {
                return new eZClusterFileFailure( 3, "Cache has been disabled for this node" );
            }

            // Check if cache has expired when cache_ttl is set
            $cacheTTL = $Result['cache_ttl'] ?? -1;
            if ( $cacheTTL > 0 )
            {
                $expiryTime = $mtime + $cacheTTL;
                if ( time() > $expiryTime )
                {
                    $cacheExpired = true;
                    $expiryReason = 'Content cache is expired by cache_ttl=' . $cacheTTL;
                }
            }

            // Check if template source files are newer, but only if the cache is not expired
            if ( !$cacheExpired )
            {
                $developmentModeEnabled = $ini->variable( 'TemplateSettings', 'DevelopmentMode' ) == 'enabled';
                // Only do filemtime checking when development mode is enabled.
                if ( $developmentModeEnabled &&
                     isset( $Result['template_list'] ) ) // And only if there is a list stored in the cache
                {
                    foreach ( $Result['template_list'] as $templateFile )
                    {
                        if ( !file_exists( $templateFile ) )
                        {
                            $cacheExpired = true;
                            $expiryReason = "Content cache is expired by template file '" . $templateFile . "', it does not exist anymore";
                            break;
                        }
                        else if ( filemtime( $templateFile ) > $mtime )
                        {
                            $cacheExpired = true;
                            $expiryReason = "Content cache is expired by template file '" . $templateFile . "'";
                            break;
                        }
                    }
                }
            }

            if ( !$cacheExpired )
            {
                if ( !isset( $Result['content_info'] ) )
                {
                    // set error type & number for kernel errors (see https://jira.ez.no/browse/EZP-23046)
                    if ( isset( $Result['errorType'] ) && isset( $Result['errorNumber'] ) )
                    {
                        $res = eZTemplateDesignResource::instance();
                        $res->setKeys(
                            [['error_type', $Result['errorType']], ['error_number', $Result['errorNumber']]]
                        );
                    }
                    return $Result;
                }
                $keyArray = [['object', $Result['content_info']['object_id']], ['node', $Result['content_info']['node_id']], ['parent_node', $Result['content_info']['parent_node_id']], ['parent_node_remote_id', $Result['content_info']['parent_node_remote_id']], ['parent_object_remote_id', $Result['content_info']['parent_object_remote_id']], ['class', $Result['content_info']['class_id']], ['view_offset', $Result['content_info']['offset']], ['navigation_part_identifier', $Result['content_info']['navigation_part_identifier']], ['viewmode', $Result['content_info']['viewmode']], ['depth', $Result['content_info']['node_depth']], ['remote_id', $Result['content_info']['remote_id']], ['node_remote_id', $Result['content_info']['node_remote_id']], ['url_alias', $Result['content_info']['url_alias']], ['persistent_variable', $Result['content_info']['persistent_variable']], ['class_group', $Result['content_info']['class_group']], ['parent_class_id', $Result['content_info']['parent_class_id']], ['parent_class_identifier', $Result['content_info']['parent_class_identifier']], ['state', $Result['content_info']['state']], ['state_identifier', $Result['content_info']['state_identifier']], ['section', $Result['section_id']]];

                if ( isset( $Result['content_info']['class_identifier'] ) )
                    $keyArray[] = ['class_identifier', $Result['content_info']['class_identifier']];

                // Added in 5.3.5 / 5.4.2, so test that cache contains this before using
                if ( isset( $Result['content_info']['section_identifier'] ) )
                    $keyArray[] = ['section_identifier', $Result['content_info']['section_identifier']];

                $res = eZTemplateDesignResource::instance();
                $res->setKeys( $keyArray );

                return $Result;
            }
        }
        else
        {
            $expiryReason = 'Content cache is expired by eZContentObject::isCacheExpired(' . $mtime . ")";
        }

        // Cache is expired so return specialized cluster object
        if ( !isset( $expiryReason ) )
            $expiryReason = 'Content cache is expired';
        return new eZClusterFileFailure( 1, $expiryReason );
    }

    /**
     * Generate convent view data
     *
     * @see contentViewRetrieve()
     *
     * @param string|false $file File in which the result will be cached
     * @param array $args Hash containing arguments, the used ones are:
     *  - NodeID
     *  - Module
     *  - tpl
     *  - LanguageCode
     *  - ViewMode
     *  - Offset
     *  - viewParameters
     *  - collectionAttributes
     *  - validation
     *  - noCache (optional)
     *
     * @return array
     */
    static public function contentViewGenerate( $file, $args )
    {
        $NodeID = null;
        $Module = null;
        $tpl = null;
        $LanguageCode = null;
        $ViewMode = null;
        $Offset = null;
        $viewParameters = null;
        $collectionAttributes = null;
        $validation = null;
        extract( $args );
        $node = eZContentObjectTreeNode::fetch( $NodeID );
        if ( !$node instanceof eZContentObjectTreeNode )
        {
            if ( !eZDB::instance()->isConnected())
            {
                return self::contentViewGenerateError( $Module, eZError::KERNEL_NO_DB_CONNECTION, false );
            }

            return self::contentViewGenerateError( $Module, eZError::KERNEL_NOT_AVAILABLE );
        }

        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject )
        {
            return self::contentViewGenerateError( $Module, eZError::KERNEL_NOT_AVAILABLE );
        }

        if ( $node->attribute( 'is_invisible' ) && !eZContentObjectTreeNode::showInvisibleNodes() )
        {
            return self::contentViewGenerateError( $Module, eZError::KERNEL_ACCESS_DENIED );
        }

        if ( !$node->canRead() )
        {
            return self::contentViewGenerateError(
                $Module,
                eZError::KERNEL_ACCESS_DENIED,
                true,
                ['AccessList' => $node->checkAccess( 'read', false, false, true )]
            );
        }

        $result = self::generateNodeViewData(
            $tpl,
            $node,
            $object,
            $LanguageCode,
            $ViewMode,
            $Offset,
            $viewParameters,
            $collectionAttributes,
            $validation
        );

        // 'store' depends on noCache: if $noCache is set, this means that retrieve
        // returned it, and the noCache fake cache file is already stored
        // and should not be stored again
        $retval = ['content' => $result, 'scope'   => 'viewcache', 'store'   => !( isset( $noCache ) and $noCache )];
        if ( $file !== false && $retval['store'] )
            $retval['binarydata'] = serialize( $result );
        return $retval;
    }

    /**
     * @param int $error
     * @param bool $store
     *
     * @return array
     */
    static protected function contentViewGenerateError( eZModule $Module, $error, $store = true, array $errorParameters = [] )
    {
        $content = $Module->handleError(
            $error,
            'kernel',
            $errorParameters
        );

        return ['content' => $content, 'scope' => 'viewcache', 'store' => $store, 'binarydata' => serialize( $content )];
    }
}

?>
