<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2012 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( SOCIAL_FOUNDRY_CONFIGURATION );

class SocialConfiguration extends FD40_FoundryComponentConfiguration
{
	static $attached = false;
	static $instance = null;

	public function __construct()
	{
		$config = FD::config();

		$this->namespace = "EASYSOCIAL";
		$this->shortName = "es";
		$this->environment = $config->get('general.environment');
		$this->mode = $config->get('general.mode');
		$this->inline = $config->get('general.inline');
		$this->version = FD::getLocalVersion();
		$this->baseUrl = FD::getBaseUrl();
		$this->token = FD::token();
		$this->enableCdn = $config->get('general.cdn.enabled');

		// moment locale mapping against joomla language
		// If the counter part doesn't exist, then we all back to the nearest possible one, or en-gb
		$momentLangMap = array(
			'af-za' => 'en-gb',
			'ar-aa' => 'ar',
			'bg-bg' => 'bg',
			'bn-bd' => 'en-gb',
			'ca-es' => 'ca',
			'cs-cz' => 'cs',
			'da-dk' => 'da',
			'de-de' => 'de',
			'el-gr' => 'el',
			'en-gb' => 'en-gb',
			'en-us' => 'en-gb',
			'es-cl' => 'es',
			'es-es' => 'es',
			'fa-ir' => 'fa',
			'fi-fi' => 'fi',
			'fr-ca' => 'fr',
			'fr-fr' => 'fr',
			'he-il' => 'he',
			'hr-hr' => 'hr',
			'hu-hu' => 'hu',
			'hy-am' => 'hy-am',
			'id-id' => 'id',
			'it-it' => 'it',
			'ja-jp' => 'ja',
			'ko-kr' => 'ko',
			'lt-lt' => 'lt',
			'ms-my' => 'ms-my',
			'nb-no' => 'nb',
			'nl-nl' => 'nl',
			'pl-pl' => 'pl',
			'pt-br' => 'pt-br',
			'pt-pt' => 'pt',
			'ro-ro' => 'ro',
			'ru-ru' => 'ru',
			'sq-al' => 'sq',
			'sv-se' => 'sv',
			'sw-ke' => 'en-gb',
			'th-th' => 'th',
			'tr-tr' => 'tr',
			'uk-ua' => 'uk',
			'vi-vn' => 'vi',
			'zh-cn' => 'zh-cn',
			'zh-hk' => 'zh-cn',
			'zh-tw' => 'zh-tw'
		);

		$langTag = strtolower(JFactory::getLanguage()->getTag());

		$this->options['momentLang'] = isset($momentLangMap[$langTag]) ? $momentLangMap[$langTag] : 'en-gb';

		// Determines if the site is on lockdown mode
		$this->options['lockdown'] = $config->get('general.site.lockdown.enabled') ? true : false;

		// Determines if the user is logged in
		$this->options['guest'] = JFactory::getUser()->guest ? true : false;
		
		// Let the component configuration initialize all values
		parent::__construct();
	}

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function update()
	{
		// We need to call parent's update method first
		// because they will automatically check for
		// url overrides, e.g. es_env, es_mode.
		parent::update();

		switch ($this->environment) {

			case 'static':
			default:
				$this->scripts = array(
					'easysocial-' . $this->version . '.static'
				);
				break;

			case 'optimized':
				$this->scripts = array(
					'easysocial-' . $this->version . '.optimized'
				);
				break;

			case 'development':
				$this->scripts = array(
					'easysocial'
				);
				break;
		}
	}

	/**
	 * Override parent's behavior to store the ajax url
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toArray()
	{
		$data = parent::toArray();

		$data['ajaxUrl'] = $this->getAjaxUrl();

		return $data;
	}

	/**
	 * Format our own ajax url
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAjaxUrl()
	{
		static $url;

		if (isset($url)) {
			return $url;
		}

		$uri = JFactory::getURI();
		$language = $uri->getVar('lang', 'none');

		// Remove any ' or " from the language because language should only have -
		$app = JFactory::getApplication();
		$input = $app->input;

		$language = $input->get('lang', '', 'cmd');

		$jConfig = FD::jconfig();

		// Get the router
		$router = $app->getRouter();

		// It could be admin url or front end url
		$url = rtrim(JURI::base(), '/') . '/';

		// Determines if we should use index.php for the url
		$config = ES::config();

		if ($config->get('general.ajaxindex')) {
			$url .= 'index.php';
		}

		// Append the url with the extension
		$url = $url . '?option=com_easysocial&lang=' . $language;

		// During SEF mode, we need to ensure that the URL is correct.
		$languageFilterEnabled = JPluginHelper::isEnabled("system","languagefilter");

		if ($router->getMode() == JROUTER_MODE_SEF && $languageFilterEnabled) {

			// Determines if the mod_rewrite is enabled on Joomla
			$rewrite = $jConfig->getValue('sef_rewrite');

			// Replace the path if it's on subfolders
			$base = str_ireplace(JURI::root(true), '', $uri->getPath());

			if ($rewrite) {
				$path = $base;
			} else {
				$path = JString::substr($base, 10);
			}

			// Remove trailing / from the url
			$path = JString::trim($path, '/');
			$parts = explode('/', $path);

			if ($parts) {
				// First segment will always be the language filter.
				$language = reset($parts);
			} else {
				$language = 'none';
			}

			if ($rewrite) {
				$url = rtrim(JURI::root(), '/') . '/' . $language . '/?option=com_easysocial';
			} else {
				$url = rtrim(JURI::root(), '/') . '/index.php/' . $language . '/?option=com_easysocial';
			}

		}

		$menu = JFactory::getApplication()->getmenu();

		if (!empty($menu)) {
			$item = $menu->getActive();

			if (isset($item->id)) {
				$url .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix. Need to sort them out here.
		$currentURL = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($currentURL)) {

			// When the url contains www and the current accessed url does not contain www, fix it.
			if (stristr($currentURL, 'www') === false && stristr($url, 'www') !== false) {
				$url = str_ireplace('www.', '', $url);
			}

			// When the url does not contain www and the current accessed url contains www.
			if (stristr($currentURL, 'www') !== false && stristr($url, 'www') === false) {
				$url = str_ireplace('://', '://www.', $url);
			}
		}

		return $url;
	}	


	/**
	 * Attaches foundry framework to the document header
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function attach()
	{
		// If foundry framework is already attached, we can skip this.
		if (self::$attached) {
			return;
		}

		// Load up the parent to attach scripts
		parent::attach();

		if ($this->environment!=="development") {
			// Get resources
			$compiler = FD::getInstance('Compiler');
			$resource = $compiler->getResources();

			// Attach resources
			if (!empty($resource))
			{
				$scriptTag = $this->createScriptTag($resource["uri"]);

				$document = JFactory::getDocument();
				$document->addCustomTag($scriptTag);
			}
		}

		self::$attached = true;
	}
}
