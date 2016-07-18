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

require_once( dirname( __FILE__ ) . '/abstract.php' );

/**
 * Event adapter for albums
 *
 * @since	1.3
 * @access	public
 *
 */
class SocialAlbumsAdapterEvent extends SocialAlbumsAdapter
{
	private $event = null;

	public function __construct(SocialAlbums $lib)
	{
		$this->event = FD::event($lib->uid);

		parent::__construct($lib);
	}

	/**
	 * Displays the albums heading for an event
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function heading()
	{
		$theme = FD::themes();
		$theme->set('event', $this->event);

		$output = $theme->output('site/albums/header.event');

		return $output;
	}

	public function isValidNode()
	{
		if (!$this->event || !$this->event->id) {
			$this->lib->setError(JText::_('COM_EASYSOCIAL_ALBUMS_EVENT_INVALID_EVENT_ID_PROVIDED'));
			return false;
		}

		return true;
	}

	/**
	 * Get the album link for this event album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getViewAlbumsLink($xhtml = true)
	{
		$url = FRoute::albums(array('uid' => $this->event->getAlias(), 'type' => SOCIAL_TYPE_EVENT), $xhtml);

		return $url;
	}

	/**
	 * Retrieves the page title
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPageTitle($layout, $prefix = true)
	{
		if ($layout == 'item') {
			$title = $this->lib->data->get('title');
		}

		if ($layout == 'form') {
			$title 	= JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_ALBUM');
		}

		if ($layout == 'default') {
			$title	= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALBUMS' );
		}

		if ($prefix) {
			$title 	= $this->event->getName() . ' - ' . $title;
		}

		return $title;
	}

	/**
	 * Determines if the current viewer can view the album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function viewable()
	{
		// Site admin should always be able to view
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Event admin is always allowed to view
		if ($this->event->isAdmin()) {
			return true;
		}

		// If the event is public, it should be viewable
		if ($this->event->isOpen()) {
			return true;
		}

		// Event members should be allowed
		if ($this->event->isMember()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can delete the album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteable()
	{
		// If this is a core album, it should never be allowed to delete
		if ($this->album->isCore()) {
			return false;
		}

		// Super admins are allowed to edit
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Event admin's are always allowed
		if ($this->event->isAdmin()) {
			return true;
		}

		// Owner of the albums are allowed to edit
		if ($this->album->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can edit the album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editable()
	{
		// Perhaps the person is creating a new album
		if (!$this->album->id) {
			return true;
		}

		// Super admins are allowed to edit
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Group admin's are always allowed
		if ($this->event->isAdmin()) {
			return true;
		}

		// If user is a member, allow them to edit
		if ($this->event->isMember()) {
			return true;
		}

		// Owner of the albums are allowed to edit
		if ($this->my->id == $this->album->user_id) {
			return true;
		}

		return false;
	}

	/**
	 * Set the current breadcrumbs for the page
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setBreadcrumbs($layout)
	{
		// Set the link to the groups
		FD::page()->breadcrumb($this->event->getName(), $this->event->getPermalink());

		if ($layout == 'form') {
			FD::page()->breadcrumb($this->getPageTitle('default', false));
		}

		if ($layout == 'item') {
			FD::page()->breadcrumb($this->getPageTitle('default', false) , FRoute::albums(array('uid' => $this->event->id , 'type' => SOCIAL_TYPE_GROUP)));
		}

		// Set the albums breadcrumb
		FD::page()->breadcrumb($this->getPageTitle($layout, false));
	}

	public function setPrivacy( $privacy , $customPrivacy )
	{
		// We don't really need to use the privacy library here.
	}

	/**
	 * Determines if the user is allowed to create albums in this event
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canCreateAlbums()
	{
		// If the user is a site admin, they are allowed to
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// If they are a member of the event, they are allowed to.
		if ($this->event->isMember($this->my->id) && $this->my->getAccess()->get('photos.create') && $this->my->getAccess()->get('albums.create') && $this->event->getCategory()->getAcl()->get('photos.enabled', true)) {			
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can upload into the album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canUpload()
	{
		// Site admins are always allowed
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if (!$this->my->getAccess()->get('photos.create')) {
			return false;
		}

		// Event admins are always allowed
		if ($this->event->isAdmin()) {
			return true;
		}

		// Event members are allowed to upload and collaborate in albums
		if ($this->event->isMember()) {
			return true;
		}

		// If the current viewer is the owner of the album
		if ($this->lib->data->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	public function exceededLimits()
	{
		// @TODO: Check for group limits

		return false;
	}

	public function getExceededHTML()
	{
		$theme = FD::themes();
		$theme->set( 'user', $my );
		$html = $theme->output( 'site/albums/exceeded' );

		return $this->output( $html, $album->data );
	}

	/**
	 * Determines if the user is allowed to set the cover for the album
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canSetCover()
	{
		// Site admin's can do anything they want
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Group admins are allowed
		if ($this->event->isAdmin()) {
			return true;
		}

		// If the user is the owner, they are allowed
		if ($this->album->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the album is owned by the current user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isOwner()
	{
		// Site admins should always be treated as the owner
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Group admins should always be treated as the owner
		if ($this->event->isAdmin()) {
			return true;
		}

		// If the user is the creator of the album, they should also be treated as the owner
		if ($this->album->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	public function allowMediaBrowser()
	{
		// Site admins should always be treated as the owner
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if ($this->event->isAdmin()) {
			return true;
		}

		return false;
	}

	public function hasPrivacy()
	{
		return false;
	}

	/**
	 * Retrieves the creation link
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreateLink()
	{
		$url = FRoute::albums(array('layout' => 'form', 'uid' => $this->event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));

		return $url;
	}

	/**
	 * Retrieves the upload limit
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUploadLimit()
	{
		$access = $this->event->getAccess();

		return $access->get('photos.maxsize') . 'M';
	}

	public function isblocked()
	{
		if (FD::user()->id != $this->event->creator_uid) {
			return FD::user()->isBlockedBy($this->event->creator_uid);
		}
		return false;
	}
}
