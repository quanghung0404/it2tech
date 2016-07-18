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

// Import parent view
FD::import('site:/views/views');


class EasySocialViewGroups extends EasySocialSiteView
{
	/**
	 * Renders the feed view of a group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		if (!$id || !$group->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'));
		}

		// Ensure that the group is published
		if (!$group->isPublished()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// Check for group permissions
		if ($group->isInviteOnly() && !$group->isMember() && !$group->isInvited() && !$this->my->isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// If the user is not the owner and the user has been blocked by the group creator
		if ($this->my->id != $group->creator_uid && $this->my->isBlockedBy($group->creator_uid)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// Set the page title
		$this->page->title($group->getName());

		// Get the stream library
		$stream = ES::stream();
		$options = array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'nosticky' => true);
		$stream->get($options);

		$items = $stream->data;

		if (!$items) {
			return;
		}

		foreach ($items as $item) {
			$feed = new JFeedItem();

			// Cleanse the title
			$feed->title = strip_tags($item->title);

			$content = $item->content . $item->preview;
			$feed->description = $content;

			// Permalink should only be generated for items with a full content
			$feed->link = $item->getPermalink(true);
			$feed->date = $item->created->toSql();
			$feed->category = $item->context;

			// author details
			$author = $item->getActor();
			$feed->author = $author->getName();
			$feed->authorEmail = $this->getRssEmail($author);

			$this->doc->addItem($feed);
		}
	}
}