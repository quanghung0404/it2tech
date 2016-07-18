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

require_once(FD40_FOUNDRY_CLASSES . '/stylesheet.php');

class SocialStylesheet extends FD40_Stylesheet
{
	public function __construct($location, $name=null, $useOverride=false)
	{
		static $defaultWorkspace;

		if (!isset($defaultWorkspace))
		{
			$config = FD::config();

			$override = JFactory::getApplication()->getTemplate();

			$defaultWorkspace = array(
				'site'       => strtolower($config->get('theme.site')),
				'site_base'  => strtolower($config->get('theme.site_base')),
				'admin'      => strtolower($config->get('theme.admin')),
				'admin_base' => strtolower($config->get('theme.admin_base')),
				'module'     => null,
				'override'   => $override
			);
		}

		$this->workspace = $defaultWorkspace;

		$workspace = array();

		// Internally, override is a location.
		if ($useOverride) {
			$location = 'override';
		}

		// For specific template, else default template will be used.
		if (!empty($name)) {
			$workspace[$location] = $name;
		}

		// Because we can't do late static binding on PHP < 5.3.
		// Used by $this->override() method.
		$this->class = __CLASS__;

		parent::__construct('EASYSOCIAL', $workspace, $location);
	}

	/**
	 * Attaches stylesheet on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function attach($minified = null, $allowOverride = true)
	{
		// Get configuration
		$config = FD::getInstance('Configuration');
		$environment = $config->environment;
		$mode = $config->mode;

		// If caller did not specify whether or not
		// to attach compressed stylesheets.
		if (is_null( $minified)) {

			// Then decide from configuration mode
			$minified = ($mode=='compressed');
		}

		// Default settings
		$build = false;


		// If we're in a development environment,
		// always cache compile stylesheet and
		// attached uncompressed stylesheets.
		if ($environment=='development') {

			$build = true;

			// Never attached minified stylesheet while in development mode.
			$minified = false;

			// Only super developers can build admin stylesheets.
			if ($this->location=='admin') $build = false;

			// Do not build if stylesheet has not been compiled before.
			$cacheFolder = $this->folder('cache');
			if (!JFolder::exists($cacheFolder)) $build = false;

			// Always build for superdevs
			$super = FD::config()->get('general.super');
			if ($super) $build = true;
		}

		// Rebuild stylesheet on page load if necessary
		if ($build) {

			$task = $this->build($environment);

			// This generates build log in the browser console.
			$script = FD::script();
			$script->set('task', $task);
			// $script->attach('themes:/admin/stylesheet/log');
		}

		parent::attach($minified, $allowOverride);
	}
}
