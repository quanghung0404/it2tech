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

/**
 * HTML initialization here.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialDocumentHTML extends EasySocial
{
	static $loaded = false;

	static $scriptsLoaded = false;

	static $stylesheetsLoaded = false;

	static $options = array();

	/**
	 * This loads a list of javascripts that are dependent throughout the whole component.
	 *
	 * @access	public
	 * @param	null
	 */
	public function init($options = array())
	{
		// @task: Only load when necessary and in html mode.
		if (self::$loaded) {
			return;
		}

		self::$options	= $options;

		$this->initScripts();
		$this->initStylesheets();

		self::$loaded	= true;
	}

	/**
	 * Initializes javascript on the head of the page.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function initScripts()
	{
		if (self::$scriptsLoaded) {
			return;
		}

		$configuration = FD::getInstance('Configuration');
		$configuration->attach();

		// TODO: Find a better place to define map language code this.
		$document = JFactory::getDocument();
		$document->addCustomTag('<meta name="foundry:location:language" content="' . FD::config()->get('general.location.language', 'en') . '" />');

		self::$scriptsLoaded = true;
	}

	/**
	 * Initializes all the stylesheet that needs to load on the page
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function initStylesheets()
	{
		static $profiles = array();

		if (isset(self::$options['processStylesheets']) && self::$options['processStylesheets'] == false) {
			self::$loaded 	= true;
			return;
		}

		if (self::$stylesheetsLoaded) {
			return;
		}

		// Build theme styles
		$location = $this->app->isAdmin() ? 'admin' : 'site';
		$theme = strtolower($this->config->get('theme.' . $location));

		// Site location
		if ($location == 'site') {

			$profile = FD::user()->getProfile();

			if ($profile && !isset($profiles[$profile->id])) {
				$params = $profile->getParams();
				$override = $params->get('theme');

				if ($override) {
					$theme	= $override;
				}

				$profiles[$profile->id] = $theme;
			}

			if ($profile && isset($profiles[$profile->id])) {
				$theme 	= $profiles[$profile->id];
			}
		}

		// Build theme styles
        $stylesheet = FD::stylesheet($location, $theme);
        $stylesheet->attach();

		// Site location
		if ($location == 'site') {
			// Check if custom.css exists on the site as template overrides
			$file = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easysocial/css/custom.css';

			if (JFile::exists($file)) {
				$customCssFile = rtrim(JURI::root(), '/') . '/templates/' . $this->app->getTemplate() . '/html/com_easysocial/css/custom.css';

				$this->doc->addStylesheet($customCssFile);
			}
		}
		


		// @TODO: Make this part of ATS in the future
		// If this is RTL, load RTL specific styles
		$direction = $this->doc->getDirection();

		if ($direction == 'rtl') {

			if ($theme == 'wireframe' || $theme == 'frosty') {
				$rtlStylesheet = rtrim(JURI::root(), '/') . '/components/com_easysocial/themes/' . $theme . '/styles/rtl.css';

				$this->doc->addStylesheet($rtlStylesheet);
			}
		}

		self::$stylesheetsLoaded = true;
	}
}
