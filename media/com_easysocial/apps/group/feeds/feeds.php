<?php
/**
* @package		%PACKAGE%
* @subpackge	%SUBPACKAGE%
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/includes/apps/apps' );

/**
 * Feeds application for EasySocial
 *
 * @since	1.3
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppFeeds extends SocialAppItem
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
		if( $item->context_type != 'feeds' )
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
		$obj->color		= '#e67e22';
		$obj->icon 		= 'fa fa-rss-square';
		$obj->label 	= 'APP_USER_FEED_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Processes notifications for feeds
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed 	= array('feeds.group.create');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}


		if ($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') {

			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		if ($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') {

			$hook 	= $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}
	}

	/**
	 * Notifies the owner when user likes their feed
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed 	= array('feeds.group.create');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// For new feed items
		if ($likes->type == 'feeds.group.create') {

			// Get the RSS feed
			$feed = FD::table('Rss');
			$feed->load($likes->uid);

			// Get the stream since we want to link it to the stream
			$stream = FD::table('Stream');
			$stream->load($likes->stream_id);

			// Get the actor of the likes
			$actor	= FD::user($likes->created_by);

			// Get the owner of the item
			$owner 	= FD::user($feed->user_id);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_FEEDS_EMAILS_LIKE_RSS_FEED_ITEM_SUBJECT',
	            'template'  	=> 'apps/group/feeds/like.feed.item',
	            'permalink' 	=> $stream->getPermalink(true, true),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'context_ids'	=> $stream->id,
	            'url'           => $stream->getPermalink(false, false, false),
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );

	        // Notify the owner of the feed item first
	        if ($feed->user_id != $likes->created_by) {
	        	FD::notify('likes.item', array($feed->user_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, 'feeds', 'group', 'create', array(), array($feed->user_id, $likes->created_by));

	        $emailOptions['title']      = 'APP_USER_FEEDS_EMAILS_LIKE_RSS_FEED_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/group/feeds/like.feed.involved';

	        // Notify other participating users
	        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}
	}

	/**
	 * Notifies the owner when user likes their feed
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		// @legacy
		// photos.user.add should just be photos.user.upload since they are pretty much the same
		$allowed 	= array('feeds.group.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// For new feed items
		if ($comment->element == 'feeds.user.create') {

			// Get the RSS feed
			$feed = FD::table('Rss');
			$feed->load($likes->uid);

			// Get the stream since we want to link it to the stream
			$stream = FD::table('Stream');
			$stream->load($comment->stream_id);

			// Get the actor of the likes
			$actor	= FD::user($comment->created_by);

			// Get the owner of the item
			$owner 	= FD::user($feed->user_id);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_FEEDS_EMAILS_COMMENT_RSS_FEED_ITEM_SUBJECT',
	            'template'  	=> 'apps/group/feeds/comment.feed.item',
	            'permalink' 	=> $stream->getPermalink(true, true),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'target'		=> $owner->getName(),
	            'comment'		=> $comment->comment
	        );

	        $systemOptions  = array(
	            'context_type'  => $comment->element,
	            'context_ids'	=> $stream->id,
	            'url'           => $stream->getPermalink(false, false, false),
	            'actor_id'      => $comment->created_by,
	            'uid'           => $comment->uid,
	            'aggregate'     => true
	        );

	        // Notify the owner of the photo first
	        if ($feed->user_id != $comment->created_by) {
	        	FD::notify('comments.item', array($feed->user_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($comment->uid, 'feeds', 'user', 'create', array(), array($feed->user_id, $comment->created_by));

	        $emailOptions['title']      = 'APP_USER_FEEDS_EMAILS_COMMENT_RSS_FEED_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/group/feeds/comment.feed.involved';

	        // Notify other participating users
	        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}
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

		if (!$params->get('stream_create', true)) {
			$exclude['feeds'] = true;
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		if ($item->context !== 'feeds') {
			return;
		}

		// Get app params
		$params = $this->getParams();

		if (!$params->get('stream_create', true)) {
			return;
		}

		// Get the feed table
		$rss = FD::table('Rss');
		$rss->load($item->contextId);

		if (!$rss->id || !$item->contextId) {
			return;
		}

		$group  = FD::group($item->cluster_id);
		$actor	= $item->actor;
		$app 	= $this->getApp();

		$this->set('app', $app);
		$this->set('rss', $rss);
		$this->set('actor', $actor);
		$this->set('group', $group);

		$item->color 	= '#e67e22';
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->fonticon = 'fa fa-rss-square';
		$item->label 	= FD::_('APP_USER_FEED_STREAM_TOOLTIP', true);

		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content	= parent::display( 'streams/' . $item->verb . '.content' );
	}
}
