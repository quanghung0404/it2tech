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

class EasySocialControllerPoints extends EasySocialController
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

		// Register aliases.
		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'unpublish' , 'publish' );
	}

	/**
	 * Deletes a list of provided points
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		$ids 	= JRequest::getVar( 'cid' );
		$ids 	= FD::makeArray( $ids );

		$view 	= $this->getCurrentView();

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$point 	= FD::table( 'Points' );
			$point->load( $id );

			$point->delete();
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_POINTS_DELETED_SUCCESSFULLY' );

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Publishes a point
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function publish()
	{
		// Check for request forgeries.
		FD::checkToken();

		$id 	= JRequest::getVar( 'cid' );

		// Get current view
		$view 	= $this->getCurrentView();

		// Get current task
		$task 	= $this->getTask();

		// Ensure that it's an array.
		$ids 	= FD::makeArray( $id );

		if( empty( $id ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$point 	= FD::table( 'Points' );
			$point->load( $id );

			$point->$task();
		}

		$message 	= $task == 'publish' ? 'COM_EASYSOCIAL_POINTS_PUBLISHED_SUCCESSFULLY' : 'COM_EASYSOCIAL_POINTS_UNPUBLISHED_SUCCESSFULLY';

		$view->setMessage( JText::_( $message ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Responsible to save a user point.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get current view
		$view 	= $this->getCurrentView();

		// Get the current task of this request.
		$task 	= $this->getTask();

		// Get the point id.
		$id 	= JRequest::getInt( 'id' );

		$point = FD::table( 'Points' );
		$point->load( $id );

		if( !$id || !$point->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $task , $point );
		}

		// Try to bind the data from $_POST now.
		$post 	= JRequest::get( 'POST' );

		// If there are params sent from the post, we need to process them accordingly.
		if (isset($post['params']) && !empty($post['params'])) {

			// Go through each of the params
			$postParams 	= $post['params'];

			// Get the params from the point
			$params 		= $point->getParams();

			foreach($postParams as $key => $value)
			{
				$params->set($key . '.value', $value);
			}

			$post['params']	= $params->toString();
		}

		$point->bind( $post );

		$state 	= $point->store();

		if( !$state )
		{
			$view->setMessage( $point->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $task , $point );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_SAVED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $task , $point );
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

		$state = $this->installPackage( $file, 'points', array( 'zip', 'points' ) );

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
		// Check for request forgeries
		FD::checkToken();

		// Retrieve the view.
		$view 	= $this->getCurrentView();

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Points' );

		// Get the list of paths that may store points
		$config = FD::config();
		$paths 	= $config->get( 'points.paths' );

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
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_INVALID_CSV_FILE' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Load up the points library
		$points 	= FD::points();

		// Collect the list of failed and successfull items
		$failed 	= array();
		$success 	= array();

		foreach( $data as $row )
		{
			$userId 	= isset( $row[ 0 ] ) ? $row[ 0 ] : false;
			$value 		= isset( $row[ 1 ] ) ? $row[ 1 ] : false;
			$message 	= isset( $row[ 2 ] ) ? $row[ 2 ] : false;

			$obj 		= (object) $row;

			// Skip this
			if( !$userId || !$points )
			{
				$failed[]	= $obj;
				continue;
			}

			$points->assignCustom( $userId , $value , $message );

			$success[]	= $obj;
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_CSV_FILE_PARSED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $success , $failed );
	}

	/**
	 * Scans for rules throughout the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function scan()
	{
		FD::checkToken();

		// Get the allowed rule scan sections
		$config		= FD::config();

		// Retrieve the view.
		$view 		= $this->getCurrentView();

		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'file' , '' );

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Points' );

		$obj 			= new stdClass();

		// Format the output to display the relative path.
		$obj->file		= str_ireplace( JPATH_ROOT , '' , $file );
		$obj->rules 	= $model->install( $file );

		return $view->call( __FUNCTION__ , $obj );
	}
}
