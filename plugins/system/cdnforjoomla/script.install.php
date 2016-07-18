<?php
/**
 * Install script
 *
 * @package         CDN for Joomla!
 * @version         4.0.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemCDNforJoomlaInstallerScript extends PlgSystemCDNforJoomlaInstallerScriptHelper
{
	public $name = 'CDN_FOR_JOOMLA';
	public $alias = 'cdnforjoomla';
	public $extension_type = 'plugin';
}
