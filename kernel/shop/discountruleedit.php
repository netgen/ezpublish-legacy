<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// TODO: it was not in the original code, but we may consider to add support for "folder with products",
//       not only products (i.e. objects with attribute of the ezprice datatype).


$module = $Params['Module'];

if ( !isset( $Params['DiscountGroupID'] ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}
else
{
    $discountGroupID = $Params['DiscountGroupID'];
}

$discountRuleID = false;

if ( isset( $Params['DiscountRuleID'] ) )
{
    $discountRuleID = $Params['DiscountRuleID'];
}

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'DiscardButton' ) )
{
    return $module->redirectTo( $module->functionURI( 'discountgroupview' ) . '/' . $discountGroupID );
}


if ( $http->hasPostVariable( 'BrowseProductButton' ) )
{
    eZContentBrowse::browse( $module,
                             ['action_name' => 'FindProduct', 'description_template' => 'design:shop/browse_discountproduct.tpl', 'keys' => ['discountgroup_id' => $discountGroupID, 'discountrule_id' => $discountRuleID], 'content' => ['discountgroup_id' => $discountGroupID, 'discountrule_id' => $discountRuleID], 'persistent_data' => ['discountrule_name' => $http->postVariable( 'discountrule_name' ), 'discountrule_percent' => $http->postVariable( 'discountrule_percent' ), 'Contentclasses' => ( $http->hasPostVariable( 'Contentclasses' ) )? json_encode( $http->postVariable( 'Contentclasses' ), JSON_THROW_ON_ERROR ): '', 'Sections' => ( $http->hasPostVariable( 'Sections' ) )? json_encode( $http->postVariable( 'Sections' ), JSON_THROW_ON_ERROR ): '', 'Products' => ( $http->hasPostVariable( 'Products' ) )? json_encode( $http->postVariable( 'Products' ), JSON_THROW_ON_ERROR ): ''], 'from_page' => "/shop/discountruleedit/$discountGroupID/$discountRuleID"] );
    return;
}

if ( $http->hasPostVariable( 'discountrule_name' ) )
{
    // if it has post variables, the values will be taken from POST variables instead of object itself
    $locale = eZLocale::instance();

    $discountRuleName = $http->postVariable( 'discountrule_name' );
    $discountRulePercent = $locale->internalNumber( $http->postVariable( 'discountrule_percent' ) );

    $discountRuleSelectedClasses = [];
    if ( $http->hasPostVariable( 'Contentclasses' ) && $http->postVariable( 'Contentclasses' ) )
    {
        $discountRuleSelectedClasses = $http->postVariable( 'Contentclasses' );
        if ( !is_array( $discountRuleSelectedClasses ) )
        {
            $discountRuleSelectedClasses = json_decode( (string) $discountRuleSelectedClasses, null, 512, JSON_THROW_ON_ERROR );
        }
    }

    $discountRuleSelectedSections = [];
    if ( $http->hasPostVariable( 'Sections' ) && $http->postVariable( 'Sections' ) )
    {
        $discountRuleSelectedSections = $http->postVariable( 'Sections' );
        if ( !is_array( $discountRuleSelectedSections ) )
        {
            $discountRuleSelectedSections = json_decode( (string) $discountRuleSelectedSections, null, 512, JSON_THROW_ON_ERROR );
        }
    }

    $discountRuleSelectedProducts = [];
    if ( $http->hasPostVariable( 'Products' ) && $http->postVariable( 'Products' ) )
    {
        $discountRuleSelectedProducts = $http->postVariable( 'Products' );
        if ( !is_array( $discountRuleSelectedProducts ) )
        {
            $discountRuleSelectedProducts = json_decode( (string) $discountRuleSelectedProducts, null, 512, JSON_THROW_ON_ERROR );
        }
    }

    $discountRule = ['id' => $discountRuleID, 'name' => $discountRuleName, 'discount_percent' => $discountRulePercent];
}
else
{
    // read variables from object, if it exists, if not, create new one...
    if ( $discountRuleID )
    {
        // exists => read needed info from db
        $discountRule = eZDiscountSubRule::fetch( $discountRuleID );
        if ( !$discountRule )
        {
            return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        }

        $discountRuleSelectedClasses = [];
        $discountRuleSelectedClassesValues = eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID, 0, false );
        foreach( $discountRuleSelectedClassesValues as $value )
        {
            $discountRuleSelectedClasses[] = $value['value'];
        }
        if ( count( $discountRuleSelectedClasses ) == 0 )
        {
            $discountRuleSelectedClasses[] = -1;
        }

        $discountRuleSelectedSections = [];
        $discountRuleSelectedSectionsValues = eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID, 1, false );
        foreach( $discountRuleSelectedSectionsValues as $value )
        {
            $discountRuleSelectedSections[] = $value['value'];
        }
        if ( count( $discountRuleSelectedSections ) == 0 )
        {
            $discountRuleSelectedSections[] = -1;
        }

        $discountRuleSelectedProductsValues = eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID, 2, false );
        foreach( $discountRuleSelectedProductsValues as $value )
        {
            $discountRuleSelectedProducts[] = $value['value'];
        }
    }
    else
    {
        // does not exist => create new one, but do not store...
        $discountRuleName = ezpI18n::tr( 'design/admin/shop/discountruleedit', 'New discount rule' );
        $discountRulePercent = 0.0;
        $discountRuleSelectedClasses = [-1];
        $discountRuleSelectedSections = [-1];
        $discountRuleSelectedProducts = [];

        $discountRule = ['id' => 0, 'name' => $discountRuleName, 'discount_percent' => $discountRulePercent];
    }
}

