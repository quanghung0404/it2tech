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

// Load FoundryConfiguration
require_once(EASYBLOG_FOUNDRY_CONFIGURATION);

class EasyBlogConfiguration extends FD50_FoundryComponentConfiguration
{
	static $attached = false;
	static $instance = null;
	public $config = null;
	public $doc = null;
	public $section = null;

	public function __construct()
	{
		$this->config = EB::config();
		$this->doc = JFactory::getDocument();

		// If environment is set to production, change to static.
		$environment = $this->config->get('easyblog_environment');

		if ($environment == 'production') {
			$environment = 'static';
		}

		$this->namespace = 'EASYBLOG';
		$this->shortName = 'eb';
		$this->environment = $environment;
		$this->mode = $this->config->get('easyblog_mode');
		$this->version = (string) EB::getLocalVersion();
		$this->baseUrl = EB::getBaseUrl();
		$this->token = EB::getToken();
		$this->inline = $this->config->get('inline_configuration');
		$this->enableCdn = $this->config->get('enable_cdn');
		$this->ajaxUrl = EB::getBaseUrl();

		parent::__construct();
	}

	/**
	 * This method should be invoked to avoid multiple instances being created on a single load
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getInstance($section = 'site')
	{
		if (is_null(self::$instance)) {
			self::$instance	= new self($section);
		}

		return self::$instance;
	}

	/**
	 * This method will be called by the parent Foundry_ComponentConfiguration
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function update()
	{
		// We need to call parent's update method first
		// because they will automatically check for
		// url overrides, e.g. es_env, es_mode.
		parent::update();

		if ($this->environment == 'static') {
			$this->scripts = array($this->section . '-' . $this->version . '.static');
		}

		if ($this->environment == 'optimized') {
			$this->scripts = array($this->section . '-' . $this->version . '.optimized');
		}

		if ($this->environment == 'development') {
			$this->scripts = array('easyblog');
		}
	}

	/**
	 * This is where all the javascripts would be attached on the page.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function attach($section = 'site')
	{
		$this->section = $section;

		if (isset(self::$attached[$section])) {
			return;
		}

		parent::attach();

		self::$attached = true;
	}
}