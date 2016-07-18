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

// We need the router
require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

class DiscussionsViewEvents extends SocialAppsView
{
    /**
     * Displays the application output in the canvas.
     *
     * @since    1.0
     * @access    public
     * @param    int        The user id that is currently being viewed.
     */
    public function display($eventId = null, $docType = null)
    {
        FD::requireLogin();

        $event = FD::event($eventId);

        // Check if the viewer is allowed here.
        if (!$event->canViewItem()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Get app params
        $params = $this->app->getParams();

        $model = FD::model('Discussions');
        $options = array('limit' => $params->get('total', 10));

        $discussions = $model->getDiscussions($event->id, SOCIAL_TYPE_EVENT, $options);
        $pagination = $model->getPagination();
        $pagination->setVar('option', 'com_easysocial');
        $pagination->setVar('view', 'events');
        $pagination->setVar('layout', 'item');
        $pagination->setVar('id', $event->getAlias());
        $pagination->setVar('appId', $this->app->getAlias());

        $this->set('app', $this->app);
        $this->set('params', $params);
        $this->set('pagination', $pagination);
        $this->set('event', $event);
        $this->set('discussions', $discussions);

        echo parent::display('events/default');
    }

}
