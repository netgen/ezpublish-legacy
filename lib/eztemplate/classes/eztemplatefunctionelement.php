<?php
/**
 * File containing the eZTemplateFunctionElement class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*! \defgroup eZTemplateFunctions Template functions
    \ingroup eZTemplate */

/*!
  \class eZTemplateFunctionElement eztemplatefunctionelement.php
  \ingroup eZTemplateElements
  \brief Represents a function element in the template tree.

  This class represents a function with it's parameters.
  It also contains child elements if the function was registered as having
  children.

*/

class eZTemplateFunctionElement
{
    /**
     * Initializes the function with a name and parameter array.
     *
     * @param string $Name
     * @param array $params
     * @param array $Children
     */
    public function __construct( /// The name of the function
    public $Name, $params, /// The child elements
    public $Children = [] )
    {
        $this->Params =& $params;
    }

    function setResourceRelation( $resource )
    {
        $this->Resource = $resource;
    }

    function setTemplateNameRelation( $templateName )
    {
        $this->TemplateName = $templateName;
    }

    function resourceRelation()
    {
        return $this->Resource;
    }

    function templateNameRelation()
    {
        return $this->TemplateName;
    }

    /*!
     Returns the name of the function.
    */
    function name()
    {
        return $this->Name;
    }

    function serializeData()
    {
        return ['class_name' => 'eZTemplateFunctionElement', 'parameters' => ['name', 'parameters', 'children'], 'variables' => ['name' => 'Name', 'parameters' => 'Params', 'children' => 'Children']];
    }

    /*!
     Tries to run the function with the children, the actual function execution
     is done by the template class.
    */
    function process( $tpl, &$text, $nspace, $current_nspace )
    {
        $tmp = $tpl->doFunction( $this->Name, $this, $nspace, $current_nspace );
        if ( $tmp === false )
            return;
        $tpl->appendElement( $text, $tmp, $nspace, $current_nspace );
    }

    /*!
     Returns a reference to the parameter list.
    */
    function &parameters()
    {
        return $this->Params;
    }

    /*!
     Returns a reference to the children.
    */
    function &children()
    {
        return $this->Children;
    }

    /*!
     Appends the child element $node to the child list.
    */
    function appendChild( &$node )
    {
        $this->Children[] =& $node;
    }
    /// The parameter list
    public $Params;

    public $Resource;
    public $TemplateName;
}

?>
