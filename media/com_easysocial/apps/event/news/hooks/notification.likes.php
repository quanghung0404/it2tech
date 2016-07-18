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
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppNewsHookNotificationLikes
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
        // Get likes participants
        $model = FD::model('Likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

        // Merge to include actor, diff to exclude self, unique to remove dups, and values to reset the index
        $users = array_values(array_unique(array_diff(array_merge($users, array($item->actor_id)), array(FD::user()->id))));

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // When someone likes on the photo that you have uploaded in a event
        if ($item->context_type == 'news.event.create') {

            // We do not want to display any content if the person likes a event announcement
            $item->content = '';

            // Get the news object
            $news = FD::table('EventNews');
            $news->load($item->uid);

            // Get the event from the stream
            $event = FD::event($news->cluster_id);

            // Set the content
            if ($event) {
                $item->image = $event->getAvatar();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($news->created_by == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_NEWS_USER_LIKES_YOUR_ANNOUNCEMENT', count($users));
                $item->title = JText::sprintf($langString, $names, $event->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = FD::string()->computeNoun('APP_EVENT_NEWS_USER_LIKES_USER_ANNOUNCEMENT', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($news->created_by)->getName(), $event->getName());

            return;
        }

        return;
    }

}
