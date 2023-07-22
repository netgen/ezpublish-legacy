<?php
/**
 * File containing the eZXMLSchema class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZXMLSchema
{
    public $Schema = ['section'   => ['blockChildrenAllowed' => ['header', 'paragraph', 'section'], 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => false, 'attributes' => ['xmlns:image', 'xmlns:xhtml', 'xmlns:custom', 'xmlns:tmp']], 'embed'     => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => false, 'childrenRequired' => null, 'isInline' => true, 'attributes' => ['object_id', 'node_id', 'show_path', 'size', 'align', 'view', 'xhtml:id', 'class', 'target'], 'attributesDefaults' => ['align' => '', 'view' => 'embed', 'class' => '']], 'embed-inline' => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => false, 'childrenRequired' => null, 'isInline' => true, 'attributes' => ['object_id', 'node_id', 'show_path', 'size', 'align', 'view', 'xhtml:id', 'class', 'target'], 'attributesDefaults' => ['align' => '', 'view' => 'embed-inline', 'class' => '']], 'table'     => ['blockChildrenAllowed' => ['tr'], 'inlineChildrenAllowed' => false, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class', 'width', 'border', 'align']], 'tr'        => ['blockChildrenAllowed' => ['td', 'th'], 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => false, 'attributes' => ['class']], 'td'        => ['blockChildrenAllowed' => ['header', 'paragraph', 'section', 'table'], 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => false, 'attributes' => ['class', 'align', 'xhtml:width', 'xhtml:colspan', 'xhtml:rowspan']], 'th'        => ['blockChildrenAllowed' => ['header', 'paragraph', 'section', 'table'], 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => false, 'attributes' => ['class', 'align', 'xhtml:width', 'xhtml:colspan', 'xhtml:rowspan']], 'ol'        => ['blockChildrenAllowed' => ['li'], 'inlineChildrenAllowed' => false, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class']], 'ul'        => ['blockChildrenAllowed' => ['li'], 'inlineChildrenAllowed' => false, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class']], 'li'        => ['blockChildrenAllowed' => ['paragraph'], 'inlineChildrenAllowed' => false, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class']], 'header'    => ['blockChildrenAllowed' => ['line'], 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class', 'anchor_name', 'align']], 'paragraph' => ['blockChildrenAllowed' => ['line', 'link', 'embed', 'table', 'ol', 'ul', 'custom', 'literal'], 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class', 'align']], 'line'      => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => false, 'attributes' => false], 'literal'   => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => ['#text'], 'childrenRequired' => true, 'isInline' => false, 'attributes' => ['class']], 'strong'    => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => true, 'attributes' => ['class']], 'emphasize' => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => true, 'attributes' => ['class']], 'link'      => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => true, 'childrenRequired' => true, 'isInline' => true, 'attributes' => ['class', 'xhtml:id', 'target', 'xhtml:title', 'object_id', 'node_id', 'show_path', 'anchor_name', 'url_id', 'id', 'view'], 'attributesDefaults' => ['target' => '_self']], 'anchor'    => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => true, 'attributes' => ['name']], 'custom'    => ['blockChildrenAllowed' => true, 'inlineChildrenAllowed' => true, 'childrenRequired' => false, 'isInline' => null, 'attributes' => ['name', 'align']], '#text'     => ['blockChildrenAllowed' => false, 'inlineChildrenAllowed' => false, 'childrenRequired' => false, 'isInline' => true, 'attributes' => false]];

    /**
     * Constructor
     */
    public function __construct()
    {
        $ini = eZINI::instance( 'content.ini' );

        // Get inline custom tags list
        $this->Schema['custom']['isInline'] = $ini->variable( 'CustomTagSettings', 'IsInline' );
        if ( !is_array( $this->Schema['custom']['isInline'] ) )
            $this->Schema['custom']['isInline'] = [];

        $this->Schema['custom']['tagList'] = $ini->variable( 'CustomTagSettings', 'AvailableCustomTags' );
        if ( !is_array( $this->Schema['custom']['tagList'] ) )
            $this->Schema['custom']['tagList'] = [];

        $eZPublishVersion = eZPublishSDK::majorVersion() + eZPublishSDK::minorVersion() * 0.1;

        // Get all tags available classes list
        foreach( array_keys( $this->Schema ) as $tagName )
        {
            if ( $ini->hasVariable( $tagName, 'AvailableClasses' ) )
            {
                $avail = $ini->variable( $tagName, 'AvailableClasses' );
                if ( is_array( $avail ) && count( $avail ) )
                    $this->Schema[$tagName]['classesList'] = $avail;
                else
                    $this->Schema[$tagName]['classesList'] = [];
            }
            else
                $this->Schema[$tagName]['classesList'] = [];
        }


        // Fix for empty paragraphs setting
        $allowEmptyParagraph = $ini->variable( 'paragraph', 'AllowEmpty' );
        $this->Schema['paragraph']['childrenRequired'] = $allowEmptyParagraph == 'true' ? false : true;

        // Get all tags custom attributes list
        $ini = eZINI::instance( 'content.ini' );
        foreach( array_keys( $this->Schema ) as $tagName )
        {
            if ( $tagName == 'custom' )
            {
                // Custom attributes of custom tags
                foreach( $this->Schema['custom']['tagList'] as $customTagName )
                {
                    if ( $ini->hasVariable( $customTagName, 'CustomAttributes' ) )
                    {
                        $avail = $ini->variable( $customTagName, 'CustomAttributes' );
                        if ( is_array( $avail ) && count( $avail ) )
                            $this->Schema['custom']['customAttributes'][$customTagName] = $avail;
                        else
                            $this->Schema['custom']['customAttributes'][$customTagName] = [];
                    }
                    else
                        $this->Schema['custom']['customAttributes'][$customTagName] = [];
                }
            }
            else
            {
                // Custom attributes of regular tags
                if ( $ini->hasVariable( $tagName, 'CustomAttributes' ) )
                {
                    $avail = $ini->variable( $tagName, 'CustomAttributes' );
                    if ( is_array( $avail ) && count( $avail ) )
                        $this->Schema[$tagName]['customAttributes'] = $avail;
                    else
                        $this->Schema[$tagName]['customAttributes'] = [];
                }
                else
                    $this->Schema[$tagName]['customAttributes'] = [];
            }
        }
    }

    /**
     * Returns a shared instance of the eZXMLSchema class.
     *
     * @return eZXMLSchema
     */
    static function instance()
    {
        if ( empty( $GLOBALS["eZXMLSchemaGlobalInstance"] ) )
        {
            $GLOBALS["eZXMLSchemaGlobalInstance"] = new eZXMLSchema();
        }

        return $GLOBALS["eZXMLSchemaGlobalInstance"];
    }

    // Determines if the tag is inline
    function isInline( $element )
    {
        if ( is_string( $element ) )
            $elementName = $element;
        else
            $elementName = $element->nodeName;

        $isInline = $this->Schema[$elementName]['isInline'];

        // Special workaround for custom tags.
        if ( is_array( $isInline ) && !is_string( $element ) )
        {
            $isInline = false;
            $name = $element->getAttribute( 'name' );

            if ( isset( $this->Schema['custom']['isInline'][$name] ) )
            {
                if ( $this->Schema['custom']['isInline'][$name] != 'false' )
                    $isInline = true;
            }
        }
        return $isInline;
    }

    /*!
       Checks if one element is allowed to be a child of another

       \param $parent   parent element: DOMNode or string
       \param $child    child element: DOMNode or string

       \return true  if elements match schema
       \return false if elements don't match schema
       \return null  in case of errors
    */

    function check( $parent, $child )
    {
        if ( is_string( $parent ) )
            $parentName = $parent;
        else
            $parentName = $parent->nodeName;

        if ( is_string( $child ) )
            $childName = $child;
        else
            $childName = $child->nodeName;

        if ( isset( $this->Schema[$childName] ) )
        {
            $isInline = $this->isInline( $child );

            if ( $isInline === true )
            {
                $allowed = $this->Schema[$parentName]['inlineChildrenAllowed'];
            }
            elseif ( $isInline === false )
            {
                // Special workaround for custom tags.
                if ( $parentName == 'custom' && !is_string( $parent ) &&
                     $parent->getAttribute( 'inline' ) != 'true' )
                {
                    $allowed = true;
                }
                else
                    $allowed = $this->Schema[$parentName]['blockChildrenAllowed'];
            }
            else
                return true;

            if ( is_array( $allowed ) )
                $allowed = in_array( $childName, $allowed );

            if ( !$allowed )
                return false;
        }
        else
        {
            return null;
        }
        return true;
    }

    function childrenRequired( $element )
    {
        //if ( !isset( $this->Schema[$element->nodeName] ) )
        //    return false;

        return $this->Schema[$element->nodeName]['childrenRequired'];
    }

    function hasAttributes( $element )
    {
        //if ( !isset( $this->Schema[$element->nodeName] ) )
        //    return false;

        return ( $this->Schema[$element->nodeName]['attributes'] != false );
    }

    function attributes( $element )
    {
        return $this->Schema[$element->nodeName]['attributes'];
    }

    function customAttributes( $element )
    {
        if ( is_string( $element ) )
        {
            return $this->Schema[$element]['customAttributes'];
        }
        else
        {
            if ( $element->nodeName == 'custom' )
            {
                $name = $element->getAttribute( 'name' );
                if ( $name )
                    return $this->Schema['custom']['customAttributes'][$name];
            }
            else
            {
                return $this->Schema[$element->nodeName]['customAttributes'];
            }
        }
        return [];
    }

    function attrDefaultValue( $tagName, $attrName )
    {
        if ( isset( $this->Schema[$tagName]['attributesDefaults'][$attrName] ) )
            return $this->Schema[$tagName]['attributesDefaults'][$attrName];
        else
            return [];
    }

    function attrDefaultValues( $tagName )
    {
        if ( isset( $this->Schema[$tagName]['attributesDefaults'] ) )
            return $this->Schema[$tagName]['attributesDefaults'];
        else
            return [];
    }

    function exists( $element )
    {
        if ( is_string( $element ) )
        {
            return isset( $this->Schema[$element] );
        }
        else
        {
            if ( $element->nodeName == 'custom' )
            {
                $name = $element->getAttribute( 'name' );
                if ( $name )
                    return in_array( $name, $this->Schema['custom']['tagList'] );
            }
            else
            {
                return isset( $this->Schema[$element->nodeName] );
            }
        }
        return false;
    }

    function availableElements()
    {
        return array_keys( $this->Schema );
    }

    function getClassesList( $tagName )
    {
        if ( isset( $this->Schema[$tagName]['classesList'] ) )
            return $this->Schema[$tagName]['classesList'];
        else
            return [];
    }

    function addAvailableClass( $tagName, $class )
    {
        if ( !isset( $this->Schema[$tagName]['classesList'] ) )
            $this->Schema[$tagName]['classesList'] = [];

        $this->Schema[$tagName]['classesList'][] = $class;
    }

    function addCustomAttribute( $element, $attrName )
    {
        if ( is_string( $element ) )
        {
            $this->Schema[$element]['customAttributes'][] = $attrName;
        }
        else
        {
            if ( $element->nodeName == 'custom' )
            {
                $name = $element->getAttribute( 'name' );
                if ( $name )
                    $this->Schema['custom']['customAttributes'][$name][] = $attrName;
            }
            else
            {
                $this->Schema[$element->nodeName]['customAttributes'][] = $attrName;
            }
        }
    }
}
?>
