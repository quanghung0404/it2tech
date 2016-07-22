<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Template upgrade
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES .'/loader.php';
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES .'/libraries/element.php';

class JSNTplWidgetMegamenu extends JSNTplWidgetBase
{
	public $objJSNTplMMElement 	= null;
	
	/**
	 * Field constructor
	 *
	 * @param   JForm  $form  Form object
	 */
	public function __construct ($form = null)
	{
		// Call parent constructor
		parent::__construct($form);
		JSNTplMMLoader::register(JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers', 'JSNTplMMHelper');
		JSNTplMMLoader::register(JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers/html', 'JSNTplHelperHtml');
		
		$this->app = JFactory::getApplication();
		
		$this->objJSNTplMMElement = new JSNTplMMElement;
		$this->objJSNTplMMElement->init();
	}

	public function renderMenuAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$this->objJSNTplMMElement->elementTpl();
		$this->setResponse($this->getMenu());
		
	}
	
	public function saveMegamenuDataAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$post = $this->app->input->getArray($_POST);
		$shortCodeContent = $this->app->input->get('shortcode_content', '', 'RAW');
		$post['shortcode_content'] = $shortCodeContent;
		$template = $this->app->input->getString('template', '');
		$styleID  = $this->app->input->getInt('style_id', 0);
		$language = $this->app->input->getString('language', '*');
		
		$style = $this->getTemplateStyle($styleID, $template, $language);
		
		parse_str($post['setting_menu_item'], $settingMenuItem);
		
		
		if (count($style))
		{
			$params = json_decode($style->params, true);
			if (isset($params['megamenu']))
			{
				if ($params['megamenu']['menuType'] != $post['menu_type'])
				{
					unset($params['megamenu']);
				}
			}		


			$params['megamenu']['menuType'] = $post['menu_type'];
			$params['megamenu']['language'] = $post['language'];
			$params['megamenu']['items'][$post['menu_id']]['isMegamenu']       = $post['is_mega'];
			$params['megamenu']['items'][$post['menu_id']]['menuSetting']      = $settingMenuItem;
			$params['megamenu']['items'][$post['menu_id']]['shortcodeContent'] = $post['shortcode_content'];
			
			$this->saveMegamenuItemSettings($styleID, $params);
		}
	}	
	
