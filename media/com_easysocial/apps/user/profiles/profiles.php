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
 * Profiles application for EasySocial.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppProfiles extends SocialAppItem
{
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
		$obj->color		= '#FF7553';
		$obj->icon 		= 'fa fa-suitcase';
		$obj->label 	= 'APP_USER_PROFILES_UPDATE_PROFILE_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{

		if ($item->type == 'likes' && $item->context_type == 'userprofile.user.update') {

			$obj	= $this->getHook('notification', 'likes');
			$obj->execute($item);

			return;
		}

		if ($item->type == 'comments' && $item->context_type == 'userprofile.user.update') {

			$obj	= $this->getHook('notification', 'comments');
			$obj->execute($item);

			return;
		}

	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave($likes)
	{
		$allowed = array('userprofile.user.update');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		$stream = FD::table('Stream');
		$state = $stream->load($likes->uid);

		if (!$state) {
			return;
		}

		$owner = $stream->actor_id;

		$emailOptions = array(
			'title'			=> 'APP_USER_PROFILES_EMAILS_LIKE_ITEM_TITLE',
			'template'		=> 'apps/user/profiles/like.item',
			'permalink'		=> $stream->getPermalink(true, true)
		);

		$systemOptions = array(
			'title'			=> '',
			'context_type'	=> $likes->type,
			'url'			=> $stream->getPermalink(false, false, false),
			'actor_id'		=> $likes->created_by,
			'uid'			=> $likes->uid,
			'aggregate'		=> true
		);

		if ($likes->created_by != $owner) {
			FD::notify('likes.item', array($owner), $emailOptions, $systemOptions);
		}


		// Get a list of recipients to be notified for this stream item.
		$recipients 	= $this->getStreamNotificationTargets($likes->uid, 'userprofile', 'user', 'update', array(), array($owner, $likes->created_by));

		$emailOptions['title'] = 'APP_USER_PROFILES_EMAILS_LIKE_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/profiles/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	/**
	 * Processes notifications when a comment is saved
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed 	= array('userprofile.user.update');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		$stream = FD::table('Stream');
		$state = $stream->load($comment->uid);

		if (!$state) {
			return;
		}

		$owner = $stream->actor_id;

		$emailOptions = array(
			'title' => 'APPS_USER_PROFILES_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/profiles/comment.item',
			'permalink' => $stream->getPermalink(true, true)
		);

		$systemOptions 	= array(
			'title' 		=> '',
			'context_type' 	=> $comment->element,
			'url' 			=> $stream->getPermalink(false, false, false),
			'actor_id' 		=> $comment->created_by,
			'uid'			=> $comment->uid,
			'aggregate'		=> true
		);

		if ($comment->created_by != $owner) {
			FD::notify('comments.item', array($owner), $emailOptions, $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item.
		$recipients 	= $this->getStreamNotificationTargets($comment->uid, 'userprofile', 'user', 'update', array(), array($owner, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_PROFILES_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/profiles/comment.involved';

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
		if ($item->context != 'profiles') {
			return;
		}

		$actor	= $item->actor;
		$genderValue = $actor->getFieldData('GENDER');
		$gender = 'THEIR';

		if ($genderValue == 1) {
			$gender = 'MALE';
		}

		if ($genderValue == 2) {
			$gender = 'FEMALE';
		}

		$this->set( 'gender'	, $gender );
		$this->set( 'actor'		, $item->actor );

		$item->title	= parent::display( 'streams/' . $item->verb . '.title' );

		if ( $includePrivacy ) {
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );
			// when in activity, the item->uid is the stream_item.id
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
		if( $item->context_type != 'profiles' )
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

		if(! $params->get('stream_update', true)) {
			$excludeVerb[] = 'update';
		}

		if (! $params->get('stream_register', true)) {
			$excludeVerb[] = 'register';
		}

		if ($excludeVerb !== false) {
			$exclude['profiles'] = $excludeVerb;
		}
	}


	/**
	 * Responsible to generate the stream content for profiles apps.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		if ($item->context != 'profiles') {
			return;
		}

		// Do not render the stream if the user is not activated or being blocked
		// to respect the user privacy
		if ($item->actor->block) {
			return;
		}

		// Get the application params
		$params = $this->getParams();

		if( $item->verb == 'update' && !$params->get( 'stream_update' , true ) )
		{
			return;
		}

		if( $item->verb == 'register' && !$params->get( 'stream_register' , true ) )
		{
			return;
		}

		$my         = FD::user();

		// Decorate the stream
		$item->display	= SOCIAL_STREAM_DISPLAY_MINI;

		if( $item->verb == 'register' )
		{
			$item->color 		= '#FF7553';
			$item->fonticon 	= 'fa-user-plus';
			$item->label		= FD::_( 'APP_USER_PROFILES_REGISTER_STREAM_TOOLTIP', true);
		}

		// When user updates their profile.
		if( $item->verb == 'update' )
		{
			$item->color 	= '#1FBCA7';
			$item->fonticon	= 'fa-suitcase';
			$item->label 	= FD::_( 'APP_USER_PROFILES_UPDATE_PROFILE_STREAM_TOOLTIP', true);
		}

		$actor	= $item->actor;

		if (! $actor->hasCommunityAccess()) {
			$item->title = '';
			return;
		}

		$genderValue = $actor->getFieldData('GENDER');

		$gender = 'THEIR';

		if ($genderValue == 1) {
			$gender = 'MALE';
		}

		if ($genderValue == 2) {
			$gender = 'FEMALE';
		}


		// Override the likes
		$item->likes 	= FD::likes($item->uid, 'userprofile', $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid);

		// Override the comments
		$comments		= FD::comments($item->uid, 'userprofile', $item->verb, SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $item->uid ) ) ), $item->uid );
		$item->comments = $comments;

		$this->set( 'gender'	, $gender );
		$this->set( 'actor'		, $item->actor );

		$item->title	= parent::display( 'streams/' . $item->verb . '.title' );

		return true;
	}

}
