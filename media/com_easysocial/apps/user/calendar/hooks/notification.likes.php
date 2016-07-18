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

class SocialUserAppCalendarHookNotificationLikes
{
    public function execute($item, $calendar)
    {
        $model = FD::model('likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

        // Include the notification actor
        $users[] = $item->actor_id;

        // Exclude the current user
        $users = array_values(array_unique(array_diff($users, array(FD::user()->id))));

        $names = FD::string()->namesToNotifications($users);

        $plurality = count($users) > 1 ? '_PLURAL' : '_SINGULAR';

        if ($calendar->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $item->title = JText::sprintf('APP_USER_CALENDAR_USER_LIKES_YOUR_EVENT' . $plurality, $names);

            return $item;
        }

        if ($calendar->user_id == $item->actor_id && count($users) == 1) {
            $item->title = JText::sprintf('APP_USER_CALENDAR_OWNER_LIKES_EVENT' . FD::user($calendar->user_id)->getGendarLang(), $names);

            return $item;
        }

        $item->title = JText::sprintf('APP_USER_CALENDAR_USER_LIKES_USER_EVENT' . $plurality, $names, FD::user($calendar->user_id)->getName());

        return $item;
    }
}
