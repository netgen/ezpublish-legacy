<?php
/**
 * File containing the eZObjectforwarder class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZObjectForwarder ezobjectforwarder.php
  \brief The class eZObjectForwarder does

*/

class eZObjectForwarder
{
    /**
     * Constructor
     *
     * @param array $Rules
     */
    public function __construct(public $Rules)
    {
    }

    function functionList()
    {
        return array_keys( $this->Rules );
    }

    function functionTemplateHints()
    {
        $hints = [];
        foreach ( $this->Rules as $name => $data )
        {
            $hints[$name] = ['parameters' => true, 'static' => false, 'transform-children' => true, 'tree-transformation' => true, 'transform-parameters' => true];
        }
        return $hints;
    }

    function templateNodeTransformation( $functionName, &$node,
                                         $tpl, $parameters, $privateData )
    {
        $view_dir = null;
        if ( !isset( $this->Rules[$functionName] ) )
            return false;
        $rule = $this->Rules[$functionName];
        $resourceData = $privateData['resource-data'];

        $parameters = eZTemplateNodeTool::extractFunctionNodeParameters( $node );
        $inputName = $rule['input_name'];
        if ( !isset( $parameters[$inputName] ) )
        {
            return false;
        }
        $inputData = $parameters[$inputName];
        $outputName = $rule['output_name'];

        $newNodes = [];

        $viewDir = '';
        $renderMode = false;
        if ( isset( $rule["render_mode"] ) )
        {
            $renderMode = $rule["render_mode"];
        }
        if ( isset( $parameters['render-mode'] ) )
        {
            $renderData = $parameters['render-mode'];
            if ( !eZTemplateNodeTool::isConstantElement( $renderData ) )
            {
                return false;
            }
            $renderMode = eZTemplateNodeTool::elementConstantValue( $renderData );
        }
        if ( $renderMode )
            $view_dir .= "/render-$renderMode";

        $viewValue = false;
        $viewName = false;
        if ( $rule['use_views'] )
        {
            $viewName = $rule['use_views'];
            if ( isset( $parameters[$viewName] ) )
            {
                $viewData = $parameters[$viewName];
                if ( !eZTemplateNodeTool::isConstantElement( $viewData ) )
                {
                    return false;
                }
                $viewValue = eZTemplateNodeTool::elementConstantValue( $viewData );
                $viewDir .= '/' . $viewValue;
            }
            else
            {
                if ( !( isset( $rule['optional_views'] ) && $rule['optional_views'] ) )
                {
                    return false;
                }
            }
        }

        $namespaceValue = false;
        if ( isset( $rule['namespace'] ) )
        {
            $namespaceValue = $rule['namespace'];
        }

        $variableList = [];

        $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $inputData, false, [],
                                                              [$namespaceValue, eZTemplate::NAMESPACE_SCOPE_RELATIVE, $outputName] );
        $variableList[] = $outputName;

