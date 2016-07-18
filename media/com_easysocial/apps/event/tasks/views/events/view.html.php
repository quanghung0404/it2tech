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

class TasksViewEvents extends SocialAppsView
{
    /**
     * Displays the application output in the canvas.
     *
     * @since   1.3
     * @access  public
     * @param   integer $eventId    The event id.
     */
    public function display($eventId = null, $docType = null)
    {
        $event = FD::event($eventId);

        // Check if the viewer is allowed here.
        if (!$event->canViewItem()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Get app params
        $params = $this->app->getParams();

        $options = array();

        // Determines if we should populate completed milestones
        if ($params->get('display_completed_milestones', true)) {
            $options['completed'] = true;
        }

        $model = FD::model('Tasks');
        $milestones = $model->getMilestones($event->id, SOCIAL_TYPE_EVENT, $options);

        $this->set('milestones', $milestones);
        $this->set('params', $params);
        $this->set('event', $event);

        echo parent::display('views/default');
    }

}
