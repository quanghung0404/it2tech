<?php
/**
 * Install script
 *
 * @package         IP Login
 * @version         2.1.1PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemIPLoginInstallerScript extends PlgSystemIPLoginInstallerScriptHelper
{
	public $name = 'IP_LOGIN';
	public $alias = 'iplogin';
	public $extension_type = 'plugin';
}
