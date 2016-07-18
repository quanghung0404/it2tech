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

class SocialUserAppStoryHookNotificationComments
{
    /**
     * Processes likes notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function execute(&$item)
    {
         // Get comment participants
        $model      = FD::model( 'Comments' );
        $users      = $model->getParticipants($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users      = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users      = array_unique($users);
        $users      = array_values($users);

        // Exclude myself from the list of users.
        $index      = array_search( FD::user()->id , $users );

        if( $index !== false )
        {
            unset( $users[ $index ] );

            $users  = array_values( $users );
        }

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // Load the comment object since we have the context_ids
        $comment    = FD::table('Comments');
        $comment->load($item->context_ids);

        // When user likes on an album or a group of photos from an album on the stream
        if ($item->context_type == 'story.user.create') {

            $stream  = FD::table('Stream');
            $stream->load($item->uid);

            if (count($users) == 1) {
                $item->content  = $comment->comment;
            }

            // We need to determine if the user is the owner
            if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString     = FD::string()->computeNoun('APP_USER_STORY_NOTIFICATIONS_USER_POSTED_COMMENT_ON_YOUR_STATUS_UPDATE', count($users));
                $item->title    = JText::sprintf($langString, $names);

                return;
            }

            if ($item->actor_id == $stream->actor_id && count($users) == 1) {

                $item->title    = JText::sprintf('APP_USER_STORY_NOTIFICATIONS_USER_POSTED_COMMENT_ON_USERS_UPDATE' . FD::user($item->actor_id)->getGenderLang(), FD::user($stream->actor_id)->getName());

                return;
            }

            // For other users, we just post a generic message
            $langString     = FD::string()->computeNoun('APP_USER_STORY_NOTIFICATIONS_USER_POSTED_COMMENT_ON_YOUR_USERS_UPDATE', count($users));
            $item->title    = JText::sprintf($langString, $names, FD::user($stream->actor_id)->getName());

            return;
        }

        return;
    }

}
