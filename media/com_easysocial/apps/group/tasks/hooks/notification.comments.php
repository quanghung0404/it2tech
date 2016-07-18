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

FD::import( 'admin:/includes/apps/apps' );

/**
 * Hook for likes
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppTasksHookNotificationComments
{
	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute($item)
    {
        // Get comment participants
        $model      = FD::model('Comments');
        $users      = $model->getParticipants($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        // Exclude myself from the list of users.
        // Ensure that the values are unique
        $users[] = $item->actor_id;

        $users = array_values(array_unique(array_diff($users, array(Foundry::user()->id))));

        // Convert the names to stream-ish
        $names  = Foundry::string()->namesToNotifications($users);

        $plurality = count($users) > 1 ? '_PLURAL' : '_SINGULAR';

        // By default content is always empty;
        $content    = '';

        // Only show the content when there is only 1 item
        if (count($users) == 1 && !empty($item->content)) {
            $content = JString::substr(strip_tags($item->content), 0, 30);

            if (JString::strlen($item->content) > 30) {
                $content .= JText::_('COM_EASYSOCIAL_ELLIPSES');
            }
        }

        // Load the milestone
        $milestone  = FD::table('Milestone');
        $state = $milestone->load($item->uid);

        if (!$state) {
            return;
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if($milestone->owner_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER ) {
            $item->title    = JText::sprintf('APP_GROUP_TASKS_USER_COMMENTED_ON_YOUR_MILESTONE', $names );

            return $item;
        }

        if ($milestone->owner_id == $item->actor_id && count($users) == 1) {
            $item->title    = JText::sprintf('APP_GROUP_TASKS_USER_COMMENTED_ON_THEIR_MILESTONE', $names );
            return $item;
        }

        // This is for 3rd party viewers
        $item->title = JText::sprintf('APP_GROUP_TASKS_USER_COMMENTED_ON_USERS_MILESTONE', $names , Foundry::user($milestone->owner_id)->getName() );

        return $item;
    }

}
