<?php
/**
 * Install script
 *
 * @package         Components Anywhere
 * @version         2.2.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemComponentsAnywhereInstallerScript extends PlgSystemComponentsAnywhereInstallerScriptHelper
{
	public $name = 'COMPONENTS_ANYWHERE';
	public $alias = 'componentsanywhere';
	public $extension_type = 'plugin';
}
