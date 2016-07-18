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

class SocialEventAppDiscussionsHookNotificationLikes
{
    public function execute(&$item)
    {
        // Get likes participants
        $model = FD::model('Likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

        // Merge to include actor, diff to exclude self, unique to remove dups, and values to reset the index
        $users = array_values(array_unique(array_diff(array_merge($users, array($item->actor_id)), array(FD::user()->id))));

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        $discussion = FD::table('Discussion');
        $discussion->load($item->context_ids);

        $event = FD::event($discussion->uid);

        $isOwner = $discussion->created_by == $item->target_id && $item->target_type == SOCIAL_TYPE_USER;

        // Set the content to the discussion title.
        $item->content = $discussion->title;

        if ($item->context_type == 'discussions.event.create') {

            if ($isOwner) {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_YOUR_DISCUSSION', count($users));
                $item->title = JText::sprintf($string, $names, $event->getName());
            } else {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_USERS_DISCUSSION', count($users));
                $item->title = JText::sprintf($string, $names, FD::user($discussion->created_by)->getName(), $event->getName());
            }
        }

        if ($item->context_type == 'discussions.event.reply') {

            if ($isOwner) {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_YOUR_REPLY', count($users));
                $item->title = JText::sprintf($string, $names, $event->getName());
            } else {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_USERS_REPLY', count($users));
                $item->title = JText::sprintf($string, $names, FD::user($discussion->created_by)->getName(), $event->getName());
            }
        }

        if ($item->context_type == 'discussions.event.answered') {
            if ($isOwner) {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_YOUR_ACCEPTED_ANSWER', count($users));
                $item->title = JText::sprintf($string, $names, $event->getName());
            } else {
                $string = FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_USER_LIKES_USERS_ACCEPTED_ANSWER', count($users));
                $item->title = JText::sprintf($string, $names, FD::user($discussion->created_by)->getName(), $event->getName());
            }
        }
    }
}
