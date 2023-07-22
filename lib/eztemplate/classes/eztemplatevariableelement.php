<?php
/**
 * File containing the eZTemplateVariableElement class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateVariableElement eztemplatevariableelement.php
  \ingroup eZTemplateElements
  \brief Represents a variable element in the template tree.

  The element contains the variable and all it's operators.
*/

class eZTemplateVariableElement
{
    /**
     * Initializes the object with the value array and operators.
     */
    public function __construct(
        /// The variable array
        public mixed $Variable
    )
    {
    }

    /*!
     Returns #variable.
    */
    function name()
    {
        return "#variable";
    }

    function serializeData()
    {
        return ['class_name' => 'eZTemplateVariableElement', 'parameters' => ['data'], 'variables' => ['data' => 'Variable']];
    }

    /*!
     Process the variable with it's operators and appends the result to $text.
    */
    function process( $tpl, &$text, $nspace, $current_nspace )
    {
        $value = $tpl->elementValue( $this->Variable, $nspace );
        $tpl->appendElement( $text, $value, $nspace, $current_nspace );
    }

    /*!
     Returns the variable array.
    */
    function variable()
    {
        return $this->Variable;
    }
}

?>
