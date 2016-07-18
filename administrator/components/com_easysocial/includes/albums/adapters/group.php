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

require_once(dirname(__FILE__) . '/abstract.php');

class SocialAlbumsAdapterGroup extends SocialAlbumsAdapter
{
	private $group 	= null;

	public function __construct( SocialAlbums $lib )
	{
		$this->group	= FD::group( $lib->uid );

		parent::__construct( $lib );
	}

	public function heading()
	{
		$theme 	= FD::themes();
		$theme->set( 'group' , $this->group );

		$output = $theme->output( 'site/albums/header.group' );

		return $output;
	}

	public function isValidNode()
	{
		if( !$this->group->id )
		{
			$this->lib->setError( JText::_( 'Sorry, but the group id provided is not a valid group id.' ) );
			return false;
		}

		if (Foundry::user()->id != $this->group->creator_uid) {
			if(FD::user()->isBlockedBy($this->group->creator_uid)) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
			}
		}

		return true;
	}

	public function getViewAlbumsLink( $xhtml = true )
	{
		$url 	= FRoute::albums( array( 'uid' => $this->group->getAlias() , 'type' => SOCIAL_TYPE_GROUP ) , $xhtml );

		return $url;
	}

	public function getPageTitle( $layout , $prefix = true )
	{
		if( $layout == 'item' )
		{
			$title 	= $this->lib->data->get( 'title' );
		}

		if( $layout == 'form' )
		{
			$title 	= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_CREATE_ALBUM' );
		}

		if( $layout == 'default' )
		{
			$title	= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALBUMS' );
		}

		if( $prefix )
		{
			$title 	= $this->group->getName() . ' - ' . $title;
		}

		return $title;
	}

	/**
	 * Determines if the current viewer can view the album
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function viewable()
	{
		// Public group should be accessible.
		if( $this->group->isOpen() )
		{
			return true;
		}

		// Group members should be allowed
		if( $this->group->isMember() )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can delete the album
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteable()
	{
		// If this is a core album, it should never be allowed to delete
		if( $this->album->isCore() )
		{
			return false;
		}

		// Site admin's are always allowed
		$my 	= FD::user();

		// Super admins are allowed to edit
		if( $my->isSiteAdmin() )
		{
			return true;
		}

		// Group admin's are always allowed
		if( $this->group->isAdmin() )
		{
			return true;
		}

		// Owner of the albums are allowed to edit
		if( $my->id == $this->album->user_id )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can edit the album
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editable()
	{
		// Perhaps the person is creating a new album
		if( !$this->album->id )
		{
			return true;
		}

		// Site admin's are always allowed
		$my 	= FD::user();

		// Super admins are allowed to edit
		if( $my->isSiteAdmin() )
		{
			return true;
		}

		// Group admin's are always allowed
		if( $this->group->isAdmin() )
		{
			return true;
		}

		// If user is a member, allow them to edit
		if ($this->group->isMember()) {
			return true;
		}

		// Owner of the albums are allowed to edit
		if( $my->id == $this->album->user_id )
		{
			return true;
		}

		return false;
	}

	public function setBreadcrumbs( $layout )
	{
		// Set the link to the groups
		FD::page()->breadcrumb( $this->group->getName() , FRoute::groups( array( 'layout' => 'item' , 'id' => $this->group->getAlias() ) ) );

		if( $layout == 'form' )
		{
			FD::page()->breadcrumb( $this->getPageTitle( 'default' , false ) );
		}

		if( $layout == 'item' )
		{
			FD::page()->breadcrumb( $this->getPageTitle( 'default' , false ) , FRoute::albums( array( 'uid' => $this->group->id , 'type' => SOCIAL_TYPE_GROUP ) ) );
		}

		// Set the albums breadcrumb
		FD::page()->breadcrumb( $this->getPageTitle( $layout , false ) );
	}

	public function setPrivacy( $privacy , $customPrivacy )
	{
		// We don't really need to use the privacy library here.
	}

	public function canCreateAlbums()
	{
		// Site admin's are always allowed
		$my = FD::user();

		// Super admins are allowed to edit
		if ($my->isSiteAdmin()){
			return true;
		}

		// @TODO: Add additional group access checks
		if ($this->group->isMember($my->id) && $my->getAccess()->get('photos.create') && $my->getAccess()->get('albums.create') && $this->group->getCategory()->getAcl()->get('photos.enabled', true)) {
			return true;
		}

		return false;
	}

	public function canUpload()
	{
		$my = FD::user();

		if (!$my->getAccess()->get('photos.create')) {
			return false;
		}

		if ($this->group->isAdmin()) {
			return true;
		}

		// Group members are allowed to upload and collaborate in albums
		if ($this->group->isMember()) {
			return true;
		}

		if ($this->lib->data->user_id == $my->id) {
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

	public function canSetCover()
	{
		// Site admin's can do anything they want
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Group admin's are allowed
		if( $this->group->isAdmin() )
		{
			return true;
		}

		// If the user is the owner, they are allowed
		if( $this->album->user_id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function isOwner()
	{
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		if( $this->group->isAdmin() )
		{
			return true;
		}

		if( $this->album->user_id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function allowMediaBrowser()
	{
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		if( $this->group->isAdmin() )
		{
			return true;
		}

		return false;
	}

	public function hasPrivacy()
	{
		return false;
	}

	public function getCreateLink()
	{
		$options 	= array( 'layout' => 'form' , 'uid' => $this->group->getAlias() , 'type' => SOCIAL_TYPE_GROUP );

		return FRoute::albums( $options );
	}

	public function getUploadLimit()
	{
		$access 	= $this->group->getAccess();

		return $access->get( 'photos.maxsize' ) . 'M';
	}

	public function isblocked()
	{
		if (FD::user()->id != $this->group->creator_uid) {
			return FD::user()->isBlockedBy($this->group->creator_uid);
		}
		return false;
	}
}