if ( $module->isCurrentAction( 'FindProduct' ) )
{
    // returning from browse; add products to product list
    $result = eZContentBrowse::result( 'FindProduct' );
    if ( $result )
    {
        $discountRuleSelectedProducts = array_merge( $discountRuleSelectedProducts, $result );
        $discountRuleSelectedProducts = array_unique( $discountRuleSelectedProducts );
    }
}

if ( $http->hasPostVariable( 'DeleteProductButton' ) )
{
    // remove products from list:
    if ( $http->hasPostVariable( 'DeleteProductIDArray' ) )
    {
        $deletedIDList = $http->postVariable( 'DeleteProductIDArray' );
        $arrayKeys = array_keys( $discountRuleSelectedProducts );

        foreach( $arrayKeys as $key )
        {
            if ( in_array( $discountRuleSelectedProducts[$key], $deletedIDList ) )
            {
                unset( $discountRuleSelectedProducts[$key] );
            }
        }
    }
}

$productList = [];
foreach ( $discountRuleSelectedProducts as $productID )
{
    $object = eZContentObject::fetch( $productID );
    if ( eZShopFunctions::isProductObject( $object ) )
        $productList[] = $object;
}

if ( $http->hasPostVariable( 'StoreButton' ) )
{
    // remove products stored in the database and store them again
    $db = eZDB::instance();
    $db->begin();
    if ( $discountRuleID )
    {
        $discountRule = eZDiscountSubRule::fetch( $discountRuleID );
        eZDiscountSubRuleValue::removeBySubRuleID ( $discountRuleID );
    }
    else
    {
        $discountRule = eZDiscountSubRule::create( $discountGroupID );
        $discountRule->store();
        $discountRuleID = $discountRule->attribute( 'id' );
    }

    $discountRule->setAttribute( 'name', trim( (string) $http->postVariable( 'discountrule_name' ) ) );
    $discountRule->setAttribute( 'discount_percent', $http->postVariable( 'discountrule_percent' ) );
    $discountRule->setAttribute( 'limitation', '*' );

    if ( $http->hasPostVariable( 'Products' ) && $http->postVariable( 'Products' ) )
    {
        foreach( $productList as $product )
        {
            $ruleValue = eZDiscountSubRuleValue::create( $discountRuleID, $product->attribute( 'id' ), 2 );
            $ruleValue->store();
        }
        $discountRule->setAttribute( 'limitation', false );
    }
    else
    {
        if ( $discountRuleSelectedClasses && !in_array( -1, $discountRuleSelectedClasses ) )
        {
            foreach( $discountRuleSelectedClasses as $classID )
            {
                $ruleValue = eZDiscountSubRuleValue::create( $discountRuleID, $classID, 0 );
                $ruleValue->store();
            }
            $discountRule->setAttribute( 'limitation', false );
        }
        if ( $discountRuleSelectedSections && !in_array( -1, $discountRuleSelectedSections ) )
        {
            foreach( $discountRuleSelectedSections as $sectionID )
            {
                $ruleValue = eZDiscountSubRuleValue::create( $discountRuleID, $sectionID, 1 );
                $ruleValue->store();
            }
            $discountRule->setAttribute( 'limitation', false );
        }
    }

    $discountRule->store();
    $db->commit();

    // we changed prices => remove content cache
    eZContentCacheManager::clearAllContentCache();

    return $module->redirectTo( $module->functionURI( 'discountgroupview' ) . '/' . $discountGroupID );
}

$classList = eZContentClass::fetchList();
$productClassList = [];
foreach ( $classList as $class )
{
    if ( eZShopFunctions::isProductClass( $class ) )
        $productClassList[] = $class;
}

$sectionList = eZSection::fetchList();

$tpl = eZTemplate::factory();

$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'discountgroup_id', $discountGroupID );
$tpl->setVariable( 'discountrule', $discountRule );

$tpl->setVariable( 'product_class_list', $productClassList );
$tpl->setVariable( 'section_list', $sectionList );

$tpl->setVariable( 'class_limitation_list', $discountRuleSelectedClasses );
$tpl->setVariable( 'section_limitation_list', $discountRuleSelectedSections );
$tpl->setVariable( 'product_list', $productList );

$tpl->setVariable( 'class_any_selected', in_array( -1, $discountRuleSelectedClasses ) );
$tpl->setVariable( 'section_any_selected', in_array( -1, $discountRuleSelectedSections ) );

$Result = [];
$Result['content'] = $tpl->fetch( 'design:shop/discountruleedit.tpl' );
$Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/shop', 'Editing rule' )]];

?>
