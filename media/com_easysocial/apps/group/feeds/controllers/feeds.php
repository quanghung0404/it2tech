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


class FeedsControllerFeeds extends SocialAppsController
{
	/**
	 * Displays the creation form for rss feeds
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax 	= FD::ajax();

		// Fetch the output of the theme
		$theme = FD::themes();

		$output = $theme->output('apps/group/feeds/views/dialog.create');

		return $ajax->resolve($output);
	}

	/**
	 * Stores a new feed item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		// Get current logged in user
		$my = FD::user();

		// Get the group object
		$groupId = $this->input->get('groupId', 0, 'int');
		$group   = FD::group($groupId);

		// Ensure that this is the owner or the group admin
		if (!$group->isMember()) {
			return $ajax->reject(JText::_('APP_FEEDS_NOT_ALLOWED_TO_CREATE'));
		}

		// Get app's id.
		$id = $this->input->get('appId', '', 'int');

		// Get feed table
		$rss = FD::table('Rss');

		// Set the feed owner
		$rss->uid  = $group->id;
		$rss->type = SOCIAL_TYPE_GROUP;
		$rss->user_id	= $my->id;

		// Get the feed title
		$rss->title = $this->input->get('title', '', 'default');
		$rss->url   = $this->input->get('url', '', 'default');
		$rss->state = true;

		// Load up the feed parser
		$parser = @JFactory::getFeedParser($rss->url);

		if ($parser) {
			// $feed->description	= $rss->description;
			$rss->description 	= @$parser->get_description();
		}

		// Try to save the feed now
		$state 	= $rss->store();

		if (!$state) {
			return $ajax->reject($rss->getError());
		}

		// Get the application params
		$params	= $this->getParams();

		// Create new stream item when a new feed is created
		if ($params->get('stream_create', true)) {
			$rss->createStream('create');
		}

		// Initialize the parser.
		$parser = @JFactory::getFeedParser($rss->url);

		// If that is invalid feed url
		if ($parser === false) {
			return $ajax->resolve();
		}		

		$rss->parser = $parser;
		$rss->total = @$parser->get_item_quantity();
		$rss->items = @$parser->get_items();

		$theme 	= FD::themes();

		// @TODO
		$theme->set('totalDisplayed', 5);
		$theme->set('rss', $rss);
		$output = $theme->output('apps/group/feeds/views/default.item');

		return $ajax->resolve($output);
	}

	/**
	 * Confirms if the user wants to delete the feed item
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		// Get current logged in user
		$my = FD::user();

		// Get the group's object
		$groupId = $this->input->get('groupId', 0, 'int');
		$group   = FD::group($groupId);

		// Get the feed item
		$rssId   = $this->input->get('feedId', 0, 'int');

		$feed    = FD::table('Rss');
		$feed->load($rssId);

		// Ensure that this is the owner or the group admin
		if (!$group->isAdmin() && $feed->user_id != $my->id) {
			return $ajax->reject(JText::_('APP_FEEDS_NOT_ALLOWED_TO_DELETE'));
		}

		$theme 	= FD::themes();
		$theme->set('feed', $feed);
		$output = $theme->output('apps/group/feeds/views/dialog.delete');

		return $ajax->resolve($output);
	}

	/**
	 * Deletes a feed item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the ajax object.
		$ajax = FD::ajax();

		// Get current logged in user
		$my = FD::user();

		// Get the group's object
		$groupId = $this->input->get('groupId', 0, 'int');
		$group   = FD::group($groupId);

		// Ensure that this is the owner or the group admin
		if (!$group->isAdmin() && $feed->user_id != $my->id) {
			return $ajax->reject(JText::_('APP_FEEDS_NOT_ALLOWED_TO_DELETE'));
		}

		// Get the feed item
		$rssId   = $this->input->get('feedId', 0, 'int');

		$feed    = FD::table('Rss');
		$feed->load($rssId);

		if (!$rssId || !$feed->id) {
			return $ajax->reject(JText::_('APP_FEEDS_INVALID_ID_PROVIDED'));
		}

		// Try to delete the feed now.
		$state 	= $feed->delete();

		if (!$state) {
			return $ajax->reject( $feed->getError() );
		}

		return $ajax->resolve();
	}
}
