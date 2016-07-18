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

class SocialGroupAppLinksHookNotificationComments
{
    private function getImage(SocialTableNotification &$item)
    {
        // Get the links that are posted for this stream
        $model      = FD::model( 'Stream' );
        $links      = $model->getAssets( $item->uid , SOCIAL_TYPE_LINKS );

        if( !isset( $links[ 0 ] ) )
        {
            return;
        }

        // Initialize default values
        $link   = $links[ 0 ];
        $actor  = FD::user( $item->actor_id );
        $meta   = FD::registry( $link->data );

        $item->content  = $meta->get( 'link' );

        // If there's an image, use it
        if( $meta->get( 'image' ) )
        {
            return $meta->get( 'image' );
        }

        return false;
    }

    /**
     * Processes comment notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function execute(SocialTableNotification &$item)
    {
        // Get the owner of the stream item since we need to notify the person
        $stream     = FD::table( 'Stream' );
        $stream->load($item->uid);

        // Get comment participants
        $model      = FD::model( 'Comments' );
        $users      = $model->getParticipants($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users      = array_merge( array( $item->actor_id ) , $users );

        // Ensure that the values are unique
        $users      = array_unique( $users );
        $users      = array_values( $users );

        // Exclude myself from the list of users.
        $index      = array_search( FD::user()->id , $users );

        if( $index !== false )
        {
            unset( $users[ $index ] );

            $users  = array_values( $users );
        }

        // By default content is always empty;
        $content    = '';

        // Only show the content when there is only 1 item
        if (count($users) == 1) {

            // Legacy fix for prior to 1.2 as there is no content stored.
            if (!empty($item->content)) {

                $content        = JString::substr( strip_tags( $item->content ) , 0 , 30 );

                if (JString::strlen( $item->content ) > 30) {
                    $content .= JText::_( 'COM_EASYSOCIAL_ELLIPSES' );
                }
            }
        }

        // Set the content to the stream
        $item->content  = $content;

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if( $stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {

            $langString     = FD::string()->computeNoun('APP_USER_LINKS_USER_POSTED_COMMENT_ON_YOUR_LINK', count($users));
            $item->title    = JText::sprintf($langString, $names );

            return $item;
        }

        // This is for 3rd party viewers
        $langString     = FD::string()->computeNoun('APP_USER_LINKS_USER_POSTED_COMMENT_ON_USERS_LINK', count($users));
        $item->title    = JText::sprintf($langString , $names , FD::user($stream->actor_id)->getName());

        return $item;
    }
}
