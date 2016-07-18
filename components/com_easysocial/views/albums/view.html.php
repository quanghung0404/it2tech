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
	 * Determines if the photos is enabled.
	 *
	 * @since	1.0
	 * @access	public
	 */
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

	/**
	 * Displays a list of recent albums that the user created.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function display( $tpl = null )
	{
		// Check if photos is enabled
		$this->checkFeature();

		// Check if the current request is made for the current logged in user or another user.
		$uid = $this->input->get('uid', null, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'cmd');

		// If this is a user type, we will want to get a list of albums the current logged in user created
		if ($type == SOCIAL_TYPE_USER && $uid == null) {
			$user 	= FD::user($uid);
			$uid 	= $user->id;
		}

		// Load up the albums library
		$lib = FD::albums($uid, $type);

		// check if this current viewer blocked by the album onwer or not.
		if ($lib->isblocked()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_ALBUMS_INVALID_USER_PROVIDED'));
		}

		// Determine if the node is valid
		$valid 	= $lib->isValidNode();

		// Determines if the viewer is trying to view albums for a valid node.
		if (!$lib->isValidNode()) {
			$this->setMessage($lib->getError(), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Check if the album is viewable
		$viewable = $lib->viewable();

		if (!$viewable) {
			return $this->restricted($lib->uid, $lib->type);
		}

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Set the page title
		$title = $lib->getPageTitle($this->getLayout());
		FD::page()->title($title);

		// Set the breadcrumbs
		$breadcrumbs	= $lib->setBreadcrumbs($this->getLayout());

		// Get albums model
		$model 	= FD::model( 'Albums' );
		$model->initStates();

		// Get the start limit from the request
		$startlimit 	= JRequest::getVar( 'limitstart', '');

		if (!$startlimit) {
			$model->setState( 'limitstart', 0);
		}

		// Get a list of normal albums
		$options 				= array();
		$options['pagination']	= true;
		$options['order'] 		= 'a.assigned_date';
		$options['direction'] 	= 'DESC';
		$options['privacy'] 	= true;

		// Get the albums
		$albums 	= $model->getAlbums($uid, $type, $options);

		// Get the album pagination
		$pagination = $model->getPagination();

		// Format albums by date
		$data	= $lib->groupAlbumsByDate( $albums );

		// Load up the themes now
		$theme	= FD::themes();

		$theme->set( 'lib'			, $lib );
		$theme->set( 'data' 		, $data );
		$theme->set( 'pagination' 	, $pagination );

		// Get the theme output
		$output = $theme->output( 'site/albums/list' );

		// Wrap it with the albums wrapper.
		return $this->output( $lib->uid , $lib->type , $output );
	}

	/**
	 * Displays all albums from the site.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function all($tpl = null)
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if photos is enabled
		$this->checkFeature();

		// Set the page title
		$this->page->title(JText::_('COM_EASYSOCIAL_ALBUMS_ALL_ALBUMS'));

		// Set the breadcrumbs
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_ALBUMS_ALL_ALBUMS'));

		// Get albums model
		$model = ES::model('Albums');
		$model->initStates();

		// Get the start limit from the request
		$startlimit = $this->input->get('limitstart', 0, 'int');

		// By default albums should be sorted by creation date.
		$sorting = $this->input->get('sort', 'created');

		if (!$startlimit) {
			$model->setState('limitstart', 0);
		}

		// Get a list of normal albums
		$options = array(
				'pagination' => true,
				'order' => 'a.assigned_date',
				'direction' => 'DESC',
				'core' => false
			);

		if ($sorting == 'alphabetical') {
			$options['order'] = 'a.title';
			$options['direction'] = 'ASC';
		}

		if ($sorting == 'popular') {
			$options['order'] = 'a.hits';
			$options['direction'] = 'DESC';
		}

		// Get the albums
		$albums = $model->getAlbums('', SOCIAL_TYPE_USER, $options);
		$photos = array();

		// we will get the photos here
		if ($albums) {
			$ids = array();

			foreach($albums as $al) {
				$ids[] = $al->id;
			}

			$pModel = ES::model('Photos');
			$photos = $pModel->getAlbumPhotos($ids, 5);
		}

		// Get the album pagination
		$pagination = $model->getPagination();

		$lib = FD::albums( FD::user()->id , SOCIAL_TYPE_USER );

		$this->set('sorting', $sorting);
		$this->set('lib', $lib);
		$this->set('albums', $albums );
		$this->set('photos', $photos);
		$this->set('pagination', $pagination);


		// Wrap it with the albums wrapper.
		echo parent::display('site/albums/all');
	}

	/**
	 * Displays a restricted page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 */
	public function restricted($uid = null, $type = SOCIAL_TYPE_USER)
	{
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

		echo parent::display( 'site/albums/restricted' );
	}

	/**
	 * If the user is viewing an invalid album.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Optional user id
	 */
	public function deleted()
	{
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getWord( 'type' , SOCIAL_TYPE_USER );

		// Load the albums library
		$albums 	= FD::albums( $uid , $type );

		$this->set( 'lib' , $albums );

		echo parent::display( 'site/albums/deleted' );
	}

	/**
	 * Displays the album item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function item()
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if photos is enabled
		$this->checkFeature();

		// Retrieve the album from request
		$id = $this->input->get('id', 0, 'int');

		// Get the unique id and type
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'string');

		// If id is provided but UID is not provided, probably they created a menu that links to a single album
		if ($id && !$uid) {
			$album = FD::table('Album');
			$album->load($id);

			if (!$album->id) {
				return $this->deleted();
			}

			$uid = $album->uid;
			$type = $album->type;
		}

		if($type == SOCIAL_TYPE_USER && $uid) {
			if (FD::user()->id != $uid) {
				if(FD::user()->isBlockedBy($uid)) {
					return JError::raiseError(404, JText::_('COM_EASYSOCIAL_ALBUMS_INVALID_USER_PROVIDED'));
				}
			}
		}

		// Load up the albums library
		$lib = FD::albums($uid, $type, $id);

		// Determines if the viewer is trying to view albums for a valid node.
		if (!$lib->isValidNode()) {
			$this->setMessage($lib->getError(), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Empty id or invalid id is not allowed.
		if (!$id || !$lib->data->id) {
			return $this->deleted();
		}

		// Check if the album is viewable
		$viewable = $lib->viewable();

		if (!$viewable) {
			return $this->restricted($lib->data->uid, $lib->data->type);
		}

		// Increment the hit of the album
		$lib->data->addHit();

		// // Get a list of photos within this album
		// $photos = $lib->getPhotos($lib->data->id, array('privacy'=>false));
		// $photos = $photos['photos'];

		// NOTE: Add opengraph data for each photos now moved to album libs

		// Set page title
		$title = $lib->getPageTitle($this->getLayout());
		FD::page()->title($title);

		// Set the breadcrumbs
		$lib->setBreadcrumbs($this->getLayout());

		// Render options
		$requiredPrivacy = ($this->my->id == $lib->data->user_id) ? true : false;
		$options = array('viewer' => $this->my->id, 'privacy'=> $requiredPrivacy);

		// Render item
		$output = $lib->renderItem($options);


		return $this->output($uid, $type, $output, $lib->data);
	}

	/**
	 * Renders the album's form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function form()
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if photos is enabled
		$this->checkFeature();

		// Only allow registered users to upload photos
		FD::requireLogin();

		// Get the current user
		$my		= FD::user();

		// Get album id
		$id = JRequest::getInt( 'id', null );

		// Load album library
		$uid = JRequest::getInt('uid');
		$type = JRequest::getWord('type', SOCIAL_TYPE_USER);

		if ($type == SOCIAL_TYPE_USER && !$uid) {
			$uid = $my->id;
		}

		$lib = Foundry::albums($uid, $type, $id);

		// If we are creating an album
		if (!$lib->data->id) {
			// Set the ownership of the album
			$lib->data->uid 	= $lib->uid;
			$lib->data->type 	= $lib->type;

			// Check if we have exceeded album creation limit.
			if ($lib->exceededLimits()) {
				return $this->output( $lib->getExceededHTML() , $lib->data );
			}
		}

		// Set the page title
		$title 	= $lib->getPageTitle($this->getLayout() );
		FD::page()->title( $title );

		// Set the breadcrumbs
		$lib->setBreadcrumbs( $this->getLayout() );

		// Determines if the current user can edit this album
		if ($lib->data->id && !$lib->editable($lib->data)) {
			return $this->restricted($lib->data->uid, $lib->data->type);
		}

		// Render options
		$options = array(
			'viewer'       => $my->id,
			'layout'       => 'form',
			'showStats'    => false,
			'showResponse' => false,
			'showTags'     => false,
			'photoItem' => array(
				'openInPopup' => false
			)
		);

		// Render item
		$output	= $lib->renderItem($options);

		return $this->output($lib->uid, $lib->type, $output, $lib->data);
	}

	/**
	 * Displays the albums a user has
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function output($uid, $type, $content = '', $album = false)
	{
		// Load up the albums library
		$lib = FD::albums($uid, $type, $album ? $album->id : null);

		// If no layout was given, load recent layout
		$layout	= $this->input->get('layout', 'recent', 'cmd');

		// Browser menu
		$id = $this->input->get('id', '', 'int');

		// Load up the model
		$model = FD::model('Albums');

		// Get a list of core albums
		$coreAlbums	= $model->getAlbums($lib->uid , $lib->type , array( 'coreAlbumsOnly' => true ) );

		// Get a list of normal albums
		$options				= array();
		$options['core'] 		= false;
		$options['order'] 		= 'a.assigned_date';
		$options['direction'] 	= 'DESC';
		$options['privacy'] 	= true;


		$albums 	= $model->getAlbums( $lib->uid , $lib->type , $options );

		// Browser frame
		// Get the user alias
		$userAlias 		= '';
		// $userAlias	= $user->getAlias();

		$this->set( 'lib'		, $lib );
		$this->set( 'userAlias'	, $userAlias );
		$this->set( 'id'     	, $id );
		$this->set( 'coreAlbums', $coreAlbums );
		$this->set( 'albums' 	, $albums );
		$this->set( 'content'	, $content );
		$this->set( 'uuid'   	, uniqid() );
		$this->set( 'layout' 	, $layout );

		echo parent::display('site/albums/default');
	}

	/**
	 * Post processing when creating a new album
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $album = null )
	{
		// Require user to be logged in
		FD::requireLogin();

		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			return $this->form();
		}

		return $this->redirect( FRoute::albums( array('id' => $album->getAlias() , 'layout' => 'item' )) );
	}

	/**
	 * Post processing when deleting an album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $link )
	{
		// Require user to be logged in
		FD::requireLogin();

		FD::info()->set( $this->getMessage() );

		$this->redirect( $link );
	}
}
