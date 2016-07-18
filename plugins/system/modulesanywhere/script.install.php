<?php
/**
 * Install script
 *
 * @package         Modules Anywhere
 * @version         4.1.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemModulesAnywhereInstallerScript extends PlgSystemModulesAnywhereInstallerScriptHelper
{
	public $name = 'MODULES_ANYWHERE';
	public $alias = 'modulesanywhere';
	public $extension_type = 'plugin';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'editors-xtd');
	}
}
