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

class SocialPage extends EasySocial
{
	/**
	 * Store a list of scripts on the page.
	 * @var	Array
	 */
	public $scripts = array();

	/**
	 * Store a list of inline scripts on the page.
	 * @var	Array
	 */
	public $inlineScripts = array();

	/**
	 * Store a list of stylesheets on the page
	 * @var	Array
	 */
	public $stylesheets = array();

	/**
	 * Store a list of inline style sheets on the page
	 * @var	Array
	 */
	public $inlineStylesheets = array();

	/**
	 * The title of the page.
	 * @var	Array
	 */
	public $title = null;

	/**
	 * Page will always be an instance.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getInstance()
	{
		static $obj = null;

		if (!$obj) {
			$obj = new self();
		}

		return $obj;
	}

	public function toUri( $path )
	{
		// TODO: Move this to the actual toUri
		$url = '';
		$uri = JURI::getInstance();

		// Url
		if( stristr( $path , $uri->getScheme() ) !== false )
		{
			$url = $path;
		}

		// File
		if( is_file( $source ) )
		{
			$url = FD::get('assets')->toUri( $path );
		}

		return $url;
	}

	/**
	 * We need to wrap all javascripts into a single <script> block. This helps us maintain a single object.
	 *
	 * @access	public
	 * @param 	string 	$source		The script source.
	 */
	public function addScript( $path )
	{
		$url = $this->toUri( $path );

		if( !empty($url) )
		{
			$this->scripts[] = $url;
		}
	}

	public function addInlineScript( $script )
	{
		if (!empty($script)) {
			$this->inlineScripts[] = $script;
		}
	}

	/**
	 * Internal method to build scripts to be embedded on the head or
	 * external script files to be added on the head.
	 *
	 * @access	private
	 */
	public function processScripts()
	{
		// Scripts
		if (!empty($this->scripts)) {

			foreach ($this->scripts as $script) {
				$this->doc->addScript($script);
			}
		}

		if (empty($this->inlineScripts)) {
			return;
		}

		// Inline scripts
		$script = FD::get('Script');
		$script->file = SOCIAL_MEDIA . '/head.js';
		$script->scriptTag	= true;
		$script->CDATA = true;
		$script->set('contents', implode($this->inlineScripts));
		$inlineScript = $script->parse();

		if ($this->doc->getType() == 'html') {
			$this->doc->addCustomTag($inlineScript);
		}
	}

	public function addStylesheet( $path )
	{
		$url = $this->toUri( $path );

		if ( !empty($url) )
		{
			$this->stylesheets[] = $url;
		}
	}

	public function addInlineStylesheet( $stylesheet )
	{
		if( !empty($stylesheet) )
		{
			$this->inlineStylesheets[] = $stylesheet;
		}
	}

	public function processStylesheets()
	{
		// Stylesheets
		if (!empty($this->stylesheets)) {
			foreach ($this->stylesheets as $stylesheet) {
				$this->doc->addStyleSheet($stylesheet);
			}
		}

		if (empty($this->inlineStylesheets)) {
			return;
		}

		// Inline scripts
		$stylesheet = FD::get('Style');
		$stylesheet->file = SOCIAL_MEDIA . '/head.css';
		$stylesheet->styleTag = true;
		$stylesheet->CDATA = true;
		$stylesheet->set('contents', implode($this->inlineStylesheets));

		$inlineStylesheet = $stylesheet->parse();

		if ($this->doc->getType() == 'html') {
			$this->doc->addCustomTag($inlineStylesheet);
		}

	}

	/**
	 * Gets the current title and sets the title on the page.
	 *
	 * @access	private
	 * @param	null
	 */
	private function processTitle()
	{
		// We do not want to set the title for admin area.
		if ($this->app->isAdmin()) {
			return;
		}

		if ($this->title) {
			$this->doc->setTitle($this->title);
		}
	}

	/**
	 * This is the starting point of the page library.
	 *
	 * @access	public
	 * @param	null
	 * @return 	null
	 */
	public function start()
	{
		// Trigger profiler's start
		if ($this->config->get('general.profiler')) {
			FD::profiler()->start();
		}

		// Additional triggers to be processed when the page starts.
		$dispatcher = FD::dispatcher();

		// Trigger: onComponentStart
		$dispatcher->trigger('user', 'onComponentStart', array());

		// Run initialization codes for javascript side of things.
		$this->init();
	}

