<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerBadges extends EasySocialController
{
	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'save' , 'store' );
		$this->registerTask( 'apply' , 'store' );

		$this->registerTask( 'publish' 	, 'togglePublish' );
		$this->registerTask( 'unpublish', 'togglePublish' );
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get ids from request
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that they are in an array form.
		$ids 	= FD::makeArray( $ids );

		// Get the current view
		$view 	= $this->getCurrentView();

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_BADGE_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$badge 	= FD::table( 'Badge' );
			$badge->load( $id );

			$badge->delete();
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_BADGES_DELETED' );

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $task );
	}

	/**
	 * Mass assign points for users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function massAssign()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 		= $this->getCurrentView();

		// Get the file from the request
		$file 		= JRequest::getVar( 'package' , '' , 'FILES');

		// Format the csv data now.
		$data		= FD::parseCSV( $file[ 'tmp_name' ] , false , false );

		if( !$data )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_CSV_FILE' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Load up the points library
		$badges 	= FD::badges();

		// Collect the list of failed and successfull items
		$failed 	= array();
		$success 	= array();

		foreach( $data as $row )
		{
			$userId 		= isset( $row[ 0 ] ) ? $row[ 0 ] : false;
			$badgeId 		= isset( $row[ 1 ] ) ? $row[ 1 ] : false;
			$dateAchieved	= isset( $row[ 2 ] ) ? $row[ 2 ] : false;
			$message 		= isset( $row[ 3 ] ) ? $row[ 3 ] : false;
			$publishStream 	= isset( $row[ 4 ] ) && $row[ 4 ] == 1 ? true : false;

			$obj 			= (object) $row;

			$badge 	= FD::table( 'Badge' );
			$badge->load( $badgeId );

			// Skip this
			if( !$userId || !$badgeId || !$badge->id )
			{
				$failed[]	= $obj;
				continue;
			}

			$user 	= FD::user( $userId );

			$badges->create( $badge , $user , $message , $dateAchieved );

			if( $publishStream )
			{
				$badges->addStream( $badge , $user->id );
			}

			$success[]	= $obj;
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_CSV_FILE_PARSED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $success , $failed );
	}

	/**
	 * Toggles the publish state for the badges
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get ids from request
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that they are in an array form.
		$ids 	= FD::makeArray( $ids );

		// Get the current view
		$view 	= $this->getCurrentView();

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_BADGE_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the current task
		$task 	= $this->getTask();

		foreach( $ids as $id )
		{
			$badge 	= FD::table( 'Badge' );
			$badge->load( $id );

			$badge->$task();
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_BADGES_PUBLISHED' );

		if( $task == 'unpublish' )
		{
			$message 	= JText::_( 'COM_EASYSOCIAL_BADGES_UNPUBLISHED' );
		}

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $task );
	}

	/**
	 * Saves a badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the badge id from the request
		$id 	= JRequest::getInt( 'id' );

		// Get the current view
		$view 	= $this->getCurrentView();

		// Try to load the badge now.
		$badge 	= FD::table( 'Badge' );
		$badge->load( $id );

		if( !$id || !$badge->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_BADGE_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the posted data.
		$post 	= JRequest::get( 'POST' );
		$badge->bind( $post );

		// Try to store the badge now
		$state 	= $badge->store();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_BADGES_UPDATED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $this->getTask() , $badge );
	}

	/**
	 * Processes the uploaded rule file.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function upload()
	{
		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'package' , '' , 'FILES');

		$state = $this->installPackage( $file, 'badges', array( 'zip', 'badge', 'badges' ) );

		$view = $this->getCurrentView();

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Discover .points files from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function discoverFiles()
	{
		FD::checkToken();

		// Retrieve the view.
		$view 	= FD::view( 'Badges' );

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Badges' );

		// Get the list of paths that may store points
		$config = FD::config();
		$paths 	= $config->get( 'badges.paths' );

		// Result set.
		$files	= array();

		foreach( $paths as $path )
		{
			$data 	= $model->scan( $path );

			foreach( $data as $file )
			{
				$files[]	= $file;
			}
		}

		// Return the data back to the view.
		return $view->call( __FUNCTION__ , $files );
	}

	/**
	 * Scans for rules throughout the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function scan()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the allowed rule scan sections
		$config		= FD::config();

		// Retrieve info lib.
		$info 		= FD::info();

		// Retrieve the view.
		$view 		= $this->getCurrentView();

		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'file' , '' );

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Badges' );

		$obj 			= new stdClass();

		// Format the output to display the relative path.
		$obj->file		= str_ireplace( JPATH_ROOT , '' , $file );
		$obj->rules 	= $model->install( $file );

		return $view->call( __FUNCTION__ , $obj );
	}
}
