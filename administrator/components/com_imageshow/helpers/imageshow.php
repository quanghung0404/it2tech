<?php
/**
 * @version    $Id: imageshow.php 16294 2012-09-22 04:07:32Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'jsn_is_showlist.php';
include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'jsn_is_showcase.php';

/**
 * ImageShow component helper.
 *
 * @package  JSN.ImageShow
 *
 * @since    2.5
 */

class JSNISImageShowHelper
{

	/**
	 * Configure the linkbar
	 *
	 * @param   string  $controller  The name of the active controller
	 *
	 * @return	void
	 */

	/**
	 * Add toolbar button.
	 *
	 * @return	void
	 */
	public static function addToolbarMenu()
	{
		$tmpl = JRequest::getVar('tmpl');
		if ($tmpl == 'component') return '';
		$edit 		= JRequest::getVar('edit');
		$document 	= JFactory::getDocument();
		$strAlert 	= '';
		if (!is_null($edit))
		{
			$strAlert = 'var objISOneImageShow = new $.JQJSNISImageShow();
							 objISOneImageShow.comfirmBox("' . JText::_('JSN_MENU_CONFIRM_BOX_ALERT', true) . '");';
		}
		$document->addScriptDeclaration("
		(function($){
			$(document).ready(function () {
			" . $strAlert . "
			});
		})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);");

		// Create a toolbar button that drop-down a sub-menu when clicked
		JSNMenuHelper::addEntry(
			'toolbar-menu', 'Menu', '', false, 'icon-list-view', 'toolbar'
		);

		// Declare 1st-level menu items
		JSNMenuHelper::addEntry(
			'launchpad',
			'JSN_MENU_LAUNCH_PAD',
			'index.php?option=com_imageshow',
			false,
			'administrator/components/com_imageshow/assets/images/icons-16/icon-off.png',
			'toolbar-menu'
		);

		JSNMenuHelper::addEntry(
			'showlist',
			'JSN_MENU_SHOWLISTS',
			'index.php?option=com_imageshow&view=showlist',
			false,
			'administrator/components/com_imageshow/assets/images/icons-16/icon-file.png',
			'toolbar-menu'
		);

		JSNMenuHelper::addEntry(
			'showcase',
			'JSN_MENU_SHOWCASES',
			'index.php?option=com_imageshow&view=showcase',
			false,
			'administrator/components/com_imageshow/assets/images/icons-16/icon-monitor.png',
			'toolbar-menu'
		);

		JSNMenuHelper::addEntry(
			'configuration',
			'JSN_MENU_CONFIGURATION_AND_MAINTENANCE',
			'index.php?option=com_imageshow&view=configuration',
			false,
			'administrator/components/com_imageshow/assets/images/icons-16/icon-cog.png',
			'toolbar-menu'
		);

		JSNMenuHelper::addEntry(
			'about',
			'JSN_MENU_ABOUT',
			'index.php?option=com_imageshow&view=about',
			false,
			'administrator/components/com_imageshow/assets/images/icons-16/icon-star.png',
			'toolbar-menu'
		);

		// Declare 2nd-level menu items	for 'items' entry
		JSNMenuHelper::addEntry(
			'all-showlists', 'All Showlists', 'index.php?option=com_imageshow&view=showlist', false, '', 'toolbar-menu.showlist'
		);

		JSNMenuHelper::addEntry(
			'all-showcases', 'All Showcases', 'index.php?option=com_imageshow&view=showcase', false, '', 'toolbar-menu.showcase'
		);

		$objJSNShowlist 	= new JSNISShowlist;
		$objJSNShowcase 	= new JSNISShowcase;
		$showlist			= $objJSNShowlist->getLastestShowlist(5);
		$showcase			= $objJSNShowcase->getLastestShowcase(5);

		if ($showlist)
		{
			JSNMenuHelper::addEntry(
				'recent-showlist', 'Recent Showlists', '', false, '', 'toolbar-menu.showlist'
			);

			foreach ($showlist AS $item)
			{
				JSNMenuHelper::addEntry(
					'showlist-' . $item->item_id,
					$item->item_title,
					'index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=' . $item->item_id,
					false,
					'',
					'toolbar-menu.showlist.recent-showlist'
				);
			}
		}

		if ($showcase)
		{
			JSNMenuHelper::addEntry(
				'recent-showcase', 'Recent Showcases', '', false, '', 'toolbar-menu.showcase'
			);

			foreach ($showcase AS $item)
			{
				JSNMenuHelper::addEntry(
					'showcase-' . $item->item_id,
					$item->item_title,
					'index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=' . $item->item_id,
					false,
					'',
					'toolbar-menu.showcase.recent-showcase'
				);
			}
		}

		JSNMenuHelper::addSeparator('toolbar-menu.showlist');
		JSNMenuHelper::addSeparator('toolbar-menu.showcase');

		JSNMenuHelper::addEntry(
			'showlist-new', 'Create new showlist', 'index.php?option=com_imageshow&controller=showlist&task=add', false, '', 'toolbar-menu.showlist'
		);

		JSNMenuHelper::addEntry(
			'showcase-new', 'Create new showcase', 'index.php?option=com_imageshow&controller=showcase&task=add', false, '', 'toolbar-menu.showcase'
		);
	}

