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

class SocialEventAppTasksHookNotificationComments
{
    public function execute($item)
    {
        // Get comment participants
        $model = FD::model('Comments');
        $users = $model->getParticipants($item->uid, $item->context_type);

        // Merge to include actor, diff to exclude self, unique to remove dups, and values to reset the index
        $users = array_values(array_unique(array_diff(array_merge($users, array($item->actor_id)), array(FD::user()->id))));

        // Convert the names to stream-ish
        $names = FD::string()->namesToNotifications($users);

        // By default content is always empty;
        $content = '';

        // Only show the content when there is only 1 item
        if (count($users) == 1) {
            // Legacy fix for prior to 1.2 as there is no content stored.
            if (!empty($item->content)) {
                $content = JString::substr(strip_tags($item->content), 0, 30);

                if (JString::strlen($item->content) > 30) {
                    $content .= JText::_('COM_EASYSOCIAL_ELLIPSES');
                }
            }
        }

        $item->content = $content;

        list($element, $group, $verb) = explode('.', $item->context_type);

        if ($verb == 'createMilestone') {

            // Load the milestone
            $milestone = FD::table('Milestone');
            $milestone->load($item->uid);

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($milestone->owner_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_COMMENTED_ON_YOUR_MILESTONE', count($users)), $names);
                $item->content = $milestone->get('title');

                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_COMMENTED_ON_USERS_MILESTONE', count($users)), $names, FD::user($milestone->owner_id)->getName());
            $item->content = $milestone->get('title');
        }

        if ($verb == 'createTask') {
            // Load the task
            $task = FD::table('Task');
            $task->load($item->uid);

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($task->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_COMMENTED_ON_YOUR_TASK', count($users)), $names);

                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_TASKS_USER_COMMENTED_ON_USERS_TASK', count($users)), $names, FD::user($task->user_id)->getName());
        }

        return $item;
    }

}
