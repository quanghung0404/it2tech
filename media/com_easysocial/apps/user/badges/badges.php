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
class SocialUserAppBadges extends SocialAppItem
{

	/**
	 * Renders the notification item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad( SocialTableNotification $item )
	{

		// When a user comments your recently achievement activity stream
		if ( stristr($item->context_type, 'badges.user.unlocked') !== false && $item->type == 'comments') {

			$hook	= $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

		// When a user likes your recently achievement activity stream
		if ( stristr($item->context_type, 'badges.user.unlocked') !== false && $item->type == 'likes') {

			$hook	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		$allowed 			= array( 'badges.unlocked' );

		if( !in_array( $item->cmd , $allowed ) )
		{
			return;
		}

		// Process notifications for followers
		if ($item->cmd == 'badges.unlocked') {
			
			$badge = FD::table('Badge');
			$badge->load( $item->uid );

			// lets load 3rd party component's language file if this is not a core badge
			if ($badge->extension && $badge->extension != 'com_easysocial') {
				Foundry::language()->load( $badge->extension , JPATH_ROOT );
			}

			$item->title = JText::sprintf('APP_USER_BADGES_NOTIFICATIONS_YOU_HAVE_JUST_UNLOCKED' , $badge->get('title'));
		}


		return $item;
	}

	/**
	 * Responsible to process notifications for likes when someone likes the achieved action
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		// We need to split it because the type now stores as badges.user.unlocked.[9999]
		$namespace 	= explode('.', $likes->type);

		array_shift($namespace);

		$context = implode('.', $namespace);

		if (count($namespace) < 4 || $context != 'badges.user.unlocked') {
			return;
		}

		list($element, $group, $verb, $owner) = $namespace;

		// Get the permalink of the achievement item which is the stream item
		$streamItem 	= FD::table('StreamItem');
		$state = $streamItem->load(array('context_type' => $element, 'verb' => $verb, 'actor_id' => $owner, 'actor_type' => $group));

		if (!$state) {
			return;
		}

		$emailOptions = array(
			'title' => 'APP_USER_BADGES_EMAILS_LIKE_ITEM_TITLE',
			'template' => 'apps/user/badges/like.item',
			'permalink' => $streamItem->getPermalink(true, true)
		);

		$systemOptions	= array(
			'context_type' => $likes->type,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $likes->created_by,
			'uid' => $likes->uid,
			'aggregate' => true
		);

		if ($likes->created_by != $owner) {
			FD::notify('likes.item', array($owner), $emailOptions, $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item.
		$recipients 	= $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb . '.' . $owner, array(), array($owner, $likes->created_by));

		$emailOptions['title'] = 'APP_USER_BADGES_EMAILS_LIKE_INVOLVED_TITLE';
		$emailoptions['template'] = 'apps/user/badges/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	/**
	 * Processes notifications when a comment is stored on the site
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		// We need to split it because the type now stores as badges.user.unlocked.[9999]
		$namespace = explode('.', $comment->element);

		// get the corret context without ownerId
		$contexts = explode('.', $comment->element);
		array_pop($contexts);
		$context = implode('.', $contexts);

		if (count($namespace) < 4 || $context != 'badges.user.unlocked') {
			return;
		}

		list($element, $group, $verb, $owner) = $namespace;

		// Get the permalink of the achievement item which is the stream item
		$streamItem 	= FD::table('StreamItem');
		$state = $streamItem->load(array('context_type' => $element, 'verb' => $verb, 'actor_id' => $owner, 'actor_type' => $group, 'uid' => $comment->stream_id));

		if (!$state) {
			return;
		}

		$emailOptions = array(
			'title' => 'APP_USER_BADGES_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/badges/comment.item',
			'permalink' => $streamItem->getPermalink(true, true),
			'comment' => $comment->comment
		);

		$systemOptions	= array(
			'context_type' => $comment->element,
			'content' => $comment->comment,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $comment->created_by,
			'uid' => $comment->uid,
			'aggregate' => true
		);

		if ($comment->created_by != $owner) {
			FD::notify('comments.item', array($owner), $emailOptions, $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item.
		$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb . '.' . $owner, array(), array($owner, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_BADGES_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/badges/comment.involved';

		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
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
		if( $item->context != 'badges' )
		{
			return;
		}

		// Get the context id.
		$id 		= $item->contextId;

		// Get the actor
		$actor 		= $item->actor;

		// Get the badge
		$badge 		= FD::table( 'Badge' );
		$badge->load( $id );

		$this->set( 'badge' , $badge );
		$this->set( 'actor' , $actor );

		$item->title 	= parent::display( 'logs/' . $item->verb );

		if( $includePrivacy )
		{
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			// item->uid is now streamitem.id
			$item->privacy 	= $privacy->form( $item->uid , SOCIAL_TYPE_ACTIVITY, $item->actor->id, 'core.view', false, $item->aggregatedItems[0]->uid );
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
		if( $item->context_type != 'badges' )
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$uid		= $item->id;
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			$sModel = FD::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );

			if( $aItem )
			{
				$uid 	= $aItem[0]->id;

				if( !$privacy->validate( 'core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id ) )
				{
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

	public function onStreamValidatePrivacy( SocialStreamItem $item )
	{
		$my 		= FD::user();
		$privacy	= FD::privacy( $my->id );

		$tbl		= FD::table( 'StreamItem' );
		$tbl->load( array('uid' => $item->uid ) );

		if(! $privacy->validate( 'core.view', $tbl->id , SOCIAL_TYPE_ACTIVITY, $item->actor->id ) )
		{
			return false;
		}

		return true;
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
		$obj->color		= '#FEBC9D';
		$obj->icon 		= 'fa fa-trophy';
		$obj->label 	= 'APP_USER_BADGES_STREAM_TOOLTIP';

		return $obj;
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

		if(! $params->get('stream_achieved', true)) {
			$exclude['badges'] = true;
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
	public function onPrepareStream( SocialStreamItem &$item )
	{
		// Load up the config object
		$config 	= FD::config();

		if( $item->context != 'badges' || !$config->get( 'badges.enabled' ) )
		{
			return;
		}

		// Check if the app should be able to generate the stream.
		$params 	= $this->getParams();

		if( !$params->get( 'stream_achieved' , true ) )
		{
			return;
		}

		// Get the actor
		$actor = $item->actor;

		// check if the actor is ESAD profile or not, if yes, we skip the rendering. 
		// the same goes with blocked user on the site.
		if (!$actor->hasCommunityAccess() || $actor->block) {
			$item->title = '';
			return;
		}

		// Test if stream item is allowed
		if( !$this->onStreamValidatePrivacy( $item ) )
		{
			return;
		}

		// Try to get the badge object from the params
		$raw 		= $item->params;
		$badge 		= FD::table( 'Badge' );

		$badge->load( $item->contextId );

		// lets load 3rd party component's language file if this is not a core badge
		if ($badge->extension && $badge->extension != 'com_easysocial') {
			Foundry::language()->load( $badge->extension , JPATH_ROOT );
		}

		// Set the display mode to be full.
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#FEBC9D';
		$item->fonticon = 'fa fa-trophy';
		$item->label 	= FD::_( 'APP_USER_BADGES_STREAM_TOOLTIP', true );

		// Format the likes for the stream
		$likes 			= FD::likes();
		$likes->get( $item->contextId , $item->context, $item->verb . '.' . $item->actor->id , SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes	= $likes;

		// Apply comments on the stream
		$comments			= FD::comments( $item->contextId , $item->context, $item->verb . '.' . $item->actor->id , SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $item->uid ) ) ), $item->uid );
		$item->comments 	= $comments;


		$this->set( 'badge' , $badge );
		$this->set( 'actor' , $actor );


		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content	= parent::display( 'streams/' . $item->verb . '.content' );

		return true;
	}
}
