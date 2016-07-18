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

class SocialEventAppPhotosHookNotificationLikes
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

        // Include the actor of the stream item as the recipient
        $users = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users = array_unique($users);
        $users = array_values($users);

        // Exclude myself from the list of users.
        $index = array_search(FD::user()->id, $users);

        if ($index !== false) {
            unset($users[$index]);
            $users  = array_values($users);
        }

        // Convert the names to stream-ish
        $names = FD::string()->namesToNotifications($users);

        // When user likes on an album or a event of photos from an album on the stream
        if ($item->context_type == 'albums.event.create') {

            $album = FD::table('Album');
            $album->load($item->uid);

            $item->content = $album->get('title');
            $item->image = $album->getCover();

            // We need to determine if the user is the owner
            if ($album->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_YOUR_ALBUMS', count($users));
                $item->title = JText::sprintf($langString, $names);

                return;
            }

            // For other users, we just post a generic message
            $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_USERS_ALBUMS', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($album->user_id)->getName());

            return;
        }

       if ($item->context_type == 'photos.event.updateCover') {

            // Get the photo object
            $photo = FD::table('Photo');
            $photo->load($item->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';

            $event = FD::event($photo->uid);

            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_YOUR_PROFILE_COVER', count($users));

                if (count($users) == 1) {
                    $item->title = JText::sprintf($langString, $names, $event->getName());
                } else {
                    $item->title = JText::sprintf($langString, $names);
                }

                return;
            }

            // For other users, we just post a generic message
            $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_USERS_PROFILE_COVER', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($photo->user_id)->getName());

            return;
        }

        if ($item->context_type == 'photos.event.uploadAvatar') {

            // Get the photo object
            $photo = FD::table('Photo');
            $photo->load($item->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';


            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_YOUR_PROFILE_PHOTO', count($users));
                $item->title = JText::sprintf($langString, $names);

                return;
            }

            // For other users, we just post a generic message
            $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_USERS_PROFILE_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($photo->user_id)->getName());

            return;
        }

        // If user uploads multiple photos on the stream
        if ($item->context_type == 'stream.event.upload') {
            // Get the photo object
            $photo = FD::table('Photo');
            $photo->load($item->context_ids);

            $item->content = '';

            // We could also set an image preview
            $item->image = $photo->getSource();

            // Because we know that this is coming from a stream, we can display a nicer message
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_YOUR_PHOTO_SHARED_ON_THE_STREAM', count($users));
                $item->title = JText::sprintf($langString, $names);

                return;
            }

            // For other users, we just post a generic message
            $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_USERS_PHOTO_SHARED_ON_THE_STREAM', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($photo->user_id)->getName());

            return;
        }

        // When user likes on a single photo item
        if ($item->context_type == 'photos.event.upload' || $item->context_type == 'photos.event.add') {

            // Get the photo object
            $photo = FD::table('Photo');
            $photo->load($item->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';


            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_YOUR_PHOTO', count($users));
                $item->title = JText::sprintf($langString, $names);

                return;
            }

            // For other users, we just post a generic message
            $langString = FD::string()->computeNoun('APP_EVENT_PHOTOS_NOTIFICATIONS_LIKES_USERS_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, FD::user($photo->user_id)->getName());

            return;
        }

        return;
    }
}
