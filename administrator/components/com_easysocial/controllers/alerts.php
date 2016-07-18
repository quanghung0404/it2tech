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

class EasySocialControllerAlerts extends EasySocialController
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

		$this->registerTask( 'emailPublish' 	, 'togglePublish' );
		$this->registerTask( 'emailUnpublish'	, 'togglePublish' );
		$this->registerTask( 'systemPublish' 	, 'togglePublish' );
		$this->registerTask( 'systemUnpublish'	, 'togglePublish' );
		$this->registerTask( 'publish'			, 'unpublish' );
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

		// Get the current task
		$task 	= $this->getTask();

		// Ensure that they are in an array form.
		$ids 	= FD::makeArray( $ids );

		// Get the current view
		$view 	= $this->getCurrentView();

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ALERTS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$alert 	= FD::table( 'Alert' );
			$alert->load( $id );

			if( $task == 'emailUnpublish' )
			{
				$alert->email 	= SOCIAL_STATE_UNPUBLISHED;
			}

			if( $task == 'emailPublish' )
			{
				$alert->email 	= SOCIAL_STATE_PUBLISHED;
			}

			if( $task == 'systemPublish' )
			{
				$alert->system 	= SOCIAL_STATE_PUBLISHED;
			}

			if( $task == 'systemUnpublish' )
			{
				$alert->system 	= SOCIAL_STATE_UNPUBLISHED;
			}

			$alert->store();
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_ALERTS_PUBLISHED_SUCCESS' );

		if( $task == 'emailUnpublish' || $task == 'systemUnpublish' )
		{
			$message 	= JText::_( 'COM_EASYSOCIAL_ALERTS_UNPUBLISHED_SUCCESS' );
		}

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $task );
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
		$model 	= FD::model( 'Alert' );

		// Get the list of paths that may store points
		$config = FD::config();
		$paths 	= $config->get( 'alerts.paths' );

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
	 * Scans for .alert rules throughout the site.
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
		$model 	= FD::model( 'Alert' );

		$obj 	= new stdClass();

		// Format the output to display the relative path.
		$obj->file		= str_ireplace( JPATH_ROOT , '' , $file );
		$obj->rules 	= $model->install( $file );

		return $view->call( __FUNCTION__ , $obj );
	}

	public function upload()
	{
		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'package' , '' , 'FILES');

		$state = $this->installPackage( $file, 'alert', array( 'zip', 'alert' ) );

		$view = $this->getCurrentView();

		return $view->call( __FUNCTION__ );
	}

	public function publish()
	{
		$ids = JRequest::getVar('cid');

		$ids = FD::makeArray($ids);

		if (empty($ids)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_ALERTS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		$state = $this->getTask() === 'publish';

		foreach ($ids as $id) {
			$table = FD::table('alert');
			$table->load($id);

			$table->published = $state;

			$table->store();
		}

		$message = $state ? 'COM_EASYSOCIAL_ALERTS_PUBLISHED_SUCCESS' : 'COM_EASYSOCIAL_ALERTS_UNPUBLISHED_SUCCESS';

		$view = $this->getCurrentView();

		$view->setMessage(JText::_($message), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__);
	}

	public function unpublish()
	{
		//make Joomla 2.5 work.
		$this->publish();
	}	
}
