<?php

/**
 * @version    $Id: subinstall.php 15057 2012-08-14 04:54:32Z thailv $
 * @package    JSNSample
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$dirInstallTmp = dirname(__FILE__) . '/admin/jsninstaller.php';
$dirInstallComponent = dirname(__FILE__) .  '/jsninstaller.php';
if (is_file($dirInstallTmp))
{
	include_once $dirInstallTmp;
}
elseif (is_file($dirInstallComponent))
{
	include_once $dirInstallComponent;
}


/**
 * Class for finalizing JSN Sample installation.
 *
 * @package  JSNSample
 * @since    1.0.0
 */
class Com_MobilizeInstallerScript extends JSNInstallerScript
{
	
}
