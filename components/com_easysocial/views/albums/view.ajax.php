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

class EasySocialViewAlbums extends EasySocialSiteView
{
	/**
	 * Displays the exceeded notice
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceeded( SocialAlbums $lib )
	{
		$ajax		= FD::ajax();

		$output 	= $lib->getExceededHTML();


		return $ajax->resolve( $output );
	}

	/**
	 * Displays the restricted page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function restricted($uid = null, $type = SOCIAL_TYPE_USER)
	{
		$ajax = FD::ajax();

		if ($type == SOCIAL_TYPE_USER) {
			$node = FD::user($uid);
		}

		if ($type == SOCIAL_TYPE_GROUP) {
			$node = FD::group($uid);
		}

		$this->set('showProfileHeader', true);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('node', $node);

		$html = $theme->output('site/albums/restricted');
		$ajax->resolve($html);
	}

	public function deleted($uid=null)
	{
		$ajax = FD::ajax();

		$uid  = (!empty($uid)) ? $uid : JRequest::getInt('userid' , null);
		$user = FD::user( $uid );

		$theme = FD::themes();
		$theme->set( 'user'   , $user );
		$html = $theme->output( 'site/albums/deleted' );

		$ajax->resolve( $html );
	}

	/**
	 * Post process after retrieving albums
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbums( $albums , $pagination )
	{
		$ajax 	= FD::ajax();

		$lib 	= FD::albums( FD::user()->id , SOCIAL_TYPE_USER );

		$theme 	= FD::themes();

		$theme->set( 'lib' , $lib );
		$theme->set( 'albums' , $albums );
		$theme->set( 'pagination' , $pagination );

		// Wrap it with the albums wrapper.
		$contents 	= $theme->output( 'site/albums/all.items' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Renders the single album view
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function item()
	{
		$ajax	= FD::ajax();

		// Get the current logged in user
		$my 	= FD::user();

		// Get the album id
		$id 	= JRequest::getInt( 'id' );

		$album 	= FD::table('Album');
		$album->load($id);

		// Empty id or invalid id is not allowed.
		if (!$id || !$album->id) {
			return $this->deleted();
		}

		// Load up the albums library
		$lib 	= FD::albums($album->uid, $album->type, $album->id);

		// Check if the album is viewable
		if (!$lib->viewable()) {
			return $this->restricted($lib->data->uid, $lib->data->type);
		}

		// Get the rendering options
		$options	= JRequest::getVar('renderOptions' , array());

		// Render the album item
		$output 	= $lib->renderItem($options);

		return $ajax->resolve( $output );
	}

	/**
	 * Renders the album form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function form()
	{
		// Only allow registered users to upload photos
		FD::requireLogin();

		$ajax	= FD::ajax();

		// Get current logged in user
		$my		= FD::user();

		// Get album id
		$id		= JRequest::getInt( 'id', null );

		// Get the uid and type from request
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getWord( 'type' , SOCIAL_TYPE_USER );

		// Load up the albums library
		$lib 	= FD::albums( $uid , $type , $id );

		// If we are creating an album
		if ( !$lib->data->id )
		{
			// Check if we have exceeded album creation limit first
			if( $lib->exceededLimits() )
			{
				return $this->exceeded( $lib );
			}

			// Set album ownershipts
			$lib->data->uid 		= $uid;
			$lib->data->type 		= $type;

			// Set album creator to the current logged in user.
			$lib->data->user_id 	= $my->id;
		}

		if( !$lib->editable( $lib->data ) )
		{
			return $this->restricted($lib->data->uid, $lib->data->type);
		}

		// Render options
		$options = array(
			'layout' => 'form',
			'showStats'    => false,
			'showResponse' => false,
			'showTags'     => false,
			'photoItem' => array(
				'openInPopup' => false
			)
		);

		// Render the album item
		$output	= $lib->renderItem( $options );


		return $ajax->resolve( $output );
	}

	/**
	 * Displays the album browser
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function dialog()
	{
		// Only logged in user is allowed here.
		FD::requireLogin();

		// Load the ajax library
		$ajax = FD::ajax();

		// Get the current user.
		$my 		= FD::user();

		// Get the items to be loaded
		$uid 		= JRequest::getInt( 'uid' );
		$type 		= JRequest::getCmd( 'type' );

		// Load up the album library
		$lib 		= FD::albums( $uid , $type );

		// @TODO: Check if the current viewer can really browse items here.
		if( !$lib->allowMediaBrowser() )
		{
			return $ajax->reject();
		}

		// Browser menu
		$id 		= JRequest::getInt( 'id' );

		// Retrieve the albums now.
		$model 		= FD::model( 'Albums' );
		$albums 	= $model->getAlbums( $uid , $type );

		$content	= '<div class="es-content-hint">' . JText::_('COM_EASYSOCIAL_ALBUMS_SELECT_ALBUM_HINT') . '</div>';
		$layout		= "item";


		$theme = FD::themes();

		$theme->set( 'id'     	, $id );
		$theme->set( 'content'	, $content );
		$theme->set( 'albums' 	, $albums );
		$theme->set( 'uuid'   	, uniqid() );
		$theme->set( 'layout' 	, $layout );

		$html = $theme->output( 'site/albums/dialog' );

		return $ajax->resolve( $html );
	}

	/**
	 * Returns a list of likes for this album
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

		$album 	= FD::table( 'Album' );
		$album->load( $id );

		if( !$id || !$album->id )
		{
			return $ajax->reject();
		}

		$theme = FD::themes();
		$theme->set( 'album' , $album );
		$html = $theme->output( 'site/albums/album.response' );

		return $ajax->resolve( $html );
	}

	/**
	 * Retrieves a list of albums
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listItems( $albums )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $albums );
	}

	/**
	 * Returns album object to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbum( $album = null )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $album->export(array('cover', 'photos')) );
	}

	/**
	 * Post processing when creating a new album
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $album = null )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Success message
		$theme 		= FD::themes();
		$theme->set( 'album' , $album );
		$message 	= $theme->output( 'site/albums/message.album.save' );

		// Notify user that the msg is saved
		$ajax->notify($message, SOCIAL_MSG_SUCCESS);

		// Load up the library
		$lib 	= FD::albums( $album->uid , $album->type , $album->id );
		$output = $lib->renderItem();

		return $ajax->resolve( $album->export() , $output );
	}

	/**
	 * Post process after an album is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The state
	 */
	public function delete( $state )
	{
		$ajax = FD::ajax();

		if (!$state) {

			return $ajax->reject( $this->getMessage() );
		}

		$redirect = JRequest::getBool('redirect', 1);

		if ($redirect)
		{
			$url = FRoute::albums();
			return $ajax->redirect( $url );
		}
		else
		{
			return $ajax->resolve();
		}
	}

