<?php
/**
 * File containing the eZUserFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZUserFunctionCollection ezuserfunctioncollection.php
  \brief The class eZUserFunctionCollection does

*/

class eZUserFunctionCollection
{
    function fetchCurrentUser()
    {
        $user = eZUser::currentUser();
        if ( $user === null )
        {
            $result = ['error' => ['error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND]];
        }
        else
        {
            $result = ['result' => $user];
        }
        return $result;
    }

    function fetchIsLoggedIn( $userID )
    {
        $isLoggedIn = eZUser::isUserLoggedIn( $userID );
        return ['result' => $isLoggedIn];
    }

    function fetchLoggedInCount()
    {
        $count = eZUser::fetchLoggedInCount();
        return ['result' => $count];
    }

    function fetchAnonymousCount()
    {
        $count = eZUser::fetchAnonymousCount();
        return ['result' => $count];
    }

    function fetchLoggedInList( $sortBy, $offset, $limit )
    {
        $list = eZUser::fetchLoggedInList( false, $offset, $limit, $sortBy );
        return ['result' => $list];
    }

    function fetchLoggedInUsers( $sortBy, $offset, $limit )
    {
        $list = eZUser::fetchLoggedInList( true, $offset, $limit, $sortBy );
        return ['result' => $list];
    }

    /**
     * Fetch policy list
     * Used by fetch( 'user', 'user_role', hash( 'user_id', $id ) ) template function.
     *
     * @param int $id User id or normal content object id in case of none user object (user group)
     * @return array(string=>array)
     */
    function fetchUserRole( $id )
    {
        $user = eZUser::fetch( $id );
        if ( $user instanceof eZUser )
            $roleList = $user->roles();
        else // user group or other non user classes:
            $roleList = eZRole::fetchByUser( [$id], true );

        $accessArray = [];
        foreach ( array_keys ( $roleList ) as $roleKey )
        {
            $role = $roleList[$roleKey];
            $accessArray = array_merge_recursive( $accessArray, $role->accessArray( true ) );
        }
        $resultArray = [];
        foreach ( $accessArray as $moduleKey => $module )
        {
            $moduleName = $moduleKey;
            if ( $moduleName != '*' )
            {
                foreach ( $module as $functionKey => $function )
                {
                    $functionName = $functionKey;
                    if ( $functionName != '*' )
                    {
                        $hasLimitation = true;
                        foreach ( $function as $limitationKey )
                        {
                            if ( $limitationKey == '*' )
                            {
                                $hasLimitation = false;
                                $limitationValue = '*';
                                $resultArray[] = ['moduleName' => $moduleName, 'functionName' => $functionName, 'limitation' =>  $limitationValue];
                            }
                        }
                        if ( $hasLimitation )
                        {
                            foreach ( $function as $limitationKey => $limitation )
                            {
                                if ( $limitationKey !== '*' )
                                {
                                    $policyID = str_replace( 'p_', '', (string) $limitationKey );
                                    $userRoleIdSeperator = strpos( $policyID, '_' );

                                    if ( $userRoleIdSeperator !== false )
                                    {
                                        $policyID = substr( $policyID, 0, $userRoleIdSeperator );
                                    }

                                    $limitationValue = eZPolicyLimitation::fetchByPolicyID( $policyID );
                                    $resultArray[] = ['moduleName' => $moduleName, 'functionName' => $functionName, 'limitation' =>  $limitationValue];
                                }
                                else
                                {
                                    $limitationValue = '*';
                                    $resultArray[] = ['moduleName' => $moduleName, 'functionName' => $functionName, 'limitation' =>  $limitationValue];
                                    break;
                                }
                            }
                        }
                    }
                    else
                    {
                        $limitationValue = '*';
                        $resultArray[] = ['moduleName' => $moduleName, 'functionName' => $functionName, 'limitation' =>  $limitationValue];
                        break;
                    }
                }
            }
            else
            {
                $functionName = '*';
                $resultArray[] = ['moduleName' => '*', 'functionName' => $functionName, 'limitation' => '*'];
                break;
            }
        }
        return ['result' => $resultArray];
    }

    /**
     * Fetch role list
     * Used by fetch( 'user', 'member_of', hash( 'id', $id ) ) template function.
     *
     * @param int $id User id or normal content object id in case of none user object (user group)
     * @return array(string=>array)
     */
    function fetchMemberOf( $id )
    {
        $user = eZUser::fetch( $id );
        if ( $user instanceof eZUser )
            $roleList = $user->roles();
        else // user group or other non user classes:
            $roleList = eZRole::fetchByUser( [$id], true );

        return ['result' => $roleList];
    }

    function hasAccessTo( $module, $view, $userID )
    {
        if ( $userID )
        {
            $user = eZUser::fetch( $userID );
        }
        else
        {
            $user = eZUser::currentUser();
        }
        if ( is_object( $user ) )
        {
            $result = $user->hasAccessTo( $module, $view );
            return ['result' => $result['accessWord'] != 'no'];
        }
        else
        {
            return ['result' => false];
        }
    }
}

?>
