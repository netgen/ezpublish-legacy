<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$tpl = eZTemplate::factory();
$http = eZHTTPTool::instance();
$module = $Params['Module'];

$upload = new eZContentUpload();

$errors = [];

if ( $module->isCurrentAction( 'CancelUpload' ) )
{
    $url = false;
    if ( $upload->attribute( 'cancel_uri' ) )
    {
        $url = $upload->attribute( 'cancel_uri' );
    }
    else if ( $upload->attribute( 'result_uri' ) )
    {
        $url = $module->redirectTo( $upload->attribute( 'result_uri' ) );
    }
    else if ( $upload->attribute( 'result_module' ) )
    {
        $info = $upload->attribute( 'result_module' );
        $moduleName = $info[0] ?? false;
        $viewName = $info[1] ?? false;
        $parameters = $info[2] ?? false;
        $unorderedParameters = $info[3] ?? false;
        $userParameters = $info[4] ?? false;
        $anchor = $info[5] ?? false;
        $url = $module->redirectionURI( $moduleName, $viewName, $parameters,
                                        $unorderedParameters, $userParameters, $anchor );
    }
    else
    {
        $url = '/';
    }
    $http = eZHTTPTool::instance();
    $http->removeSessionVariable( 'ContentUploadParameters' );
    return $module->redirectTo( $url );
}

if ( $module->isCurrentAction( 'UploadFile' ) )
{
    $location = false;
    if ( $module->hasActionParameter( 'UploadLocation' ) )
        $location = $module->actionParameter( 'UploadLocation' );

    if ( $module->hasActionParameter( 'ObjectName' ) )
        $objectName = $module->actionParameter( 'ObjectName' );

    if ( $upload->handleUpload( $result, 'UploadFile', $location, false, $objectName ) )
    {
        $object = $result['contentobject'];
        $mainNode = $result['contentobject_main_node'];
        if ( $result['redirect_url'] )
        {
            return $module->redirectTo( $result['redirect_url'] );
        }
        if ( $result['result'] )
        {
            $resultData = $result['result'];
            $resultContent = false;
            if ( is_array( $resultData ) )
            {
                if ( isset( $resultData['content'] ) )
                    $resultContent = $resultData['content'];
                if ( isset( $resultData['path'] ) )
                    $Result['path'] = $resultData['path'];
            }
            else
            {
                $resultContent = $resultData;
            }
            $Result['content'] = $resultContent;
        }

        // Redirect to request URI if it is set, if not view the new object in main node
        if ( $upload->attribute( 'result_uri' ) )
        {
            $uri = $upload->attribute( 'result_uri' );
            return $module->redirectTo( $uri );
        }
        else if ( $upload->attribute( 'result_module' ) )
        {
            $data = $upload->attribute( 'result_module' );
            $moduleName = $data[0];
            $view = $data[1];
            $parameters = $data[2] ?? [];
            $userParameters = $data[3] ?? [];
            $resultModule = eZModule::findModule( $moduleName, $module );
            $resultModule->setCurrentAction( $upload->attribute( 'result_action_name' ), $view );
            $actionParameters = false;
            if ( $upload->hasAttribute( 'result_action_parameters' ) )
            {
                $actionParameters = $upload->attribute( 'result_action_parameters' );
            }

            if ( $actionParameters )
            {
                foreach ( $actionParameters as $actionParameterName => $actionParameter )
                {
                    $resultModule->setActionParameter( $actionParameterName, $actionParameter, $view );
                }
            }
            return $resultModule->run( $view, $parameters, false, $userParameters );
        }
        else
        {
            $mainNode = $object->mainNode();
            $upload->cleanupAll();
            return $module->redirectTo( '/' . $mainNode->attribute( 'url' ) );
        }
    }
    else
    {
        $errors = $result['errors'];
    }
}


$res = eZTemplateDesignResource::instance();
$keyArray = [];
$attributeKeys = $upload->attribute( 'keys' );

if ( is_array( $attributeKeys ) )
{
    foreach ( $attributeKeys as $attributeKey => $attributeValue )
    {
        $keyArray[] = [$attributeKey, $attributeValue];
    }
}
$res->setKeys( $keyArray );

$tpl->setVariable( 'upload', $upload );
$tpl->setVariable( 'errors', $errors );

$Result = [];

$navigationPart = $upload->attribute( 'navigation_part_identifier' );
if ( $navigationPart )
    $Result['navigation_part'] = $navigationPart;
$uiContext = $upload->attribute( 'ui_context' );
if ( $uiContext )
{
    $module->setUIContextName( $uiContext );
    $tpl->setVariable( 'ui_context', $uiContext );
}
// setting keys for override
$res = eZTemplateDesignResource::instance();

$Result['content'] = $tpl->fetch( 'design:content/upload.tpl' );

$Result['path'] = [['text' => 'Upload', 'url' => false]];

?>
