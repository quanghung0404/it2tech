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

// Import joomla's filesystem library.
jimport( 'joomla.filesystem.file' );

class SocialTemplate
{
	/**
	 * Stores the template variables
	 * @var array
	 */
	public $vars = array();

	public $file = '';

	/**
	 * Stores the current extension
	 * @var array
	 */
	public $extension = 'php';

	/**
	 * Stores the access for the current user
	 * @var SocialAccess
	 */
	static $userAccess	= null;

	static $user 		= null;
	static $tmplMode	= null;

	/**
	 * Stores the access for the current user
	 * @var SocialAccess
	 */
	static $templateConfig	= null;

	public function __construct()
	{
		// Define Joomla's app
		$this->app 		= JFactory::getApplication();
		$this->input 	= $this->app->input;

		// Load configuration object.
		$config 	= FD::config();
		$jConfig 	= FD::jconfig();

		// Define the current logged in user or guest
		if (is_null(self::$user)) {
			self::$user 	= FD::user();
		}

		// Define the current logged in user's access.
		if (is_null(self::$userAccess)) {
			self::$userAccess	= FD::access();
		}

		if (is_null(self::$tmplMode)) {
			self::$tmplMode 	= $this->input->get('tmpl', '', 'default');
		}

		// Get the current access
		$this->my 			= self::$user;
		$this->access		= self::$userAccess;

		// Define our own configuration
		$this->config 		= $config;

		// Define template's own configuration
		if (is_null(self::$templateConfig)) {
			self::$templateConfig	= $this->getConfig();
		}

		$this->template 	= self::$templateConfig;

		// Define Joomla's configuration so the world can use it.
		$this->jConfig		= $jConfig;

		// Determine if the current request has tmpl=xxx
		$this->tmpl = self::$tmplMode;
	}

	/**
	 * Retrieves the template configuration
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getConfig()
	{
		static $cached = array();

		// Get the current configured theme
		$element 	= $this->config->get('theme.site');

		// Retrieve the user's theme.
		$profile 	= $this->my->getProfile();

		if ($profile) {

			$params 	= $profile->getParams();
			$theme 		= $params->get('theme');

			if($theme) {
				$element 	= $theme;
			}
		}

		if (!isset($cached[$element]) && empty($cached[$element])) {
			$model 			= FD::model('Themes');
			$defaultParams 	= $model->getDefaultParams($element);

			$params 		= $model->getParams($element);
			$defaultParams->mergeObjects($params);

			$cached[$element]	= $defaultParams;
		}

		return $cached[$element];
	}


	/**
	 * Returns the metadata of a template file.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$tpl	The name of the file. Example: themes:/dashboard/default
	 * @return	stdClass
	 */
	public function getTemplate($namespace = null)
	{
		// Explode the namespace
		$parts = explode(':', $namespace);

		// Legacy fixes.
		$hasProtocol = count( $parts ) > 1 ? true : false;

		if (!$hasProtocol) {
			$namespace = 'themes:/' . $namespace;
		}

		$template = new stdClass();
		$template->file = FD::resolve($namespace . '.' . $this->extension);
		$template->script = FD::resolve($namespace . '.js');

		return $template;
	}

	/**
	 * This is the factory method to ensure that this class is always created all the time.
	 * Usage: FD::get( 'Template' );
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public static function factory()
	{
		return new self();
	}

	/**
	 * Assigns a value into the vars data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set( $key , $value )
	{
		$this->vars[ $key ]	= $value;

		return $this;
	}

	/**
	 * Similar to loadTemplate but the only difference is that the variables are shared with $this->vars
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function includeTemplate( $path , $vars = array() )
	{
		if (count($vars) > 0) {
			foreach ($vars as $key => $value) {
				$this->set($key, $value);
			}
		}

		return $this->output($path);
	}

	/**
	 * Given a set of arguments, load a new template file and only extracted vars would be available in this scope.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The template's file name.
	 * @param	Array
	 * @return
	 */
	public function loadTemplate($path, $vars = array())
	{
		$data 	= array();

		if (count($vars) > 0) {
			foreach ($vars as $key => $value) {
				$data[$key]	= $value;
			}
		}

		return $this->output($path, $data);
	}

	/**
	 * Outputs the data from a template file.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		Path to template.
	 * @param	Array		An array of arguments.
	 *
	 * @return	string		The output of the theme file.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function output( $path = null , $vars = null )
	{
		// Keep original file value
		if (!is_null($path)) {
			$_file = $this->file;
			$this->file = FD::resolve($path . '.' . $this->extension);
		}

		// Let's try to extract the data.
		$output = $this->parse($vars);

		// Restore original file value
		if (!is_null($path)) {
			$this->file = $_file;
		}

		// Free up some memory
		unset($_file);

		return $output;
	}

	/**
	 * Cleaner extract method. All variables that are set in $this->vars would be extracted within this scope only.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of arguments to supply to the theme file.
	 *
	 * @return	string	The template contents.
	 */
	public function parse($vars = null)
	{
		ob_start();

		// If argument is passed in, we only want to load that into the scope.
		if (is_array($vars)) {
			extract($vars);
		} else {
			// Extract variables that are available in the namespace
			if(!empty($this->vars)) {
				extract($this->vars);
			}
		}

		// Magic happens here when we include the template file.
		include($this->file);

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


	/**
	 * Template helper
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The name of the method.
	 * @return	mixed
	 */
	public function html($namespace)
	{
		static $namespaces = array();

		$helper		= explode( '.' , $namespace );
		$helperName	= $helper[ 0 ];
		$methodName	= $helper[ 1 ];
		$class 		= 'ThemesHelper' . ucfirst( $helperName );

		if (!isset($namespaces[$namespace]) && !class_exists($class)) {

			$file  = dirname( __FILE__ ) . '/helpers/' . strtolower( $helperName ) . '.php';

			include($file);

			$namespaces[$namespace]	= true;
		}

		// Remove the first 2 arguments from the args.
		$args	= func_get_args();
		$args	= array_splice($args, 1);

		return call_user_func_array( array( $class , $methodName ) , $args );
	}

	/**
	 * Check if the template file exist
	 *
	 * @since	1.1
	 * @access	public
	 * @param	string		The namespace of the file
	 * @return	boolean
	 *
	 */
	public function exists($namespace, $type = 'file')
	{
		$template = $this->getTemplate( $namespace );

		return !empty( $template->$type ) && JFile::exists( $template->$type );
	}
}
