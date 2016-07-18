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
class SocialUserAppGroupsHookNotificationLikes
{
	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute($item, $verb)
    {
        // Get the owner of the stream item since we need to notify the person
        $stream     = FD::table( 'Stream' );
        $stream->load($item->uid);

        // Get comment participants
        $model      = FD::model('Likes');
        $users      = $model->getLikerIds($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users      = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users      = array_unique($users);
        $users      = array_values($users);

        // Exclude myself from the list of users.
        $index      = array_search( FD::user()->id , $users );

        if( $index !== false )
        {
            unset( $users[ $index ] );

            $users  = array_values( $users );
        }

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // Get the group from the stream
        $group  = FD::group($stream->cluster_id);

        // Set the content
        if ($group) {
            $item->content  = $group->getName();
            $item->image    = $group->getAvatar();
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if( $stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
        {
            $item->title    = JText::sprintf('APP_USER_GROUPS_NOTIFICATIONS_USER_LIKES_YOUR_GROUP_' . strtoupper($verb) , $names );

            return $item;
        }

        // This is for 3rd party viewers
        $item->title    = JText::sprintf('APP_USER_GROUPS_NOTIFICATIONS_USER_LIKES_USERS_GROUP_' . strtoupper($verb) , $names , FD::user( $stream->actor_id )->getName() );

        return $item;
    }

}
