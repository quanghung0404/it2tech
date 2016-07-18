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

// Import dependencies.
FD::import( 'admin:/includes/apps/dependencies' );

/**
 *
 * Handles applications installed on the site.
 *
 * @since	1.0
 * @access	public
 *
 */
class SocialApps
{
	/**
	 * Static variable for caching.
	 * @var	SocialApps
	 */
	private static $instance = null;

	/**
	 * Cached stored apps on this object for easy access
	 * @var	SocialApps
	 */
	private static $cachedApps = array();

	// Store apps locally.
	private $apps 	= array();

	/**
	 * Object initialisation for the class. Apps should be initialized using
	 * FD::getInstance( 'Apps' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  SocialApps	The SocialApps object.
	 */
	public static function getInstance()
	{
		if( !self::$instance )
		{
			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * Loads all app language files.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function loadAllLanguages()
	{
		jimport( 'joomla.filesystem.folder' );

		// Get list of apps that should be loaded.
		$model 		= FD::model( 'Apps' );

		// @TODO: MUST FIX THIS TO NOT USE ANY LIMITS
		$apps		= $model->setLimit( 10000 )->getApps( array( 'state' => SOCIAL_STATE_PUBLISHED ) );

		if( !$apps )
		{
			return;
		}

		foreach( $apps as $app )
		{
			$app->loadLanguage();
		}
	}

	/**
	 * Load a list of applications.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The type of applications to load. (E.g: users , groups )
	 * @param	Array 		An array of application that we should load. (E.g: joomla_username, joomla_password)
	 * @param
	 */
	public function load( $group , $inclusion = array() )
	{
		static $loaded	= array();

		// Singleton pattern where we should only load necessary items.
		if( !isset( $loaded[ $group ] ) )
		{
			// Get a list of applications that should be rendered for this app type.
			$model 		= FD::model( 'Apps' );

			// Get a list of apps
			$options 	= array( 'type' => 'apps' , 'group' => $group , 'state' => SOCIAL_STATE_PUBLISHED );
			$apps 		= $model->getApps( $options );

			if( $apps )
			{
				foreach( $apps as $app )
				{
					$this->loadApp( $app );
				}

				$this->apps[ $group ] = $apps;

				$loaded[ $group ]	= true;
			}
			else
			{
				$loaded[ $group ]	= false;
			}
		}

		return $loaded[ $group ];
	}

	/**
	 * Responsible to render the widget on specific profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderWidgets( $group , $view , $position , $args = array() )
	{
		// Get a list of apps that has widget layout.
		$model = ES::model('Apps');

		// Determine if the uid is provided
		if (isset($args['uid'])) {
			$uid = $args['uid'];
		} else {

			// Get the user
			$user = isset($args[0]) ? $args[0] : FD::user();
			$uid = $user->id;
		}

		$options = array('uid' => $uid, 'widget' => SOCIAL_STATE_PUBLISHED, 'group' => $group, 'limit' => null, 'state' => SOCIAL_STATE_PUBLISHED, 'type' => SOCIAL_APPS_TYPE_APPS);

		// For now, only $group == 'user' have 'key' in order to filter what app does the user install
		// group and event doesn't need to pass in key because we don't have mapping of which app exist in which group/event. All group/event apps is default.
		if ($group == SOCIAL_TYPE_USER) {
			$options['key'] = $group;
		}

		// Get a list of apps
		$apps = $model->getApps($options);

		if (!$apps) {
			return false;
		}

		// Set the initial path of the apps
		$folder = SOCIAL_APPS . '/' . $group;

		// Initialize default contents
		$contents = '';

		// Go through each of these apps that are widgetable and see if there is a .widget file.
		foreach ($apps as $app) {

			// Check if the widget folder exists for this view.
			$file 	= $folder . '/' . $app->element . '/widgets/' . $view . '/view.html.php';

			if (!JFile::exists($file)) {
				continue;
			}

			require_once($file);

			$className 	= ucfirst($app->element) . 'Widgets' . ucfirst($view);

			// Check if the class exists in this context.
			if (!class_exists($className)) {
				continue;
			}

			if ($className == 'PhotosWidgetsGroups') {
				// echo '<pre>';
				// var_dump($app);
				// echo '</pre>';
			}

			$widgetObj	= new $className($app, $view);

			// Check if the method exists in this context.
			if (!method_exists($widgetObj, $position)) {
				continue;
			}

			ob_start();
			call_user_func_array( array( $widgetObj , $position ) , $args );
			$output 	= ob_get_contents();
			ob_end_clean();

			$contents .= $output;
		}

		// If nothing to display, just return false.
		if (empty($contents)) {
			return false;
		}

		// We need to wrap the app contents with our own wrapper.
		$theme 		= FD::themes();
		$theme->set('contents', $contents);
		$contents	= $theme->output('site/apps/default.widget.' . strtolower($view));

		return $contents;
	}

	/**
	 * Render's an app controller
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderController( $controllerName , $controllerTask , SocialTableApp $app )
	{
		// If application id is not provided, stop execution here.
		if (!$app->id) {
			return false;
		}

		// Construct the app's controller path.
		$controllerName = strtolower($controllerName);
		$file = SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/controllers/' . $controllerName . '.php';

		// Check if the controller file exists
		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		// Construct the class name.
		$className 	= ucfirst($app->element) . 'Controller' . ucfirst($controllerName);

		// If despite loading the file, the class doesn't exist, don't proceed.
		if (!class_exists($className)) {
			return false;
		}

		// Instantiate the new class since we need to render it.
		$controller = new $className($app->group, $app->element);

		// Get the contents.
		$controller->$controllerTask();
	}

	/**
	 * Responsible to render an application's contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The view to render.
	 * @param	SocialTableApp	The application table object.
	 * @param	Array			An array of key value pairs to pass to the view as arguments.
	 * @param
	 */
	public function renderView( $viewType , $viewName , SocialTableApp $app , $args = array() )
	{
		// If application id is not provided, stop execution here.
		if (!$app->id) {
			return JText::_( 'COM_EASYSOCIAL_APPS_INVALID_ID_PROVIDED' );
		}

		// Construct the apps path.
		$path 	= SOCIAL_APPS . '/' . $app->group . '/' . $app->element;

		// Construct the relative file path based on the current view request.
		$file 	= 'views/' . $viewName . '/view.html.php';

		// Construct the absolute path now.
		$absolutePath 	= $path . '/' . $file;

		// Check if the view really exists.
		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $absolutePath ) )
		{
			return JText::sprintf( 'COM_EASYSOCIAL_APPS_VIEW_DOES_NOT_EXIST' , $viewName );
		}

		require_once( $absolutePath );

		// Construct the class name for this view.
		$className 	= ucfirst( $app->element ) . 'View' . ucfirst( $viewName );

		if( !class_exists( $className ) )
		{
			return JText::sprintf( 'COM_EASYSOCIAL_APPS_CLASS_DOES_NOT_EXIST' , $className );
		}

		// Instantiate the new class since we need to render it.
		$viewObj 	= new $className( $app , $viewName );

		// Get the contents.
		ob_start();
		call_user_func_array( array( $viewObj , 'display' ) , $args );
		$contents 	= ob_get_contents();
		ob_end_clean();

		// We need to wrap the app contents with our own wrapper.
		$template 	= 'site/apps/default.' . strtolower( $viewType ) . '.wrapper';

		$theme 		= FD::themes();
		$theme->set( 'contents' , $contents );
		$contents	= $theme->output( $template );

		return $contents;
	}

