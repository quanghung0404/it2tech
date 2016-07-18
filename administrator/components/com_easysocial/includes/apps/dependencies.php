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

class SocialAppsAbstract extends EasySocial
{
	/**
	 * The current logged in user.
	 * @var SocialUser
	 */
	public $my = null;

	/**
	 * The app's group.
	 * @var string
	 */
	public $group = null;

	/**
	 * The app's element name.
	 * @var string
	 */
	public $element 	= null;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves the app table row
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function getApp()
	{
		static $apps 	= array();

		$key 	= $this->group . $this->element;

		if( !isset( $apps[ $key ] ) )
		{
			$app 	= FD::table( 'App' );
			$app->load( array( 'element' => $this->element , 'group' => $this->group ) );

			$apps[ $key ]	= $app;
		}

		return $apps[ $key ];
	}


	/**
	 * Retrieves the params for this app
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	SocialRegistry
	 */
	public function getParams()
	{
		static $params 	= array();

		$key 	= $this->element . $this->group;

		if( !isset( $params[ $key ] ) )
		{
			$app 		= $this->getApp();
			$registry	= $app->getParams();

			$params[ $key ]	= $registry;

		}

		return $params[ $key ];
	}
}

abstract class SocialAppItem extends SocialAppsAbstract
{
	protected $theme = null;
	public $paths = null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $options = array() )
	{
		$this->my = FD::user();

		if (!empty($options)) {

			if (isset($options['group'])) {
				$this->group = $options['group'];
			}

			if (isset($options['element'])) {
				$this->element = $options['element'];
			}
		}

		// Initialize the theme object for the current app.
		$this->theme = ES::themes();

		$this->paths = array(
								'models'	=> SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/models',
								'tables'	=> SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/tables',
								'views'		=> SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/views',
								'config'	=> SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/config',
							);

		parent::__construct();
	}

	/**
	 * Determines if the app has stream filter
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasStreamFilter()
	{
		$params = $this->getApp()->getParams();

		$filter = $params->get('stream_filter', true);

		if ($filter) {
			return true;
		}

		return false;
	}

	/**
	 * Executes when a trigger is called.
	 *
	 * @since	1.0
	 * @param	string	The event name.
	 * @param	Array	An array of arguments
	 * @access	public
	 */
	public final function update( $eventName , &$args )
	{
		$paths 	= array();

		$paths[ 'tables' ]	= SOCIAL_APPS . '/' . $this->group . '/' . $this->element . '/tables';

		$this->paths 		= $paths;

		if( method_exists( $this , $eventName ) )
		{
			return call_user_func_array( array( $this , $eventName ) , $args );
		}

		return false;
	}

	/**
	 * Retrieves a JTable object. This simplifies the caller from manually adding include path all the time.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name		The table's name without the prefix.
	 * @param	string	$prefix		Optional prefixed table name.
	 *
	 * @return	JTable				The JTable object.
	 */
	public function getTable( $name , $prefix = '' )
	{
		JTable::addIncludePath( $this->paths[ 'tables' ] );

		$prefix	= empty( $prefix ) ? ucfirst( $this->element ) . 'Table' : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Responsible to help apps to output theme files.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($file)
	{
		// Since this is a field item, we always want to prefix with the standard POSIX format.
		$namespace = 'themes:/apps/' . $this->group . '/' . $this->element . '/' . $file;

		// If there is a "protocol" such as site:/ or admin:/, we should just use it's own namespace
		if (stristr($file, ':/') !== false) {
			$namespace = $file;
		}

		return $this->theme->output($namespace);
	}

	/**
	 * Sets a variable to the theme object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set( $key , $var )
	{
		$this->theme->set( $key , $var );
	}

	/**
	 * Retrieves a list of notification targets
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStreamNotificationTargets($uid, $element, $group, $verb, $targets = array(), $exclusion = array())
	{
		// Get a list of people that also likes this
		$likes		= FD::likes($uid, $element, $verb, $group);
		$targets	= array_merge($targets, $likes->getParticipants(false));

		// Get people who are part of the comments
		$comments 	= FD::comments($uid, $element, $verb, $group);
		$targets 	= array_merge($targets, $comments->getParticipants(array(), false));

		// Remove exclustion
		$targets	= array_diff($targets, $exclusion);

		// Ensure that recipients are unique now.
		$targets 	= array_unique($targets);

		// Reset all indexes
		$targets 	= array_values($targets);

		return $targets;
	}

	/**
	 * Processes the stream action rules
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processStream(SocialStreamItem &$item)
	{
		$verb = $item->verb;
		$path = JPATH_ROOT . '/media/com_easysocial/apps/' . $this->group . '/' . $this->element . '/streams/' . strtolower($verb) . '.php';

		require_once($path);

		$class = 'Social' . ucfirst($this->group) . 'App' . ucfirst($this->element) . 'Stream' . ucfirst($verb);
		$obj   = new $class();
		$obj->execute($item);

		return $obj;
	}

	/**
	 * Retrieves the trigger object for an app
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string	The type of hook. Used for separating between different types of hooks "notifications", "triggers"
	 * @param	string	The name of the hook. Mostly for actions E.g: "comments", "likes"
	 * @return
	 */
	protected function getHook($type, $hook)
	{
		$path 	= JPATH_ROOT . '/media/com_easysocial/apps/' . $this->group . '/' . $this->element . '/hooks/' . strtolower($type) . '.' . strtolower($hook) . '.php';

		require_once($path);

		$class 	= 'Social' . ucfirst($this->group) . 'App' . ucfirst($this->element) . 'Hook' . ucfirst($type) . ucfirst($hook);
		$obj 	= new $class();

		return $obj;
	}

}

