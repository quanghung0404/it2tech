<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include parent library
require_once(__DIR__ . '/controller.php');

class EasyBlogControllerAddonsList extends EasyBlogSetupController
{
	public function execute()
	{
		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		if ($this->isDevelopment()) {

			$result = new stdClass();
			$result->html = '<div style="padding:20px;background: #f4f4f4;border: 1px dotted #d7d7d7;margin-top:20px;">In development mode, this option is disabled.</div>';

			return $this->output($result);
		}

		// Check if path is empty.

		// Construct the extraction path for the module
		$info = $this->getInfo();
		$modulesExtractPath = EB_TMP . '/modules_v' . $info->version;
		$pluginsExtractPath = EB_TMP . '/plugins_v' . $info->version;

		// Get the modules list
		$modules = $this->getModulesList($path, $modulesExtractPath);

		// Get the plugins list
		$plugins = $this->getPluginsList($path, $pluginsExtractPath);

		$data = new stdClass();
		$data->modules = $modules;
		$data->plugins = $plugins;
		
		ob_start();
		include(dirname(__DIR__) . '/themes/steps/addons.list.php');
		$contents = ob_get_contents();
		ob_end_clean();

		$result = new stdClass();
		$result->html = $contents;
		$result->modulePath = $modulesExtractPath;
		$result->pluginPath = $pluginsExtractPath;
		
		return $this->output($result);
	}

	private function getPluginsList($path, $tmp)
	{
		$info = $this->getInfo();
		$zip = $path . '/plugins.zip';

		$state = JArchive::extract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of plugin groups
		$groups = JFolder::folders($tmp, '.', false, true);

		$plugins = array();

		foreach ($groups as $group) {
			$groupTitle = basename($group);

			// Get a list of items in each groups
			$items = JFolder::folders($group, '.', false, true);
			
			foreach ($items as $item) {
				$element = basename($item);
				$manifest = $item . '/' . $element . '.xml';

				// Read the xml file
				$parser = JFactory::getXml($manifest);

				if (!$parser) {
					continue;
				}
				$plugin = new stdClass();
				$plugin->element = $element;
				$plugin->group = $groupTitle;
				$plugin->title = (string) $parser->name;
				$plugin->version = (string) $parser->version;
				$plugin->description = (string) $parser->description;
				$plugin->description = trim($plugin->description);

				$plugins[] = $plugin;
			}
		}

		return $plugins;
	}

	private function getModulesList($path, $tmp)
	{
		$info = $this->getInfo();
		$zip = $path . '/modules.zip';

		$state = JArchive::extract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of modules
		$items = JFolder::folders($tmp, '.', false, true);

		$modules = array();
		$installedModules = array();

		// Get installed module.
		// We only do this for upgrade from 3.x
		if ($this->isUpgradeFrom3x()) {
			$installedModules = $this->getInstalledModules();
		}
		
		foreach ($items as $item) {
			$element = basename($item);
			$manifest = $item . '/' . $element . '.xml';

			// Read the xml file
			$parser = JFactory::getXml($manifest);

			$module = new stdClass();
			$module->title = (string) $parser->name;
			$module->version = (string) $parser->version;
			$module->description = (string) $parser->description;
			$module->description = trim($module->description);
			$module->element = $element;
			$module->disabled = false; 

			// Check if the module already installed, put a flag
			if (in_array($module->element, $installedModules)) {
				$module->disabled = true; 
			}

			$modules[] = $module;
		}

		return $modules;
	}

	private function getInstalledModules()
	{
		$db = EB::db();

		$moduleNames = array('mod_easyblogarchive','mod_easyblogbio','mod_easyblogcalendar','mod_easyblogcategories','mod_easybloglatestblogger','mod_easybloglatestcomment','mod_easybloglist',
							'mod_easyblogmostactiveblogger','mod_easyblogmostcommentedpost','mod_easyblogmostpopularpost','mod_easyblognewpost','mod_easyblogpostmap','mod_easyblograndompost',
							'mod_easyblogrelatedpost','mod_easyblogsubscribe','mod_easyblogtagcloud','mod_easyblogwelcome','mod_imagewall','mod_latestblogs','mod_searchblogs','mod_showcase',
							'mod_subscribers','mod_teamblogs','mod_topblogs');


		$modules = '';
		foreach($moduleNames as $module){
			$modules .= ($modules) ? ',' . $db->Quote($module) : $db->Quote($module);
		}

	    // jos_modules
	    $query = array();
	    $query[] = 'SELECT '. $db->quoteName('module') .' FROM ' . $db->quoteName('#__modules');
	    $query[] = ' WHERE ' . $db->quoteName('module') . ' IN (' . $modules . ')';

	    $query = implode(' ', $query);

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

	    // module names old => new
	    $names = array('mod_topblogs' => 'mod_easyblogtopblogs',
	    				'mod_teamblogs' => 'mod_easyblogteamblogs',
	    				'mod_subscribers' => 'mod_easyblogsubscribers',
	    				'mod_searchblogs' => 'mod_easyblogsearch',
	    				'mod_latestblogs' => 'mod_easybloglatestblogs',
	    				'mod_imagewall' => 'mod_easyblogimagewall',
	    				'mod_showcase' => 'mod_easyblogshowcase');

	    $modules = array();

	    // try rename the variable to follow new modules name
		foreach ($result as $module) {
			if (array_key_exists($module->module, $names)) {
				$module->module = $names[$module->module];
			}
			$modules[] = $module->module;
		}

	    return $modules;
	}

	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "easyblog_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__easyblog_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('scriptversion');
				$db->setQuery($query);

				$scriptversion = $db->loadResult();
				$scriptversion = explode('.', $scriptversion);
				
				// We know if the scriptversion is equal to 3, this is upgrade from version 3.x
				if ($scriptversion[0] == '3') {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}
}
