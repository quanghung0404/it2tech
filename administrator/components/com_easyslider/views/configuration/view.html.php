<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration view of JSN Framework EasySlider component
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JSNEasySliderViewConfiguration extends JSNConfigView
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
		$app = JFactory::getApplication();
		$input = $app->input;

		// Get config parameters
		$config = JSNConfigHelper::get();

		// Set the toolbar
		JToolbarHelper::title(JText::_('JSN_EASYSLIDER_CONFIGURATION_SETTING'));

		// Add toolbar menu
		JSNEasySliderHelper::addToolbarMenu();

		// Set the submenu
		JSNEasySliderHelper::addSubmenu('maintenance');

		// Get messages
		$msgs = '';

		if ( ! $config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('CONFIGURATION');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}

		// Assign variables for rendering
		$this->msgs = $msgs;
		$g = $input->get('g');
		if (!empty($g) && $g == 'data')
		{
			echo JSNHtmlAsset::loadScript('jsn/data', array('language' => array('JSN_EXTFW_GENERAL_CLOSE' => JText::_('JSN_EXTFW_GENERAL_CLOSE'))), true);
		}
		
		// Add assets
		JSNEasySliderHelper::addAssets();

		$this->_addAssets();
		// Display the template
		parent::display($tpl);
	}
	
	protected function _addAssets()
	{
		
		JSNHtmlAsset::addScript(JSNES_ASSETS_URL . 'js/configuration.js');
	}
}
