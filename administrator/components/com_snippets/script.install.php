<?php
/**
 * Install script
 *
 * @package         Snippets
 * @version         4.1.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_SnippetsInstallerScript extends Com_SnippetsInstallerScriptHelper
{
	public $name = 'SNIPPETS';
	public $alias = 'snippets';
	public $extension_type = 'component';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'system');
		$this->uninstallPlugin($this->extname, 'editors-xtd');
	}

	public function onAfterInstall()
	{
		$this->createTable();
		$this->fixOldFormatInDatabase();
		$this->deleteOldFiles();
	}

	public function createTable()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__snippets` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`alias` TEXT NOT NULL,
			`name` TEXT NOT NULL,
			`description` TEXT NOT NULL,
			`content` TEXT NOT NULL,
			`params` TEXT NOT NULL,
			`published` TINYINT(1)  NOT NULL DEFAULT '0',
			`ordering` INT(11) NOT NULL DEFAULT '0',
			`checked_out` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (`id`),
			KEY `id` (`id`,`published`)
		) DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function fixOldFormatInDatabase()
	{
		// convert old J1.5 params syntax to new
		$query = $this->db->getQuery(true);
		$query->select('s.id, s.params')
			->from('#__snippets as s')
			->where('s.params REGEXP ' . $this->db->quote('^[^\{]'));
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		foreach ($rows as $row)
		{
			if (empty($row->params))
			{
				continue;
			}

			$params = JRegistryFormat::getInstance('INI')->stringToObject($row->params);
			foreach ($params as $key => $val)
			{
				if (is_string($val) && !(strpos($val, '|') === false))
				{
					$params->{$key} = explode('|', $val);
				}
			}
			$query = $this->db->getQuery(true);
			$query->update('#__snippets as s')
				->set('s.params = ' . $this->db->quote(json_encode($params)))
				->where('s.id = ' . (int) $row->id);
			$this->db->setQuery($query);
			$this->db->execute();
		}
	}


	private function deleteOldFiles()
	{
		$this->deleteFolders(
			array(
				JPATH_SITE . '/components/com_snippets',
			)
		);
	}
}
