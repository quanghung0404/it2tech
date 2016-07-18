<?php
/**
 * @version    $Id: view.html.php 16294 2012-09-22 04:07:32Z giangnd $
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
 * About view of JSN ImageShow component
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class ImageShowViewAbout extends JViewLegacy
{

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */

	public function display($tpl = null)
	{
		$objJSNMsg = JSNISFactory::getObj('classes.jsn_is_message');
		// Get config parameters
		$config = JSNConfigHelper::get();
		$this->_document = JFactory::getDocument();

		JHtmlBehavior::framework();
		// Set the toolbar
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('ABOUT_ABOUT'), 'about');
		// Get messages
		$msgs = '';
		$msgs = $objJSNMsg->getList('ABOUT');
		$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);
		$this->_addAssets();
		$this-> addToolbar();
		// Display the template
		parent::display($tpl);
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

		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');

		// Add toolbar menu
		JSNISImageShowHelper::addToolbarMenu();

		// Set the submenu
		JSNISImageShowHelper::addSubmenu('about');
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */

	protected function addToolbar()
	{
		jimport('joomla.html.toolbar');
	}
}
