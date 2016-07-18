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

class SocialGroupAppVideosHookNotificationLikes
{
    /**
     * Process likes notifications
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function execute(&$item)
    {
        $model = ES::model('Likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

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


        // When user likes on a single video item
        if ($item->context_type == 'videos.group.create' || $item->context_type == 'videos.group.featured') {

            // Get the video object
            $video = ES::video($item->uid, SOCIAL_TYPE_GROUP, $item->context_ids);

            // Set the video image
            $item->image = $video->getThumbnail();
            $item->content = '';
            
            // We need to determine if the user is the owner
            if ($video->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_GROUP_VIDEOS_NOTIFICATIONS_LIKES_USER_VIDEO', count($users));
                $item->title = JText::sprintf($langString, $names);

                return;
            }

            // We do not need to pluralize here since we know there's always only 1 recipient
            if ($item->actor_id == $video->user_id && count($users) == 1) {
                $item->title = JText::sprintf('APP_GROUP_VIDEOS_NOTIFICATIONS_LIKES_USERS_VIDEO' . ES::user($item->actor_id)->getGenderLang(), ES::user($item->actor_id)->getName());

                return;
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_GROUP_VIDEOS_NOTIFICATIONS_LIKES_USERS_VIDEO', count($users));
            $item->title = JText::sprintf($langString, $names, ES::user($video->user_id)->getName());

            return;
        }


        return;
    }

}
