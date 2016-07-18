<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Forms.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewLaunchpad extends JView
{

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	function display($tpl = null)
	{
		$config = JSNConfigHelper::get();

		// Get messages
		$msgs = '';
		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('LAUNCHPAD');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}
		$presentationMethods = array(
		'0' => array('value' => '',
		'text' => '- ' . JText::_('JSN_UNIFORM_LAUNCHPAD_SELECT_PRESENTATION_METHOD') . ' -'),
		'1' => array('value' => 'menu',
		'text' => JText::_('JSN_UNIFORM_LAUNCHPAD_VIA_MENU_ITEM_COMPONENT')),
		'2' => array('value' => 'module',
		'text' => JText::_('JSN_UNIFORM_LAUNCHPAD_IN_MODULE_POSITION_MODULE')),
		'3' => array('value' => 'plugin',
		'text' => JText::_('JSN_UNIFORM_LAUNCHPAD_INSIDE_ARTICLE_CONTENT_PLUGIN'))
		);

		$this->presentationMethods = JHTML::_('select.genericList', $presentationMethods, 'presentation_method', 'disabled="disabled"' . '', 'value', 'text', "");

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);
		$this->addToolbar();
		parent::display($tpl);
		$this->_addAssets();
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 * 
	 * @since	1.6
	 */
	private function _addAssets()
	{
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/bootstrap/css/bootstrap.min.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/joomlashine/css/jsn-gui.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/joomlashine/css/jsn-view-launchpad.css');
		JSNHtmlAsset::addStyle(JURI::base(true) . '/components/com_uniform/assets/css/uniform.css');
		if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
		{
			JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.ie.css');
		}
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
		$arrayTranslated = array('JSN_UNIFORM_UPGRADE_EDITION_TITLE', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX', 'JSN_UNIFORM_YOU_MUST_SELECT_SOME_FORM', 'JSN_UNIFORM_EDIT_SELECTED_FORM', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX_DES', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION', 'JSN_UNIFORM_UPGRADE_EDITION');
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery/jquery-1.8.2.js');
		JSNHtmlAsset::addScriptLibrary('jquery.ui', '3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min', array('jquery'));
		echo JSNHtmlAsset::loadScript('uniform/launchpad', array('baseZeroClipBoard' => JSN_URL_ASSETS . '/3rd-party/jquery-zeroclipboard/ZeroClipboard.swf', 'edition' => $edition, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated)),true);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 * 
	 * @since	1.6
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('JSN_UNIFORM_LAUNCHPAD_MANAGER'), 'uniform-launchpad');
		JSNUniformHelper::buttonMenu();
	}

}
