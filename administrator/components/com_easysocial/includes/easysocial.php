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

// Load dependencies
require_once(__DIR__ . '/dependencies.php');

class ES
{
	/**
	 * Stores all the models that are initialized.
	 * @var Array
	 */
	static private $models = array();

	/**
	 * Stores all the views that are initialized.
	 * @var Array
	 */
	static private $views = array();

	/**
	 * Initializes the necessary dependencies
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function initialize()
	{
		$cdnRoot = ES::getCdnUrl();

		if ($cdnRoot) {
			$config = ES::config();
			$passiveCdn = $config->get('general.cdn.passive', false);
			FD40_FoundryFramework::defineComponentCDNConstants('EASYSOCIAL', $cdnRoot, $passiveCdn);
		}
	}

	/**
	 * Alias to JText::_
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function _($string, $escape = false)
	{
		$string = JText::_($string);

		if ($escape) {
			$string = FD::string()->escape($string);
		}

		return $string;
	}

	/**
	 * Checks if Foundry folder really exists on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function exists()
	{
		static $exists 	= null;

		if (is_null($exists)) {
			// Check if foundry folder exists since we require it.
			$path	= SOCIAL_FOUNDRY;
			$exists = true;

			jimport('joomla.filesystem.folder');

			if (!JFolder::exists($path)) {
				$exists	= false;
			}

		}

		return $exists;
	}

	/**
	 * Singleton for every other classes. It is responsible to return whatever
	 * necessary to perform a proper chaining
	 *
	 * @param	string	$item		Defines what item this method should load
	 * @param	boolean	$forceNew	Tells method whether it is necessary to create a new copy of the object.
	 **/
	public static function getInstance($item = '')
	{
		static $objects	= array();

		// We always want lowercased items.
		$item				= strtolower($item);

		$path				= SOCIAL_LIB . '/' . $item . '/' . $item . '.php';
		$objects[ $item ]	= false;

		// We shouldn't add file checks here because it greatly slows down the script.
		// The caller should know what's it doing.
		include_once($path);
		$class				= 'Social' . ucfirst($item);

		if (class_exists($class)) {
			$args	= func_get_args();

			// We do array_shift instead of unset($args[0]) to prevent using array_values to reset the index of the array, and also to maintain the reference
			array_shift($args);

			if (method_exists($class, 'getInstance')) {
				$objects[$item]	= call_user_func_array(array($class, 'getInstance'), $args);
			}
		}

		return $objects[$item];
	}

	/**
	 * Magic method to load static methods
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function __callStatic($name, $arguments)
	{
		// Load the library first
		ES::load($name);

		$className = 'Social' . ucfirst($name);

		if (method_exists($className, 'factory')) {
			$object = call_user_func_array(array($className, 'factory'), $arguments);

			return $object;
		}


		$object = new $className;



		return $object;
	}

	/**
	 * Loads a library
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function load($library)
	{
		// We do not need to use JString here because files are not utf-8 anyway.
		$library = strtolower($library);
		$obj = false;

		$path = SOCIAL_LIB . '/' . $library . '/' . $library . '.php';

		include_once($path);
	}

	/**
	 * This is a simple wrapper method to access a particular library in EasySocial. This method will always
	 * instantiate a new class based on the given class name.
	 *
	 * @param	string	$item		Defines what item this method should load
	 **/
	public static function get($lib = '')
	{
		// Try to load up the library
		self::load($lib);

		$class = 'Social' . ucfirst($lib);

		$args = func_get_args();

		// Remove the first argument because we know the first argument is always the library.
		if (isset($args[0])) {
			unset($args[0]);
		}

		return FD::factory($class, $args);
	}

	/**
	 * Creates a new object given the class.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function factory( $class , $args = array() )
	{
		// Reset the indexes
		$args 		= array_values( $args );
		$numArgs	= count($args);

		// It's too bad that we have to write these cods but it's much faster compared to call_user_func_array
		if($numArgs < 1)
		{
			return new $class();
		}

		if($numArgs === 1)
		{
			return new $class($args[0]);
		}

		if($numArgs === 2)
		{
			return new $class($args[0], $args[1]);
		}

		if($numArgs === 3 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] );
		}

		if($numArgs === 4 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] );
		}

		if($numArgs === 5 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] );
		}

		if($numArgs === 6 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] );
		}

		if($numArgs === 7 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] , $args[ 6 ] );
		}

		if($numArgs === 8 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] , $args[ 6 ] , $args[ 7 ]);
		}

		return call_user_func_array($fn, $args);
	}

	/**
	 * Single point of entry for static calls.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The class name without prefix. E.g: (Themes)
	 * @param	string	The method name
	 * @param	Array	An array of arguments.
	 * @return
	 */
	public static function call( $className , $method , $args = array() )
	{
		// We always want lowercased items.
		$item 	= strtolower($className);
		$obj	= false;

		$path	= SOCIAL_LIB . '/' . $item . '/' . $item . '.php';

		require_once($path);

		$class	= 'Social' . ucfirst( $className );

		// Ensure that $args is an array.
		$args 	= FD::makeArray( $args );

		return call_user_func_array( array( $class , $method ) , $args );
	}

