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

class SocialEventAppEventsHookNotificationComments
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

        list($element, $group, $verb) = explode('.', $item->context_type);

        $item->content = $content;

        $event = FD::event($item->uid);

        $owner = FD::user($item->getParams()->get('owner_id'));

        // Verbs
        // create
        // feature
        // update

        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_CREATE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_CREATE_PLURAL
        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_FEATURE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_FEATURE_PLURAL
        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_UPDATE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_UPDATE_PLURAL
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_CREATE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_CREATE_PLURAL
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_UPDATE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_UPDATE_PLURAL
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_FEATURE_SINGULAR
        // APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_FEATURE_PLURAL

        if ($item->target_type === SOCIAL_TYPE_USER && $item->target_id == $owner->id) {
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_USER_EVENTS_USER_COMMENTED_ON_YOUR_EVENT_' . strtoupper($verb), count($users)), $names);

            return $item;
        }

        $item->title = JText::sprintf(FD::string()->computeNoun('APP_USER_EVENTS_USER_COMMENTED_ON_USERS_EVENT_' . strtoupper($verb), count($users)), $names, $owner->getName());

        return $item;
    }
}