/**
 * Main class file that should be extended by application views.
 *
 * @since	1.0
 * @access	public
 */
class SocialAppsController extends SocialAppsAbstract
{
	/**
	 * Stores a list of already initiated models.
	 * @var	Array
	 */
	protected $models 	= array();

	/**
	 * Stores a list of already initiated views.
	 * @var	Array
	 */
	protected $views 	= array();

	/**
	 * Stores a list of default paths for this app.
	 * @var	Array
	 */
	protected $paths 	= array();


	public function __construct( $appGroup , $appElement )
	{
		$this->input = FD::request();
		$this->element 	= $appElement;
		$this->group 	= $appGroup;

		$this->paths 	= array(
								'models'	=> SOCIAL_APPS . '/' . $appGroup . '/' . $appElement . '/models',
								'tables'	=> SOCIAL_APPS . '/' . $appGroup . '/' . $appElement . '/tables',
								'views'		=> SOCIAL_APPS . '/' . $appGroup . '/' . $appElement . '/views',
								'config'	=> SOCIAL_APPS . '/' . $appGroup . '/' . $appElement . '/config',
							);

		$doc = JFactory::getDocument();

		if ($doc->getType() == 'ajax') {
			$this->ajax = FD::ajax();
		}

		parent::__construct();
	}

