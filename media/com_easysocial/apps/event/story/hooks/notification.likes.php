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

class SocialEventAppStoryHookNotificationLikes
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
        $names = FD::string()->namesToNotifications($users);


        // When someone likes on the photo that you have uploaded in a event
        if ($item->context_type == 'photos.event.share') {

            $this->notificationPhotos($names, $users, $item);

            return;
        }

        // When someone likes your post in a event
        if ($item->context_type == 'story.event.create') {

            // Get the owner of the stream item since we need to notify the person
            $stream = FD::table('Stream');
            $stream->load($item->uid);

            // Get the event from the stream
            $event = FD::event($stream->cluster_id);

            // Set the content
            if ($event) {
                $item->image = $event->getAvatar();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER)
            {
                $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_YOUR_POST', count($users));
                $item->title = JText::sprintf($langString, $names, $event->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_USERS_POST', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($stream->actor_id)->getName(), $event->getName());

            return;
        }

        if ($item->context_type == 'links.create') {

            // Get the owner of the stream item since we need to notify the person
            $stream = FD::table('Stream');
            $stream->load($item->uid);

            // Get the event from the stream
            $event = FD::event($stream->cluster_id);

            // Set the content
            if ($event) {
                $item->image = $event->getAvatar();
            }

            // Get the link object
            $model = FD::model('Stream');
            $links = $model->getAssets($item->uid, SOCIAL_TYPE_LINKS);

            if ($links) {
                $link = FD::makeObject($links[0]->data);

                $item->content = $link->link;
                $item->image = $link->image;
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER)
            {
                $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_YOUR_LINK', count($users));
                $item->title = JText::sprintf($langString, $names, $event->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_USERS_LINK', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($stream->actor_id)->getName(), $event->getName());

            return;
        }

        return $item;
    }

    private function notificationPhotos($names, $users, &$item)
    {
        // Get the stream object
        $stream = FD::table('Stream');
        $stream->load($item->uid);

        // Get the event
        $event = FD::event($item->context_ids);

        // Get all child stream items
        $streamItems = $stream->getItems();

        // Get the first photo since we can't get all photos
        if ($streamItems && isset($streamItems[0])) {

            $streamItem = $streamItems[0];

            $photo = FD::table('Photo');
            $photo->load($streamItem->context_id);

            $item->image = $photo->getSource();
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER)
        {
            $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_YOUR_SHARED_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, $event->getName());

            return $item;
        }

        // This is for 3rd party viewers
        $langString = FD::string()->computeNoun('APP_EVENT_STORY_USER_LIKES_USERS_SHARED_PHOTO', count($users));
        $item->title = JText::sprintf($langString, $names, FD::user($stream->actor_id)->getName(), $event->getName());
    }
}
