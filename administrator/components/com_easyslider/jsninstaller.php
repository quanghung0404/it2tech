<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Disable notice and warning by default for our products.
// The reason for doing this is if any notice or warning appeared then handling JSON string will fail in our code.
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

/**
 * Class for finalizing JSN extension installation.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
abstract class JSNInstallerScript
{
	/**
	 * Variable for holding backed up data.
	 *
	 * @var	SimpleXMLElement
	 */
	protected $jsnesdata;
	
	/**
	 * XML manifest.
	 *
	 * @var  SimpleXMLElement
	 */
	protected $manifest;

	/**
	 * Implement preflight hook.
	 *
	 * This step will be verify permission for install/update process.
	 *
	 * @param   string  $mode    Install or update?
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  boolean
	 */
	public function preflight($mode, $parent)
	{
		// Initialize variables
		$this->app				= JFactory::getApplication();		
		$this->parentInstaller 	= $parent->getParent();
		
		//check if EasySlider is installed and version is smaller than 2.x.x or not
		$easyslider = $this->getExtInfo();
		
		if (count($easyslider))
		{
			$extManifest 		= json_decode($easyslider->manifest_cache);
			$installedVersion 	= $extManifest->version;
			
			if (version_compare($installedVersion, '2.0.0', '<'))
			{
				if ($this->app->input->getVar('jsn_es_status', '') == '')
				{
					$this->showWarningMessage($this->parentInstaller);
					return false;
				}
				elseif ($this->app->input->getVar('jsn_es_status', '') == 'yes')
				{
					$config	= JFactory::getConfig();
					$tmpPath = $config->get('tmp_path');
					if (!is_writable($tmpPath))
					{
						$this->app->enqueueMessage('Cannot install "JSN EasySlider" because folder "/tmp" is Unwritable. Please set Writable permission (CHMOD 777) for it before performing update operations', 'error');
						return false;						
					}
					//Backup date for version 1.x.x
					$rbackup = $this->backup($extManifest);
					
					if (!$rbackup)
					{
						$this->app->enqueueMessage('Cannot install "JSN EasySlider" because the backup function cannot backup your data. Please check and make sure "/tmp" folder is WRITABLE', 'error');
						return false;
					}
				}
				else
				{
					//do nothing
				}
			}
		}
		
		
		$this->manifest	= $this->parentInstaller->getManifest();

		// Get installed extension directory and name
		$this->path = $this->parentInstaller->getPath('extension_administrator');
		$this->name = substr(basename($this->path), 4);

		// Check if installed Joomla version is compatible
		$joomlaVersion	= new JVersion;
		$canInstall		= substr($joomlaVersion->RELEASE, 0, 1) == substr((string) $this->manifest['version'], 0, 1) ? true : false;

		if ( ! $canInstall)
		{
			$this->app->enqueueMessage(sprintf('Cannot install "%s" because the installation package is not compatible with your installed Joomla version', (string) $this->manifest->name), 'error');
		}

		// Parse dependency
		$this->parseDependency($this->parentInstaller);

		// Skip checking folder permission if FTP layer is enabled
		$config = JFactory::getConfig();

		if ($config->get('ftp_enable'))
		{
			// Try to backup user edited language file
			return $this->backupLanguage();
		}

		// Check environment
		$canInstallExtension		= true;
		$canInstallSiteLanguage		= is_writable(JPATH_SITE . '/language');
		$canInstallAdminLanguage	= is_writable(JPATH_ADMINISTRATOR . '/language');
		$canInstallProduct			= is_writable($config->get('tmp_path'));
		
		if (! $canInstallProduct)
		{
			$this->app->enqueueMessage(sprintf('Cannot install because "%s" is not writable', $config->get('tmp_path')), 'error');
		}
		
		if ( ! $canInstallSiteLanguage)
		{
			$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', JPATH_SITE . '/language'), 'error');
		}
		else
		{
			foreach (glob(JPATH_SITE . '/language/*', GLOB_ONLYDIR) AS $dir)
			{
				if ( ! is_writable($dir))
				{
					$canInstallSiteLanguage = false;
					$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', $dir), 'error');
				}
			}
		}

		if ( ! $canInstallAdminLanguage)
		{
			$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', JPATH_ADMINISTRATOR . '/language'), 'error');
		}
		else
		{
			foreach (glob(JPATH_ADMINISTRATOR . '/language/*', GLOB_ONLYDIR) AS $dir)
			{
				if ( ! is_writable($dir))
				{
					$canInstallAdminLanguage = false;
					$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', $dir), 'error');
				}
			}
		}

		// Checking directory permissions for dependency installation
		foreach ($this->dependencies AS & $extension)
		{
			// Simply continue if extension is set to be removed
			if (isset($extension->remove) AND (int) $extension->remove > 0)
			{
				continue;
			}

			// Check if dependency can be installed
			switch ($extension->type = strtolower($extension->type))
			{
				case 'component':
					$sitePath	= JPATH_SITE . '/components';
					$adminPath	= JPATH_ADMINISTRATOR . '/components';

					if ( ! is_dir($sitePath) OR ! is_writable($sitePath))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $sitePath), 'error');
					}

					if ( ! is_dir($adminPath) OR ! is_writable($adminPath))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $adminPath), 'error');
					}
				break;

				case 'plugin':
					$path = JPATH_ROOT . '/plugins/' . $extension->folder;

					if ((is_dir($path) AND ! is_writable($path)) OR ( ! is_dir($path) AND ! is_writable(dirname($path))))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $path), 'error');
					}
				break;

				case 'module':
				case 'template':
					$path = ($extension->client == 'site' ? JPATH_SITE : JPATH_ADMINISTRATOR) . "/{$extension->type}s";

					if ( ! is_dir($path) OR ! is_writable($path))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $path), 'error');
					}
				break;
			}

			if ($canInstallProduct AND $canInstall AND $canInstallExtension AND $canInstallSiteLanguage AND $canInstallAdminLanguage AND isset($extension->source))
			{
				// Backup dependency parameters
				$db	= JFactory::getDbo();
				$q	= $db->getQuery(true);

				$q->select('params');
				$q->from('#__extensions');
				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);

				$extension->params = $db->loadResult();
			}
		}

		if ($canInstallProduct AND $canInstall AND $canInstallExtension AND $canInstallSiteLanguage AND $canInstallAdminLanguage)
		{
			$rBackupLanguage = $this->backupLanguage();
			
			//if is old version then clean redundant files		
			if ($rBackupLanguage && count($easyslider))
			{
				$this->removeRedundantFiles();
			}
			
			return $rBackupLanguage;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Implement postflight hook.
	 *
	 * @param   string  $type    Extension type.
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		// Get original installer
		isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();

		// Update language file
		$this->updateLanguage();
		
		// Process dependency installation
		foreach ($this->dependencies AS $extension)
		{
			// Remove installed extension if requested
			if (isset($extension->remove) AND (int) $extension->remove > 0)
			{
				$this->removeExtension($extension);
				continue;
			}

			// Install only dependency that has local installation package
			if (isset($extension->source))
			{
				// Install and update dependency status
				$this->installExtension($extension);
			}
			elseif ( ! isset($this->missingDependency))
			{
				$this->missingDependency = true;
			}
		}

		// Create dependency declaration constant
		if ( ! defined('JSN_' . strtoupper($this->name) . '_DEPENDENCY'))
		{
			// Get Joomla config
			$config = JFactory::getConfig();

			// Unset some unnecessary properties
			foreach ($this->dependencies AS & $dependency)
			{
				unset($dependency->source);
				unset($dependency->upToDate);
			}

			$this->dependencies = json_encode($this->dependencies);

			// Store dependency declaration
			file_exists($defines = $this->path . '/defines.php')
				OR file_exists($defines = $this->path . "/{$this->name}.defines.php")
				OR $defines = $this->path . "/{$this->name}.php";

			if ($config->get('ftp_enable') OR is_writable($defines))
			{
				$buffer = preg_replace(
					'/(defined\s*\(\s*._JEXEC.\s*\)[^\n]+\n)/',
					'\1' . "\ndefine('JSN_" . strtoupper($this->name) . "_DEPENDENCY', '" . $this->dependencies . "');\n",
					JFile::read($defines)
				);

				JFile::write($defines, $buffer);
			}
		}

		// Clean latest product version cache file
		$cache = JFactory::getConfig()->get('tmp_path') . '/JoomlaShineUpdates.json';

		if (file_exists($cache))
		{
			jimport('joomla.filesystem.file');
			JFile::delete($cache);
		}

		// Register check update link for Joomla
		$JVersion = new JVersion;

		// Check if current joomla version is 2.5.x
		$isJoomla25 = (version_compare($JVersion->getShortVersion(), '2.5', '>=') AND version_compare($JVersion->getShortVersion(), '3.0', '<'));
		
		//if (version_compare($JVersion->RELEASE, '3.1', '>='))
		//{
			// Get id for the extension just installed
			$ext = JTable::getInstance('Extension');

			$ext->load(array(
				'name'		=> $this->name,
				'element'	=> basename($this->path),
				'type'		=> 'component'
			));

			if ($ext->extension_id)
			{
				// Get current check update data
				$db	= JFactory::getDbo();
				$q	= $db->getQuery(true);

				$q->select('update_site_id');
				$q->from('#__update_sites');
				$q->where('name = ' . $q->quote(strtolower(trim($this->name))));
				$q->where('type = ' . $q->quote('collection'));

				$db->setQuery($q);

				if ($uid = $db->loadResult())
				{
					// Clean-up current check update data
					$q = $db->getQuery(true);

					$q->delete('#__update_sites');
					$q->where('update_site_id = ' . (int) $uid);

					$db->setQuery($q);
					
					if ($isJoomla25)
					{	
						$db->query();
					}
					else
					{
						$db->execute();
					}

					$q = $db->getQuery(true);

					$q->delete('#__update_sites_extensions');
					$q->where('update_site_id = ' . (int) $uid);

					$db->setQuery($q);
					
					if ($isJoomla25)
					{	
						$db->query();
					}
					else
					{
						$db->execute();
					}
				}

				// Register check update data
				$ln	 = 'http://www.joomlashine.com/versioning/extensions/' . $ext->element . '.xml';
				$q	= $db->getQuery(true);

				$q->insert('#__update_sites');
				$q->columns('`name`, `type`, `location`, `enabled`');
				$q->values($q->quote(strtolower(trim($this->name))) . ', ' . $q->quote('collection') . ', ' . $q->quote($ln) . ', 1');

				$db->setQuery($q);
				
				if ($isJoomla25)
				{	
					$db->query();
				}
				else
				{
					$db->execute();
				}

				if ($uid = $db->insertid())
				{
					$q	= $db->getQuery(true);

					$q->insert('#__update_sites_extensions');
					$q->columns('`update_site_id`, `extension_id`');
					$q->values((int) $uid . ', ' . (int) $ext->extension_id);

					$db->setQuery($q);
					
					if ($isJoomla25)
					{
						$db->query();
					}
					else
					{
						$db->execute();
					}
				}
			}
		//}

		// Check if redirect should be disabled
		if ($this->app->input->getBool('tool_redirect', true))
		{
			// Do we have any missing dependency
			if ($this->missingDependency)
			{
				if (strpos($_SERVER['HTTP_REFERER'], '/administrator/index.php?option=com_installer') !== false)
				{
					// Set redirect to finalize dependency installation
					$this->parentInstaller->setRedirectURL('index.php?option=com_' . $this->name . '&view=installer');
				}
				else
				{
					// Let Ajax client redirect
					echo '
<script type="text/javascript">
	if (window.parent)
		window.parent.location.href ="' . JUri::root() . 'administrator/index.php?option=com_' . $this->name . '&view=installer";
	else
		location.href ="' . JUri::root() . 'administrator/index.php?option=com_' . $this->name . '&view=installer";
</script>';
					exit;
				}
			}
		}
	}

	/**
	 * Implement uninstall hook.
	 *
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  void
	 */
	public function uninstall($parent)
	{
		// Initialize variables
		isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();

		$this->app			= JFactory::getApplication();
		$this->manifest		= $this->parentInstaller->getManifest();

		// Get installed extension directory and name
		$this->path = $this->parentInstaller->getPath('extension_administrator');
		$this->name = substr(basename($this->path), 4);

		// Parse dependency
		$this->parseDependency($this->parentInstaller);

		// Remove all dependency
		foreach ($this->dependencies AS $extension)
		{
			$this->removeExtension($extension);
		}
	}

	/**
	 * Retrieve dependency from manifest file.
	 *
	 * @param   object  $installer  JInstaller object.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function parseDependency($installer)
	{
		// Continue only if dependency not checked before
		if ( ! isset($this->dependencies) OR ! is_array($this->dependencies))
		{
			// Preset dependency list
			$this->dependencies = array();

			if (isset($this->manifest->subinstall) AND $this->manifest->subinstall instanceOf SimpleXMLElement)
			{
				// Loop on each node to retrieve dependency information
				foreach ($this->manifest->subinstall->children() AS $node)
				{
					// Verify tag name
					if (strcasecmp($node->getName(), 'extension') != 0)
					{
						continue;
					}

					// Re-create serializable dependency object
					$extension = (array) $node;
					$extension = (object) $extension['@attributes'];

					$extension->title = trim((string) $node != '' ? (string) $node : ($node['title'] ? (string) $node['title'] : (string) $node['name']));

					// Validate dependency
					if ( ! isset($extension->name) OR ! isset($extension->type) OR ! in_array($extension->type, array('template', 'plugin', 'module', 'component')) OR ($extension->type == 'plugin' AND ! isset($extension->folder)))
					{
						continue;
					}

					// Check if dependency has local installation package
					if (isset($extension->dir) AND is_dir($source = $installer->getPath('source') . '/' . $extension->dir))
					{
						$extension->source	= $source;
					}

					$this->dependencies[] = $extension;
				}
			}
		}

		return $this;
	}

	/**
	 * Install a dependency.
	 *
	 * @param   object  $extension  Object containing extension details.
	 *
	 * @return  void
	 */
	public function installExtension($extension)
	{
		// Get application object
		isset($this->app) OR $this->app = JFactory::getApplication();

		// Get database object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query to get dependency installation status
		$q->select('manifest_cache, custom_data');
		$q->from('#__extensions');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);

		if ($status = $db->loadObject())
		{
			// Initialize variables
			$JVersion = new JVersion;
			$manifest = json_decode($status->manifest_cache);

			// Get information about the dependency to be installed
			$xml = JPATH::clean($extension->source . '/' . $extension->name . '.xml');

			if (is_file($xml) AND ($xml = simplexml_load_file($xml)))
			{
				if ($JVersion->RELEASE == (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<'))
				{
					// The dependency to be installed is newer than the existing one, mark for update
					$doInstall = true;
				}

				if ($JVersion->RELEASE != (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<='))
				{
					// The dependency to be installed might not newer than the existing one but Joomla version is difference, mark for update
					$doInstall = true;
				}
			}
		}
		elseif (isset($extension->source))
		{
			// The dependency to be installed not exist, mark for install
			$doInstall = true;
		}

		if (isset($doInstall) AND $doInstall)
		{
			// Install dependency
			$installer = new JInstaller;

			if ( ! $installer->install($extension->source))
			{
				$this->app->enqueueMessage(sprintf('Error installing "%s - %s" %s', $extension->folder, $extension->name, $extension->type), 'error');
			}
			else
			{
				$this->app->enqueueMessage(sprintf('Install "%s - %s" %s was successfull', $extension->folder, $extension->name, $extension->type));

				// Update dependency status
				$this->updateExtension($extension);

				// Build query to get dependency installation status
				$q	= $db->getQuery(true);

				$q->select('manifest_cache, custom_data');
				$q->from('#__extensions');
				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);

				// Load dependency installation status
				$status = $db->loadObject();
			}
		}

		// Update dependency tracking
		if (isset($status))
		{
			$ext = isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
			$dep = ! empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

			// Backward compatible: move all dependency data from params to custom_data column
			if (is_array($params = (isset($extension->params) AND $extension->params != '{}') ? (array) json_decode($extension->params) : null))
			{
				foreach (array('imageshow', 'poweradmin', 'easyslider') AS $com)
				{
					if ($com != $ext AND isset($params[$com]))
					{
						$dep[] = $com;
					}
				}
			}

			// Update dependency list
			in_array($ext, $dep) OR $dep[] = $ext;
			$status->custom_data = array_unique($dep);

			// Build query to update dependency data
			$q = $db->getQuery(true);

			$q->update('#__extensions');
			$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

			// Backward compatible: keep data in this column for older product to recognize
			$manifestCache = json_decode($status->manifest_cache);
			$manifestCache->dependency = $status->custom_data;

			$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

			// Backward compatible: keep data in this column also for another old product to recognize
			$params = is_array($params)
				? array_merge($params, array_combine($status->custom_data, $status->custom_data))
				: array_combine($status->custom_data, $status->custom_data);

			$q->set("params = '" . json_encode($params) . "'");

			$q->where("element = '{$extension->name}'");
			$q->where("type = '{$extension->type}'");
			$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

			$db->setQuery($q);
			$db->execute();
		}
	}

	/**
	 * Update dependency status.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function updateExtension($extension)
	{
		// Get object to working with extensions table
		$table = JTable::getInstance('Extension');

		// Load extension record
		$condition = array(
			'type'		=> $extension->type,
			'element'	=> $extension->name
		);

		if ($extension->type == 'plugin')
		{
			$condition['folder'] = $extension->folder;
		}

		$table->load($condition);

		// Update extension record
		$table->enabled		= (isset($extension->publish)	AND (int) $extension->publish > 0)	? 1 : 0;
		$table->protected	= (isset($extension->lock)		AND (int) $extension->lock > 0)		? 1 : 0;
		$table->client_id	= (isset($extension->client)	AND $extension->client == 'site')	? 0 : 1;

		// Store updated extension record
		$table->store();

		// Update module instance
		if ($extension->type == 'module')
		{
			// Get object to working with modules table
			$module = JTable::getInstance('module');

			// Load module instance
			$module->load(array('module' => $extension->name));

			// Update module instance
			$module->title		= $extension->title;
			$module->ordering	= isset($extension->ordering) ? $extension->ordering : 0;
			$module->published	= (isset($extension->publish) AND (int) $extension->publish > 0) ? 1 : 0;

			if ($hasPosition = (isset($extension->position) AND (string) $extension->position != ''))
			{
				$module->position = (string) $extension->position;
			}

			// Store updated module instance
			$module->store();

			// Set module instance to show in all page
			if ($hasPosition AND (int) $module->id > 0)
			{
				// Get database object
				$db	= JFactory::getDbo();
				$q	= $db->getQuery(true);

				try
				{
					// Remove all menu assignment records associated with this module instance
					$q->delete('#__modules_menu');
					$q->where("moduleid = {$module->id}");

					// Execute query
					$db->setQuery($q);
					$db->execute();

					// Build query to show this module instance in all page
					$q->insert('#__modules_menu');
					$q->columns('moduleid, menuid');
					$q->values("{$module->id}, 0");

					// Execute query
					$db->setQuery($q);
					$db->execute();
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
		}

		return $this;
	}

	/**
	 * Disable a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function disableExtension($extension)
	{
		// Get database object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query
		$q->update('#__extensions');
		$q->set('enabled = 0');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);
		$db->execute();

		return $this;
	}

	/**
	 * Unlock a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function unlockExtension($extension)
	{
		// Get database object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query
		$q->update('#__extensions');
		$q->set('protected = 0');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);
		$db->execute();

		return $this;
	}

	/**
	 * Remove a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function removeExtension($extension)
	{
		// Get application object
		isset($this->app) OR $this->app = JFactory::getApplication();

		// Preset dependency status
		$extension->removable = true;

		// Get database object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query to get dependency installation status
		$q->select('extension_id, manifest_cache, custom_data, params');
		$q->from('#__extensions');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);

		if ($status = $db->loadObject())
		{
			// Initialize variables
			$id		= $status->extension_id;
			$ext	= isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
			$deps	= ! empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

			// Update dependency tracking
			$status->custom_data = array();

			foreach ($deps AS $dep)
			{
				// Backward compatible: ensure that product is not removed
				// if ($dep != $ext)
				if ($dep != $ext AND is_dir(JPATH_BASE . '/components/com_' . $dep))
				{
					$status->custom_data[] = $dep;
				}
			}

			if (count($status->custom_data))
			{
				// Build query to update dependency data
				$q = $db->getQuery(true);

				$q->update('#__extensions');
				$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

				// Backward compatible: keep data in this column for older product to recognize
				$manifestCache = json_decode($status->manifest_cache);
				$manifestCache->dependency = $status->custom_data;

				$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

				// Backward compatible: keep data in this column also for another old product to recognize
				$params = is_array($params = (isset($status->params) AND $status->params != '{}') ? (array) json_decode($status->params) : null)
					? array_merge($params, array_combine($status->custom_data, $status->custom_data))
					: array_combine($status->custom_data, $status->custom_data);

				$q->set("params = '" . json_encode($params) . "'");

				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);
				$db->execute();

				// Indicate that extension is not removable
				$extension->removable = false;
			}
		}
		else
		{
			// Extension was already removed
			$extension->removable = false;
		}

		if ($extension->removable)
		{
			// Disable and unlock dependency
			$this->disableExtension($extension);
			$this->unlockExtension($extension);

			// Remove dependency
			$installer = new JInstaller;

			if ($installer->uninstall($extension->type, $id))
			{
				$this->app->enqueueMessage(sprintf('"%s" %s has been uninstalled', $extension->name, $extension->type));
			}
			else
			{
				$this->app->enqueueMessage(sprintf('Cannot uninstall "%s" %s', $extension->name, $extension->type));
			}
		}

		return $this;
	}

	/**
	 * Attemp to backup user edited language file before re-install/update.
	 *
	 * @return  boolean  FALSE if backup fail for any reason, TRUE otherwise.
	 */
	protected function backupLanguage()
	{
		// Load language utility class
		$langUtil	= (class_exists('JSNUtilsLanguage') AND method_exists('JSNUtilsLanguage', 'edited'))
					? 'JSNUtilsLanguage' : 'JSNUtilsLanguageTmp';

		if ($langUtil != 'JSNUtilsLanguage')
		{
			file_exists($this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php')
				? require_once $this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php'
				: ($langUtil = null);
		}

		if ($langUtil)
		{
			// Get list of component's supported language
			$admin	= is_dir($this->path . '/language/admin') ? JFolder::folders($this->path . '/language/admin') : null;
			$site	= is_dir($this->path . '/language/site') ? JFolder::folders($this->path . '/language/site') : null;

			if ($admin AND $site)
			{
				$langs = array_merge($admin, $site);
			}
			elseif ($admin OR $site)
			{
				$langs = $admin ? $admin : $site;
			}
			$langs = isset($langs) ? array_unique($langs) : array();

			// Loop thru supported language list and get all language files installed in Joomla's language folder
			foreach ($langs AS $lang)
			{
				// Check if language is installed in Joomla's language folder and manually edited by user
				$isEdited = array(
					'admin'	=> call_user_func(array($langUtil, 'edited'), $lang, false, "com_{$this->name}"),
					'site'	=> call_user_func(array($langUtil, 'edited'), $lang, true, "com_{$this->name}")
				);

				foreach ($isEdited AS $client => $edited)
				{
					if ($edited)
					{
						// Get list of language file
						$files = glob($this->path . "/language/{$client}/{$lang}/{$lang}.*.ini");

						// Backup all language file installed in Joomla's language folder
						foreach ($files AS $file)
						{
							// Generate path to user edited language file in Joomla's language folder
							$f = ($client == 'admin' ? JPATH_ADMINISTRATOR : JPATH_SITE) . "/language/{$lang}/" . basename($file);

							// Backup user edited language file to temporary directory
							if (is_readable($f))
							{
								if ( ! JFile::copy($f, "{$f}.jsn-installer-backup"))
								{
									$backupFails[] = str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $f);
								}
							}
						}
					}
				}
			}

			if (isset($backupFails))
			{
				$this->app->enqueueMessage(
						'Cannot backup following user edited language file(s): <ul><li>' . implode('</li><li>', $backupFails) . '</li></ul>',
						'warning'
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * Update all language file installed in Joomla's language folder.
	 *
	 * @return  boolean  FALSE if update fail for any reason, TRUE otherwise.
	 */
	protected function updateLanguage()
	{
		// Load language utility class
		$langUtil	= (class_exists('JSNUtilsLanguage') AND method_exists('JSNUtilsLanguage', 'edited'))
					? 'JSNUtilsLanguage' : 'JSNUtilsLanguageTmp';

		if ($langUtil != 'JSNUtilsLanguage')
		{
			file_exists($this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php')
				? require_once $this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php'
				: ($langUtil = null);
		}

		if ($langUtil)
		{
			// Get list of component's supported language
			$admin	= is_dir($this->path . '/language/admin') ? JFolder::folders($this->path . '/language/admin') : null;
			$site	= is_dir($this->path . '/language/site') ? JFolder::folders($this->path . '/language/site') : null;

			if ($admin AND $site)
			{
				$langs = array_merge($admin, $site);
			}
			elseif ($admin OR $site)
			{
				$langs = $admin ? $admin : $site;
			}
			$langs = isset($langs) ? array_unique($langs) : array();

			// Loop thru supported language list and get all language files installed in Joomla's language folder
			foreach ($langs AS $lang)
			{
				// Check if language is installed in Joomla's language folder
				$isInstalled = array(
					'admin'	=> call_user_func(array($langUtil, 'installed'), $lang, false, "com_{$this->name}"),
					'site'	=> call_user_func(array($langUtil, 'installed'), $lang, true, "com_{$this->name}")
				);

				foreach ($isInstalled AS $client => $installed)
				{
					if ($installed)
					{
						// Install new language file
						call_user_func(array($langUtil, 'install'), (array) $lang, $client != 'admin' ? true : false, true, "com_{$this->name}");

						// Get list of language file
						$files = glob($this->path . "/language/{$client}/{$lang}/{$lang}.*.ini");

						// Check if any installed language file has backup
						foreach ($files AS $file)
						{
							// Clean all possible new-line character left by 'glob' function
							$file = preg_replace('/(\r|\n)/', '', $file);

							// Generate path to installed language file in Joomla's language folder
							$f = ($client == 'admin' ? JPATH_ADMINISTRATOR : JPATH_SITE) . "/language/{$lang}/" . basename($file);

							// If language file has backup, merge all user's translation into new language file
							if (is_readable("{$f}.jsn-installer-backup"))
							{
								// Read content of new language file to array
								$new = file($file);

								// Read content of backup file to associative array
								foreach (file("{$f}.jsn-installer-backup") AS $line)
								{
									if ( ! empty($line) AND ! preg_match('/^\s*;/', $line) AND preg_match('/^\s*([^=]+)="([^\r\n]+)"\s*$/', $line, $match))
									{
										$bak[$match[1]] = $match[2];
									}
								}

								// Merge user's translation into new language file
								foreach ($new AS & $line)
								{
									if ( ! empty($line) AND ! preg_match('/^\s*;/', $line) AND preg_match('/^\s*([^=]+)="([^\r\n]+)"\s*$/', $line, $match))
									{
										if (isset($bak[$match[1]]))
										{
											$line = str_replace($match[2], $bak[$match[1]], $line);

											// Mark as merged
											isset($merged) OR $merged = true;
										}
									}
								}

								// Update installed language file with merged content
								if (isset($merged) AND $merged)
								{
									$new = implode($new);

									if ( ! JFile::write($f, $new))
									{
										$mergeFails[] = str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $f);
									}
								}

								// Remove backup file
								JFile::delete("{$f}.jsn-installer-backup");
							}
						}
					}
				}
			}

			if (isset($mergeFails))
			{
				$this->app->enqueueMessage(
					'Cannot merge user edited translation back to following language file(s): <ul><li>' . implode('</li><li>', $mergeFails) . '</li></ul>',
					'warning'
				);

				return false;
			}
		}

		return true;
	}
	
	/**
	 * Show warning message when update from version 1.x.x to 2.x.x
	 * 
	 * @param object $parentInstaller JInstaller object.
	 * @return void
	 */
	protected function showWarningMessage($parentInstaller)
	{
		$path = $parentInstaller->getPath('source');
		
		$html = '<form enctype="multipart/form-data" action="' . JRoute::_('index.php?option=com_installer&view=install') . '" method="post" name="JSNESAdminForm" id="JSNESAdminForm" class="form-horizontal" style="display: none;">';
		$html .= '<input type="text" name="install_directory" class="span5 input_box" size="70" value="' . $path . '" />';
		$html .= '<button type="submit" id="JSNESNInstallationBtn">Upload and Install</button>';
		$html .= '<input type="hidden" name="type" value="" />';
		$html .= '<input type="hidden" name="installtype" value="folder" />';
		$html .= '<input type="hidden" name="task" value="install.install" />';
		$html .= '<input type="hidden" name="jsn_es_status" value="yes" />';
		$html .= JHtml::_('form.token');
		$html .= '</form>';
		$html .= '<p>JSN EasySlider version 2.x.x contains many changes from 1.x.x to provide better User Interface and tons of new features to your sliders. We will auto-backup and migrate your data from 1.x.x to latest version. The backup file will be put in folder <strong>/tmp</strong>. You should revise all your sliders after this update.</p>';
		$html .= '<p><input type="checkbox" id="JSNESNYesInstallationCheckBox" value="yes" style="margin: 0 5px;"><label for="JSNESNYesInstallationCheckBox" style="display: inline-block;"><strong>I have clearly understand this message and want to update.</strong>
</label></p>';		
		$html .= '<a href="' . JRoute::_('index.php?option=com_easyslider') . '" class="btn" id="JSNESNNOInstallationBtn">No</a>&nbsp;';
		$html .= '<button type="button" class="btn btn-primary" id="JSNESNYesInstallationBtn" disabled="disabled">Yes</button>';
		//$document = JFactory::getDocument();
		

		$html .= '<script type="text/javascript">
					try
					{
						jQuery(document).ready(function ($) {
							jQuery("#JSNESNYesInstallationCheckBox").on("click", function(){
								if(jQuery(this).is(":checked"))
								{
									jQuery("#JSNESNYesInstallationBtn").prop("disabled", false);
								}
								else
								{
									jQuery("#JSNESNYesInstallationBtn").prop("disabled", true);
								}
							});
							jQuery("#JSNESNYesInstallationBtn").on("click", function(){
								jQuery("#loading").css("display", "block");
								var form = document.getElementById("JSNESAdminForm");
								form.submit();
							});
							
						});
					}catch(e){
					}</script>';
		$app		= JFactory::getApplication();	
	
		//Check if user update and upgrade via EasySlider
		if (strpos(@ $_SERVER['HTTP_REFERER'], '?option=' . $app->input->getCmd('option')) !== false 
		&& (strpos(@ $_SERVER['HTTP_REFERER'], '&view=update') !== false || strpos(@ $_SERVER['HTTP_REFERER'], '&view=upgrade') !== false))
		{
			echo $html;
			exit();
		}
		$app->redirect('index.php?option=com_installer&view=install', $html, 'error');
		return false;
	}
	
	/**
	 * Get EasySlider information
	 *
	 * @return  object
	 */		
	protected function getExtInfo()
	{
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where($db->quoteName('element') . '=' . $db->quote('com_easyslider'));
		$query->where($db->quoteName('type') . '=' . $db->quote('component'));	
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
	
	/**
	 * Backup selected database tables 
	 *
	 * @param   object  $info  Manifest information.
	 *
	 * @return  boolean
	 */	
	protected function backup($info)
	{
		jimport('joomla.filesystem.file');
		// Get Joomla config and version object
		$config	= JFactory::getConfig();
		$name = 'jsn_easyslider_data_version_1.x.x_' . date('YmdHis');
		$name	= array(
			'zip' => "{$name}.zip",
			'xml' => "jsn_easyslider_backup_db.xml"
		);		
		
		$this->jsnesdata = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><backup></backup>');
		$this->jsnesdata->addAttribute('extension-name', 'JSN EasySlider');
		$this->jsnesdata->addAttribute('extension-version', $info->version);
		$this->jsnesdata->addAttribute('joomla-version', '3.0.0');
		
		// Backup data from selected tables
		$this->backupTables();
		
		//Do any extra work needed after doing real data backup
		$result = $this->afterBackup($name);
		
		if (!$result) return false;
		
		if (isset($this->zippedBackup))
		{
			// Store zipped backup to file system
			$rwriteFile = JFile::write($config->get('tmp_path') . '/' . $name['zip'], $this->zippedBackup);
			
			if (!$rwriteFile)
			{
				return false;
			}
			else
			{	
				$session   = JFactory::getSession();
				$session->set('JSN_EASYSLIDER_BACKUP_OLD_DATA_PATH', $config->get('tmp_path') . '/' . $name['zip']);
				return true;
			}
			
		}
		
		return false;
	}

	/**
	 * Backup data from selected database tables.
	 *
	 * @return  void
	 */	
	protected function backupTables()
	{
		$tables = array('#__jsn_easyslider_config', '#__jsn_easyslider_messages', '#__jsn_easyslider_sliders');
		// Create parent node for storing dumped data
		$this->jsnesdata->addChild('tables');
		// Get database object
		$db = JFactory::getDbo();
		
		foreach ($tables AS $table)
		{
			$query = $db->getQuery(true);

			$query->select('*');
			$query->from($table);

			$db->setQuery($query);
			if ($rows = $db->loadAssocList())
			{
				$this->storeTableData($table, $rows);
			}
	
		}
	}
	
	/**
	 * Store backed up table data to XML object.
	 *
	 * @param   array  $table  Name of data table.
	 * @param   array  $rows   Dumped data from the table.
	 *
	 * @return  void
	 */
	protected function storeTableData($table, $rows)
	{
		// Create new node for storing backed up table data
		$node = $this->jsnesdata->tables->addChild('table');
		$node->addAttribute('name', $table);

		// Store backed up table data to table node
		$node = $node->addChild('rows');

		foreach ($rows AS $row)
		{
			// Create new node for storing current row of data
			$rowNode = $node->addChild('row');

			foreach ($row AS $name => $value)
			{
				$rowNode->addChild($name, htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
			}
		}
	}

	/**
	 * Do any extra work needed after doing real data backup.
	 *
	 * @param   array  &$options  Backup options.
	 * @param   array  &$name     array('zip' => 'zip_backup_file_name', 'xml' => 'xml_backup_file_name')
	 *
	 * @return  void
	 */
	protected function afterBackup($name)
	{
		// Create zip file containing all backed up data and/or files
		return $this->finalizeBackup($name);
	}
	
	/**
	 * Create downloadable zip-archived backup file.
	 *
	 * @param   array  $name  array('zip' => 'zip_backup_file_name', 'xml' => 'xml_backup_file_name')
	 *
	 * @return  void
	 */
	protected function finalizeBackup($name)
	{
		// Get XML data from XML object
		if (($data = $this->jsnesdata->asXML()) === false)
		{
			return false;
		}

		if (!class_exists('JSNUtilsArchive'))
		{
			if (file_exists(JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/utils/archive.php'))
			{
				require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/utils/archive.php';
			}
			else
			{
				return false;
			}
		}
		// Prepend XML file to the array of backed up files
		//array_unshift($this->files, array($name['xml'] => $data));
		$file = array(array($name['xml'] => $data));
		// Create zip file containing all backed up data and/or files
		try
		{
			if (!($this->zippedBackup = JSNUtilsArchive::createZip($file)))
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
			
		return true;
	}
	/**
	 * Remove redundant files when update.
	 *
	 * @return  void
	 */
	protected function removeRedundantFiles()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$folders = array();
		$folders [JPATH_ROOT . '/administrator/components/'] 	= JPATH_ROOT . '/administrator/components/com_easyslider';
		$folders [JPATH_ROOT . '/modules/'] 					= JPATH_ROOT . '/modules/mod_easyslider';
		$folders [JPATH_ROOT . '/plugins/content/'] 			= JPATH_ROOT . '/plugins/content/jsneasyslider';
		$folders [JPATH_ROOT . '/plugins/editors-xtd/'] 		= JPATH_ROOT . '/plugins/editors-xtd/jsneasyslider';
		$folders [JPATH_ROOT . '/plugins/system/'] 				= JPATH_ROOT . '/plugins/system/easyslider';
		
		foreach ($folders as $key => $folder)
		{
			$rootPath = $key;
			if (file_exists($folder))
			{
				$fileList   	= JFolder::files($folder, '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'easyslider.xml', 'mod_easyslider.xml', 'jsneasyslider.xml'));
				$subFolderList 	= JFolder::folders($folder);
				
				if (count($fileList))
				{
					foreach($fileList as $file)
					{
						$tmpFile = $folder . '/' . $file;
					
						if (file_exists($tmpFile) && strpos($tmpFile, 'easyslider') !== false)
						{
							try
							{
								JFile::delete($tmpFile);
							}
							catch (Exception $e) 
							{
								// do nothing 
							}	
						}						
					}
				}
				
				if (count($subFolderList))
				{
					foreach($subFolderList as $subFolder)
					{
						$tmpFolder = $folder . '/' . $subFolder;
								
						if (file_exists($tmpFolder) && is_writable($tmpFolder) && strpos($tmpFolder, 'easyslider') !== false)
						{
							try 
							{
								JFolder::delete($tmpFolder);
							} 
							catch (Exception $e) 
							{
								// do nothing 
							}
						}						
					}	
				}
			}
		}
	}
}
