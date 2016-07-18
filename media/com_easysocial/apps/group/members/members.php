<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

class SocialGroupAppMembers extends SocialAppItem
{
    /**
     * Processes notification items
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function onBeforeNotificationRedirect(&$notification)
    {
        $allowed = array('group.requested');

        if (!in_array($notification->cmd, $allowed)) {
            return;
        }

        // We want to alter the redirection URL to the apps page
        $group = ES::group($notification->uid);

        // Get the application object 
        $application = $this->getApp();

        // Alter the original notification url.
        $notification->url = $group->getAppsPermalink($application->getAlias());
    }
}
