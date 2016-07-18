<?php
/**
 * @version    $Id: view.html.php 16537 2012-09-29 03:36:46Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Showcases view of JSN ImageShow component
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class ImageShowViewShowCases extends JViewLegacy
{
	public function display($tpl = null)
	{
		$lists				= array();
		$app				= JFactory::getApplication();
		$this->_document	= JFactory::getDocument();
		$objJSNMsg 			= JSNISFactory::getObj('classes.jsn_is_message');
		$objJSNShowcase 	= JSNISFactory::getObj('classes.jsn_is_showcase');
		//$objJSNShowcase->checkShowcaseLimition();

		$this->state 				= $this->get('State');
		// Get messages
		$msgs = '';
		$msgs = $objJSNMsg->getList('SHOWCASES');
		$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		$this->assignRef('msgs', $msgs);
		$this->_addAssets();
		$this->addToolbar();
		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */

	protected function addToolbar()
	{
		jimport('joomla.html.toolbar');
		$canDo 		= JSNISImageShowHelper::getActions();
		$bar     	= JToolBar::getInstance('toolbar');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal', 'a.modal');
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('SHOWCASE_SHOWCASES_MANAGER'), 'showcase');
		$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNShowcase 	= JSNISFactory::getObj('classes.jsn_is_showcase');
		//$limitStatus		= $objJSNUtils->checkLimit();
		//$count 				= $objJSNShowcase->countShowcase();

		//if (@$count[0] >= 3 && $limitStatus == true)
		//{
			//$bar->appendButton('Custom', '<button class="btn btn-small btn-success jsn-popup-upgrade disabled"><i class="icon-new icon-white"> </i>' . JText::_('JTOOLBAR_NEW') . '</button>');
		//}
		//else
		//{
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew();
		}
		//}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList();
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList();
			JToolBarHelper::divider();
		}
		// Add toolbar menu
		JSNISImageShowHelper::addToolbarMenu();

		// Set the submenu
		JSNISImageShowHelper::addSubmenu('showcases');
	}

	/**
	 * Add nesscessary JS & CSS files
	 *
	 * @return void
	 */

	private function _addAssets()
	{
		$objJSNMedia = JSNISFactory::getObj('classes.jsn_is_media');

		! class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/mediamanager.css');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');
		JSNHtmlAsset::loadScript('imageshow/joomlashine/showcases', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_OK',
						'JSN_IMAGESHOW_CLOSE',
						'CPANEL_UPGRADE_TO_PRO_EDITION_FOR_MORE',
						'UPGRADE_TO_PRO_EDITION',
						'JSN_IMAGESHOW_SHOWCASE_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SHOWCASES_IN_FREE_EDITION'
				))
		));
	}
}