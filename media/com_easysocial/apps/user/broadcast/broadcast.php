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

class SocialUserAppBroadcast extends SocialAppItem
{
	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj 			= new stdClass();
		$obj->color		= '#CF3510';
		$obj->icon 		= 'fa fa-bullhorn';
		$obj->label 	= 'APP_USER_FRIENDS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Processes as soon as the story is saved
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(SocialStream &$stream, SocialTableStreamItem &$streamItem, SocialStreamTemplate &$streamTemplate)
	{
		// Determines if this request is for broadcasting
		$broadcast = $this->input->get('broadcast_broadcast', false, 'bool');

		if (!$broadcast || $streamItem->context_type != 'broadcast') {
			return;
		}

		// Get the broadcast link
		$link = $this->input->get('broadcast_link', '', 'default');

		// Get the broadcast title
		$title = $this->input->get('broadcast_title', '', 'string');

		// Determine which target profile id
		$profileId = $this->input->get('broadcast_profileId', 0, 'int');

		// Determine which type this broadcast is
		$type = $this->input->get('broadcast_type', 'notification', 'string');

		// Get the content
		$content = $streamTemplate->content;

		// For broadcasted items, we want to insert a new notification for everyone on the site
		$model = FD::model('Broadcast');
		
		// To check if user select via notification, save to notification table instead.				
		if ($type == 'popup') {
			$id = $model->broadcast($profileId, nl2br($content), $this->my->id, $title, $link);
		} else {
			$id = $model->notifyBroadcast($profileId, $title, $content, $link, $this->my->id, $streamItem);
		}
		
		$streamItem->context_id = $id;

		// Save the stream object
		$streamItem->store();
	}

	/**
	 * When a broadcast is made, it should also appear on the stream
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream(SocialStreamItem &$stream)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($stream->context != 'broadcast') {
			return;
		}

		// Load up the broadcast object
		$contextId = isset($stream->contextIds[0]) ? $stream->contextIds[0] : false;

		if (!$contextId) {
			return;
		}

		// Load the broadcast item
		$broadcast = FD::table('Broadcast');
		$broadcast->load((int) $contextId);

		// The stream is not editable
		$stream->editable = false;

		// Get the stream actor
		$actor = $stream->actor;

		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;
		$stream->color = '#CF3510';
		$stream->fonticon = 'fa fa-bullhorn';
		$stream->label = JText::_('COM_EASYSOCIAL_STREAM_APP_FILTER_BROADCAST');

		// There will not be any likes for this
		$stream->likes = false;
		$stream->comments = false;

		$this->set('broadcast', $broadcast);
		$this->set('actor', $actor);

		$stream->title = parent::display('streams/title.create');
		$stream->content = parent::display('streams/content.create');

		return true;
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
		// Broadcast disabled
		if (!$this->config->get('notifications.broadcast.popup')) {
			return;
		}
		
		// Broadcast tool only works for site admin
		if (!$this->my->isSiteAdmin()) {
			return;
		}

		// Get app properties
		$params = $this->getParams();

		// Create plugin object
		$plugin	= $story->createPlugin('broadcast', 'panel');

		// Get a list of profiles on the site
		$model    = FD::model('Profiles');
		$profiles = $model->getProfiles();

		// We need to attach the button to the story panel
		$theme  = FD::themes();
		$theme->set('profiles', $profiles);

		$plugin->button->html 	= $theme->output('themes:/apps/user/broadcast/story/panel.button');
		$plugin->content->html 	= $theme->output('themes:/apps/user/broadcast/story/panel.content');

		// Attachment script
		$script	= FD::script();
		$plugin->script	= $script->output('apps:/user/broadcast/story');

		return $plugin;
	}
}
