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
class SocialUserAppNotesHookNotificationLikes
{
	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute($item, $note)
    {
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

        $plurality  = count($users) > 1 ? '_PLURAL' : '_SINGULAR';

        // Set the title of the note
        $item->content  = $note->title;

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if( $note->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER )
        {
            $item->title    = JText::sprintf('APP_USER_NOTES_USER_LIKES_YOUR_NOTE' . $plurality, $names );

            return $item;
        }

        if ($note->user_id == $item->actor_id && count($users) == 1) {
            $item->title = JText::sprintf('APP_USER_NOTES_OWNER_LIKES_NOTE' . FD::user($note->user_id)->getGenderLang(), $names);

            return $item;
        }

        // This is for 3rd party viewers
        $item->title    = JText::sprintf('APP_USER_NOTES_USER_LIKES_USERS_NOTE' . $plurality, $names , FD::user( $note->user_id )->getName() );

        return $item;
    }

}
