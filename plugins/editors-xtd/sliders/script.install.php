<?php
/**
 * @package         Sliders
 * @version         5.1.11PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgEditorsXtdSlidersInstallerScript extends PlgEditorsXtdSlidersInstallerScriptHelper
{
	public $name = 'SLIDERS';
	public $alias = 'sliders';
	public $extension_type = 'plugin';
	public $plugin_folder = 'editors-xtd';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'system');
	}
}