	public function renderElementFormAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$isModal = 	JSNTplMMHelperFunctions::isModal();
		$modalType = $this->app->input->getString('modal_type', '');
		if ($isModal)
		{
			$instance = JSNTplMMHelperModal::getInstance($this);
			
			if (! empty($modalType))
			{
				$instance->showModal($modalType);
			}	
		}	
	}
	
	public function getMenu()
	{		
		$app   		= JFactory::getApplication();
		$input		= $app->input;
		
		$menuType  	= trim($input->getCmd('menutype', 'mainmenu'));
		$lang		= trim($input->getString('lang', '*'));
		
		$styleID	= $input->getInt('style_id', 0);
		$template	= $input->getString('template', '');
		$style 		= $this->getTemplateStyle($styleID, $template, $lang);
		
		$megamenuItems		= null;
		if (count($style))
		{
			$params = json_decode($style->params, true);
			if (isset($params['megamenu']))
			{
				if ($params['megamenu']['menuType'] == $menuType)
				{
					$megamenuItems = $params['megamenu'];
				}
			}			
		}	
		//$acl  		= trim($input->getInt('acl', 1));
		
		$attributes		= array();
		$values 		= array();
		$languages 		= array();
		$accessLevel	= array();		
		$html			= '';
		$html			.= '<input type="hidden" value="false" id="jsn-tpl-is-mega">';
		
		//menu type
		$attributes = array('menutype');
		$values     = array($menuType);
		
		//languages
		if ($lang != '*')
		{
			$languages [] = $lang;
			$languages [] = '*';
		}
		else
		{
			$languages [] = $lang;
		}	
		
		$attributes[] = 'language';
		$values[]     = $languages;
		
		//accessLevel
// 		$accessLevel = array(1, $acl);			
// 		if (in_array(3, $accessLevel))
// 		{
// 			$accessLevel[] = 2;
// 		}
		
		//$accessLevel = array_unique($accessLevel);
		//sort($accessLevel);
		
		//$attributes[] = 'access';
		//$values[]     = $accessLevel;

		$attributes[] = 'level';
		$values[]     =  1;
		
		$menu  = $app->getMenu('site');

		
		$items = $menu->getItems($attributes, $values);
		
		if (count($items))
		{
			$html .= '<ul class="nav navbar-nav" id="jsn-tpl-mm-top-level-menu">';
			$index = 0;
			foreach ($items as $item)
			{
				$isMega     = 'false';
				$containerWidth  = '600';
				$fullWidthValue = '1';
				$cssClassSuffix = '';
				if (isset($megamenuItems['items'][$item->id]))
				{
					$isMega = (string) $megamenuItems['items'][$item->id]['isMegamenu'];
					$containerWidth =  $megamenuItems['items'][$item->id]['menuSetting']['container_width'];
					$fullWidthValue =  $megamenuItems['items'][$item->id]['menuSetting']['full_width_value'];
					
					if (isset($megamenuItems['items'][$item->id]['menuSetting']['css_class_suffix_value']))
					{
						$cssClassSuffix = $megamenuItems['items'][$item->id]['menuSetting']['css_class_suffix_value'];
					}
				}
				
				
				$check       	= ($fullWidthValue == '1' ) ? 'active' : '';
				$fullWidth  	= ($check == 'active' ) ? $check : '';
				$fixedWidth 	= ($check == '' ) ? 'active' : '';
				
				$icon_menu_item = '';
				if ($isMega == 'true') 
				{
					$switch = '<button class="btn btn-small on active btn-success">' . JText::_('JSN_TPLFW_MEGAMENU_ON', true) . '</button><button class="btn btn-small off btn-default">' . JText::_('JSN_TPLFW_MEGAMENU_OFF', true) . '</button>';
					$icon_menu_item = '<span class="icon-checkmark"></span>';
				}
				else
				{
					$icon_menu_item = '<span class="icon-checkmark hide"></span>';
					$switch = '<button class="btn btn-small on btn-default">' . JText::_('JSN_TPLFW_MEGAMENU_ON', true) . '</button><button class="btn btn-small off active btn-danger">' . JText::_('JSN_TPLFW_MEGAMENU_OFF', true) . '</button>';
				}
				
				$active = ' inactive';
// 				if ($index == 0)
// 				{	
// 					$active = ' active';
// 				}
				$html .= '<li class="top-level-item' . $active . '" data-level="' . $item->level . '" data-id="' . $item->id . '">';
				$html .= '<a class="top-level-item-link" href="javascript: void(0);">' . $icon_menu_item . $item->title . '</a>';
				
				$html .= '<span class="label label-warning btn-menu-setting-popover hidden dropup" data-menu="' . $item->id . '"><span class="icon-cog"></span></span>';
				$html .= '<div class="popover bottom setting-menu-item" id="setting-menu-item-' . $item->id . '">';
				$html .= '<div class="arrow"></div>';
				$html .= '<h3 class="popover-title">
							' . JText::_('JSN_TPLFW_MEGAMENU_CONFIGURATION', true) . '
							 	<div class="btn-group btn-toggle pull-right">
								' . $switch . '
								</div>
						</h3>';
				$html .= '<div class="popover-content">
					<div class="form-horizontal setting-content">
						<input type="hidden" value="' . $isMega . '" id="jsn-tpl-is-mega-' . $item->id . '">
							<div class="control-group">
								<label class="container-label control-label">' . JText::_('JSN_TPLFW_CONTAINER_WIDTH', true) . '</label>
								<div class="container-width">
									<div id="container_group" class="btn-group">
									  <button type="button" id="full_width" class="btn btn-small btn-default ' . $fullWidth . '">' . JText::_('JSN_TPLFW_FULL', true) . '</button>
									  <button type="button" id="fixed_width" class="btn btn-small btn-default ' . $fixedWidth . '">' . JText::_('JSN_TPLFW_FIXED', true) . '</button>
									</div>
								</div>
								<div class="container-fixed-with">
									<div class="input-group">
									  <input type="number" min="100" id="container_width" name="container_width" class="input-mini container-width"  value="' . $containerWidth . '" />
									  <span class="input-group-addon">' . JText::_('px') . '</span>
									  <input type="hidden" id="full_width_value" name="full_width_value" value="' . $fullWidthValue . '" />
									</div>
	
								</div>
						  	</div>
							<div class="control-group container-css-suffix">
								<label class="container-label container-css-suffix-label control-label">' . JText::_('JSN_TPLFW_MEGAMENU_CSS_CLASS_SUFFIX_TITLE', true) . '</label>
								    <div class="input-group">
      									<input type="text" id="css_class_suffix_value" class="input-medium css-class-suffix" name="css_class_suffix_value" value="' . $cssClassSuffix . '" />
   									</div>
							</div>
					</div>
				</div';			
				$html .= '</div>';
				$html .= '</li>';
				$index++;
			}
			
			$html .= '</ul>';
		}	
		
		echo $html;
		exit();
	}
	
	public function getTotalModuleAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$key 	= JFactory::getApplication()->input->getString('kword', '');
		$key	= urldecode($key);
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__modules'));
		$query->where('client_id = 0');
		if ($key != '')
		{
			$query->where('title LIKE "%' . (string) $key . '%"');
		}	
		
		$db->setQuery($query);
		$result = $db->loadResult();
		$this->setResponse(array(
				'total' => (int) $result,
				'kword' => $key
		));
	}
	
	public function getModuleListAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$key 	= JFactory::getApplication()->input->getString('kword', '');
		$key	= urldecode($key);
		$limitstart = JFactory::getApplication()->input->getInt('start', 0);
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__modules'));
		$query->where('client_id = 0');
		if ($key != '')
		{
			$query->where('title LIKE "%' . (string) $key . '%"');
		}
		
		$db->setQuery($query, $limitstart, 20);
		$items = $db->loadObjectList();
		
		if (count($items))
		{
			$html = '';
			$client = JApplicationHelper::getClientInfo(0);
			$lang = JFactory::getLanguage();
			foreach ($items as $item)
			{
				$path = JPath::clean($client->path . '/modules/' . $item->module. '/' . $item->module. '.xml');
				
				if(file_exists($path))
				{
					$item->xml = simplexml_load_file($path);
				}else
				{
					$item->xml = null;
				}	

				$lang->load($item->module. '.sys', $client->path, null, false, true) || $lang->load($item->module. 'sys', $client->path . '/modules/'. $item->module, null, false, true);
				if (isset($item->xml) && $text = trim($item->xml->description))
				{
					$item->desc = JText::_($text);
				}  
				else 
				{
					$item->desc = JText::_('This Module is no description');
				}
			
				$moduleType = htmlentities($item->title, ENT_QUOTES, "UTF-8");
				$shortDesc	= JHTML::_('string.truncate', htmlentities($item->desc, ENT_QUOTES, "UTF-8"), 40);
				$title = JHTML::_('string.truncate', htmlentities($item->title, ENT_QUOTES, "UTF-8"), 30);
				$locked = '';
				if (!$item->published)
				{
					$locked = '<i class="icon-locked" title="Unpublished" style="float: right;"></i>';
				}
				
				$html .= '<div id="' . $item->id . '" class="jsn-item-type jsn-tpl-mm-element-module-item">
					<div class="btn jsn-tpl-mm-element-module-item-btn"  title="' . $title. '"  data-module-title="' . $title. '">
						<span>' . $item->title . $locked . '</span>
						<p>['. $moduleType .'] - '. $shortDesc .'</p>
					</div>
				</div>';
			}

			echo $html;
			exit();
		}
		else
		{
			echo '<div class="alert alert-block no-module">' . JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_MODULE_NO_MODULE_SELECTED', true) . '</div>';
		}	
		
		exit();
	}
	
	public function getTemplateStyle($id, $template, $lang = null)
	{
		$results = JSNTplMMHelperMegamenu::getTemplateStyle($id, $template);

		if (!$results)
		{
			return false;
		}
		
		$megamenuParams = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($id, $lang);
		if (count($megamenuParams))
		{
			// Merge megamenu params
			$oldParams = (object) json_decode($results->params);

			// Set new params
			$oldParams->megamenu = json_decode($megamenuParams->params);
			
			$newParams = json_encode($oldParams);
			
			if ($newParams)
			{
				$results->params = $newParams;
			}
		}
		return $results;
	}
	
	public function saveMegamenuItemSettings($styleID, $params) 
	{
		$language = $params['megamenu']['language'];
		$result = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($styleID, $language);
		$db = JFactory::getDbo();
		$date = JFactory::getDate()->toSql();
		$query = $db->getQuery(true);
		
		if (count($result))
		{			
			$query
				->update('#__jsn_tplframework_megamenu')
				->set('menu_type=' . $db->quote($params['megamenu']['menuType']))
				->set('params=' . $db->quote(json_encode($params['megamenu'])))
				->set('modified=' . $db->quote($date))
				->where('megamenu_id=' . $db->quote((int) $result->megamenu_id));
		}
		else 
		{
			$columns = array('style_id', 'language_code', 'menu_type', 'params', 'created', 'modified');
			$lang = isset($params['megamenu']['language']) ? $params['megamenu']['language'] : '*';
			$values = array(
				$db->quote((int) $styleID),
				$db->quote((string) $lang),
				$db->quote((string) $params['megamenu']['menuType']),
				$db->quote(json_encode($params['megamenu'])),
				$db->quote($date),
				$db->quote($date)
			);
			$query
				->insert($db->quoteName('#__jsn_tplframework_megamenu'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
		}
		
		$db->setQuery($query);
		try
		{
			return $db->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
			
	}
	
	public function getMegamenuLayoutAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$menuID = $this->app->input->getInt('menu_id', 0);
		$styleID = $this->app->input->getInt('style_id', 0);
		$template = $this->app->input->getString('template', '');
		$menuType = $this->app->input->getString('menutype', '');
		$language = $this->app->input->getString('language', '');
		
		$style = $this->getTemplateStyle($styleID, $template, $language);
		if (count($style))
		{
			$params = json_decode($style->params, true);
			if (isset($params['megamenu']))
			{
				if ($params['megamenu']['menuType'] == $menuType)
				{
					if (isset($params['megamenu']['items'][$menuID]))
					{
						//echo json_encode($params['megamenu']['items'][$menuID]);
						
						if ($params['megamenu']['items'][$menuID]['shortcodeContent'] != '')
						{
							
							$content = urldecode($params['megamenu']['items'][$menuID]['shortcodeContent']);
							$content = preg_replace('/^<p>(.*)<\/p>$/', '$1', $content);
							
							echo JSNTplMMHelperShortcode::doShortcodeAdmin($content, false, true);
						}	
						exit();
					}
				}
			}
		}				
		echo '';
		exit();
	}
	
	public function getModulePositionAction()
	{
		$document 			= JFactory::getDocument();
		$config 			= JFactory::getConfig();
		$secret 			= $config->get('secret');
		$defaultTemplate 	= JSNTplTemplatePositionrender::getDefaultTemplate();
					
		if ($this->app->input->getCmd('template', '') != $defaultTemplate->name)
		{
			echo JText::_('JSN_TPLFW_ERROR_THE_MODULE_POSITION_CHOOSER_IS_AVAILABLE_IF_THIS_TEMPLATE_IS_SET_HOME_TEMPLATE');
			exit();
		}
		
		$previewModulePositionsIsEnabled = JComponentHelper::getParams('com_templates')->get('template_positions_display', 0);
		
		if (!$previewModulePositionsIsEnabled)
		{
			JSNTplTemplatePositionrender::enablePreviewMode();
		}
		
		$onPositionClick = "
		if ( !$(this).hasClass('active-position') ){
			window.parent.jQuery.JSNTplMMShortcodeModulePositionSelectPosition($(this).find('p').text());
		}
		";
		
		$_customScript = "
			var changeposition;
			(function($){
				$(document).ready(function (){
					$('#jsn-tpl-megamenu').hide();
					var posOutline	= $('.jsn-position');
					var _idAlter	= false;
					if ($('.jsn-position').length == 0) {
						posOutline	= $('.mod-preview');
						_idAlter	= true;
					}else{
						posOutline.css({'z-index':'9999', 'position':'relative'});
					}
					posOutline.each(function(){
						if(_idAlter){
							previewInfo = $(this).children('.mod-preview-info').text();

							_splitted = previewInfo.split('[');
							if(_splitted.length > 1){
								posname	= _splitted[0];
							}
							_splitted = posname.split(': ');
							if(_splitted.length > 1){
								posname	= _splitted[1];
							}

							posname = $.trim(posname);

							$(this).attr('id', posname + '-jsnposition');
						}

						$(this)[0].oncontextmenu = function() {
							return false;
						}
					})
					.click(function () {
						" . $onPositionClick . "
					});
				});
			})(jQuery);
		";		
		
		JSNTplTemplatePositionrender::renderPage(JURI::root() . 'index.php?tp=1&jsntpl_position=1&secret_key=' . md5($secret));		
		echo JSNTplTemplatePositionrender::getHeader();
		echo '<div id="jsn-page-container">' . JSNTplTemplatePositionrender::getBody() . '</div>';
		echo '<link rel="stylesheet" href="' . JUri::root(true) . '/plugins/system/jsntplframework/assets/joomlashine/css/jsn-positions.css' .'" type="text/css" />';
		echo '<script type="text/javascript">' . $_customScript . '</script>';
		exit();
	}
	
	public function getMegamenuByLanguageAction()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		include_once JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/form/field.php';
		include_once JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/form/fields/jsnmegamenutype.php';
	
		$jsnMenuType = New JFormFieldJSNMegaMenuType();
	
		echo $jsnMenuType->getInput();
		exit();
	}
}
