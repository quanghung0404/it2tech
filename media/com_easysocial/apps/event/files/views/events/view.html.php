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

class FilesViewEvents extends SocialAppsView
{
    public function display($eventId = null, $docType = null)
    {
        // Load up the event
        $event = FD::event($eventId);

        // Only allow event members access here.
        if (!$event->getGuest()->isGuest()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Load up the explorer library.
        $explorer = FD::explorer($event->id, SOCIAL_TYPE_EVENT);

        // Get total number of files that are already uploaded in the event
        $model = FD::model('Files');
        $total = (int) $model->getTotalFiles($event->id, SOCIAL_TYPE_EVENT);

        // Get the access object
        $access = $event->getAccess();

        // Determines if the event exceeded their limits
        $allowUpload = $access->get('files.max') == 0 || $total < $access->get('files.max') ? true : false;
        $uploadLimit = $access->get('files.maxsize');

        $this->set('uploadLimit', $uploadLimit);
        $this->set('allowUpload', $allowUpload);
        $this->set('explorer', $explorer);
        $this->set('event', $event);

        echo parent::display('events/default');
    }
}
