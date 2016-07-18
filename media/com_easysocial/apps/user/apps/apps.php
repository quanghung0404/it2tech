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
class SocialUserAppApps extends SocialAppItem
{
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
		if( $item->context_type != SOCIAL_TYPE_APPS ) {
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
		$obj->color		= '#553982';
		$obj->icon 		= 'fa fa-cube';
		$obj->label 	= 'APP_USER_APPS_STREAM_TOOLTIP';

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

		if(! $params->get('stream_install', true)) {
			$exclude[SOCIAL_TYPE_APPS] = true;
		}
	}

	/**
	 * Trigger for onPrepareStream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We only want to process related items
		if( $item->context != SOCIAL_TYPE_APPS )
		{
			return;
		}

		// Get application params to see if we should render the stream
		$params 	= $this->getParams();

		if( !$params->get( 'stream_install' , true ) )
		{
			return;
		}

		// Get current logged in user.
		$my         = FD::user();

		// Define a color for the context
		$item->color	= '#553982';
		$item->fonticon = 'fa fa-cube';
		$item->label	= FD::_( 'APP_USER_APPS_STREAM_TOOLTIP', true );
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;

		// Get user's privacy.
		$privacy 	= FD::privacy( $my->id );

		$verb 		= strtolower( $item->verb );
		$method 	= 'prepare' .ucfirst( $verb ) . 'Stream';

		$this->$method( $item );


		// Format the likes for the stream
		$likes 			= FD::likes();
		$likes->get($item->contextId, $item->context, $item->verb . '.' . $item->actor->id, SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes	= $likes;

		// Apply comments on the stream
		$comments			= FD::comments( $item->contextId , $item->context, $item->verb . '.' . $item->actor->id , SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $item->uid ) ) ), $item->uid );
		$item->comments 	= $comments;


		return true;
	}

	/**
	 * Formats the activity log
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We only want to process related items
		if( $item->context != SOCIAL_TYPE_APPS )
		{
			return;
		}

		// Get the necessary data from the stream
		$element 	= $item->context;
		$data 		= FD::makeObject($item->params);

		$app 		= FD::table( 'App' );
		$app->bind($data);

		$this->set( 'app' , $app );

		// Display the title
		$item->title 	= parent::display( 'logs/' . $item->verb . '.title' );

		return true;
	}

	/**
	 * Prepares the stream item for installed apps
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareInstallStream( &$item )
	{
		// Get the params
		$data 		= FD::makeObject( $item->params );
		$element	= $item->context;
		$appId 		= $item->contextId;
		$actor 		= $item->actor;
		$my 		= FD::user();

		// Load up the app table
		$app 		= FD::table( 'App' );

		// If the data exists, we just bind it back instead of needing to reload it again.
		if( $data )
		{
			$app->bind( $data );
		}
		else
		{
			$app->load( $appId );
		}

		// Determine if the current viewer has already installed this app.
		$installed	= $app->isInstalled( $my->id );

		$this->set( 'installed' , $installed );
		$this->set( 'actor'		, $actor );
		$this->set( 'app'		, $app );
		$this->set( 'uid'		, $item->uid );


		// Display the title
		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content 	= parent::display( 'streams/' . $item->verb . '.content' );
	}

	public function onAfterLikeSave($likes)
	{
		$segments = explode('.', $likes->type);

		$userid = array_pop($segments);

		$context = implode('.', $segments);

		$allowed = array('apps.user.install');

		if (!in_array($context, $allowed)) {
			return;
		}

		list($element, $group, $verb) = $segments;

		$streamItem = FD::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $likes->uid));

		if (!$state) {
			return;
		}

		$emailOptions = array(
			'title' => 'APP_USER_APPS_EMAILS_LIKE_ITEM_TITLE',
			'template' => 'apps/user/apps/like.item',
			'permalink' => $streamItem->getPermalink(true, true)
		);

		$systemOptions = array(
			'title' => '',
			'context_type' => $likes->type,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $likes->created_by,
			'uid' => $likes->uid,
			'aggregate' => true
		);

		if ($likes->created_by != $userid) {
			FD::notify('likes.item', array($userid), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($userid, $likes->created_by));

		$emailOptions['title'] = 'APP_USER_APPS_EMAILS_LIKE_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/apps/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onAfterCommentSave($comment)
	{
		$segments = explode('.', $comment->element);

		$userid = array_pop($segments);

		$context = implode('.', $segments);

		$allowed = array('apps.user.install');

		if (!in_array($context, $allowed)) {
			return;
		}

		list($element, $group, $verb) = $segments;

		// We restructure the permalink based on the stream item instead of relying on the comments to give us the permalink
		// $permalink = $comment->getPermalink();

		$streamItem = FD::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $comment->uid));

		$emailOptions = array(
			'title' => 'APP_USER_APPS_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/apps/comment.item',
			'permalink' => $streamItem->getPermalink(true, true)
		);

		$systemOptions = array(
			'title' => '',
			'content' => $comment->comment,
			'context_type' => $comment->element,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $comment->created_by,
			'uid' => $comment->uid,
			'aggregate' => true
		);

		if ($comment->created_by != $userid) {
			FD::notify('comments.item', array($userid), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($userid, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_APPS_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/apps/comment.involved';

		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$segments = explode('.', $item->context_type);

		$userid = array_pop($segments);

		$context = implode('.', $segments);

		$allowed = array('apps.user.install');

		if (!in_array($context, $allowed)) {
			return;
		}

		list($element, $group, $verb) = $segments;

		$obj = $this->getHook('notification', $item->type);
		$obj->execute($item);

		return;
	}
}
