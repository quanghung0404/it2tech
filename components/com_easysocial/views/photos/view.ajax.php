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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewPhotos extends EasySocialSiteView
{

	/**
	 * Renders a photo item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function item()
	{
		// Load up the ajax library
		$ajax = FD::ajax();

		// Get current user
		$my = FD::user();

		// Get photo object
		$id = $this->input->get('id', 0, 'int');
		$table = FD::table('Photo');
		$table->load($id);

		// If id is not given or photo does not exist
		if (!$id || !$table->id) {
			return $this->deleted();
		}

		// Load up photo library
		$lib = FD::photo($table->uid, $table->type, $table->id);

		// Check if the album is viewable
		if (!$lib->viewable()) {
			return $this->restricted($lib);
		}

		// Assign a badge for the user
		$lib->data->assignBadge('photos.browse' , $my->id);

		// Render options
		$options = array('viewer' => $my->id, 'size' => SOCIAL_PHOTOS_LARGE, 'showNavigation' => true );

		// If the viewer is a guest, we do not want to show the toolbar
		// if( $my->id == 0 )
		// {
		// 	$options['showToolbar'] 	= false;
		// }

		// We want to display the comments.
		$options['showResponse'] = true;

		// Determine resizing method
		$options['resizeUsingCss'] = false;
		$options['resizeMode'] = 'contain';

		$popup = JRequest::getBool('popup', false);
		if ($popup) {
			$options['template'] = 'site/photos/popup/item';
		}

		// Render the photo output
		$output 	= $lib->renderItem( $options );

		// Wrap the content in a photo browser if required
		$browser = JRequest::getInt( 'browser' , null );

		if( $browser )
		{
			$output		= $this->renderBrowser( $output );
		}

		return $ajax->resolve( $output );
	}

	/**
	 * Renders the html wrapper for photos
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function renderBrowser( $content = '' )
	{
		// Get the ajax library
		$ajax		= FD::ajax();

		// Get current photo
		$id			= JRequest::getInt( 'id' , null );
		$photo		= FD::table( 'photo' );
		$photo->load( $id );

		// Load up photo's library
		$lib 		= FD::photo( $photo->uid , $photo->type , $photo );

		// If the photo id is invalid, throw deleted
		if( !$id || !$photo->id )
		{
			$this->deleted();
		}

		// Test if the user can view the entire album
		$photos 	= array( $photo );

		if( $lib->albumViewable() )
		{
			$photos 	= $lib->getAlbumPhotos( array( 'limit' => 2048 ) );
		}

		// Generate photo browser template
		$theme 		= FD::themes();

		$theme->set( 'id'	, $photo->id );
		$theme->set( 'album', $lib->albumLib->data );
		$theme->set( 'photos' , $photos );
		$theme->set( 'lib' , $lib );
		$theme->set( 'heading' , false );
		$theme->set( 'content' , $content );
		$theme->set( 'uuid'   , uniqid() );

		return $theme->output( 'site/photos/default' );
	}

	/**
	 * Responsible to display the restricted area
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialPhoto		The photo library
	 * @return
	 */
	public function restricted( SocialPhoto $lib )
	{
		// Get the ajax library
		$ajax	= FD::ajax();

		// Load up the themes
		$theme	= FD::themes();
		$theme->set( 'lib' , $lib );

		$output 	= $theme->output( 'site/photos/restricted' );

		return $ajax->resolve( $output );
	}

	/**
	 * Responsible to display the deleted / missing notice
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialPhoto	The photo library
	 * @return
	 */
	public function deleted( SocialPhoto $lib )
	{
		// Get the ajax library
		$ajax	= FD::ajax();

		// Load up the themes
		$theme	= FD::themes();
		$theme->set( 'lib' , $lib );

		$output 	= $theme->output( 'site/photos/deleted' );

		return $ajax->resolve( $output );
	}

	/**
	 * Post process to retrieve photo data
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhoto( $photo = null , $attributes = array() )
	{
		// Get the ajax library
		$ajax 	= FD::ajax();

		// If the controller throws errors, send the appropriate response type
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Export the photo data
		$data 	= $photo->export();

		// Decorate with additional attributes
		if( in_array( 'content', $attributes ) )
		{
			// Load the album
			$album		= FD::table( 'album' );
			$album->load( $photo->album_id );

			// Get the creator of the photo
			$creator	= FD::user( $photo->user_id );

			$theme 		= FD::themes();
			$theme->set( 'creator', $creator );
			$theme->set( 'photo'  , $photo );
			$theme->set( 'album'  , $album );

			$data[ 'content' ][ 'inline' ]	= $theme->output( 'site/photos/content' );
			$data[ 'content' ][ 'popup' ]	= $theme->output( 'site/photos/content' );
		}

		// Determines if we should render the tags as well
		if( in_array( 'tags', $attributes ) )
		{
			$data[ 'tags' ]	= $photo->getTags();
		}

		return $ajax->resolve( $data );
	}

	/**
	 * Post processing after a photo is unliked
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unlike( $photoId )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$like 	= FD::likes( $photoId , SOCIAL_TYPE_PHOTO, 'upload', SOCIAL_APPS_GROUP_USER );

		$obj 		= new stdClass();
		$obj->state	= false;
		$obj->count	= $like->getCount();
		$obj->html 	= $like->toString();

		return $ajax->resolve( $obj );
	}

	/**
	 * Post processing after a photo is liked
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function like( $photoId )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$like 	= FD::likes( $photoId , SOCIAL_TYPE_PHOTO, 'upload', SOCIAL_APPS_GROUP_USER );

		$obj 		= new stdClass();
		$obj->state	= true;
		$obj->count	= $like->getCount();
		$obj->html 	= $like->toString();

		return $ajax->resolve( $obj );
	}

	/**
	 * Returns a list of likes for this photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function response()
	{
		$ajax = FD::ajax();

		// Get id from request.
		$id 	= JRequest::getInt( 'id' );

		$photo 	= FD::table( 'Photo' );
		$photo->load( $id );

		if( !$id || !$photo->id )
		{
			return $ajax->reject();
		}

		$theme = FD::themes();
		$theme->set( 'photo' , $photo );
		$html = $theme->output( 'site/albums/photo.response' );

		return $ajax->resolve( $html );
	}

	/**
	 * Post process after a photo has been featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function feature( $isFeatured = false )
	{
		$ajax 	= FD::ajax();

		if( $isFeatured )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_PHOTO_FEATURED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		}
		else
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_PHOTO_UNFEATURED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		}


		return $ajax->resolve( $this->getMessage() , $isFeatured );
	}

	/**
	 * Processes after an item is marked as featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toggleFeatured()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	public function confirmDelete()
	{
		$ajax = FD::ajax();

		// Get dialog
		$theme = FD::themes();
		$html = $theme->output( 'site/photos/dialog.delete' );

		return $ajax->resolve( $html );
	}

	/**
	 * Post processing after a tag is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteTag( )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( );
	}

	/**
	 * Post processing after a photo is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $newCover = false )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		if( $newCover )
		{
			$ajax->setCover( $newCover->export() );
		}

		return $ajax->resolve();
	}

	public function move()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Displays the move to album dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moveToAnotherAlbum()
	{
		$ajax = FD::ajax();

		// Get the current user.
		$my		= FD::user();

		// Get photo
		$id		= JRequest::getInt( 'id' );
		$photo	= FD::table( 'photo' );
		$photo->load( $id );

		// If photo id is invalid, reject this.
		if (!$photo->id || !$id) {
			return $ajax->reject();
		}

		// Load up the photo lib
		$lib 	= FD::photo($photo->uid, $photo->type, $photo);

		// Check if the user is really allowed to move the photo
		if (!$lib->canMovePhoto()) {
			return $ajax->reject();
		}

		// Get albums
		$model	= FD::model( 'Albums' );
		$albums	= $model->getAlbums($my->id, SOCIAL_TYPE_USER, array('exclusion' => $photo->album_id, 'core' => false));


		// Get dialog
		$theme = FD::themes();
		$theme->set( 'albums', $albums );
		$theme->set( 'photo' , $photo );
		$html = $theme->output( 'site/photos/dialog.move' );

		return $ajax->resolve( $html );
	}

	/**
	 * Returns a list of tags for a particular photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags( $tags )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getErrors() );
		}

		return $ajax->resolve( $tags );
	}

	/**
	 * Processes after storing a tag object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createTag( $tag, $photo )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$tags = $photo->getTags();
		$comma = (count($tags) > 1 ) ? true : false;

		$theme = FD::themes();
		$theme->set('tag'  , $tag);
		$theme->set('photo', $photo);
		$theme->set('comma', $comma);
		$tagItem = $theme->output('site/photos/tags.item');
		$tagListItem = $theme->output('site/photos/taglist.item');
		$infoTagListItem = $theme->output('site/photos/info.taglist.item');

		return $ajax->resolve( $tag, $tagItem, $tagListItem, $infoTagListItem );
	}

	/**
	 * Post processing after creating an avatar from a photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createAvatar( $photo = null )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$my 		= FD::user();

		$userObj 	= new stdClass();
		$userObj->avatars 	= new stdClass();

		// Initialize values
		$userObj->avatars->small	= $my->getAvatar( SOCIAL_AVATAR_SMALL );
		$userObj->avatars->medium 	= $my->getAvatar( SOCIAL_AVATAR_MEDIUM );
		$userObj->avatars->large 	= $my->getAvatar( SOCIAL_AVATAR_LARGE );
		$userObj->avatars->square 	= $my->getAvatar( SOCIAL_AVATAR_SQUARE );

		$photoObj 	= (object) $photo;

		return $ajax->resolve($photoObj, $userObj, $my->getPermalink( false ) );
	}

	/**
	 * Post processing after a photo is rotated.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTablePhoto	The photo table.
	 * @param	Array				The paths.
	 * @return
	 */
	public function rotate($photo, $paths)
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$result = $photo->getTags();
		$tags 	= array();

		if( $result )
		{
			foreach( $result as $row )
			{
				$obj 		= new stdClass();

				$obj->id		= $row->id;
				$obj->width		= $row->width;
				$obj->height	= $row->height;
				$obj->left 		= $row->left;
				$obj->top 		= $row->top;

				$tags[]	= $obj;
			}
		}

		return $ajax->resolve( $photo->export() , $tags );
	}

	public function reorder()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	public function thumbnails()
	{
		$ajax = FD::ajax();

		$albumId = JRequest::getInt('albumId');
		$photoId = JRequest::getInt('photoId');

		if( !$albumId )
		{
			$ajax->reject('Invalid album id provided', SOCIAL_MSG_ERROR);
		}

		$album = FD::table('album');
		$album->load($albumId);

		$photos = $album->getPhotos();
		$photos = $photos["photos"];

		$theme = FD::themes();
		$theme->set('album', $album);
		$theme->set('photos', $photos);
		$theme->set('photoId', $photoId);

		$html = $theme->output('site/photos/thumbnails');

		return $ajax->resolve($html);
	}

	/**
	 * Displays the side bar of the photo in the popup
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function content()
	{
		$ajax = FD::ajax();

		$id = JRequest::getInt('id');

		if( !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
		}

		$photo = FD::table( 'photo' );
		$photo->load( $id );

		$album = FD::table( 'album' );
		$album->load( $photo->album_id );

		$theme = FD::themes();
		$theme->set( 'photo', $photo );
		$theme->set( 'album', $album );
		$theme->set( 'photos', $album->getPhotos() );

		$type = JRequest::getCmd('type', 'single');

		$html = $theme->output('site/photos/content');

		return $ajax->resolve($html);
	}

	/**
	 * Post process after a photo is saved
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function update( $photo )
	{
		$ajax	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$data	= $photo->export();

		$user 	= FD::user( $photo->uid );

		$theme	= FD::themes();
		$theme->set( 'userAlias' , $user->getAlias() );
		$theme->set( 'photo', $photo );
		$info	= $theme->output( 'site/photos/info' );

		return $ajax->resolve( $data, $info );
	}

}
