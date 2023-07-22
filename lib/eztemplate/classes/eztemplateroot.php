<?php
/**
 * File containing the eZTemplateRoot class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*! \defgroup eZTemplateElements Template elements
    \ingroup eZTemplate
*/

/*!
  \class eZTemplateRoot eztemplateroot.php
  \ingroup eZTemplateElements
  \brief Represents a root element of the template tree.

  This starts the template tree and is the base of template includes.

  It has a list of child elements and runs process() on each child.
*/

class eZTemplateRoot
{
    /**
     * Constructor
     *
     * @param array $Children
     */
    public function __construct(
        /// The child array
        public $Children = []
    )
    {
    }

    /*!
     Returns #root as the name.
    */
    function name()
    {
        return "#root";
    }

    function serializeData()
    {
        return ['class_name' => 'eZTemplateRoot', 'parameters' => ['children'], 'variables' => ['children' => 'Children']];
    }

    /*!
     Runs process() on all child elements.
    */
    function process( $tpl, &$text, $nspace, $current_nspace )
    {
        foreach( array_keys( $this->Children ) as $key )
        {
            $this->Children[$key]->process( $tpl, $text, $nspace, $current_nspace );
        }
    }

    /*!
     Removes all children.
    */
    function clear()
    {
        $this->Children = [];
    }

    /*!
     Returns a reference to the child array.
    */
    function &children()
    {
        return $this->Children;
    }

    /*!
     Appends the child $node to the child array.
    */
    function appendChild( &$node )
    {
        $this->Children[] =& $node;
    }
}

?>
