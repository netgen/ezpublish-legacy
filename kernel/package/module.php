<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = ['name' => 'eZPackage'];

$ViewList = [];
$ViewList['list'] = ['functions' => ['list'], 'script' => 'list.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['ChangeRepositoryButton' => 'ChangeRepository', 'InstallPackageButton' => 'InstallPackage', 'RemovePackageButton' => 'RemovePackage', 'ConfirmRemovePackageButton' => 'ConfirmRemovePackage', 'CancelRemovePackageButton' => 'CancelRemovePackage', 'CreatePackageButton' => 'CreatePackage'], 'post_action_parameters' => ['ChangeRepository' => ['RepositoryID' => 'RepositoryID'], 'RemovePackage' => ['PackageSelection' => 'PackageSelection'], 'ConfirmRemovePackage' => ['PackageSelection' => 'PackageSelection']], "unordered_params" => ["offset" => "Offset"], 'params' => ['RepositoryID']];

$ViewList['upload'] = ['functions' => ['import'], 'script' => 'upload.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['UploadPackageButton' => 'UploadPackage', 'UploadCancelButton' => 'UploadCancel'], 'params' => []];

$ViewList['create'] = ['functions' => ['create'], 'script' => 'create.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['CreatePackageButton' => 'CreatePackage', 'PackageStep' => 'PackageStep'], 'post_action_parameters' => ['CreatePackage' => ['CreatorItemID' => 'CreatorItemID'], 'PackageStep' => ['CreatorItemID' => 'CreatorItemID', 'CreatorStepID' => 'CreatorStepID', 'PreviousStep' => 'PreviousStepButton', 'NextStep' => 'NextStepButton']], 'params' => []];

$ViewList['export'] = ['functions' => ['export'], 'script' => 'export.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'params' => ['PackageName']];

$ViewList['view'] = ['functions' => ['read'], 'script' => 'view.php', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['InstallButton' => 'Install', 'UninstallButton' => 'Uninstall', 'ExportButton' => 'Export'], 'params' => ['ViewMode', 'PackageName', 'RepositoryID']];

$ViewList['install'] = ['functions' => ['install'], 'script' => 'install.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['HandleError' => 'HandleError', 'InstallPackageButton' => 'InstallPackage', 'PackageStep' => 'PackageStep', 'SkipPackageButton' => 'SkipPackage'], 'post_action_parameters' => ['InstallPackage' => ['InstallerType' => 'InstallerType'], 'PackageStep' => ['InstallerType' => 'InstallerType', 'InstallStepID' => 'InstallStepID', 'PreviousStep' => 'PreviousStepButton', 'NextStep' => 'NextStepButton'], 'HandleError' => ['ActionID' => 'ActionID', 'RememberAction' => 'RememberAction']], 'params' => ['PackageName']];

$ViewList['uninstall'] = ['functions' => ['install'], 'script' => 'uninstall.php', 'ui_context' => 'edit', 'default_navigation_part' => 'ezsetupnavigationpart', 'single_post_actions' => ['HandleError' => 'HandleError', 'UninstallPackageButton' => 'UninstallPackage', 'SkipPackageButton' => 'SkipPackage'], 'post_action_parameters' => ['HandleError' => ['ActionID' => 'ActionID', 'RememberAction' => 'RememberAction']], 'params' => ['PackageName']];

$TypeID = ['name'=> 'Type', 'values'=> [], 'class' => 'eZPackage', 'function' => 'typeList', 'parameter' => [false]];

$CreatorTypeID = ['name'=> 'CreatorType', 'values'=> [], 'class' => 'eZPackageCreationHandler', 'function' => 'creatorLimitationList', 'parameter' => [false]];

$RoleID = ['name'=> 'Role', 'values'=> [], 'class' => 'eZPackage', 'function' => 'maintainerRoleListForRoles', 'parameter' => [false]];


$FunctionList = [];
$FunctionList['read'] = ['Type' => $TypeID];
$FunctionList['list'] = ['Type' => $TypeID];
$FunctionList['create'] = ['Type' => $TypeID, 'CreatorType' => $CreatorTypeID, 'Role' => $RoleID];
$FunctionList['edit'] = ['Type' => $TypeID];
$FunctionList['remove'] = ['Type' => $TypeID];
$FunctionList['install'] = ['Type' => $TypeID];
$FunctionList['import'] = ['Type' => $TypeID];
$FunctionList['export'] = ['Type' => $TypeID];

?>
