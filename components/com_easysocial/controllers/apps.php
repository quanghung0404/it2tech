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

// Import parent controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerApps extends EasySocialController
{
	/**
	 * Allows user to save settings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveSettings()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get current view.
		$view 	= $this->getCurrentView();

		// Get current logged in user
		$my 	= FD::user();

		// Get the app id from request.
		$id 	= JRequest::getInt( 'id' );

		// Try to load the app
		$app 	= FD::table( 'App' );
		$app->load( $id );

		if( !$id || !$app->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Ensure that the user can really access this app settings.
		if( !$app->isInstalled() )
		{
			return $ajax->reject( FD::info()->set( JText::_( 'COM_EASYSOCIAL_APPS_SETTINGS_NOT_INSTALLED' ) , SOCIAL_MSG_ERROR ) );
		}

		$data 	= JRequest::getVar( 'data' , '' );

		// Convert the object to proper json string
		$raw	= FD::makeJSON( $data );

		$map 	= FD::table( 'AppsMap' );
		$map->load( array( 'uid' => $my->id , 'app_id' => $app->id ) );

		$map->params 	= $raw;

		// Store user params
		$map->store();

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves a list of apps
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getApps()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that they are logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// See if sort is provided.
		$sort 	= JRequest::getWord( 'sort' , 'alphabetical' );

		$group	= JRequest::getWord( 'group', SOCIAL_APPS_GROUP_USER );

		// Default properties
		$modelFunc	= 'getApps';
		$options	= array( 'type' => SOCIAL_APPS_TYPE_APPS , 'installable' => true, 'group' => $group );

		// See if filter is provided
		$filter 	= JRequest::getWord( 'filter' , '' );

		if( !empty( $filter ) && $filter != 'browse' )
		{
			// Currently the only filter type is 'mine'
			$my 	= FD::user();

			$options[ 'uid' ]	= $my->id;
			$options[ 'key' ]	= SOCIAL_TYPE_USER;
		}

		switch( $sort )
		{
			case 'recent':
				$options['sort'] = 'a.created';
				$options['order'] = 'desc';
				break;

			case 'alphabetical':
				$options['sort'] = 'a.title';
				$options['order'] = 'asc';
				break;

			case 'trending':
				// need a separate logic to get trending based on apps_map
				$modelFunc = 'getTrendingApps';
				break;
		}

		// Get apps model
		$model 		= FD::model( 'Apps' );
		$apps 		= $model->$modelFunc( $options );

		$view->call( __FUNCTION__ , $apps );
	}

	/**
	 * Allows apps to process a controller
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function controller()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the app
		$id  = $this->input->get('appId', 0, 'int');
		$app = FD::table('App');
		$app->load($id);

		// Allow app to specify their own controller and task
		$controller = $this->input->get('appController', '', 'cmd');
		$task = $this->input->get('appTask', '', 'cmd');

		// Process the app's controller
		$lib = FD::apps();
		$lib->renderController($controller, $task, $app);
	}

	public function getTnc()
	{
		// Check for request forgeries
		FD::checkToken();

		$id		= JRequest::getInt( 'id' );

		$app	= FD::table( 'App' );
		$app->load( $id );

		$config = $app->getManifest();

		$tnc = JText::_( 'COM_EASYSOCIAL_APPS_TNC' );

		if( is_object( $config ) && property_exists( $config, 'tnc' ) )
		{
			// $app->loadLanguage();
			$tnc = JText::_( $config->tnc );
		}

		$this->getCurrentView()->call( __FUNCTION__, $tnc );
	}

	/**
	 * Allows caller to install applications
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installApp()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the user must be logged in.
		FD::requireLogin();

		// Get the current view.
		$view	= $this->getCurrentView();

		// Get the app id.
		$id		= JRequest::getInt( 'id' );

		// Check if app is a valid app
		$app	= FD::table( 'App' );

		// Get the current logged in user.
		$my 	= FD::user();

		if( !$app->load( $id ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_APP_ID_INVALID' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		if( $app->isInstalled() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_APP_ID_INVALID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Try to install the app now.
		$result = $app->install( $my->id );

		if( !$result )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_INSTALL_ERROR_OCCURED' ), SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows caller to uninstall an application
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function uninstallApp()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allow registered users
		FD::requireLogin();

		// Get app id.
		$id		= JRequest::getInt( 'id' );

		// Check if app is a valid app
		$app	= FD::table( 'App' );

		// Get the current view
		$view 	= $this->getCurrentView();

		if (!$app->load($id)) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_UNINSTALL_ERROR_OCCURED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__, false );
		}

		// Try to uninstall the app.
		$result = $app->uninstallUserApp();

		if (!$result) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_UNINSTALL_ERROR_OCCURED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		return $view->call( __FUNCTION__ , true );
	}
}
