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
class SocialPhotoAdapterEvent extends SocialPhotoAdapter
{
	private $event = null;
	private $access = null;

	public function __construct(SocialPhoto $lib , SocialAlbums $albumLib)
	{
		$this->event  = FD::event($lib->uid);
		$this->access = $this->event->getAccess();

		parent::__construct($lib, $albumLib);
	}

	public function heading()
	{
		$theme = FD::themes();
		$theme->set('event', $this->event);

		$output = $theme->output('site/albums/header.event');

		return $output;
	}

	/**
	 * Determines if the current user is allowed to view the photo
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function viewable()
	{
		// Admin can do anything they want
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Open group, anyone can view anything
		if ($this->event->isOpen()) {
			return true;
		}

		// As long as the viewer is a member, allow them to view.
		if ($this->event->isMember()) {
			return true;
		}

		return false;
	}

	public function albumViewable()
	{
		return $this->albumLib->viewable();
	}

	/**
	 * Determines the page title to display to the user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPageTitle($layout, $prefix = true)
	{
		if ($layout == 'item' || $layout == 'form') {
			$title	= $this->photo->get( 'title' );
		}

		if ($prefix) {
			$title 	= $this->event->getName() . ' - ' . $title;
		}

		return $title;
	}

	public function setBreadcrumbs($layout)
	{
		// Set the link to the groups
		FD::page()->breadcrumb($this->event->getName(), $this->event->getPermalink());

		if ($layout == 'item') {
			FD::page()->breadcrumb($this->album->get('title'), $this->album->getPermalink());
		}

		// Set the albums breadcrumb
		FD::page()->breadcrumb($this->getPageTitle($layout, false));
	}

	public function getAlbumLink()
	{
		$url 	= FRoute::albums( array( 'layout' => 'item' , 'id' => $this->album->getAlias() , 'uid' => $this->event->getAlias() , 'type' => SOCIAL_TYPE_GROUP ) );

		return $url;
	}

	public function featureable()
	{
		// Allow site admin to do anything
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Allow group admin to do anything
		if ($this->event->isAdmin()) {
			return true;
		}

		// Allow album owner to feature the photo
		if ($this->isAlbumOwner()) {
			return true;
		}

		return false;
	}

	public function isMine()
	{
		// Site admin should be treated as their own item
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Group admin should be treated as their own item
		if ($this->event->isAdmin()) {
			return true;
		}

		// The creator should be treated as their own item
		if ($this->photo->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	public function editable()
	{
		// By default, super admin is free to do anything
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Only allow group admin and owner of photo to edit the photo
		if ($this->event->isAdmin() || $this->photo->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the photo is share-able
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function shareable($id = null)
	{
		// Allow sharing on open groups
		if ($this->event->isOpen()) {
			return true;
		}

		return false;
	}

	public function downloadable( $id = null )
	{
		if (!$this->config->get('photos.downloads', true)) {
			return false;
		}
		
		// Site admins are free to do anything
		if( $this->my->isSiteAdmin() )
		{
			return true;
		}

		// Allow downloads for public group
		if( $this->event->isOpen() )
		{
			return true;
		}

		// If the user is member
		if ($this->event->isMember()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can move the photo
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moveable()
	{
		// Site admins are free to do anything
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Only allow group admins
		if ($this->event->isAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can delete this photo
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteable()
	{
		// Site admins are free to do anything
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Event admins are allowed to delete photos
		if ($this->event->isAdmin()) {
			return true;
		}

		// Allow photo owner to delete this photo
		if ($this->photo->user_id == $this->my->id) {
			return true;
		}

		// Allow album owner to delete this photo
		if ($this->isAlbumOwner()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user is allowed to tag on the photo
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function taggable()
	{
		// Site admin can do anything they want
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Group members can tag people in a photo.
		if ($this->event->isMember()) {
			return true;
		}

		return false;
	}

	public function canSetProfilePicture()
	{
		// Do not allow users to set profile picture for photos from a group
		return false;
	}

	public function canSetProfileCover()
	{
		// Do not allow users to set profile cover for photos from a group
		return false;
	}

	public function getErrorMessage( $type )
	{
		if ($type == 'upload.exceeded') {
			return JText::sprintf( 'COM_EASYSOCIAL_PHOTOS_EXCEEDED_MAX_UPLOAD', $this->access->get('photos.max'));
		}

		if ($type == 'upload.daily.exceeded') {
			return JText::sprintf( 'COM_EASYSOCIAL_PHOTOS_EXCEEDED_DAILY_MAX_UPLOAD', $this->access->get('photos.maxdaily'));
		}
	}

	/**
	 * Determines if the event exceeded their upload limits
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededUploadLimit()
	{
		// If it is 0, it means unlimited
		if ($this->access->get( 'photos.max' ) == 0) {
			return false;
		}

		if ($this->access->exceeded('photos.max', $this->event->getTotalPhotos())) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the event exceeded their disk storage usage
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededDiskStorage()
	{
		// Not implemented on events yet
		return false;
	}

	/**
	 * Determines if the event exceeded their daily upload limit
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededDailyUploadLimit()
	{
		// If it is 0, it means unlimited
		if ($this->access->get('photos.maxdaily') == 0) {
			return false;
		}

		if ($this->access->exceeded('photos.maxdaily', $this->event->getTotalPhotos(true))) {
			return true;
		}

		return false;
	}

	public function getUploadFileSizeLimit()
	{
		// Get the group access
		$limit		= $this->access->get('photos.maxsize') . 'M';

		return $limit;
	}

	public function canRotatePhoto()
	{
		// Allow site admins to rotate any photo they want
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Allow group admin to rotate photos
		if ($this->event->isAdmin()) {
			return true;
		}

		if ($this->event->isMember()) {
			return true;
		}

		// Allow photo owner to rotate photos
		if ($this->photo->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	public function createStream( $verb, $mysqldatestring = '' )
	{
		// Encode the photo as a json string to offload the weight
		$params = FD::json()->encode($this->photo);

		// Get the stream lib
		$stream	= FD::stream();
		$tpl	= $stream->getTemplate();

		// Set the actor, it always has to be the user
		$tpl->setActor($this->photo->user_id , SOCIAL_TYPE_USER);

		// Set the context.
		$tpl->setContext($this->photo->id, SOCIAL_TYPE_PHOTO, $params);

		// set the target id, in this case, the album id.
		$tpl->setTarget($this->photo->album_id);

		// Set the verb
		$tpl->setVerb($verb);

		if (!empty($mysqldatestring)) {
			$tpl->setDate($mysqldatestring);
		}

		// Since this is group uploads, we want to set the cluster
		$tpl->setCluster($this->event->id, SOCIAL_TYPE_EVENT);

		// Set the params to cache the group data
		$registry = FD::registry();
		$registry->set('event', $this->event);

		// Set the params to cache the group data
		$tpl->setParams( $registry );

		// Public viewing of the photo should rely on photos.view privacy.
		$tpl->setPublicStream('photos.view');

		if ($verb == 'create') {
			// We want to aggregate new photo uploads to an album.
			$tpl->setAggregate(true, true);
		}

		if ($verb == 'uploadAvatar' || $verb == 'updateCover') {
			// We shouldnt aggregate avatar uploads.
			$tpl->setAggregate(false);
		}

		// Create the stream data.
		$stream->add($tpl);
	}

	public function isAlbumOwner()
	{
		$this->albumLib->isOwner();
	}

	/**
	 * Determines if we can use cover
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowUseCover()
	{
		if ($this->photo->uid == $this->event->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer is allowed to remove the cover.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canDeleteCover()
	{
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if ($this->event->isAdmin()) {
			return true;
		}

		return false;
	}

	public function canUploadCovers()
	{
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if ($this->event->isAdmin()) {
			return true;
		}

		return false;
	}

	public function canUseAvatar()
	{
		if( $this->photo->uid != $this->event->id )
		{
			return false;
		}

		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if ($this->event->isAdmin()) {
			return true;
		}

		return false;
	}

	public function getDefaultAlbum()
	{
		$model = FD::model('Albums');
		$album = $model->getDefaultAlbum($this->event->id, SOCIAL_TYPE_EVENT, SOCIAL_ALBUM_PROFILE_PHOTOS);

		return $album;
	}


	public function hasPrivacy()
	{
		return false;
	}

	public function canMovePhoto()
	{
		// If this is a system album like cover photos, profile pictures, they will not be able to move photos within this album.
		$disallowed = array( SOCIAL_ALBUM_STORY_ALBUM , SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

		if (in_array($this->album->core, $disallowed)) {
			return false;
		}

		// If user is a site admin, allow this
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// If user is group admin
		if ($this->event->isAdmin()) {
			return true;
		}

		if ($this->photo->user_id != $this->my->id) {
			return false;
		}

		return false;
	}

	public function isblocked()
	{
		if (FD::user()->id != $this->event->creator_uid) {
			return FD::user()->isBlockedBy($this->event->creator_uid);
		}

		return false;
	}
}
