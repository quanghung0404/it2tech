<?php
/**
 * Install script
 *
 * @package         Cache Cleaner
 * @version         4.2.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemCacheCleanerInstallerScript extends PlgSystemCacheCleanerInstallerScriptHelper
{
	public $name = 'CACHE_CLEANER';
	public $alias = 'cachecleaner';
	public $extension_type = 'plugin';

	public function uninstall($adapter)
	{
		$this->uninstallModule($this->extname);
	}

	public function onAfterInstall()
	{
		$this->fixOldParams();
	}

	public function fixOldParams()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->qn('params'))
			->from('#__extensions')
			->where($this->db->qn('element') . ' = ' . $this->db->q('cachecleaner'))
			->where($this->db->qn('folder') . ' = ' . $this->db->q('system'));
		$this->db->setQuery($query);
		$params = $this->db->loadResult();

		if (empty($params))
		{
			return;
		}

		$params = json_decode();

		if (empty($params))
		{
			return;
		}

		if (isset($params->clean_folders_selection))
		{
			return;
		}

		$params->clean_tmp = isset($params->clean_tmp) ? 2 : 0;

		if (!empty($params->clean_folders))
		{
			$params->clean_folders_selection = $params->clean_folders;
			$params->clean_folders           = 2;
		}

		if (isset($params->auto_save_clean_folders))
		{
			$params->clean_tmp     = isset($params->clean_tmp) ? 1 : 0;
			$params->clean_folders = isset($params->clean_folders) ? 1 : 0;
		}

		unset($params->auto_save_clean_folders);

		$params->clean_tables     = isset($params->clean_tables) ? 2 : 0;
		$params->clean_jre        = isset($params->clean_jre) ? 2 : 0;
		$params->clean_jotcache   = isset($params->clean_jotcache) ? 2 : 0;
		$params->clean_siteground = isset($params->clean_siteground) ? 2 : 0;
		$params->clean_maxcdn     = isset($params->clean_maxcdn) ? 2 : 0;
		$params->clean_keycdn     = isset($params->clean_keycdn) ? 2 : 0;
		$params->clean_cloudflare = isset($params->clean_cloudflare) ? 2 : 0;

		if (isset($params->auto_save_clean_party))
		{
			$params->clean_tables = isset($params->clean_tables) ? 1 : 0;
		}

		if (isset($params->auto_save_clean_party))
		{
			$params->clean_jre        = isset($params->clean_jre) ? 1 : 0;
			$params->clean_jotcache   = isset($params->clean_jotcache) ? 1 : 0;
			$params->clean_siteground = isset($params->clean_siteground) ? 1 : 0;
			$params->clean_maxcdn     = isset($params->clean_maxcdn) ? 1 : 0;
			$params->clean_keycdn     = isset($params->clean_keycdn) ? 1 : 0;
			$params->clean_cloudflare = isset($params->clean_cloudflare) ? 1 : 0;
		}

		unset($params->auto_save_clean_tables);
		unset($params->auto_save_clean_party);

		$query->clear()
			->update('#__extensions')
			->set($this->db->qn('params') . ' = ' . $this->db->q(json_encode($params)))
			->where($this->db->qn('element') . ' = ' . $this->db->q('cachecleaner'))
			->where($this->db->qn('folder') . ' = ' . $this->db->q('system'));
		$this->db->setQuery($query);
		$this->db->execute();
	}
}
