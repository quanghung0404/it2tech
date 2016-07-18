<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
class SocialUserAppBadgesHookNotificationComments
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
        // Get the badge
        $badge      = FD::table('Badge');
        $badge->load($item->uid);

        // Break the namespace
        list($element, $group, $verb, $owner)      = explode('.', $item->context_type);

        // Get the permalink of the achievement item which is the stream item
        $streamItem     = FD::table('StreamItem');
        $streamItem->load(array('context_type' => $element, 'verb' => $verb, 'actor_type' => $group, 'actor_id' => $owner));

        // Get comment participants
        $model      = FD::model( 'Comments' );
        $users      = $model->getParticipants($item->uid, $item->context_type);

        $users[] = $item->actor_id;

        $users = array_values(array_unique(array_diff($users, array(FD::user()->id))));

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // Get the badge image
        $item->image    = $badge->getAvatar();

        $plurality = count($users) > 1 ? '_PLURAL' : '_SINGULAR';

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if($owner == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $item->title = JText::sprintf('APP_USER_BADGES_USER_COMMENTED_ON_YOUR_ACHIEVEMENT' . $plurality, $names, $badge->get('title'));

            return $item;
        }

        if ($owner == $item->actor_id && count($users) == 1) {
            $item->title = JText::sprintf('APP_USER_BADGES_OWNER_COMMENTED_ON_ACHIEVEMENT' . FD::user($owner)->getGenderLang(), $names, $badge->get('title'));

            return $item;
        }

        // This is for 3rd party viewers
        $item->title = JText::sprintf('APP_USER_BADGES_USER_COMMENTED_ON_USERS_ACHIEVEMENT' . $plurality, $names, FD::user( $stream->actor_id )->getName(), $badge->get('title'));

        return $item;
    }

}
