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

class TasksViewForm extends SocialAppsView
{
    /**
     * Displays the application output in the canvas.
     *
     * @since   1.3
     * @access  public
     * @param   integer $uid    The event id.
     */
    public function display($uid = null, $docType = null)
    {
        $event = FD::event($uid);

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

        if (!empty($milestone->id)) {
            FD::page()->title(JText::_('APP_EVENT_TASKS_TITLE_EDITING_MILESTONE'));
        } else {
            FD::page()->title(JText::_('APP_EVENT_TASKS_TITLE_CREATE_MILESTONE'));
        }

        // Get a list of members from the group
        $members = FD::model('Events')->getMembers($event->id);

        $this->set('members', $members);
        $this->set('milestone', $milestone);
        $this->set('params', $params);
        $this->set('event', $event);

        echo parent::display('views/form');
    }

}
