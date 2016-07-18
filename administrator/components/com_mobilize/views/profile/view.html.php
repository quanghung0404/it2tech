<?php


/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Form
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
// Load class for rendering edit page
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libraries' . DS . 'joomlashine' . DS . 'mobilize.php';
/**
 * View class for a list of Form.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNMobilizeViewProfile extends JSNBaseView
{

	protected $_document;

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
	public function display($tpl = null)
	{
		// Initialize variables
		$input = JFactory::getApplication()->input;

		$this->_document = JFactory::getDocument();
		$this->_item = $this->get('Item');
		$this->_style = new stdClass();
		$this->_styleContainer = "";
		$this->_styleModule = "";
		$this->_styleContentTitle = "";
		$this->_styleContentBody = "";
		$this->_form = $this->get('Form');
		$this->_menuType = $this->get("MenuType");
		$dataDesign = $this->get("DataDesign");
		$this->_styleIcon = "";
		$this->_os = $this->get("DataOS");
		$osSupport = $this->get("DataOSSupport");
		$this->_osSupport = array();
		if (!empty($osSupport))
		{
			foreach ($osSupport as $os)
			{
				$this->_osSupport[] = $os->os_id;
			}
		}
		$this->_dataDesign = array();
		if (!empty($dataDesign))
		{
			foreach ($dataDesign as $item)
			{
				$value = json_decode($item->value);
				if (!empty($value))
				{
					$this->_dataDesign[$item->name] = $value;
				}
				else
				{
					$this->_dataDesign[$item->name] = $item->value;
				}
			}
		}

		$this->_style = !empty($this->_dataDesign['mobilize-style']) ? $this->_dataDesign['mobilize-style'] : "";
		if (empty($this->_style->jsn_menu))
		{
			@$this->_style->jsn_menu = '[{"key":"jsn_menu_container_bo_borderThickness","value":""},{"key":"jsn_menu_container_bo_borderStyle","value":"hidden"},{"key":"jsn_menu_container_bo_borderColor","value":""},{"key":"jsn_menu_container_ba_backgroundType","value":"Solid"},{"key":"jsn_menu_container_ba_soildColor","value":"#282828"},{"key":"jsn_menu_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_menu_container_ba_activeColor","value":"#404040"},{"key":"jsn_menu_container_ic_iconColor","value":"#ffffff"},{"key":"jsn_menu_sublevel1_bo_borderThickness","value":""},{"key":"jsn_menu_sublevel1_bo_borderStyle","value":"hidden"},{"key":"jsn_menu_sublevel1_bo_borderColor","value":""},{"key":"jsn_menu_sublevel1_ba_normalColor","value":"#333333"},{"key":"jsn_menu_sublevel1_ba_activeColor","value":""},{"key":"jsn_menu_sublevel1_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel1_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel1_fo_fontSize","value":""},{"key":"jsn_menu_sublevel1_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel1_fo_fontColor","value":""},{"key":"jsn_menu_sublevel2_ba_normalColor","value":""},{"key":"jsn_menu_sublevel2_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_menu_sublevel2_fo_fontFace","value":"Verdana"},{"key":"jsn_menu_sublevel2_fo_fontSize","value":""},{"key":"jsn_menu_sublevel2_fo_fontStyle","value":"inherit"},{"key":"jsn_menu_sublevel2_fo_fontColor","value":""}]';
		}
		if (empty($this->_style->jsn_content_top))
		{
			@$this->_style->jsn_content_top = '[{"key":"jsn_content_top_container_bo_borderThickness","value":""},{"key":"jsn_content_top_container_bo_borderStyle","value":"hidden"},{"key":"jsn_content_top_container_bo_borderColor","value":""},{"key":"jsn_content_top_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_container_ba_soildColor","value":"#404040"},{"key":"jsn_content_top_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_container_sp_padding","value":""},{"key":"jsn_content_top_module_tabContainer_bo_borderThickness","value":""},{"key":"jsn_content_top_module_tabContainer_bo_borderStyle","value":"hidden"},{"key":"jsn_content_top_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_top_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_top_module_tabContainer_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_top_module_tabContainer_bo_roundedCornerRadius","value":""},{"key":"jsn_content_top_module_tabContainer_sh_shadowSpread","value":""},{"key":"jsn_content_top_module_tabContainer_sh_shadowColor","value":""},{"key":"jsn_content_top_module_tabContainer_sp_margin","value":""},{"key":"jsn_content_top_module_tabContainer_sp_padding","value":""},{"key":"jsn_content_top_module_tabContent_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_module_tabContent_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_module_tabContent_title_fo_fontSize","value":""},{"key":"jsn_content_top_module_tabContent_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_module_tabContent_title_fo_fontColor","value":""},{"key":"jsn_content_top_module_tabContent_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_top_m_ct_fo_fontFace","value":"Verdana"},{"key":"jsn_content_top_module_tabContent_body_fo_fontSize","value":""},{"key":"jsn_content_top_module_tabContent_body_fo_fontStyle","value":"inherit"},{"key":"jsn_content_top_module_tabContent_body_fo_fontColor","value":""},{"key":"jsn_content_top_module_tabContent_link_linkColor","value":""}]';
		}
		if (empty($this->_style->jsn_mainbody))
		{
			@$this->_style->jsn_mainbody = '[{"key":"jsn_mainbody_container_sp_paddingleft","value":"20"},{"key":"jsn_mainbody_container_sp_paddingright","value":"20"},{"key":"jsn_mainbody_container_sp_paddingtop","value":"10"},{"key":"jsn_mainbody_container_sp_paddingbottom","value":"10"}]';
		}
		if (empty($this->_style->jsn_user_bottom))
		{
			@$this->_style->jsn_user_bottom = '[{"key":"jsn_user_bottom_container_bo_borderThickness","value":""},{"key":"jsn_user_bottom_container_bo_borderStyle","value":"hidden"},{"key":"jsn_user_bottom_container_bo_borderColor","value":""},{"key":"jsn_user_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_user_bottom_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_container_sp_padding","value":""},{"key":"jsn_user_bottom_module_tabContainer_bo_borderThickness","value":""},{"key":"jsn_user_bottom_module_tabContainer_bo_borderStyle","value":"hidden"},{"key":"jsn_user_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_user_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_user_bottom_module_tabContainer_bo_roundedCornerRadius","value":""},{"key":"jsn_user_bottom_module_tabContainer_sh_shadowSpread","value":""},{"key":"jsn_user_bottom_module_tabContainer_sh_shadowColor","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_margin","value":""},{"key":"jsn_user_bottom_module_tabContainer_sp_padding","value":""},{"key":"jsn_user_bottom_module_tabContent_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_module_tabContent_title_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_module_tabContent_title_fo_fontSize","value":""},{"key":"jsn_user_bottom_module_tabContent_title_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_module_tabContent_title_fo_fontColor","value":""},{"key":"jsn_user_bottom_module_tabContent_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_user_bottom_m_ct_fo_fontFace","value":"Verdana"},{"key":"jsn_user_bottom_module_tabContent_body_fo_fontSize","value":""},{"key":"jsn_user_bottom_module_tabContent_body_fo_fontStyle","value":"inherit"},{"key":"jsn_user_bottom_module_tabContent_body_fo_fontColor","value":""},{"key":"jsn_user_bottom_module_tabContent_link_linkColor","value":""}]';
		}
		if (empty($this->_style->jsn_content_bottom))
		{
			@$this->_style->jsn_content_bottom = '[{"key":"jsn_content_bottom_container_bo_borderThickness","value":""},{"key":"jsn_content_bottom_container_bo_borderStyle","value":"hidden"},{"key":"jsn_content_bottom_container_bo_borderColor","value":""},{"key":"jsn_content_bottom_container_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_container_ba_soildColor","value":"#d9d9d9"},{"key":"jsn_content_bottom_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_container_sp_padding","value":""},{"key":"jsn_content_bottom_module_tabContainer_bo_borderThickness","value":""},{"key":"jsn_content_bottom_module_tabContainer_bo_borderStyle","value":"hidden"},{"key":"jsn_content_bottom_module_tabContainer_bo_borderColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_backgroundType","value":"Solid"},{"key":"jsn_content_bottom_module_tabContainer_ba_soildColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_content_bottom_module_tabContainer_bo_roundedCornerRadius","value":""},{"key":"jsn_content_bottom_module_tabContainer_sh_shadowSpread","value":""},{"key":"jsn_content_bottom_module_tabContainer_sh_shadowColor","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_margin","value":""},{"key":"jsn_content_bottom_module_tabContainer_sp_padding","value":""},{"key":"jsn_content_bottom_module_tabContent_title_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_module_tabContent_title_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_module_tabContent_title_fo_fontSize","value":""},{"key":"jsn_content_bottom_module_tabContent_title_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_module_tabContent_title_fo_fontColor","value":""},{"key":"jsn_content_bottom_module_tabContent_body_fo_fontFaceType","value":"standard fonts"},{"value":""},{"value":""},{"key":"jsn_content_bottom_m_ct_fo_fontFace","value":"Verdana"},{"key":"jsn_content_bottom_module_tabContent_body_fo_fontSize","value":""},{"key":"jsn_content_bottom_module_tabContent_body_fo_fontStyle","value":"inherit"},{"key":"jsn_content_bottom_module_tabContent_body_fo_fontColor","value":""},{"key":"jsn_content_bottom_module_tabContent_link_linkColor","value":""}]';
		}
		if (empty($this->_style->jsn_logo))
		{
			@$this->_style->jsn_logo = '[{"key":"jsn_logo_container_bo_borderThickness","value":""},{"key":"jsn_logo_container_bo_borderStyle","value":"solid"},{"key":"jsn_logo_container_bo_borderColor","value":""},{"key":"jsn_logo_container_ba_backgroundType","value":"Solid"},{"key":"jsn_logo_container_ba_soildColor","value":""},{"key":"jsn_logo_container_ba_gradientColor","value":"-moz-linear-gradient(-90deg, #ffffff 0%, #ffffff 100%)"},{"key":"jsn_logo_container_sp_paddingleft","value":""},{"key":"jsn_logo_container_sp_paddingright","value":""},{"key":"jsn_logo_container_sp_paddingbottom","value":""},{"key":"jsn_logo_container_sp_paddingtop","value":""},{"key":"jsn_logo_content_alignment","value":"center"}]';
		}
		$this->_JSNMobilize = new JSNMobilize($this->_dataDesign);
		$this->_Modules = $this->_JSNMobilize->getModules();
		$this->_defaultTempateSite = $this->_JSNMobilize->getTemplateDefault();
		// Hide the main menu
		$input->set('hidemainmenu', true);

		// Initialize toolbar
		$this->initToolbar();

		// Get config
		$config = JSNConfigHelper::get();
		$msgs = '';

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('PROFILE');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}
		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Display the template
		parent::display($tpl);
		JSNMobilizeHelper::loadAssets();
		$this->addAssets();
	}

	/**
	 * Setup toolbar.
	 *
	 * @return void
	 */
	protected function initToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::apply('profile.apply');
		JToolBarHelper::save('profile.save');
		JToolBarHelper::cancel('profile.cancel', 'JSN_MOBILIZE_CLOSE');
		!JSNVersion::isJoomlaCompatible('2.5') OR JToolBarHelper::divider();
		JSNMobilizeHelper::initToolbar('JSN_MOBILIZE_PROFILE_PAGETITLE', 'mobilize-profiles', false);

	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addAssets()
	{
		// Initialize variables
		$config 	= JSNConfigHelper::get();
		$jUri 		= JURI::getInstance();
		$pathOnly 	= JURI::root(true);
		$doc        = JFactory::getDocument();
		
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-colorpicker/css/colorpicker.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-gradientpicker/jquery.gradientPicker.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/joomlashine/css/jsn-advanced-parameters.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-select2/select2.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/codemirror/lib/codemirror.css');
		//JSNHtmlAsset::addScript(JSN_MOBILIZE_ASSETS_URL . '/js/jsn.jquery.noconflict.js');
		$device = "mobilize";
		$links = ($links = $jUri->getScheme()) . (empty($links) ? '' : '://');

		if (substr($config->get("link_{$device}"), 0, 1) == '/')
		{
			$links .= $jUri->getHost() . $config->get("link_{$device}");
		}
		else
		{
			$links .= $config->get("link_{$device}") . JURI::root(true);
		}

		// Setup text translation
		$arrayTranslated = array('JSN_MOBILIZE_SWITCHER_TITLE',
			'JSN_MOBILIZE_YOU_CAN_NOT_HIDE_THE_COPYLINK',
			'JSN_MOBILIZE_SWITCHER_SETTINGS',
			'JSN_MOBILIZE_CANCEL',
			'JSN_MOBILIZE_SAVE',
			'JSN_MOBILIZE_ALIGNMENT',
			'JSN_MOBILIZE_SELECT_THEME',
			'JSN_MOBILIZE_SELECT',
			'JSN_MOBILIZE_CLEAR',
			'JSN_MOBILIZE_IMAGE_ALT',
			'JSN_MOBILIZE_IMAGE_URL',
			'JSN_MOBILIZE_ENABLE_MOBILIZE_MENU_LINK',
			'JSN_MOBILIZE_ENABLE_MOBILIZE_SEARCH_LINK',
			'JSN_MOBILIZE_ENABLE_MOBILIZE_LOGIN_LINK',
			'JSN_MOBILIZE_CHANGE',
			'JSN_MOBILIZE_CLICK_SELECT',
			'JSN_MOBILIZE_TITLE_SMARTPHONE',
			'JSN_MOBILIZE_TITLE_TABLET',
			'JSN_MOBILIZE_TYPE_POSITION',
			'JSN_MOBILIZE_TYPE_MODULE',
			'JSN_MOBILIZE_SELECT_MENU',
			'JSN_MOBILIZE_SELECT_MODULE',
			'JSN_MOBILIZE_SELECTED_MODULE',
			'JSN_MOBILIZE_CLOSE',
			'JSN_MOBILIZE_SELECT_LOGO',
			'JSN_MOBILIZE_SELECT_STYLE',
			'JSN_MOBILIZE_SELECT_POSITION',
			'JSN_MOBILIZE_SELECT_MODULE',
			'JSN_MOBILIZE_ENABLE_MOBILIZE_SWITCHER_LINK',
			'JSN_MOBILIZE_ADD_ELEMENT',
			'JSN_MOBILIZE_ADD_MODULE',
			'JSN_MOBILIZE_ADD_POSITION',
			'JSN_MOBILIZE_IMAGE_FILE',
			'JSN_MOBILIZE_YES',
			'JSN_MOBILIZE_NO',
			'JSN_MOBILIZE_CONFIRM_LOAD_STYLE',
			'JSN_MOBILIZE_SWITCH_TO_WEB_UI_FOR_MOBILIZE',
			'JSN_MOBILIZE_STYLE_SETTINGS_IS_AVAILABLE_ONLY_IN_PRO_EDITION',
			'JSN_MOBILIZE_UPGRADE_NOW',
			'JSN_MOBILIZE_LINK_SOCIAL',
			'JSN_MOBILIZE_DELETE',
			'JSN_MOBILIZE_BROWSER',
			'JSN_MOBILIZE_UPLOAD',
			'JSN_MOBILIZE_UPGRADE_EDITION_TITLE', 'JSN_MOBILIZE_UPGRADE_EDITION', 'JSN_MOBILIZE_ADD_ELEMENT_IS_AVAILABLE_ONLY_IN_PRO_EDITION');
		// Initialize Javascript
		$edition = defined('JSN_MOBILIZE_EDITION') ? JSN_MOBILIZE_EDITION : "free";
		$token   = JSession::getFormToken();
		//echo $token;die;
		echo JSNHtmlAsset::loadScript('mobilize/profile', array('token' => $token,'editions' => $edition, 'pathRoot' => JURI::root(), 'defaultTemplate' => $this->_defaultTempateSite, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated), 'listMenu' => $this->_menuType, 'listModule' => $this->_Modules, 'mobilizeLink' => $links, 'configuration' => isset($this->_items['configuration']) ? $this->_items['configuration'] : ''), true);

		$app 			= JFactory::getApplication();
		$templateName 	= $app->getTemplate();
		$templateJSPath = JPATH_ROOT . '/administrator/templates/' . $templateName . '/js/template.js';

		if (file_exists($templateJSPath))
		{
			if (method_exists($doc, 'addScriptVersion'))
			{
				$doc->addScriptVersion('templates/' . $templateName . '/js/template.js');
			}
		}

		JSNMobilizeHelper::loadAssets();
	}
	/**
	 * Convert stdClass to use as array
	 * @param $val stdClass
	 * @return array
	 *
	 */
	public function arrVal($val){
		foreach ($val as $ky=>$vl){
			$arr[$ky] = $vl; 
		}
		return $arr;
	}
}