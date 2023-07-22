<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class eZPageZone
{
    private array $attributes = [];

    /**
     * Constructor
     *
     * @param string $name
     */
    function __construct( $name = null )
    {
        if ( isset( $name ) )
            $this->attributes['name'] = $name;
    }

    /**
     * Creates DOMElement with zone data
     *
     * @return DOMElement
     */
    public function toXML( DOMDocument $dom )
    {
        $zoneNode = $dom->createElement( 'zone' );
        foreach ( $this->attributes as $attrName => $attrValue )
        {
            switch ( $attrName )
            {
                case 'id':
                    $zoneNode->setAttribute( 'id', 'id_' . $attrValue );
                    break;

                case 'action':
                    $zoneNode->setAttribute( 'action', $attrValue );
                    break;

                case 'blocks':
                    foreach ( $this->attributes['blocks'] as $block )
                    {
                        $blockNode = $block->toXML( $dom );
                        $zoneNode->appendChild( $blockNode );
                    }
                    break;

                default:
                    $node = $dom->createElement( $attrName );
                    $nodeValue = $dom->createTextNode( $attrValue );
                    $node->appendChild( $nodeValue );
                    $zoneNode->appendChild( $node );
                    break;
            }
        }

        return $zoneNode;
    }

    /**
     * Creates and return eZPageZone object from given XML
     *
     * @static
     * @return eZPageZone
     */
    public static function createFromXML( DOMElement $node )
    {
        $newObj = new eZPageZone();

        if ( $node->hasAttributes() )
        {
            foreach ( $node->attributes as $attr )
            {
                if ( $attr->name == 'id' )
                {
                    $value = explode( '_', (string) $attr->value );
                    $newObj->setAttribute( $attr->name, $value[1] );
                }
                else
                {
                    $newObj->setAttribute( $attr->name, $attr->value );
                }
            }
        }

        foreach ( $node->childNodes as $node )
        {
            if ( $node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'block' )
            {
                $blockNode = eZPageBlock::createFromXML( $node );
                $newObj->addBlock( $blockNode );
            }
            elseif ( $node->nodeType == XML_ELEMENT_NODE )
            {
                $newObj->setAttribute( $node->nodeName, $node->nodeValue );
            }
        }

        return $newObj;
    }

    /**
     * Add new $block to eZPageZone object
     *
     * @return eZPageBlock
     */
    public function addBlock( eZPageBlock $block )
    {
        $this->attributes['blocks'][] = $block;
        return $block;
    }

    /**
     * Sorting blocks for given sort array which contains block ids
     *
     */
    public function sortBlocks( array $sortArray )
    {
        $blocksToBeRemoved = [];
        $sortedBlocks = [];

        foreach( $sortArray as $sortItem )
        {
            $blocksToBeRemoved = [];
            
            foreach( $this->attributes['blocks'] as $block )
            {
                if ( $block->attribute('id') === $sortItem )
                    $sortedBlocks[] = $block;
                
                if ( $block->toBeRemoved() )
                    $blocksToBeRemoved[] = $block;
            }
        }

        $sortedBlocks = [...$sortedBlocks, ...$blocksToBeRemoved];

        $this->attributes['blocks'] = $sortedBlocks;
    }

    /**
     * Move current block position up
     *
     * @param integer $currentIndex
     * @return bool
     */
    public function moveBlockUp( $currentIndex )
    {
        $array =& $this->attributes['blocks'];

        $newIndex = $currentIndex - 1;

        if ( $newIndex < 0 || $newIndex >= (is_countable($array) ? count( $array ) : 0) )
            return false;

        $tmpItem = $array[$newIndex];

        $array[$newIndex] =& $array[$currentIndex];
        $array[$currentIndex] =& $tmpItem;

        if ( $tmpItem->toBeRemoved() )
            $this->moveBlockUp( $newIndex );

        return true;
    }

    /**
     * Move current block position down
     *
     * @param integer $currentIndex
     * @return bool
     */
    public function moveBlockDown( $currentIndex )
    {
        $array =& $this->attributes['blocks'];

        $newIndex = $currentIndex + 1;

        if ( $newIndex < 0 || $newIndex >= (is_countable($array) ? count( $array ) : 0) )
            return false;

        $tmpItem = $array[$newIndex];

        $array[$newIndex] =& $array[$currentIndex];
        $array[$currentIndex] =& $tmpItem;

        if ( $tmpItem->toBeRemoved() )
            $this->moveBlockDown( $newIndex );

        return true;
    }

    /**
     * Remove block with given $index from eZPageZone object
     *
     * @param integer $index
     */
    public function removeBlock( $index )
    {
        unset( $this->attributes['blocks'][$index] );
    }

    /**
     * Return eZPageZone name attribute
     *
     * @return string
     */
    public function getName()
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Return total block count
     *
     * @return integer
     */
    public function getBlockCount()
    {
        return isset( $this->attributes['blocks'] ) ? is_countable($this->attributes['blocks']) ? count( $this->attributes['blocks'] ) : 0 : 0;
    }

    /**
     * Return eZPageBlock object by given $index
     *
     * @return eZPageBlock
     * @param integer $index
     */
    public function getBlock( $index )
    {
        $block = null;

        if ( isset( $this->attributes['blocks'][$index] ) )
            $block = $this->attributes['blocks'][$index];

        return $block;
    }

    /**
     * Return attributes names
     *
     * @return array(string)
     */
    public function attributes()
    {
        return array_keys( $this->attributes );
    }

    /**
     * Checks if attribute with given $name exists
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute( $name )
    {
        return in_array( $name, array_keys( $this->attributes ) );
    }

    /**
     * Set attribute with given $name to $value
     *
     * @param string $name
     */
    public function setAttribute( $name, mixed $value )
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Return value of attribute with given $name
     *
     * @return mixed
     * @param string $name
     */
    public function attribute( $name )
    {
        if ( $this->hasAttribute( $name ) )
        {
            return $this->attributes[$name];
        }
        else
        {
            $value = null;
            return $value;
        }
    }

    /**
     * Cleanup processed objects, removes action attribute
     * removes all blocks marked with "remove" action
     *
     * @return eZPageZone
     */
    public function removeProcessed()
    {
        if ( $this->hasAttribute( 'action' ) )
        {
            unset( $this->attributes['action'] );
        }

        if ( $this->getBlockCount() > 0 )
        {
            foreach ( $this->attributes['blocks'] as $index => $block )
            {
                if ( $block->toBeRemoved() )
                {
                    $this->removeBlock( $index );
                }
                else
                {
                    $block->removeProcessed();
                }
            }
        }

        return $this;
    }

    /**
     * Checks if current zone is to be removed
     *
     * @return bool
     */
    public function toBeRemoved()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'remove';
    }

    /**
     * Checks if current zone is to be modified
     *
     * @return bool
     */
    public function toBeModified()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'modify';
    }

    /**
     * Checks if current zone is to be added
     *
     * @return bool
     */
    public function toBeAdded()
    {
        return isset( $this->attributes['action'] ) && $this->attributes['action'] == 'add';
    }

    /**
     * Method executed when an object copy is created 
     * by using the clone keyword
     *
     */
    public function __clone()
    {
        $this->attributes['id'] = md5( (string)microtime() . (string)random_int(0, mt_getrandmax()) );
        $this->attributes['action'] = 'add';

        if ( $this->hasAttribute( 'blocks' ) )
        {
            foreach ( $this->attributes['blocks'] as $i => $block )
            {
                $this->attributes['blocks'][$i] = clone $block;
                $this->attributes['blocks'][$i]->setAttribute( 'zone_id', $this->attributes['id'] );
            }
        }
    }
}

?>
