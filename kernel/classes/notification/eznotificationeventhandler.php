<?php
/**
 * File containing the eZNotificationEventHandler class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationEventHandler eznotificationeventhandler.php
  \brief The class eZNotificationEventHandler does

*/

class eZNotificationEventHandler
{
    final public const EVENT_HANDLED = 0;
    final public const EVENT_SKIPPED = 1;
    final public const EVENT_UNKNOWN = 2;
    final public const EVENT_ERROR = 3;
    
    /**
     * Constructor
     *
     * @param string $IDString
     * @param string $Name
     */
    public function __construct(public $IDString, public $Name)
    {
    }

    function attributes()
    {
        return ['id_string', 'name'];
    }

    function hasAttribute( $attr )
    {
        return in_array( $attr, $this->attributes() );
    }

    function attribute( $attr )
    {
        if ( $attr == 'id_string' )
        {
            return $this->IDString;
        }
        else if ( $attr == 'name' )
        {
            return $this->Name;
        }

        eZDebug::writeError( "Attribute '$attr' does not exist", __METHOD__ );
        return null;
    }

    function handle( $event )
    {
        return true;
    }

    /*!
     Cleanup any specific tables or other resources.
    */
    function cleanup()
    {
    }

    function fetchHttpInput( $http, $module )
    {
        return true;
    }

    function storeSettings( $http, $module )
    {
        return true;
    }
}

?>
