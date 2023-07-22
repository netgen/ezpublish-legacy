<?php
/**
 * File containing PHP session handler
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/** PHP session handler class
 * Does not register it self as opposed to most other handler, as the point is to let PHP handle most things
 *
 * @since 4.4
 * @package lib
 * @subpackage ezsession
 */
class ezpSessionHandlerPHP extends ezpSessionHandler
{
    /**
     * reimp (Does nothing, lets php handle sessions)
     * Does set gc_maxlifetime to SessionTimeout to make sure timeout works like DB handler
     */
    public function setSaveHandler()
    {
        $ini = eZINI::instance();
        if ( $ini->hasVariable('Session', 'SessionTimeout') && $ini->variable('Session', 'SessionTimeout') )
        {
            ini_set("session.gc_maxlifetime", $ini->variable('Session', 'SessionTimeout') );
        }
        // make sure eZUser does not update lastVisit on every request and only on login
        $GLOBALS['eZSessionIdleTime'] = 0;
        return true;
    }

    /**
     *  reimp (not used in this handler)
     */
    public function read( $sessionId )
    {
        return false;
    }

    /**
     *  reimp (not used in this handler)
     */
    public function write( $sessionId, $sessionData )
    {
        return false;
    }

    public function destroy( $sessionId )
    {
        $_SESSION = [];
        session_destroy();

        ezpEvent::getInstance()->notify( 'session/destroy', [$sessionId] );
        return true;
    }

    /**
     *  reimp (Only uses php and callbacks)
     */
    public function regenerate( $updateBackendData = true )
    {
        $oldSessionId = session_id();
        session_regenerate_id( $updateBackendData );
        $newSessionId = session_id();

        ezpEvent::getInstance()->notify( 'session/regenerate', [$oldSessionId, $newSessionId] );

        if ( $updateBackendData )
        {
            $db = eZDB::instance();
            $escOldKey = $db->escapeString( $oldSessionId );
            $escNewKey = $db->escapeString( $newSessionId );
            $escUserID = $db->escapeString( eZSession::userID() );
            eZSession::triggerCallback( 'regenerate_pre', [$db, $escNewKey, $escOldKey, $escUserID] );
            eZSession::triggerCallback( 'regenerate_post', [$db, $escNewKey, $escOldKey, $escUserID] );
        }
        return true;
    }

   /**
     * reimp (not used in this handler)
     */
    public function gc( $maxLifeTime )
    {
        ezpEvent::getInstance()->notify( 'session/gc', [$maxLifeTime] );
        $db = eZDB::instance();
        eZSession::triggerCallback( 'gc_pre', [$db, $maxLifeTime] );
        eZSession::triggerCallback( 'gc_post', [$db, $maxLifeTime] );
        return false;
    }

   /**
     * reimp (not used in this handler)
     */
    public function cleanup()
    {
        ezpEvent::getInstance()->notify( 'session/cleanup', [] );
        $db = eZDB::instance();
        eZSession::triggerCallback( 'cleanup_pre', [$db] );
        eZSession::triggerCallback( 'cleanup_post', [$db] );
        return true;
    }

    /**
     * reimp (not used in this handler)
     */
    public function deleteByUserIDs( array $userIDArray )
    {
    }

   /**
     * reimp (this handler does not use db)
     */
    static public function hasBackendAccess()
    {
        return false;
    }

   /**
     * reimp (this handler does not use db)
     */
    static public function dbRequired()
    {
        return false;
    }
}
?>
