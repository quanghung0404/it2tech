<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyBlogThemes extends EasyBlog
{
	/**
	 * Stores the template variables
	 * @var	Array
	 */
	public $vars = array();

	/**
	 * Determines if this is for the dashboard
	 *
	 * @deprecated 4.0
	 * @var	bool
	 */
	public $dashboard = false;

	/**
	 * Determines the user's selected theme.
	 *
	 * @deprecated 4.0
	 * @var	bool
	 */
	public $user_theme = '';

	/**
	 * Determines the category theme
	 * @var string
	 */
	public $categoryTheme = '';

	/**
	 * Theme params
	 * @var	bool
	 */
	public $params = null;

	/**
	 * Theme params
	 * @var	bool
	 */
	public $entryParams = null;

	/**
	 * Determines if this view is for the adminv iew
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
		$theme	= $this->config->get('layout_theme', $overrideTheme);

		// If a view is provided into the theme, the theme files could call methods from a view
		if (isset($options['view']) && is_object($options['view'])) {
			$this->view = $options['view'];
		}

		$this->theme = $theme;

		// var_dump($this->theme);

		$obj = new stdClass();
		$obj->config = EB::config();
		$obj->my = JFactory::getUser();
		$obj->admin = EB::isSiteAdmin();
		$obj->profile = EB::user();

		// lets check if current page is a blogger standalone page or not. if yes, get the blogger's theme
		$bloggerTheme = EB::getBloggerTheme();
		if ($bloggerTheme) {
			$this->theme = $bloggerTheme;
		}

		// If it's development mode, allow user to invoke in the url to change theme.
		$environment = $obj->config->get('easyblog_environment');

		if ($environment == 'development') {
			$invokeTheme = $this->input->get('theme', '', 'word');

			if ($invokeTheme) {
				$this->theme = $invokeTheme;
			}
		}

		// If this is entry view, or category view, we need to respect the theme's category
		$this->menu = $this->app->getMenu()->getActive();
		$this->params = new JRegistry();

		// If there is an active menu, try to get the menu parameters.
		if ($this->menu) {


			// Get the params prefix
			$prefix = isset($options['paramsPrefix']) ? $options['paramsPrefix'] : '';

			// Set the current parameters.
			if ($prefix) {
				$model = EB::model('Menu');
				$this->params = $model->getCustomMenuParams($this->menu->id, $this->menu->params, $prefix);
			} else {
				$this->params = $this->menu->params;
			}

			// Check the view
			$view = $this->app->input->get('view');
			$layout = $this->input->get('layout', '', 'cmd');
			
			// Check the id
			$id = $this->input->get('id', 0, 'int');

			if ($view == 'categories') {
				$this->params = EB::getMenuParams($id, 'category');
			}

			if ($view == 'tags') {
				$this->params = EB::getMenuParams($id, 'tag');
			}

			if ($view == 'blogger' && $layout == 'listings') {
				$this->params = EB::getMenuParams($id, 'blogger');
			}

			if ($this->params->get('post_image', null) == null) {
				// if this happen, we know the whatever menu item is created prior to 5.0. Lets just get the default listing options from config.
				$defaultListingParams = EB::getMenuParams('0', 'listing', $this->params);

				$defaultListingParams = $defaultListingParams->toArray();

				if ($defaultListingParams) {
					foreach($defaultListingParams as $key => $val) {
						$this->params->set($key, $val);
					}
				}
			}

			// We will just set it here from the menu when this class first get instantiate.
			// The corresponding view will have to do their own assignment if the view's templates need to access this entryParams
			$this->entryParams = $this->params;
		}

		//is blogger mode flag
		$obj->isBloggerMode	= EBR::isBloggerMode();

		$this->my = $obj->my;

		// Assign the acl
		$this->acl = EB::acl();
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
	public function resolve($namespace='', $extension='php')
	{
		$parts     = explode('/', $namespace);
		$location  = $parts[0];
		$path      = '';
		$extension = '.' . $extension;

		unset($parts[0]);

		// For admin theme files
		if ($location=='admin') {
			$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/themes/default/' . implode('/', $parts);
			return $path;
		}

		// For site theme files
		if ($location=='site') {

			// Implode the parts back to form the namespace
			$namespace = implode('/', $parts);

			// Category Theme
			if (!empty($this->categoryTheme)) {

				$path   = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $this->categoryTheme . '/' . $namespace;
				$exists = JFile::exists($path . $extension);

				if ($exists) {
					return $path;
				}
			}

			// Override Theme
			$path   = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			// Current Theme
			$path = EBLOG_THEMES . '/' . $this->theme . '/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			// Base Theme
			// We no longer inherit from other themes. All themes will fallback to the wireframe theme by default.
			$path = EBLOG_THEMES . '/wireframe/' . $namespace;
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

		// Use a standard module style if no style is provided
		if (!isset($attributes['style'])) {
			$attributes['style'] = 'xhtml';
		}
	
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

	public function getNouns($text , $count , $includeCount = false )
	{
		return EB::string()->getNoun( $text , $count , $includeCount );
	}

	public function getParam( $key , $default = null )
	{
		return $this->params->get( $key , $default );
	}

	/**
	 * Formats a date.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatDate( $format , $dateString )
	{
		$date 	= EB::call('Date', 'dateWithOffSet', array($dateString));

		return $date->format($format);
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
		static $language = false;

		if (!$language) {
			// Load language strings from back end.
			JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT . '/administrator');

			$language = true;
		}

		$helper		= explode( '.' , $namespace );
		$helperName	= $helper[ 0 ];
		$methodName	= $helper[ 1 ];

		$file 		= dirname(__FILE__) . '/helpers/' . strtolower($helperName) . '.php';

		// Remove the first 2 arguments from the args.
		$args	= func_get_args();
		$args	= array_splice( $args , 1 );

		include_once($file);

		$class 	= 'EasyBlogThemesHelper' . ucfirst( $helperName );

		if (!method_exists($class, $methodName)) {
			return false;
		}

		return call_user_func_array(array($class, $methodName), $args);
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
	public function output($namespace, $vars=array(), $extension='php')
	{
		$path = $this->resolve($namespace, $extension);
		$extension = '.' . $extension;

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}

		if (isset($this->vars)) {
			extract($this->vars);
		}

		$templateFile = $path . $extension;
		$templateContent = '';

		ob_start();
			include($templateFile);
			$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script within template
		$scriptFile = $path . '.js';
		$scriptFileExists = JFile::exists($scriptFile);

		if ($scriptFileExists) {

			if ($namespace == 'site/blogs/code') {
				return;
			}
			ob_start();
				echo '<script type="text/javascript">';
				include($scriptFile);
				echo '</script>';
				$scriptContent = ob_get_contents();
			ob_end_clean();

			// Add to collection of scripts
			if ($this->doc->getType() == 'html') {
				EB::scripts()->add($scriptContent);
			} else {

				// Append script to template content
				// if we're not on html document (ajax).
				$templateContent .= $scriptContent;
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
		return EB::string()->escape($val);
	}
}
