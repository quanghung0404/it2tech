<?php
/**
 * @version    $Id: view.html.php 17049 2012-10-15 11:43:54Z giangnd $
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

class ImageShowViewCpanel extends JViewLegacy
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

		$objJSNShowcase = JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNShowlist = JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNLog 		= JSNISFactory::getObj('classes.jsn_is_log');
		$objJSNUtils	= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNMsg 		= JSNISFactory::getObj('classes.jsn_is_message');

		$totalShowlist 	= $objJSNShowlist->countShowlist();

		$totalShowcase 	= $objJSNShowcase->getTotalShowcase();

		$this->_document = JFactory::getDocument();

		// Set the toolbar
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('CPANEL_LAUNCH_PAD'), 'launchpad');

		// Get messages
		$msgs = '';
		$msgs = $objJSNMsg->getList('LAUNCH_PAD');

		$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';

		$objJSNLog->deleteRecordLog();

		$checkModule 			= $objJSNUtils->checkIntallModule();
		$checkPluginContent 	= $objJSNUtils->checkIntallPluginContent();
		$checkPluginSystem 		= $objJSNUtils->checkIntallPluginSystem();

		if (!$checkModule || !$checkPluginContent || !$checkPluginSystem)
		{
			if (!$checkModule)
			{
				$msgNotice [] = '<li>&nbsp;&nbsp;-&nbsp;&nbsp;' . JText::_('CPANEL_JSN_IMAGESHOW_MODULE') . '</li>';
			}

			if (!$checkPluginSystem)
			{
				$msgNotice [] = '<li>&nbsp;&nbsp;-&nbsp;&nbsp;' . JText::_('CPANEL_JSN_IMAGESHOW_SYSTEM_PLUGIN') . '</li>';
			}

			if (!$checkPluginContent)
			{
				$msgNotice [] = '<li>&nbsp;&nbsp;-&nbsp;&nbsp;' . JText::_('CPANEL_JSN_IMAGESHOW_CONTENT_PLUGIN') . '</li>';
			}

			$strMsg = implode('', $msgNotice);

			JError::raiseWarning(100, JText::sprintf('CPANEL_FOLLOWING_ELEMENTS_ARE_NOT_INSTALLED', $strMsg));
		}
		$presentationMethods = array(
				'0' => array('value' => '',
						'text' => '- ' . JText::_('CPANEL_SELECT_PRESENTATION_METHOD') . ' -'),
				'1' => array('value' => 'menu',
						'text' => JText::_('CPANEL_VIA_MENU_ITEM_COMPONENT')),
				'2' => array('value' => 'module',
						'text' => JText::_('CPANEL_IN_MODULE_POSITION_MODULE')),
				'3' => array('value' => 'plugin',
						'text' => JText::_('CPANEL_INSIDE_ARTICLE_CONTENT_PLUGIN'))
		);

		$lists['presentationMethods'] 	= JHTML::_('select.genericList', $presentationMethods, 'presentation_method', 'class="jsn-gallery-selectbox" disabled="disabled"' . '', 'value', 'text', "");
		$lists['showlist'] 				= $objJSNShowlist->renderShowlistComboBox('0', JText::_('CPANEL_SELECT_SHOWLIST'), 'showlist_id', 'class="pull-left"');
		$lists['showcase'] 				= $objJSNShowcase->renderShowcaseComboBox('0', JText::_('CPANEL_SELECT_SHOWCASE'), 'showcase_id', 'class="pull-left"');
		$lists['menu'] 					= $objJSNUtils->renderMenuComboBox(null, 'Select menu', 'menutype', 'class="jsn-menutype-selection"');

		// Assign variables for rendering
		$this->assignRef('lists', $lists);
		$this->assignRef('objJSNUtils', $objJSNUtils);
		$this->assignRef('msgs', $msgs);
		$this->assignRef('totalShowlist', $totalShowlist[0]);
		$this->assignRef('totalShowcase', $totalShowcase[0]);
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

		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/joomlashine/css/jsn-view-launchpad.css');

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/showlist.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/cpanel.css');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/ZeroClipboard.js');

		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');

		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/window.js');

		JSNHtmlAsset::loadScript('imageshow/joomlashine/launchpad', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
			        'JSN_IMAGESHOW_OK',
			        'JSN_IMAGESHOW_CLOSE',
					'CPANEL_PLUGIN_SYNTAX_DETAILS',
					'CPANEL_GO',
					'CPANEL_EDIT_SELECTED_SHOWCASE',
					'CPANEL_YOU_MUST_SELECT_SOME_SHOWCASE_TO_EDIT',
					'CPANEL_EDIT_SELECTED_SHOWLIST',
					'CPANEL_YOU_MUST_SELECT_SOME_SHOWLIST_TO_EDIT',
					'JSN_IMAGESHOW_SAVE'
			    ))
		));
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */

	protected function addToolbar()
	{
		jimport('joomla.html.toolbar');
		// Add toolbar menu
		JSNISImageShowHelper::addToolbarMenu();

		// Set the submenu
		JSNISImageShowHelper::addSubmenu('cpanel');
		//$path		= JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers';
		//$toolbar 	= JToolBar::getInstance('toolbar');
	    //$toolbar->addButtonPath($path);
		//$toolbar->appendButton('JSNHelpButton', '', '', 'index.php?option=com_imageshow&controller=help&tmpl=component', 960, 480);
		//JToolBarHelper::divider();
		//$toolbar->appendButton('JSNMenuButton');
	}
}
