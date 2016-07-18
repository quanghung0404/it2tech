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

FD::import('admin:/includes/apps/apps');

class SocialGroupAppEvents extends SocialAppItem
{
    public function getFavIcon()
    {
        $obj = new stdClass();
        $obj->color = '#f06050';
        $obj->icon = 'fa-calendar';
        $obj->label = 'APP_GROUP_EVENTS_STREAM_TOOLTIP';

        return $obj;
    }

    // Stream is prepared on app/event/events

    public function onPrepareStoryPanel($story)
    {
        if ($story->clusterType != SOCIAL_TYPE_GROUP) {
            return;
        }

        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        // If events is disabled, we shouldn't display this
        if (!FD::config()->get('events.enabled')) {
            return;
        }

        // Ensure that the group category has access to create events
        $group = FD::group($story->cluster);
        $access = $group->getAccess();

        if (!$access->get('events.groupevent')) {
            return;
        }

        // Create plugin object
        $plugin = $story->createPlugin('event', 'panel');

        // Get the theme class
        $theme = FD::themes();

        // Get the available event category
        $categories = FD::model('EventCategories')->getCreatableCategories(FD::user()->getProfile()->id);

        $theme->set('categories', $categories);

        $plugin->button->html = $theme->output('apps/user/events/story/panel.button');
        $plugin->content->html = $theme->output('apps/user/events/story/panel.content');

        $script = FD::get('Script');
        $plugin->script = $script->output('apps:/user/events/story');

        return $plugin;
    }

    public function onBeforeStorySave(&$template, &$stream, &$content)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        $in = FD::input();

        $title = $in->getString('event_title');
        $description = $in->getString('event_description');
        $categoryid = $in->getInt('event_category');
        $start = $in->getString('event_start');
        $end = $in->getString('event_end');
        $timezone = $in->getString('event_timezone');

        // If no category id, then we don't proceed
        if (empty($categoryid)) {
            return;
        }

        // Perhaps in the future we use FD::model('Event')->createEvent() instead.
        // For now just hardcode it here to prevent field triggering and figuring out how to punch data into the respective field data because the form is not rendered through field trigger.

        $my = FD::user();

        $event = FD::event();

        $event->title = $title;

        $event->description = $description;

        // Set a default params for this event first
        $event->params = '{"photo":{"albums":true},"news":true,"discussions":true,"allownotgoingguest":false,"allowmaybe":true,"guestlimit":0}';

        // event type will always follow group type
        $event->type = FD::group($template->cluster_id)->type;
        $event->creator_uid = $my->id;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $categoryid;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->alias = FD::model('Events')->getUniqueAlias($title);
        $event->created = FD::date()->toSql();
        $event->key = md5($event->created . $my->password . uniqid());

        $event->state = SOCIAL_CLUSTER_PENDING;

        if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
            $event->state = SOCIAL_CLUSTER_PUBLISHED;
        }

        // Trigger apps
        FD::apps()->load(SOCIAL_TYPE_USER);

        $dispatcher  = FD::dispatcher();
        $triggerArgs = array(&$event, &$my, true);

        // @trigger: onEventBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

        $state = $event->save();

        // Notifies admin when a new event is created
        if ($event->state === SOCIAL_CLUSTER_PENDING || !$my->isSiteAdmin()) {
            FD::model('Events')->notifyAdmins($event);
        }

        // Set the meta for start end timezone
        $meta = $event->meta;
        $meta->cluster_id = $event->id;
        $meta->start = FD::date($start)->toSql();
        $meta->end = FD::date($end)->toSql();
        $meta->timezone = $timezone;

        // Set the group id
        $meta->group_id = $template->cluster_id;

        $meta->store();

        // Recreate the event object
        SocialEvent::$instances[$event->id] = null;
        $event = FD::event($event->id);

        // Create a new owner object
        $event->createOwner($my->id);

        // @trigger: onEventAfterSave
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        // Due to inconsistency, we don't use SOCIAL_TYPE_EVENT.
        // Instead we use "events" because app elements are named with 's', namely users, groups, events.
        $template->context_type = 'events';

        $template->context_id = $event->id;
        $template->cluster_access = $event->type;
        $template->cluster_type = $event->cluster_type;
        $template->cluster_id = $event->id;

        $params = array(
            'event' => $event
        );

        $template->setParams(FD::json()->encode($params));
    }

    public function onBeforeGetStream(&$options, $view = '')
    {
        if ($view != 'groups') {
            return;
        }

        $layout = JRequest::getVar('layout', '');
        if ($layout == 'category') {
            // if this is viewing group category page, we ignore the events stream for groups.
            return;
        }

        // Check if there are any group events
        $groupEvents = FD::model('Events')->getEvents(array(
            'group_id' => $options['clusterId'],
            'state' => SOCIAL_STATE_PUBLISHED,
            'idonly' => true
        ));

        if (count($groupEvents) == 0) {
            return;
        }

        // Support in getting event stream as well
        if (!is_array($options['clusterType'])) {
            $options['clusterType'] = array($options['clusterType']);
        }

        if (!in_array(SOCIAL_TYPE_EVENT, $options['clusterType'])) {
            $options['clusterType'][] = SOCIAL_TYPE_EVENT;
        }

        if (!is_array($options['clusterId'])) {
            $options['clusterId'] = array($options['clusterId']);
        }

        $options['clusterId'] = array_merge($options['clusterId'], $groupEvents);
    }

    /**
     * Determines if this app should be visible in the group page
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function appListing($view, $groupId, $type)
    {
        $group = FD::group($groupId);

        if (!$this->config->get('events.enabled')) {
            return false;
        }

        return $group->getAccess()->get('events.groupevent', true);
    }
}
