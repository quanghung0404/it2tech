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

class PlgSystemSnippetsInstallerScript extends PlgSystemSnippetsInstallerScriptHelper
{
	public $name = 'SNIPPETS';
	public $alias = 'snippets';
	public $extension_type = 'plugin';

	public function uninstall($adapter)
	{
		$this->uninstallComponent($this->extname);
		$this->uninstallPlugin($this->extname, 'editors-xtd');
	}
}
