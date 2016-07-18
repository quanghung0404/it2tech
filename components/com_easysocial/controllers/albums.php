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

// Import main controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerAlbums extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'create'	, 'store' );
		$this->registerTask( 'update'	, 'store' );
	}

	/**
	 * Retrieves a play list for an album
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function playlist()
	{
		// Check for request forgeries
		// FD::checkToken();

		// Only registered users are allowed to like an album
		// FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Retrieve the album data
		$id		= JRequest::getInt( 'albumId' );
		$album	= FD::table( 'Album' );
		$album->load( $id );

		if( !$id || !$album->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Photos' );
		$rows	= $model->getPhotos(array('album_id' => $album->id, 'pagination' => false));

		// Get the current user's privacy
		$my 		= FD::user();
		$privacy 	= $my->getPrivacy();
		$photos 	= array();

		foreach ($rows as $row) {
			if ($row->viewable()) {
				$photos[]	= $row;
			}
		}

		return $view->call( __FUNCTION__ , $photos);
	}

	/**
	 * Custom implementation of likes for albums
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function like()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered users are allowed to like an album
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the album id.
		$id 	= JRequest::getInt( 'id' );

		// Load up album
		$album 	= FD::table( 'Album' );
		$album->load( $id );

		if( !$id || !$album->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current logged in user.
		$my 	= FD::user();

		// Load up likes library
		$likes 	= FD::get( 'Likes' );
		$isLike = false;

		if( $likes->hasLiked( $album->id , SOCIAL_TYPE_ALBUM , $my->id ) )
		{
			$state 	= $likes->delete( $album->id , SOCIAL_TYPE_ALBUM , $my->id );
		}
		else
		{
			$isLike = true;
			$state 	= $likes->add( $album->id , SOCIAL_TYPE_ALBUM , $my->id );
		}

		if( $state === false )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_ERROR_SAVING_LIKES' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$method 	= $isLike ? __FUNCTION__ : 'unlike';

		return $view->call( $method , $state );
	}

	/**
	 * Custom implementation of favourite for albums
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function favourite()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered users are allowed to like an album
		FD::requireLogin();

		// Get the album id.
		$id = JRequest::getInt( 'id' );

		// Load up album
		$album = FD::table('Album');
		$album->load($id);

		if (!$id || !$album->id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		$model = FD::model('Albums');
		$exists = $model->isFavourite($album->id, $this->my->id);

		// If already favourited, unfavourite it
		if ($exists) {
			$state = $model->removeFavourite($album->id, $this->my->id);
		} else {
			$state = $model->addFavourite($album->id, $this->my->id);

			if ($this->my->id != $album->user_id) {
				// Email template
				$emailOptions = array(
										'actor'		=> $this->my->getName(),
										'title'		=> 'COM_EASYSOCIAL_EMAILS_ALBUM_FAVOURITE_SUBJECT',
										'template'	=> 'site/albums/new.favourite',
										'permalink' 	=> $album->getPermalink(true, true),
										'albumTitle'	=> $album->get('title'),
										'albumPermalink' => $album->getPermalink(false, true),
										'albumCover'	=> $album->getCover(),
										'actorAvatar'   => $this->my->getAvatar(SOCIAL_AVATAR_SQUARE),
										'actorLink'     => $this->my->getPermalink(true, true)
								);

				$systemOptions = array(
									    'context_type'  => 'albums.user.favourite',
									    'context_ids'	=> $album->id,
									    'url'           => $album->getPermalink(false, false, 'item', false),
									    'actor_id'      => $this->my->id,
									    'uid'           => $album->id,
									    'aggregate'     => true
									);

				FD::notify('albums.favourite', array($album->user_id), $emailOptions, $systemOptions);

			}

		}

		if ($state === false) {
			$this->view->setMessage(JText::_( 'COM_EASYSOCIAL_ALBUMS_ERROR_SAVING_FAVOURITE'), SOCIAL_MSG_ERROR);
			return $this->view->call( __FUNCTION__ );
		}

		return $this->view->call( __FUNCTION__, $state );
	}

	/**
	 * Retrieves a list of albums a user owns
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listItems()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the current user.
		$my 	= FD::user();

		$model 	= FD::model( 'Albums' );
		$albums	= $model->getAlbums( $my->id , SOCIAL_TYPE_USER );

		return $view->call( __FUNCTION__ , $albums );
	}

	/**
	 * Retrieve a list of albums on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbums()
	{
		// Check for request forgeries
		FD::checkToken();

		// To get the referer url for sorting purpose
		$callback = FRoute::referer();

		// Get the current view.
		$view = $this->getCurrentView();

		// Default sorting value
		$ordering = $this->input->get('sort', 'created', 'cmd');

		// Sort items
		if ($callback) {
			$callback = parse_url($callback);
			parse_str($callback['query'], $query);
			$ordering = $query['sort'];
		}

		// Get a list of normal albums
		$options = array(
				'pagination' => true,
				'order' => 'a.assigned_date',
				'direction' => 'DESC',
				'core' => false
			);

		if ($ordering == 'alphabetical') {
			$options['order'] = 'a.title';
			$options['direction'] = 'ASC';
		}

		if ($ordering == 'popular') {
			$options['order'] = 'a.hits';
			$options['direction'] = 'DESC';
		}

		$model = FD::model('Albums');
		$model->initStates();
		$albums = $model->getAlbums('' , SOCIAL_TYPE_USER , $options);

		$pagination = $model->getPagination();
		$pagination->setVar('view', 'albums');
		$pagination->setVar('layout', 'all');
		$pagination->setVar('ordering', $ordering);

		return $view->call(__FUNCTION__, $albums, $pagination);
	}

	/**
	 * Retrieve album object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbum()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view 	= $this->getCurrentView();

		$id = JRequest::getInt( 'id', 0 );

		if( $id === 0 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Load the album object
		$album = FD::table( 'Album' );
		$album->load( $id );

		return $view->call( __FUNCTION__, $album );
	}

	/**
	 * Creating of new albums
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the uid and type
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getWord( 'type' , SOCIAL_TYPE_USER );

		// Get the current logged in user.
		$my 	= FD::user();

		// Get the data from request.
		$post 	= JRequest::get( 'post' );

		// Load the album
		$album	= FD::table( 'Album' );
		$album->load( $post[ 'id' ] );

		// Determine if this item is a new item
		$isNew 	= true;

		if( $album->id )
		{
			$isNew = false;
		}

		// Load the album's library
		$lib 	= FD::albums( $uid , $type );

		// Check if the person is allowed to create albums
		if( $isNew && !$lib->canCreateAlbums() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_ACCESS_NOT_ALLOWED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Set the album uid and type
		$album->uid 			= $uid;
		$album->type 			= $type;

		// Determine if the user has already exceeded the album creation
		if( $isNew && $lib->exceededLimits() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_ACCESS_EXCEEDED_LIMIT' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Set the album creation alias
		$album->assigned_date 	= FD::date()->toMySQL();

		// Set custom date
		if( isset( $post['date'] ) )
		{
			$album->assigned_date 	= $post[ 'date' ];

			unset( $post['date'] );
		}

		// Map the remaining post data with the album.
		$album->bind( $post );

		// Set the user creator
		$album->user_id		= $my->id;

		// Try to store the album
		$state 			= $album->store();

		// Throw error when there's an error saving album
		if( !$state )
		{
			$view->setMessage( $album->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Detect for location
		$address 	= JRequest::getVar( 'address' , '' );
		$latitude 	= JRequest::getVar( 'latitude' , '' );
		$longitude	= JRequest::getVar( 'longitude' , '' );

		if( !empty( $address ) && !empty( $latitude) && !empty( $longitude ) )
		{
			$location = FD::table('Location');
			$location->load(array('uid' => $album->id, 'type' => SOCIAL_TYPE_ALBUM));

			$location->uid = $album->id;
			$location->type = SOCIAL_TYPE_ALBUM;
			$location->user_id = $my->id;
			$location->address = $address;
			$location->longitude = $longitude;
			$location->latitude = $latitude;

			$location->store();
		}

		// Set the privacy for the album
		$privacy 		= JRequest::getWord( 'privacy' );
		$customPrivacy  = JRequest::getString( 'privacyCustom', '' );

		// Set the privacy through our library
		$lib->setPrivacy( $privacy , $customPrivacy );


		$albumPhotos = array();

		if( isset( $post['photos'] ) )
		{

			// Save individual photos
			foreach( $post['photos'] as $photo )
			{
				$photo		= (object) $photo;

				// Load the photo object
				$photoTable	= FD::table( 'photo' );
				$photoTable->load( $photo->id );

				$photoTable->album_id	= $album->id;
				$photoTable->title 		= $photo->title;
				$photoTable->caption	= $photo->caption;

				if( isset( $post['ordering'] ) && isset( $post['ordering'][$photo->id] ) )
				{
					$photoTable->ordering = $post['ordering'][$photo->id];
				}

				if( isset( $photo->date ) && !empty( $photo->date ) )
				{
					$photoTable->assigned_date 	= FD::date( $photo->date )->toMySQL();
				}

				// Throw error when there's an error saving photo
				if( !$photoTable->store() )
				{
					$view->setMessage( $photoTable->getError(), SOCIAL_MSG_ERROR );

					return $view->call( __FUNCTION__ );
				}

				// Add stream item for the photos.
				$createStream 	= JRequest::getBool( 'createStream' );

				$streamExist = FD::stream()->exists($photoTable->id, 'photos', 'create', $my->id);

				if ($createStream && !$streamExist) {
					$photoTable->addPhotosStream('create');
				}

				// Store / update photo location when necessary
				if( !empty( $photo->address ) && !empty( $photo->latitude ) && !empty( $photo->longitude ) )
				{
					$location = FD::table('Location');
					$location->load(array('uid' => $photo->id, 'type' => SOCIAL_TYPE_PHOTO));

					$location->uid = $photo->id;
					$location->type = SOCIAL_TYPE_PHOTO;
					$location->user_id = $my->id;
					$location->address = $photo->address;
					$location->latitude = $photo->latitude;
					$location->longitude = $photo->longitude;

					$location->store();
				}

				$albumPhotos[] = $photoTable;
			}
		}

		// Assign the photos back to the album object
		if( !empty( $albumPhotos ) )
		{
			$album->photos = $albumPhotos;
		}

		return $view->call( __FUNCTION__ , $album );
	}

	/**
	 * Delete albums
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the id of the album
		$id 	= JRequest::getInt( 'id' );

		// Get the current view
		$view	= $this->getCurrentView();

		// Load the album object
		$album 	= FD::table( 'Album' );
		$album->load( $id );

		if( !$id || !$album->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Load up albums library
		$lib 	= FD::albums( $album->uid , $album->type , $album->id );

		// Checks if the user can delete
		if( !$lib->deleteable() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_NO_PERMISSIONS_TO_DELETE_ALBUM' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false);
		}

		// Try to delete the album
		$state 	= $album->delete();

		if( !$state )
		{
			$view->setMessage( $album->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false);
		}

		// @points: photos.albums.delete
		// Deduct points from creator when his album is deleted.
		$album->assignPoints( 'photos.albums.delete' , $album->uid );

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_ALBUM_DELETED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $lib->getViewAlbumsLink( false ) );
	}

	/**
	 * Reordering of albums.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function reorder()
	{
		// Check for request forgeries
		FD::checkToken();

		$this->getCurrentView()->call( __FUNCTION__ );
	}

	/**
	 * Allows caller to set a cover photo for the album
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function setCover()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only users that is logged in is allowed
		FD::requireLogin();

		// Get the current view
		$view	= $this->getCurrentView();

		// Load up the album table
		$id 		= JRequest::getInt( 'albumId' );
		$album 		= FD::table( 'Album' );
		$album->load( $id );

		if( !$id || !$album->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Load up the album library
		$lib 	= FD::albums( $album->uid , $album->type , $album );

		// Check if the person is allowed to set a cover album
		if( !$lib->canSetCover() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_NOT_ALLOWED_TO_SET_COVER' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Get the object to be used as the album's cover
		$photoId 	= JRequest::getInt( 'coverId' );
		$photo 		= FD::table( 'Photo' );
		$photo->load( $photoId );

		if( !$photoId || !$photo->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_INVALID_COVER_ID' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Check if the photo is within the same album
		if( $photo->album_id != $album->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTO_NOT_IN_THIS_ALBUM' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Set the new cover id
		$album->cover_id = $photo->id;

		// Try to save the album
		$result = $album->store();

		if( !$result )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_UNABLE_TO_SAVE_COVER_ID' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		return $view->call( __FUNCTION__, $photo );
	}


	/**
	 * Displays paginated photos within an album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadMore()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get params
		$id = $this->input->get('albumId', 0, 'int');
		$start = $this->input->get('start', 0, 'int');

		if ($start == '-1') {
			return $view->call(__FUNCTION__, '', $start);
		}

		// Load up the album
		$album = ES::table("Album");
		$album->load($id);

		// If the album id is invalid, we should skip this
		if (!$id || !$album->id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_ALBUMS_INVALID_ALBUM_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Load up our albums library
		$lib = FD::albums($album->uid, $album->type, $album);
		$respectPrivacy = true;

		// Privacy should only be respected when the album type is a user type
		if ($album->type != SOCIAL_TYPE_USER) {
			$respectPrivacy = false;
		}

		$result = $lib->getPhotos($album->id, array('start' => $start, 'privacy' => $respectPrivacy));

		return $this->view->call(__FUNCTION__, $result['photos'], $result['nextStart']);
	}
}
