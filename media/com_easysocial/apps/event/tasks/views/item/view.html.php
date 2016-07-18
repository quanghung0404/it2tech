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

class TasksViewItem extends SocialAppsView
{
    public function display($eventId = null, $docType = null)
    {
        $event = FD::event($eventId);

        // Check if the viewer is allowed here.
        if (!$event->canViewItem()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Get app params
        $params = $this->app->getParams();

        // Load the milestone
        $id = JRequest::getInt('milestoneId');
        $milestone = FD::table('Milestone');
        $milestone->load($id);

        // Set the page title
        FD::page()->title($milestone->title);

        // Get a list of members from the group
        $eventModel = FD::model('Events');
        $members = $eventModel->getMembers($event->id);

        // Get a list of tasks for this milestone
        $model = FD::model('Tasks');
        $openTasks = $model->getTasks($milestone->id, array('open' => true));

        // Get a list of closed tasks
        $closedTasks = $model->getTasks($milestone->id, array('closed' => true));

        // Get total open tasks
        $totalOpen = $model->getTotalTasks($milestone->id, array('open' => true));
        $totalClosed = $model->getTotalTasks($milestone->id, array('closed' => true));

        $this->set('totalOpen', $totalOpen);
        $this->set('totalClosed', $totalClosed);
        $this->set('openTasks', $openTasks);
        $this->set('closedTasks', $closedTasks);
        $this->set('members', $members);
        $this->set('milestone', $milestone);
        $this->set('params', $params);
        $this->set('event', $event);

        echo parent::display('views/item');
    }

}
