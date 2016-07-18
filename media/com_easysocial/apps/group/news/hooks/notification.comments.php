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

class SocialGroupAppNewsHookNotificationComments
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
        // Get comment participants
        $model      = FD::model( 'Comments' );
        $users      = $model->getParticipants($item->uid, $item->context_type);

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

        // When someone likes on the photo that you have uploaded in a group
        if ($item->context_type == 'news.group.create') {

            // Get the news object
            $news       = FD::table('ClusterNews');
            $news->load($item->uid);

            // Get the group from the stream
            $group      = FD::group($news->cluster_id);

            // Set the content
            if ($group) {
                $item->image    = $group->getAvatar();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if( $news->created_by == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
            {
                $item->title    = JText::sprintf('APP_GROUP_NEWS_USER_COMMENTED_ON_YOUR_ANNOUNCEMENT', $names, $group->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $item->title    = JText::sprintf('APP_GROUP_NEWS_USER_COMMENTED_ON_USERS_ANNOUNCEMENT', $names, FD::user($news->created_by)->getName(), $group->getName());

            return;
        }

        return;
    }

}
