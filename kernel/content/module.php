<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZContentObject', 'variable_params' => true];

$ViewList = [];
$ViewList['edit'] = ['functions' => ['edit or create'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'edit', 'single_post_actions' => ['PreviewButton' => 'Preview', 'TranslateButton' => 'Translate', 'VersionsButton' => 'VersionEdit', 'PublishButton' => 'Publish', 'DiscardButton' => 'Discard', 'BrowseNodeButton' => 'BrowseForNodes', 'RemoveAssignmentButton' => 'RemoveAssignments', 'EditLanguageButton' => 'EditLanguage', 'FromLanguageButton' => 'FromLanguage', 'TranslateLanguageButton' => 'TranslateLanguage', 'BrowseObjectButton' => 'BrowseForObjects', 'UploadFileRelationButton' => 'UploadFileRelation', 'NewButton' => 'NewObject', 'DeleteRelationButton' => 'DeleteRelation', 'StoreButton' => 'Store', 'StoreExitButton' => 'StoreExit', 'MoveNodeID' => 'MoveNode', 'RemoveNodeID' => 'DeleteNode', 'ConfirmButton' => 'ConfirmAssignmentDelete', 'SectionEditButton' => 'SectionEdit', 'StateEditButton' => 'StateEdit'], 'post_action_parameters' => ['EditLanguage' => ['SelectedLanguage' => 'EditSelectedLanguage'], 'FromLanguage' => ['FromLanguage' => 'FromLanguage'], 'TranslateLanguage' => ['SelectedLanguage' => 'EditSelectedLanguage'], 'UploadFileRelation' => ['UploadRelationLocation' => 'UploadRelationLocationChoice'], 'SectionEdit' => ['RedirectRelativeURI' => 'RedirectRelativeURI']], 'post_actions' => ['BrowseActionName'], 'script' => 'edit.php', 'params' => ['ObjectID', 'EditVersion', 'EditLanguage', 'FromLanguage']];

$ViewList['removenode'] = ['functions' => ['edit'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'edit', 'single_post_actions' => ['ConfirmButton' => 'ConfirmAssignmentRemove', 'CancelButton' => 'CancelAssignmentRemove'], 'script' => 'removenode.php', 'params' => ['ObjectID', 'EditVersion', 'EditLanguage', 'NodeID']];

$ViewList['removeassignment'] = ['functions' => ['edit'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'edit', 'ui_component' => 'content', 'single_post_actions' => ['ConfirmRemovalButton' => 'ConfirmRemoval', 'CancelRemovalButton' => 'CancelRemoval'], 'script' => 'removeassignment.php', 'params' => []];

$ViewList['pdf'] = ['functions' => ['pdf'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'pdf.php', 'params' => ['NodeID'], 'unordered_params' => ['language' => 'Language', 'offset' => 'Offset', 'year' => 'Year', 'month' => 'Month', 'day' => 'Day']];

$ViewList['view'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'view.php', 'params' => ['ViewMode', 'NodeID'], 'unordered_params' => ['language' => 'Language', 'offset' => 'Offset', 'year' => 'Year', 'month' => 'Month', 'day' => 'Day']];

$ViewList['copy'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'copy.php', 'ui_context' => 'edit', 'single_post_actions' => ['CopyButton' => 'Copy', 'CancelButton' => 'Cancel'], 'post_action_parameters' => ['Copy' => ['VersionChoice' => 'VersionChoice']], 'post_actions' => ['BrowseActionName'], 'params' => ['ObjectID']];

$ViewList['copysubtree'] = ['functions' => ['create'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'copysubtree.php', 'ui_context' => 'administration', 'single_post_actions' => ['CopyButton' => 'Copy', 'CancelButton' => 'Cancel'], 'post_action_parameters' => ['Copy' => ['VersionChoice' => 'VersionChoice', 'CreatorChoice' => 'CreatorChoice', 'TimeChoice' => 'TimeChoice']], 'post_actions' => ['BrowseActionName'], 'params' => ['NodeID']];

$ViewList['versionview'] = ['functions' => ['versionread'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'edit', 'script' => 'versionview.php', 'single_post_actions' => ['ChangeSettingsButton' => 'ChangeSettings', 'EditButton' => 'Edit', 'VersionsButton' => 'Versions', 'PreviewPublishButton' => 'Publish'], 'post_action_parameters' => ['ChangeSettings' => ['Language' => 'SelectedLanguage', 'PlacementID' => 'SelectedPlacement', 'SiteAccess' => 'SelectedSiteAccess']], 'params' => ['ObjectID', 'EditVersion', 'LanguageCode', 'FromLanguage'], 'unordered_params' => ['language' => 'Language', 'offset' => 'Offset', 'site_access' => 'SiteAccess']];

$ViewList['restore'] = ['functions' => ['restore'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'administration', 'script' => 'restore.php', 'single_post_actions' => ['ConfirmButton' => 'Confirm', 'CancelButton' => 'Cancel', 'AddLocationAction' => 'AddLocation'], 'post_action_parameters' => ['Confirm' => ['RestoreType' => 'RestoreType']], 'params' => ['ObjectID']];

$ViewList['search'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'search.php', 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['urlalias'] = ['functions' => ['edit'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'urlalias.php', 'ui_context' => 'administration', 'single_post_actions' => ['NewAliasButton' => 'NewAlias', 'RemoveAllAliasesButton' => 'RemoveAllAliases', 'RemoveAliasButton' => 'RemoveAlias'], 'post_action_parameters' => ['RemoveAlias' => ['ElementList' => 'ElementList'], 'NewAlias' => ['LanguageCode' => 'LanguageCode', 'AliasText' => 'AliasText']], 'params' => ['NodeID'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['urltranslator'] = [
    'functions' => ['urltranslator'],
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'urlalias_global.php',
    'ui_context' => 'administration',
    'single_post_actions' => ['NewAliasButton' => 'NewAlias', 'RemoveAllAliasesButton' => 'RemoveAllAliases', 'RemoveAliasButton' => 'RemoveAlias'],
    'post_action_parameters' => ['RemoveAlias' => ['ElementList' => 'ElementList'], 'NewAlias' => ['LanguageCode' => 'LanguageCode', 'AliasSourceText' => 'AliasSourceText', 'AliasDestinationText' => 'AliasDestinationText']],
    /*    'single_post_actions' => array( 'NewURLAliasButton' => 'NewURLAlias',
          'NewForwardURLAliasButton' => 'NewForwardURLAlias',
          'NewWildcardURLAliasButton' => 'NewWildcardURLAlias',
          'RemoveURLAliasButton' => 'RemoveURLAlias',
          'StoreURLAliasButton' => 'StoreURLAlias' ),*/
    'params' => [],
    'unordered_params' => ['offset' => 'Offset'],
];

$ViewList['urlwildcards'] = ['functions' => ['urltranslator'], 'default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'urlalias_wildcard.php', 'ui_context' => 'administration', 'single_post_actions' => ['NewWildcardButton' => 'NewWildcard', 'RemoveAllWildcardsButton' => 'RemoveAllWildcards', 'RemoveWildcardButton' => 'RemoveWildcard'], 'post_action_parameters' => ['RemoveWildcard' => ['WildcardIDList' => 'WildcardIDList', 'Offset' => 'Offset'], 'NewWildcard' => ['WildcardType' => 'WildcardType', 'WildcardSourceText' => 'WildcardSourceText', 'WildcardDestinationText' => 'WildcardDestinationText', 'Offset' => 'Offset']], 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['advancedsearch'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'advancedsearch.php', 'params' => ['ViewMode'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['browse'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'browse', 'script' => 'browse.php', 'params' => ['NodeID', 'ObjectID', 'EditVersion'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['upload'] = ['functions' => ['create'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'upload.php', 'single_post_actions' => ['UploadFileButton' => 'UploadFile', 'CancelUploadButton' => 'CancelUpload'], 'post_action_parameters' => ['UploadFile' => ['UploadLocation' => 'UploadLocationChoice', 'ObjectName' => 'ObjectName']], 'params' => []];

$ViewList['removeobject'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'removeobject.php', 'params' => []];

$ViewList['removeuserobject'] = ['functions' => ['read'], 'default_navigation_part' => 'ezusernavigationpart', 'script' => 'removeobject.php', 'params' => []];

$ViewList['removemediaobject'] = ['functions' => ['read'], 'default_navigation_part' => 'ezmedianavigationpart', 'script' => 'removeobject.php', 'params' => []];

$ViewList['removeeditversion'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'removeeditversion.php', 'ui_context' => 'edit', 'params' => []];

$ViewList['download'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'download.php', 'params' => ['ContentObjectID', 'ContentObjectAttributeID'], 'unordered_params' => ['version' => 'Version']];

$ViewList['action'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'action.php', 'ui_context' => 'edit', 'params' => [], 'single_post_actions' => ['RemoveAssignmentButton' => 'RemoveAssignment', 'AddAssignmentButton' => 'SelectAssignmentLocation', 'AddAssignmentAction' => 'AddAssignment', 'UpdateMainAssignmentButton' => 'UpdateMainAssignment', 'ClearViewCacheButton' => 'ClearViewCache', 'ClearViewCacheSubtreeButton' => 'ClearViewCacheSubtree', 'MoveNodeButton' => 'MoveNodeRequest', 'MoveNodeAction' => 'MoveNode', 'SwapNodeButton' => 'SwapNodeRequest', 'SwapNodeAction' => 'SwapNode', 'UploadFileAction' => 'UploadFile'], 'post_action_parameters' => ['SelectAssignmentLocation' => ['AssignmentIDSelection' => 'AssignmentIDSelection', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'AddAssignment' => ['AssignmentIDSelection' => 'AssignmentIDSelection', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'RemoveAssignment' => [
    'AssignmentIDSelection' => 'AssignmentIDSelection',
    // Note: AssignmentIDSelection is deprecated, use LocationIDSelection
    'LocationIDSelection' => 'LocationIDSelection',
    'NodeID' => 'ContentNodeID',
    'ObjectID' => 'ContentObjectID',
    'ViewMode' => 'ViewMode',
    'LanguageCode' => 'ContentObjectLanguageCode',
], 'UpdateMainAssignment' => ['MainAssignmentID' => 'MainAssignmentCheck', 'HasMainAssignment' => 'HasMainAssignment', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'ClearViewCache' => ['NodeID' => 'NodeID', 'ObjectID' => 'ObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode', 'CurrentURL' => 'CurrentURL'], 'ClearViewCacheSubtree' => ['NodeID' => 'NodeID', 'ObjectID' => 'ObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode', 'CurrentURL' => 'CurrentURL'], 'MoveNodeRequest' => ['NodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'MoveNode' => ['NodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode', 'NewParentNode' => 'NewParentNode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'SwapNodeRequest' => ['NodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'SwapNode' => ['NodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode', 'NewNode' => 'NewNode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'UploadFile' => ['UploadActionName' => 'UploadActionName', 'UploadParentNodes' => 'UploadParentNodes', 'UploadRedirectBack' => 'UploadRedirectBack']], 'post_actions' => ['BrowseActionName']];

$ViewList['collectinformation'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'collectinformation.php', 'single_post_actions' => ['ActionCollectInformation' => 'CollectInformation'], 'post_action_parameters' => ['CollectInformation' => ['ContentObjectID' => 'ContentObjectID', 'ContentNodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode']], 'params' => []];

$ViewList['draft'] = ['functions' => ['edit'], 'script' => 'draft.php', 'default_navigation_part' => 'ezmynavigationpart', 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['history'] = ['functions' => ['read', 'edit'], 'default_navigation_part' => 'ezcontentnavigationpart', 'ui_context' => 'edit', 'script' => 'history.php', 'single_post_actions' => ['HistoryCopyVersionButton' => 'CopyVersion', 'HistoryEditButton' => 'Edit'], 'post_action_parameters' => ['CopyVersion' => ['VersionID' => 'RevertToVersionID', 'VersionKeyArray' => 'HistoryCopyVersionButton', 'LanguageArray' => 'CopyVersionLanguage'], 'Edit' => ['VersionID' => 'RevertToVersionID', 'VersionKeyArray' => 'HistoryEditButton']], 'params' => ['ObjectID', 'EditVersion'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['trash'] = ['functions' => ['restore'], 'script' => 'trash.php', 'default_navigation_part' => 'ezcontentnavigationpart', 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['translations'] = ['functions' => ['translations'], 'ui_context' => 'administration', 'default_navigation_part' => 'ezsetupnavigationpart', 'script' => 'translations.php', 'single_post_actions' => ['RemoveButton' => 'Remove', 'StoreButton' => 'StoreNew', 'NewButton' => 'New', 'ConfirmButton' => 'Confirm'], 'post_action_parameters' => ['StoreNew' => ['LocaleID' => 'LocaleID', 'TranslationName' => 'TranslationName', 'TranslationLocale' => 'TranslationLocale'], 'Remove' => ['SelectedTranslationList' => 'DeleteIDArray'], 'Confirm' => ['ConfirmList' => 'ConfirmTranlationID']], 'params' => ['TranslationID']];

$ViewList['tipafriend'] = ['functions' => ['tipafriend', 'read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'tipafriend.php', 'params' => ['NodeID']];

$ViewList['keyword'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'keyword.php', 'params' => ['alphabet'=>'Alphabet'], 'unordered_params' => ['offset' => 'Offset', 'classid' => 'ClassID']];

$ViewList['collectedinfo'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'collectedinfo.php', 'params' => ['NodeID']];

$ViewList['bookmark'] = ['functions' => ['bookmark'], 'default_navigation_part' => 'ezmynavigationpart', 'script' => 'bookmark.php', 'params' => [], 'single_post_actions' => ['AddButton' => 'Add', 'RemoveButton' => 'Remove'], 'post_actions' => ['BrowseActionName'], 'post_action_parameters' => ['Remove' => ['DeleteIDArray' => 'DeleteIDArray']], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['pendinglist'] = ['functions' => ['pendinglist'], 'default_navigation_part' => 'ezmynavigationpart', 'script' => 'pendinglist.php', 'params' => [], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['new'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'newcontent.php', 'params' => []];

$ViewList['hide'] = ['functions' => ['hide'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'hide.php', 'params' => ['NodeID']];

$ViewList['move'] = ['functions' => ['edit'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'move.php', 'params' => ['NodeID']];

$ViewList['reverserelatedlist'] = ['functions' => ['reverserelatedlist'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'reverserelatedlist.php', 'params' => ['NodeID'], 'unordered_params' => ['offset' => 'Offset']];

$ViewList['translation'] = ['functions' => ['read'], 'default_navigation_part' => 'ezcontentnavigationpart', 'script' => 'translation.php', 'params' => [], 'single_post_actions' => ['CancelButton' => 'Cancel', 'UpdateInitialLanguageButton' => 'UpdateInitialLanguage', 'UpdateAlwaysAvailableButton' => 'UpdateAlwaysAvailable', 'RemoveTranslationButton' => 'RemoveTranslation'], 'post_action_parameters' => ['Cancel' => ['NodeID' => 'ContentNodeID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'UpdateInitialLanguage' => ['InitialLanguageID' => 'InitialLanguageID', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'UpdateAlwaysAvailable' => ['AlwaysAvailable' => 'AlwaysAvailable', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode'], 'RemoveTranslation' => ['LanguageID' => 'LanguageID', 'ConfirmRemoval' => 'ConfirmRemoval', 'NodeID' => 'ContentNodeID', 'ObjectID' => 'ContentObjectID', 'ViewMode' => 'ViewMode', 'LanguageCode' => 'ContentObjectLanguageCode']]];

$ViewList['treemenu'] = ['functions' => ['read'], 'ui_context' => 'ajax', 'script' => 'treemenu.php', 'default_navigation_part' => 'ezmynavigationpart', 'params' => ['NodeID', 'Modified', 'Expiry', 'Perm']];

$ViewList['dashboard'] = ['functions' => ['dashboard'], 'script' => 'dashboard.php', 'default_navigation_part' => 'ezmynavigationpart', 'params' => [], 'unordered_params' => []];

$ViewList['queued'] = ['functions' => ['edit'], 'default_navigation_part' => 'ezmynavigationpart', 'script' => 'queued.php', 'params' => ['ContentObjectID', 'version']];

$ClassID = ['name'=> 'Class', 'values'=> [], 'class' => 'eZContentClass', 'function' => 'fetchList', 'parameter' => [0, false, false, ['name' => 'asc']]];

$ParentClassID = ['name'=> 'ParentClass', 'values'=> [], 'class' => 'eZContentClass', 'function' => 'fetchList', 'parameter' => [0, false, false, ['name' => 'asc']]];

$SectionID = ['name'=> 'Section', 'values'=> [], 'class' => 'eZSection', 'function' => 'fetchList', 'parameter' => [false]];

$VersionStatusRead = ['name'=> 'Status', 'values'=> [], 'class' => 'eZContentObjectVersion', 'function' => 'statusList', 'parameter' => ['read']];

$VersionStatusRemove = ['name'=> 'Status', 'values'=> [], 'class' => 'eZContentObjectVersion', 'function' => 'statusList', 'parameter' => ['remove']];

$Language = ['name'=> 'Language', 'values'=> [], 'class' => 'eZContentLanguage', 'function' => 'fetchLimitationList', 'parameter' => [false]];

$Assigned = ['name'=> 'Owner', 'values'=> [['Name' => 'Self', 'value' => '1']]];

$AssignedEdit = ['name'=> 'Owner', 'single_select' => true, 'values'=> [['Name' => 'Self', 'value' => '1'], ['Name' => 'Self or anonymous users per HTTP session', 'value' => '2']]];

$ParentAssignedEdit = ['name'=> 'ParentOwner', 'single_select' => true, 'values'=> [['Name' => 'Self', 'value' => '1'], ['Name' => 'Self or anonymous users per HTTP session', 'value' => '2']]];


$AssignedGroup = ['name'=> 'Group', 'single_select' => true, 'values'=> [['Name' => 'Self', 'value' => '1']]];

$ParentAssignedGroup = ['name'=> 'ParentGroup', 'single_select' => true, 'values'=> [['Name' => 'Self', 'value' => '1']]];

$ParentDepth = ['name' => 'ParentDepth', 'values' => [], 'class' => 'eZContentObjectTreeNode', 'function' => 'parentDepthLimitationList', 'parameter' => [false]];

$Node = ['name'=> 'Node', 'values'=> []];

$Subtree = ['name'=> 'Subtree', 'values'=> []];

$stateLimitations = eZContentObjectStateGroup::limitations();

$FunctionList = [];
$FunctionList['bookmark'] = [];

$FunctionList['move'] = [];

$FunctionList['read'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Group' => $AssignedGroup, 'Node' => $Node, 'Subtree' => $Subtree];
$FunctionList['read'] = array_merge( $FunctionList['read'], $stateLimitations );
$FunctionList['diff'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Node' => $Node, 'Subtree' => $Subtree];
$FunctionList['view_embed'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Node' => $Node, 'Subtree' => $Subtree];
$FunctionList['create'] = ['Class' => $ClassID, 'Section' => $SectionID, 'ParentOwner' => $ParentAssignedEdit, 'ParentGroup' => $ParentAssignedGroup, 'ParentClass' => $ParentClassID, 'ParentDepth' => $ParentDepth, 'Node' => [...$Node, 'DropList' => ['ParentClass', 'Section']], 'Subtree' => $Subtree, 'Language' => $Language];
$FunctionList['edit'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $AssignedEdit, 'Group' => $AssignedGroup, 'Node' => $Node, 'Subtree' => $Subtree, 'Language' => $Language];
$FunctionList['edit'] = array_merge( $FunctionList['edit'], $stateLimitations );

$FunctionList['publish'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $AssignedEdit, 'Group' => $AssignedGroup, 'Node' => $Node, 'Subtree' => $Subtree, 'Language' => $Language];
$FunctionList['publish'] = array_merge( $FunctionList['publish'], $stateLimitations );

$FunctionList['manage_locations'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Subtree' => $Subtree];
$FunctionList['manage_locations'] = array_merge( $FunctionList['manage_locations'], $stateLimitations );

$FunctionList['hide'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $AssignedEdit, 'Group' => $AssignedGroup, 'Node' => $Node, 'Subtree' => $Subtree, 'Language' => $Language];

$FunctionList['reverserelatedlist'] = [];

$FunctionList['translate'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Node' => $Node, 'Subtree' => $Subtree, 'Language' => $Language];
$FunctionList['remove'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Node' => $Node, 'Subtree' => $Subtree];
$FunctionList['remove'] = array_merge( $FunctionList['remove'], $stateLimitations );

$FunctionList['versionread'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Status' => $VersionStatusRead, 'Node' => $Node, 'Subtree' => $Subtree];

$FunctionList['versionremove'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Status' => $VersionStatusRemove, 'Node' => $Node, 'Subtree' => $Subtree];

$FunctionList['pdf'] = ['Class' => $ClassID, 'Section' => $SectionID, 'Owner' => $Assigned, 'Node' => $Node, 'Subtree' => $Subtree];

$FunctionList['translations'] = [];
$FunctionList['urltranslator'] = [];
$FunctionList['pendinglist'] = [];
$FunctionList['restore'] = [];
$FunctionList['cleantrash'] = [];
$FunctionList['tipafriend'] = [];
$FunctionList['dashboard'] = [];

?>
