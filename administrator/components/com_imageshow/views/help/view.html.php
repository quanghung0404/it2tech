<?php
/**
 * @version    $Id: view.html.php 16077 2012-09-17 02:30:25Z giangnd $
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
 */
class ImageShowViewHelp extends JViewLegacy
{

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 */

	public function display($tpl = null)
	{
		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNXML 		= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$objJSNMsg 		= JSNISFactory::getObj('classes.jsn_is_message');
		// Get config parameters
		$config 			= JSNConfigHelper::get();
		$this->_document 	= JFactory::getDocument();
	
		JHtmlBehavior::framework();
		// Set the toolbar
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('HELP_HELP_AND_SUPPORT'), 'help');

		$shortEdition 	= '';
		$xml			= array();

		// Get messages
		$msgs = '';
		$msgs = $objJSNMsg->getList('HELP_AND_SUPPORT');
		$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		$xml 			= $objJSNXML->parserXMLDetails();
		$shortEdition 	= $objJSNUtils->getShortEdition();

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);
		$this->assignRef('xml', $xml);
		$this->assignRef('shortEdition', $shortEdition);
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
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
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
