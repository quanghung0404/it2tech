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

class EasySocialModEventsMenuHelper
{
    public static function getApps(&$params, $event)
    {
        // Load list of apps for this event
        $appsModel = FD::model('Apps');

        // Retrieve apps
        $apps = $appsModel->getEventApps($event->id);

        return $apps;
    }

    public static function getPendingMembers(&$params, $event)
    {
        $options = array();
        $options['state'] = SOCIAL_GROUPS_MEMBER_PENDING;

        $model = FD::model('Events');
        $users = $model->getMembers($event->id, $options);

        return $users;
    }
}
