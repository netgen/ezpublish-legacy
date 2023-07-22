<?php
/**
 * File containing the eZContentFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZContentFunctionCollection ezcontentfunctioncollection.php
  \brief The class eZContentFunctionCollection does

*/

class eZContentFunctionCollection
{
    static public function fetchContentObject( $objectID, $remoteID = false )
    {
        if ( $objectID === false && $remoteID !== false )
        {
            $contentObject = eZContentObject::fetchByRemoteID( $remoteID );
        }
        else
        {
            $contentObject = eZContentObject::fetch( $objectID );
        }

        if ( $contentObject === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $contentObject];
        }

        return $result;
    }

    static public function fetchContentVersion( $objectID, $versionID )
    {
        $contentVersion = eZContentObjectVersion::fetchVersion( $versionID, $objectID );
        if ( !$contentVersion )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $contentVersion];
        }

        return $result;
    }

    static public function fetchContentNode( $nodeID, $nodePath, $languageCode, $remoteID = false )
    {
        $contentNode = null;
        if ( $nodeID )
        {
            if ( !isset( $languageCode ) )
                $languageCode = false;

            $contentNode = eZContentObjectTreeNode::fetch( $nodeID, $languageCode );
        }
        else if ( $nodePath )
        {
            $nodeID = eZURLAliasML::fetchNodeIDByPath( $nodePath );

            if ( $nodeID )
            {
               $contentNode = eZContentObjectTreeNode::fetch( $nodeID );
            }
        }
        else if ( $remoteID )
        {
            $contentNode = eZContentObjectTreeNode::fetchByRemoteID( $remoteID );
        }
        if ( $contentNode === null )
        {
            $retVal = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $retVal = ['result' => $contentNode];
        }

        return $retVal;
    }

    static public function fetchNonTranslationList( $objectID, $version )
    {
        $version = eZContentObjectVersion::fetchVersion( $version, $objectID );
        if ( !$version )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];

        $nonTranslationList = $version->nonTranslationList();
        if ( $nonTranslationList === null )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        return ['result' => $nonTranslationList];
    }

    static public function fetchTranslationList()
    {
        $translationList = eZContentObject::translationList();
        if ( $translationList === null )
        {
            $result =  ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $translationList];
        }

        return $result;
    }

    static public function fetchPrioritizedLanguages()
    {
        $languages = eZContentLanguage::prioritizedLanguages();
        if ( $languages === null )
        {
            $result =  ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $languages];
        }

        return $result;
    }

    static public function fetchPrioritizedLanguageCodes()
    {
        $languageCodes = eZContentLanguage::prioritizedLanguageCodes();
        if ( $languageCodes === null )
        {
            $result =  ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $languageCodes];
        }

        return $result;
    }

    static public function fetchLocaleList( $withVariations )
    {
        $localeList = eZLocale::localeList( true, $withVariations );
        if ( $localeList === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $localeList];
        }

        return $result;
    }

    static public function fetchLocale( $localeCode )
    {
        // Fetch locale list
        $localeList = eZLocale::localeList( false, true );
        $localeObj = eZLocale::instance( $localeCode );
        // Check if $localeName exists
        if ( $localeObj === null or ( is_object( $localeObj ) and !in_array( $localeObj->localeFullCode(), $localeList ) ) )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $localeObj];
        }

        return $result;
    }

    static public function fetchObject( $objectID )
    {
        $object = eZContentObject::fetch( $objectID );
        if ( $object === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $object];
        }

        return $result;
    }

    static public function fetchClass( $classID )
    {
        if ( !is_numeric( $classID ) )
            $object = eZContentClass::fetchByIdentifier( $classID );
        else
            $object = eZContentClass::fetch( $classID );
        if ( $object === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $object];
        }

        return $result;
    }

    static public function fetchClassAttributeList( $classID, $versionID )
    {
        $objectList = eZContentClass::fetch( $classID )->fetchAttributes( false, true, $versionID );
        if ( $objectList === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $objectList];
        }

        return $result;
    }

    static public function fetchClassAttribute( $attributeID, $versionID )
    {
        $attribute = eZContentClassAttribute::fetch( $attributeID, true, $versionID );
        if ( $attribute === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $attribute];
        }

        return $result;
    }

    static public function calendar( $parentNodeID, $offset, $limit, $depth, $depthOperator,
                               $classID, $attribute_filter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
                               $groupBy, $mainNodeOnly, $ignoreVisibility, $limitation )
    {
        $treeParameters = ['Offset' => $offset, 'Limit' => $limit, 'Limitation' => $limitation, 'class_id' => $classID, 'AttributeFilter' => $attribute_filter, 'ExtendedAttributeFilter' => $extended_attribute_filter, 'ClassFilterType' => $class_filter_type, 'ClassFilterArray' => $class_filter_array, 'IgnoreVisibility' => $ignoreVisibility, 'MainNodeOnly' => $mainNodeOnly];
        if ( is_array( $groupBy ) )
        {
            $groupByHash = ['field' => $groupBy[0], 'type' => false];
            if ( isset( $groupBy[1] ) )
                $groupByHash['type'] = $groupBy[1];
            $treeParameters['GroupBy'] = $groupByHash;
        }

        if ( $depth !== false )
        {
            $treeParameters['Depth'] = $depth;
            $treeParameters['DepthOperator'] = $depthOperator;
        }

        $children = null;
        if ( is_numeric( $parentNodeID ) )
        {
            $children = eZContentObjectTreeNode::calendar( $treeParameters,
                                                            $parentNodeID );
        }

        if ( $children === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $children];
        }
        return $result;
    }

    static public function fetchObjectTree( $parentNodeID, $sortBy, $onlyTranslated, $language, $offset, $limit, $depth, $depthOperator,
                              $classID, $attribute_filter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
                              $groupBy, $mainNodeOnly, $ignoreVisibility, $limitation, $asObject, $objectNameFilter, $loadDataMap = null )
    {
        $treeParameters = ['Offset' => $offset, 'OnlyTranslated' => $onlyTranslated, 'Language' => $language, 'Limit' => $limit, 'Limitation' => $limitation, 'SortBy' => $sortBy, 'class_id' => $classID, 'AttributeFilter' => $attribute_filter, 'ExtendedAttributeFilter' => $extended_attribute_filter, 'ClassFilterType' => $class_filter_type, 'ClassFilterArray' => $class_filter_array, 'IgnoreVisibility' => $ignoreVisibility, 'ObjectNameFilter' => $objectNameFilter, 'MainNodeOnly' => $mainNodeOnly];
        if ( is_array( $groupBy ) )
        {
            $groupByHash = ['field' => $groupBy[0], 'type' => false];
            if ( isset( $groupBy[1] ) )
                $groupByHash['type'] = $groupBy[1];
            $treeParameters['GroupBy'] = $groupByHash;
        }
        if ( $asObject !== null )
            $treeParameters['AsObject'] = $asObject;
        if ( $loadDataMap )
            $treeParameters['LoadDataMap'] = true;
        else if ( $loadDataMap === null )
            $treeParameters['LoadDataMap'] = 15;
        if ( $depth !== false )
        {
            $treeParameters['Depth'] = $depth;
            $treeParameters['DepthOperator'] = $depthOperator;
        }

        $children = null;
        if ( is_numeric( $parentNodeID ) or is_array( $parentNodeID ) )
        {
            $children = eZContentObjectTreeNode::subTreeByNodeID( $treeParameters,
                                                                  $parentNodeID );
        }

        if ( $children === null )
        {
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            return ['result' => $children];
        }
    }

    static public function fetchObjectTreeCount( $parentNodeID, $onlyTranslated, $language, $class_filter_type, $class_filter_array,
                                   $attributeFilter, $depth, $depthOperator,
                                   $ignoreVisibility, $limitation, $mainNodeOnly, $extendedAttributeFilter, $objectNameFilter )
    {
        $childrenCount = null;

        if ( is_numeric( $parentNodeID ) or is_array( $parentNodeID ) )
        {
            $childrenCount = eZContentObjectTreeNode::subTreeCountByNodeID( ['Limitation' => $limitation, 'ClassFilterType' => $class_filter_type, 'ClassFilterArray' => $class_filter_array, 'AttributeFilter' => $attributeFilter, 'DepthOperator' => $depthOperator, 'Depth' => $depth, 'IgnoreVisibility' => $ignoreVisibility, 'OnlyTranslated' => $onlyTranslated, 'Language' => $language, 'ObjectNameFilter' => $objectNameFilter, 'ExtendedAttributeFilter' => $extendedAttributeFilter, 'MainNodeOnly' => $mainNodeOnly],
                                                                     $parentNodeID );
        }

        if ( $childrenCount === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $childrenCount];
        }
        return $result;
    }

    static public function fetchContentSearch( $searchText, $subTreeArray, $offset, $limit, $searchTimestamp, $publishDate, $sectionID,
                                 $classID, $classAttributeID, $ignoreVisibility, $limitation, $sortArray )
    {
        $searchArray = eZSearch::buildSearchArray();
        $parameters = [];
        if ( $classID !== false )
            $parameters['SearchContentClassID'] = $classID;
        if ( $classAttributeID !== false )
            $parameters['SearchContentClassAttributeID'] = $classAttributeID;
        if ( $sectionID !== false )
            $parameters['SearchSectionID'] = $sectionID;
        if ( $publishDate !== false )
            $parameters['SearchDate'] = $publishDate;
        if ( $sortArray !== false )
            $parameters['SortArray'] = $sortArray;
        $parameters['SearchLimit'] = $limit;
        $parameters['SearchOffset'] = $offset;
        $parameters['IgnoreVisibility'] = $ignoreVisibility;
        $parameters['Limitation'] = $limitation;

        if ( $subTreeArray !== false )
            $parameters['SearchSubTreeArray'] = $subTreeArray;
        if ( $searchTimestamp )
            $parameters['SearchTimestamp'] = $searchTimestamp;
        $searchResult = eZSearch::search( $searchText,
                                          $parameters,
                                          $searchArray );
        return ['result' => $searchResult];
    }

    static public function fetchTrashObjectCount( $objectNameFilter, $attributeFilter = false )
    {
        $params = [];
        if ( $objectNameFilter !== false )
        {
            $params['ObjectNameFilter'] = $objectNameFilter;
        }

        $params[ 'AttributeFilter' ] = $attributeFilter;

        $trashCount = eZContentObjectTrashNode::trashListCount( $params );
        return ['result' => $trashCount];
    }

    static public function fetchTrashObjectList( $offset, $limit, $objectNameFilter, $attributeFilter = false, $sortBy = false, $asObject = true )
    {
        $params = [];
        if ( $objectNameFilter !== false )
        {
            $params['ObjectNameFilter'] = $objectNameFilter;
        }
        $params[ 'Limit' ] = $limit;
        $params[ 'Offset' ] = $offset;
        $params[ 'AttributeFilter' ] = $attributeFilter;
        $params[ 'SortBy' ] = $sortBy;
        $params[ 'AsObject' ] = $asObject;

        $trashNodesList = eZContentObjectTrashNode::trashList( $params, false );
        return ['result' => $trashNodesList];
    }

    static public function fetchDraftVersionList( $offset, $limit )
    {
        $userID = eZUser::currentUserID();
        $draftVersionList =  eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                                   null, ['creator_id' => $userID, 'status' => eZContentObjectVersion::STATUS_DRAFT],
                                                                   ['modified' => true, 'initial_language_id' => true],
                                                                   ['length' => $limit, 'offset' => $offset],
                                                                   true );
        return ['result' => $draftVersionList];
    }

    static public function fetchDraftVersionCount()
    {
        $userID = eZUser::currentUserID();
        $draftVersionList = eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                                 [],
                                                                 ['creator_id' => $userID, 'status' => eZContentObjectVersion::STATUS_DRAFT],
                                                                 false,
                                                                 null,
                                                                 false,
                                                                 false,
                                                                 [['operation' => 'count( * )', 'name' => 'count']] );
        return ['result' => $draftVersionList[0]['count']];
    }

    static public function fetchPendingList( $offset, $limit )
    {
        $userID = eZUser::currentUserID();
        $pendingList =  eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                             null, ['creator_id' => $userID, 'status' => eZContentObjectVersion::STATUS_PENDING],
                                                             null, ['length' => $limit, 'offset' => $offset],
                                                             true );
        return ['result' => $pendingList];

    }

    static public function fetchPendingCount()
    {
        $userID = eZUser::currentUserID();
        $pendingList = eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                            [],
                                                            ['creator_id' => $userID, 'status' => eZContentObjectVersion::STATUS_PENDING],
                                                            false,
                                                            null,
                                                            false,
                                                            false,
                                                            [['operation' => 'count( * )', 'name' => 'count']] );
        return ['result' => $pendingList[0]['count']];
    }


    static public function fetchVersionList( $contentObject, $offset, $limit, $sorts = null )
    {
        if ( !is_object( $contentObject ) )
            return ['result' => null];
        $versionList =  eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                              null, ['contentobject_id' => $contentObject->attribute("id")],
                                                                   $sorts, ['length' => $limit, 'offset' => $offset],
                                                                   true );
        return ['result' => $versionList];

    }

    static public function fetchVersionCount( $contentObject )
    {
        if ( !is_object( $contentObject ) )
            return ['result' => 0];
        $versionList = eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                            [],
                                                            ['contentobject_id' => $contentObject->attribute( 'id' )],
                                                            false,
                                                            null,
                                                            false,
                                                            false,
                                                            [['operation' => 'count( * )', 'name' => 'count']] );
        return ['result' => $versionList[0]['count']];
    }

    static public function canInstantiateClassList( $groupID, $parentNode, $filterType, $fetchID, $asObject, $groupByClassGroup = false )
    {
        $ClassGroupIDs = false;

        if ( is_numeric( $groupID ) && ( $groupID > 0 ) )
        {
            $ClassGroupIDs = [$groupID];
        }
        else if( is_array( $groupID ) )
        {
            $ClassGroupIDs = $groupID;
        }

        if ( is_numeric( $parentNode ) )
            $parentNode = eZContentObjectTreeNode::fetch( $parentNode );

        if ( is_object( $parentNode ) )
        {
            $classList = $parentNode->canCreateClassList( $asObject, $filterType == 'include', $ClassGroupIDs, $fetchID );
        }
        else
        {
            $classList = eZContentClass::canInstantiateClassList( $asObject, $filterType == 'include', $ClassGroupIDs, $fetchID );
        }

        if ( $groupByClassGroup && $asObject )
        {
            $tmpClassList = [];

            $ini = eZINI::instance( 'content.ini' );

            foreach ( $classList as $class )
            {
                foreach ( $class->fetchGroupList() as $group )
                {
                    $groupID = $group->attribute( 'group_id' );

                    if ( !in_array( $class->attribute('identifier'), $ini->variable( 'FetchFunctionSettings', 'InstClassListFilter' ) ) )
                    {
                        if ( isset( $tmpClassList[$groupID] ) )
                        {
                            $tmpClassList[$groupID]['items'][] = $class;
                        }
                        else
                        {
                            $tmpClassList[$groupID]['items'] = [$class];
                            $tmpClassList[$groupID]['group_name'] = $group->attribute( 'group_name' );
                        }
                    }
                }
            }

            $classList = $tmpClassList;
        }

        return ['result' => $classList];
    }

    static public function canInstantiateClasses( $parentNode )
    {
        if ( is_object( $parentNode ) )
        {
            $contentObject = $parentNode->attribute( 'object' );
            return ['result' => $contentObject->attribute( 'can_create' )];
        }
        return ['result' => eZContentClass::canInstantiateClasses()];
    }

    static public function contentobjectAttributes( $version, $languageCode )
    {
        if ( $languageCode == '' )
        {
            return ['result' => $version->contentObjectAttributes( )];
        }
        else
        {
            return ['result' => $version->contentObjectAttributes( $languageCode )];
        }
    }

    static public function fetchBookmarks( $offset, $limit )
    {
        $user = eZUser::currentUser();
        return ['result' => eZContentBrowseBookmark::fetchListForUser( $user->id(), $offset, $limit )];
    }

    static public function fetchRecent()
    {
        $user = eZUser::currentUser();
        return ['result' => eZContentBrowseRecent::fetchListForUser( $user->id() )];
    }

    static public function fetchSectionList()
    {
        return ['result' => eZSection::fetchList()];
    }

    static public function fetchTipafriendTopList( $offset, $limit, $start_time, $end_time, $duration, $ascending, $extended )
    {
        $currentTime = time();
        $conds = [];

        if ( is_numeric( $start_time ) and is_numeric( $end_time ) )
        {
            $conds = ['requested' => [false, [$start_time, $end_time]]];
        }
        else if ( is_numeric( $start_time ) and is_numeric( $duration ) )
        {
            $conds = ['requested' => [false, [$start_time, $start_time + $duration]]];
        }
        else if ( is_numeric( $end_time ) and is_numeric( $duration ) )
        {
            $conds = ['requested' => [false, [$end_time - $duration, $end_time]]];
        }
        else if ( is_numeric( $start_time ) )
        {
            $conds = ['requested' => ['>', $start_time]];
        }
        else if ( is_numeric( $end_time ) )
        {
            $conds = ['requested' => ['<', $end_time]];
        }
        else if ( is_numeric( $duration ) )
        {
            // substract passed duration from current time timestamp to get start_time stamp
            // end_timestamp is equal to current time in this case
            $conds = ['requested' => ['>', $currentTime - $duration]];
        }

        $topList = eZPersistentObject::fetchObjectList( eZTipafriendCounter::definition(),
                                                        ['node_id'],
                                                        $conds,
                                                        ['count' => ( $ascending ? 'asc' : 'desc' )],
                                                        ['length' => $limit, 'offset' => $offset],
                                                        false,
                                                        ['node_id'],
                                                        [['operation' => 'count( * )', 'name' => 'count']] );
        if ( $extended )
        {
            foreach ( array_keys( $topList ) as $key )
            {
                $contentNode = eZContentObjectTreeNode::fetch( $topList[ $key ][ 'node_id' ] );
                if ( !is_object( $contentNode ) )
                    return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
                $topList[ $key ][ 'node' ] = $contentNode;
            }
            return ['result' => $topList];
        }
        else
        {
            $retList = [];
            foreach ( $topList as $entry )
            {
                $contentNode = eZContentObjectTreeNode::fetch( $entry[ 'node_id' ] );
                if ( !is_object( $contentNode ) )
                    return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
                $retList[] = $contentNode;
            }
            return ['result' => $retList];
        }

    }

    static public function fetchMostViewedTopList( $classID, $sectionID, $offset, $limit )
    {
        $topList = eZViewCounter::fetchTopList( $classID, $sectionID, $offset, $limit );
        $contentNodeList = [];
        foreach ( array_keys ( $topList ) as $key )
        {
            $nodeID = $topList[$key]['node_id'];
            $contentNode = eZContentObjectTreeNode::fetch( $nodeID );
            if ( $contentNode === null )
                return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
            $contentNodeList[] = $contentNode;
        }
        return ['result' => $contentNodeList];
    }

    static public function fetchCollectedInfoCount( $objectAttributeID, $objectID, $value, $creatorID = false, $userIdentifier = false )
    {
        return eZInfocollectorFunctionCollection::fetchCollectedInfoCount( $objectAttributeID, $objectID, $value, $creatorID, $userIdentifier );
    }

    static public function fetchCollectedInfoCountList( $objectAttributeID )
    {
        return eZInfocollectorFunctionCollection::fetchCollectedInfoCountList( $objectAttributeID );
    }

    static public function fetchCollectedInfoCollection( $collectionID, $contentObjectID )
    {
        return eZInfocollectorFunctionCollection::fetchCollectedInfoCollection( $collectionID, $contentObjectID );
    }

    static public function fetchCollectionsList( $objectID = false, $creatorID = false, $userIdentifier = false, $limit = false, $offset = false, $sortBy = false )
    {
        return eZInfocollectorFunctionCollection::fetchCollectionsList( $objectID,
                                                                        $creatorID,
                                                                        $userIdentifier,
                                                                        $limit,
                                                                        $offset,
                                                                        $sortBy );
     }

    static public function fetchObjectByAttribute( $identifier )
    {
        $contentObjectAttribute = eZContentObjectAttribute::fetchByIdentifier( $identifier );
        if ( $contentObjectAttribute === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $contentObjectAttribute->attribute( 'object' )];
        }
        return $result;
    }

    static public function fetchObjectCountByUserID( $classID, $userID, $status = false )
    {
        $objectCount = eZContentObject::fetchObjectCountByUserID( $classID, $userID, $status );
        return ['result' => $objectCount];
    }

    static public function fetchKeywordCount( $alphabet,
                                $classid,
                                $owner = false,
                                $parentNodeID = false,
                                $includeDuplicates = true,
                                $strictMatching = false,
                                $depth = 1 )
    {
        $classIDArray = [];
        if ( is_numeric( $classid ) )
        {
            $classIDArray = [$classid];
        }
        else if ( is_array( $classid ) )
        {
            $classIDArray = $classid;
        }

        $showInvisibleNodesCond = eZContentObjectTreeNode::createShowInvisibleSQLString( true, false );
        $limitation = false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $sqlPermissionChecking = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList );

        $db = eZDB::instance();

        $alphabet = $db->escapeString( $alphabet );

        $sqlOwnerString = is_numeric( $owner ) ? "AND ezcontentobject.owner_id = '$owner'" : '';
        $parentNodeIDString = '';
        if ( is_numeric( $parentNodeID ) )
        {
            $notEqParentString  = '';
            // If the node(s) doesn't exist we return null.
            if ( !eZContentObjectTreeNode::createPathConditionAndNotEqParentSQLStrings( $parentNodeIDString, $notEqParentString, $parentNodeID, $depth ) )
            {
                return null;
            }
        }

        $sqlClassIDs = '';
        if ( $classIDArray != null )
        {
            $sqlClassIDs = 'AND ' . $db->generateSQLINStatement( $classIDArray, 'ezkeyword.class_id', false, false, 'int' ) . ' ';
        }

        $sqlToExcludeDuplicates = '';
        if ( !$includeDuplicates )
        {
          //will use SELECT COUNT( DISTINCT ezcontentobject.id ) to count object only once even if it has
          //several keywords started with $alphabet.
          //COUNT( DISTINCT fieldName ) is SQL92 compliant syntax.
            $sqlToExcludeDuplicates = ' DISTINCT';
        }
        // composing sql for matching tag word, it could be strict equiality or LIKE clause dependent of $strictMatching parameter.
        $sqlMatching = "ezkeyword.keyword LIKE '$alphabet%'";
        if ( $strictMatching )
        {
            $sqlMatching = "ezkeyword.keyword = '$alphabet'";
        }

        $query = "SELECT COUNT($sqlToExcludeDuplicates ezcontentobject.id) AS count
                  FROM ezkeyword
                      INNER JOIN ezkeyword_attribute_link ON (ezkeyword_attribute_link.keyword_id = ezkeyword.id)
                      INNER JOIN ezcontentobject_attribute ON (ezcontentobject_attribute.id = ezkeyword_attribute_link.objectattribute_id)
                      INNER JOIN ezcontentobject ON (ezcontentobject.id = ezcontentobject_attribute.contentobject_id AND ezcontentobject.current_version = ezcontentobject_attribute.version)
                      INNER JOIN ezcontentobject_tree ON (ezcontentobject_tree.contentobject_id = ezcontentobject.id)
                      INNER JOIN ezcontentclass ON (ezcontentclass.id = ezcontentobject.contentclass_id)
                       $sqlPermissionChecking[from]
                  WHERE
                  $parentNodeIDString
                  $sqlMatching
                  $showInvisibleNodesCond
                  $sqlPermissionChecking[where]
                  $sqlClassIDs
                  $sqlOwnerString
                  AND ezcontentclass.version = 0
                  AND ezcontentobject.status = " . eZContentObject::STATUS_PUBLISHED . "
                  AND ezcontentobject_tree.main_node_id = ezcontentobject_tree.node_id";

        $keyWords = $db->arrayQuery( $query );
        // cleanup temp tables
        $db->dropTempTableList( $sqlPermissionChecking['temp_tables'] );

        return ['result' => $keyWords[0]['count']];
    }

    //
    //Returns an array( 'result' => array( 'keyword' => keyword, 'link_object' => node_id );
    //By default fetchKeyword gets a list of (not necessary unique) nodes and respective keyword strings
    //Search keyword provided in $alphabet parameter.
    //By default keyword matching implemented by LIKE so all keywords that starts with $alphabet
    //will successfully match. This means that if some object have attached keywords:
    //'Skien', 'Skien forests', 'Skien comunity' than fetchKeyword('Skien') will return tree entries
    //for this object.
    //Setting $includeDuplicates parameter to false makes fetchKeyword('Skien') to return just
    //one entry for such objects.
    static public function fetchKeyword( $alphabet,
                           $classid,
                           $offset,
                           $limit,
                           $owner = false,
                           $sortBy = [],
                           $parentNodeID = false,
                           $includeDuplicates = true,
                           $strictMatching = false,
                           $depth = 1 )
    {
        $classIDArray = [];
        if ( is_numeric( $classid ) )
        {
            $classIDArray = [$classid];
        }
        else if ( is_array( $classid ) )
        {
            $classIDArray = $classid;
        }

        $showInvisibleNodesCond = eZContentObjectTreeNode::createShowInvisibleSQLString( true, false );
        $limitation = false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $sqlPermissionChecking = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList );

        $db_params = [];
        $db_params['offset'] = $offset;
        $db_params['limit'] = $limit;

        $keywordNodeArray = [];
        $lastKeyword = '';

        $db = eZDB::instance();

        //in SELECT clause below we will use a full keyword value
        //or just a part of ezkeyword.keyword matched to $alphabet respective to $includeDuplicates parameter.
        //In the case $includeDuplicates = ture we need only a part
        //of ezkeyword.keyword to be fetched in field to allow DISTINCT to remove rows with the same node id's
        $sqlKeyword = 'ezkeyword.keyword';
        if ( !$includeDuplicates )
        {
            $sqlKeyword = $db->subString('ezkeyword.keyword', 1, strlen( (string) $alphabet ) ) . ' AS keyword ';
        }

        $alphabet = $db->escapeString( $alphabet );

        $sortingInfo = [];
        $sortingInfo['attributeFromSQL'] = '';
        $sqlTarget = $sqlKeyword.',ezcontentobject_tree.node_id';

        if ( is_array( $sortBy ) && count ( $sortBy ) > 0 )
        {
            switch ( $sortBy[0] )
            {
                case 'keyword':
                case 'name':
                    $sortingString = '';
                    if ( $sortBy[0] == 'name' )
                    {
                        $sortingString = 'ezcontentobject.name';
                    }
                    elseif ( $sortBy[0] == 'keyword' )
                    {
                        if ( $includeDuplicates )
                            $sortingString = 'ezkeyword.keyword';
                        else
                            $sortingString = 'keyword';
                    }

                    $sortOrder = true; // true is ascending
                    if ( isset( $sortBy[1] ) )
                        $sortOrder = $sortBy[1];
                    $sortingOrder = $sortOrder ? ' ASC' : ' DESC';
                    $sortingInfo['sortingFields'] = $sortingString . $sortingOrder;
                    break;
                default:
                    $sortingInfo = eZContentObjectTreeNode::createSortingSQLStrings( $sortBy );
            }

            // Fixing the attributeTargetSQL
            switch ( $sortBy[0] )
            {
                case 'keyword':
                    $sortingInfo['attributeTargetSQL'] = '';
                    break;
                case 'name':
                    $sortingInfo['attributeTargetSQL'] = ', ezcontentobject.name';
                    break;
                case 'attribute':
                case 'class_name':
                    break;
                default:
                    $sortingInfo['attributeTargetSQL'] .= ', ' . strtok( $sortingInfo["sortingFields"], " " );
            }

            $sqlTarget .= $sortingInfo['attributeTargetSQL'];
        }
        else
        {
            $sortingInfo['sortingFields'] = 'ezkeyword.keyword ASC';
        }

        //Adding DISTINCT to avoid duplicates,
        //check if DISTINCT keyword was added before providing clauses for sorting.
        if ( !$includeDuplicates && !str_starts_with($sqlTarget, 'DISTINCT ') )
        {
            $sqlTarget = 'DISTINCT ' . $sqlTarget;
        }

        $sqlOwnerString = is_numeric( $owner ) ? "AND ezcontentobject.owner_id = '$owner'" : '';
        $parentNodeIDString = '';
        if ( is_numeric( $parentNodeID ) )
        {
            $notEqParentString  = '';
            // If the node(s) doesn't exist we return null.
            if ( !eZContentObjectTreeNode::createPathConditionAndNotEqParentSQLStrings( $parentNodeIDString, $notEqParentString, $parentNodeID, $depth ) )
            {
                return null;
            }
        }

        $sqlClassIDString = '';
        if ( is_array( $classIDArray ) and count( $classIDArray ) )
        {
            $sqlClassIDString = 'AND ' . $db->generateSQLINStatement( $classIDArray, 'ezkeyword.class_id', false, false, 'int' ) . ' ';
        }

        // composing sql for matching tag word, it could be strict equiality or LIKE clause
        // dependent of $strictMatching parameter.
        $sqlMatching = "ezkeyword.keyword LIKE '$alphabet%'";
        if ( $strictMatching )
        {
            $sqlMatching = "ezkeyword.keyword = '$alphabet'";
        }

        $query = "SELECT $sqlTarget
                  FROM ezkeyword
                       INNER JOIN ezkeyword_attribute_link ON (ezkeyword_attribute_link.keyword_id = ezkeyword.id)
                       INNER JOIN ezcontentobject_attribute ON (ezcontentobject_attribute.id = ezkeyword_attribute_link.objectattribute_id)
                       INNER JOIN ezcontentobject ON (ezcontentobject_attribute.version = ezcontentobject.current_version AND ezcontentobject_attribute.contentobject_id = ezcontentobject.id)
                       INNER JOIN ezcontentobject_tree ON (ezcontentobject_tree.contentobject_id = ezcontentobject.id)
                       INNER JOIN ezcontentclass ON (ezcontentclass.id = ezcontentobject.contentclass_id)
                       $sortingInfo[attributeFromSQL]
                       $sqlPermissionChecking[from]
                  WHERE
                  $parentNodeIDString
                  $sqlMatching
                  $showInvisibleNodesCond
                  $sqlPermissionChecking[where]
                  $sqlClassIDString
                  $sqlOwnerString
                  AND ezcontentclass.version = 0
                  AND ezcontentobject.status = ".eZContentObject::STATUS_PUBLISHED."
                  AND ezcontentobject_tree.main_node_id = ezcontentobject_tree.node_id
                  ORDER BY {$sortingInfo['sortingFields']}";

        $keyWords = $db->arrayQuery( $query, $db_params );

        $trans = eZCharTransform::instance();

        foreach ( $keyWords as $keywordArray )
        {
            $keyword = $keywordArray['keyword'];
            $nodeID = $keywordArray['node_id'];
            $nodeObject = eZContentObjectTreeNode::fetch( $nodeID );

            if ( $nodeObject != null )
            {
                $keywordLC = $trans->transformByGroup( $keyword, 'lowercase' );
                if ( $lastKeyword == $keywordLC )
                    $keywordNodeArray[] = ['keyword' => '', 'link_object' => $nodeObject];
                else
                    $keywordNodeArray[] = ['keyword' => $keyword, 'link_object' => $nodeObject];

                $lastKeyword = $keywordLC;
            }
            else
            {
                $lastKeyword = $trans->transformByGroup( $keyword, 'lowercase' );
            }
        }
        return ['result' => $keywordNodeArray];
    }

    static public function fetchSameClassAttributeNodeList( $contentclassattributeID, $value, $datatype )
    {
        if ( $datatype == "int" )
             $type = "data_int";
        else if ( $datatype == "float" )
             $type = "data_float";
        else if ( $datatype == "text" )
             $type = "data_text";
        else
        {
            eZDebug::writeError( "DatatypeString not supported in fetch same_classattribute_node, use int, float or text" );
            return false;
        }
        $db = eZDB::instance();
        $contentclassattributeID =(int) $contentclassattributeID;
        $value = $db->escapeString( $value );
        if ( $datatype != "text" )
            settype( $value, $datatype );
        $resultNodeArray = [];
        $nodeList = $db->arrayQuery( "SELECT ezcontentobject_tree.node_id, ezcontentobject.name, ezcontentobject_tree.parent_node_id
                                            FROM ezcontentobject_tree, ezcontentobject, ezcontentobject_attribute
                                           WHERE ezcontentobject_attribute.$type='$value'
                                             AND ezcontentobject_attribute.contentclassattribute_id='$contentclassattributeID'
                                             AND ezcontentobject_attribute.contentobject_id=ezcontentobject.id
                                             AND ezcontentobject_attribute.version=ezcontentobject.current_version
                                             AND ezcontentobject_tree.contentobject_version=ezcontentobject.current_version
                                             AND ezcontentobject_tree.contentobject_id=ezcontentobject.id
                                        ORDER BY ezcontentobject.name");

        foreach ( $nodeList as $nodeObject )
        {
            $nodeID = $nodeObject['node_id'];
            $node = eZContentObjectTreeNode::fetch( $nodeID );
            $resultNodeArray[] = $node;
        }
        return ['result' => $resultNodeArray];
    }

    static public function checkAccess( $access, $contentObject, $contentClassID, $parentContentClassID, $languageCode = false )
    {
        if ( $contentObject instanceof eZContentObjectTreeNode )
        {
            $contentObject = $contentObject->attribute( 'object' );
        }
        if (  $contentClassID !== false and !is_numeric( $contentClassID ) )
        {
            $class = eZContentClass::fetchByIdentifier( $contentClassID );
            if ( !$class )
                return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
            $contentClassID = $class->attribute( 'id' );
        }
        if ( $access and $contentObject instanceof eZContentObject )
        {
            $result = $contentObject->checkAccess( $access, $contentClassID, $parentContentClassID, false, $languageCode );
            return ['result' => $result];
        }
    }

    // Fetches all navigation parts as an array
    static public function fetchNavigationParts()
    {
        return ['result' => eZNavigationPart::fetchList()];
    }

    // Fetches one navigation parts by identifier
    static public function fetchNavigationPart( $identifier )
    {
        return ['result' => eZNavigationPart::fetchPartByIdentifier( $identifier )];
    }

    static public function contentobjectRelationTypeMask( $contentObjectRelationTypes = false )
    {
        $relationTypeMask = 0;
        if ( is_array( $contentObjectRelationTypes ) )
        {
            $relationTypeMap = ['common'    => eZContentObject::RELATION_COMMON, 'xml_embed' => eZContentObject::RELATION_EMBED, 'xml_link'  => eZContentObject::RELATION_LINK, 'attribute' => eZContentObject::RELATION_ATTRIBUTE];
            foreach ( $contentObjectRelationTypes as $relationType )
            {
                if ( isset( $relationTypeMap[$relationType] ) )
                {
                    $relationTypeMask |= $relationTypeMap[$relationType];
                }
                else
                {
                    eZDebug::writeWarning( "Unknown relation type: '$relationType'.", __METHOD__ );
                }
            }
        }
        elseif ( !is_bool( $contentObjectRelationTypes ) )
        {
            $contentObjectRelationTypes = false;
        }

        if ( is_bool( $contentObjectRelationTypes ) )
        {
            $relationTypeMask = eZContentObject::relationTypeMask( $contentObjectRelationTypes );
        }

        return $relationTypeMask;
    }

    // Fetches related objects id grouped by relation types
    static public function fetchRelatedObjectsID( $objectID, $attributeID, $allRelations)
    {
        if ( !is_array( $allRelations ) || $allRelations === [] )
        {
            $allRelations = ['common', 'xml_embed', 'attribute'];
            if ( eZContentObject::isObjectRelationTyped() )
            {
                $allRelations[] = 'xml_link';
            }
        }

        $relatedObjectsTyped = [];
        foreach ( $allRelations as $relationType )
        {
            $relatedObjectsTyped[$relationType] =
                eZContentFunctionCollection::fetchRelatedObjects( $objectID, $attributeID, [$relationType], false, [] );
        }

        $relatedObjectsTypedIDArray = [];
        foreach ( $relatedObjectsTyped as $relationTypeName => $relatedObjectsByType )
        {
            $relatedObjectsTypedIDArray[$relationTypeName] = [];
            foreach ( $relatedObjectsByType['result'] as $relatedObjectByType )
            {
                $relatedObjectsTypedIDArray[$relationTypeName][] = $relatedObjectByType->ID;
            }
        }

        return ['result' => $relatedObjectsTypedIDArray];
    }

    // Fetches reverse related objects id grouped by relation types
    static public function fetchReverseRelatedObjectsID( $objectID, $attributeID, $allRelations )
    {
        if ( !is_array( $allRelations ) || $allRelations === [] )
        {
            $allRelations = ['common', 'xml_embed', 'attribute'];
            if ( eZContentObject::isObjectRelationTyped() )
            {
                $allRelations[] = 'xml_link';
            }
        }

        $relatedObjectsTyped = [];
        foreach ( $allRelations as $relationType )
        {
            $relatedObjectsTyped[$relationType] =
                eZContentFunctionCollection::fetchReverseRelatedObjects( $objectID, $attributeID, [$relationType], false, [], null );
        }

        $relatedObjectsTypedIDArray = [];
        foreach ( $relatedObjectsTyped as $relationTypeName => $relatedObjectsByType )
        {
            $relatedObjectsTypedIDArray[$relationTypeName] = [];
            foreach ( $relatedObjectsByType['result'] as $relatedObjectByType )
            {
                $relatedObjectsTypedIDArray[$relationTypeName][] = $relatedObjectByType->ID;
            }
        }

        return ['result' =>$relatedObjectsTypedIDArray];
    }


    /**
     * Fetches related object for $objectID
     *
     * @param int $objectID
     * @param int $attributeID Relation attribute id
     * @param int $allRelations Accepted elation bitmask
     * @param array $sortBy
     * @param int $limit
     * @param int $offset
     * @param bool $asObject
     * @param bool $loadDataMap
     * @param bool $ignoreVisibility
     * @param array $relatedClassIdentifiers Array of related class identifiers that will be accepted
     * @return array ANn array of eZContentObject
     */
    static public function fetchRelatedObjects( $objectID, $attributeID, $allRelations, mixed $groupByAttribute, $sortBy, $limit = false, $offset = false, $asObject = true, $loadDataMap = false, $ignoreVisibility = null, array $relatedClassIdentifiers = null )
    {
        if ( !is_numeric( $objectID ) )
        {
            eZDebug::writeError( "ObjectID is missing or invalid", __METHOD__ );
            return false;
        }

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            eZDebug::writeError( "An error occured fetching object #$objectID", __METHOD__ );
            return false;
        }

        $params = [];
        $params['Limit'] = $limit;
        $params['Offset'] = $offset;
        $params['AsObject'] = $asObject;
        $params['LoadDataMap'] = $loadDataMap;

        if ( $sortBy )
        {
            if ( is_array( $sortBy ) )
            {
                $params['SortBy'] = $sortBy;
            }
            else
            {
                eZDebug::writeError( "Function parameter 'SortBy' should be an array.", 'content/fetchRelatedObjects' );
            }
        }

        if ( $ignoreVisibility !== null )
        {
            $params['IgnoreVisibility'] = $ignoreVisibility;
        }

        if ( !$attributeID )
        {
            $attributeID = 0;
        }

        if ( isset( $allRelations ) )
        {
            if ( $attributeID && !$allRelations )
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( ['attribute'] );
            }
            elseif( $allRelations === true )
            {
                $attributeID = false;
            }
            else
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( $allRelations );
            }
        }

        if ( $relatedClassIdentifiers !== null )
        {
            $params['RelatedClassIdentifiers'] = $relatedClassIdentifiers;
        }

        if ( $attributeID && !is_numeric( $attributeID ) && !is_bool( $attributeID ) )
        {
            $attributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $attributeID );
            if ( !$attributeID )
            {
                eZDebug::writeError( "Can't get class attribute ID by identifier" );
                return false;
            }
        }

        return ['result' => $object->relatedContentObjectList( false, $objectID, $attributeID, $groupByAttribute, $params )];
    }

        // Fetches count of reverse related objects
    static public function fetchRelatedObjectsCount( $objectID, $attributeID, $allRelations )
    {
        if ( !is_numeric( $objectID ) )
        {
            eZDebug::writeError( "ObjectID is missing", __METHOD__ );
            return false;
        }

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            eZDebug::writeError( "An error occured fetching object #$objectID", __METHOD__ );
            return false;
        }

        $params=[];
        if ( !$attributeID )
        {
            $attributeID = 0;
        }

        if ( isset( $allRelations ) )
        {
            if ( $attributeID && !$allRelations )
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( ['attribute'] );
            }
            elseif( $allRelations === true )
            {
                $attributeID = false;
            }
            else
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( $allRelations );
            }
        }

        if ( $attributeID && !is_numeric( $attributeID ) && !is_bool( $attributeID ) )
        {
            $attributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $attributeID );
            if ( !$attributeID )
            {
                eZDebug::writeError( "Can't get class attribute ID by identifier" );
                return false;
            }
        }

        return ['result' => $object->relatedContentObjectCount( false, $attributeID, $params )];
    }

    static public function fetchReverseRelatedObjects( $objectID, $attributeID, $allRelations, $groupByAttribute, $sortBy, $ignoreVisibility,  $limit = false, $offset = false, $asObject = true, $loadDataMap = false  )
    {
        if ( !$objectID or !is_numeric( $objectID ) )
        {
            eZDebug::writeDebug( "Missing or incorrect \$objectID parameter", __METHOD__ );
            return false;
        }

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            eZDebug::writeError( "An error occured fetching object #$objectID", __METHOD__ );
            return false;
        }

        $params = [];
        $params['Limit'] = $limit;
        $params['Offset'] = $offset;
        $params['AsObject'] = $asObject;
        $params['LoadDataMap'] = $loadDataMap;

        if ( $sortBy )
        {
            if ( is_array( $sortBy ) )
            {
                $params['SortBy'] = $sortBy;
            }
            else
            {
                eZDebug::writeError( "Function parameter 'SortBy' should be an array.", 'content/fetchReverseRelatedObjects' );
            }
        }
        if ( isset( $ignoreVisibility ) )
        {
            $params['IgnoreVisibility'] = $ignoreVisibility;
        }
        if ( !$attributeID )
        {
            $attributeID = 0;
        }

        if ( isset( $allRelations ) )
        {
            if ( $attributeID && !$allRelations )
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( ['attribute'] );
            }
            elseif( $allRelations === true )
            {
                $attributeID = false;
            }
            else
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( $allRelations );
            }
        }

        if ( $attributeID && !is_numeric( $attributeID ) && !is_bool( $attributeID ) )
        {
            $attributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $attributeID );
            if ( !$attributeID )
            {
                eZDebug::writeError( "Can't get class attribute ID by identifier" );
                return false;
            }
        }
        return ['result' => $object->reverseRelatedObjectList( false, $attributeID, $groupByAttribute, $params )];
    }

    // Fetches count of reverse related objects
    static public function fetchReverseRelatedObjectsCount( $objectID, $attributeID, $allRelations, $ignoreVisibility  )
    {
        if ( !is_numeric( $objectID ) )
        {
            eZDebug::writeError( "\$objectID is missing or invalid", __METHOD__ );
            return false;
        }

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            eZDebug::writeError( "An error occured fetching object #$objectID", __METHOD__ );
            return false;
        }

        $params = [];
        if ( isset( $ignoreVisibility ) )
        {
            $params['IgnoreVisibility'] = $ignoreVisibility;
        }

        if ( !$attributeID )
        {
            $attributeID = 0;
        }

        if ( isset( $allRelations ) )
        {
            if ( $attributeID && !$allRelations )
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( ['attribute'] );
            }
            elseif( $allRelations === true )
            {
                $attributeID = false;
            }
            else
            {
                $params['AllRelations'] = eZContentFunctionCollection::contentobjectRelationTypeMask( $allRelations );
            }
        }

        if ( $attributeID && !is_numeric( $attributeID ) && !is_bool( $attributeID ) )
        {
            $attributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $attributeID );
            if ( !$attributeID )
            {
                eZDebug::writeError( "Can't get class attribute ID by identifier" );
                return false;
            }
        }
        return ['result' => $object->reverseRelatedObjectCount( false, $attributeID, $params )];
    }

    static public function fetchAvailableSortFieldList()
    {
        return ['result' => ['6' => ezpI18n::tr( 'kernel/content', 'Class identifier' ), '7' => ezpI18n::tr( 'kernel/content', 'Class name' ), '5' => ezpI18n::tr( 'kernel/content', 'Depth' ), '3' => ezpI18n::tr( 'kernel/content', 'Modified' ), '9' => ezpI18n::tr( 'kernel/content', 'Name' ), '1' => ezpI18n::tr( 'kernel/content', 'Path String' ), '8' => ezpI18n::tr( 'kernel/content', 'Priority' ), '2' => ezpI18n::tr( 'kernel/content', 'Published' ), '4' => ezpI18n::tr( 'kernel/content', 'Section' )]];
    }

    static public function fetchCountryList( $filter, $value )
    {
        // Fetch country list
        if ( !$filter and !$value )
        {
            $country = eZCountryType::fetchCountryList();
        }
        else
        {
            $country = eZCountryType::fetchCountry( $value, $filter );
        }

        return ['result' => $country];
    }

    static public function fetchContentTreeMenuExpiry()
    {
        $expiryHandler = eZExpiryHandler::instance();

        if ( !$expiryHandler->hasTimestamp( 'content-tree-menu' ) )
        {
            $expiryHandler->setTimestamp( 'content-tree-menu', time() );
            $expiryHandler->store();
        }

        return ['result' => $expiryHandler->timestamp( 'content-tree-menu' )];
    }
}

?>