        foreach ( array_keys( $parameters ) as $parameterName )
        {
            if ( $parameterName == $inputName or
                 $parameterName == $outputName or
                 $parameterName == $viewName )
                continue;

            $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $parameters[$parameterName], false, [],
                                                                  [$namespaceValue, eZTemplate::NAMESPACE_SCOPE_RELATIVE, $parameterName] );
            $variableList[] = $parameterName;
        }

        $templateRoot = $rule["template_root"];
        $matchFileArray = eZTemplateDesignResource::overrideArray();

        if ( is_string( $templateRoot ) )
        {
            $resourceNodes = $this->resourceAcquisitionTransformation( $functionName, $node, $rule, $inputData,
                                                                       $outputName, $namespaceValue,
                                                                       $templateRoot, $viewDir, $viewValue,
                                                                       $matchFileArray, 0, $resourceData );
            // If the transformation failed we return false to invoke interpreted mode
            if ( $resourceNodes === false )
                return false;
            $newNodes = array_merge( $newNodes, $resourceNodes );
        }
        else
        {
            if ( isset( $templateRoot['type'] ) and
                 $templateRoot['type'] == 'multi_match' and
                 isset( $templateRoot['attributes'] ) and
                 isset( $templateRoot['matches'] ) )
            {
                $attributeAccessData = [];
                $attributeAccessData[] = eZTemplateNodeTool::createVariableElement( $outputName, $namespaceValue, eZTemplate::NAMESPACE_SCOPE_RELATIVE );
                foreach ( $templateRoot['attributes'] as $rootAttributeName )
                {
                    $attributeAccessData[] = eZTemplateNodeTool::createAttributeLookupElement( $rootAttributeName );
                }
                $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $attributeAccessData, false,
                                                                      ['spacing' => 0], 'templateRootMatch' );
                $rootMatchCounter = 0;
                foreach ( $templateRoot['matches'] as $rootMatch )
                {
                    $templateRoot = $rootMatch[1];

                    if ( is_array( $templateRoot ) )
                    {
                        $templateRoot = $templateRoot[0];
                    }

                    $resourceNodes = $this->resourceAcquisitionTransformation( $functionName, $node, $rule, $inputData,
                                                                               $outputName, $namespaceValue,
                                                                               $templateRoot, $viewDir, $viewValue,
                                                                               $matchFileArray, 4, $resourceData );

                    // If this transformation failed we continue to the next root match
                    if ( $resourceNodes === false )
                        continue;

                    $rootMatchValueText = eZPHPCreator::variableText( $rootMatch[0], 0, 0, false );
                    $code = '';
                    if ( $rootMatchCounter > 0 )
                    {
                        $code .= "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "";
                    }
                    $code .= "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( \$templateRootMatch == $rootMatchValueText )\n{";
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( $code );
                    $newNodes = array_merge( $newNodes, $resourceNodes );
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}" );
                    ++$rootMatchCounter;
                }

                // If the transformation failed we invoke interpreted mode
                if ( $rootMatchCounter == 0 )
                {
                    return false;
                }

                $newNodes[] = eZTemplateNodeTool::createVariableUnsetNode( 'templateRootMatch' );
            }
        }

        foreach ( $variableList as $variableName )
        {
            $newNodes[] = eZTemplateNodeTool::createVariableUnsetNode( [$namespaceValue, eZTemplate::NAMESPACE_SCOPE_RELATIVE, $variableName] );
        }

        return $newNodes;
    }

    function resourceAcquisitionTransformation( $functionName, &$node, $rule, $inputData,
                                                $outputName, $namespaceValue,
                                                $templateRoot, $viewDir, $viewValue,
                                                $matchFileArray, $acquisitionSpacing,
                                                &$resourceData )
    {
        $newNodes = [];
        $startRoot = '/' . $templateRoot . $viewDir;
        $viewFileMatchName = '/' . $templateRoot . '/' . $viewValue . '.tpl';
        $startRootLength = strlen( $startRoot );
        $matchList = [];
        $viewFileMatch = null;
        foreach ( $matchFileArray as $matchFile )
        {
            if ( !isset( $matchFile['template'] ) )
                continue;

            $path = $matchFile['template'];
            if ( substr( (string) $path, 0, $startRootLength ) == $startRoot and
                 $path[$startRootLength] == '/' )
            {
                $matchFile['match_part'] = substr( (string) $path, $startRootLength + 1 );
                $matchList[] = $matchFile;
            }
            if ( $path == $viewFileMatchName )
                $viewFileMatch = $matchFile;
        }
        $designKeysName = 'dKeys';
        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( !isset( \$$designKeysName ) )\n" .
                                                               "{\n" .
                                                               "    \$resH = \$tpl->resourceHandler( 'design' );\n" .
                                                               "    \$$designKeysName = \$resH->keys();\n" .
                                                               "}", ['spacing' => $acquisitionSpacing] );
        if ( isset( $rule["attribute_keys"] ) )
        {
            $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( !isset( \$" . $designKeysName . "Stack ) )\n" .
                                                                   "{\n" .
                                                                   "    \$" . $designKeysName . "Stack = array();\n" .
                                                                   "}\n" .
                                                                   "\$" . $designKeysName . "Stack[] = \$$designKeysName;",
                                                                   ['spacing' => $acquisitionSpacing] );
            foreach ( $rule["attribute_keys"] as $designKey => $attributeKeyArray )
            {
                $attributeAccessData = [];
                $attributeAccessData[] = eZTemplateNodeTool::createVariableElement( $outputName, $namespaceValue, eZTemplate::NAMESPACE_SCOPE_RELATIVE );
                foreach ( $attributeKeyArray as $attributeKey )
                {
                    $attributeAccessData[] = eZTemplateNodeTool::createAttributeLookupElement( $attributeKey );
                }
                $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $attributeAccessData, false,
                                                                      ['spacing' => 0], 'dKey' );
                $designKeyText = eZPHPCreator::variableText( $designKey, 0, 0, false );
                $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "\$" . $designKeysName . "[$designKeyText] = \$dKey;",
                                                                       ['spacing' => $acquisitionSpacing] );
                $newNodes[] = eZTemplateNodeTool::createVariableUnsetNode( 'dKey' );
            }
        }

        $attributeAccess = $rule["attribute_access"];

        $hasAttributeAccess = false;
        if ( is_array( $attributeAccess ) )
        {
            $hasAttributeAccess = count( $attributeAccess ) > 0;
            $attributeAccessCount = 0;
            foreach ( $attributeAccess as $attributeAccessEntries )
            {
                $attributeAccessData = $inputData;
                $spacing = $acquisitionSpacing;
                if ( $attributeAccessCount > 1 )
                {
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . " if ( !\$resourceFound )\n{\n", ['spacing' => $acquisitionSpacing] );
                    $spacing += 4;
                }
                else if ( $attributeAccessCount > 0 )
                {
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( !\$resourceFound )\n{\n", ['spacing' => $acquisitionSpacing] );
                    $spacing += 4;
                }
                foreach ( $attributeAccessEntries as $attributeAccessName )
                {
//                    $attributeAccessData[] = eZTemplateNodeTool::createCodePieceNode( "" . ( $resourceData['use-comments'] ? ( "/*TC:" . __LINE__ . "*/" ) : "" ) . "" );
                    $attributeAccessData[] = eZTemplateNodeTool::createAttributeLookupElement( $attributeAccessName );
                }
                $accessNodes = [];
                $accessNodes[] = eZTemplateNodeTool::createVariableNode( false, $attributeAccessData, false,
                                                                         ['spacing' => $spacing], 'attributeAccess' );

                $acquisitionNodes = [];
                $templateCounter = 0;
                $hasAcquisitionNodes = false;
                $matchLookupArray = [];
                foreach ( $matchList as $matchItem )
                {
                    $tmpAcquisitionNodes = [];
                    $matchPart = $matchItem['match_part'];
                    if ( preg_match( "/^(.+)\.tpl$/", (string) $matchPart, $matches ) )
                        $matchPart = $matches[1];
                    $code = "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( \$attributeAccess == '$matchPart' )\n{\n";
                    if ( $templateCounter > 0 )
                        $code = "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "" . $code;
                    $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( $code, ['spacing' => $spacing] );

                    $defaultMatchSpacing = $spacing;
                    $useArrayLookup = false;
                    $addFileResource = true;
                    if ( isset( $matchItem['custom_match'] ) )
                    {
                        $customSpacing = $spacing + 4;
                        $defaultMatchSpacing = $spacing + 4;
                        $matchCount = 0;
                        foreach ( $matchItem['custom_match'] as $customMatch )
                        {
			    if( is_null( $customMatch['conditions'] ) )
			    {
				$customMatch['conditions'] = [];
			    }
                            $matchConditionCount = is_countable($customMatch['conditions']) ? count( $customMatch['conditions'] ) : 0;
                            $code = '';
                            if ( $matchCount > 0 )
                            {
                                $code = "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "";
                            }
                            if ( $matchConditionCount > 0 )
                            {
                                if ( $matchCount > 0 )
                                    $code .= " ";
                                $code .= "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( ";
                            }
                            $ifLength = strlen( $code );
                            $conditionCount = 0;
                            if ( isset( $customMatch['conditions'] ) )
                            {
                                foreach ( $customMatch['conditions'] as $conditionName => $conditionValue )
                                {
                                    if ( $conditionCount > 0 )
                                        $code .= " and\n" . str_repeat( ' ', $ifLength );
                                    $conditionNameText = eZPHPCreator::variableText( $conditionName, 0 );
                                    $conditionValueText = eZPHPCreator::variableText( $conditionValue, 0 );

                                    $code .= "isset( \$" . $designKeysName . "[$conditionNameText] ) and ";
                                    if ( $conditionNameText == '"url_alias"' )
                                    {
                                        $code .= "( strpos(\$" . $designKeysName . "[$conditionNameText], $conditionValueText ) === 0 )";
                                    }
                                    else
                                    {
                                        $code .= "( is_array( \$" . $designKeysName . "[$conditionNameText] ) ? " .
                                                 "in_array( $conditionValueText, \$" . $designKeysName . "[$conditionNameText] ) : " .
                                                 "\$" . $designKeysName . "[$conditionNameText] == $conditionValueText )";
                                    }
                                    ++$conditionCount;
                                }
                            }
                            if ( $matchConditionCount > 0 )
                            {
                                $code .= " )\n";
                            }
                            if ( $matchConditionCount > 0 or $matchCount > 0 )
                            {
                                $code .= "{";
                            }
                            $matchFile = $customMatch['match_file'];
                            $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( $code, ['spacing' => $customSpacing] );
                            $hasAcquisitionNodes = true;
                            // If $matchFile is an array we cannot create a transformation for this entry
                            if ( is_array( $matchFile ) )
                                return false;
                            $tmpAcquisitionNodes[] = eZTemplateNodeTool::createResourceAcquisitionNode( '',
                                                                                                     $matchFile, $matchFile,
                                                                                                     eZTemplate::RESOURCE_FETCH, false,
                                                                                                     $node[4], ['spacing' => $customSpacing + 4],
                                                                                                     $rule['namespace'] );
                            if ( $matchConditionCount > 0 or $matchCount > 0 )
                            {
                                $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $customSpacing] );
                            }
                            ++$matchCount;
                            if ( $matchConditionCount == 0 )
                            {
                                $addFileResource = false;
                                break;
                            }
                        }
                        if ( $addFileResource )
                            $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . " \n{", ['spacing' => $customSpacing] );
                    }
                    else
                    {
                        $matchFile = $matchItem['base_dir'] . $matchItem['template'];
                        $matchLookupArray[$matchPart] = $matchFile;
                        $useArrayLookup = true;
                    }

                    if ( !$useArrayLookup )
                    {
                        if ( $addFileResource )
                        {
                            $matchFile = $matchItem['base_dir'] . $matchItem['template'];
                            $tmpAcquisitionNodes[] = eZTemplateNodeTool::createResourceAcquisitionNode( '',
                                                                                                        $matchFile, $matchFile,
                                                                                                        eZTemplate::RESOURCE_FETCH, false,
                                                                                                        $node[4], ['spacing' => $defaultMatchSpacing + 4],
                                                                                                        $rule['namespace'] );
                            $hasAcquisitionNodes = true;
                            if ( isset( $matchItem['custom_match'] ) )
                                $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $customSpacing] );
                        }
                        ++$templateCounter;
                        $tmpAcquisitionNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $spacing] );
                        $acquisitionNodes = array_merge( $acquisitionNodes, $tmpAcquisitionNodes );
                    }
                }

                if ( count( $matchLookupArray ) > 0 )
                {
                    $newNodes = array_merge( $newNodes, $accessNodes );
                    $accessNodes = [];
                    // If $matchFile is an array we cannot create a transformation for this entry
                    if ( is_array( $matchLookupArray ) )
                        return false;
                    $newNodes[] = eZTemplateNodeTool::createResourceAcquisitionNode( '',
                                                                                     $matchLookupArray, false,
                                                                                     eZTemplate::RESOURCE_FETCH, false,
                                                                                     $node[4], ['spacing' => $spacing],
                                                                                     $rule['namespace'], 'attributeAccess' );
                    if ( $hasAcquisitionNodes )
                    {
                        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "\n{", ['spacing' => $spacing] );
                        $newNodes[] = eZTemplateNodeTool::createSpacingIncreaseNode();
                    }
                }
                if ( $hasAcquisitionNodes )
                {
                    $newNodes = array_merge( $newNodes, $accessNodes, $acquisitionNodes );

                    if ( $attributeAccessCount > 0 )
                    {
                        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $acquisitionSpacing] );
                    }
                    ++$attributeAccessCount;
                }
                else if ( count( $matchLookupArray ) == 0 )
                {
                    $newNodes[] = eZTemplateNodeTool::createErrorNode( "Failed to load template",
                                                                       $functionName,
                                                                       eZTemplateNodeTool::extractFunctionNodePlacement( $node ),
                                                                       ['spacing' => $acquisitionSpacing] );
                }
                if ( count( $matchLookupArray ) > 0 and $hasAcquisitionNodes )
                {
                    $newNodes[] = eZTemplateNodeTool::createSpacingDecreaseNode();
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $spacing] );
                }
            }
        }
        if ( $viewFileMatch !== null )
        {
            $mainSpacing = 0;
            if ( $hasAttributeAccess )
            {
                $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "\n{\n", ['spacing' => $acquisitionSpacing] );
                $mainSpacing = 4;
            }
            $templateCounter = 0;


            $addFileResource = true;
            if ( isset( $viewFileMatch['custom_match'] ) )
            {
                $spacing = $mainSpacing + 4;
                $matchCount = 0;
                foreach ( $viewFileMatch['custom_match'] as $customMatch )
                {
                    $matchConditionCount = is_countable($customMatch['conditions']) ? count( $customMatch['conditions'] ) : 0;
                    $code = '';
                    if ( $matchCount > 0 )
                    {
                        $code = "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "";
                    }
                    if ( $matchConditionCount > 0 )
                    {
                        if ( $matchCount > 0 )
                            $code .= " ";
                        $code .= "if " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "( ";

                        $ifLength = strlen( $code );
                        $conditionCount = 0;

                        if ( is_array( $customMatch['conditions'] ) )
                        {
                            foreach ( $customMatch['conditions'] as $conditionName => $conditionValue )
                            {
                                if ( $conditionCount > 0 )
                                    $code .= " and\n" . str_repeat( ' ', $ifLength );
                                $conditionNameText = eZPHPCreator::variableText( $conditionName, 0 );
                                $conditionValueText = eZPHPCreator::variableText( $conditionValue, 0 );

                                $code .= "isset( \$" . $designKeysName . "[$conditionNameText] ) and ";
                                if ( $conditionNameText == '"url_alias"' )
                                {
                                    $code .= "( strpos(\$" . $designKeysName . "[$conditionNameText], $conditionValueText ) === 0 )";
                                }
                                else
                                {
                                    $code .= "( is_array( \$" . $designKeysName . "[$conditionNameText] ) ? " .
                                             "in_array( $conditionValueText, \$" . $designKeysName . "[$conditionNameText] ) : " .
                                             "\$" . $designKeysName . "[$conditionNameText] == $conditionValueText )";
                                }
                                ++$conditionCount;
                            }
                        }
                        $code .= " )\n";
                    }
                    if ( $matchConditionCount > 0 or $matchCount > 0 )
                    {
                        $code .= "{";
                    }
                    $matchFile = $customMatch['match_file'];
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( $code, ['spacing' => $acquisitionSpacing] );
                    // If $matchFile is an array we cannot create a transformation for this entry
                    if ( is_array( $matchFile ) )
                        return false;
                    $newNodes[] = eZTemplateNodeTool::createResourceAcquisitionNode( '',
                                                                                     $matchFile, $matchFile,
                                                                                     eZTemplate::RESOURCE_FETCH, false,
                                                                                     $node[4], ['spacing' => $spacing],
                                                                                     $rule['namespace'] );
                    if ( $matchConditionCount > 0 or $matchCount > 0 )
                    {
                        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $acquisitionSpacing] );
                    }
                    ++$matchCount;
                    if ( $matchConditionCount == 0 )
                    {
                        if ( $matchCount > 0 )
                            $addFileResource = false;
                        break;
                    }
                }
                if ( $addFileResource )
                    $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "else " . ( $resourceData['use-comments'] ? ( "/*OF:" . __LINE__ . "*/" ) : "" ) . "\n{", ['spacing' => $acquisitionSpacing] );
            }
            if ( $addFileResource )
            {
                $file = $viewFileMatch['base_dir'] . $viewFileMatch['template'];
                $newNodes[] = eZTemplateNodeTool::createResourceAcquisitionNode( '',
                                                                                 $file, $file,
                                                                                 eZTemplate::RESOURCE_FETCH, false,
                                                                                 $node[4], ['spacing' => $mainSpacing],
                                                                                 $rule['namespace'] );
            }
            if ( isset( $viewFileMatch['custom_match'] ) and $addFileResource )
                $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}", ['spacing' => $acquisitionSpacing] );

            if ( $hasAttributeAccess )
                $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "}\n", ['spacing' => $acquisitionSpacing] );
        }
        if ( isset( $rule["attribute_keys"] ) )
        {
            $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "\$$designKeysName = array_pop( \$" . $designKeysName . "Stack );",
                                                                   ['spacing' => $acquisitionSpacing] );
        }
        return $newNodes;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $uri = null;
        if ( !isset( $this->Rules[$functionName] ) )
        {
            $tpl->undefinedFunction( $functionName );
            return;
        }
        $rule = $this->Rules[$functionName];
        $template_dir = $rule["template_root"];
        $input_name = $rule["input_name"];
        $outCurrentNamespace = $currentNamespace;
        if ( isset( $rule['namespace'] ) )
        {
            $ruleNamespace = $rule['namespace'];
            if ( $ruleNamespace != '' )
            {
                if ( $outCurrentNamespace != '' )
                    $outCurrentNamespace .= ':' . $ruleNamespace;
                else
                    $outCurrentNamespace = $ruleNamespace;
            }
        }

        $params = $functionParameters;
        if ( !isset( $params[$input_name] ) )
        {
            $tpl->missingParameter( $functionName, $input_name );
            return;
        }

        $old_nspace = $rootNamespace;

        $input_var = $tpl->elementValue( $params[$input_name], $rootNamespace, $currentNamespace, $functionPlacement );
        if ( !is_object( $input_var ) )
        {
            $tpl->warning( $functionName, "Parameter $input_name is not an object", $functionPlacement );
            return;
        }

        $txt = "";
        $attributeAccess = $rule["attribute_access"];
        $view_mode = "";
        $view_dir = "";
        $view_var = null;
        $renderMode = false;
        if ( isset( $rule["render_mode"] ) )
        {
            $renderMode = $rule["render_mode"];
        }
        if ( isset( $params['render-mode'] ) )
        {
            $renderMode = $tpl->elementValue( $params['render-mode'], $rootNamespace, $currentNamespace, $functionPlacement );
        }
        if ( $renderMode )
            $view_dir .= "/render-$renderMode";
        if ( $rule["use_views"] )
        {
            $view_var = $rule["use_views"];
            if ( !isset( $params[$view_var] ) )
            {
                if ( !isset( $rule['optional_views'] ) or
                     !$rule['optional_views'] )
                    $tpl->warning( $functionName, "No view specified, skipping views" );
            }
            else
            {
                $view_mode = $tpl->elementValue( $params[$view_var], $rootNamespace, $currentNamespace, $functionPlacement );
                $view_dir .= "/" . $view_mode;
            }
        }

        $resourceKeys = false;
        if ( isset( $rule['attribute_keys'] ) )
        {
            $resourceKeys = [];
            foreach( $rule['attribute_keys'] as $attributeKey => $attributeSelection )
            {
                $resourceKeys[] = [$attributeKey, $tpl->variableAttribute( $input_var, $attributeSelection )];
            }
        }

        $triedFiles = [];
        $extraParameters = [];
        if ( $resourceKeys !== false )
            $extraParameters['ezdesign:keys'] = $resourceKeys;
        if ( is_array( $template_dir ) )
        {
            $templateRoot = $template_dir;
            $template_dir = '';
            if ( !isset( $templateRoot['type'] ) )
                $tpl->error( $functionName,
                             'No template root type defined' );
            else if ( $templateRoot['type'] == 'multi_match' )
            {
                if ( !isset( $templateRoot['attributes'] ) )
                    $tpl->error( $functionName,
                                 'No template root attributes defined' );
                else if ( !isset( $templateRoot['matches'] ) )
                    $tpl->error( $functionName,
                                 'No template root matches defined' );
                else
                {
                    $templateRootValue = $tpl->variableAttribute( $input_var, $templateRoot['attributes'] );
                    foreach ( $templateRoot['matches'] as $templateRootMatch )
                    {
                        if ( $templateRootMatch[0] == $templateRootValue )
                        {
                            $template_dir = $templateRootMatch[1];
                            if ( is_array( $template_dir ) )
                            {
                                $template_dir = $template_dir[0];
                                $attributeValues = [];
                                foreach ( $template_dir[1] as $templateDirAttributes )
                                {
                                    $attributeValues[] = $tpl->variableAttribute( $input_var, $templateDirAttributes );
                                }
                                $template_dir .= implode( '/', $attributeValues );
                            }
                            break;
                        }
                    }
                }
            }
            else
                $tpl->error( $functionName,
                             'Unknown template root type: ' . $templateRoot['type'] );
        }

        $resourceData = null;
        if ( is_array( $attributeAccess ) )
        {
            foreach( $attributeAccess as $attributeAccessArray )
            {
                $incfile = $tpl->variableAttribute( $input_var, $attributeAccessArray );
                $uri = "design:$template_dir$view_dir/$incfile.tpl";
                $resourceData = $tpl->loadURIRoot( $uri, false, $extraParameters );
                if ( $resourceData === null )
                    $triedFiles[] = $uri;
                else
                    break;
            }
            if ( $resourceData === null )
            {
                $uri = "design:$template_dir/$view_mode.tpl";
                $resourceData = $tpl->loadURIRoot( $uri, false, $extraParameters );
                if ( $resourceData === null )
                    $triedFiles[] = $uri;
            }
        }

        if ( $resourceData !== null )
        {
            $designUsedKeys = [];
            $designMatchedKeys = [];
            if ( isset( $extraParameters['ezdesign:used_keys'] ) )
                $designUsedKeys = $extraParameters['ezdesign:used_keys'];
            if ( isset( $extraParameters['ezdesign:matched_keys'] ) )
                $designMatchedKeys = $extraParameters['ezdesign:matched_keys'];
            if ( $outCurrentNamespace != '' )
                $designKeyNamespace = $outCurrentNamespace . ':DesignKeys';
            else
                $designKeyNamespace = 'DesignKeys';

            $sub_text = "";
            $setVariableArray = [];
            $tpl->setVariable( $rule["output_name"], $input_var, $outCurrentNamespace, true );
            $setVariableArray[] = $rule["output_name"];
            // Set design keys
            $tpl->setVariable( 'used', $designUsedKeys, $designKeyNamespace );
            $tpl->setVariable( 'matched', $designMatchedKeys, $designKeyNamespace );
            // Set function parameters
            foreach ( array_keys( $params ) as $paramName )
            {
                if ( $paramName == $input_name or
                     $paramName == $view_var )
                {
                    continue;
                }

                $tpl->setVariable(
                    $paramName,
                    $tpl->elementValue( $params[$paramName], $old_nspace, $currentNamespace, $functionPlacement ),
                    $outCurrentNamespace,
                    true
                );
                $setVariableArray[] = $paramName;
            }
            // Set constant variables
            if ( isset( $rule['constant_template_variables'] ) )
            {
                foreach ( $rule['constant_template_variables'] as $constantTemplateVariableKey => $constantTemplateVariableValue )
                {
                    if ( $constantTemplateVariableKey == $input_name or
                         $constantTemplateVariableKey == $view_var or
                         $tpl->hasVariable( $constantTemplateVariableKey, $currentNamespace ) )
                        continue;
                    $tpl->setVariable( $constantTemplateVariableKey, $constantTemplateVariableValue, $outCurrentNamespace, true );
                    $setVariableArray[] = $constantTemplateVariableKey;
                }
            }

            $templateCompilationUsed = false;
            if ( $resourceData['compiled-template'] )
            {
                if ( $tpl->executeCompiledTemplate( $resourceData, $textElements, $outCurrentNamespace, $outCurrentNamespace, $extraParameters ) )
                    $templateCompilationUsed = true;
            }
            if ( !$templateCompilationUsed and
                 $resourceData['root-node'] )
            {
                $tpl->process( $resourceData['root-node'], $sub_text, $outCurrentNamespace, $outCurrentNamespace );
                $tpl->setIncludeOutput( $uri, $sub_text );

                $textElements[] = $sub_text;
            }
            foreach ( $setVariableArray as $setVariableName )
            {
                $tpl->unsetVariable( $setVariableName, $outCurrentNamespace );
            }
        }
        else
        {
            $tpl->warning( $functionName,
                           "None of the templates " . implode( ", ", $triedFiles ) .
                           " could be found" );
        }
    }

    function hasChildren()
    {
        return false;
    }
}

?>
