<?php
/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Configuration
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration view of JSN Framework Sample component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNMobilizeViewConfiguration extends JSNConfigView
{

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$this->_document = JFactory::getDocument();
		$this->_config = JSNConfigHelper::get();

		// Initialize toolbar
		JSNMobilizeHelper::initToolbar('JSN_MOBILIZE_PAGE_CONFIGURATION_TITLE', 'mobilize-config', false);

		// Get messages
		$msgs = '';

		if (!$this->_config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('CONFIGURATION');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Load the submenu.
		$input = JFactory::getApplication()->input;
		$get = $input->getArray($_GET);
		JSNMobilizeHelper::addSubmenu($input->get('view', 'configuration'));
		if (!empty($get['g']) && $get['g'] == 'data')
		{
			echo JSNHtmlAsset::loadScript('jsn/data', array('language' => array('JSN_EXTFW_GENERAL_CLOSE' => JText::_('JSN_EXTFW_GENERAL_CLOSE'))), true);
		}
		// Load assets
		JSNMobilizeHelper::loadAssets();
		// Display the template
		parent::display($tpl);

	}


}
