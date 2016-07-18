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
	private function checkFeature()
	{
		$config	= FD::config();

		// Do not allow user to access photos if it's not enabled
		if( !$config->get( 'photos.enabled' ) )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_PHOTOS_DISABLED' ) , SOCIAL_MSG_ERROR );

			FD::info()->set( $this->getMessage() );
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}
	}

	public function display( $content = '' )
	{
		// Check if photos is enabled
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// See if are viewing another user's album (userid param determines that).
		$uid	= JRequest::getInt('userid' , null);

		// If we viewing another user's albums, load that user.
		// If not, load current logged in user.
		$user	= FD::user( $uid );

		$url 	= FRoute::albums( array( 'userid' => $user->getAlias() ) , false );

		return $this->redirect( $url );
	}

	/**
	 * Responsible to display the restricted page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialPhoto		The photo library
	 * @return
	 */
	public function restricted( SocialPhoto $lib )
	{
		$this->set( 'lib' , $lib );

		echo parent::display( 'site/photos/restricted' );
	}

	/**
	 * Responsible to display a nice message when a photo is already deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function deleted($lib)
	{
		$uid  = (!empty($uid)) ? $uid : JRequest::getInt('userid' , null);
		$user = FD::user( $uid );

		$this->set('lib', $lib);
		$this->set( 'showProfileHeader', true);
		$this->set( 'user'   , $user );

		echo parent::display( 'site/photos/deleted' );
	}

	/**
	 * Displays the photo item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function item()
	{
		// Check if photos is enabled
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get current user
		$my		= FD::user();

		// Get the owner id and type
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getWord( 'type' );

		// Get photo library
		$id		= JRequest::getInt( 'id' , null );

		$lib	= FD::photo( $uid , $type , $id );

		if ($lib->isblocked()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PHOTOS_DELETED'));
		}

		// If id is not given or photo does not exist
		if( !$id || !$lib->data->id )
		{
			return $this->deleted($lib);
		}

		// Set the opengraph data for this photo
		FD::opengraph()->addImage( $lib->data->getSource() );

		// Get the album's library
		$album	= $lib->album();

		// Set the page title.
		$title 	= $lib->getPageTitle( $this->getLayout() );
		FD::page()->title( $title );

		// Set the breadcrumbs
		$lib->setBreadcrumbs( $this->getLayout() );

		// Determines if the photo is viewable or not.
		if( !$lib->viewable() )
		{
			return $this->restricted( $lib );
		}

		// Assign a badge for the user
		$lib->data->assignBadge( 'photos.browse' , $my->id );

		// Render options
		$options = array( 'viewer' => $my->id, 'size' => SOCIAL_PHOTOS_LARGE, 'showNavigation' => true );

		// We want to display the comments.
		$options['showResponse'] 	= true;

		$options['resizeUsingCss'] = false;
		$options['resizeMode'] = 'contain';

		// Render the photo output
		$output 	= $lib->renderItem( $options );

		return $this->output( $lib , $output );
	}

	/**
	 * Responsible to output the contents wrapped within the photo view.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
 	public function output( SocialPhoto $lib , $content = '' )
 	{
 		// Get the current photo table
 		$photo 	= $lib->data;

 		// Determines if the user can really view the photo's from the current album.
 		if( !$lib->albumViewable() )
 		{
 			// If the user can't view the entire album, just show a single photo
 			$photos 	= array( $photo );
 		}
 		else
 		{
 			$photos 	= $lib->getAlbumPhotos( array( 'limit' => 2048 ) );
 		}

 		$this->set( 'id'	, $photo->id );
 		$this->set( 'album'	, $lib->albumLib->data );
		$this->set( 'photos' , $photos );
		$this->set( 'lib'	, $lib );
		$this->set( 'content', $content );
		$this->set( 'uuid'   , uniqid() );

		echo parent::display( 'site/photos/default' );
 	}


	/**
	 * Displays the photo form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function form()
	{
		// Only logged in users are allowed to modify photos.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if photos is enabled
		$this->checkFeature();

		// Get current user
		$my		= FD::user();

		// Get photo
		$id		= JRequest::getInt( 'id' , null );

		// Load the photo table
		$table 	= FD::table( 'Photo' );
		$table->load( $id );

		// If id is not given or photo does not exist
		if( !$id || !$table->id )
		{
			return $this->deleted($lib);
		}

		// Load up the library photo library
		$lib 	= FD::photo( $table->uid , $table->type , $table );

		// Check if the person is allowed to edit the photo
		if( !$lib->editable() )
		{
			return $this->restricted( $lib );
		}

		// Set the page title.
		$title 	= $lib->getPageTitle( $this->getLayout() );
		FD::page()->title( $title );

		// Set the breadcrumbs
		$lib->setBreadcrumbs( $this->getLayout() );

		// Render options
		$options = array( 'size' => 'large', 'showForm' => true , 'layout' => 'form');

		// Render item
		$output 	= $lib->renderItem( $options );

		return $this->output( $lib , $output );
	}

	/**
	 * Allows use to download a photo from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Check if photos is enabled
		$this->checkFeature();

		// Load up info object
		$info 	= FD::info();

		// Get the id of the photo
		$id 	= JRequest::getInt( 'id' );
		$photo	= FD::table( 'Photo' );
		$photo->load($id);

		// Id provided must be valid
		if( !$id || !$photo->id )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			$info->set( $this->getMessage() );

			return $this->redirect( FRoute::albums( array() , false ) );
		}

		// Load up photo library
		$lib 	= FD::photo($photo->uid , $photo->type , $photo);

		if( !$lib->downloadable() )
		{
			return $this->restricted( $lib );
		}

		// Let's try to download the file now
		$photo->download();
	}
}
