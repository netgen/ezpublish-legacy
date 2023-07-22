<?php
/**
 * File containing the eZTreeMenuOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZTreeMenuOperator
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name = 'treemenu' )
    {
        $this->Operators = [$name];
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return ['path' => ['type' => 'array', 'required' => true, 'default' => false], 'node_id' => ['type' => 'int', 'required' => false, 'default' => false], 'class_filter' => ['type' => 'array', 'required' => false, 'default' => false], 'depth_skip' => ['type' => 'int', 'required' => false, 'default' => false], 'max_level' => ['type' => 'int', 'required' => false, 'default' => false], 'is_selected_method' => ['type' => 'string', 'required' => false, 'default' => 'tree'], 'indentation_level' => ['type' => 'int', 'required' => false, 'default' => 15], 'language' => ['type' => 'string|array', 'required' => false, 'default' => false], 'load_data_map' => ['type' => 'boolean', 'required' => false, 'default' => null]];
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $level = 0;
        $done = false;
        $i = 0;
        $pathArray = [];
        $tmpModulePath = $namedParameters['path'];
        $classFilter = $namedParameters['class_filter'];
        $language = $namedParameters['language'];
        $loadDataMap = $namedParameters['load_data_map'];
        // node_id is not used anymore
        if ( !empty( $namedParameters['node_id'] ) )
        {
            eZDebug::writeNotice( 'Deprecated parameter "node_id" in treemenu template operator' );
        }

        if ( $classFilter === false )
        {
            $classFilter = [];
        }
        else if ( (is_countable($classFilter) ? count( $classFilter ) : 0) == 0 )
        {
            $classFilter = [1];
        }
        $classFilter = ( (is_countable($classFilter) ? count( $classFilter ) : 0) == 1 and !isset( $classFilter[0] ) ) ? [1] : $classFilter;
        $tmpModulePathCount = is_countable($tmpModulePath) ? count( $tmpModulePath ) : 0;
        if ( !$tmpModulePath[ $tmpModulePathCount -1 ]['url'] and isset( $tmpModulePath[ $tmpModulePathCount -1 ]['node_id'] ) )
            $tmpModulePath[ $tmpModulePathCount -1 ]['url'] = '/content/view/full/' . $tmpModulePath[ $tmpModulePathCount -1 ]['node_id'];

        $depthSkip = $namedParameters['depth_skip'];
        $indentationLevel = $namedParameters['indentation_level'];

        $maxLevel = $namedParameters['max_level'];
        $isSelectedMethod = $namedParameters['is_selected_method'];
        if ( $maxLevel === false )
            $maxLevel = 2;

        while ( !$done && isset( $tmpModulePath[$i+$depthSkip] ) )
        {
            // get node id
            $elements = explode( '/', (string) $tmpModulePath[$i+$depthSkip]['url'] );
            $nodeID = false;
            if ( isset( $elements[4] ) )
                $nodeID = $elements[4];

            $excludeNode = false;

            if ( isset( $elements[1] ) &&
                 isset( $elements[2] ) &&
                $elements[1] == 'content' &&
                $elements[2] == 'view' &&
                is_numeric( $nodeID ) &&
                $excludeNode == false &&
                $level < $maxLevel )
            {
                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if ( !isset( $node ) ) { $operatorValue = $pathArray; return; }
                if ( isset( $tmpModulePath[$i+$depthSkip+1] ) )
                {
                    $nextElements = explode( '/', (string) $tmpModulePath[$i+$depthSkip+1]['url'] );
                    if ( isset( $nextElements[4] ) )
                    {
                        $nextNodeID = $nextElements[4];
                    }
                    else
                    {
                        $nextNodeID = false;
                    }
                }
                else
                    $nextNodeID = false;

                $menuChildren = eZContentObjectTreeNode::subTreeByNodeID( ['Depth' => 1, 'Offset' => 0, 'SortBy' => $node->sortArray(), 'Language' => $language, 'ClassFilterType' => 'include', 'ClassFilterArray' => $classFilter],
                                                                  $nodeID );

                /// Fill objects with attributes, speed boost, only use if load_data_map is true
                // or if less then 16 nodes and param is not set (null)
                if ( $loadDataMap || (  $loadDataMap === null && count( (array) $menuChildren ) <= 15 ) )
                    eZContentObject::fillNodeListAttributes( $menuChildren );

                $tmpPathArray = [];
                foreach ( $menuChildren as $child )
                {
                    $name = $child->attribute( 'name' );
                    $tmpNodeID = $child->attribute( 'node_id' );

                    $url = "/content/view/full/$tmpNodeID/";
                    $urlAlias = '/' . $child->attribute( 'url_alias' );
                    $hasChildren = $child->attribute( 'is_container' ) && $child->attribute( 'children_count' ) > 0;
                    $contentObject = $child->attribute( 'object' );

                    $indent = ($i - 1) * $indentationLevel;

                    $isSelected = false;
                    $nextNextElements = ( $isSelectedMethod == 'node' and isset( $tmpModulePath[$i+$depthSkip+2]['url'] ) ) ? explode( '/', (string) $tmpModulePath[$i+$depthSkip+2]['url'] ) : null;
                    if ( $nextNodeID === $tmpNodeID and !isset( $nextNextElements[4] ) )
                    {
                        $isSelected = true;
                    }

                    $tmpPathArray[] = ['id' => $tmpNodeID, 'level' => $i, 'class_name' => $contentObject->classname(), 'is_main_node' => $child->attribute( 'is_main' ), 'has_children' => $hasChildren, 'indent' => $indent, 'url_alias' => $urlAlias, 'url' => $url, 'text' => $name, 'is_selected' => $isSelected, 'node' => $child];
                }

                // find insert pos
                $j = 0;
                $insertPos = 0;
                foreach ( $pathArray as $path )
                {
                    if ( $path['id'] == $nodeID )
                        $insertPos = $j;
                    $j++;
                }
                $restArray = array_splice( $pathArray, $insertPos + 1 );

                $pathArray = [...$pathArray, ...$tmpPathArray];
                $pathArray = [...$pathArray, ...$restArray];
            }
            else
            {
                if ( $level == 0 )
                {
                    $node = eZContentObjectTreeNode::fetch( 2 );
                    if ( !$node instanceof eZContentObjectTreeNode )
                    {
                        $operatorValue = $pathArray;
                        return;
                    }

                    $menuChildren = eZContentObjectTreeNode::subTreeByNodeID( ['Depth' => 1, 'Offset' => 0, 'SortBy' => $node->sortArray(), 'Language' => $language, 'ClassFilterType' => 'include', 'ClassFilterArray' => $classFilter],
                                                                      2 );

                    /// Fill objects with attributes, speed boost, only use if load_data_map is true
                    // or if less then 16 nodes and param is not set (null)
                    if ( $loadDataMap || (  $loadDataMap === null && count( (array) $menuChildren ) < 16 ) )
                        eZContentObject::fillNodeListAttributes( $menuChildren );

                    $pathArray = [];
                    foreach ( $menuChildren as $child )
                    {
                        $name = $child->attribute( 'name' );
                        $tmpNodeID = $child->attribute( 'node_id' );

                        $url = "/content/view/full/$tmpNodeID/";
                        $urlAlias = '/' . $child->attribute( 'url_alias' );

                        $pathArray[] = ['id' => $tmpNodeID, 'level' => $i, 'is_main_node' => $child->attribute( 'is_main' ), 'url_alias' => $urlAlias, 'url' => $url, 'text' => $name, 'is_selected' => false, 'node' => $child];
                    }
                }
                $done = true;
            }
            ++$level;
            ++$i;
        }

        $operatorValue = $pathArray;
    }

    /// \privatesection
    public $Operators;
}

?>
