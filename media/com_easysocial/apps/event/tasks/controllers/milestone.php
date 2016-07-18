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

class TasksControllerMilestone extends SocialAppsController
{
    /**
     * Displays delete confirmation dialog
     *
     * @since    1.2
     * @access    public
     */
    public function delete()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Ensure that the user is logged in.
        FD::requireLogin();

        $ajax = FD::ajax();

        // Get the event
        $eventId = JRequest::getInt('eventId', 0);
        $event = FD::event($eventId);

        $my = FD::user();

        // Check if the user is allowed to create a discussion
        if (!$event->getGuest()->isGuest() && !$my->isSiteAdmin()) {
            return $ajax->reject();
        }

        $id = JRequest::getInt('id');
        $milestone = FD::table('Milestone');
        $milestone->load($id);

        if (!$id || !$milestone->id || $milestone->uid != $event->id) {
            return $ajax->reject();
        }

        $milestone->delete();

        // @points: events.milestone.delete
        $points = FD::points();
        $points->assign('events.milestone.delete', 'com_easysocial', $milestone->user_id);

        return $ajax->resolve();
    }

    /**
     * Displays delete confirmation dialog
     *
     * @since    1.2
     * @access    public
     */
    public function confirmDelete()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Ensure that the user is logged in.
        FD::requireLogin();

        $ajax = FD::ajax();

        // Get the event
        $eventId = JRequest::getInt('eventId', 0);
        $event = FD::event($eventId);

        $user = FD::user();

        // Check if the user is allowed to create a discussion
        if (!$event->getGuest()->isGuest() && !$user->isSiteAdmin()) {
            return $ajax->reject();
        }

        $theme = FD::themes();
        $contents = $theme->output('apps/event/tasks/views/dialog.delete');

        $ajax->resolve($contents);
    }

    /**
     * Unresolve a milestone
     *
     * @since    1.2
     * @access    public
     */
    public function unresolve()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Ensure that the user is logged in.
        FD::requireLogin();

        $ajax = FD::ajax();

        // Get the current logged in user.
        $my = FD::user();

        // Get the event
        $eventId = JRequest::getInt('eventId', 0);
        $event = FD::event($eventId);

        // Check if the user is allowed to create a discussion
        if (!$event->getGuest()->isGuest() && !$my->isSiteAdmin()) {
            FD::info()->set(JText::_('APP_EVENT_TASKS_NOT_ALLOWED_HERE'), SOCIAL_MSG_ERROR);

            // Perform a redirection
            return JFactory::getApplication()->redirect(FRoute::dashboard());
        }

        // Load up the data
        $id = JRequest::getInt('id');
        $milestone = FD::table('Milestone');
        $milestone->load($id);

        if (!$id || !$milestone->id) {
            return $ajax->reject();
        }

        $milestone->state = SOCIAL_TASK_UNRESOLVED;

        $milestone->store();

        return $ajax->resolve();
    }

    /**
     * Resolves a milestone
     *
     * @since    1.2
     * @access    public
     */
    public function resolve()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Ensure that the user is logged in.
        FD::requireLogin();

        $ajax = FD::ajax();

        // Get the current logged in user.
        $my = FD::user();

        // Get the event
        $eventId = JRequest::getInt('eventId', 0);
        $event = FD::event($eventId);

        // Check if the user is allowed to create a discussion
        if (!$event->getGuest()->isGuest() && !$my->isSiteAdmin()) {
            FD::info()->set(JText::_('APP_EVENT_TASKS_NOT_ALLOWED_HERE'), SOCIAL_MSG_ERROR);

            // Perform a redirection
            return JFactory::getApplication()->redirect(FRoute::dashboard());
        }

        // Load up the data
        $id = JRequest::getInt('id');
        $milestone = FD::table('Milestone');
        $milestone->load($id);

        if (!$id || !$milestone->id) {
            return $ajax->reject();
        }

        $milestone->state = SOCIAL_TASK_RESOLVED;

        $milestone->store();

        return $ajax->resolve();
    }

    /**
     * Creates a new milestone for tasks
     *
     * @since    1.2
     * @access    public
     */
    public function save()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Ensure that the user is logged in.
        FD::requireLogin();

        // Get the current logged in user.
        $my = FD::user();

        // Get the event
        $eventId = JRequest::getInt('cluster_id', 0);
        $event = FD::event($eventId);

        // Check if the user is allowed to create a discussion
        if (!$event->getGuest()->isGuest() && !$my->isSiteAdmin()) {
            FD::info()->set(JText::_('APP_EVENT_TASKS_NOT_ALLOWED_HERE'), SOCIAL_MSG_ERROR);

            // Perform a redirection
            return JFactory::getApplication()->redirect(FRoute::dashboard());
        }

        // Get the milestone data
        $id = JRequest::getInt('id');
        $milestone = FD::table('Milestone');
        $milestone->load($id);

        $milestone->title = JRequest::getVar('title');
        $milestone->uid = (int) $event->id;
        $milestone->type = SOCIAL_TYPE_EVENT;
        $milestone->state = SOCIAL_TASK_UNRESOLVED;
        if ($event->getGuest()->isGuest()) {
            $milestone->user_id = JRequest::getInt('user_id');
        }
        $milestone->description = JRequest::getVar('description');
        $milestone->due = JRequest::getVar('due');
        $milestone->owner_id = (int) $my->id;
        $milestone->store();

        // Get the app
        $app = $this->getApp();

        // Get the application params
        $params = $app->getParams();

        // Get the redirection url
        $url = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'milestoneId' => $milestone->id), false);

        // If this is new milestone, perform some tasks
        if (!$id) {
            // Generate a new stream
            if ($params->get('stream_milestone', true)) {
                $milestone->createStream('createMilestone');
            }

            if ($params->get('notify_milestone', true)) {
                $event->notifyMembers('milestone.create', array('userId' => $my->id, 'id' => $milestone->id, 'title' => $milestone->title, 'content' => $milestone->getContent(), 'permalink' => $url));
            }

            // @points: events.milestone.create
            // Add points to the user that updated the event
            $points = FD::points();
            $points->assign('events.milestone.create', 'com_easysocial', $my->id);
        }

        FD::info()->set(JText::_('APP_EVENT_TASKS_MILESTONE_CREATED'));

        // Perform a redirection
        $this->redirect($url);
    }
}