	/**
	 * This is the ending point of the page library.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function end( $options = array() )
	{
		// Initialize required dependencies.
		FD::document()->init($options);

		$processStylesheets	= isset( $options[ 'processStylesheets' ] ) ? $options[ 'processStylesheets' ] : true;

		// @task: Process any scripts that needs to be injected into the head.
		if ($processStylesheets) {
			$this->processStylesheets();
		}

		// @task: Process any scripts that needs to be injected into the head.
		$this->processScripts();

		// @task: Process the document title.
		$this->processTitle();

		// @task: Process opengraph tags
		FD::opengraph()->render();

		// @task: Trigger profiler's end.
		if ($this->config->get('general.profiler')) {
			FD::profiler()->end();
		}

		// Additional triggers to be processed when the page starts.
		// $dispatcher 	= FD::dispatcher();

		// Trigger: onComponentStart
		// $dispatcher->trigger('user', 'onComponentEnd', array());
	}

	/**
	 * Initializes the javascript framework part.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function init()
	{
		if ($this->input->get('compile', false, 'bool') != true) {
			return false;
		}

		// Determines if we should minify the output.
		$minify = $this->input->get('minify', false, 'bool');

		$compiler = FD::getInstance('Compiler');
		$results = $compiler->compile($minify);

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($results);

		exit;
	}

	/**
	 * Adds into the breadcrumb list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function breadcrumb($title, $link = '')
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathways = $pathway->getPathwayNames();

		if (!empty($pathways)) {
			$pathways = array_map(array('JString', 'strtolower'), $pathways);
		}

		// Ensure that the title is translated
		$title = JText::_($title);

		// Set the temporary title
		$tmp = JString::strtolower($title);

		// Do not allow duplicate titles in the breadcrumb
		if (in_array($tmp, $pathways)) {
			return false;
		}

		$state = $pathway->addItem($title, $link);

		return $state;
	}

	/**
	 * Allows caller to set a canonical link
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canonical($link)
	{
		$docLinks = $this->doc->_links;

		// if joomla already added a canonial links here, we remove it.
		if ($docLinks) {
			foreach($docLinks as $jLink => $data) {
				if ($data['relation'] == 'canonical' && $data['relType'] == 'rel') {

					//unset this variable
					unset($this->doc->_links[$jLink]);
				}
			}
		}

		$link = FD::string()->escape($link);
		$this->doc->addHeadLink($link, 'canonical');
	}

	/**
	 * Sets the title of the page.
	 *
	 * @access	public
	 * @param	string	$title 	The title of the current page.
	 */
	public function title($default, $override = true, $view = null)
	{
		// Get the view.
		$view = is_null($view) ? $this->input->get('view', '', 'cmd') : $view;

		// Get the passed in title.
		$title = $default;

		// @TODO: Create SEO section that allows admin to customize the header of the page. Test if there's any custom title set in SEO section

		// Get current menu
		$activeMenu = $this->app->getMenu()->getActive();

		if ($activeMenu) {
			$params = $activeMenu->params;
			$menuView = $activeMenu->query['view'];

			if ($params->get('page_title') && $override && $view == $menuView) {
				$title = $params->get('page_title');
			}
		}

		// Apply translations on the title
		$title = JText::_($title);

		// Prepare Joomla's site title if necessary.
		$this->title = $this->getSiteTitle($title);


		// Need to think about keywords , author , metadesc and robots

		// nofollow ?
	}

	/**
	 * Given a string to be added to the title, compute it with the site title
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSiteTitle($title = '')
	{
		$jConfig 	= FD::config('joomla');
		$addTitle 	= $jConfig->getValue('sitename_pagetitles');
		$siteTitle 	= $jConfig->getValue('sitename');

		if ($addTitle) {

			$siteTitle 	= $jConfig->getValue( 'sitename' );

			if ($addTitle == 1) {
				$title 	= $siteTitle . ' - ' . $title;
			}

			if ($addTitle == 2) {
				$title	= $title . ' - ' . $siteTitle;
			}
		}

		return $title;
	}
}
