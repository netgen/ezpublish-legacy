<?php
/**
 * File containing Symfony session handler
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */


/**
 * Symfony session handler. Basically, it let Symfony manage the session and
 * call the Symfony session storage when needed for very specific operation.
 *
 * @package lib
 * @subpackage ezsession
 */
class ezpSessionHandlerSymfony extends ezpSessionHandler
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    protected $storage;

    /**
     * reimp. Does not do anything to let Symfony manage the session handling
     */
    public function setSaveHandler()
    {
        // make sure eZUser does not update lastVisit on every request and only on login
        $GLOBALS['eZSessionIdleTime'] = 0;
        return true;
    }

    public function read( $sessionId )
    {
        return false;
    }

    public function write( $sessionId, $sessionData )
    {
        return false;
    }

    public function destroy( $sessionId )
    {
        if ( eZSys::isShellExecution() )
        {
            return false;
        }

        $sfHandler = $this->storage->getSaveHandler();
        ezpEvent::getInstance()->notify( 'session/destroy', [$sessionId] );
        if ( method_exists( $sfHandler, 'destroy' ) )
        {
            return $sfHandler->destroy( $sessionId );
        }
        return false;
    }

    public function regenerate( $updateBackendData = true )
    {
        if ( eZSys::isShellExecution() )
        {
            return false;
        }

        $oldSessionId = session_id();
        $this->storage->regenerate( $updateBackendData );
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

    public function gc( $maxLifeTime )
    {
        if ( eZSys::isShellExecution() )
        {
            return false;
        }

        ezpEvent::getInstance()->notify( 'session/gc', [$maxLifeTime] );
        $db = eZDB::instance();
        eZSession::triggerCallback( 'gc_pre', [$db, $maxLifeTime] );
        $sfHandler = $this->storage->getSaveHandler();
        if ( method_exists( $sfHandler, 'gc' ) )
        {
            $sfHandler->gc( $maxLifeTime );
        }
        eZSession::triggerCallback( 'gc_post', [$db, $maxLifeTime] );
        return false;
    }

    public function cleanup()
    {

    }

    public function deleteByUserIDs( array $userIDArray )
    {
    }

    static public function count()
    {
        return -1;
    }

    static public function hasBackendAccess()
    {

    }

    static public function  dbRequired()
    {
        return false;
    }

    /**
     * Set the storage handler defined in Symfony.
     *
     * @param \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface $storage
     */
    public function setStorage( $storage )
    {
        $this->storage = $storage;
    }

    /**
     * Let Symfony starts the session
     *
     * @return bool
     */
    public function sessionStart()
    {
        if ( $this->storage && !$this->storage->isStarted() )
        {
            $this->storage->start();
        }

        return true;
    }
}