	/**
	 * An alias to FD::getInstance( 'Config' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::config();
	 * echo $config->get( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function config( $key = 'site' )
	{
		// Load config library
		FD::load('config');

		$config 	= SocialConfig::getInstance($key);

		return $config;
	}

	/**
	 * An alias to FD::getInstance( 'Config' , 'joomla' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::jconfig();
	 * echo $config->getValue( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function jconfig()
	{
		// Load config library
		FD::load('config');

		$config 	= SocialConfig::getInstance('joomla');

		return $config;
	}

	/**
	 * An alias to FD::getInstance( 'Config' , 'joomla' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::jconfig();
	 * echo $config->getValue( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function storage( $type = 'joomla' )
	{
		return FD::get( 'Storage' , $type );
	}

	/**
	 * An alias to FD::getInstance( 'Config' , 'joomla' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::jconfig();
	 * echo $config->getValue( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function tag($uid = null, $type = null)
	{
		$tag = FD::get('Tag', $uid, $type);

		return $tag;
	}

	/**
	 * An alias to FD::getInstance( 'Config' , 'joomla' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::jconfig();
	 * echo $config->getValue( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function fields($params = array())
	{
		FD::load('Fields');

		$fields = SocialFields::getInstance($params);

		return $fields;
	}

	/**
	 * An alias to FD::getInstance( 'Router' , 'profile' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $config 	= FD::jconfig();
	 * echo $config->getValue( 'some.value' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function router($view)
	{
		FD::load('Router');

		$router = SocialRouter::getInstance($view);

		return $router;
	}


	/**
	 * An alias to FD::get( 'Migrators' )
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $mailer 	= FD::mailer();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function migrators( $extension )
	{
		return FD::get( 'Migrators' , $extension );
	}

	/**
	 * Helper for checking valid tokens
	 *
	 * Example:
	 * <code>
	 * <?php
	 * FD::checkToken();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableConfig	Configuration object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function checkToken()
	{
		JRequest::checkToken('request') or die('Invalid Token');
	}


	/**
	 * Includes a file given a particular namespace in POSIX format.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$file		Eg: admin:/includes/model will include /administrator/components/com_easysocial/includes/model.php
	 * @return	boolean				True on success false otherwise
	 */
	public static function import( $namespace )
	{
		static $locations	= array();

		if( !isset( $locations[ $namespace ] ) )
		{
			// Explode the parts to know exactly what to lookup for
			$parts		= explode( ':' , $namespace );

			// Non POSIX standard.
			if( count( $parts ) <= 1 )
			{
				return false;
			}

			$base 		= $parts[ 0 ];

			switch( $base )
			{
				case 'admin':
					$basePath	= SOCIAL_ADMIN;
				break;
				case 'themes':
					$basePath	= SOCIAL_THEMES;
				break;
				case 'apps':
					$basePath	= SOCIAL_APPS;
				break;
				case 'fields':
					$basePath	= SOCIAL_FIELDS;
				break;
				case 'site':
				default:
					$basePath	= SOCIAL_SITE;
				break;
			}

			// Replace / with proper directory structure.
			$path 		= str_ireplace( '/' , DIRECTORY_SEPARATOR , $parts[ 1 ] );

			// Get the absolute path now.
			$path 		= $basePath . $path . '.php';

			// Include the file now.
			include_once( $path );

			$locations[ $namespace ]	= true;
		}

		return true;
	}

