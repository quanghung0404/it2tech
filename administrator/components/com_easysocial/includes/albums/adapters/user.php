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
 * User adapter for albums
 *
 * @since	1.2
 * @access	public
 *
 */
class SocialAlbumsAdapterUser extends SocialAlbumsAdapter
{
	private $user 	= null;

	public function __construct( SocialAlbums $lib )
	{
		$this->user 	= FD::user( $lib->uid );

		parent::__construct( $lib );
	}

	public function heading()
	{
		$theme 	= FD::themes();
		$theme->set( 'user' , $this->user );

		$output = $theme->output( 'site/albums/header.user' );

		return $output;
	}

	public function isValidNode()
	{
		if( !$this->user->id )
		{
			$this->lib->setError( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_USER_PROVIDED' ) );
			return false;
		}

		return true;
	}

	public function getPageTitle($layout, $prefix = true)
	{
		// Set page title
		$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_ALBUMS');

		if ($layout == 'form') {
			$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_ALBUM');
		}

		if ($prefix) {
			$title = $this->user->getName() . ' - ' . $title;
		}

		if ($layout == 'item') {
			$title .= ' - ' . $this->album->get('title');
		}

		return $title;
	}

	public function editable()
	{
		$my 	= FD::user();

		// Super admins are allowed to edit
		if( $my->isSiteAdmin() )
		{
			return true;
		}

		// If the current album is new album, they should be allowed
		if( !$this->album->id )
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

	public function getViewAlbumsLink( $xhtml = true )
	{
		$url 	= FRoute::albums( array( 'uid' => $this->user->getAlias() , 'type' => SOCIAL_TYPE_USER ) , $xhtml );

		return $url;
	}

	public function viewable()
	{
		// Get the privacy library
		$privacy = FD::privacy( FD::user()->id );

		if( $privacy->validate( 'albums.view' , $this->album->id, 'albums', $this->user->id ) )
		{
			return true;
		}


		return false;
	}

	public function setBreadcrumbs( $layout )
	{
		// Set the breadcrumbs
		FD::page()->breadcrumb( $this->getPageTitle( $layout ) );
	}

	public function deleteable()
	{
		// If this is a core album, it should never be allowed to delete
		if( $this->album->isCore() )
		{
			return false;
		}

		$my 	= FD::user();

		// Admins are allowed to delete
		if( $my->isSiteAdmin() )
		{
			return true;
		}

		// If the owner of the album is the user.
		if( $this->album->user_id == $my->id )
		{
			return true;
		}

		return false;
	}

	public function canCreateAlbums()
	{
		$my = FD::user();
		$access = $my->getAccess();

		// Super admins should always be able to create a new album
		if ($my->isSiteAdmin()) {
			return true;
		}

		// We need to check for albums and photos creation access as well.
		if ($my->id && $access->allowed('albums.create') && $access->allowed('photos.create')) {
			return true;
		}

		return false;
	}

	public function setPrivacy( $privacy , $customPrivacy )
	{
		$lib		= FD::privacy();
		$lib->add( 'albums.view' , $this->album->id , 'albums' , $privacy, null, $customPrivacy );
	}

	public function exceededLimits()
	{
		$my 		= FD::user();
		$access		= $my->getAccess();

		// If it is unlimited, it should never exceed the limit
		if( $access->get( 'albums.total' , 0 ) == 0 )
		{
			return false;
		}

		if( $access->exceeded( 'albums.total' , $my->getTotalAlbums( true ) ) )
		{
			return true;
		}

		return false;
	}

	public function getExceededHTML()
	{
		$my 	= FD::user();

		$theme	= FD::themes();
		$theme->set( 'user'	, $my );

		$output	= $theme->output( 'site/albums/exceeded.user' );

		return $output;
	}

	public function canUpload()
	{
		$my 	= FD::user();

		if (!$my->getAccess()->allowed('photos.create')) {
			return false;
		}

		// This could be a new album
		if( !$this->lib->data->id && $my->id )
		{
			return true;
		}

		if( $this->lib->data->user_id == $my->id )
		{
			return true;
		}

		return false;
	}

	public function canSetCover()
	{
		// Site admin's can do anything they want
		if( $this->my->isSiteAdmin() )
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
		// Site admin can do anything they want
		if( $this->my->isSiteAdmin() )
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

	public function allowMediaBrowser()
	{
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function hasPrivacy()
	{
		// If this is a system album like cover photos, profile pictures, they will not be able to move photos within this album.
		$disallowed = array(SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

		if (in_array($this->album->core, $disallowed)) {
			return false;
		}

		return true;
	}

	public function getCreateLink()
	{
		$options 	= array( 'layout' => 'form' , 'uid' => $this->user->getAlias() , 'type' => SOCIAL_TYPE_USER );

		return FRoute::albums( $options );
	}

	public function getUploadLimit()
	{
		$access 	= $this->my->getAccess();

		return $access->get( 'photos.uploader.maxsize' ) . 'M';
	}

	public function isblocked()
	{
		if (FD::user()->id != $this->user->id) {
			return FD::user()->isBlockedBy($this->user->id);
		}

		return false;
	}
}
