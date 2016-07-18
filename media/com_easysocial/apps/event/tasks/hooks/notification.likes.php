<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppTasksHookNotificationLikes
{
    public function execute($item)
    {
        // Get comment participants
        $model = FD::model('Likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

        // Merge to include actor, diff to exclude self, unique to remove dups, and values to reset the index
        $users = array_values(array_unique(array_diff(array_merge($users, array($item->actor_id)), array(FD::user()->id))));

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        list($element, $group, $verb) = explode('.', $item->context_type);

        if ($verb == 'createMilestone') {

            // Load the milestone
            $milestone  = FD::table('Milestone');
            $milestone->load($item->uid);

            // Set the milestone title as the content
            $item->content  = $milestone->title;

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($milestone->owner_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_LIKES_YOUR_MILESTONE', count($users)), $names);
                $item->content = $milestone->get('title');
                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_LIKES_USERS_MILESTONE', count($users)), $names, FD::user($milestone->owner_id)->getName());
            $item->content = $milestone->get('title');
        }

        if ($verb == 'createTask') {
            // Load the task
            $task  = FD::table('Task');
            $task->load($item->uid);

            // Set the milestone title as the content
            $item->content  = $task->title;

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($task->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_LIKES_YOUR_TASK', count($users)), $names);

                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_LIKES_USERS_TASK', count($users)), $names, FD::user($task->user_id)->getName());
        }

        return $item;
    }

}
