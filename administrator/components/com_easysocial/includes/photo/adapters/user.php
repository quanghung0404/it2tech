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

require_once(__DIR__ . '/abstract.php');

class SocialPhotoAdapterUser extends SocialPhotoAdapter
{
	private $user = null;

	public function __construct(SocialPhoto $lib, SocialAlbums $albumLib)
	{
		// In the event the uid is invalid, we get the user id from the photo
		if (!$lib->uid && isset($lib->data->user_id) && $lib->data->user_id) {
			$lib->uid = $lib->data->user_id;
		}

		$this->user = FD::user($lib->uid, $albumLib);

		parent::__construct( $lib , $albumLib );
	}

	public function heading()
	{
		$theme 	= FD::themes();
		$theme->set('user', $this->user);

		$output = $theme->output('site/albums/header.user');

		return $output;
	}

	public function viewable()
	{
		// If the viewer is trying to view the cover or profile pictures,
		// it should always be visible regardless since it's already visible in the first place.
		if ($this->album->core == SOCIAL_ALBUM_PROFILE_PHOTOS || $this->album->core == SOCIAL_ALBUM_PROFILE_COVERS) {
			return true;
		}

		// The privacy of photos are dependent on the album
		$privacy = FD::privacy(FD::user()->id);

		// we need to check the photo's album privacy to see if user allow to view or not.
		if (!$privacy->validate('albums.view', $this->album->id,  SOCIAL_TYPE_ALBUM, $this->photo->uid)) {
			return false;
		}

		return $privacy->validate( 'photos.view', $this->photo->id , 'photos' , $this->photo->uid );
	}

	public function albumViewable()
	{
		return $this->albumLib->viewable();
	}

	public function getPageTitle( $layout , $prefix = true )
	{
		if( $layout == 'item' || $layout == 'form' )
		{
			$title	= $this->photo->get( 'title' );
		}

		if( $prefix )
		{
			$title 	= $this->user->getName() . ' - ' . $title;
		}

		return $title;
	}

	public function setBreadcrumbs( $layout )
	{
		if( $layout == 'item' )
		{
			FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALBUMS' ) , FRoute::albums() );
			FD::page()->breadcrumb( $this->album->get( 'title' ) , $this->album->getPermalink() );
			FD::page()->breadcrumb( $this->photo->get( 'title' ) );
		}

		if( $layout == 'form' )
		{
			FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALBUMS' ) , FRoute::albums() );
			FD::page()->breadcrumb( $this->album->get( 'title' ) , $this->album->getPermalink() );
			FD::page()->breadcrumb( $this->photo->get( 'title' ) , $this->photo->getPermalink() );
			FD::page()->breadcrumb( JText::_( 'Editing photo' ) );
		}
	}

	public function getAlbumLink()
	{
		$url 	= FRoute::albums( array( 'layout' => 'item' , 'id' => $this->album->getAlias() , 'uid' => $this->user->getAlias() , 'type' => SOCIAL_TYPE_USER ) );

		return $url;
	}

	public function featureable()
	{
		if( $this->photo->uid == $this->my->id || $this->my->isSiteAdmin() )
		{
			return true;
		}

		// @TODO: Test if this photo privacy allow friends to tag on the photo

		return false;
	}

