<?php
/**
 * File containing the eZApproveCollaborationHandler class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZApproveCollaborationHandler ezapprovecollaborationhandler.php
  \brief Handles approval communication using the collaboration system

  The handler uses the fields data_int1, data_int2 and data_int3 to store
  information on the contentobject and the approval status.

  - data_int1 - The content object ID
  - data_int2 - The content object version
  - data_int3 - The status of the approval, see defines.

*/

class eZApproveCollaborationHandler extends eZCollaborationItemHandler
{
    /// Approval message type
    final public const MESSAGE_TYPE_APPROVE = 1;

    /// Default status, no approval decision has been made
    final public const STATUS_WAITING = 0;

    /// The contentobject was approved and will be published.
    final public const STATUS_ACCEPTED = 1;

    /// The contentobject was denied and will be archived.
    final public const STATUS_DENIED = 2;

    /// The contentobject was deferred and will be a draft again for reediting.
    final public const STATUS_DEFERRED = 3;

    public function __construct()
    {
        parent::__construct(
            'ezapprove',
            ezpI18n::tr( 'kernel/classes', 'Approval' ),
            ['use-messages' => true, 'notification-types' => true, 'notification-collection-handling' => eZCollaborationItemHandler::NOTIFICATION_COLLECTION_PER_PARTICIPATION_ROLE]
        );
    }

    function title( $collaborationItem )
    {
        return ezpI18n::tr( 'kernel/classes', 'Approval' );
    }

    function content( $collaborationItem )
    {
        return ["content_object_id" => $collaborationItem->attribute( "data_int1" ), "content_object_version" => $collaborationItem->attribute( "data_int2" ), "approval_status" => $collaborationItem->attribute( "data_int3" )];
    }

    function notificationParticipantTemplate( $participantRole )
    {
        if ( $participantRole == eZCollaborationItemParticipantLink::ROLE_APPROVER )
        {
            return 'approve.tpl';
        }
        else if ( $participantRole == eZCollaborationItemParticipantLink::ROLE_AUTHOR )
        {
            return 'author.tpl';
        }
        else
            return false;
    }

    /*!
     \return the content object version object for the collaboration item \a $collaborationItem
    */
    static function contentObjectVersion( $collaborationItem )
    {
        $contentObjectID = $collaborationItem->contentAttribute( 'content_object_id' );
        $contentObjectVersion = $collaborationItem->contentAttribute( 'content_object_version' );
        return eZContentObjectVersion::fetchVersion( $contentObjectVersion, $contentObjectID );
    }

    /*!
     Updates the last_read for the participant link.
    */
    function readItem( $collaborationItem, $viewMode = false )
    {
        $collaborationItem->setLastRead();
    }

    /*!
     \return the number of messages for the approve item.
    */
    function messageCount( $collaborationItem )
    {
        return eZCollaborationItemMessageLink::fetchItemCount( ['item_id' => $collaborationItem->attribute( 'id' )] );
    }

    /*!
     \return the number of unread messages for the approve item.
    */
    function unreadMessageCount( $collaborationItem )
    {
        $lastRead = 0;
        $status = $collaborationItem->attribute( 'user_status' );
        if ( $status )
            $lastRead = $status->attribute( 'last_read' );
        return eZCollaborationItemMessageLink::fetchItemCount( ['item_id' => $collaborationItem->attribute( 'id' ), 'conditions' => ['modified' => ['>', $lastRead]]] );
    }

    /*!
     \static
     \return the status of the approval collaboration item \a $approvalID.
    */
    static function checkApproval( $approvalID )
    {
        $collaborationItem = eZCollaborationItem::fetch( $approvalID );
        if ( $collaborationItem !== null )
        {
            return $collaborationItem->attribute( 'data_int3' );
        }
        return false;
    }

    /*!
     \static
     \return makes sure the approval item is activated for all participants \a $approvalID.
    */
    static function activateApproval( $approvalID )
    {
        $collaborationItem = eZCollaborationItem::fetch( $approvalID );
        if ( $collaborationItem !== null )
        {
            $collaborationItem->setAttribute( 'data_int3', self::STATUS_WAITING );
            $collaborationItem->setAttribute( 'status', eZCollaborationItem::STATUS_ACTIVE );
            $timestamp = time();
            $collaborationItem->setAttribute( 'modified', $timestamp );
            $collaborationItem->store();
            $participantList = eZCollaborationItemParticipantLink::fetchParticipantList( ['item_id' => $approvalID] );
            foreach( $participantList as $participantLink )
            {
                $collaborationItem->setIsActive( true, $participantLink->attribute( 'participant_id' ) );
            }
            return true;
        }
        return false;
    }

