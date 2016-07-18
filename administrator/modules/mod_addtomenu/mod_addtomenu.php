<?php
/**
 * Main Module File
 *
 * @package         Add to Menu
 * @version         4.0.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Module that adds menu items
 */

$user = JFactory::getUser();
if (!$user->authorise('core.create', 'com_menus'))
{
	return;
}

// return if NoNumber Framework plugin is not installed
jimport('joomla.filesystem.file');
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	return;
}

jimport('joomla.filesystem.folder');
$option = JFactory::getApplication()->input->get('option');
$folder = JPATH_ADMINISTRATOR . '/components/' . $option . '/addtomenu';
if (!JFolder::exists($folder))
{
	$folder = JPATH_ADMINISTRATOR . '/modules/mod_addtomenu/components/' . $option;
}
if (!JFolder::exists($folder))
{
	return;
}

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$helper = new ModAddToMenu($params);
$helper->render();
