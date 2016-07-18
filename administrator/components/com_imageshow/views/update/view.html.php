<?php
/**
 * @version    $Id: view.html.php 16609 2012-10-02 09:23:05Z haonv $
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

jimport('joomla.application.component.view');
class ImageShowViewUpdate extends JSNUpdateView
{
	/**
	 * Display method
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();

		// Add assets
		$this->_document = JFactory::getDocument();

		// Get messages
		$msgs = '';
		if ( ! $config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('CONFIGURATION');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);
		$this->_addAssets();
		$this->addToolbar();
		// Display the template
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

		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('UPDATE_UPDATE'));
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

		//$this->_document->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');
		//JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
	}
}