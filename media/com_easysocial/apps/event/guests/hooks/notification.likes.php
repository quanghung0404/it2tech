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

class SocialEventAppGuestsHookNotificationLikes
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

        $event = FD::event($item->uid);

        $owner = FD::user($item->getParams()->get('owner_id'));

        // Verbs
        // makeadmin
        // going
        // notgoing

        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_MAKEADMIN_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_MAKEADMIN_PLURAL
        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_GOING_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_GOING_PLURAL
        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_NOTGOING_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_NOTGOING_PLURAL
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_MAKEADMIN_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_MAKEADMIN_PLURAL
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_GOING_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_GOING_PLURAL
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_NOTGOING_SINGULAR
        // APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_NOTGOING_PLURAL

        if ($item->cmd == 'likes.involved') {
            $item->title = JText::_('COM_EASYSOCIAL_LIKES_INVOLVED_SYSTEM_TITLE');

            return $item;
        }

        if ($item->target_type === SOCIAL_TYPE_USER && $item->target_id == $owner->id) {
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_USER_EVENTS_GUESTS_USER_LIKES_YOUR_UPDATE_' . strtoupper($verb), count($users)), $names);

            return $item;
        }

        $item->title = JText::sprintf(FD::string()->computeNoun('APP_USER_EVENTS_GUESTS_USER_LIKES_USERS_UPDATE_' . strtoupper($verb), count($users)), $names, $owner->getName());

        return $item;
    }

}
