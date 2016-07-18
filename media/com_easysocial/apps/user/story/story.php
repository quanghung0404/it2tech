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

FD::import( 'admin:/includes/group/group' );

class SocialUserAppStory extends SocialAppItem
{
	/**
	 * event onLiked on story
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave( &$likes )
	{
		if( !$likes->type )
		{
			return;
		}

		// Set the default element.
		$element 	= $likes->type;
		$verb 		= '';
		$uid 		= $likes->uid;

		if( strpos( $element , '.' ) !== false )
		{
			$data		= explode( '.', $element );
			$group		= $data[1];
			$element	= $data[0];
			$verb		= isset( $data[2] ) ? $data[2] : '';
		}


		// When a user likes a comment
		if ($element == 'comments') {

			// For this, we only have 1 recipient since the comment itself was posted by a user
			$comment 		= FD::table('Comments');
			$comment->load($uid);

			// Get the actor that likes the comment
			$actor			= FD::user($likes->created_by);

			// Get the comment url
			$permalink = $comment->getPermalink();

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_STORY_EMAILS_LIKE_COMMENT_ITEM_SUBJECT',
	            'template'  	=> 'apps/user/story/like.comment.item',
	            'permalink' 	=> $permalink,
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'target'		=> FD::user($comment->created_by)->getName(),
	            'targetLink'	=> FD::user($comment->created_by)->getPermalink(false, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'url'           => $permalink,
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );


	        // Notify the owner of the comment first
	        if ($comment->created_by != $likes->created_by) {
	        	FD::notify('likes.item', array($comment->created_by), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, 'comments', 'user', 'like', array(), array($comment->created_by, $likes->created_by));

	        $emailOptions['title']      = 'APP_USER_STORY_EMAILS_LIKE_COMMENT_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/user/story/like.comment.involved';

	        // Notify other participating users
	        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

	        return;
		}

		// When a user likes a story
		if ($element == 'story') {

			// Since the uid is tied to the album we can get the album object
			$stream 	= FD::table('Stream');
			$stream->load($likes->uid);

			// Get the actor of the likes
			$actor		= FD::user($likes->created_by);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_STORY_EMAILS_LIKE_STATUS_ITEM_SUBJECT',
	            'template'  	=> 'apps/user/story/like.status.item',
	            'permalink' 	=> $stream->getPermalink(true, true),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'target'		=> FD::user($stream->actor_id)->getName(),
	            'targetLink'	=> FD::user($stream->actor_id)->getPermalink(false, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'url'           => $stream->getPermalink(false, false, false),
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );


	        // Notify the owner of the photo first
	        if ($likes->created_by != $stream->actor_id) {
	        	FD::notify('likes.item', array($stream->actor_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, $element, 'user', $verb, array(), array($stream->actor_id, $likes->created_by));

	        $emailOptions['title']      = 'APP_USER_STORY_EMAILS_LIKE_STATUS_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/user/story/like.status.involved';

	        // Notify other participating users
	        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

			return;
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
		$obj->color		= '#16a085';
		$obj->icon 		= 'fa fa-pencil';
		$obj->label 	= 'APP_USER_STORY_UPDATES_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Triggered before comments notify subscribers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave( &$comment )
	{
		$allowed 	= array('story.user.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// For likes on albums when user uploads multiple photos within an album
		if ($comment->element == 'story.user.create') {

			// Since the uid is tied to the album we can get the album object
			$stream 	= FD::table('Stream');
			$stream->load($comment->uid);

			// Get the actor of the likes
			$actor		= FD::user($comment->created_by);

			$owner = FD::user($stream->actor_id);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_STORY_EMAILS_COMMENT_STATUS_ITEM_SUBJECT',
	            'template'  	=> 'apps/user/story/comment.status.item',
	            'permalink' 	=> $stream->getPermalink(true, true),
				'comment'		=> $comment->comment,
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'target'		=> $owner->getName(),
	            'targetLink'	=> $owner->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $comment->element,
	            'context_ids'	=> $comment->id,
	            'url'           => $stream->getPermalink(false, false, false),
	            'actor_id'      => $comment->created_by,
	            'uid'           => $comment->uid,
	            'aggregate'     => true
	        );


	        // Notify the owner of the photo first
	        if ($stream->actor_id != $comment->created_by) {
	        	FD::notify('comments.item', array($stream->actor_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($comment->uid, 'story', 'user', 'create', array(), array($stream->actor_id, $comment->created_by));

	        $emailOptions['title']      = 'APP_USER_STORY_EMAILS_COMMENT_STATUS_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/user/story/comment.status.involved';

	        // Notify other participating users
	        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}
	}

	/**
	 * Renders the notification item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableNotification
	 * @return
	 */
	public function onNotificationLoad( &$item )
	{
		// @legacy
		// Fix older notifications
		if ($item->type == 'comments' && $item->cmd == 'comments.like') {

			$item->context_type = 'comments.user.like';

			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		$allowed 	= array('likes.item', 'likes.involved', 'comments.item', 'comments.involved', 'comments.like', 'story.tagged', 'stream.tagged', 'story.story', 'comments.tagged');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}


		// When someone likes on a status update
		$allowedContexts 	= array('story.user.create', 'story');
		if ($item->type == 'likes' && ($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') && in_array($item->context_type, $allowedContexts)) {

			// @legacy
			$item->context_type = 'story.user.create';


			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		// When someone comments on a status update
		$allowedContexts 	= array('story.user.create', 'story');
		if ($item->type == 'comments' && ($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') && in_array($item->context_type, $allowedContexts)) {

			// @legacy
			$item->context_type = 'story.user.create';

			$hook 	= $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

		// When someone likes a comment
		$allowedContexts 	= array('comments.user.like');
		if ($item->type == 'likes' && ($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') && in_array($item->context_type, $allowedContexts)) {

			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		// When a user is mentioned using @
		if ($item->cmd == 'story.tagged') {

			$hook 	= $this->getHook('notification', 'mentions');
			$hook->execute($item);

			return;
		}

		// When a user is tagged using the "with" method
		if ($item->cmd == 'stream.tagged') {

			$hook 	= $this->getHook('notification', 'tagged');
			$hook->execute($item);

			return;
		}

		// When user posts on another person's timeline
		if ($item->cmd == 'story.story' && $item->context_type == 'post.user.timeline') {

			$hook 	= $this->getHook('notification', 'story');
			$hook->execute($item);

			return;
		}

		if ($item->cmd == 'comments.tagged' && $item->context_type == 'comments.user.tagged') {

			$hook 	= $this->getHook('notification', 'tagged');
			$hook->execute($item);

			return;
		}


	}

	/**
	 * Prepares the activity log for user's actions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareActivityLog( SocialStreamItem &$stream, $includePrivacy = true )
	{
		if( $stream->context != 'story')
		{
			return;
		}

		// Stories wouldn't be aggregated
		$actor 		= $stream->actor;
		$target 	= count( $stream->targets ) > 0 ? $stream->targets[0] : '';

		$stream->display	= SOCIAL_STREAM_DISPLAY_MINI;

		// @triggers: onPrepareStoryContent
		// Processes any apps to process the content.
		FD::apps()->load( SOCIAL_TYPE_USER );

		$args 			= array( &$story , &$stream );
		$dispatcher 	= FD::dispatcher();

		$result 		= $dispatcher->trigger( SOCIAL_TYPE_USER , 'onPrepareStoryContent' , $args );

		$this->set( 'actor', $actor );
		$this->set( 'target', $target );
		$this->set( 'stream', $stream );
		$this->set( 'result', $result );


		$stream->title 		= parent::display( 'logs/title.' . $stream->verb );
		$stream->content	= parent::display( 'logs/content.' . $stream->verb );

		if( $includePrivacy )
		{
			$my         = FD::user();

			// only activiy log can use stream->uid directly bcos now the uid is holding id from social_stream_item.id;
			$stream->privacy = FD::privacy( $my->id )->form( $stream->uid, SOCIAL_TYPE_STORY, $stream->actor->id, 'story.view', false, $stream->aggregatedItems[0]->uid );
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
		if( $item->context_type != 'story')
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			if( !$privacy->validate( 'story.view', $item->id , SOCIAL_TYPE_STORY , $item->actor_id ) )
			{
				$item->cnt = 0;
			}
		}

		return true;
	}


	/**
	 * Triggered to prepare the stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$stream, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if ($stream->context != 'story') {
			return;
		}

		$uid = $stream->uid;
		$my = FD::user();
		$privacy = FD::privacy($my->id);

		if ($stream->cluster_id) {

			// Group access checking
			$cluster	= FD::cluster($stream->cluster_type, $stream->cluster_id);

			if (!$cluster) {
				return;
			}

			if (!$cluster->canViewItem()) {
				return;
			}

			// Allow editing of the stream item
			$stream->editable = $my->isSiteAdmin() || $cluster->isAdmin() || $stream->actor->id == $my->id;
		} else {
			// Allow editing of the stream item
			$stream->editable = $my->isSiteAdmin() || $stream->actor->id == $my->id;
		}

		// we stil need to check for the privacy because the context might come from the 'repost'
		if ($includePrivacy && !$privacy->validate( 'story.view', $uid , SOCIAL_TYPE_STORY, $stream->actor->id)) {
			return;
		}

		// Actor of this stream
		$actor = $stream->actor;
		$target = count($stream->targets) > 0 ? $stream->targets[0] : '';

		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;
		$stream->color = '#16a085';
		$stream->fonticon = 'fa fa-pencil';
		$stream->label = FD::_('APP_USER_STORY_UPDATES_STREAM_TOOLTIP', true);

		if ($stream->cluster_id) {

			if ($stream->cluster_type == SOCIAL_TYPE_GROUP) {
				$stream->color 		= '#303229';
				$stream->fonticon	= 'fa-users';
				$stream->label		= FD::_( 'APP_USER_STORY_GROUPS_STREAM_TOOLTIP', true);
			}

			if ($stream->cluster_type == SOCIAL_TYPE_EVENT) {
				$stream->color = '#f06050';
				$stream->fonticon = 'fa fa-calendar';
				$stream->label = FD::_('APP_USER_STORY_EVENTS_STREAM_TOOLTIP', true);
			}
		}

		$appGroup = SOCIAL_APPS_GROUP_USER;
		if ($stream->cluster_id) {
			if ($stream->cluster_type == SOCIAL_TYPE_EVENT) {
				$appGroup = SOCIAL_APPS_GROUP_EVENT;
			} else {
				$appGroup = SOCIAL_APPS_GROUP_GROUP;
			}
		}

		// Apply likes on the stream
		$likes = FD::likes();
		$likes->get($stream->uid , $stream->context, $stream->verb, $appGroup, $stream->uid);
		$stream->likes = $likes;

		// Apply comments on the stream
		$comments			= FD::comments($stream->uid, $stream->context, $stream->verb, $appGroup, array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $stream->uid ) ) ), $stream->uid);
		$stream->comments 	= $comments;

		// Apply repost on the stream
		$repost 		= FD::get( 'Repost', $stream->uid , SOCIAL_TYPE_STREAM, $appGroup );
		$stream->repost	= $repost;

		// If this is a group type, and the viewer is not a member of the group, we need to hide these data
		if ($stream->cluster_id ) {
			$cluster 	= FD::cluster($stream->cluster_type, $stream->cluster_id);

			if (!$cluster->isMember()) {
				$stream->commentLink	= false;
				$stream->repost 		= false;
				$stream->commentForm 	= false;
			}

			// Sharing only show in public group
			if (!$cluster->isOpen()) {
				$stream->sharing = false;
			}
		}

		// Get application params
		$params = $this->getParams();

		$this->set('params', $params);
		$this->set('actor', $actor);
		$this->set('target', $target);
		$this->set('stream', $stream);

		if ($stream->cluster_id) {
			$clusterReg = FD::registry($stream->params);
			$object = $clusterReg->get($stream->cluster_type);

			$cluster = FD::cluster($stream->cluster_type);

			if ($object) {
				// If have the object only bind
				$cluster->bind($object);

			} else {
				$cluster = $stream->getCluster();
			}

			$this->set('cluster', $cluster);
		}

		$titleFileName = ( $stream->cluster_type ) ? $stream->cluster_type . '.' . $stream->verb : $stream->verb;

		$stream->title = parent::display('streams/title.' . $titleFileName);
		$stream->content = parent::display('streams/content.' . $stream->verb);

		// Append the opengraph tags
		$stream->addOgDescription($stream->content);

		if ($includePrivacy) {
			$stream->privacy = $privacy->form($uid, SOCIAL_TYPE_STORY, $stream->actor->id, 'story.view', false, $stream->uid);
		}

		return true;
	}

}
