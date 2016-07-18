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

class DiscussionsWidgetsEvents extends SocialAppsWidgets
{
    /**
     * Display admin actions for the group
     *
     * @since    1.2
     * @access    public
     * @param    string
     * @return
     */
    public function eventAdminStart($event)
    {
        if ($this->app->state == SOCIAL_STATE_UNPUBLISHED) {
            return;
        }

        if (!$event->getParams()->get('discussions', true)) {
            return;
        }

        $model = FD::model('Discussions');

        $theme = FD::themes();
        $theme->set('event', $event);
        $theme->set('app', $this->app);

        echo $theme->output('themes:/apps/event/discussions/widgets/widget.menu');
    }
}