	/**
	 * Alias for FD::getInstance( 'Apps' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax 	The ajax library.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function apps()
	{
		return FD::getInstance( 'apps' );
	}

	/**
	 * Retrieves the CDN URL
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCdnUrl()
	{
		$config = FD::config();

		// Override with CDN settings.
		if( !$config->get( 'general.cdn.enabled' , false ) )
		{
			return false;
		}

		if( !$config->get( 'general.cdn.url' , '' ) )
		{
			return false;
		}

		$url = $config->get( 'general.cdn.url' );

		return $url;
	}

	public static function stylesheet($location, $name=null, $useOverride=false)
	{
		return FD::get( 'Stylesheet', $location, $name, $useOverride);
	}

	/**
	 * Alias for FD::getInstance( 'Dispatcher' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax 	The ajax library.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function dispatcher()
	{
		return FD::getInstance( 'Dispatcher' );
	}

	/**
	 * Alias for FD::get( 'Uploader' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax 	The ajax library.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function uploader( $options=array() )
	{
		return FD::get( 'Uploader', $options );
	}

	/**
	 * Alias for FD::getInstance( 'Ajax' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax 	The ajax library.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function ajax()
	{
		return FD::getInstance('Ajax');
	}

	/**
	 * Intelligent method to determine if the string uses plural or singular.
	 *
	 * @param	string		$string		The language string
	 * @param	integer	 	$count		Use 0 for singular
	 * @param	boolean		$useCount	True for counting string
	 *
	 * @return	string
	 */
	public static function text($string, $count, $useCount = true)
	{
		$count = (int) $count;

		// @TODO: Make singular and plural configurable.
		if ($count <= 1) {
			$string .= '_SINGULAR';
		}

		if ($count > 1) {
			$string .= '_PLURAL';
		}

		if ($useCount) {
			return JText::sprintf($string, $count);
		}

		return JText::_($string);
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
	public static function table($name, $prefix = 'SocialTable')
	{
		$table = SocialTable::getInstance($name, $prefix);

		return $table;
	}

	/**
	 * Retrieves the view object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The view's name.
	 * @param	bool	True for back end , false for front end.
	 */
	public static function view( $name , $backend = true )
	{
		$className 	= 'EasySocialView' . ucfirst( $name );

		if( !isset( self::$views[ $className ] ) || ( !self::$views[ $className ] instanceof EasySocialView ) ) {

			if (!class_exists($className)) {
				$path  = $backend ? SOCIAL_ADMIN : SOCIAL_SITE;
				$doc   = JFactory::getDocument();
				$path .= '/views/' . strtolower( $name ) . '/view.' . $doc->getType() . '.php';

				if (!JFile::exists($path)) {
					return false;
				}

				// Include the view
				require_once($path);
			}

			if (!class_exists($className)) {
				JError::raiseError( 500 , JText::sprintf( 'View class not found: %1s' , $className ) );
				return false;
			}

			self::$views[ $className ]	= new $className( array() );
		}

		return self::$views[ $className ];
	}

	/**
	 * Retrieves a model object.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param 	string 	$modelName 	The name of the model.
	 **/
	public static function model( $name , $config = array() )
	{
		// $cacheId 	= !empty( $config ) ? md5( $name . implode( $config ) ) : md5( $name );

		// Cache by model name only because implode config may causes unexpected result
		// $a = array( 'a' => true );
		// $b = array( 'b' => true );
		// Both case have the same result of implode
		$cacheId	= strtolower($name);

		if (!isset(self::$models[$cacheId])) {

			$className	= 'EasySocialModel' . ucfirst($name);

			if (!class_exists($className)) {
				// Include the model file. This is much quicker than doing JLoader::import
				$path 	= SOCIAL_MODELS . '/' . strtolower($name) . '.php';
				require_once( $path );
			}

			// If the class still doesn't exist, let's just throw an error here.
			if (!class_exists($className)) {
				return JError::raiseError(500, JText::sprintf('COM_EASYSOCIAL_MODEL_NOT_FOUND', $className) );
			}

			$model 	= new $className($config);

			self::$models[ $cacheId ]	= $model;
		}

		// Forcefully run initState here instead of construct in the model because the same model might be used more than once in different states
		if (!empty($config['initState'] ) ) {
			self::$models[$cacheId]->initStates();
		}

		return self::$models[$cacheId];
	}

	/**
	 * This should be triggered when certain pages are not found in the system.
	 * Particularly when certain id does not exist on the system.
	 *
	 */
	public static function show404()
	{
		// @TODO: Log some errors here.
		echo 'some errors here';
	}

	/**
	 * Shows a layout that the user has no access to the particular item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function showNoAccess($message)
	{
		echo $message;
	}

	/**
	 * Sets some callback data into the current session
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setCallback( $data )
	{
		$session		= JFactory::getSession();

		// Serialize the callback data.
		$data 			= serialize( $data );

		// Store the profile type id into the session.
		$session->set( 'easysocial.callback' , $data , SOCIAL_SESSION_NAMESPACE );
	}

	/**
	 * Retrieves stored callback data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCallback()
	{
		$session 		= JFactory::getSession();
		$data 			= $session->get( 'easysocial.callback' , '' , SOCIAL_SESSION_NAMESPACE );

		$data 			= unserialize( $data );

		// Clear off the session once it's been picked up.
		$session->clear( 'easysocial.callback' , SOCIAL_SESSION_NAMESPACE );

		return $data;
	}

	/**
	 * Renders a login page if necessary. If this is called via an ajax method, it will trigger a dialog instead.
	 *
	 * @access	public
	 * @param	null
	 * @return	string	Contents.
	 */
	public static function requireLogin()
	{
		$doc = JFactory::getDocument();
		$my = ES::user();

		// User is logged in, allow them to proceed
		if (!$my->guest) {
			return true;
		}

		$docType = $doc->getType();

		if ($docType == 'html') {

			$message = new stdClass();
			$message->message = JText::_('COM_EASYSOCIAL_PLEASE_LOGIN_FIRST');
			$message->type = SOCIAL_MSG_INFO;

			$info = ES::info();
			$info->set($message);

			// Set the current url as the callback
			$callback = FRoute::current();
			ES::setCallback($callback);

			// Create the login url
			$url = FRoute::login(array(), false);

			$app = JFactory::getApplication();
			return $app->redirect($url);
		}

		if ($docType == 'ajax') {
			$ajax = ES::ajax();

			// Get any referrer
			$callback = FRoute::referer();

			if ($callback) {
				ES::setCallback($callback);
			}

			$ajax->script('EasySocial.login();');

			return $ajax->send();
		}
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	1.0
	 * @param	mixed	An object or string.
	 * @param	string	If a delimeter is provided for string, use that as delimeter when exploding.
	 * @return	Array	Converted into an array.
	 */
	public static function makeArray( $item , $delimeter = null )
	{
		// If this is already an array, we don't need to do anything here.
		if( is_array( $item ) )
		{
			return $item;
		}

		// Test if source is a SocialRegistry/JRegistry object
		if ($item instanceof SocialRegistry || $item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if( is_object( $item ) )
		{
			return JArrayHelper::fromObject( $item );
		}

		if( is_integer( $item ) )
		{
			return array( $item );
		}

		// Test if source is a string.
		if( is_string( $item ) )
		{
			if( $item == '' )
			{
				return array();
			}

			// Test for comma separated values.
			if( !is_null( $delimeter ) && stristr( $item , $delimeter) !== false )
			{
				$data 	= explode( $delimeter , $item );

				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));
			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return FD::json()->decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = FD::json()->decode($item);

				return JArrayHelper::fromObject($result);
			}

			return array( $item );
		}

		return false;
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	1.0
	 * @param	mixed	$item		An object or string.
	 * @return	Array	$result		Converted into an array.
	 */
	public static function makeObject( $item, $debug = false )
	{
		// If this is already an object, skip this
		if( is_object( $item ) )
		{
			return $item;
		}

		if( is_array( $item ) )
		{
			return (object) $item;
		}

		if( strlen( $item ) < 1024 && is_file( $item ) )
		{
			jimport( 'joomla.filesystem.file' );

			$item	= JFile::read( $item );
		}

		$json 	= FD::json();

		// Test if source is a string.
		if( $json->isJsonString( $item ) )
		{

			if ($debug) {
				$obj 	= $json->decode( $item );
				var_dump($item, $obj);
				exit;
			}

			// Trim the string first
			$item = trim( $item );

			$obj 	= $json->decode( $item );

			if( !is_null( $obj ) )
			{
				return $obj;
			}

			$obj 	= new stdClass();

			return $obj;
		}

		return false;
	}

	/**
	 * Converts an array to string
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function makeString( $val , $join = '' )
	{
		if( is_string( $val ) )
		{
			return $val;
		}

		return implode( $join , $val );
	}

	/**
	 * Converts an argument into a json string. If argument is a string, it wouldn't be processed.
	 *
	 * @since	1.0
	 * @param	mixed	An object or array.
	 * @return	string	Converted into a json string.
	 */
	public static function makeJSON( $item )
	{
		if( is_string( $item ) )
		{
			return $item;
		}

		$json 	= FD::json();

		$data 	= $json->encode( $item );

		return $data;
	}

	/**
	 * Parses a csv file to array of data
	 *
	 * @since	1.0.1
	 * @param	string	Filename to parse
	 * @return	Array	Arrays of the data
	 */
	public static function parseCSV( $file, $firstRowName = true, $firstColumnKey = true )
	{
		if( !JFile::exists( $file ) )
		{
			return array();
		}

		$handle = fopen( $file, 'r' );

		$line = 0;

		$columns = array();

		$data = array();

		while( ( $row = fgetcsv( $handle ) ) !== false )
		{
			if( $firstRowName && $line === 0 )
			{
				$columns = $row;
			}
			else
			{
				$tmp = array();

				if( $firstRowName )
				{
					foreach( $row as $i => $v )
					{
						$tmp[$columns[$i]] = $v;
					}
				}
				else
				{
					$tmp = $row;
				}

				if( $firstColumnKey )
				{
					if( $firstRowName )
					{
						$data[$tmp[$columns[0]]] = $tmp;
					}
					else
					{
						$data[$tmp[0]] = $tmp;
					}
				}
				else
				{
					$data[] = $tmp;
				}
			}

			$line++;
		}

		fclose( $handle );

		return $data;
	}

	/**
	 * Resolve a given POSIX path.
	 *
	 * <code>
	 * <?php
	 * // This would translate to administrator/components/com_easysocial/themes/CURRENT_THEME/users/default.php
	 * FD::resolve( 'themes:/admin/users/default' );
	 *
	 * // This would translate to components/com_easysocial/themes/CURRENT_THEME/dashboard/default.php
	 * FD::resolve( 'themes:/site/dashboard/default' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The posix path to lookup for.
	 * @return	string		The translated path
	 */
	public static function resolve( $path )
	{
		if (strpos($path, ':/') === false) {
			return false;
		}

		$parts = explode( ':/' , $path );

		// Get the protocol.
		$protocol 	= $parts[ 0 ];

		// Get the real path.
		$path 		= $parts[ 1 ];

		switch( $protocol )
		{
			case 'modules':

				return FD::call( 'Modules' , 'resolve' , $path );

				break;
			case 'themes':
				return FD::call('Themes', 'resolve', $path);
				break;

			case 'ajax':
				return FD::call( 'Ajax' , 'resolveNamespace' , $path );
				break;

			case 'emails':
				return FD::call( 'Mailer' , 'resolve' , $path );
				break;

			case 'fields':
			case 'admin':
			case 'apps':
			case 'site':

				$key = 'SOCIAL_' . strtoupper($protocol);
				$basePath = constant($key);

				return $basePath . '/' . $path;
				break;
		}

		return false;
	}

	/**
	 * Alias for FD::getInstance( 'Page' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialPage
	 */
	public static function page()
	{
		return FD::getInstance( 'Page' );
	}

	/**
	 * Alias for FD::getInstance( 'Explorer' )
	 *
	 * @since	1.2
	 * @access	public
	 * @return	SocialExplorer
	 */
	public static function explorer( $uid , $type )
	{
		return FD::getInstance( 'Explorer' , $uid , $type );
	}

	/**
	 * Alias for FD::getInstance( 'Document' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialDocument
	 */
	public static function document()
	{
		return FD::getInstance( 'Document' );
	}

	/**
	 * Alias for FD::getInstance( 'Profiler' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target's id.
	 * @param	string	The target's type.
	 * @param	string	The extension name.
	 * @return	SocialPrivacy
	 */
	public static function profiler()
	{
		return FD::getInstance( 'Profiler' );
	}

	/**
	 * Alias for FD::get( 'DB' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target's id.
	 * @param	string	The target's type.
	 * @param	string	The extension name.
	 * @return	SocialPrivacy
	 */
	public static function privacy($target = '', $type = SOCIAL_TYPE_USER)
	{
		return FD::get('Privacy', $target, $type);
	}


	/**
	 * Retrieves a token generated by the platform.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function token()
	{
		$version 	= FD::getInstance( 'Version' );

		if( $version->getVersion() >= '3.0' )
		{
			return JFactory::getSession()->getFormToken();
		}

		return JUtility::getToken();
	}
	/**
	 * Detects if the folder exist based on the path given. If it doesn't exist, create it.
	 *
	 * @since	1.0
	 * @param	string	$path		The path to the folder.
	 * @return	boolean				True if exists (after creation or before creation) and false otherwise.
	 */
	public static function makeFolder( $path )
	{
		jimport( 'joomla.filesystem.folder' );

		// If folder exists, we don't need to do anything
		if( JFolder::exists( $path ) )
		{
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		if( JFolder::create( $path ) )
		{
			FD::copyIndex( $path );
			return true;
		}

		return false;
	}

	/**
	 * Cleans a given string and replaces all /\ with proper directory structure DIRECTORY_SEPARATOR and removes any trailing or leading /
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The file / folder name.
	 * @return	string	Cleaned file / folder name.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function language()
	{
		static $language 	= null;

		if (is_null($language)) {
			// Try to load up the library
			FD::load('Language');


			$language = new SocialLanguage();
		}

		return $language;
	}

	/**
	 * Cleans a given string and replaces all /\ with proper directory structure DIRECTORY_SEPARATOR and removes any trailing or leading /
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The file / folder name.
	 * @return	string	Cleaned file / folder name.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function cleanPath( $value )
	{
		$value	= ltrim( $value , '\/' );
		$value	= rtrim( $value , '\/' );
		$value 	= str_ireplace( array( '\\' ,'/' ) , '/' , $value );

		return $value;
	}

	/**
	 * Alias for FD::get( 'DB' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialDB		The database layer object.
	 */
	public static function db()
	{
		FD::load('DB');

		$db 	= SocialDB::getInstance();

		return $db;
	}

	/**
	 * Alias for FD::get( 'Date' );
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function date($current = 'now' , $withoffset = true)
	{
		if( is_object( $current ) && get_class( $current ) == 'SocialDate' )
		{
			return $current;
		}

		FD::load('Date');

		$date 	= new SocialDate($current, $withoffset);

		return $date;
	}

	/**
	 * Alias for FD::get( 'User' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser		The user's object
	 */
	public static function user($ids = null, $debug = false)
	{
		// Load the user library
		self::load('User');

		return SocialUser::factory($ids, $debug);
	}

	/**
	 * Alias for FD::get( 'Mailchimp' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser		The user's object
	 */
	public static function mailchimp( $apikey )
	{
		$lib 	= FD::get( 'Mailchimp' , $apikey );

		return $lib;
	}

	/**
	 * Alias for FD::get( 'Group' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser		The user's object
	 */
	public static function group( $ids = null , $debug = false )
	{
		// Load the group library
		FD::load('group');

		if (is_null($ids)) {
			return new SocialGroup();
		}

		$state = SocialGroup::factory($ids, $debug);

		if( $state === false )
		{
			return new SocialGroup();
		}

		return $state;
	}

	/**
	 * Alias for FD::get('Event')
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Mixed		$ids	The id to load.
	 * @return	SocialEvent			The event object
	 */
	public static function event($ids = null, $debug = false)
	{
		// Load the group library
		FD::load('event');

		if (is_null($ids)) {
			return new SocialEvent();
		}

		$state = SocialEvent::factory($ids, $debug);

		if( $state === false )
		{
			return new SocialEvent();
		}

		return $state;
	}

	/**
	 * Alias for FD::get( 'User' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser		The user's object
	 */
	public static function version()
	{
		$version = ES::getInstance('Version');

		return $version;
	}

	/**
	 * Generates a blank index.html file into a specific target location.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The target location
	 * @return	bool	True if success, false otherwise.
	 */
	public static function copyIndex( $targetLocation )
	{
		$defaultLocation 	= SOCIAL_SITE . '/index.html';
		$targetLocation		= $targetLocation . '/index.html';

		jimport( 'joomla.filesystem.file' );

		// Copy the file over.
		return JFile::copy( $defaultLocation , $targetLocation );
	}

	/**
	 * Alias to FD::getInstance( 'Notification' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialNotification		The notification library.
	 */
	public static function notification()
	{
		return FD::getInstance( 'Notification' );
	}

	/**
	 * Alias to FD::getInstance( 'Badges' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialPoints	The points library
	 */
	public static function badges()
	{
		return FD::getInstance( 'Badges' );
	}

	/**
	 * Alias to FD::getInstance( 'Points' );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialPoints	The points library
	 */
	public static function points()
	{
		return FD::getInstance( 'Points' );
	}

	/**
	 * Alias method to load JSON library
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function json()
	{
		FD::load('JSON');

		$lib = SocialJSON::getInstance();

		return $lib;
	}

	/**
	 * Alias method to load info library
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function info()
	{
		$info	= FD::getInstance( 'Info' );

		return $info;
	}

	/**
	 * Shorthand method to check version
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean
	 */
	public static function isJoomla31()
	{
		$version	= FD::getInstance( 'version' );
		return $version->getVersion() >= '3.1';
	}

	public static function isJoomla30()
	{
		$version	= FD::getInstance( 'version' );
		return $version->getVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		$version	= FD::getInstance( 'version' );
		return $version->getVersion() >= '1.6' && $version->getVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		$version	= FD::getInstance( 'version' );
		return $version->getVersion() <= '1.5';
	}

	/**
	 * Generates a hash on a string.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The string to be hashed.
	 */
	public static function getHash($str)
	{
		if (ES::isJoomla30()) {
			return JApplication::getHash($str);
		}

		return JUtility::getHash($str);
	}

	public static function filelog()
	{
		$args = func_get_args();

		$now = FD::date()->toSql();

		$contents = '<h2>' . $now . '</h2><pre>';

		foreach ($args as $arg) {
			ob_start();
			var_export($arg);
			$contents .= ob_get_contents();
			ob_end_clean();
		}

		$contents .= '</pre>';

		$path = SOCIAL_TMP . '/debuglog.html';

		jimport('joomla.filesystem.file');

		if (JFile::exists($path)) {
			$original = JFile::read($path);

			$contents = $original . $contents;
		}

		JFile::write($path, $contents);
	}

	/**
	 * Alias for FD::get( 'Image' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target's id.
	 * @param	string	The target's type.
	 * @param	string	The extension name.
	 * @return	SocialPrivacy
	 */
	public static function avatar( SocialImage $image , $id = null , $type = null )
	{
		return FD::get( 'Avatar' , $image , $id , $type );
	}

	/**
	 * Alias for FD::get( 'Albums' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		An id of the album (Optional)
	 * @param	int 	The cluster id if it is associated with a cluster (Optional)
	 * @return
	 */
	public static function albums( $uid , $type = SOCIAL_TYPE_USER , $id = null )
	{
		return FD::get( 'Albums' , $uid , $type , $id );
	}

	/**
	 * Alias for FD::get( 'Photo' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id of the owner.
	 * @param	string 	The unique type of the owner.
	 * @param	int 	The cluster id if it is associated with a cluster (Optional)
	 * @return
	 */
	public static function photo( $uid , $type = SOCIAL_TYPE_USER , $id = null )
	{
		return FD::get( 'Photo' , $uid , $type , $id );
	}

	/**
	 * Retrieves the video library
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function video($uid = null, $type = null, $key = null)
	{
		$video = ES::get('Video', $uid, $type, $key);

		return $video;
	}

	/**
	 * Generates a new exception
	 *
	 * @since	1.4.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function exception($message='', $type = SOCIAL_MSG_ERROR)
	{
		return FD::get('Exception', $message, $type);
	}

	public static function math()
	{
		return FD::getInstance( 'Math' );
	}

	/**
	 * Alias for FD::get( 'Location' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialReports
	 */
	// public static function location($provider = null)
	// {
	// 	return FD::get('Location', $provider);
	// }

	/**
	 * Alias for FD::get( 'Access' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialReports
	 */
	public static function access( $userId = null, $type = SOCIAL_TYPE_USER )
	{
		// Load access library
		FD::load('Access');

		$access 	= new SocialAccess($userId, $type);

		return $access;
	}

	/**
	 * Alias for FD::getInstance( 'Opengraph' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialOpengraph
	 */
	public static function opengraph()
	{
		return FD::getInstance( 'Opengraph' );
	}

	/**
	 * Alias for FD::getInstance( 'OAuth' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target's id.
	 * @param	string	The target's type.
	 * @param	string	The extension name.
	 * @return	SocialOauth
	 */
	public static function oauth( $client = '' , $callback = '' )
	{
		return FD::getInstance( 'OAuth' , $client , $callback );
	}

	/**
	 * Alias for FD::get( 'bbcode' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target's id.
	 * @param	string	The target's type.
	 * @param	string	The extension name.
	 * @return	SocialOauth
	 */
	public static function bbcode()
	{
		FD::load('BBCode');

		$bbcode = new SocialBBCode();

		return $bbcode;
	}

	public static function callFunc( $obj , $fn , array $args = array() )
	{
		$numArgs = count($args);

		if($numArgs < 1)
		{
			return $obj->$fn();
		}

		if($numArgs === 1)
		{
			return $obj->$fn($args[0]);
		}

		if($numArgs === 2)
		{
			return $obj->$fn($args[0], $args[1]);
		}

		if($numArgs === 3 )
		{
			return $obj->$fn($args[0], $args[1] , $args[ 2 ] );
		}

		if($numArgs === 4 )
		{
			return $obj->$fn($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] );
		}

		if($numArgs === 5 )
		{
			return $obj->$fn($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] );
		}

		return call_user_func_array($fn, $args);
	}

	/**
	 * Alias for FD::get( 'Likes' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The type.
	 * @return	SocialStory
	 */
	public static function likes( $uid = null , $type = null, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $options = array() )
	{
		return FD::get( 'Likes' , $uid , $type, $verb, $group, $options );
	}

	/**
	 * Alias for FD::get( 'Story' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The type.
	 * @return	SocialStory
	 */
	public static function story( $type = '' )
	{
		return FD::get( 'Story' , $type );
	}

	/**
	 * Alias for FD::get( 'Registry' )
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The raw string.
	 * @return	SocialRegistry
	 */
	public static function registry( $raw = '' )
	{
		return FD::get( 'Registry' , $raw );
	}

	/**
	 * Alias for FD::getInstance( 'Modules' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialComments
	 */
	public static function modules( $name )
	{
		$modules 	= FD::get( 'Modules' , $name );

		return $modules;
	}

	/**
	 * Alias for FD::getInstance( 'Comments' )
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialComments
	 */
	public static function comments( $uid = null, $element = null, $verb = 'null', $group = SOCIAL_APPS_GROUP_USER, $options = array(), $useStreamId = false )
	{
		$comments = FD::getInstance( 'Comments' );

		if( !is_null( $uid ) && !is_null( $element ) )
		{
			return $comments->load( $uid, $element, $verb, $group, $options, $useStreamId );
		}

		return $comments;
	}

	public static function alert($element = null, $rulename = null)
	{
		$alert = FD::getInstance('Alert');

		if (is_null($element)) {
			return $alert;
		}

		$registry = $alert->getRegistry($element);

		if (is_null($rulename)) {
			return $registry;
		}

		return $registry->getRule($rulename);
	}

	/**
	 * Shorthand to send out notification
	 *
	 * FD::notify( 'element.rulename', array( 1, 2, 3 ) );
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean		State of sending the notification
	 */
	public static function notify($rule, $participants, $emailOptions = array(), $systemOptions = array())
	{
		$segments = explode('.', $rule);
		$element = array_shift($segments);
		$rulename = implode('.', $segments);
		$alert = ES::alert($element, $rulename);

		$arg = new stdClass();
		$arg->rule = $rule;
		$arg->participant = $participants;
		$arg->email_options = $emailOptions;
		$arg->sys_options = $systemOptions;

		$args = array(&$arg);

		$dispatcher = ES::getInstance('Dispatcher');

		// @trigger onNotificationBeforeCreate from user apps
		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onNotificationBeforeCreate', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onNotificationBeforeCreate', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onNotificationBeforeCreate', $args);

		if (!$alert) {
			return false;
		}

		$state = $alert->send($participants, $emailOptions, $systemOptions);

		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onNotificationAfterCreate', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onNotificationAfterCreate', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onNotificationAfterCreate', $args);
		
		return $state;
	}

	/**
	 * Retrieves the current version of EasySocial installed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLocalVersion()
	{
		static $version = false;

		if ($version === false) {
			$file 	= SOCIAL_ADMIN . '/easysocial.xml';

			$parser = ES::parser();
			$parser->load($file);

			$version	= $parser->xpath('version');
			$version 	= (string) $version[0];
		}


		return $version;
	}

	/**
	 * Retrieves the latest version of EasySocial from the server
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getOnlineVersion()
	{
		$connector = ES::connector();
		$connector->addUrl( SOCIAL_SERVICE_NEWS );
		$connector->connect();

		$contents	= $connector->getResult( SOCIAL_SERVICE_NEWS );

		$obj = FD::makeObject( $contents );

		if (empty($obj->version)) {
			return '';
		}

		return $obj->version;
	}

	/**
	 * Generates a default cover link
	 *
	 * @since	1.4.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getDefaultCover($group)
	{
		static $covers = array();

		if (!isset($covers[$group])) {
			$config = ES::config();

			$covers[$group] = SOCIAL_JOOMLA_URI . $config->get('covers.default.' . $group . '.' . SOCIAL_COVER_DEFAULT);

			// If there is a cover override in the template, use it instead.
			$assets = ES::assets();
			$template = $assets->getJoomlaTemplate();

			$overridePath = JPATH_ROOT . '/templates/' . $template . '/html/com_easysocial/covers/' . $group . '/default.jpg';
			$exists = JFile::exists($overridePath);
			
			if ($exists) {
				$covers[$group] = rtrim(JURI::root(), '/') . '/templates/' . $template . '/html/com_easysocial/covers/' . $group . '/default.jpg';
			}
		}

		return $covers[$group];
	}

	public static function getEnvironment()
	{
		$config = FD::getInstance( 'Configuration' );
		return $config->environment;
	}

	public static function getMode()
	{
		$config = FD::getInstance( 'Configuration' );
		return $config->mode;
	}

	/**
	 * Loads the sharing library
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function sharing($options = array())
	{
		$sharing = FD::get('Sharing', $options);

		return $sharing;
	}

	/**
	 * Synchronizes the database table columns
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function syncDB( $from = '' )
	{
		$db		= FD::db();

		return $db->sync( $from );
	}

	/**
	 * Proxy to a target URL item.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string	The url to check for.
	 * @param	string	The type of the target.
	 * @return	string	The proxied url
	 */
	public static function proxy($link, $type = 'image')
	{
		$link 	= JURI::root() . 'index.php?option=com_easysocial&view=crawler&layout=proxy&tmpl=component&type=' . $type . '&url=' . urlencode($link);

		return $link;
	}

	/**
	 * Retrieves the base URL of the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getBaseUrl()
	{
		$baseUrl	= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easysocial';


		$app	    = JFactory::getApplication();
		$config 	= FD::config();
		$uri		= JFactory::getURI();
		$language	= $uri->getVar( 'lang' , 'none' );
		$router		= $app->getRouter();
		$baseUrl	= rtrim( JURI::base() , '/' ) . '/index.php?option=com_easysocial&lang=' . $language;

		$itemId 	= JRequest::getVar( 'Itemid' ) ? '&Itemid=' . JRequest::getVar( 'Itemid' ) : '';

		if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled( "system" , "languagefilter" ) )
		{
			$rewrite	= $config->get('sef_rewrite');
			$base		= str_ireplace(JURI::root(true), '', $uri->getPath());
			$path		= $rewrite ? $base : JString::substr($base , 10);
			$path		= trim( $path , '/' );
			$parts		= explode( '/' , $path );

			if( $parts )
			{
				// First segment will always be the language filter.
				$language	= reset( $parts );
			}
			else
			{
				$language	= 'none';
			}

			if( $rewrite )
			{
				$baseUrl		= rtrim( JURI::base() , '/' ) . '/' . $language . '/?option=com_easysocial';
				$language	= 'none';
			}
			else
			{
				$baseUrl		= rtrim( JURI::base() , '/' ) . '/index.php/' . $language . '/?option=com_easysocial';
			}
		}

		return $baseUrl . $itemId;
	}

	/**
	 * Alias for FD::getInstance('maintenance')
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return SocialMaintenance    Maintenance library
	 */
	public static function maintenance()
	{
		return FD::getInstance('maintenance');
	}

	public static function checkCompleteProfile()
	{
		$config = FD::config();
		$my = FD::user();

		// If user is not registered, or no profile id, or settings is not enabled, we cannot do anything to check
		if (empty($my->id) || empty($my->profile_id) || !$config->get('user.completeprofile.required', false)) {
			return true;
		}

		$total = $my->getProfile()->getTotalFields(SOCIAL_PROFILES_VIEW_EDIT);

		$filled = $my->completed_fields;

		// Avoid using maintenance script to do this because it is possible that a site might have >1000 users
		// Using this method instead so that every user will at least get executed once during login
		// Won't happen on subsequent logins
		if (empty($filled)) {
			$fields = FD::model('Fields')->getCustomFields(array(
				'profile_id' => $my->getProfile()->id,
				'data' => true,
				'dataId' => $my->id,
				'dataType' => SOCIAL_TYPE_USER,
				'visible' => SOCIAL_PROFILES_VIEW_EDIT,
				'group' => SOCIAL_FIELDS_GROUP_USER
			));

			$args = array(&$my);
			$completedFields = FD::fields()->trigger('onProfileCompleteCheck', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
			$table = FD::table('Users');
			$table->load(array('user_id' => $my->id));
			$table->completed_fields = count($completedFields);
			$table->store();

			$filled = $table->completed_fields;
		}

		if ($total == $filled) {
			return true;
		}

		$percentage = (int) (($filled / $total) * 100);

		if ($percentage < 100) {
			$action = $config->get('user.completeprofile.action', 'info');

			if ($action === 'redirect') {
				$mainframe = JFactory::getApplication();

				$mainframe->redirect(FRoute::profile(array('layout' => 'edit')));
			}

			if ($action === 'info' || ($action === 'infoprofile' && JRequest::getVar('view') === 'profile')) {
				$incompleteMessage = JText::sprintf('COM_EASYSOCIAL_PROFILE_YOUR_PROFILE_IS_INCOMPLETE', $percentage, FRoute::profile(array('layout' => 'edit')));

				FD::info()->set(false, $incompleteMessage, SOCIAL_MSG_WARNING, 'easysocial.profilecompletecheck');
			}

			return false;
		}

		return true;
	}

	public static function dbcache($key, $options = array())
	{
		static $instances = array();

		if (!isset($instances[$key])) {
			$instances[$key] = FD::get('Dbcache', $key, $options);
		}

		return $instances[$key];
	}

	/**
	 * Alias method to return the appropriate cluster type
	 *
	 * @since  1.3
	 * @access public
	 * @param  integer/array    $id   This can be either the cluster id or array of cluster ids.
	 * @param  string           $type The cluster type.
	 * @return SocialCluster/array    The corresponding cluster class object or array of SocialCluster objects.
	 */
	public static function cluster($type, $id = null)
	{
		return call_user_func(array('ES', $type), $id);
	}

	/**
	 * Remove older javascript files
	 *
	 * @since	1.3
	 * @access	public
	 */
	public static function purgeOldVersionScripts()
	{
		// Get the current installed version
		$version = ES::getLocalVersion();

		// Ignored files
		$ignored = array('.svn', 'CVS', '.DS_Store', '__MACOSX');
		$ignored[] = 'easysocial-' . $version . '.static.min.js';
		$ignored[] = 'easysocial-' . $version . '.static.js';
		$ignored[] = 'easysocial-' . $version . '.optimized.min.js';
		$ignored[] = 'easysocial-' . $version . '.optimized.js';

		$files = JFolder::files(JPATH_ROOT . '/media/com_easysocial/scripts', 'easysocial-', false, true, $ignored);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * Purge js configuration files
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function purgeJavascriptResources()
	{
		// Purge configuration files
		$configuration = FD::getInstance('Configuration');
		$state = $configuration->purge();

		// Purge resources files
		$compiler = FD::getInstance('Compiler');
		$state = $compiler->purgeResources();
	}

	/**
	 * Alias method to return JFactory::getApplication()->input;
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2.17
	 * @access public
	 * @return JInput The JInput object instance.
	 */
	public static function input($hash = 'default')
	{
		// Possible $hash = 'default', 'get', 'post', 'server', 'files';

		$input = JFactory::getApplication()->input;

		$hash = strtolower($hash);

		if ($hash === 'default') {
			return $input;
		}

		return $input->$hash;
	}


    /**
     * Determines if SH404 is installed
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public static function isSh404Installed()
    {
        $file = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
        $enabled = false;

        if (defined('SH404SEF_AUTOLOADER_LOADED') && JFile::exists($file)) {
            require_once($file);

            if (class_exists('shRouter')) {
                $sh404Config = shRouter::shGetConfig();

                if ($sh404Config->Enabled) {
                    $enabled = true;
                }
            }
        }

        return $enabled;
    }

	/**
	 *
	 * @since	1.4.0
	 * @access	public
	 * @return	SocialCache
	 */
	public static function request()
	{
		return FD::get('Request');
	}

	public static function cache()
	{
		FD::load('Cache');
		$cache = SocialCache::getInstance();

		return $cache;
	}

}

// Backward compatibility
class FD extends ES {}
class Foundry extends ES {}

ES::initialize();
