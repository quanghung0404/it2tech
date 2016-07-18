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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerAlbums extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		// Map the alias methods here.
		$this->registerTask( 'save'		, 'store' );
		$this->registerTask( 'savenew' 	, 'store' );
		$this->registerTask( 'apply'    , 'store' );

		$this->registerTask( 'publish'	, 'togglePublish' );
		$this->registerTask( 'unpublish', 'togglePublish' );

		$this->registerTask( 'activate'		, 'toggleActivation' );
		$this->registerTask( 'deactivate'	, 'toggleActivation' );
	}

	/**
	 * Deletes an album from the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the list of ids
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that the id's are in an array
		$ids 	= FD::makeArray( $ids );

		foreach( $ids as $id )
		{
			$album	= FD::table( 'Album' );
			$album->load( $id );

			$album->delete();

			// @points: photos.albums.delete
			// Deduct points from creator when his album is deleted.
			$album->assignPoints( 'photos.albums.delete' , $album->uid );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALBUMS_ALBUM_DELETED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}
}
