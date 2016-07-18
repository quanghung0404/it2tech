<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyDiscussThemes extends EasyDiscuss
{
	/**
	 * Stores the template variables
	 * @var	Array
	 */
	public $vars = array();

	/**
	 * Theme params
	 * @var	bool
	 */
	public $params = null;

	/**
	 * Determines if this view is for the admin view
	 *
	 * @param	object
	 */
	public $admin = false;

	/**
	 * Holds the current view object
	 *
	 * @param	object
	 */
	public $view = null;

	public function __construct($overrideTheme = null, $options = array())
	{
		parent::__construct();

		// Determine if this is an admin location
		if (isset($options['admin']) && $options['admin']) {
			$this->admin = true;
		}

		// Determine the configured theme
		$theme = $this->config->get('layout_site_theme', $overrideTheme);

		// @TODO: Remove this
		// $theme = 'wireframe';

		$this->defaultTheme = 'wireframe';

		if ($theme == 'simplistic') {
			$theme = $this->defaultTheme;
		}

		$this->theme = $theme;

		// If a view is provided into the theme, the theme files could call methods from a view
		if (isset($options['view']) && is_object($options['view'])) {
			$this->view = $options['view'];
		}

		$obj = new stdClass();
		$this->config = ED::config();
		$this->my = JFactory::getUser();
		$this->acl = ED::acl();
		$this->profile = ED::user($this->my->id);
		$this->jconfig = ED::jConfig();
	}

	/**
	 * Allows caller to set a custom theme
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setCategoryTheme($theme)
	{
		$this->categoryTheme = $theme;
	}

	/**
	 * Resolves a given namespace to the appropriate path
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function resolve($namespace='', $checkOverridden = true)
	{
		$parts = explode('/', $namespace);
		$location = $parts[0];
		$path = '';
		$extension = '.php';

		// Remove the first porition from the exploded parts since we don't need this
		unset($parts[0]);

		// For admin theme files
		if ($location == 'admin') {
			$path = DISCUSS_ADMIN_THEMES . '/default/' . implode('/', $parts);

			return $path;
		}

		// For site theme folders
		// Implode the parts back to form the namespace
		$namespace = implode('/', $parts);

		if ($checkOverridden) {
			// Overriden Theme
			$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}
		}

		// Current Theme
		$path = DISCUSS_THEMES . '/' . $this->theme . '/' . $namespace;
		$exists = JFile::exists($path . $extension);

		if (!$exists) {

			// lets fall back to wireframe theme
			$path = DISCUSS_THEMES . '/' . $this->defaultTheme . '/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if (! $exists) {
				return JError::raiseError(500, JText::sprintf('The file you requested for does not exists, %1$s', $path . $extension));
			}

		}

		return $path;
	}

	/**
	 * Retrieves the path to the current theme.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPath()
	{
		$theme 	= (string) trim(strtolower($this->theme));

		return EBLOG_THEMES . '/' . $theme;
	}


	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		foreach ($modules as $module) {
			$theme = EB::template();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/modules/item');
		}

		return $buffer;
	}

	/**
	 * Retrieves the document direction. Whether this is rtl or ltr
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDirection()
	{
		$document	= JFactory::getDocument();
		return $document->getDirection();
	}

	/**
	 * Converts the entire output into a JSON object.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toJSON()
	{
		$output = json_encode($this->vars);

		return $output;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getNouns($text , $count , $includeCount = false )
	{
		return ED::string()->getNoun($text, $count, $includeCount);
	}

	public function getParam( $key , $default = null )
	{
		return $this->params->get( $key , $default );
	}

	/**
	 * Sets a variable on the template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Retrieves the theme's name.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getName()
	{
		return $this->theme;
	}

	/**
	 * New method to display contents from template files
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function output($namespace, $vars = array(), $extension = 'php')
	{
		$path = $this->resolve($namespace);
		$extension = '.' . $extension;

		// Extract internal vars first
		if (isset($this->vars)) {
			extract($this->vars);
		}

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}


		$templateFile = $path . $extension;
		$templateContent = '';

		ob_start();
			include($templateFile);
			$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script for template
		$scriptFile = $path . '.js'; // the path might now pointing to override folder. we will need to check if this js file exist or not. if not, let get the non-override path.

		if (! JFile::exists($scriptFile)) {
			// we will need to point the folder to non-overridden path
			$tmppath = $this->resolve($namespace, false);
			$scriptFile = $tmppath . '.js';
		}


		// Generate a uid for the script file
		$uid = md5($scriptFile);

		// Check if the script already exists on the namespace so we don't run it multiple times
		$exists = ED::scripts()->exists($uid);

		if (!$exists) {

			$scriptFileExists = JFile::exists($scriptFile);

			if ($scriptFileExists) {

				ob_start();

					if ($this->config->get('system_environment') == 'development') {
						echo '<script type="text/javascript" data-src="' . $scriptFile . '">';
					} else {
						echo '<script type="text/javascript">';
					}

					include($scriptFile);
					echo '</script>';
					$scriptContent = ob_get_contents();
				ob_end_clean();

				// Add to collection of scripts
				if ($this->doc->getType() == 'html') {
					ED::scripts()->add($uid, $scriptContent);
				} else {

					// Append script to template content
					// if we're not on html document (ajax).
					$templateContent .= $scriptContent;
				}
			}
		}


		return $templateContent;
	}

	/**
	 * Retrieves the images path for the current template
	 *
	 * @since	4.0
	 * @access	public
	 * @return	string	The absolute URI to the images path
	 */
	public function getPathUri($location)
	{
		if ($this->admin) {

			$path = rtrim(JURI::root(), '/') . '/administrator/components/com_easyblog/themes/default/' . ltrim($location, '/');

			return $path;
		}
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
		$helper = explode('.', $namespace);
		$helperName	= $helper[0];
		$methodName	= $helper[1];

		$file = dirname(__FILE__) . '/helpers/' . strtolower($helperName) . '.php';

		// Remove the first 2 arguments from the args.
		$args = func_get_args();
		$args = array_splice( $args , 1 );

		include_once($file);

		$class = 'EasyDiscussThemesHelper' . ucfirst($helperName);

		if (!method_exists($class, $methodName)) {
			return false;
		}

		return call_user_func_array(array($class, $methodName), $args);
	}

	public function __call($method, $args)
	{
		if (is_null($this->view)) {
			return false;
		}

		if (!method_exists($this->view, $method)) {
			return false;
		}

		return call_user_func_array(array($this->view, $method), $args);
	}

	/**
	 * Escapes a string
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function escape($val)
	{
		return ED::string()->escape($val);
	}
}