	/**
	 * Allows caller to retrieve the app object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApp( SocialTableApp &$app )
	{
		// Load the app first.
		$this->loadApp( $app );

		// Check if the app exists
		if( !isset( self::$cachedApps[ $app->group ][ $app->element ] ) )
		{
			return false;
		}

		// Once the app is loaded, return the data
		$result 	= self::$cachedApps[ $app->group ][ $app->element ];

		return $result;
	}

	/**
	 * Responsible to attach the application into the SocialDispatcher object.
	 * In short, it does the requiring of files here.
	 *
	 * @since	1.0
	 * @access	private
	 * @param	SocialTableApp	The application ORM.
	 * @return	bool
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	private function loadApp(SocialTableApp &$app)
	{
		static $loadedApps	= array();

		// Application type and element should always be in lowercase.
		$group 		= strtolower($app->group);
		$element	= strtolower($app->element );

		if( !isset( $loadedApps[ $group ][ $element ] ) )
		{
			// Get dispatcher object.
			$dispatcher = FD::dispatcher();

			// Application trigger file paths.
			$filePath 	= SOCIAL_APPS . '/' . $group . '/' . $element . '/' . $element . '.php';
			$fileExists	= JFile::exists($filePath);

			// If file doesn't exist, skip the entire block.
			if (!$fileExists) {
				$loadedApps[$group][$element]	= false;

				return $loadedApps[$group][$element];
			}

			// Assuming that the file exists here (It should)
			require_once($filePath);

			$className		= 'Social' . ucfirst( $group ) . 'App' . ucfirst( $element );

			// If the class doesn't exist in this context,
			// the application might be using a different class. Ignore this.
			if (!class_exists($className)) {
				$loadedApps[ $group ][ $element ]	= false;
				return $loadedApps[ $group ][ $element ];
			}

			$appObj				= new $className();
			$appObj->group		= $group;
			$appObj->element	= $app->element;

			self::$cachedApps[$group][$element]	= $appObj;

			// Attach the application into the observer list.
			$dispatcher->attach($group , $app->element , $appObj );

			// Add a state for this because we know it has already been loaded.
			$loadedApps[$group][$element]	= true;
		}

		return $loadedApps[$group][$element];
	}

	public function getCallable($namespace)
	{
		$path = '';

		$class = null;

		$className = '';

		$parts = explode( '/', $namespace );

		$location = array_shift( $parts );

		$method = array_pop( $parts );

		if( $location == 'site' || $location == 'admin' )
		{
			list( $type, $file ) = $parts;

			$path = $location == 'admin' ? SOCIAL_ADMIN : SOCIAL_SITE;

			$path .=  '/' . $type . '/' . $file;

			switch( $type )
			{
				case 'controllers':
				case 'models':
					$path .= '.php';
				break;
				case 'views':
					$path .= '/view.html.php';
				break;
			}

			$className = 'EasySocial' . ucfirst( rtrim( $type, 's' ) ) . ucfirst( $file );
		}

		if( $location == 'apps' )
		{
			list( $group, $element, $type, $file ) = $parts;

			$path = SOCIAL_APPS . '/' . $group . '/' . $element . '/' . $type . '/';

			switch( $type )
			{
				case 'controllers':
				case 'models':
					$path .= $file . '.php';
				break;
				case 'views':
					$path .=  'view.html.php';
			}

			$className = ucfirst( $element ) . ucfirst( trim( $type, 's' ) ) . ucfirst( $file );
		}

		if( $location == 'fields' )
		{
			list( $group, $element ) = $parts;

			$path = SOCIAL_FIELDS . '/' . $group . '/' . $element . '/' . $element . '.php';

			$className = 'SocialFields' . ucfirst( $group ) . ucfirst( $element );
		}

		if( !JFile::exists( $path ) )
		{
			return false;
		}

		include_once( $path );

		if( !class_exists( $className ) )
		{
			return false;
		}

		if( $location == 'admin' || $location == 'site' )
		{
			$class = new $className();
		}

		if( $location == 'apps' )
		{
			$class = new $className( $parts[0], $parts[1] );
		}

		if( $location == 'fields' )
		{
			$config = array( 'group' => $parts[0], 'element' => $parts[1] );

			$class = new $className( $config );
		}

		$callable = array( $class, $method );

		if( !is_callable( $callable ) )
		{
			return false;
		}


		return $callable;
	}

	/**
	 * Determines if the app should appear on the app listings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasActivityLog( SocialTableApp $table )
	{
		$file 	= SOCIAL_APPS . '/' . $table->group . '/' . $table->element . '/' . $table->element . '.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return true;
		}

		require_once( $file );

		$appClass 	= 'Social' . ucfirst( $table->group ) . 'App' . ucfirst( $table->element );

		if( !class_exists( $appClass ) )
		{
			return true;
		}

		$app 			= new $appClass();
		$app->element	= $table->element;
		$app->group 	= $table->group;

		// Always return true unless explicitly disabled
		if( !method_exists( $app , 'hasActivityLog' ) )
		{
			return true;
		}

		$appear 	= $app->hasActivityLog();


		return $appear;
	}

	/**
	 * Determines if the app should appear on the app listings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasAppListing( SocialTableApp $table , $view , $uid = '' , $type = '' )
	{
		$file = SOCIAL_APPS . '/' . $table->group . '/' . $table->element . '/' . $table->element . '.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return true;
		}

		require_once($file);

		$appClass = 'Social' . ucfirst($table->group) . 'App' . ucfirst($table->element);

		if (!class_exists($appClass)) {
			return true;
		}

		$app = new $appClass();
		$app->element = $table->element;
		$app->group = $table->group;

		// Properties based
		if (isset($app->appListing)) {
			return $app->appListing;
		}

		if (!method_exists($app, 'appListing')) {
			return true;
		}

		$display = $app->appListing($view, $uid, $type);
		return $display;
	}
}
