<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppVideosHookNotificationComments
{
    /**
     * Process comment notifications
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function execute(&$item)
    {
        // Get the list of participants for comments
        $model = ES::model('Comments');
        $users = $model->getParticipants($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users = array_unique($users);
        $users = array_values($users);

        // Exclude myself from the list of users.
        $index = array_search(FD::user()->id, $users);

        if ($index !== false) {
            unset($users[$index]);

            $users = array_values($users);
        }

        // Convert the names to stream-ish
        $names = FD::string()->namesToNotifications($users);

        // Load the comment object since we have the context_ids
        $comment = ES::table('Comments');
        $comment->load($item->uid);

        if ($item->context_type == 'videos.event.create') {

            // Get the video object
            $video = ES::video($item->uid, SOCIAL_TYPE_GROUP, $item->context_ids);

            // Set the video image
            $item->image = $video->getThumbnail();

            // We need to determine if the user is the owner
            if ($video->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf('APP_USER_VIDEOS_NOTIFICATIONS_COMMENTED_ON_YOUR_VIDEO', $names);

                return;
            }

            if ($item->actor_id == $video->user_id && count($users) == 1) {

                // We do not need to pluralize here since we know there's always only 1 recipient
                $item->title = JText::sprintf('APP_USER_VIDEOS_NOTIFICATIONS_COMMENTED_ON_USERS_VIDEO' . FD::user($item->actor_id)->getGenderLang(), FD::user($item->actor_id)->getName());

                return;
            }

            // For other users, we just post a generic message
            $item->title = JText::sprintf('APP_USER_VIDEOS_NOTIFICATIONS_COMMENTED_ON_USERS_VIDEO', $names, FD::user($video->user_id)->getName());

            return;
        }

        return;
    }

}
