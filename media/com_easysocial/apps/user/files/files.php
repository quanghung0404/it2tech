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

FD::import('admin:/includes/apps/apps');
FD::import('admin:/includes/group/group');

class SocialUserAppFiles extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $options = array() )
	{
		parent::__construct($options);
	}


	/**
	 * Triggered before comments notify subscribers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed = array('files.user.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// For likes on albums when user uploads multiple photos within an album
		if ($comment->element == 'files.user.create') {

			// Since the uid is tied to the album we can get the album object
			$stream = FD::table('Stream');
			$stream->load($comment->uid);

			// Get the actor of the likes
			$actor = FD::user($comment->created_by);

			$owner = FD::user($stream->actor_id);

	        // Set the email options
	        $emailOptions   = array(
	            'title' => 'APP_USER_FILES_EMAILS_COMMENT_STREAM_SUBJECT',
	            'template' => 'apps/user/files/comment.status.item',
	            'permalink' => $stream->getPermalink(true, true),
				'comment' => $comment->comment,
	            'actor' => $actor->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true),
	            'target' => $owner->getName(),
	            'targetLink' => $owner->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type' => $comment->element,
	            'context_ids' => $comment->id,
	            'url' => $stream->getPermalink(false, false, false),
	            'actor_id' => $comment->created_by,
	            'uid' => $comment->uid,
	            'aggregate' => true
	        );

	        // Notify the owner of the photo first
	        if ($stream->actor_id != $comment->created_by) {
	        	FD::notify('comments.item', array($stream->actor_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($comment->uid, 'files', 'user', 'create', array(), array($stream->actor_id, $comment->created_by));

	        $emailOptions['title']  = 'APP_USER_FILES_EMAILS_COMMENT_STREAM_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/user/files/comment.status.involved';

	        // Notify other participating users
	        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}
	}

	/**
	 * We do not want to display this in the activity log
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasActivityLog()
	{
		return false;
	}

	/**
	 * Processes after the story is saved so that we can generate a stream item for this
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(SocialStream &$stream , SocialTableStreamItem $streamItem , &$template)
	{
		$files = $this->input->get('files', array(), 'array');

		if (!$files) {
			return;
		}

		// Add points for the user when they upload a file.
		FD::points()->assign('files.upload', 'com_easysocial', $this->my->id);

		// We need to set the context id's for the files shared in this stream.
		$params = FD::registry();
		$params->set('files', $files);

		$streamItem->params = $params->toString();
		$streamItem->store();
	}

	/**
	 * Renders the notification item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableNotification
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('comments.item', 'comments.involved');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}


		// When someone likes on a status update
		$allowedContexts = array('files.user.create');

		if ($item->type == 'comments' && ($item->cmd == 'comments.involved' || $item->cmd == 'comments.item') && in_array($item->context_type, $allowedContexts)) {
			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);
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
		$obj->color		= '#00B6AD';
		$obj->icon 		= 'fa fa-file';
		$obj->label 	= 'APP_USER_FILES_STREAM_TOOLTIP';

		return $obj;
	}

	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != SOCIAL_TYPE_FILES) {
			return false;
		}

		// If this is a cluster stream, let check if user can view this stream or not.
		if ($item->cluster_id && $item->cluster_type) {
			$params = FD::registry($item->params);
			$group = FD::group($params->get('group'));

			if (!$group) {
				return;
			}

			$item->cnt = 1;

			if ($group->type != SOCIAL_GROUPS_PUBLIC_TYPE && !$group->isMember()) {
				$item->cnt = 0;
			}
		} else {
			// There is no need to validate against privacy for this item.
			$item->cnt = 1;
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
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true )
	{
		if ($item->context != SOCIAL_TYPE_FILES) {
			return;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		if ($item->cluster_id && $item->cluster_type) {

			$cluster = FD::cluster($item->cluster_type, $item->cluster_id);

			if (!$cluster) {
				return;
			}

			if (!$cluster->canViewItem()) {
				return;
			}

			// Do not show social share button in private/invite group
			if (!$cluster->isOpen()) {
				$item->sharing = false;
			}			

			$item->display = SOCIAL_STREAM_DISPLAY_FULL;

			if ($item->cluster_type == SOCIAL_TYPE_GROUP) {
				$item->color = '#303229';
				$item->fonticon	= 'fa fa-users';
				$item->label = FD::_('APP_USER_FILES_GROUPS_STREAM_TOOLTIP', true);
			}

			if ($item->cluster_type == SOCIAL_TYPE_EVENT) {
				$item->color = '#f06050';
				$item->fonticon = 'fa fa-calendar';
				$item->label = FD::_('APP_USER_EVENTS_STREAM_TOOLTIP', true);
			}

			if ($item->verb == 'uploaded') {
				$this->prepareUploadedStream($item);
			}

			return;
		}

		// File uploads by user
		if (isset($item->contextParams[0])) {

			$params = FD::registry($item->contextParams[0]);
			$items = $params->get('files');
			$total = count($items);
			$files = array();

			if (!$items) {
				return;
			}

			foreach ($items as $id) {
				$file = FD::table('File');
				$file->load($id);

				$files[] = $file;
			}

			$plurality = $total > 1 ? '_PLURAL' : '_SINGULAR';

			$targets = $item->targets ? $item->targets[0] : false;

			$this->set('target', $targets);
			$this->set('content', $item->content);
			$this->set('plurality', $plurality);
			$this->set('total', $total);
			$this->set('files', $files);
			$this->set('actor', $item->actor);

			$item->display = SOCIAL_STREAM_DISPLAY_FULL;
			$item->color = '#00B6AD';
			$item->fonticon	= 'fa fa-file';
			$item->label = FD::_('APP_USER_FILES_STREAM_TOOLTIP', true);

			// Apply likes on the stream
			$likes = FD::likes();
			$likes->get($item->uid , $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid);
			$item->likes = $likes;

			// Apply comments on the stream
			$comments = FD::comments($item->uid, $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, array('url' => FRoute::stream(array('layout' => 'item', 'id' => $item->uid))), $item->uid);
			$item->comments = $comments;

			$item->title = parent::display('streams/uploaded.title.user');
			$item->content = parent::display('streams/uploaded.content.user');
		}
	}

	/**
	 * Prepares the stream item for new file uploads
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareUploadedStream(&$item)
	{
		$params = FD::registry($item->params);

		// Default items
		$files = array();

		// Load the file from params
		$obj = $params->get('file');

		// Default content
		$content = '';

		if (is_object($obj)) {
	
			// Get the file object
			$file = FD::table('File');

			$exists = $file->load($obj->id);

			if (!$exists) {
				return;
			}


			$files[] = $file;

		} else {
			
			// This is not an object and probably it's an array?
			$params = FD::registry($item->contextParams[0]);
			$fileItems = $params->get('file');
			
			foreach ($fileItems as $fileId) {
				$file = FD::table('File');
				$file->load((int) $fileId);

				$files[] = $file;
			}

			$content = $item->content;
		}

		// Get the actor
		$actor = $item->actor;

		$this->set('content', $content);
		$this->set('actor', $actor);
		$this->set('files', $files);

		$clusterType = '';

		if ($item->cluster_id && $item->cluster_type) {

			$cluster = FD::cluster($item->cluster_type, $item->cluster_id);
			$this->set('cluster', $cluster);

			$clusterType = '.' . $item->cluster_type;
		}

		// Load up the contents now.
		$item->title = parent::display('streams/uploaded.title' . $clusterType);
		$item->content = parent::display('streams/uploaded.content');
	}


	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel($story)
	{
		$params = $this->getParams();
		$access = $this->my->getAccess();

		// Determine if the user can use this feature
		if (!$access->get('files.upload') || !$params->get('enable_uploads', true)) {
			return;
		}

		// Create plugin object
		$plugin	= $story->createPlugin('files', 'panel');

		// Get the allowed extensions
		$allowedExtensions = $params->get('allowed_extensions', 'zip,txt,pdf,gz,php,doc,docx,ppt,xls');
		$maxFileSize = $params->get('max_upload_size', 8) . 'M';

		// We need to attach the button to the story panel
		$theme  = FD::themes();

		$plugin->button->html 	= $theme->output('themes:/apps/user/files/story/panel.button');
		$plugin->content->html 	= $theme->output('themes:/apps/user/files/story/panel.content');

		// Attachment script
		$script	= FD::script();
		$script->set('allowedExtensions', $allowedExtensions);
		$script->set('maxFileSize', $maxFileSize);
		$script->set('type', SOCIAL_TYPE_USER);
		$script->set('uid', $this->my->id);

		$plugin->script	= $script->output('apps:/user/files/story');

		return $plugin;
	}
}