	/**
	 * Displays a confirmation dialog to delete an album.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$ajax = FD::ajax();

		$id 	= JRequest::getInt( 'id' );

		// Get dialog
		$theme	= FD::themes();
		$theme->set( 'id' , $id );
		$output	= $theme->output( 'site/albums/dialog.delete' );

		return $ajax->resolve( $output );
	}

	public function setCover( $photo = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $photo->export() );
	}

	public function playlist( $photos = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $photos );
	}

	/**
	 * Method to allow caller to load more photos in an album.
	 *
	 * @since	1.2.11
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadMore($photos = array(), $nextStart = 0)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		// Get the current logged in user.
		$my			= FD::user();

		// Get layout
		$layout 	= JRequest::getCmd('layout', 'item');

		$options = array(
			'viewer' => $my->id,
			'layout' => $layout,
			'showResponse' => false,
			'showTags'     => false
		);

		if ($layout=="dialog") {
			$options['showForm'] = false;
			$options['showInfo'] = false;
			$options['showStats'] = false;
			$options['showToolbar'] = false;
		}

		$htmls = array();

		foreach( $photos as $photo )
		{
			$lib 		= FD::photo( $photo->uid , $photo->type , $photo );
			$htmls[]	= $lib->renderItem( $options );
		}

		return $ajax->resolve( $htmls, $nextStart );
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

}
