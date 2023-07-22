<?php
/**
 * File containing the eZNotificationFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationFunctionCollection eznotificationfunctioncollection.php
  \brief The class eZNotificationFunctionCollection does

*/

class eZNotificationFunctionCollection
{
    function handlerList()
    {
        $availableHandlers = eZNotificationEventFilter::availableHandlers();
        return ['result' => $availableHandlers];
    }

    function digestHandlerList( $time, $address )
    {
        $handlers = eZGeneralDigestHandler::fetchHandlersForUser( $time, $address );
        return ['result' => $handlers];
    }

    function digestItems( $time, $address, $handler )
    {
        $items = eZGeneralDigestHandler::fetchItemsForUser( $time, $address, $handler );
        return ['result' => $items];
    }

    function eventContent( $eventID )
    {
        $event = eZNotificationEvent::fetch( $eventID );
        return ['result' => $event->content()];
    }

    function subscribedNodesCount()
    {
        $count = eZSubTreeHandler::rulesCount();
        return ['result' => $count];
    }

    function subscribedNodes( $offset = false, $limit = false )
    {
        $nodes = eZSubTreeHandler::rules( false, $offset, $limit );
        return ['result' => $nodes];
    }
}

?>
