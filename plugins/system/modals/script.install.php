<?php
/**
 * @package         Modals
 * @version         6.2.9PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemModalsInstallerScript extends PlgSystemModalsInstallerScriptHelper
{
	public $name = 'MODALS';
	public $alias = 'modals';
	public $extension_type = 'plugin';

	public function onAfterInstall()
	{
		// Copy modal.php to system template folder
		JFile::copy(__DIR__ . '/modal.php', JPATH_SITE . '/templates/system/modal.php');
	}
}
