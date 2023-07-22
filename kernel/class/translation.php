<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];


if ( !$module->hasActionParameter( 'ClassID' ) )
{
    eZDebug::writeError( 'Missing ClassID parameter for action ' . $module->currentAction(),
                         'class/translation' );
    return $module->redirectToView( 'grouplist' );
}

$classID = $module->actionParameter( 'ClassID' );

if ( !$module->hasActionParameter( 'LanguageCode' ) )
{
    eZDebug::writeError( 'Missing LanguageCode parameter for action ' . $module->currentAction(),
                         'class/translation' );
    return $module->redirectToView( 'view', [$classID] );
}

$languageCode = $module->actionParameter( 'LanguageCode' );

if ( $module->isCurrentAction( 'Cancel' ) )
{
    return $module->redirectToView( 'view', [$classID], ['Language' => $languageCode] );
}

$class = eZContentClass::fetch( $classID );

if ( !$class )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( $module->isCurrentAction( 'UpdateInitialLanguage' ) )
{
    if ( $module->hasActionParameter( 'InitialLanguageID' ) )
    {
        $newInitialLanguageID = $module->actionParameter( 'InitialLanguageID' );

        $language = eZContentLanguage::fetch( $newInitialLanguageID );
        if ( $language )
        {
            $class->setAttribute( 'initial_language_id', $newInitialLanguageID );
            $class->setAlwaysAvailableLanguageID( $newInitialLanguageID );
        }
    }

    return $module->redirectToView( 'view', [$classID], ['Language' => $languageCode] );
}
else if ( $module->isCurrentAction( 'RemoveTranslation' ) )
{
    if ( !$module->hasActionParameter( 'LanguageID' ) )
    {
        return $module->redirectToView( 'view', [$classID], ['Language' => $languageCode] );
    }

    $languageIDArray = $module->actionParameter( 'LanguageID' );

    if ( $module->hasActionParameter( 'ConfirmRemoval' ) && $module->actionParameter( 'ConfirmRemoval' ) )
    {
        foreach( $languageIDArray as $languageID )
        {
            if ( !$class->removeTranslation( $languageID ) )
            {
                eZDebug::writeError( "Class with id " . $class->attribute( 'id' ) . ": cannot remove the translation with language id $languageID!", 'class/translation' );
            }
        }

        //probably we've just removed translation we were viewing.
        if ( !$class->hasNameInLanguage( $languageCode ) )
            $languageCode = $class->alwaysAvailableLanguageLocale();

        return $module->redirectToView( 'view', [$classID], ['Language' => $languageCode] );
    }

    $languages = [];
    foreach( $languageIDArray as $languageID )
    {
        $language = eZContentLanguage::fetch( $languageID );
        if ( $language )
        {
            $languages[] = $language;
        }
    }

    if ( !$languages )
    {
        return $module->redirectToView( 'view', [$classID], [$languageCode] );
    }

    $tpl = eZTemplate::factory();

    $tpl->setVariable( 'class_id', $classID );
    $tpl->setVariable( 'class', $class );
    $tpl->setVariable( 'language_code', $languageCode );
    $tpl->setVariable( 'languages', $languages );

    $Result = [];
    $Result['content'] = $tpl->fetch( 'design:class/removetranslation.tpl' );
    $Result['path'] = [['url' => false, 'text' => ezpI18n::tr( 'kernel/class', 'Remove translation' )]];

    return;
}





?>
