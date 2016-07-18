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
ES::import('admin:/controllers/controller');

class EasySocialControllerApps extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'unpublish' , 'unpublish' );
		$this->registerTask( 'save' , 'store' );
		$this->registerTask( 'apply' , 'store' );
	}

	/**
	 * Purges discovered items from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purgeDiscovered()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		$model 	= FD::model( 'Apps' );

		// Delete discovered items
		$model->deleteDiscovered();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_DISCOVERED_APPS_PURGED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Application Discovery
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function discover()
	{
		// Check for request forgeries
		FD::checkToken();

		$model = ES::model('Apps');
		$total = $model->discover();

		if (!$total) {
			$this->view->setMessage('COM_EASYSOCIAL_APPS_NO_APPS_DISCOVERED');
			return $this->view->call(__FUNCTION__, $total);
		}
		
		$this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_APPS_DISCOVERED_APPS', $total));

		return $this->view->call(__FUNCTION__, $total);
	}

	/**
	 * Saves the app
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the current task
		$task 	= $this->getTask();

		// Get the app id.
		$id 	= JRequest::getInt( 'id' );

		// Load the app
		$app 	= FD::table( 'App' );
		$app->load( $id );

		if( !$id || !$app->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_UNABLE_TO_FIND_APP' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app , $task );
		}

		// Determines if the "default" value changed
		$default	 = JRequest::getVar( 'default' );

		// Determine if the default is changed from 0 -> 1
		// This is because when it's changed from 0 -> 1, we need to delete existing user params.
		if( $app->default != $default && $default )
		{
			$model 	= FD::model( 'Apps' );
			$state	= $model->removeUserApp( $app->id );
		}

		// Get the posted data.
		$post = JRequest::get( 'post' );

		// Retrieve params values
		$rawParams = $post['params'];
		$post['params']	= FD::json()->encode($rawParams);

		// Bind the posted data to the app
		$app->bind( $post );

		$state 	= $app->store();

		if( !$state )
		{
			$view->setMessage( $app->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app , $task );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_SAVED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $app , $task );
	}
	
	/**
	 * Unpublishes an app
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function publish()
	{
		// Check for tokens.
		ES::checkToken();

		// Get apps from the request.
		$ids = JRequest::getVar('cid');

		// Ensure that it's in an array form
		$ids = ES::makeArray($ids);

		$type = '';
		foreach ($ids as $id) {
			$app = ES::table('App');
			$app->load($id);
			$app->publish();


			$type = $app->type;
		}

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_APPS_PUBLISHED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $type);
	}

	/**
	 * Unpublishes an app
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unpublish()
	{
		// Check for tokens.
		ES::checkToken();

		// Get apps from the request.
		$ids = JRequest::getVar('cid');

		// Ensure that it's in an array form
		$ids = ES::makeArray($ids);

		$type = '';

		foreach ($ids as $id) {
			$app = ES::table('App');
			$app->load($id);
			$app->unpublish();

			$type = $app->type;
		}

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_APPS_UNPUBLISHED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $type);
	}

	/**
	 * Uninstalls an app from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstall()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get current view
		$view 	= $this->getCurrentView();

		// Get the application id.
		$ids 	= JRequest::getVar( 'cid' );
		$ids	= FD::makeArray( $ids );

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$app 	= FD::table( 'App' );
			$app->load( $id );

			// If app is a core app, do not allow the admin to delete this.
			if( $app->core )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_UNABLE_TO_DELETE_CORE_APP' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}

			// Perform the uninstallation of the app.
			$state 	= $app->uninstall();
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_UNINSTALLED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Processes installation of discovered apps
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installDiscovered()
	{
		// Check for request forgeries.
		FD::checkToken();

		$view 	= $this->getCurrentView();

		// Get a list of id's to install
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that they are in an array form
		$ids 	= FD::makeArray( $ids );
		$apps 	= array();

		foreach( $ids as $id )
		{
			$app 	 = FD::table( 'App' );
			$app->load( $id );

			$path		= SOCIAL_APPS;

			if( $app->type == 'apps' )
			{
				$path 	= $path . '/' . $app->group . '/' . $app->element;
			}

			if( $app->type == 'fields' )
			{
				$path 	= $path . '/fields/' . $app->group . '/' . $app->element;
			}

			$installer 	= FD::get( 'Installer' );
			$installer->load( $path );

			$app		= $installer->install();

			$apps[]	= $app;
		}

		$total	 = count( $apps );

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_APPS_DISCOVERED_INSTALLED' , $total ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $apps );
	}

	/**
	 * Processes the installation package from directory method.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 **/
	public function installFromDirectory($path = '')
	{
		// Check for request forgeries.
		FD::checkToken();

		if (!$path) {
			$path = $this->input->get('package-directory', '');
		}

		// Get Joomla's configuration
		$jConfig = ES::config('joomla');

		// Try to detect if the temporary path is the same as the default path.
		if ($path == $jConfig->getValue('tmp_path')) {
			$this->view->setMessage('COM_EASYSOCIAL_INSTALLER_PLEASE_SPECIFY_DIRECTORY', SOCIAL_MSG_ERROR);

			return $this->view->install();
		}

		// Retrieve the installer library.
		$installer = ES::get('Installer');

		// Try to load the installation from path.
		$state = $installer->load($path);

		// If there's an error, we need to log it down.
		if (!$state) {
			$this->view->setMessage($installer->getError(), SOCIAL_MSG_ERROR);

			return $this->view->install();
		}

		// Install the app now
		$app = $installer->install();

		// If there's an error installing, log this down.
		if ($app === false) {
			$this->view->setMessage($installer->getError(), SOCIAL_MSG_ERROR);
			return $this->view->install();
		}

		return $this->view->installCompleted($app);
	}

	/**
	 * Processes the install by uploading
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 **/
	public function installFromUpload()
	{
		// Check for request forgeries.
		FD::checkToken();

		$package = JRequest::getVar('package', '', 'files');

		// Test for empty packages.
		if (!isset($package['tmp_name']) || !$package['tmp_name']) {
			$this->view->setMessage('COM_EASYSOCIAL_APPS_PLEASE_UPLOAD_INSTALLER', SOCIAL_MSG_ERROR);
			return $this->view->install();
		}

		$source = $package['tmp_name'];
		$jConfig = ES::config('joomla');

		// Construct the destination path
		$destination = $jConfig->getValue('tmp_path') . '/' . $package['name'];

		// Get the installer library
		$installer = ES::get('Installer');

		// Now try to upload the installer
		$state = $installer->upload($source, $destination);

		if (!$state) {
			$this->view->setMessage('COM_EASYSOCIAL_APPS_UNABLE_TO_COPY_UPLOADED_FILE', SOCIAL_MSG_ERROR);
			return $this->view->install();
		}

		// Unpack the archive.
		$path = $installer->extract($destination);

		// When something went wrong with the installation, just display the error
		if ($path === false) {
			$error = ES::get('Errors')->getErrors('installer.extract');

			$this->info->set($error, SOCIAL_MSG_ERROR);
			$this->app->redirect('index.php?option=com_easysocial&view=applications&layout=error');
			return $this->app->close();
		}

		return $this->installFromDirectory($path);
	}

	/**
	 * List apps from the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getApps()
	{
		// Get the current view object.
		$view 			= FD::getInstance( 'View' , 'Apps' );

		// Get dispatcher.
		$dispatcher		= FD::getInstance( 'dispatcher' );

		// Retrieves a list of filters.
		$filters		= JRequest::getVar( 'filters', array() );

		// Determine the trigger to be executed
		$trigger		= JRequest::getString( 'trigger', '' );

		// Get list of apps.
		$apps 			= FD::getInstance( 'apps' );
		$items 			= $apps->getApps( $filters[ 'type' ] );

		// We need to format the ajax result with appropriate values.
		if( $items )
		{
			foreach( $items as &$item )
			{
				$item->app_id 	= $item->id;
				$item->config 	= $apps->getManifest( $item , 'config' , 'fields' );

				$params 		= $apps->getManifest( $item );
				$callback 		= array( 'setParams' => $params , 'setField' => $item , 'setElementName' => $item->element );

				$item->html 	= $dispatcher->trigger( $item->type , $trigger , array() , $item->element , $callback );
			}
		}
		return $view->call( __FUNCTION__ , $items );
	}

}
