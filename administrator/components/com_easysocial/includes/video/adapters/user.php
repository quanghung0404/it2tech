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

require_once(__DIR__ . '/abstract.php');

class SocialVideoAdapterUser extends SocialVideoAdapter
{
	protected $author = null;

	public function __construct($uid, $type, SocialTableVideo $table)
	{
		if ($table->user_id) {
			$this->author = ES::user($table->user_id);
		}

		parent::__construct($uid, $type, $table);
	}

	/**
	 * Retrieves the group alias
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAlias()
	{
		$author = ES::user($this->uid);

		return $author->getAlias();
	}

	/**
	 * Generates the mini header for the video layout
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getMiniHeader()
	{
		$layout = $this->input->get('layout');

		// If there is a uid on the current page, we need to display the mini header
		$uid = $this->input->get('uid', 0, 'int');

		// User is viewing their own all videos page
		if ($uid && $this->my->id == $uid) {
			return;
		}

		if ($uid && $this->my->id != $uid) {
			$user = ES::user($uid);

			return ES::template()->html('html.miniheader', $user);
		}

		if ($layout != 'item' && !$uid) {
			return;
		}

		// Do not display mini header when viewing own video
		if ($this->author && $this->author->id == $this->my->id) {
			return;
		}

		$user = $this->author;

		return ES::template()->html('html.miniheader', $user);
	}

	/**
	 * Determines if the current user can edit this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEditable()
	{
		// This is a new video
		if (!$this->table->id) {
			return true;
		}

		// Edited video
		if ($this->table->id && $this->table->user_id == $this->my->id) {
			return true;
		}

		// If user is a site admin they should be allowed to edit videos
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the provided user is allowed to view this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isViewable()
	{
		$privacy = FD::privacy(FD::user()->id);

        // privacy validation
        if (!$privacy->validate('videos.view', $this->table->id, SOCIAL_TYPE_VIDEOS, $this->table->uid)) {
            return false;
        }

        return true;
	}

	/**
	 * Determines if the user is allowed to create videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowCreation()
	{
		$access = $this->my->getAccess();

		if (!$access->allowed('videos.create')) {
			return false;
		}

		if ($this->hasExceededLimit()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the user exceeded the limit to upload videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasExceededLimit()
	{
		$access = $this->my->getAccess();

        if ($access->exceeded('videos.total', $this->my->getTotalVideos())) {
            return true;
        }

        return false;
	}

	/**
	 * Retrieves the upload size limit
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUploadLimit($withUnits = true)
	{
		$access = $this->my->getAccess();

		$limit = (int) $access->get('videos.maxsize');

		if ($withUnits) {
			$limit .= 'M';
		}

		return $limit;
	}

	/**
	 * Determines if the user can add a tag for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canAddTag()
	{
		// Super admin can always do anything they want
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Allow owner to add tags.
		if ($this->table->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can unfeature the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canUnfeature()
	{
		if ($this->table->isUnfeatured()) {
			return false;
		}

		// @TODO: Check for acl here

		if ($this->my->isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can feature the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canFeature()
	{
		// If this video is featured already, it should never be possible to feature the video again
		if ($this->table->isFeatured()) {
			return false;
		}

		// @TODO: Check for acl here

		if ($this->my->isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can delete the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canDelete()
	{
		// Allow users to delete their own video
		if ($this->my->id == $this->table->user_id) {
			return true;
		}

		// Allow site admin to delete the video
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		return false;
	}


	/**
	 * Determines if the user can edit the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canEdit()
	{
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// If video wasn't created yet, it needs to check against "can create".
		if ($this->table->user_id == $this->my->id) {
			return $this->allowCreation();
		}

		return false;
	}

	/**
	 * Determines if the current user is allowed to process the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canProcess()
	{
		$owner = ($this->table->user_id == $this->my->id) || $this->my->isSiteAdmin();

		return $owner;
	}

	/**
	 * Determines if the current user can access the videos section.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canAccessVideos()
	{
		if ($this->config->get('video.enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the page title for listing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getListingPageTitle()
	{
		$user = ES::user($this->uid);

		// Set the title to the user's videos page title
		$title = JText::sprintf('COM_EASYSOCIAL_VIDEOS_USER_PAGE_TITLE', $user->getName());

		return $title;
	}

	/**
	 * Retrieves the page title for listing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getFeaturedPageTitle()
	{
		$user = ES::user($this->uid);

		// Set the title to the user's videos page title
		$title = JText::sprintf('COM_EASYSOCIAL_VIDEOS_USER_FEATURED_PAGE_TITLE', $user->getName());

		return $title;
	}

	/**
	 * Retrieves the page title for listing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCategoryPageTitle(SocialTableVideoCategory $category)
	{
		$user = ES::user($this->uid);

		// Set the title to the user's videos page title
		$title = JText::sprintf('COM_EASYSOCIAL_VIDEOS_USER_CATEGORY_PAGE_TITLE', $user->getName(), $category->title);

		return $title;
	}
}