	/**
	 * Determines if the photo is owned by the provided user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isMine()
	{
		if( $this->photo->uid == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function editable()
	{
		// Site admins can do anything they want
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// If the user is the owner of this photo we need to allow this
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function shareable()
	{
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Allow owner to share the photo
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		// If the photo is from a private album, pointless to share this?
		if( $this->viewable() )
		{
			return true;
		}

		return false;
	}


	/**
	 * Tests if the user is allowed to download the photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function downloadable( $id = null )
	{
		if (!$this->config->get('photos.downloads', true)) {
			return false;
		}

		$user 	= FD::user( $id );

		return true;
	}


	/**
	 * Determines if the user is allowed to move the photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moveable()
	{
		// If this is a system album like cover photos, profile pictures, they will not be able to move photos within this album.
		$disallowed = array( SOCIAL_ALBUM_STORY_ALBUM , SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

		if( in_array( $this->album->core , $disallowed ) )
		{
			return false;
		}

		// Allow site admins to move anything
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Allow owners to move the photo
		if( $this->photo->user_id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	/**
	 * Tests if the album is delete able by the provided user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	User id.
	 * @return
	 */
	public function deleteable( $id = null , $type = SOCIAL_TYPE_USER )
	{
		// Super admin can do anything they want
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Allow owner of the photo to delete their photo
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function taggable()
	{
		// Guest is not allowed
		if( !$this->my->id )
		{
			return false;
		}

		// Allow site admin to do anything they want
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Allow owner of the photo to tag the item
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		// Determine if the photo privacy allows friend to tag on their photo
		$privacy 	= FD::privacy( $this->user->id );

		if( $privacy->validate( 'photo.tag' , $this->user->id , SOCIAL_TYPE_USER ) )
		{
			return true;
		}

		return false;
	}

	public function canSetProfilePicture()
	{
		// Allow user to set the profile picture
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function canSetProfileCover()
	{
		// Allow user to set the profile cover
		if( $this->user->id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function exceededDiskStorage()
	{
		// For site admin, ignore this
		if ($this->my->isSiteAdmin()) {
			return false;
		}

        $model = FD::model('Photos');
        $usage = $model->getDiskUsage($this->my->id, 'mb');

        // Get the user's access
        $access = $this->my->getAccess();

        if ($access->exceeded('photos.totalfiles.limit', $usage)) {
			$this->lib->setError(JText::sprintf('COM_EASYSOCIAL_PHOTOS_EXCEEDED_DISK_USAGE', $access->get('photos.totalfiles.limit')));

			return true;
        }

        return false;
	}

	public function exceededUploadLimit()
	{
		$access = $this->my->getAccess();

		// If it is 0, it means unlimited
		if( $access->get( 'photos.uploader.max' ) == 0 )
		{
			return false;
		}

		// check max photos upload here.
		if( $access->exceeded( 'photos.uploader.max' , $this->my->getTotalPhotos() ) )
		{
			$this->lib->setError( JText::sprintf( 'COM_EASYSOCIAL_PHOTOS_EXCEEDED_MAX_UPLOAD', $access->get( 'photos.uploader.max' ) ) );

			return true;
		}

		return false;
	}

	public function exceededDailyUploadLimit()
	{
		$access		= $this->my->getAccess();

		if ($this->my->isSiteAdmin()) {
			return false;
		}

		$limit = $access->get('photos.uploader.maxdaily');
		$total = $this->my->getTotalPhotos(true, true);

		// If it is 0, it means unlimited
		if ($limit == 0) {
			return false;
		}

		if ($access->exceeded('photos.uploader.maxdaily', $total)) {
			$this->lib->setError(JText::sprintf( 'COM_EASYSOCIAL_PHOTOS_EXCEEDED_DAILY_MAX_UPLOAD', $access->get('photos.uploader.maxdaily')));
			return true;
		}

		return false;
	}

	public function getUploadFileSizeLimit()
	{
		$access	= $this->my->getAccess();

		$limit 	= $access->get('photos.uploader.maxsize') . 'M';

		return $limit;
	}

	public function canRotatePhoto()
	{
		// Super admin is free to do anything they want
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Photo owner is allowed to rotate photo
		if( $this->user->id == $this->my->id )
		{
			return true;
		}
	}

	public function createStream( $verb, $mysqldatestring = '' )
	{
		// Encode the photo as a json string to offload the weight
		$params 	= FD::json()->encode( $this->photo );

		// Get the stream lib
		$stream	= FD::stream();
		$tpl	= $stream->getTemplate();

		// Set the actor, it always has to be the user
		$tpl->setActor( $this->photo->user_id , SOCIAL_TYPE_USER );

		// Set the context.
		$tpl->setContext( $this->photo->id , SOCIAL_TYPE_PHOTO , $params );

		// set the target id, in this case, the album id.
		$tpl->setTarget( $this->photo->album_id );

		// Set the verb
		$tpl->setVerb( $verb );

		if( !empty( $mysqldatestring ) )
		{
			$tpl->setDate( $mysqldatestring );
		}

		// Public viewing of the photo should rely on photos.view privacy.
		$tpl->setAccess( 'photos.view' );

		if( $verb == 'create' )
		{
			// We want to aggregate new photo uploads to an album.
			$tpl->setAggregate( true, true );
		}

		if( $verb == 'uploadAvatar' || $verb == 'updateCover' )
		{
			// We shouldnt aggregate avatar uploads.
			$tpl->setAggregate( false );
		}

		// Create the stream data.
		$stream->add( $tpl );
	}


	public function isAlbumOwner()
	{
		$this->albumLib->isOwner();
	}

	public function allowUseCover()
	{
		if( $this->photo->user_id == $this->my->id || ($this->photo->uid == $this->my->id && $this->photo->type == SOCIAL_TYPE_USER) )
		{
			return true;
		}

		return false;
	}

	public function canDeleteCover()
	{
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		if( $this->photo->user_id == $this->my->id )
		{
			return true;
		}

		return false;
	}

	public function canUploadCovers()
	{
		return true;
	}

	public function canUseAvatar()
	{
		if( $this->photo->user_id == $this->my->id )
		{
			return true;
		}

		if( $this->my->isSiteAdmin() )
		{
			return true;
		}


		return false;
	}

	public function getDefaultAlbum()
	{
		$model 	= FD::model( 'Albums' );
		$album	= $model->getDefaultAlbum( $this->user->id , SOCIAL_TYPE_USER , SOCIAL_ALBUM_PROFILE_PHOTOS );

		return $album;
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

	public function canMovePhoto()
	{
		// If this is a system album like cover photos, profile pictures, they will not be able to move photos within this album.
		$disallowed = array( SOCIAL_ALBUM_STORY_ALBUM , SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

		if (in_array($this->album->core, $disallowed)) {
			return false;
		}

		// Allow site admins to move anything
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Allow owners to move the photo
		if ($this->photo->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	public function isblocked()
	{
		if (FD::user()->id != $this->user->id) {
			return FD::user()->isBlockedBy($this->user->id);
		}
		return false;
	}
}
