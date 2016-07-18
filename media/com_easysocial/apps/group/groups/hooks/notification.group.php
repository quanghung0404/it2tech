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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class SocialGroupAppGroupsHookNotificationGroup
{
    /**
     * Processes likes notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function execute(&$item)
    {
       // For rejection, we know that there's always only 1 target
        if ($item->cmd == 'groups.promoted') {

            // Get the group
            $group  = FD::group($item->uid);

            $item->title    = JText::sprintf('APP_GROUP_GROUPS_YOU_HAVE_BEEN_PROMOTED_AS_THE_GROUP_ADMIN', $group->getName());
            $item->image    = $group->getAvatar();
            return;
        }

        // For rejection, we know that there's always only 1 target
        if ($item->cmd == 'groups.user.rejected') {

            // Get the group
            $group  = FD::group($item->uid);

            $item->title    = JText::sprintf('APP_GROUP_GROUPS_YOUR_APPLICATION_HAS_BEEN_REJECTED', $group->getName());

            return;
        }

        // For user removal, we know that there's always only 1 target
        if ($item->cmd == 'groups.user.removed') {

            // Get the group
            $group  = FD::group($item->uid);

            $item->title    = JText::sprintf('APP_GROUP_GROUPS_YOU_HAVE_BEEN_REMOVED_FROM_GROUP', $group->getName());

            return;
        }
    }

}
