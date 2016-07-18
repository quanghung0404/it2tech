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

ES::import('admin:/includes/group/group');

class SocialUserAppPhotos extends SocialAppItem
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Determines if the viewer can delete the comments
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canDeleteComment(SocialTableComments &$comment, SocialUser &$viewer)
	{
		$allowed = array('photos.user.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// Get the photo owner
		$photo 	= Foundry::table('Photo');
		$photo->load($comment->uid);

		if ($photo->user_id == $viewer->id) {
			return true;
		}

		return;
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
		if( $item->context != 'photos' )
		{
			return;
		}

		// Get the context id.
		$id 		= $item->contextId;

		// Load the profiles table.
		$photo	= Foundry::table( 'Photo' );
		$state 	= $photo->load( $id );

		$album 	= Foundry::table( 'Album' );
		$album->load( $photo->album_id );

		// Get the actor
		$actor 		= $item->actor;
		$target 	= false;

		// Determines if the photo is shared on another person's timeline
		if( $item->verb == 'share' && $item->targets )
		{
			$target 	= $item->targets[0];
		}


		$term 	= $this->getGender( $item->actor );

		$this->set( 'term'	, $term );
		$this->set( 'actor'	, $actor );
		$this->set( 'target', $target );
		$this->set( 'album'	, $album );
		$this->set( 'photo'	, $photo );

		$count 			= count( $item->contextIds );
		$this->set( 'count'	, $count );

		$file = 'user/';

		if( $item->cluster_id )
		{
			$file = 'group/';

			$group = Foundry::group( $item->cluster_id );
			$this->set( 'group'	, $group );
		}

		if ($item->verb == 'uploadAvatar') {
			$file 	.= 'upload.avatar';
		}

		if ($item->verb == 'updateCover') {
			$file 	.= 'upload.cover';
		}

		if ($item->verb == 'create' || $item->verb == 'add' ) {
			$file 	.= 'add';
		}

		if ($item->verb == 'share') {
			$file 	.= 'share';
		}

		$item->display 	= SOCIAL_STREAM_DISPLAY_MINI;
		$item->title	= parent::display( 'streams/' . $file . '.title' );
		$item->content	= parent::display( 'streams/activity_content' );

		$privacyRule = 'photos.view';
		if( $item->verb == 'uploadAvatar' || $item->verb == 'updateCover')
		{
			$privacyRule = 'core.view';
		}

		if( $includePrivacy )
		{
			$my         = Foundry::user();

			$sModel = Foundry::model('Stream');
			$aItem  = $sModel->getActivityItem( $item->aggregatedItems[0]->uid, 'uid' );

			$streamId = count($aItem) > 1 ? '' : $item->aggregatedItems[0]->uid;
			$item->privacy = Foundry::privacy( $my->id )->form( $photo->id, 'photos', $item->actor->id, $privacyRule, false, $streamId );
		}
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
		$obj 			= new stdClass();
		$obj->color		= '#F8829C';
		$obj->icon 		= 'fa fa-image';
		$obj->label 	= 'APP_USER_PHOTOS_STREAM_TOOLTIP';

		return $obj;
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
		if( $item->context_type != 'photos' )
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$my         = Foundry::user();
			$privacy	= Foundry::privacy( $my->id );

			$sModel = Foundry::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );


			$uid 		= $aItem[0]->context_id;
			$rule 		= 'photos.view';
			$context 	= 'photos';

			if( count( $aItem ) > 0 )
			{
				$uid 		= $aItem[0]->target_id;

				if( $aItem[0]->target_id )
				{
					$rule 		= 'albums.view';
					$context 	= 'albums';
					$uid 		= $aItem[0]->target_id;
				}
			}

			if( !$privacy->validate( $rule, $uid, $context, $item->actor_id ) )
			{
				$item->cnt = 0;
			}

		}

		return true;
	}

	/**
	 * Trigger for onPrepareStream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We only want to process related items
		if ($item->context != 'photos') {
			return;
		}

		// If photos has been disabled, there's no point showing any stream
		$config		= Foundry::config();

		// Do not allow user to access photos if it's not enabled
		if (!$config->get( 'photos.enabled' ) && $item->verb != 'uploadAvatar' && $item->verb != 'updateCover') {
			return;
		}

		// Get current logged in user.
		$my         = Foundry::user();

		// Get user's privacy.
		$privacy 	= Foundry::privacy( $my->id );

		$element	= $item->context;
		$uid     	= $item->contextId;
		$useAlbum	= count($item->contextIds) > 1 ? true : false;

		// Decorate the stream
		$item->color 		= '#F8829C';
		$item->fonticon 	= 'fa-image';
		$item->label 		= FD::_('APP_USER_PHOTOS_STREAM_TOOLTIP', true);
		$item->display		= SOCIAL_STREAM_DISPLAY_FULL;


		// Load the photo object
		$photo			= Foundry::table( 'Photo' );
		$photoId		= $item->contextId;
		$photoParams	= ( isset( $item->contextParams[ $photoId ] ) ) ? $item->contextParams[ $photoId ] : '';

		if ($photoParams) {
			$obj = FD::json()->decode($photoParams);

			if (!$obj) {
				$photo->load($photoId);
			} else {

				// Bind the photo data
				$photo->bind($obj);

				if (!$photo->id) {
					$photo->load($photoId);
				}
			}
		} else {
			$photo->load( $photoId );
		}

		// If this is a group, we need to prepare accordingly.
		if( $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_GROUP ) {
			$group		= Foundry::group( $item->cluster_id );

			// If we can't locate the group, skip this.
			if( !$group )
			{
				return;
			}

			// Check if the user can really view this stream
			if( !$group->isOpen() && !$group->isMember() )
			{
				return;
			}

			// Check if the group is private or invite, dont show the sharing button
			if( !$group->isOpen()) {
				$item->sharing = false;
			}

			// We need a different label for group items
			$item->color 	= '#303229';
			$item->fonticon = 'fa fa-users';
			$item->label 	= FD::_( 'APP_USER_PHOTOS_GROUPS_STREAM_TOOLTIP', true);
		}

		// If this is an event, we need to prepare accordingly.
		if ($item->cluster_id && $item->cluster_type == SOCIAL_TYPE_EVENT) {
			$event = FD::event($item->cluster_id);

			if (empty($event) || empty($event->id)) {
				return;
			}

			if (!$event->isOpen() && !$event->getGuest()->isGuest()) {
				return;
			}

			// Check if the event is private or invite, dont show the sharing button
			if( !$event->isOpen()) {
				$item->sharing = false;
			}

			$item->color = '#f06050';
			$item->fonticon = 'fa fa-calendar';
			$item->label = FD::_('APP_USER_EVENTS_STREAM_TOOLTIP', true);
		}

		// Process actions on the stream
		$this->processActions( $item , $privacy );

		$privacyRule = ( $useAlbum ) ? 'albums.view' : 'photos.view';

		if( $item->verb == 'uploadAvatar' || $item->verb == 'updateCover')
		{
			$privacyRule = 'core.view';
		}

		if( $includePrivacy )
		{
			if ($privacyRule == 'photos.view') {
				// we need to check the photo's album privacy to see if user allow to view or not.
				// if( !$privacy->validate( 'albums.view' , $photo->album_id,  SOCIAL_TYPE_ALBUM, $item->actor->id ) )
				if (!$privacy->validate('photos.view' , $photo->id,  SOCIAL_TYPE_PHOTO, $item->actor->id)) {
					return;
				}

			} else {

				if ($useAlbum && $privacyRule =='albums.view') {

					$uid = $photo->album_id;
					$element = 'albums';
				}

				// Determine if the user can view this current context
				if( !$privacy->validate( $privacyRule , $uid, $element , $item->actor->id ) )
				{
					return;
				}
			}
		}

		// Get the single context id
		$id 		= $item->contextId;
		$albumId 	= '';

		$params 	= $this->getApp()->getParams();

		// Process group avatar updates
		if ($item->verb == 'uploadAvatar' && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_GROUP && $params->get('uploadAvatar', true)) {
			$this->prepareGroupUploadAvatarStream($item, $privacy, $includePrivacy);
		}

		// Process event avatar updates
		if ($item->verb == 'uploadAvatar' && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_EVENT && $params->get('uploadAvatar', true)) {
			$this->prepareEventUploadAvatarStream($item, $privacy, $includePrivacy);
		}

		// Process user avatar updates
		if($item->verb == 'uploadAvatar' && !$item->cluster_id && $params->get('uploadAvatar', true)) {
			$this->prepareUploadAvatarStream($item, $privacy, $includePrivacy);
		}

		// Process group cover updates
		if ($item->verb == 'updateCover' && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_GROUP && $params->get('uploadCover', true)) {
			$this->prepareGroupUpdateCoverStream($item, $privacy, $includePrivacy);
		}

		// Process event cover updates
		if ($item->verb == 'updateCover' && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_EVENT && $params->get('uploadCover', true)) {
			$this->prepareEventUpdateCoverStream($item, $privacy, $includePrivacy);
		}

		// Process user cover updates
		if ($item->verb == 'updateCover' && !$item->cluster_id && $params->get('uploadCover', true)) {
			$this->prepareUpdateCoverStream($item, $privacy, $includePrivacy);
		}

		// Photo stream types. Uploaded via the story form
		$photoStreams 	= array( 'add' , 'create' , 'share' );

		// Old data compatibility
		$item->verb 	= $item->verb == 'create' ? 'add' : $item->verb;

		// Process photo streams for groups
		if( in_array( $item->verb , $photoStreams ) && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_GROUP && $params->get( 'uploadPhotos' , true ) )
		{
			$this->prepareGroupPhotoStream( $item, $privacy , $includePrivacy, $useAlbum );
		}

		// Process photo streams for events
		if (in_array($item->verb, $photoStreams) && $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_EVENT && $params->get('uploadPhotos', true)) {
			$this->prepareEventPhotoStream($item, $privacy, $includePrivacy, $useAlbum);
		}

		// Process photo streams for users
		if( in_array( $item->verb , $photoStreams ) && !$item->cluster_id && $params->get( 'uploadPhotos' , true ) )
		{
			$this->preparePhotoStream( $item, $privacy , $includePrivacy, $useAlbum );
		}
	
		// Append the opengraph tags
		if ($item->content) {
			$item->addOgDescription($item->content);
		} else {
			$item->addOgDescription($item->title);
		}

		return;
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since	1.2
	 * @access	public
	 * @param	array
	 */
	public function onStreamVerbExclude( &$exclude )
	{
		// Get app params
		$params		= $this->getParams();

		$excludeVerb = false;

		if(! $params->get('uploadAvatar', true)) {
			$excludeVerb[] = 'uploadAvatar';
		}

		if (! $params->get('uploadCover', true)) {
			$excludeVerb[] = 'updateCover';
		}

		if (! $params->get('uploadPhotos', true)) {
			$excludeVerb[] = 'add';
			$excludeVerb[] = 'create';
			$excludeVerb[] = 'share';
		}

		if ($excludeVerb !== false) {
			$exclude['photos'] = $excludeVerb;
		}
	}

	/**
	 * Processes the stream actions
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function processActions( SocialStreamItem &$item , $privacy )
	{

		$group 			= $item->cluster_id ? $item->cluster_type : SOCIAL_APPS_GROUP_USER;

		// Whether the item is shared or uploaded via the photo albums, we need to bind the repost here
		$repost 		= Foundry::get('Repost', $item->uid, SOCIAL_TYPE_STREAM, $group);
		$item->repost	= $repost;

		// For photo items that are shared on the stream
		if ($item->verb =='share') {

			// By default, we'll use the stream id as the object id
			$objectId 		= $item->uid;
			$objectType 	= SOCIAL_TYPE_STREAM;
			$commentUrl 	= FRoute::stream(array('layout' => 'item', 'id' => $item->uid));

			// When there is only 1 photo that is shared on the stream, we need to link to the photo item
			// We will only alter the id
			if (count($item->contextIds) == 1) {
				$photo 		= Foundry::table('Photo');
				$photo->load($item->contextIds[0]);

				$objectId 	= $photo->id;
				$objectType	= SOCIAL_TYPE_PHOTO;
				$commentUrl	= $photo->getPermalink();
			}

			// Append the likes action on the stream
			$likes 			= Foundry::likes();
			$likes->get($objectId, $objectType, 'upload', $group, $item->uid);
			$item->likes	= $likes;

			// Append the comment action on the stream
			$comments			= Foundry::comments($objectId, $objectType, 'upload', $group,  array( 'url' => $commentUrl), $item->uid);
			$item->comments 	= $comments;

			return;
		}

		// If there is more than 1 photo uploaded, we need to link the likes and comments on the album
		if (count($item->contextIds) > 1) {

			$photo = false;
			$photos = $this->getPhotoFromParams($item, $privacy);

			if ($photos instanceof SocialTablePhoto) {
				$photo = $photos;
			}

			if (is_array($photos)) {
				$photo = $photos[0];
			}

			// If we can't get anything, skip this
			if (!$photo) {
				return;
			}

			// If we can't get anything, skip this
			if (!$photo) {
				return;
			}

			$element 	= SOCIAL_TYPE_ALBUM;
			$uid 		= $photo->album_id;

			// Get the album object
			$album 			= Foundry::table('Album');
			$album->load($photo->album_id);

			// Format the likes for the stream
			$likes 			= Foundry::likes();
			$likes->get($photo->album_id, 'albums', 'create', $group);
			$item->likes	= $likes;

			// Apply comments on the stream
			$commentParams 		= array( 'url' => $album->getPermalink());
			$comments			= Foundry::comments($photo->album_id, 'albums', 'create', $group, $commentParams);
			$item->comments 	= $comments;

			return;
		}

		// Here onwards we are assuming that the likes and comment should be applied on the single photo.
		// Since the stream library already handles this nicely, we leave it to the stream to handle.

		// $likes 				= Foundry::likes();
		// $likes->get($item->contextId , $item->context, $item->verb , SOCIAL_APPS_GROUP_USER );
		// $item->likes		= $likes;

		// // Apply comments on the stream
		// $comments			= Foundry::comments( $item->contextId , $item->context , $item->verb, SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::photos( array( 'layout' => 'item', 'id' => $item->contextId ) ) ) );
		// $item->comments 	= $comments;
	}

	/**
	 * Prepares the stream items for photo uploads via story in a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareGroupPhotoStream( SocialStreamItem &$item , $privacy , $includePrivacy = true , $useAlbum = false )
	{
		// There could be more than 1 photo
		$photos  	= array();

		// The default element and uid
		$element	= $item->context;
		$uid     	= $item->contextId;

		// Get photo objects
		$photos 	= $this->getPhotoFromParams( $item , $privacy );

		// Get the unique item and element to be used
		if (count($item->contextIds) > 1) {
			$uid 		= $photos[ 0 ]->id;
			$element	= SOCIAL_TYPE_ALBUM;
		}

		// Get the first photo's album id.
		$albumId 	= $photos[ 0 ]->album_id;

		// Determine the privacy rule to use.
		$privacyRule = ( $useAlbum ) ? 'albums.view' : 'photos.view';

		// Load up the album object
		$album 		= Foundry::table( 'Album' );
		$album->load( $albumId );

		// Get the actor
		$actor 		= $item->actor;

		// Determine what text to use.
		$count 		= count( $item->contextIds );

		// Load up the group object
		$group 			= Foundry::group($item->cluster_id);
		$totalPhotos	= count($photos);

		$ids 	= array();

		foreach ($photos as $photo) {
			$ids[]	= $photo->id;
		}

		// Get params
		$app = Foundry::table( 'app' );
		$app->loadByElement( 'photos', 'user', 'apps');
		$params = $app->getParams();

		$this->set( 'totalPhotos', $totalPhotos );
		$this->set( 'ids'		, $ids );
		$this->set( 'group'		, $group );
		$this->set( 'count'		, $count );
		$this->set( 'photos'	, $photos );
		$this->set( 'album'		, $album );
		$this->set( 'actor'		, $actor );
		$this->set( 'params'	, $params );

		// old data compatibility
		$verb = ( $item->verb == 'create' ) ? 'add' : $item->verb;

		// Set the display mode to be full.
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;

		$item->title 	= parent::display( 'streams/group/' . $verb . '.title' );
		$item->preview 	= parent::display( 'streams/group/' . $verb . '.content' );

		if( $includePrivacy )
		{
			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, $privacyRule, false, $item->uid );
		}
	}

	/**
	 * Prepares the stream items for photo uploads via story in a event.
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function prepareEventPhotoStream(SocialStreamItem &$item, $privacy, $includePrivacy = true, $useAlbum = false)
	{
		// There could be more than 1 photo
		$photos = array();

		// The default element and uid
		$element = $item->context;
		$uid = $item->contextId;

		// Get photo objects
		$photos = $this->getPhotoFromParams($item, $privacy);

		// Get the unique item and element to be used
		if (count($item->contextIds) > 1) {
			$uid = $photos[0]->id;
			$element = SOCIAL_TYPE_ALBUM;
		}

		// Get the first photo's album id.
		$albumId = $photos[0]->album_id;

		// Determine the privacy rule to use.
		$privacyRule = ($useAlbum) ? 'albums.view' : 'photos.view';

		// Load up the album object
		$album = FD::table('Album');
		$album->load($albumId);

		// Get the actor
		$actor = $item->actor;

		// Determine what text to use.
		$count = count($item->contextIds);

		// Load up the group object
		$event = FD::event($item->cluster_id);
		$totalPhotos = count($photos);

		$ids = array();

		foreach ($photos as $photo) {
			$ids[] = $photo->id;
		}

		// Get params
		$app = FD::table('app');
		$app->loadByElement('photos', 'user', 'apps');
		$params = $app->getParams();

		$this->set('totalPhotos', $totalPhotos);
		$this->set('ids', $ids);
		$this->set('event', $event);
		$this->set('count', $count);
		$this->set('photos', $photos);
		$this->set('album', $album);
		$this->set('actor', $actor);
		$this->set('params', $params);

		// old data compatibility
		$verb = ($item->verb == 'create') ? 'add' : $item->verb;

		// Set the display mode to be full.
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		$item->title = parent::display('streams/event/' . $verb . '.title');
		$item->preview = parent::display('streams/event/' . $verb . '.content');

		if ($includePrivacy) {
			$item->privacy = $privacy->form($uid, $element, $item->actor->id, $privacyRule, false, $item->uid);
		}
	}

	/**
	 * Prepares the stream items for photo uploads
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function preparePhotoStream(&$item, $privacy, $includePrivacy = true, $useAlbum = false)
	{
		// There could be more than 1 photo
		$photos = array();

		// The default element and uid
		$element = $item->context;
		$uid = $item->contextId;

		// Get photo objects
		$photos = $this->getPhotoFromParams($item, $privacy);

		if (! $photos) {
			return;
		}

		// Get the unique item and element to be used
		if (count($item->contextIds) > 1) {
			$uid = $photos[0]->album_id;
			$element = SOCIAL_TYPE_ALBUM;
			$useAlbum = true;
		}

		// Get the first photo's album id.
		$albumId = $photos[0]->album_id;

		// Determine the privacy rule to use.
		$privacyRule = ($useAlbum) ? 'albums.view' : 'photos.view';

		// Load up the album object
		$album = Foundry::table('Album');
		$album->load($albumId);

		// Get the actor
		$actor = $item->actor;

		// Ensure that they are all unique
		$item->contextIds = array_unique($item->contextIds);

		$count = count($item->contextIds);
		$totalPhotos = count($photos);

		$ids = array();
		foreach ($photos as $photo) {
			$ids[] = $photo->id;
		}

		// Determine if there is a target
		$target = $item->targets ? $item->targets[0] : '';

		// Get params
		$app = Foundry::table('app');
		$app->loadByElement('photos', 'user', 'apps');
		$params = $app->getParams();

		$this->set('target', $target);
		$this->set('totalPhotos', $totalPhotos);
		$this->set('ids', $ids);
		$this->set('count', $count);
		$this->set('photos', $photos);
		$this->set('album', $album);
		$this->set('actor', $actor);
		$this->set('content', $item->content);
		$this->set('params', $params);
		$this->set('item', $item);

		// old data compatibility
		$verb = ($item->verb == 'create') ? 'add' : $item->verb;

		// Set the display mode to be full.
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		$item->title = parent::display('streams/user/' . $verb . '.title');
		$item->preview = parent::display('streams/user/' . $verb . '.content');

		// Currently, only share photo from story panel has the possiblity to have text and 
		// we want to add read more feature to it.
		if ($verb == 'share') {
			$item->content  = parent::display( 'streams/user/' . $verb . '.text' );
		}

		if ($includePrivacy) {
			// $item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, $privacyRule, false, $item->uid, array('override' => true, 'value' => true) );
			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, $privacyRule, false, $item->uid );
		}
	}

	/**
	 * Prepares the upload avatar stream for a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStream
	 * @return
	 */
	public function prepareGroupUploadAvatarStream( &$item , $privacy, $includePrivacy = true)
	{
		$element	= $item->context;
		$uid    	= $item->contextId;

		// Load the photo
		$photo	= $this->getPhotoFromParams( $item );

		// Get the data of the group
		$registry	= Foundry::registry( $item->params );
		$group 		= new SocialGroup();
		$group->bind( $registry->get( $item->cluster_type ) );

		$this->set( 'group'	, $group );
		$this->set( 'photo' , $photo );
		$this->set( 'actor'	, $item->actor );


		$item->title 	= parent::display( 'streams/group/upload.avatar.title' );
		$item->content 	= parent::display( 'streams/group/upload.avatar.content' );

		if( $includePrivacy )
		{
			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, 'core.view', false, $item->uid );
		}
	}

	/**
	 * Prepares the upload avatar stream for a event
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function prepareEventUploadAvatarStream(&$item, $privacy, $includePrivacy = true)
	{
		$element = $item->context;
		$uid = $item->contextId;

		// Load the photo
		$photo = $this->getPhotoFromParams($item);

		// Get the data of the group
		$registry = Foundry::registry($item->params);
		$event = new SocialEvent();
		$event->bind($registry->get($item->cluster_type));

		$this->set('event', $event);
		$this->set('photo', $photo);
		$this->set('actor', $item->actor);


		$item->title = parent::display('streams/event/upload.avatar.title');
		$item->content = parent::display('streams/event/upload.avatar.content');

		if ($includePrivacy) {
			$item->privacy = $privacy->form($uid, $element, $item->actor->id, 'core.view', false, $item->uid);
		}
	}

	/**
	 * Retrieves the Gender representation of the language string
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getGender(SocialUser $user)
	{
		// Get the term to be displayed
		$value = $user->getFieldData('GENDER');

		$term = 'NOGENDER';

		if ($value == 1) {
			$term = 'MALE';
		}

		if ($value == 2) {
			$term = 'FEMALE';
		}

		return $term;
	}

	/**
	 * Prepares the upload avatar stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStream
	 * @return
	 */
	public function prepareUploadAvatarStream( &$item , $privacy, $includePrivacy = true)
	{
		$element = $item->context;
		$uid     = $item->contextId;

		// Load the photo
		$photo	= $this->getPhotoFromParams( $item );

		$term 	= $this->getGender( $item->actor );

		$this->set( 'term'	, $term );
		$this->set( 'photo' , $photo );
		$this->set( 'actor'	, $item->actor );

		$item->title 	= parent::display( 'streams/user/upload.avatar.title' );
		$item->content 	= parent::display( 'streams/user/upload.avatar.content' );

		if( $includePrivacy )
		{
			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, 'core.view', false, $item->uid );
		}
	}

	/**
	 * Prepares the upload avatar stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStream
	 * @return
	 */
	public function prepareGroupUpdateCoverStream( &$item, $privacy, $includePrivacy = true )
	{
		// Load the photo
		$photo	= $this->getPhotoFromParams( $item );

		// Load the group
		$group 	= Foundry::group( $item->cluster_id );

		// Get the cover object for the group
		$cover 	= $group->getCoverData();

		$this->set( 'cover'	, $cover );
		$this->set( 'photo' , $photo );
		$this->set( 'actor'	, $item->actor );
		$this->set( 'group'	, $group );

		$item->title 	= parent::display( 'streams/group/upload.cover.title' );
		$item->content	= parent::display( 'streams/group/upload.cover.content' );

		if( $includePrivacy )
		{
			$element	= $item->context;
			$uid		= $item->contextId;

			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, 'core.view', false, $item->uid );
		}
	}

	/**
	 * Prepares the upload cover stream for event.
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function prepareEventUpdateCoverStream(&$item, $privacy, $includePrivacy = true)
	{
		// Load the photo
		$photo = $this->getPhotoFromParams($item);

		// Load the event
		$event = Foundry::event($item->cluster_id);

		// Get the cover object for the event
		$cover = $event->getCoverData();

		$this->set('cover', $cover);
		$this->set('photo', $photo);
		$this->set('actor', $item->actor);
		$this->set('event', $event);

		$item->title = parent::display('streams/event/upload.cover.title');
		$item->content = parent::display('streams/event/upload.cover.content');

		if ($includePrivacy) {
			$element = $item->context;
			$uid = $item->contextId;

			$item->privacy = $privacy->form($uid, $element, $item->actor->id, 'core.view', false, $item->uid);
		}
	}

	/**
	 * Prepares the upload avatar stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStream
	 * @return
	 */
	public function prepareUpdateCoverStream( &$item, $privacy, $includePrivacy = true )
	{
		// Load the photo
		$photo	= $this->getPhotoFromParams( $item );

		// Get the cover object for the group
		$cover 	= $item->actor->getCoverData();

		// There is a possibility that this cover is missing.
		if (!$cover) {
			return;
		}

		// Get the term to be displayed
		$term 	= $this->getGender( $item->actor );

		$this->set( 'cover'	, $cover );
		$this->set( 'photo' , $photo );
		$this->set( 'actor'	, $item->actor );
		$this->set( 'term'	, $term );

		$item->title 	= parent::display( 'streams/user/upload.cover.title' );
		$item->content	= parent::display( 'streams/user/upload.cover.content' );

		if( $includePrivacy )
		{
			$element	= $item->context;
			$uid		= $item->contextId;

			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, 'core.view', false, $item->uid );
		}
	}

	/**
	 * Retrieve the table object from the stream item params
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhotoFromParams( SocialStreamItem &$item , $privacy = null )
	{
		if( count( $item->contextIds ) > 0 && $item->verb != 'uploadAvatar' && $item->verb != 'updateCover' )
		{
			$photos = array();

			// We only want to get a maximum of 5 photos if we have more than 1 photo to show.
			$ids 	= array_reverse( $item->contextIds );

			$i 		= 0;

			foreach( $ids as $id )
			{
				if( $i >= 5 )
				{
					break;
				}

				$photo 	= Foundry::table( 'Photo' );
				$raw 	= isset( $item->contextParams[ $id ] ) ? $item->contextParams[ $id ] : '';

				if( $raw )
				{
					$obj 	= Foundry::json()->decode( $raw );
					$photo->bind( $obj );

					if( !$photo->id )
					{
						$photo->load( $id );
					}
				}
				else
				{
					$photo->load( $id );
				}

				// Determine if the user can view this photo or not.
				if( !$item->cluster_id && $privacy->validate( 'photos.view' , $photo->id, SOCIAL_TYPE_PHOTO , $item->actor->id ) )
				{
					$photos[] = $photo;
				}
				else if( $item->cluster_id )
				{
					$photos[]	= $photo;
				}

				$i++;
			}

			return $photos;
		}

		// Load up the photo object
		$photo	= Foundry::table( 'Photo' );

		// Get the context id.
		$id 	= $item->contextId;
		$raw 	= isset( $item->contextParams[ $id ] ) ? $item->contextParams[ $id ] : '';

		if( $raw )
		{
			$obj 	= Foundry::json()->decode( $raw );
			$photo->bind( $obj );

			if( !$photo->id )
			{
				$photo->load( $id );
			}

			return $photo;
		}

		$photo->load( $id );

		return $photo;
	}

	/**
	 * Processes a saved story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave( &$stream , $streamItem , &$template )
	{
		$photos	= JRequest::getVar( 'photos' );

		// If there's no data, we don't need to do anything here.
		if( empty( $photos ) )
		{
			return;
		}

		if( empty( $template->content ) )
		{
			$template->content 	.= '<br />';
		}


		// Now that we know the saving is successfull, we want to update the state of the photo table.
		foreach( $photos as $photoId )
		{
			$table 	= Foundry::table( 'Photo' );
			$table->load( $photoId );

			$album	= Foundry::table( 'Album' );
			$album->load( $table->album_id );

			$table->state	= SOCIAL_STATE_PUBLISHED;
			$table->store();

			// Determine if there's a cover for this album.
			if( !$album->hasCover() )
			{
				$album->cover_id	= $table->id;
				$album->store();
			}

			$template->content 	.= '<img src="' . $table->getSource( 'thumbnail' ) . '" width="128" />';
		}

		return true;
	}

	/*
	 * Save trigger which is called after really saving the object.
	 */
	public function onAfterSave( &$data )
	{
	    // for now we only support the photo added by person. later on we will support
	    // for groups, events and etc.. the source will determine the type.
	    $source		= isset( $data->source ) ? $data->source : 'people';
	    $actor		= ($source == 'people' ) ? Foundry::get('People', $data->created_by) : '0';

	    // save into activity streams
	    $item   = new StdClass();
	    $item->actor_id 	= $actor->get( 'node_id' );
	    $item->source_type	= $source;
	    $item->source_id 	= $actor->id;
	    $item->context_type = 'photos';
	    $item->context_id 	= $data->id;
	    $item->verb 		= 'upload';
	    $item->target_id 	= $data->album_id;

	    //$item   = get_object_vars($item);
        //Foundry::get('Stream')->addStream( array($item, $item, $item) );
        Foundry::get('Stream')->addStream( $item );
		return true;
	}

	/**
	 * Prepares the photos in the story form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function onPrepareStoryPanel($story)
	{
		if (!$this->config->get('photos.enabled')) {
			return;
		}

		// Get user access
		$access = $this->my->getAccess();

		if (!$access->allowed('photos.create')) {
			return;
		}

		// Create the story plugin
		$plugin = $story->createPlugin("photos", "panel");

		$theme = ES::themes();

		// check max photos upload here.
		if ($access->exceeded('photos.uploader.max', $this->my->getTotalPhotos())) {
			$theme->set('exceeded', JText::sprintf('COM_EASYSOCIAL_PHOTOS_EXCEEDED_MAX_UPLOAD', $access->get('photos.uploader.max')));
		}

		// check max photos upload daily here.
		if ($access->exceeded('photos.uploader.maxdaily', $this->my->getTotalPhotos(true))) {
			$theme->set('exceeded', JText::sprintf('COM_EASYSOCIAL_PHOTOS_EXCEEDED_DAILY_MAX_UPLOAD', $access->get('photos.uploader.maxdaily')));
		}

        $button = $theme->output('site/photos/story/button');
        $form = $theme->output('site/photos/story/form');

       	// Attach the script files
        $script = ES::script();

        $script->set('type', SOCIAL_TYPE_USER);
        $script->set('uid', $this->my->id);
		$script->set('maxFileSize', $access->get('photos.uploader.maxsize') . 'M');
		$scriptFile = $script->output('site/photos/story/plugin');

		$plugin->setHtml($button, $form);
		$plugin->setScript($scriptFile);

		return $plugin;
	}

	/**
	 * Triggers when unlike happens
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeDelete( &$likes )
	{
		if( !$likes->type )
		{
			return;
		}

		// Set the default element.
		$element 	= $likes->type;
		$uid 		= $likes->uid;

		// Get the photo object
		$photo 	= Foundry::table('Photo');

		if ($likes->type == 'stream.user.upload') {
			// $uid is the stream id, not the photo id.
			// Need to find the photo id back from stream table
			$streamItem = Foundry::table('streamitem');
			$streamItem->load(array('uid' => $uid));

			$photoId = $streamItem->context_id;

			$photo->load( $photoId );

			// @points: photos.unlike
			// Deduct points for the current user for unliking this item
			$photo->assignPoints('photos.unlike', Foundry::user()->id);
		}

		if ($likes->type == 'photos.user.create' || $likes->type == 'photos.user.add' || $likes->type == 'photos.user.upload' || $likes->type == 'photos.user.uploadAvatar' || $likes->type == 'photos.user.updateCover') {
			$photo->load($likes->uid);
			$photo->assignPoints('photos.unlike', Foundry::user()->id);
		}
	}

	/**
	 * Retrieves a list of tag recipients on a photo
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getTagRecipients(&$recipients, SocialTablePhoto &$photo, $exclusion = array())
	{
		// Get a list of tagged users
		$tags 	= $photo->getTags(true);

		if (!$tags) {
			return;
		}

		foreach ($tags as $tag) {

			if (!in_array($tag->uid, $exclusion)) {
				$recipients[]	= $tag->uid;
			}

		}
	}

	/**
	 * Triggers after a like is saved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave(&$likes)
	{
		// @legacy
		// photos.user.add should just be photos.user.upload since they are pretty much the same
		$allowed 	= array('photos.user.upload', 'stream.user.upload', 'albums.user.create', 'photos.user.add', 'photos.user.uploadAvatar', 'photos.user.updateCover');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// For likes on albums when user uploads multiple photos within an album
		if ($likes->type == 'albums.user.create') {

			// Since the uid is tied to the album we can get the album object
			$album 	= Foundry::table('Album');
			$album->load($likes->uid);

			// Get the actor of the likes
			$actor	= Foundry::user($likes->created_by);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_PHOTOS_EMAILS_LIKE_ALBUM_ITEM_SUBJECT',
	            'template'  	=> 'apps/user/photos/like.album.item',
	            'permalink' 	=> $album->getPermalink(true, true),
	            'albumTitle'	=> $album->get('title'),
	            'albumPermalink' => $album->getPermalink(false, true),
	            'albumCover'	=> $album->getCover(),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'context_ids'	=> $album->id,
	            'url'           => $album->getPermalink(false, false, 'item', false),
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );


	        // Notify the owner of the photo first
	        if ($likes->created_by != $album->user_id) {
	        	Foundry::notify('likes.item', array($album->user_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, 'albums', 'user', 'create', array(), array($album->user_id, $likes->created_by));

	        $emailOptions['title']      = 'APP_USER_PHOTOS_EMAILS_LIKE_ALBUM_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/user/photos/like.album.involved';

	        // Notify other participating users
	        Foundry::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}

		// For single photo items on the stream
		$allowed 	= array('photos.user.upload', 'stream.user.upload', 'photos.user.add', 'photos.user.uploadAvatar', 'photos.user.updateCover');
		if (in_array($likes->type, $allowed)) {

			// Get the actor of the likes
			$actor	= Foundry::user($likes->created_by);

	        // Set the email options
	        $emailOptions   = array(
	            'template'  	=> 'apps/user/photos/like.photo.item',
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $likes->type,
	            'actor_id'      => $likes->created_by,
	            'uid'           => $likes->uid,
	            'aggregate'     => true
	        );

			// Standard email subject
			$ownerTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PHOTO_ITEM_SUBJECT';
			$involvedTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PHOTO_INVOLVED_SUBJECT';

			// If this item is multiple share on the stream, we need to get the photo id here.
			if ($likes->type == 'stream.user.upload') {

				// Since this item is tied to the stream, we need to load the stream object
				$stream 	= Foundry::table('Stream');
				$stream->load($likes->uid);

				// Get the photo object from the context id of the stream
				$model		= Foundry::model( 'Stream' );
				$origin 	= $model->getContextItem($likes->uid);

				$photo 		= Foundry::table('Photo');
				$photo->load($origin->context_id);

				$systemOptions['context_ids'] = $photo->id;

		        // Get the permalink to the photo
		        $emailOptions['permalink'] = $stream->getPermalink(true, true);
		        $systemOptions['url'] = $stream->getPermalink(false, false, false);

		        $element 	= 'stream';
		        $verb		= 'upload';
			}

			// For single photo items on the stream
			if ($likes->type == 'photos.user.upload' || $likes->type == 'photos.user.add' || $likes->type == 'photos.user.uploadAvatar' || $likes->type == 'photos.user.updateCover') {
				// Get the photo object
				$photo 	= Foundry::table( 'Photo' );
				$photo->load($likes->uid);

				$systemOptions['context_ids'] = $photo->id;

		        // Get the permalink to the photo
		        $emailOptions['permalink'] = $photo->getPermalink(true, true);
		        $systemOptions['url'] = $photo->getPermalink(false, false, 'item', false);

		        $element 	= 'photos';
		        $verb		= 'upload';
			}

			if ($likes->type == 'photos.user.uploadAvatar') {
				$verb 		= 'uploadAvatar';

				$ownerTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PROFILE_PICTURE_ITEM_SUBJECT';
				$involvedTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PROFILE_PICTURE_INVOLVED_SUBJECT';
			}

			if ($likes->type == 'photos.user.updateCover') {
				$verb 		= 'updateCover';

				$ownerTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PROFILE_COVER_ITEM_SUBJECT';
				$involvedTitle 	= 'APP_USER_PHOTOS_EMAILS_LIKE_PROFILE_COVER_INVOLVED_SUBJECT';
			}

			$emailOptions['title'] = $ownerTitle;

			// @points: photos.like
			// Assign points for the author for liking this item
			$photo->assignPoints('photos.like' , $likes->created_by);

	        // Notify the owner of the photo first
	        if ($likes->created_by != $photo->user_id) {
	        	Foundry::notify('likes.item', array($photo->user_id), $emailOptions, $systemOptions);
	        }

	        // Get additional recipients since photos has tag
	        $additionalRecipients 	= array();
	        $this->getTagRecipients($additionalRecipients, $photo);

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($likes->uid, $element, 'user', $verb, $additionalRecipients, array($photo->user_id, $likes->created_by));

	        $emailOptions['title']      = $involvedTitle;
	        $emailOptions['template']   = 'apps/user/photos/like.photo.involved';

	        // Notify other participating users
	        Foundry::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}

	}

	/**
	 * Triggered when a comment save occurs
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed 	= array('photos.user.upload', 'albums.user.create', 'stream.user.upload', 'photos.user.add', 'photos.user.uploadAvatar', 'photos.user.updateCover');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// For likes on albums when user uploads multiple photos within an album
		if ($comment->element == 'albums.user.create') {

			// Since the uid is tied to the album we can get the album object
			$album 	= Foundry::table('Album');
			$album->load($comment->uid);

			// Get the actor of the likes
			$actor	= Foundry::user($comment->created_by);

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'APP_USER_PHOTOS_EMAILS_COMMENT_ALBUM_ITEM_SUBJECT',
	            'template'  	=> 'apps/user/photos/comment.album.item',
	            'permalink' 	=> $album->getPermalink(true, true),
				'comment'		=> $comment->comment,
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true)
	        );

	        $systemOptions  = array(
	            'context_type'  => $comment->element,
	            'context_ids'	=> $comment->uid,
	            'url'           => $album->getPermalink(false, false, 'item', false),
	            'actor_id'      => $comment->created_by,
	            'uid'           => $comment->id,
	            'aggregate'     => true
	        );


	        // Notify the owner of the photo first
	        if ($comment->created_by != $album->user_id) {
	        	Foundry::notify('comments.item', array($album->user_id), $emailOptions, $systemOptions);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($comment->uid, 'albums', 'user', 'create', array(), array($album->user_id, $comment->created_by));

	        $emailOptions['title']      = 'APP_USER_PHOTOS_EMAILS_COMMENT_ALBUM_INVOLVED_SUBJECT';
	        $emailOptions['template']   = 'apps/user/photos/comment.album.involved';

	        // Notify other participating users
	        Foundry::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}

		// For comments made on photos
		$allowed 	= array('photos.user.upload', 'stream.user.upload', 'photos.user.add', 'photos.user.uploadAvatar', 'photos.user.updateCover');
		if (in_array($comment->element, $allowed)) {

			// Get the actor of the likes
			$actor	= Foundry::user($comment->created_by);

	        // Set the email options
	        $emailOptions   = array(
	            'template'  	=> 'apps/user/photos/comment.photo.item',
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'comment'		=> $comment->comment
	        );

	        $systemOptions  = array(
	            'context_type'  => $comment->element,
	            'context_ids'	=> $comment->uid,
	            'actor_id'      => $comment->created_by,
	            'uid'           => $comment->id,
	            'aggregate'     => true
	        );

			// Standard email subject
			$ownerTitle 	= 'APP_USER_PHOTOS_EMAILS_COMMENT_PHOTO_ITEM_SUBJECT';
			$involvedTitle 	= 'APP_USER_PHOTOS_EMAILS_COMMENT_PHOTO_INVOLVED_SUBJECT';

			// If this item is multiple share on the stream, we need to get the photo id here.
			if ($comment->element == 'stream.user.upload') {

				// Since this item is tied to the stream, we need to load the stream object
				$stream 	= Foundry::table('Stream');
				$stream->load($comment->uid);

				// Get the photo object from the context id of the stream
				$model		= Foundry::model( 'Stream' );
				$origin 	= $model->getContextItem($comment->uid);

				$photo 		= Foundry::table('Photo');
				$photo->load($origin->context_id);

		        // Get the permalink to the photo
		        $emailOptions['permalink'] 	= $stream->getPermalink(true, true);
		        $systemOptions['url'] 	= $stream->getPermalink(false, false, false);

		        $element 	= 'stream';
		        $verb		= 'upload';
			}

			// For single photo items on the stream
			if ($comment->element == 'photos.user.upload' || $comment->element == 'photos.user.add' || $comment->element == 'photos.user.uploadAvatar' || $comment->element == 'photos.user.updateCover') {
				// Get the photo object
				$photo 	= Foundry::table( 'Photo' );
				$photo->load($comment->uid);

		        // Get the permalink to the photo
		        $emailOptions['permalink'] 	= $photo->getPermalink(true, true);
		        $systemOptions['url'] 	= $photo->getPermalink(false, false, 'item', false);

		        $element 	= 'photos';
		        $verb		= 'upload';
			}

			if ($comment->element == 'photos.user.uploadAvatar') {
				$verb 		= 'uploadAvatar';

				$ownerTitle		= 'APP_USER_PHOTOS_EMAILS_COMMENT_PROFILE_PICTURE_ITEM_SUBJECT';
				$involvedTitle	= 'APP_USER_PHOTOS_EMAILS_COMMENT_PROFILE_PICTURE_INVOLVED_SUBJECT';
			}

			if ($comment->element == 'photos.user.updateCover') {
				$verb 		= 'updateCover';

				$ownerTitle		= 'APP_USER_PHOTOS_EMAILS_COMMENT_PROFILE_COVER_ITEM_SUBJECT';
				$involvedTitle	= 'APP_USER_PHOTOS_EMAILS_COMMENT_PROFILE_COVER_INVOLVED_SUBJECT';
			}

			$emailOptions['title'] = $ownerTitle;

			// @points: photos.like
			// Assign points for the author for liking this item
			$photo->assignPoints('photos.comment.add' , $comment->created_by);

	        // Notify the owner of the photo first
	        if ($photo->user_id != $comment->created_by) {
	        	Foundry::notify('comments.item', array($photo->user_id), $emailOptions, $systemOptions);
	        }


	        // Get additional recipients since photos has tag
	        $additionalRecipients 	= array();
	        $this->getTagRecipients($additionalRecipients, $photo);

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients     = $this->getStreamNotificationTargets($comment->uid, $element, 'user', $verb, $additionalRecipients, array($photo->user_id, $comment->created_by));

	        $emailOptions['title']      = $involvedTitle;
	        $emailOptions['template']   = 'apps/user/photos/comment.photo.involved';

	        // Notify other participating users
	        Foundry::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

			return;
		}

	}

	private function getUniqueUsers($item, $users, $ownerId)
	{
		// Exclude myself from the list of users.
		$index 			= array_search( Foundry::user()->id , $users );

		if( $index !== false )
		{
			unset( $users[ $index ] );

			$users 	= array_values( $users );
		}

		// Add the author of the photo as the recipient
		if( $item->actor_id != $ownerId )
		{
			$users[]	= $ownerId;
		}

		// Ensure that the values are unique
		$users		= array_unique( $users );
		$users 		= array_values( $users );

		// Exclude the stream creator and the current logged in user from the list.
		if( $users )
		{
			for($i = 0; $i < count( $users ); $i++ )
			{
				if( $users[ $i ] == Foundry::user()->id )
				{
					unset( $users[ $i ] );
				}
			}

			$users 	= array_values( $users );
		}

		return $users;
	}

	/**
	 * Renders the notification item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad( SocialTableNotification &$item )
	{
		$allowed 	= array('comments.item', 'comments.involved', 'likes.item', 'likes.involved', 'photos.tagged',
							'likes.likes' , 'comments.comment.add', 'albums.favourite' );

		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		// When user likes a single photo
		$allowedContexts 	= array('photos.user.upload', 'stream.user.upload', 'photos.user.add', 'albums.user.create', 'photos.user.uploadAvatar', 'photos.user.updateCover');
		if (($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') && in_array($item->context_type, $allowedContexts)) {

			$hook 	= $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

		// When user likes a single photo
		$allowedContexts 	= array('photos.user.upload', 'stream.user.upload', 'photos.user.add', 'albums.user.create', 'photos.user.uploadAvatar', 'photos.user.updateCover');
		if (($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') && in_array($item->context_type, $allowedContexts)) {

			$hook 	= $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		// When user is tagged in a photo
		if ($item->cmd == 'photos.tagged' && $item->context_type == 'tagging') {

			$hook 	= $this->getHook('notification', 'tagging');
			$hook->execute($item);
		}

		// when user favourte an album
		$allowedContexts 	= array('albums.user.favourite');
		if (($item->cmd == 'albums.favourite') && in_array($item->context_type, $allowedContexts)) {

			$hook 	= $this->getHook('notification', 'favourite');
			$hook->execute($item);

			return;
		}


		return;
	}
}
