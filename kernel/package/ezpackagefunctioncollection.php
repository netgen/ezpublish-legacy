<?php
/**
 * File containing the eZPackageFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPackageFunctionCollection ezpackagefunctioncollection.php
  \brief The class eZPackageFunctionCollection does

*/

class eZPackageFunctionCollection
{
    function fetchList( $filterArray, $offset, $limit, $repositoryID )
    {
        $filterParams = [];
        $filterList = false;
        if ( isset( $filterArray ) and
             is_array( $filterArray ) and
             count( $filterArray ) > 0 )
        {
            $filterList = $filterArray;
            if ( count( $filterArray ) > 1 and
                 !is_array( $filterArray[0] ) )
            {
                $filterList = [$filterArray];
            }
        }
        if ( $filterList !== false )
        {
            foreach ( $filterList as $filter )
            {
                if ( is_array( $filter ) and count( $filter ) > 0 )
                {
                    $filterName = $filter[0];
                    switch ( $filterName )
                    {
                        case 'type':
                        {
                            $typeValue = $filter[1];
                            $typeParam = ['type' => $typeValue];
                            $filterParams = [...$filterParams, ...$typeParam];
                        } break;
                        case 'priority':
                        {
                            $priorityValue = $filter[1];
                            $priorityParam = ['priority' => $priorityValue];
                            $filterParams = [...$filterParams, ...$priorityParam];
                        } break;
                        case 'vendor':
                        {
                            $vendorValue = $filter[1];
                            $vendorParam = ['vendor' => $vendorValue];
                            $filterParams = [...$filterParams, ...$vendorParam];
                        } break;
                        case 'extension':
                        {
                            $extensionValue = $filter[1];
                            $extensionParam = ['extension' => $extensionValue];
                            $filterParams = [...$filterParams, ...$extensionParam];
                        } break;
                        default:
                        {
                            eZDebug::writeWarning( 'Unknown package filter name: ' . $filterName );
                        }
                    }
                }
            }
        }
        $params = ['offset' => $offset, 'limit' => $limit];
        if ( $repositoryID )
            $params['repository_id'] = $repositoryID;

        $packageList = eZPackage::fetchPackages( $params,
                                                 $filterParams );
        if ( $packageList === null )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        return ['result' => $packageList];
    }

    function fetchPackage( $packageName, $repositoryID )
    {
        $package = eZPackage::fetch( $packageName, false, $repositoryID );
        if ( $package === false )
        {
            $retValue = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $retValue = ['result' => $package];
        }
        return $retValue;
    }

    function fetchDependentPackageList( $packageName, $filterArray, $repositoryID )
    {
        $filterParams = [];
        $filterList = false;
        if ( isset( $filterArray ) and
             is_array( $filterArray ) and
             count( $filterArray ) > 0 )
        {
            $filterList = $filterArray;
            if ( count( $filterArray ) > 1 and
                 !is_array( $filterArray[0] ) )
            {
                $filterList = [$filterArray];
            }
        }
        if ( $filterList !== false )
        {
            foreach ( $filterList as $filter )
            {
                if ( is_array( $filter ) and count( $filter ) > 0 )
                {
                    $filterName = $filter[0];
                    switch ( $filterName )
                    {
                        case 'type':
                        {
                            $typeValue = $filter[1];
                            $typeParam = ['type' => $typeValue];
                            $filterParams = [...$filterParams, ...$typeParam];
                        } break;
                        case 'name':
                        {
                            $nameValue = $filter[1];
                            $nameParam = ['name' => $nameValue];
                            $filterParams = [...$filterParams, ...$nameParam];
                        } break;
                        case 'priority':
                        {
                            $priorityValue = $filter[1];
                            $priorityParam = ['priority' => $priorityValue];
                            $filterParams = [...$filterParams, ...$priorityParam];
                        } break;
                        case 'vendor':
                        {
                            $vendorValue = $filter[1];
                            $vendorParam = ['vendor' => $vendorValue];
                            $filterParams = [...$filterParams, ...$vendorParam];
                        } break;
                        case 'extension':
                        {
                            $extensionValue = $filter[1];
                            $extensionParam = ['extension' => $extensionValue];
                            $filterParams = [...$filterParams, ...$extensionParam];
                        } break;
                        default:
                        {
                            eZDebug::writeWarning( 'Unknown package filter name: ' . $filterName );
                        }
                    }
                }
            }
        }
        $package = eZPackage::fetch( $packageName, false, $repositoryID );
        $packageList = $package->fetchDependentPackages( $filterParams );
        if ( $packageList === false )
        {
            $retValue = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $retValue = ['result' => $packageList];
        }
        return $retValue;
    }

    function fetchMaintainerRoleList( $packageType, $checkRoles )
    {
        $list = eZPackage::fetchMaintainerRoleList( $packageType, $checkRoles );
        if ( $list === false )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        return ['result' => $list];
    }

    function fetchRepositoryList()
    {
        $list = eZPackage::packageRepositories();
        if ( $list === false )
            return ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        return ['result' => $list];
    }

    function canCreate()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'create' )];
    }

    function canEdit()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'edit' )];
    }

    function canImport()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'import' )];
    }

    function canInstall()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'install' )];
    }

    function canExport()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'export' )];
    }

    function canRead()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'read' )];
    }

    function canList()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'list' )];
    }

    function canRemove()
    {
        return ['result' => eZPackage::canUsePolicyFunction( 'remove' )];
    }
}

?>
