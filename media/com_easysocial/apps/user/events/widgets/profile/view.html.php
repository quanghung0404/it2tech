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

class EventsWidgetsProfile extends SocialAppsWidgets
{
    public function sidebarBottom($user)
    {
        if (!FD::config()->get('events.enabled')) {
            return;
        }

        $params = $this->getParams();

        if (!$params->get('widget_profile', true)) {
            return;
        }

        $limit = $params->get('widget_profile_total', 5);

        // Get created events
        $this->getCreatedEvents($user, $limit, $params);
    }

    public function getCreatedEvents(SocialUser $user, $limit, $params)
    {
        $model = FD::model('Events');

        $now = FD::date()->toSql();

        $createdEvents = $model->getEvents(array(
            'creator_uid' => $user->id,
            'creator_type' => SOCIAL_TYPE_USER,
            'state' => SOCIAL_STATE_PUBLISHED,
            'ordering' => 'start',
            'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE),
            'limit' => $limit,
            'ongoing' => true, 
            'upcoming' => true
        ));

        $createdTotal = $model->getTotalEvents(array(
            'creator_uid' => $user->id,
            'creator_type' => SOCIAL_TYPE_USER,
            'state' => SOCIAL_STATE_PUBLISHED,
            'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE),
            'ongoing' => true, 
            'upcoming' => true
        ));

        $attendingEvents = $model->getEvents(array(
            'guestuid' => $user->id,
            'gueststate' => SOCIAL_EVENT_GUEST_GOING,
            'state' => SOCIAL_STATE_PUBLISHED,
            'ongoing' => true, 
            'upcoming' => true,
            'ordering' => 'start',
            'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE),
            'limit' => $limit,
        ));

        $attendingTotal = $model->getTotalEvents(array(
            'guestuid' => $user->id,
            'gueststate' => SOCIAL_EVENT_GUEST_GOING,
            'state' => SOCIAL_STATE_PUBLISHED,
            'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE),
            'ongoing' => true, 
            'upcoming' => true
        ));

        $allowCreate = $user->isSiteAdmin() || $user->getAccess()->get('events.create');

        $total = $createdTotal + $attendingTotal;

        $theme = FD::themes();
        $theme->set('user', $user);
        $theme->set('total', $total);
        $theme->set('createdEvents', $createdEvents);
        $theme->set('createdTotal', $createdTotal);
        $theme->set('attendingEvents', $attendingEvents);
        $theme->set('attendingTotal', $attendingTotal);
        $theme->set('app', $this->app);
        $theme->set('allowCreate', $allowCreate);

        echo $theme->output('themes:/apps/user/events/widgets/profile/events');
    }
}
