<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class SocialGroupAppStoryHookNotificationComments
{
    /**
     * Processes comment notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function execute(SocialTableNotification &$item)
    {
        // Get the owner of the stream item since we need to notify the person
        $stream     = FD::table( 'Stream' );
        $stream->load($item->uid);

        // Get comment participants
        $model      = FD::model( 'Comments' );
        $users      = $model->getParticipants($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users      = array_merge( array( $item->actor_id ) , $users );

        // Ensure that the values are unique
        $users      = array_unique( $users );
        $users      = array_values( $users );

        // Exclude myself from the list of users.
        $index      = array_search( FD::user()->id , $users );

        if( $index !== false )
        {
            unset( $users[ $index ] );

            $users  = array_values( $users );
        }

        // By default content is always empty;
        $content    = '';

        // Only show the content when there is only 1 item
        if( count( $users ) == 1 )
        {
            // Legacy fix for prior to 1.2 as there is no content stored.
            if( !empty( $item->content ) )
            {
                $content        = JString::substr( strip_tags( $item->content ) , 0 , 30 );

                if( JString::strlen( $item->content ) > 30 )
                {
                    $content .= JText::_( 'COM_EASYSOCIAL_ELLIPSES' );
                }
            }
        }

        // Set the content to the stream
        $item->content  = $content;

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        if ($item->context_type == 'photos.group.share') {

            $this->notificationPhotos($names, $users, $item);
            return;
        }

        // When someone comments on your status update in a group.
        if ($item->context_type == 'story.group.create') {
            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if( $stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
            {
                $item->title    = JText::sprintf( 'APP_GROUP_STORY_USER_POSTED_COMMENT_ON_YOUR_POST' , $names );

                return $item;
            }

            // This is for 3rd party viewers
            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_POSTED_COMMENT_ON_USERS_POST' , $names , FD::user( $stream->actor_id )->getName() );

            return $item;
        }

        // When someone comments on the link you shared in the group
        if ($item->context_type == 'links.group.create') {

            // Get the stream object
            $stream     = FD::table('Stream');
            $stream->load($item->uid);

            // Get the group object
            $group      = FD::group($stream->cluster_id);

            // Get the link object
            $model      = FD::model( 'Stream' );
            $links      = $model->getAssets($item->uid, SOCIAL_TYPE_LINKS);

            if ($links) {
                $link   = FD::makeObject($links[0]->data);

                $item->content  = $link->link;
                $item->image    = $link->image;
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if( $stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
            {
                $item->title    = JText::sprintf('APP_GROUP_STORY_USER_COMMENTED_ON_YOUR_LINK', $names, $group->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_COMMENTED_ON_USERS_LINK', $names, FD::user($stream->actor_id)->getName(), $group->getName());

            return;
        }
    }

    /**
     *
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    private function notificationPhotos($names, $users, &$item)
    {
        // Get the stream object
        $stream     = FD::table('Stream');
        $stream->load($item->uid);

        // Get the group
        $group  = FD::group($item->context_ids);

        // Get all child stream items
        $streamItems      = $stream->getItems();

        // Get the first photo since we can't get all photos
        if ($streamItems && isset($streamItems[0])) {

            $streamItem    = $streamItems[0];

            $photo      = FD::table('Photo');
            $photo->load($streamItem->context_id);

            $item->image    = $photo->getSource();
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if( $stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
        {
            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_COMMENTED_ON_YOUR_SHARED_PHOTO', $names, $group->getName());

            return $item;
        }

        // This is for 3rd party viewers
        $item->title    = JText::sprintf('APP_GROUP_STORY_USER_COMMENTED_ON_USERS_SHARED_PHOTO', $names, FD::user($stream->actor_id)->getName(), $group->getName());
    }
}
