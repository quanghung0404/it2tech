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

class FilesWidgetsEvents extends SocialAppsWidgets
{
    public function sidebarBottom($eventId)
    {
        $params = $this->app->getParams();

        if (!$params->get('widget')) {
            return;
        }

        $event = FD::event($eventId);
        $theme = FD::themes();

        $limit = $params->get('widget_total', 5);
        $model = FD::model('Files');
        $options = array('limit' => $limit);
        $files = $model->getFiles($event->id, SOCIAL_TYPE_EVENT, $options);

        $theme->set('files', $files);

        echo $theme->output('themes:/apps/event/files/widgets/widget.files');
    }
}
