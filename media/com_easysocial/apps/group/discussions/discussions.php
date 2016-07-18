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
 * Discussions app for EasySocial Group
 *
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppDiscussions extends SocialAppItem
{
	public function __construct()
	{
		parent::__construct();
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

		$obj->color 	= '#69b598';
		$obj->icon		= 'fa-comments';
		$obj->label 	= 'APP_USER_GROUP_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Performs clean up when a group is deleted
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup		The group object
	 */
	public function onBeforeDelete( &$group )
	{
		// Delete all discussions from a group
		$model 	= FD::model( 'Discussions' );
		$model->delete( $group->id , SOCIAL_TYPE_GROUP );
	}

	/**
	 * Determines if the app should appear on the sidebar
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function appListing($view, $id, $type)
	{
		if ($type != SOCIAL_TYPE_GROUP) {
			return true;
		}

		// We should not display the discussions on the app if it's disabled
		$group = FD::group($id);
		$registry = $group->getParams();

		if (!$registry->get('discussions', true)) {
			return false;
		}

		return true;
	}

	/**
	 * Processes likes notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed 	= array('discussions.group.create');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		if ($likes->type == 'discussions.group.create') {

			$stream 		= FD::table('Stream');
			$stream->load($likes->uid);

			$streamItems 	= $stream->getItems();
			$streamItem 	= $streamItems[0];

	        // Get the actor
	        $actor      = FD::user($likes->created_by);

	        // Get the discussion object since it's tied to the stream
	        $discussion 	= FD::table('Discussion');
	        $discussion->load($streamItem->context_id);

	        $emailOptions   = array(
	            'title'     	=> 'APP_GROUP_DISCUSSIONS_EMAILS_LIKE_ITEM_SUBJECT',
	            'template'  	=> 'apps/group/discussions/like.discussion.item',
	            'permalink' 	=> $discussion->getPermalink(true, true),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'url'           => $discussion->getPermalink(false, false, false),
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );

	        // Notify the owner first
	        if ($likes->created_by != $discussion->created_by) {
	        	FD::notify('likes.item', array($discussion->created_by), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, 'discussions', 'group', 'create', array(), array($discussion->created_by, $likes->created_by));

	        $emailOptions['title']      = 'APP_GROUP_DISCUSSIONS_EMAILS_LIKE_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/group/discussions/like.discussion.involved';

	        // Notify other participating users
	        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
		}

	}

	/**
	 * Prepare notification items for discussions
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed 	= array('group.discussion.create', 'group.discussion.reply', 'likes.item');

		if (!in_array( $item->cmd , $allowed)) {
			return;
		}

		// Get the group information
		$group 	= FD::group($item->uid);
		$actor 	= FD::user($item->actor_id);

		if ($item->cmd == 'likes.item' && $item->context_type == 'discussions.group.create') {

			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		if ($item->cmd == 'group.discussion.create') {

			$discussion 	= FD::table('Discussion');
			$discussion->load($item->context_ids);

			$item->title 	= JText::sprintf('APP_GROUP_DISCUSSIONS_NOTIFICATIONS_CREATED_DISCUSSION', $actor->getName(), $group->getName());
			$item->content 	= $discussion->title;

			return $item;
		}

		if ($item->cmd == 'group.discussion.reply') {

			$item->title 	= JText::sprintf('APP_GROUP_DISCUSSIONS_NOTIFICATIONS_REPLED_DISCUSSION', $actor->getName(), $group->getName());

			return $item;
		}
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
		if( $item->context_type != 'discussions' )
		{
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' ) );

		if (!$group) {
			return;
		}

		$item->cnt = 1;

		if (!$group->isOpen() && !$group->isMember()) {
			$item->cnt = 0;
		}

		return true;
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
		if ($item->context != 'discussions') {
			return;
		}

		// group access checking
		$group	= FD::group($item->cluster_id);

		if (!$group) {
			return;
		}

		if (!$group->canViewItem()) {
			return;
		}

		// Ensure that announcements are enabled for this group
		$registry = $group->getParams();

		if (!$registry->get('discussions', true)) {
			return;
		}

		// Define standard stream looks
		$item->display 	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#69b598';
		$item->fonticon	= 'fa-comments';
		$item->label	= FD::_('COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_DISCUSSIONS_TOOLTIP', true);

		$params 	= $this->getApp()->getParams();

		if ($params->get( 'stream_' . $item->verb , true ) == false) {
			return;
		}

		// Do not allow user to repost discussions
		$item->repost 	= false;

		// Process likes and comments differently.
		$likes 			= FD::likes();
		$likes->get($item->contextId , $item->context, $item->verb, SOCIAL_APPS_GROUP_GROUP, $item->uid);
		$item->likes	= $likes;

		// Apply comments on the stream
		$comments		= FD::comments($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_GROUP , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $item->uid ) ) ), $item->uid );
		$item->comments 	= $comments;

		if ($item->verb == 'create') {
			$this->prepareCreateDiscussionStream($item);
		}

		if ($item->verb == 'reply') {
			$this->prepareReplyStream($item);
		}

		if ($item->verb == 'answered') {
			$this->prepareAnsweredStream($item);
		}

		if ($item->verb == 'lock') {
			$this->prepareLockedStream($item);
		}
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareCreateDiscussionStream( &$item )
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion	= FD::table( 'Discussion' );
		$discussion->load($item->contextId);

		// Determines if there are files associated with the discussion
		$files 		= $discussion->hasFiles();
		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $this->getApp()->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $discussion );

		$this->set( 'files'		, $files );
		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/create.title' );
		$item->content 	= parent::display( 'streams/create.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareReplyStream( &$item )
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );
		$discussion = FD::table( 'Discussion' );
		$discussion->load($item->contextId);

		$reply	= FD::table( 'Discussion' );
		$reply->bind( $params->get( 'reply' ) );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $this->getApp()->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $reply );

		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'reply'		, $reply );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/reply.title' );
		$item->content 	= parent::display( 'streams/reply.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareAnsweredStream( &$item )
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion = FD::table( 'Discussion' );
		$discussion->bind( $params->get( 'discussion' ) );

		$reply	= FD::table( 'Discussion' );
		$reply->bind( $params->get( 'reply' ) );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $this->getApp()->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $reply );

		// Get the reply author
		$reply->author	= FD::user( $reply->created_by );

		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'reply'		, $reply );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/answered.title' );
		$item->content 	= parent::display( 'streams/answered.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareLockedStream( &$item )
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion = FD::table( 'Discussion' );
		$discussion->bind( $params->get( 'discussion' ) );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $this->getApp()->getAlias() , 'discussionId' => $discussion->id ) , false );

		$item->display 	= SOCIAL_STREAM_DISPLAY_MINI;

		$this->set( 'permalink'	, $permalink );
		$this->set( 'actor'		, $item->actor );
		$this->set( 'discussion', $discussion );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/locked.title' );
	}

	public function formatContent($discussion)
	{
		// Reduce length based on the settings
		$params 	= $this->getParams();
		$max 		= $params->get('stream_length', 250);
		$content 	= $discussion->content;

		// Remove code blocks
		$content 	= FD::string()->parseBBCode( $content , array( 'code' => true , 'escape' => false ) );

		// Remove [file] from contents
		$content 	= $discussion->removeFiles( $content );



        if ($max) {

            // lets do a simple content truncation here.
            $content = strip_tags($content);
            $content = strlen($content) > $max ? JString::substr($content, 0, $max ) . JText::_('COM_EASYSOCIAL_ELLIPSES') : $content ;
        }

		return $content;
	}
}