    /*!
     Creates a new approval collaboration item which will approve the content object \a $contentObjectID
     with version \a $contentObjectVersion.
     The item will be added to the author \a $authorID and the approver array \a $approverIDArray.
     \return the collaboration item.
    */
    static function createApproval( $contentObjectID, $contentObjectVersion, $authorID, $approverIDArray )
    {
        $collaborationItem = eZCollaborationItem::create( 'ezapprove', $authorID );
        $collaborationItem->setAttribute( 'data_int1', $contentObjectID );
        $collaborationItem->setAttribute( 'data_int2', $contentObjectVersion );
        $collaborationItem->setAttribute( 'data_int3', false );
        $collaborationItem->store();
        $collaborationID = $collaborationItem->attribute( 'id' );

        $participantList = [['id' => [$authorID], 'role' => eZCollaborationItemParticipantLink::ROLE_AUTHOR], ['id' => $approverIDArray, 'role' => eZCollaborationItemParticipantLink::ROLE_APPROVER]];
        foreach ( $participantList as $participantItem )
        {
            foreach( $participantItem['id'] as $participantID )
            {
                $participantRole = $participantItem['role'];
                $link = eZCollaborationItemParticipantLink::create( $collaborationID, $participantID,
                                                                    $participantRole, eZCollaborationItemParticipantLink::TYPE_USER );
                $link->store();

                $profile = eZCollaborationProfile::instance( $participantID );
                $groupID = $profile->attribute( 'main_group' );
                eZCollaborationItemGroupLink::addItem( $groupID, $collaborationID, $participantID );
            }
        }

        // Create the notification
        $collaborationItem->createNotificationEvent();
        return $collaborationItem;
    }

    /*!
     Adds a new comment, approves the item or denies the item.
    */
    function handleCustomAction( $module, $collaborationItem )
    {
        $redirectView = 'item';
        $redirectParameters = ['full', $collaborationItem->attribute( 'id' )];
        $addComment = false;

        if ( $this->isCustomAction( 'Comment' ) )
        {
            $addComment = true;
        }
        else if ( $this->isCustomAction( 'Accept' ) or
                  $this->isCustomAction( 'Deny' ) or
                  $this->isCustomAction( 'Defer' ) )
        {
            // check user's rights to approve
            $user = eZUser::currentUser();
            $userID = $user->attribute( 'contentobject_id' );
            $participantList = eZCollaborationItemParticipantLink::fetchParticipantList( ['item_id' => $collaborationItem->attribute( 'id' )] );

            $approveAllowed = false;
            foreach( $participantList as $participant )
            {
                if ( $participant->ParticipantID == $userID &&
                     $participant->ParticipantRole == eZCollaborationItemParticipantLink::ROLE_APPROVER )
                {
                    $approveAllowed = true;
                    break;
                }
            }
            if ( !$approveAllowed )
            {
                return $module->redirectToView( $redirectView, $redirectParameters );
            }

            $contentObjectVersion = static::contentObjectVersion($collaborationItem);
            $status = self::STATUS_DENIED;
            if ( $this->isCustomAction( 'Accept' ) )
                $status = self::STATUS_ACCEPTED;
//             else if ( $this->isCustomAction( 'Defer' ) )
//                 $status = self::STATUS_DEFERRED;
//             else if ( $this->isCustomAction( 'Deny' ) )
//                 $status = self::STATUS_DENIED;
            else if ( $this->isCustomAction( 'Defer' ) or
                      $this->isCustomAction( 'Deny' ) )
                $status = self::STATUS_DENIED;
            $collaborationItem->setAttribute( 'data_int3', $status );
            $collaborationItem->setAttribute( 'status', eZCollaborationItem::STATUS_INACTIVE );
            $timestamp = time();
            $collaborationItem->setAttribute( 'modified', $timestamp );
            $collaborationItem->setIsActive( false );
            $redirectView = 'view';
            $redirectParameters = ['summary'];
            $addComment = true;
        }
        if ( $addComment )
        {
            $messageText = $this->customInput( 'ApproveComment' );
            if ( trim( (string) $messageText ) != '' )
            {
                $message = eZCollaborationSimpleMessage::create( 'ezapprove_comment', $messageText );
                $message->store();
                eZCollaborationItemMessageLink::addMessage( $collaborationItem, $message, self::MESSAGE_TYPE_APPROVE );
            }
        }
        $collaborationItem->sync();
        return $module->redirectToView( $redirectView, $redirectParameters );
    }

}

?>
