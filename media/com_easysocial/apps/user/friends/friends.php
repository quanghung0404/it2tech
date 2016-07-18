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
 * Friends application for EasySocial.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppFriends extends SocialAppItem
{
	/**
	 * Notification triggered when generating notification item.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialTableNotification	The notification table object
	 * @return	null
	 */
	public function onNotificationLoad( &$item )
	{
		$allowed 	= array('friends.approve');

		if (!in_array( $item->cmd , $allowed)) {
			return;
		}

		if ($item->cmd == 'friends.approve') {
			$user 			= FD::user($item->actor_id);
			$item->title	= JText::sprintf('APP_USER_FRIENDS_NOTIFICATIONS_USER_ACCEPTED_YOUR_FRIEND_REQUEST', $user->getName());
			$item->image 	= $user->getAvatar();
		}
	}

	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj 			= new stdClass();
		$obj->color		= '#7AD7EE';
		$obj->icon 		= 'fa fa-user';
		$obj->label 	= 'APP_USER_FRIENDS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Responsible to generate the activity contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		if( $item->context != 'friends' )
		{
			return;
		}

		// There should be at least 1 target
		if (!$item->targets) {
			return;
		}

		$my = FD::user();

		// Receiving actor.
		$target		= $item->targets[0];

		// Set the target.
		$this->set( 'actor'	, $item->actor );
		$this->set( 'target'	, $target );

		// User A made friends with user B
		if( $item->verb == 'add' )
		{
			$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		}

		if( $includePrivacy )
		{
			$privacy	= FD::privacy( $my->id );
			$item->privacy 	= $privacy->form( $item->contextId , 'friends', $item->actor->id, 'core.view', false, $item->aggregatedItems[0]->uid );
		}

		return true;
	}


	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'friends' )
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			$sModel = FD::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );

			$contextId = $aItem[0]->context_id;

			if( !$privacy->validate( 'core.view', $contextId, 'friends', $item->actor_id ) )
			{
				$item->cnt = 0;
			}

		}

		return true;
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since	1.2
	 * @access	public
	 * @param	array
	 */
	public function onStreamVerbExclude( &$exclude )
	{
		// Get app params
		$params		= $this->getParams();

		$excludeVerb = false;

		if(! $params->get('stream_friends', true)) {
			$exclude['friends'] = true;
		}
	}


	/**
	 * Responsible to generate the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		if( $item->context != 'friends' )
		{
			return;
		}

		// Determines if the stream should be generated
		$params 	= $this->getParams();

		if( !$params->get( 'stream_friends' , true ) )
		{
			return;
		}

		// Get the actor
		$actor 			= $item->actor;

		// check if the actor is ESAD profile or not, if yes, we skip the rendering.
		if (! $actor->hasCommunityAccess()) {
			$item->title = '';
			return;
		}

		$my         = FD::user();
		$privacy	= FD::privacy( $my->id );

		if( $includePrivacy )
		{
			if(! $privacy->validate( 'core.view', $item->contextId, 'friends', $item->actor->id ) )
			{
				return;
			}
		}

		// Get the context id.
		$id 		= $item->contextId;

		// no target. this could be data error. ignore this item.
		if(! $item->targets )
		{
			return;
		}

		// Receiving actor.
		$target		= $item->targets[ 0 ];

		// Get the current id.
		$id 		= JRequest::getInt( 'id' );

		// Decorate the stream
		$item->display		= SOCIAL_STREAM_DISPLAY_MINI;
		$item->color 		= '#7AD7EE';
		$item->fonticon		= 'fa-user';
		$item->label 		= FD::_( 'APP_USER_FRIENDS_STREAM_TOOLTIP', true );

		// Apply likes on the stream
		$likes 			= FD::likes();
		$likes->get( $item->contextId , $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes	= $likes;

		$this->set( 'actor'		, $actor );
		$this->set( 'target'	, $target );

		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );

		if( $includePrivacy )
		{
			$item->privacy 	= $privacy->form( $item->contextId , 'friends', $item->actor->id, 'core.view', false, $item->uid );
		}

		return true;
	}

	/**
	 * Processes a saved story so that we can notify users who are tagged in the system
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave( &$stream , $streamItem , $streamTemplate )
	{
		// If there's no "with" data, skip this.
		if (!$streamTemplate->with) {
			return;
		}

		// Get list of users that are tagged in this post.
		$taggedUsers 	= $streamTemplate->with;

		// Get the creator of this update
		$poster 		= FD::user( $streamTemplate->actor_id );

		// Get the content of the stream item.
		$content 		= $streamTemplate->content;

		if (!$taggedUsers) {
			return;
		}

		foreach ($taggedUsers as $id) {

			$taggedUser 	= FD::user( $id );


	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_FRIENDS_EMAILS_USER_TAGGED_YOU_IN_POST_SUBJECT',
	            'template'  	=> 'apps/user/friends/post.tagged',
	            'permalink' 	=> $streamItem->getPermalink(true, true),
	            'actor'     	=> $poster->getName(),
	            'actorAvatar'   => $poster->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $poster->getPermalink(true, true),
	            'message'		=> $content
	        );

	        $systemOptions  = array(
	        	'uid'           => $streamItem->id,
	            'context_type'  => 'tagged',
	            'type'			=> 'stream',
	            'url'           => $streamItem->getPermalink(false, false, false),
	            'actor_id'      => $poster->id,
	            'aggregate'     => false
	        );

			// Add new notification item
			FD::notify('stream.tagged',  array($taggedUser->id), $emailOptions, $systemOptions);
		}

		return true;
	}
}
