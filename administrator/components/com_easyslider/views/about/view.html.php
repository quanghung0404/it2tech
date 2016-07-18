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
 * About view
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JSNEasySliderViewAbout extends JSNBaseView
{
	/**
	 * Display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();

		// Set the toolbar
		JToolbarHelper::title(JText::_('JSN_EASYSLIDER_ABOUT_TITLE'));

		// Add toolbar menu
		JSNEasySliderHelper::addToolbarMenu();

		// Set the submenu
		JSNEasySliderHelper::addSubmenu('about');

		// Get messages
		$msgs = '';

		if ( ! $config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('ABOUT');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Add assets
		JSNEasySliderHelper::addAssets();

		// Display the template
		parent::display($tpl);
	}
}
