<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

require_once(__DIR__ . '/helper.php');

class SocialUserAppLinks extends SocialAppItem
{
	/**
	 * event onLiked on shared link
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave( &$likes )
	{
		$allowed = array('links.user.create');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// Get the stream object
		$stream = FD::table('Stream');
		$stream->load($likes->uid);

		// Get the likes actor
		$actor = FD::user($likes->created_by);

		$owner = FD::user($stream->actor_id);

        // Set the email options
        $emailOptions   = array(
            'title' => 'APP_USER_LINKS_EMAILS_LIKE_STATUS_ITEM_SUBJECT',
            'template' => 'apps/user/links/like.link.item',
            'permalink' => $stream->getPermalink(true, true),
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true),
            'target' => $owner->getName(),
            'targetLink' => $owner->getPermalink(true, true)
        );

        $systemOptions  = array(
            'context_type' => $likes->type,
            'url' => $stream->getPermalink(false, false, false),
            'actor_id' => $likes->created_by,
            'uid' => $likes->uid,
            'aggregate' => true
        );

        // Notify the owner of the photo first
        if ($likes->created_by != $stream->actor_id) {
        	FD::notify('likes.item', array($stream->actor_id), $emailOptions, $systemOptions);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($likes->uid, 'links', 'user', 'create', array(), array($stream->actor_id, $likes->created_by));

        $emailOptions['title'] = 'APP_USER_LINKS_EMAILS_LIKE_STATUS_INVOLVED_SUBJECT';
        $emailOptions['template'] = 'apps/user/links/like.link.involved';

        // Notify other participating users
        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
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
		$allowed 	= array('links.user.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// Since the uid is tied to the album we can get the album object
		$stream 	= FD::table('Stream');
		$stream->load($comment->uid);

		// Get the actor of the likes
		$actor		= FD::user($comment->created_by);

        // Set the email options
        $emailOptions   = array(
            'title'     	=> 'APP_USER_LINKS_EMAILS_COMMENT_STATUS_ITEM_SUBJECT',
            'template'  	=> 'apps/user/links/comment.link.item',
            'permalink' 	=> $stream->getPermalink(true, true),
			'comment'		=> $comment->comment,
            'actor'     	=> $actor->getName(),
            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink'     => $actor->getPermalink(true, true),
            'target'		=> FD::user($stream->actor_id)->getName(),
            'targetLink'	=> FD::user($stream->actor_id)->getPermalink(false, true)
        );

        $systemOptions  = array(
            'context_type'  => $comment->element,
            'context_ids'	=> $comment->id,
            'url'           => $stream->getPermalink(false, false, false),
            'actor_id'      => $comment->created_by,
            'uid'           => $comment->uid,
            'content'		=> $comment->comment,
            'aggregate'     => true
        );


        // Notify the owner of the photo first
        if ($stream->actor_id != $comment->created_by) {
        	FD::notify('comments.item', array($stream->actor_id), $emailOptions, $systemOptions);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients     = $this->getStreamNotificationTargets($comment->uid, 'links', 'user', 'create', array(), array($stream->actor_id, $comment->created_by));

        $emailOptions['title']      = 'APP_USER_LINKS_EMAILS_COMMENT_STATUS_INVOLVED_SUBJECT';
        $emailOptions['template']   = 'apps/user/links/comment.link.involved';

        // Notify other participating users
        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
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

		// Process notifications when someone posts a likes on a link
		// context_type: links.user.create
		// type: comments
		if ($item->type == 'likes' && $item->context_type == 'links.user.create') {

			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		// Process notifications when someone posts a comment on your status update
		// context_type: links.user.create
		// type: comments
		if ($item->type == 'comments' && $item->context_type == 'links.user.create') {

			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

	}

	/**
	 * Processes a saved story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(&$stream, &$streamItem, &$template)
	{
		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_links', true)) {
			return;
		}

		// Get the link information from the request
		$link = $this->input->get('links_url', '', 'default');
		$title = $this->input->get('links_title', '', 'default');
		$content = $this->input->get('links_description', '', 'default');
		$image = $this->input->get('links_image', '', 'default');
		$video = $this->input->get('links_video', '', 'default');

		// If there's no data, we don't need to store in the assets table.
		if (empty($title) && empty($content) && empty($image)) {
			return;
		}

		$registry = FD::registry();
		$registry->set('title', $title);
		$registry->set('content', $content);
		$registry->set('image', $image );
		$registry->set('link', $link );

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
		if( $item->context_type != 'links' ) {
			return false;
		}

		$item->cnt = 1;

		if ($includePrivacy) {

			$my = FD::user();
			$privacy = FD::privacy( $my->id );

			if (!$privacy->validate( 'story.view', $item->id, SOCIAL_TYPE_LINKS, $item->actor_id)) {
				$item->cnt = 0;
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
		$obj = new stdClass();
		$obj->color = '#5580BE';
		$obj->icon = 'fa-link';
		$obj->label = 'APP_USER_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Generates the stream title of group.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
	{
		if ($stream->context != 'links') {
			return;
		}

		$streamGroup = SOCIAL_APPS_GROUP_USER;

		// group access checking
		if ($stream->cluster_id) {

			$streamGroup = $stream->cluster_type;

			if ($stream->cluster_type == SOCIAL_APPS_GROUP_GROUP) {
				$cluster = FD::group($stream->cluster_id);
			}

			if ($stream->cluster_type == SOCIAL_APPS_GROUP_EVENT) {
				$cluster = FD::event($stream->cluster_id);
			}

			if (!$cluster) {
				return;
			}

			if (!$cluster->canViewItem()) {
				return;
			}

			// Do not show social share button in private/invite group
			if (!$cluster->isOpen()) {
				$stream->sharing = false;
			}			
		}

		//get links object, in this case, is the stream_item
		$uid = $stream->uid;

		$stream->color = '#5580BE';
		$stream->fonticon = 'fa fa-link';
		$stream->label = FD::_( 'APP_USER_LINKS_STREAM_TOOLTIP', true );

		// Apply likes on the stream
		$stream->setLikes($streamGroup, $stream->uid);

		// Apply comments on the stream
		$permalink = FRoute::stream(array('layout' => 'item', 'id' => $stream->uid));
		$stream->setComments($streamGroup, $stream->uid, array('url' => $permalink));

		// Apply repost on the stream
		$stream->setRepost($streamGroup, SOCIAL_TYPE_STREAM);

		$my = FD::user();
		$privacy = FD::privacy($my->id);

		if ($includePrivacy && !$privacy->validate( 'story.view', $uid , SOCIAL_TYPE_LINKS , $stream->actor->id)) {
			return;
		}

		// Get the person that created this stream item
		$actor = $stream->getActor();

		// Get the targets if this stream was posted on another target
		$target = $stream->getTargets();

		// Get the assets associated with this stream
		$assets = $stream->getAssets();

		if (empty($assets)) {
			return;
		}

		// Get app params
		$params = $this->getParams();

		$assets = $assets[0];

		// Retrieve the link that is stored.
		$hash = md5($assets->get('link'));

		// Load the link object
		$link = FD::table('Link');
		$link->load(array('hash' => $hash));

		// Get the link data
		$linkObj = json_decode($link->data);

		// Determine if there's any embedded object
		$oembed = isset($linkObj->oembed) ? $linkObj->oembed : '';

		// Determine if this oembed obj is an article or not
		if ($oembed) {
			$oembed->isArticle = isset($oembed->isArticle) ? $oembed->isArticle : false;
		}

		$uri = JURI::getInstance();

		// Get the image file
		$image = $assets->get('image');
		$cachePath = ltrim($this->config->get('links.cache.location'), '/');

		// @since 1.3.8
		// This block of code should be removed later.
		// FIX Older images where 'cached' state is not stored.
		// Check if the image string contains the cached storage path
		if (!$assets->get('cached') && (stristr($image, '/media/com_easysocial/cache') !== false)) {
			$assets->set('cached', true);
		}

		// Dirty way of checking 
		// If the image is cached, we need to get the correct path
		if ($assets->get('cached')) {

			// First we try to load the image from the image link table
			$linkImage = FD::table('LinkImage');
			$exists = $linkImage->load(array('internal_url' => $image));

			if ($exists) {
				$image = $linkImage->getUrl();
			} else {
				$fileName = basename($image);
				$image = rtrim(JURI::root(), '/') . '/' . $cachePath . '/' . $fileName;
			}
		}

		// If necessary, feed in our own proxy to avoid http over https issues.
		if ($params->get('stream_link_proxy', false) && ($oembed || $assets->get('image')) && $uri->getScheme() == 'https') {

			// Check if there are any http links
			if (isset($oembed->thumbnail) && $oembed->thumbnail && stristr($oembed->thumbnail, 'http://') !== false) {
				$oembed->thumbnail = FD::proxy($oembed->thumbnail);
			}

			if ($image && stristr($image, 'http://') !== false) {
				$image = FD::proxy($image);
			}
		}

		// Fix video issues with youtube when site is on https
		if (isset($oembed->provider_url) && $oembed->provider_url == 'http://www.youtube.com/') {
			$oembed->html = JString::str_ireplace('http://', 'https://', $oembed->html);
			$oembed->thumbnail = str_ireplace('http://', 'https://', $oembed->thumbnail);
		}

		// Get the contents and truncate accordingly
		$content = $assets->get('content', '');	

		if ($params->get('stream_link_truncate')) {
			$content = JString::substr(strip_tags($content), 0, $params->get('stream_link_truncate_length', 250)) . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		$this->set('image', $image);
		$this->set('content', $content);
		$this->set('params', $params);
		$this->set('oembed', $oembed);
		$this->set('assets', $assets);
		$this->set('actor', $actor);
		$this->set('target', $target);
		$this->set('stream', $stream);

		if ($stream->cluster_id) {

			if ($stream->cluster_type == SOCIAL_APPS_GROUP_GROUP) {
				$stream->label = FD::_('APP_USER_LINKS_GROUPS_STREAM_TOOLTIP', true);
				$stream->color = '#303229';
				$stream->fonticon = 'fa fa-users';
			}

			if ($stream->cluster_type == SOCIAL_APPS_GROUP_EVENT) {
				$stream->color = '#f06050';
				$stream->fonticon = 'fa fa-calendar';
				$stream->label = FD::_('APP_USER_EVENTS_STREAM_TOOLTIP', true);
			}

			$this->set('cluster', $cluster);
			$stream->title = parent::display('streams/title.' . $stream->verb . '.' . $stream->cluster_type);
		} else {
			$stream->title = parent::display('streams/title.' . $stream->verb);
		}
		
		// Set the stream display mode
		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;

		// Set the preview
		$stream->preview = parent::display('streams/preview.' . $stream->verb);

		// Append the opengraph tags
        if ($image) {
        	$stream->addOgImage($image);
        }

		if ($content) {
			$stream->addOgDescription($content);
		} else {

			// If the content is empty, try to get the stream content
			if ($stream->content) {
				$stream->addOgDescription($stream->content);
			} else {
				$stream->addOgDescription($stream->title);
			}
		}

		// Include privacy checking or not
		if ($includePrivacy) {
			$stream->privacy = $privacy->form($uid, SOCIAL_TYPE_LINKS, $stream->actor->id, 'story.view', false, $stream->uid);
		}

		return true;
	}


	/**
	 * Responsible to generate the activity logs.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		if ($item->context != 'links') {
			return;
		}

		//get story object, in this case, is the stream_item
		$tbl = FD::table( 'StreamItem' );
		$tbl->load( $item->uid ); // item->uid is now streamitem.id

		$uid = $tbl->uid;

		// Get story object, in this case, is the stream_item
		$my = FD::user();
		$privacy = FD::privacy($my->id);

		$actor = $item->actor;
		$target = count($item->targets) > 0 ? $item->targets[0] : '';

		$assets = $item->getAssets($uid);

		if (empty($assets)) {
			return;
		}

		$assets = $assets[0];

		$this->set('actor', $actor);
		$this->set('assets', $assets);
		$this->set('target', $target);
		$this->set('stream', $item);

		$item->display = SOCIAL_STREAM_DISPLAY_MINI;
		$item->title = parent::display('logs/' . $item->verb);

		if ($includePrivacy) {
			$item->privacy = $privacy->form($uid, SOCIAL_TYPE_LINKS, $item->actor->id, 'story.view', false, $item->aggregatedItems[0]->uid);
		}

		return true;

	}

	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel($story)
	{
		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_links', true)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('links', 'panel');

		// We need to attach the button to the story panel
		$theme = ES::themes();

        $button = $theme->output('site/links/story/button');
        $form = $theme->output('site/links/story/form');

		// Attach the scripts
		$script = ES::script();
		$scriptFile = $script->output('site/links/story/plugin');

		$plugin->setHtml($button, $form);
		$plugin->setScript($scriptFile);

		return $plugin;
	}
}
