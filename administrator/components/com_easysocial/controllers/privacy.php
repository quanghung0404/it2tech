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

class EasySocialControllerPrivacy extends EasySocialController
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

		$this->registerTask( 'save' , 'save' );
		$this->registerTask( 'apply' , 'save' );

		$this->registerTask( 'publish' , 'togglePublish' );
		$this->registerTask( 'unpublish' , 'togglePublish' );
	}

	public function togglePublish()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		$ids 	= JRequest::getVar( 'cid' );
		$task 	= $this->getTask();

		if( !$ids )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call(__FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$privacy 	= FD::table( 'Privacy' );
			$privacy->load( $id );

			// We don't want the user to toggle publishing for core.view.
			if( $privacy->type == 'core' && $privacy->rule == 'view' )
			{
				continue;
			}

			$privacy->state 	= $task == 'publish' ? SOCIAL_STATE_PUBLISHED : SOCIAL_STATE_UNPUBLISHED;

			$privacy->store();
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_PRIVACY_PUBLISHED_SUCCESS' );

		if( $task == 'unpublish' )
		{
			$message 	= JText::_( 'COM_EASYSOCIAL_PRIVACY_UNPUBLISHED_SUCCESS' );
		}

		$view->setMessage( $message );

		return $view->call( __FUNCTION__ );
	}

	public function cancel()
	{
		// Check for request forgeries
		FD::checkToken();

		$view 	= $this->getCurrentView();
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Deletes a privacy
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the id from the request
		$ids 	= JRequest::getVar( 'cid' );

		if( !$ids )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call(__FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$privacy 	= FD::table( 'Privacy' );
			$privacy->load( $id );

			if( $privacy->core )
			{
				continue;
			}

			$privacy->delete();
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_DELETED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );

		$view->call( __FUNCTION__ );
	}

	/**
	 * Saves a badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the badge id from the request
		$id 	= JRequest::getInt( 'id' );

		// Get the current view
		$view 	= $this->getCurrentView();

		// Try to load the badge now.
		$privacy 	= FD::table( 'Privacy' );
		$privacy->load( $id );

		if( !$id || !$privacy->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the posted data.
		$post 	= JRequest::get( 'POST' );
		$value 	= $post['value'];

		$privacy->value = $value;
		$state = $privacy->store();

		if( $state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		}
		else
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATED_FAILED' ) , SOCIAL_MSG_ERROR );
		}

		return $view->call( __FUNCTION__ , $this->getTask() , $privacy );
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

		$state = $this->installPackage( $file, 'privacy', array( 'zip', 'privacy' ) );

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
		$view 	= FD::view( 'Privacy', true );

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Privacy' );

		// Get the list of paths that may store points
		$paths[]	= '/administrator/components';
		$paths[]	= '/components';
		$paths[]	= '/media/com_easysocial/apps/user';
		$paths[]	= '/media/com_easysocial/apps/fields/user';
		$paths[]	= '/plugins';


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
		$view 		= FD::view( 'Privacy', true );

		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'file' , '' );

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Privacy' );

		$obj 			= new stdClass();

		// Format the output to display the relative path.
		$obj->file		= str_ireplace( JPATH_ROOT , '' , $file );
		$obj->rules 	= $model->install( $file );

		return $view->call( __FUNCTION__ , $obj );
	}
}
