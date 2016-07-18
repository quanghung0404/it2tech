<?php
/**
 * Install script
 *
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_AdvancedTemplatesInstallerScript extends Com_AdvancedTemplatesInstallerScriptHelper
{
	public $name = 'ADVANCED_TEMPLATE_MANAGER';
	public $alias = 'advancedtemplatemanager';
	public $extname = 'advancedtemplates';
	public $extension_type = 'component';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, $folder = 'system');
	}

	public function onBeforeInstall()
	{
		// Fix incorrectly formed versions because of issues in old packager
		$this->fixFileVersions(
			array(
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/advancedtemplates.xml',
				JPATH_PLUGINS . '/system/advancedtemplates/advancedtemplates.xml',
			)
		);
	}

	public function onAfterInstall()
	{
		$this->createTable();
		$this->removeAdminMenu();
		$this->removeFrontendComponentFromDB();
		$this->deleteOldFiles();
		$this->checkForGeoIP();
	}

	public function createTable()
	{
		// main table
		$query = "CREATE TABLE IF NOT EXISTS `#__advancedtemplates` (
			`styleid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`params` TEXT NOT NULL,
			PRIMARY KEY (`styleid`)
		) DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function removeAdminMenu()
	{
		// hide admin menu
		$query = $this->db->getQuery(true)
			->delete('#__menu')
			->where($this->db->quoteName('path') . ' = ' . $this->db->quote('advancedtemplates'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
			->where($this->db->quoteName('client_id') . ' = 1');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function removeFrontendComponentFromDB()
	{
		// remove frontend component from extensions table
		$query = $this->db->getQuery(true)
			->delete('#__extensions')
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('com_advancedtemplates'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
			->where($this->db->quoteName('client_id') . ' = 0');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function deleteOldFiles()
	{
		JFile::delete(
			array(
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/script.advancedtemplates.php',
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/models/forms/filter_styles.xml',
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/models/forms/filter_templates.xml',
			)
		);

		$this->deleteFolders(
			array(
				JPATH_SITE . '/components/com_advancedtemplates',
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/layouts/joomla/searchtools',
				JPATH_ADMINISTRATOR . '/components/com_advancedtemplates/models/fields',
			)
		);
	}

	private function checkForGeoIP()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('extension_id'))
			->from('#__extensions')
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('geoip'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('library'));
		$this->db->setQuery($query, 0, 1);
		$result = $this->db->loadResult();

		// GeoIP library is installed, so ignore
		if (!empty($result))
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('styleid'))
			->from('#__advancedtemplates')
			->where($this->db->quoteName('params') . ' RLIKE ' . $this->db->quote('"assignto_geo(continents|countries|regions)":"1"'));
		$this->db->setQuery($query, 0, 1);
		$result = $this->db->loadResult();

		// No modules found with Geo assignments, so ignore
		if (empty($result))
		{
			return;
		}

		JFactory::getApplication()->enqueueMessage(
			'Advanced Template Manager no longer uses external services for the <strong>Geolocation assignments</strong>.<br/>
			It now makes use of a new <strong>NoNumber GeoIP library</strong>.<br />
			<br />
			You currently have template styles with Geo assignments. To continue using these assignments you are required to install the NoNumber GeoIP library<br /><br />
			<a href="https://www.nonumber.nl/geoip" target="_blank" class="btn">Install the NoNumber GeoIP library</a>',
			'warning'
		);
	}
}
