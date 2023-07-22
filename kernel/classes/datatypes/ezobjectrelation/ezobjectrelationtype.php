<?php
/**
 * File containing the eZObjectRelationType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZObjectRelationType ezobjectrelationtype.php
  \ingroup eZDatatype
  \brief A content datatype which handles object relations

*/

class eZObjectRelationType extends eZDataType
{
    final public const DATA_TYPE_STRING = "ezobjectrelation";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Object relation", 'Datatype name' ),
                           ['serialize_supported' => true] );
    }

    public function isRelationType()
    {
        return true;
    }

    /*!
     Initializes the class attribute with some data.
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_int" );
            $contentObjectAttribute->setAttribute( "data_int", $dataText );
        }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $postVariableName = $base . "_data_object_relation_id_" . $contentObjectAttribute->attribute( "id" );
        if ( $http->hasPostVariable( $postVariableName ) )
        {
            $relatedObjectID = $http->postVariable( $postVariableName );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $contentObjectAttribute->validateIsRequired() and $relatedObjectID == 0 )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Missing objectrelation input.' ) );
                return eZInputValidator::STATE_INVALID;
            }
        }
        else if ( $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Missing objectrelation input.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $postVariableName = $base . "_data_object_relation_id_" . $contentObjectAttribute->attribute( "id" );
        $haveData = false;
        if ( $http->hasPostVariable( $postVariableName ) )
        {
            $relatedObjectID = $http->postVariable( $postVariableName );
            if ( $relatedObjectID == '' )
                $relatedObjectID = null;
            $contentObjectAttribute->setAttribute( 'data_int', $relatedObjectID );
            $haveData = true;
        }
        $fuzzyMatchVariableName = $base . "_data_object_relation_fuzzy_match_" . $contentObjectAttribute->attribute( "id" );
        if ( $http->hasPostVariable( $fuzzyMatchVariableName ) )
        {
            $trans = eZCharTransform::instance();

            $fuzzyMatchText = trim( (string) $http->postVariable( $fuzzyMatchVariableName ) );
            if ( $fuzzyMatchText != '' )
            {
                $fuzzyMatchText = $trans->transformByGroup( $fuzzyMatchText, 'lowercase' );
                $classAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
                if ( $classAttribute )
                {
                    $classContent = $classAttribute->content();
                    if ( $classContent['default_selection_node'] )
                    {
                        $nodeID = $classContent['default_selection_node'];
                        $nodeList = eZContentObjectTreeNode::subTreeByNodeID( ['Depth' => 1], $nodeID );
                        $lastDiff = false;
                        $matchObjectID = false;
                        foreach ( $nodeList as $node )
                        {
                            $name = $trans->transformByGroup( trim( (string) $node->attribute( 'name' ) ), 'lowercase' );
                            $diff = $this->fuzzyTextMatch( $name, $fuzzyMatchText );
                            if ( $diff === false )
                                continue;
                            if ( $diff == 0 )
                            {
                                $matchObjectID = $node->attribute( 'contentobject_id' );
                                break;
                            }
                            if ( $lastDiff === false or
                                 $diff < $lastDiff )
                            {
                                $lastDiff = $diff;
                                $matchObjectID = $node->attribute( 'contentobject_id' );
                            }
                        }
                        if ( $matchObjectID !== false )
                        {
                            $contentObjectAttribute->setAttribute( 'data_int', $matchObjectID );
                            $haveData = true;
                        }
                    }
                }
            }
        }
        return $haveData;
    }

    /*!
     \private
     \return a number of how near \a $match is to \a $text, the lower the better and 0 is a perfect match.
     \return \c false if it does not match
    */
    function fuzzyTextMatch( $text, $match )
    {
        $pos = strpos( (string) $text, (string) $match );
        if ( $pos !== false )
        {
            $diff = strlen( (string) $text ) - ( strlen( (string) $match ) + $pos );
            $diff += $pos;
            return $diff;
        }
        return false;
    }

    function storeClassAttributeContent( $classAttribute, $content )
    {
        if ( is_array( $content ) )
        {
            $doc = static::createClassDOMDocument($content);
            static::storeClassDOMDocument($doc, $classAttribute);
            return true;
        }
        return false;
    }

    static function storeClassDOMDocument( $doc, $classAttribute )
    {
        $docText = self::domString( $doc );
        $classAttribute->setAttribute( 'data_text5', $docText );
    }

    static function storeObjectDOMDocument( $doc, $objectAttribute )
    {
        $docText = self::domString( $doc );
        $objectAttribute->setAttribute( 'data_text', $docText );
    }

    /*!
     \static
     \return the XML structure in \a $domDocument as text.
             It will take of care of the necessary charset conversions
             for content storage.
    */
    static function domString( $domDocument )
    {
        $ini = eZINI::instance();
        $xmlCharset = $ini->variable( 'RegionalSettings', 'ContentXMLCharset' );
        if ( $xmlCharset == 'enabled' )
        {
            $charset = eZTextCodec::internalCharset();
        }
        else if ( $xmlCharset == 'disabled' )
            $charset = true;
        else
            $charset = $xmlCharset;
        if ( $charset !== true )
        {
            $charset = eZCharsetInfo::realCharsetCode( $charset );
        }
        $domString = $domDocument->saveXML();
        return $domString;
    }

    static function createClassDOMDocument( $content )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'related-object' );
        $constraints = $doc->createElement( 'constraints' );
        foreach ( $content['class_constraint_list'] as $constraintClassIdentifier )
        {
            unset( $constraintElement );
            $constraintElement = $doc->createElement( 'allowed-class' );
            $constraintElement->setAttribute( 'contentclass-identifier', $constraintClassIdentifier );
            $constraints->appendChild( $constraintElement );
        }
        $root->appendChild( $constraints );
        $doc->appendChild( $root );
        return $doc;
    }

    /*!
     Stores relation to the ezcontentobject_link table
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $contentClassAttributeID = $contentObjectAttribute->ContentClassAttributeID;
        $contentObjectID = $contentObjectAttribute->ContentObjectID;
        $contentObjectVersion = $contentObjectAttribute->Version;
        $languageCode = $contentObjectAttribute->attribute( 'language_code' );

        /** @var eZContentObject */
        $contentObject = $contentObjectAttribute->object();

        if ( $contentObjectAttribute->ID !== null )
        {
            // cleanup previous relations
            $contentObject->removeContentObjectRelation( false, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );

            // if translatable, we need to re-add the relations for other languages of (previously) published version.
            $publishedVersionNo = $contentObject->publishedVersion();
            if ( $contentObjectAttribute->contentClassAttributeCanTranslate() && $publishedVersionNo > 0 )
            {
                $existingRelations = [];

                // get published translations of this attribute
                $pubAttribute = eZContentObjectAttribute::fetch($contentObjectAttribute->ID, $publishedVersionNo );
                if ( $pubAttribute )
                {
                    foreach( $pubAttribute->fetchAttributeTranslations() as $attributeTranslation )
                    {
                        // skip if language is the one being saved
                        if ( $attributeTranslation->LanguageCode === $languageCode )
                            continue;

                        if ( $attributeTranslation->attribute( 'data_int' ) )
                            $existingRelations[$attributeTranslation->LanguageCode] = (int)$attributeTranslation->attribute( 'data_int' );
                    }
                }

                // fetch existing attribute translations for current editing version
                foreach( $contentObjectAttribute->fetchAttributeTranslations() as $attributeTranslation )
                {
                    if ( $attributeTranslation->LanguageCode === $languageCode )
                        continue;

                    if ( $attributeTranslation->attribute( 'data_int' ) )
                        $existingRelations[$attributeTranslation->LanguageCode] = (int)$attributeTranslation->attribute( 'data_int' );
                }

                // re-add existing or new relations for other languages
                foreach( array_unique($existingRelations) as $existingObjectId )
                {
                    $contentObject->addContentObjectRelation( $existingObjectId, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );
                }
            }
        }

        $objectID = $contentObjectAttribute->attribute( 'data_int' );
        if ( $objectID )
        {
            $contentObject->addContentObjectRelation( $objectID, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );
        }
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $selectionTypeName = 'ContentClass_ezobjectrelation_selection_type_' . $classAttribute->attribute( 'id' );
        $state = eZInputValidator::STATE_ACCEPTED;
        if ( $http->hasPostVariable( $selectionTypeName ) )
        {
            $selectionType = $http->postVariable( $selectionTypeName );
            if ( $selectionType < 0 and
                 $selectionType > 2 )
            {
                $state = eZInputValidator::STATE_INVALID;
            }
        }
        return $state;
    }

    function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $selectionTypeName = 'ContentClass_ezobjectrelation_selection_type_' . $classAttribute->attribute( 'id' );
        $content = $classAttribute->content();
        $postVariable = 'ContentClass_ezobjectrelation_class_list_' . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $postVariable ) )
        {
            $constrainedList = $http->postVariable( $postVariable );
            $constrainedClassList = [];
            foreach ( $constrainedList as $constraint )
            {
                if ( trim( (string) $constraint ) != '' )
                    $constrainedClassList[] = $constraint;
            }
            $content['class_constraint_list'] = $constrainedClassList;
            $hasData = true;
        }
        $hasData = false;
        if ( $http->hasPostVariable( $selectionTypeName ) )
        {
            $selectionType = $http->postVariable( $selectionTypeName );
            $content['selection_type'] = $selectionType;
            $hasData = true;
        }
        $helperName = 'ContentClass_ezobjectrelation_selection_fuzzy_match_helper_' . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $helperName ) )
        {
            $fuzzyMatchName = 'ContentClass_ezobjectrelation_selection_fuzzy_match_' . $classAttribute->attribute( 'id' );
            $content['fuzzy_match'] = false;
            $hasData = true;
            if ( $http->hasPostVariable( $fuzzyMatchName ) )
            {
                $content['fuzzy_match'] = true;
            }
        }
        if ( $hasData )
        {
            $classAttribute->setContent( $content );
            return true;
        }
        return false;
    }

    function initializeClassAttribute( $classAttribute )
    {
        $xmlText = $classAttribute->attribute( 'data_text5' );
        if ( trim( (string) $xmlText ) == '' )
        {
            $content = $this->defaultClassAttributeContent();
            return $this->storeClassAttributeContent( $classAttribute, $content );
        }
    }

    function preStoreClassAttribute( $classAttribute, $version )
    {
        $content = $classAttribute->content();
        $classAttribute->setAttribute( 'data_int1', $content['selection_type'] );
        $classAttribute->setAttribute( 'data_int2', $content['default_selection_node'] );
        $classAttribute->setAttribute( 'data_int3', $content['fuzzy_match'] );

        $xmlContentArray = [];
        $defaultClassAttributeContent = $this->defaultClassAttributeContent();
        foreach( $content as $xmlKey => $xmlContent )
        {
            if( isset( $defaultClassAttributeContent[$xmlKey] ) )
            {
                $xmlContentArray[$xmlKey] = $xmlContent;
            }
        }
        $this->storeClassAttributeContent( $classAttribute, $xmlContentArray );
    }

    /*!
     \private
     Delete the old version from ezcontentobject_link if count of translations > 1
    */
    function removeContentObjectRelation( $contentObjectAttribute )
    {
        $obj = $contentObjectAttribute->object();
        $atrributeTrans = $contentObjectAttribute->fetchAttributeTranslations( );
        // Check if current relation exists in ezcontentobject_link
        foreach ( $atrributeTrans as $attrTarns )
        {
            if ( $attrTarns->attribute( 'id' ) != $contentObjectAttribute->attribute( 'id' ) )
                if ( $attrTarns->attribute( 'data_int' ) == $contentObjectAttribute->attribute( 'data_int' ) )
                     return;
        }

        //get eZContentObjectVersion
        $currVerobj = $obj->currentVersion();
        // get array of ezcontentobjecttranslations
        $transList = $currVerobj->translations( false );
        // get count of LanguageCode in transList
        $countTsl = is_countable($transList) ? count( $transList ) : 0;
        // Delete the old version from ezcontentobject_link if count of translations > 1
        if ( $countTsl > 1 )
        {
            $objectID = $contentObjectAttribute->attribute( "data_int" );
            $contentClassAttributeID = $contentObjectAttribute->ContentClassAttributeID;
            $contentObjectID = $contentObjectAttribute->ContentObjectID;
            $contentObjectVersion = $contentObjectAttribute->Version;
            eZContentObject::fetch( $contentObjectID )->removeContentObjectRelation( $objectID, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );
        }
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case "set_object_relation" :
            {
                if ( $http->hasPostVariable( 'BrowseActionName' ) and
                          $http->postVariable( 'BrowseActionName' ) == ( 'AddRelatedObject_' . $contentObjectAttribute->attribute( 'id' ) ) and
                          $http->hasPostVariable( "SelectedObjectIDArray" ) )
                {
                    if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                    {
                        $selectedObjectArray = $http->hasPostVariable( "SelectedObjectIDArray" );
                        $selectedObjectIDArray = $http->postVariable( "SelectedObjectIDArray" );

                        // Delete the old version from ezcontentobject_link if count of translations > 1
                        $this->removeContentObjectRelation( $contentObjectAttribute );

                        $objectID = $selectedObjectIDArray[0];
                        $contentObjectAttribute->setAttribute( 'data_int', $objectID );
                        $contentObjectAttribute->store();
                    }
                }
            } break;

            case "browse_object" :
            {
                $module = $parameters['module'];
                $redirectionURI = $parameters['current-redirection-uri'];
                $ini = eZINI::instance( 'content.ini' );

                $browseParameters = ['action_name' => 'AddRelatedObject_' . $contentObjectAttribute->attribute( 'id' ), 'type' =>  'AddRelatedObjectToDataType', 'browse_custom_action' => ['name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_set_object_relation]', 'value' => $contentObjectAttribute->attribute( 'id' )], 'persistent_data' => ['HasObjectInput' => 0], 'from_page' => $redirectionURI];
                $browseTypeINIVariable = $ini->variable( 'ObjectRelationDataTypeSettings', 'ClassAttributeStartNode' );
                foreach( $browseTypeINIVariable as $value )
                {
                    [$classAttributeID, $type] = explode( ';',(string) $value );
                    if ( $classAttributeID == $contentObjectAttribute->attribute( 'contentclassattribute_id' ) && strlen( $type ) > 0 )
                    {
                        $browseParameters['type'] = $type;
                        break;
                    }
                }

                $nodePlacementName = $parameters['base_name'] . '_browse_for_object_start_node';
                if ( $http->hasPostVariable( $nodePlacementName ) )
                {
                    $nodePlacement = $http->postVariable( $nodePlacementName );
                    if ( isset( $nodePlacement[$contentObjectAttribute->attribute( 'id' )] ) )
                        $browseParameters['start_node'] = eZContentBrowse::nodeAliasID( $nodePlacement[$contentObjectAttribute->attribute( 'id' )] );
                }

                // Fetch the list of "allowed" classes .
                // A user can select objects of only those allowed classes when browsing.
                $classAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
                $classContent   = $classAttribute->content();
                if ( isset( $classContent['class_constraint_list'] ) )
                {
                    $classConstraintList = $classContent['class_constraint_list'];
                }
                else
                {
                    $classConstraintList = [];
                }

                if ( (is_countable($classConstraintList) ? count($classConstraintList) : 0) > 0 )
                {
                    $browseParameters['class_array'] = $classConstraintList;
                }

                eZContentBrowse::browse( $module,
                                         $browseParameters );
            } break;

            case "remove_object" :
            {
                // Delete the old version from ezcontentobject_link if count of translations > 1
                $this->removeContentObjectRelation( $contentObjectAttribute );

                $contentObjectAttribute->setAttribute( 'data_int', 0 );
                $contentObjectAttribute->store();
            } break;

            default :
            {
                eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZObjectRelationType" );
            } break;
        }
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $objectID = $contentObjectAttribute->attribute( "data_int" );
        if ( $objectID != 0 )
            $object = eZContentObject::fetch( $objectID );
        else
            $object = null;
        return $object;
    }

    static function parseXML( $xmlText )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( $xmlText );
        return $dom;
    }

    function defaultClassAttributeContent()
    {
        return ['class_constraint_list' => []];
    }

    /*!
     Sets \c grouped_input to \c true when browse mode is active or
     a dropdown with a fuzzy match is used.
    */
    function objectDisplayInformation( $objectAttribute, $mergeInfo = false )
    {
        $classAttribute = $objectAttribute->contentClassAttribute();
        $content = $this->classAttributeContent( $classAttribute );
        $editGrouped = ( $content['selection_type'] == 0 or
                         ( $content['selection_type'] == 1 and $content['fuzzy_match'] ) );

        $info = ['edit' => ['grouped_input' => $editGrouped], 'collection' => ['grouped_input' => $editGrouped]];
        return eZDataType::objectDisplayInformation( $objectAttribute, $info );
    }

    function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }

    function sortKeyType()
    {
        return 'int';
    }

    function classAttributeContent( $classObjectAttribute )
    {
        $selectionType = $classObjectAttribute->attribute( "data_int1" );
        $defaultSelectionNode = $classObjectAttribute->attribute( "data_int2" );
        $fuzzyMatch = $classObjectAttribute->attribute( "data_int3" );

        $attributeContent = ['selection_type' => $selectionType, 'default_selection_node' => $defaultSelectionNode, 'fuzzy_match' => $fuzzyMatch];

        $attributeXMLContent = $this->defaultClassAttributeContent();
        $xmlText = $classObjectAttribute->attribute( 'data_text5' );
        if ( trim( (string) $xmlText ) != '' )
        {
            $doc = static::parseXML($xmlText);
            $attributeXMLContent = $this->createClassContentStructure( $doc );
        }
        return array_merge( $attributeContent, $attributeXMLContent );

    }

    function deleteNotVersionedStoredClassAttribute( eZContentClassAttribute $classAttribute )
    {
        eZContentObjectAttribute::removeRelationsByContentClassAttributeId( $classAttribute->attribute( 'id' ) );
    }

    function createClassContentStructure( $doc )
    {
        $content = $this->defaultClassAttributeContent();
        $root = $doc->documentElement;
        $constraints = $root->getElementsByTagName( 'constraints' )->item( 0 );
        if ( $constraints )
        {
            $allowedClassList = $constraints->getElementsByTagName( 'allowed-class' );
            foreach( $allowedClassList as $allowedClass )
            {
                $content['class_constraint_list'][] = $allowedClass->getAttribute( 'contentclass-identifier' );
            }
        }
        return $content;
    }

    function customClassAttributeHTTPAction( $http, $action, $classAttribute )
    {
        switch ( $action )
        {
            case 'browse_for_selection_node':
            {
                $module = $classAttribute->currentModule();
                $customActionName = 'CustomActionButton[' . $classAttribute->attribute( 'id' ) . '_browsed_for_selection_node]';
                eZContentBrowse::browse( $module,
                                         ['action_name' => 'SelectObjectRelationNode', 'content' => ['contentclass_id' => $classAttribute->attribute( 'contentclass_id' ), 'contentclass_attribute_id' => $classAttribute->attribute( 'id' ), 'contentclass_version' => $classAttribute->attribute( 'version' ), 'contentclass_attribute_identifier' => $classAttribute->attribute( 'identifier' )], 'persistent_data' => [$customActionName => '', 'ContentClassHasInput' => false], 'description_template' => 'design:class/datatype/browse_objectrelation_placement.tpl', 'from_page' => $module->currentRedirectionURI()] );
            } break;
            case 'browsed_for_selection_node':
            {
                $nodeSelection = eZContentBrowse::result( 'SelectObjectRelationNode' );
                if ( (is_countable($nodeSelection) ? count( $nodeSelection ) : 0) > 0 )
                {
                    $nodeID = $nodeSelection[0];
                    $content = $classAttribute->content();
                    $content['default_selection_node'] = $nodeID;
                    $classAttribute->setContent( $content );
                }
            } break;
            case 'disable_selection_node':
            {
                $content = $classAttribute->content();
                $content['default_selection_node'] = false;
                $classAttribute->setContent( $content );
            } break;
            default:
            {
                eZDebug::writeError( "Unknown objectrelationlist action '$action'", __METHOD__ );
            } break;
        }
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $object = $this->objectAttributeContent( $contentObjectAttribute );
        if ( $object )
        {
            // Does the related object exist in the same language as the current content attribute ?
            if ( in_array( $contentObjectAttribute->attribute( 'language_code' ), $object->attribute( 'current' )->translationList( false, false ) ) )
            {
                $attributes = $object->attribute( 'current' )->contentObjectAttributes( $contentObjectAttribute->attribute( 'language_code' ) );
            }
            else
            {
                $attributes = $object->contentObjectAttributes();
            }

            return eZContentObjectAttribute::metaDataArray( $attributes, true );
        }
        return false;
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        if ( !is_numeric( $string ) || !eZContentObject::fetch( $string ) )
            return false;

        $contentObjectAttribute->setAttribute( 'data_int', $string );
        return true;
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     Returns the content of the string for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $object = $this->objectAttributeContent( $contentObjectAttribute );
        if ( $object )
        {
            return $object->attribute( 'name' );
        }
        return false;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $object = $this->objectAttributeContent( $contentObjectAttribute );
        if ( $object )
            return true;
        return false;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $content = $classAttribute->content();
        $dom = $attributeParametersNode->ownerDocument;
        $selectionTypeNode = $dom->createElement( 'selection-type' );
        $selectionTypeNode->setAttribute( 'id', $content['selection_type'] );
        $attributeParametersNode->appendChild( $selectionTypeNode );
        $fuzzyMatchNode = $dom->createElement( 'fuzzy-match' );
        $fuzzyMatchNode->setAttribute( 'id', $content['fuzzy_match'] );
        $attributeParametersNode->appendChild( $fuzzyMatchNode );
        if ( $content['default_selection_node'] )
        {
            $defaultSelectionNode = $dom->createElement( 'default-selection' );
            $defaultSelectionNode->setAttribute( 'node-id', $content['default_selection_node'] );
            $attributeParametersNode->appendChild( $defaultSelectionNode );
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $content = $classAttribute->content();
        $selectionTypeNode = $attributeParametersNode->getElementsByTagName( 'selection-type' )->item( 0 );
        $content['selection_type'] = 0;
        if ( $selectionTypeNode )
            $content['selection_type'] = $selectionTypeNode->getAttribute( 'id' );

        $fuzzyMatchNode = $attributeParametersNode->getElementsByTagName( 'fuzzy-match' )->item( 0 );
        $content['fuzzy_match'] = false;
        if ( $fuzzyMatchNode )
            $content['fuzzy_match'] = $fuzzyMatchNode->getAttribute( 'id' );

        $defaultSelectionNode = $attributeParametersNode->getElementsByTagName( 'default-selection' )->item( 0 );
        $content['default_selection_node'] = false;
        if ( $defaultSelectionNode )
            $content['default_selection_node'] = $defaultSelectionNode->getAttribute( 'node-id' );

        $classAttribute->setContent( $content );
        $classAttribute->store();
    }

    /*!
     Export related object's remote_id.
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $relatedObjectID = $objectAttribute->attribute( 'data_int' );

        if ( $relatedObjectID !== null )
        {
            $relatedObject = eZContentObject::fetch( $relatedObjectID );
            if ( !$relatedObject )
            {
                eZDebug::writeNotice( 'Related object with ID: ' . $relatedObjectID . ' does not exist.' );
            }
            else
            {
                $relatedObjectRemoteID = $relatedObject->attribute( 'remote_id' );
                $dom = $node->ownerDocument;
                $relatedObjectRemoteIDNode = $dom->createElement( 'related-object-remote-id' );
                $relatedObjectRemoteIDNode->appendChild( $dom->createTextNode( $relatedObjectRemoteID ) );
                $node->appendChild( $relatedObjectRemoteIDNode );
            }
        }

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $relatedObjectRemoteIDNode = $attributeNode->getElementsByTagName( 'related-object-remote-id' )->item( 0 );
        $relatedObjectID = null;

        if ( $relatedObjectRemoteIDNode )
        {
            $relatedObjectRemoteID = $relatedObjectRemoteIDNode->textContent;
            $object = eZContentObject::fetchByRemoteID( $relatedObjectRemoteID );
            if ( $object )
            {
                $relatedObjectID = $object->attribute( 'id' );
            }
            else
            {
                // store remoteID so it can be used in postUnserialize
                $objectAttribute->setAttribute( 'data_text', $relatedObjectRemoteID );
            }
        }

        $objectAttribute->setAttribute( 'data_int', $relatedObjectID );
    }

    function postUnserializeContentObjectAttribute( $package, $objectAttribute )
    {
        $attributeChanged = false;
        $relatedObjectID = $objectAttribute->attribute( 'data_int' );

        if ( !$relatedObjectID )
        {
            // Restore cross-relations using preserved remoteID
            $relatedObjectRemoteID = $objectAttribute->attribute( 'data_text' );
            if ( $relatedObjectRemoteID)
            {
                $object = eZContentObject::fetchByRemoteID( $relatedObjectRemoteID );
                $relatedObjectID = ( $object !== null ) ? $object->attribute( 'id' ) : null;

                if ( $relatedObjectID )
                {
                    $objectAttribute->setAttribute( 'data_int', $relatedObjectID );
                    $attributeChanged = true;
                }
            }
        }

        return $attributeChanged;
    }

    /*!
     Removes objects with given ID from the relations list
    */
    function removeRelatedObjectItem( $contentObjectAttribute, $objectID )
    {
        $contentObjectAttribute->setAttribute( "data_int", null );
        return true;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /// \privatesection
}

eZDataType::register( eZObjectRelationType::DATA_TYPE_STRING, "eZObjectRelationType" );

?>