	public static function addSubmenu($vName)
	{
		// Declare 1st-level menu items
		JSNMenuHelper::addEntry(
			'launchpad',
			'JSN_IMAGESHOW_MENU_LAUNCHPAD',
			'index.php?option=com_imageshow',
			$vName == '' OR $vName == 'cpanel',
			'administrator/components/com_imageshow/assets/images/icons-16/icon-off.png',
			'sub-menu'
		);

		if (self::getAccesses('showlist'))
		{
			JSNMenuHelper::addEntry(
				'showlists',
				'JSN_MENU_SHOWLISTS',
				'index.php?option=com_imageshow&view=showlist',
				$vName == 'showlists',
				'administrator/components/com_imageshow/assets/images/icons-16/icon-file.png',
				'sub-menu'
			);
		}

		if (self::getAccesses('showcase'))
		{
			JSNMenuHelper::addEntry(
				'showcases',
				'JSN_MENU_SHOWCASES',
				'index.php?option=com_imageshow&view=showcase',
				$vName == 'showcases',
				'administrator/components/com_imageshow/assets/images/icons-16/icon-monitor.png',
				'sub-menu'
			);
		}

		if (self::getAccesses('configuration'))
		{
			JSNMenuHelper::addEntry(
				'configuration',
				'JSN_MENU_CONFIGURATION_AND_MAINTENANCE',
				'index.php?option=com_imageshow&view=configuration',
				$vName == 'configuration' || $vName == 'maintenance',
				'administrator/components/com_imageshow/assets/images/icons-16/icon-cog.png',
				'sub-menu'
			);
		}
		JSNMenuHelper::addEntry(
			'about',
			'JSN_MENU_ABOUT',
			'index.php?option=com_imageshow&view=about',
			$vName == 'about',
			'administrator/components/com_imageshow/assets/images/icons-16/icon-star.png',
			'sub-menu'
		);

		// Declare 2nd-level menu items	for 'items' entry
		JSNMenuHelper::addEntry(
			'all-showlists', JText::_('JSN_IMAGESHOW_SUB_MENU_ALL_SHOWLISTS', true), 'index.php?option=com_imageshow&view=showlist', false, '', 'sub-menu.showlists'
		);

		JSNMenuHelper::addEntry(
			'all-showcases', JText::_('JSN_IMAGESHOW_SUB_MENU_ALL_SHOWCASES', true), 'index.php?option=com_imageshow&view=showcase', false, '', 'sub-menu.showcases'
		);

		$objJSNShowlist 	= new JSNISShowlist;
		$objJSNShowcase 	= new JSNISShowcase;
		$showlist			= $objJSNShowlist->getLastestShowlist(5);
		$showcase			= $objJSNShowcase->getLastestShowcase(5);

		if ($showlist)
		{
			JSNMenuHelper::addEntry(
				'recent-showlist', JText::_('JSN_IMAGESHOW_SUB_MENU_RECENT_SHOWLISTS', true), '', false, '', 'sub-menu.showlists'
			);

			foreach ($showlist AS $item)
			{
				JSNMenuHelper::addEntry(
					'showlist-' . $item->item_id,
					$item->item_title,
					'index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=' . $item->item_id,
					false,
					'',
					'sub-menu.showlists.recent-showlist'
				);
			}
		}

		if ($showcase)
		{
			JSNMenuHelper::addEntry(
				'recent-showcase', JText::_('JSN_IMAGESHOW_SUB_MENU_RECENT_SHOWCASES', true), '', false, '', 'sub-menu.showcases'
			);

			foreach ($showcase AS $item)
			{
				JSNMenuHelper::addEntry(
					'showcase-' . $item->item_id,
					$item->item_title,
					'index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=' . $item->item_id,
					false,
					'',
					'sub-menu.showcases.recent-showcase'
				);
			}
		}

		JSNMenuHelper::addSeparator('sub-menu.showlists');
		JSNMenuHelper::addSeparator('sub-menu.showcases');

		JSNMenuHelper::addEntry(
			'showlist-new', JText::_('JSN_IMAGESHOW_SUB_MENU_CREATE_NEW_SHOWLIST', true), 'index.php?option=com_imageshow&controller=showlist&task=add', false, '', 'sub-menu.showlists'
		);

		JSNMenuHelper::addEntry(
			'showcase-new', JText::_('JSN_IMAGESHOW_SUB_MENU_CREATE_NEW_SHOWCASE', true), 'index.php?option=com_imageshow&controller=showcase&task=add', false, '', 'sub-menu.showcases'
		);

		// Render the sub-menu
		if (JFactory::getApplication()->input->getCmd('tmpl', '') == '') {
			JSNMenuHelper::render('sub-menu');
		}
	}

	public static function getAccesses($view)
	{
		jimport('joomla.access.access');
		$componentName 	= 'com_imageshow';
		$prefix			= 'imageshow.manage';
		$user   		= JFactory::getUser();
		$result 		= new JObject();

		switch($view)
		{
			case 'showcase':
				$assetName = $prefix.'.showcase';
				break;
			case 'showlist':
				$assetName = $prefix.'.showlist';
				break;
			case 'configuration':
				$assetName = $prefix.'.configuration';
				break;
			default:
				$assetName = 'core.manage';
				break;
		}
		return $user->authorise($assetName, $componentName);
	}

	public static function getActions($type = 'component',$id = 0)
	{
		jimport('joomla.access.access');
		$componentName 		= 'com_imageshow';
		$user			 	= JFactory::getUser();
		$result		  		= new JObject();
		$actions		 	= JAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/' . $componentName . '/access.xml');

		if ($type != 'component')
		{
			$assetName = $componentName . '.' . $type . '.' . $id;
		}
		else
		{
			$assetName = $componentName;
		}

		foreach ($actions as $action)
		{
			$result->set($action->name,$user->authorise($action->name,$assetName));
		}

		return $result;
	}
}
