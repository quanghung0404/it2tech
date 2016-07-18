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

class Mod_CacheCleanerInstallerScript extends Mod_CacheCleanerInstallerScriptHelper
{
	public $name = 'CACHE_CLEANER';
	public $alias = 'cachecleaner';
	public $extension_type = 'module';
	public $module_position = 'status';
	public $client_id = 1;

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'system');
	}
}
