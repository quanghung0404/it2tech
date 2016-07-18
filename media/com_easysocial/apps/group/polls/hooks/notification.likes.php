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

class SocialGroupAppLinksHookNotificationLikes
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
        // Get likes participants
        $model      = FD::model('Likes');
        $users      = $model->getLikerIds($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users      = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users      = array_unique($users);
        $users      = array_values($users);

        // Exclude myself from the list of users.
        $index      = array_search( FD::user()->id , $users );

        if ($index !== false) {
            unset($users[$index]);
            $users  = array_values($users);
        }

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // Load the stream object
        $stream     = FD::table('Stream');
        $stream->load($item->uid);

        // Get the link assets
        $assets     = $stream->getAssets(SOCIAL_TYPE_LINKS);

        if (!empty($assets)) {

            $asset  = $assets[0];
            $link   = FD::makeObject($asset->data);

            if ($link) {

                if ($link->link) {
                    $item->content  = $link->link;
                }

                if ($link->image) {
                    $item->image    = $link->image;
                }

            }

        }

        // We need to determine if the user is the owner
        if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $langString     = FD::string()->computeNoun('APP_USER_LINKS_NOTIFICATIONS_USER_LIKES_YOUR_LINK_UPDATE', count($users));
            $item->title    = JText::sprintf($langString, $names);

            return;
        }

        // For other users, we just post a generic message
        $langString     = FD::string()->computeNoun('APP_USER_LINKS_NOTIFICATIONS_USER_LIKES_USERS_LINK_UPDATE', count($users));
        $item->title    = JText::sprintf($langString, $names, FD::user($stream->actor_id)->getName());

        return;
    }
}