	/**
	 * Retrieves a JTable object. This simplifies the caller from manually adding include path all the time.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name		The table's name without the prefix.
	 * @param	string	$prefix		Optional prefixed table name.
	 *
	 * @return	JTable				The JTable object.
	 */
	public function getTable( $name , $prefix = '' )
	{
		JTable::addIncludePath( $this->paths[ 'tables' ] );

		$prefix	= empty( $prefix ) ? ucfirst( $this->element ) . 'Table' : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Helper function to assist child classes to retrieve a model object.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param 	string 	$modelName 	The name of the model.
	 **/
	public function getModel( $name )
	{
		if( !isset( $this->models[ $name ] ) )
		{
			$className	= $name . 'Model';

			if( !class_exists( $className ) )
			{
				jimport( 'joomla.application.component.model' );

				// @TODO: Properly test if the file exists before including it.
				JLoader::import( strtolower( $name ) , $this->paths[ 'models' ] );
			}

			// If the class still doesn't exist, let's just throw an error here.
			if( !class_exists( $className ) )
			{
				JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

				return JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_MODEL_NOT_FOUND' , $className ) );
			}

			$model 					= new $className( $name );
			$this->models[ $name ]	= $model;
		}

		return $this->models[ $name ];
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @access	public
	 * @param	string	$uri 	The raw uri string.
	 * @param	boolean	$route	Whether or not the uri should be routed
	 */
	public function redirect( $uri , $route = true )
	{
		$app 	= JFactory::getApplication();
		$app->redirect( $uri );
		$app->close();
	}
}


/**
 * Main class file that should be extended by application views.
 *
 * @since	1.0
 * @access	public
 */
class SocialAppsView
{
	// Stores a list of already initiated models.
	protected $models 	= array();

	// Stores a list of default paths for this app.
	protected $paths 	= array();

	/**
	 * The current view's name.
	 * @var	string
	 */
	protected $viewName	= '';

	public function __construct( SocialTableApp $app , $viewName )
	{
		// Current logged in user
		$this->my = FD::user();

		// The ORM for the app.
		$this->app = $app;

		// Set the view's name.
		$this->viewName	= $viewName;

		$this->paths 	= array(
								'models'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/models',
								'tables'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/tables',
								'views'		=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/views',
								'config'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/config',
							);


		// Allow themes to be available to the caller.
		$this->theme = FD::themes();
		$this->input = FD::request();
		// Allow app to be available in the theme.
		$this->set( 'app'	, $app );
	}

	/**
	 * Retrieves a JTable object. This simplifies the caller from manually adding include path all the time.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name		The table's name without the prefix.
	 * @param	string	$prefix		Optional prefixed table name.
	 *
	 * @return	JTable				The JTable object.
	 */
	public function getTable( $name , $prefix = '' )
	{
		JTable::addIncludePath( $this->paths[ 'tables' ] );

		$prefix	= empty( $prefix ) ? ucfirst( $this->app->element ) . 'Table' : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Helper function to assist child classes to retrieve a model object.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param 	string 	$modelName 	The name of the model.
	 **/
	public function getModel( $name )
	{
		if( !isset( $this->models[ $name ] ) )
		{
			$className	= $name . 'Model';

			if( !class_exists( $className ) )
			{
				jimport( 'joomla.application.component.model' );

				// @TODO: Properly test if the file exists before including it.
				JLoader::import( strtolower( $name ) , $this->paths[ 'models' ] );
			}

			// If the class still doesn't exist, let's just throw an error here.
			if( !class_exists( $className ) )
			{
				JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

				return JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_MODEL_NOT_FOUND' , $className ) );
			}

			$model 					= new $className( $name );
			$this->models[ $name ]	= $model;
		}

		return $this->models[ $name ];
	}

	/**
	 * Retrieves the user params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserParams( $userId )
	{
		$map 	= FD::table( 'AppsMap' );
		$map->load( array( 'app_id' => $this->app->id , 'uid' => $userId ) );

		$registry	= FD::registry( $map->params );

		return $registry;
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @access	public
	 * @param	string	$uri 	The raw uri string.
	 * @param	boolean	$route	Whether or not the uri should be routed
	 */
	public function redirect( $uri , $route = true )
	{
		if( $route )
		{
			// Since redirects does not matter of the xhtml codes, we can just ignore this.
			$uri    = FRoute::_( $uri , false );
		}

		$app 	= JFactory::getApplication();
		$app->redirect( $uri );
		$app->close();
	}

	/**
	 * Main method to help caller to display contents from their theme files.
	 * The method automatically searches for {%APP_NAME%/themes/%CURRENT_THEME%/%FILE_NAME%}
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The template file name.
	 * @return
	 */
	public function display( $tpl = null , $docType = null )
	{

		$format		= JRequest::getWord( 'format' , 'html' );

		// Since the $tpl now only contains the name of the file, we need to be smart enough to determine the full location.
		$template 	= 'themes:/apps/' . $this->app->group . '/' . $this->app->element . '/' . $tpl;

		return $this->theme->output( $template );
	}

	public function set( $key , $value = null )
	{
		return $this->theme->set( $key , $value );
	}
}

/**
 * Main class file that should be extended by application widgets.
 *
 * @since	1.0
 * @access	public
 */
class SocialAppsWidgets extends SocialAppsAbstract
{
	// Stores a list of already initiated models.
	protected $models 	= array();

	// Stores a list of default paths for this app.
	protected $paths 	= array();


	/**
	 * The current view's name.
	 * @var	string
	 */
	protected $viewName	= '';

	public function __construct( SocialTableApp $app , $viewName )
	{
		// The ORM for the app.
		$this->app 		= $app;

		$this->group 	= $app->group;
		$this->element 	= $app->element;

		// Set the view's name.
		$this->viewName	= $viewName;

		$this->paths 	= array(
								'models'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/models',
								'tables'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/tables',
								'views'		=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/views',
								'config'	=> SOCIAL_APPS . '/' . $app->group . '/' . $app->element . '/config',
							);


		// Allow themes to be available to the caller.
		$this->theme 	= FD::themes();

		// Allow app to be available in the theme.
		$this->set( 'app'	, $app );
	}

	/**
	 * Retrieves a JTable object. This simplifies the caller from manually adding include path all the time.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$name		The table's name without the prefix.
	 * @param	string	$prefix		Optional prefixed table name.
	 *
	 * @return	JTable				The JTable object.
	 */
	public function getTable( $name , $prefix = '' )
	{
		JTable::addIncludePath( $this->paths[ 'tables' ] );

		$prefix	= empty( $prefix ) ? ucfirst( $this->app->element ) . 'Table' : $prefix;

		$table	= JTable::getInstance( $name , $prefix );

		return $table;
	}

	/**
	 * Helper function to assist child classes to retrieve a model object.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param 	string 	$modelName 	The name of the model.
	 **/
	public function getModel( $name )
	{
		if( !isset( $this->models[ $name ] ) )
		{
			$className	= $name . 'Model';

			if( !class_exists( $className ) )
			{
				jimport( 'joomla.application.component.model' );

				// @TODO: Properly test if the file exists before including it.
				JLoader::import( strtolower( $name ) , $this->paths[ 'models' ] );
			}

			// If the class still doesn't exist, let's just throw an error here.
			if( !class_exists( $className ) )
			{
				JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

				return JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_MODEL_NOT_FOUND' , $className ) );
			}

			$model 					= new $className( $name );
			$this->models[ $name ]	= $model;
		}

		return $this->models[ $name ];
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @access	public
	 * @param	string	$uri 	The raw uri string.
	 * @param	boolean	$route	Whether or not the uri should be routed
	 */
	public function redirect( $uri , $route = true )
	{
		if( $route )
		{
			// Since redirects does not matter of the xhtml codes, we can just ignore this.
			$uri    = FRoute::_( $uri , false );
		}

		$this->app->redirect( $uri );
		$this->app->close();
	}

	/**
	 * Main method to help caller to display contents from their theme files.
	 * The method automatically searches for {%APP_NAME%/themes/%CURRENT_THEME%/%FILE_NAME%}
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The template file name.
	 * @return
	 */
	public function display( $tpl = null , $docType = null )
	{
		$format		= JRequest::getWord( 'format' , 'html' );

		// Since the $tpl now only contains the name of the file, we need to be smart enough to determine the full location.
		$template 	= 'themes:/apps/' . $this->app->group . '/' . $this->app->element . '/' . $tpl;

		return $this->theme->output( $template );
	}

	/**
	 * Retrieves the user params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserParams( $userId )
	{
		$map 	= FD::table( 'AppsMap' );
		$map->load( array( 'app_id' => $this->app->id , 'uid' => $userId ) );

		$registry	= FD::registry( $map->params );

		return $registry;
	}

	public function set( $key , $value = null )
	{
		return $this->theme->set( $key , $value );
	}
}
