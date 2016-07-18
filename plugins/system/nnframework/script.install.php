<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemNNFrameworkInstallerScript extends PlgSystemNNFrameworkInstallerScriptHelper
{
	public $name = 'NONUMBER_FRAMEWORK';
	public $alias = 'nnframework';
	public $extension_type = 'plugin';

	public function onBeforeInstall()
	{
		if (!$this->isNewer())
		{
			return false;
		}
	}
}
