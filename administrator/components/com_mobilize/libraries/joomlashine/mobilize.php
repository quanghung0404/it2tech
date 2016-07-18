<?php

/**
 * @version     $Id: mobilize.php 15520 2012-08-27 08:20:36Z cuongnm $
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
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
 * Layout class.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilize
{

	private $_contentData = null;

	private $_dataModules = null;

	/**
	 * Class constructor.
	 *
	 * @param   string  $contentData  Preset content.
	 */
	public function __construct($contentData = null)
	{
		$this->_contentData = $contentData;
		$this->_dataModules = $this->getModules();
	}

	/**
	 * Generate HTML content block.
	 *
	 * @param   type  $name   Name of content block.
	 *
	 * @param   type  $class  Class style
	 *
	 * @return  string
	 */
	function getBlockContent($name, $class = '')
	{
		$edition = defined('JSN_MOBILIZE_EDITION') ? JSN_MOBILIZE_EDITION : "free";
		if (strtolower($edition) == "free" && in_array($name, array('mobilize-user-top-left', 'mobilize-user-top-right', 'mobilize-content-top-left', 'mobilize-content-top-right', 'mobilize-user-bottom-left', 'mobilize-user-bottom-right', 'mobilize-content-bottom-left', 'mobilize-content-bottom-right')))
		{
			$html = '<div id="' . $name . '" class="jsn-column ' . $class . '">
					<div class="jsn-element-disabale-container"></div>
					<div class="mobile-phone-block-action row-fluid">
						<input type="hidden" name="jsnmobilize[' . $name . '][]" value="" />
						<a class="jsn-add-more" title="' . JText::_('JSN_MOBILIZE_ADD_ELEMENT') . '" href="javascript:void(0);">' . JText::_('JSN_MOBILIZE_ADD_ELEMENT') . ' <span class="label label-important">PRO</span></a>
					</div>
				</div>';
		}
		else
		{
			$html = '<div id="' . $name . '" class="jsn-column ' . $class . '">
					<div class="jsn-element-container">
						' . $this->getItemsBlockContent($name) . '
					</div>
					<div class="mobile-phone-block-action row-fluid">
						<input type="hidden" name="jsnmobilize[' . $name . '][]" value="" />
						<a class="jsn-add-more" title="' . JText::_('JSN_MOBILIZE_ADD_ELEMENT') . '" href="javascript:void(0);">' . JText::_('JSN_MOBILIZE_ADD_ELEMENT') . '</a>
					</div>
				</div>';
		}
		return $html;
	}

	/**
	 * Get items content block.
	 *
	 * @param   type  $name  Name of content block.
	 *
	 * @return  string
	 */
	function getItemsBlockContent($name)
	{

		$html = '';

		$mobilizeBlockContent = isset($this->_contentData[$name]) ? $this->_contentData[$name] : '';

		if (!empty($mobilizeBlockContent) && count($mobilizeBlockContent))
		{

			foreach ($mobilizeBlockContent as $value => $type)
			{
				if ($type == "module")
				{
					$nameItem = isset($this->_dataModules['getById'][$value]) ? $this->_dataModules['getById'][$value] : '';
				}
				else
				{
					$nameItem = $value;
				}
				$html .= '<div class="jsn-element ui-state-default jsn-iconbar-trigger" data-value="' . $value . '" data-type="' . $type . '" >';
				$html .= ' <div class="jsn-element-content">
						    <span class="type-element">' . Jtext::_("JSN_MOBILIZE_TYPE_" . strtoupper($type)) . ': </span>
						    <span class="name-element">' . $nameItem . '</span>
						    <input type="hidden"  class="data-block-mobilize" name="jsnmobilize[' . $name . '][]" value=\'' . json_encode(array($value => $type)) . '\' />
						   </div>';
				$html .= '<div class="jsn-iconbar">
						    <a class="element-edit" title="Change ' . Jtext::_("JSN_MOBILIZE_TYPE_" . strtoupper($type)) . '" href="javascript:void(0)"><i class="icon-pencil"></i></a>
						    <a class="element-delete" title="Delete ' . Jtext::_("JSN_MOBILIZE_TYPE_" . strtoupper($type)) . '" href="javascript:void(0)"><i class="icon-trash"></i></a>
					    </div>
				    </div>';
			}
		}

		return $html;
	}

	/**
	 * Get items logo
	 *
	 * @param   type  $name   Logo name
	 * @param   type  $title  Logo title
	 *
	 * @return string
	 */
	function getItemsLogo($name, $title,$style)
	{
		$mobilizeLogo = isset($this->_contentData[$name]) ? $this->_contentData[$name] : '';
		$mobilizeLogoSrc = !empty($mobilizeLogo) && is_object($mobilizeLogo) && key($mobilizeLogo) != '_empty_' ? key($mobilizeLogo) : '';
		$mobilizeLogoSlogan = isset($mobilizeLogo->$mobilizeLogoSrc) ? $mobilizeLogo->$mobilizeLogoSrc : '';
		if (empty($mobilizeLogoSlogan) && !empty($mobilizeLogo->_empty_))
		{
			$mobilizeLogoSlogan = $mobilizeLogo->_empty_;
		}
		$text = "";
		$class = "";
		if (!empty($mobilizeLogoSrc))
		{
			$text = '<span style="display:none;" class="jsn-select-logo">' . JText::_($title) . '</span><img  src="' . JURI::root() . $mobilizeLogoSrc . '" alt="' . $mobilizeLogoSlogan . '" />';
		}
		else
		{
			$class = "jsn-logo-null link-menu-mobilize";
			$text = '<i><span class="jsn-select-logo">' . JText::_($title) . '</span></i><img src="" />';
		}
		$value = htmlspecialchars (json_encode(array($mobilizeLogoSrc => $mobilizeLogoSlogan)), ENT_QUOTES, 'UTF-8');
		$html = '<input type="hidden" class="data-mobilize" data-id="' . $mobilizeLogoSrc . '" name="jsnmobilize[' . $name . ']" value=\'' . $value . '\'/>
			<a id="jsn_mobilize_select_logo" title="' . JText::_($title) . '" data-type="' . $name . '" href="javascript:void(0)" class="element-edit ' . $class . '" data-state="' . $mobilizeLogoSlogan . '" data-value="' . $mobilizeLogoSrc . '" style="'.$style.'">' . $text . '</a>';
		return $html;
	}

	/**
	 * Get items menu icon.
	 *
	 * @param   string  $name   Menu name.
	 * @param   string  $title  Menu title.
	 * @param   string  $type   Menu type.
	 * @param   String  $icon   Menu Icon
	 * @param   String  $popup  Position Popup
	 * @param   String  $style  style
	 * @return  string
	 */
	function getItemsMenuIcon($name, $title, $type = '', $icon = '', $style = '')
	{
		$mobilizeMenu = isset($this->_contentData[$name]) ? $this->_contentData[$name] : '';
		$mobilizeMenuId = !empty($mobilizeMenu) && is_object($mobilizeMenu) && key($mobilizeMenu) != '_empty_' ? key($mobilizeMenu) : '';
		$mobilizeMenuText = isset($mobilizeMenu->$mobilizeMenuId) ? $mobilizeMenu->$mobilizeMenuId : '';
		$mobilizeMenuState = isset($mobilizeMenu->$mobilizeMenuId) ? $mobilizeMenu->$mobilizeMenuId : '';
		$text = "<i style='{$style}' class=\"{$icon}\"></i>";
		$language = JFactory::getLanguage()->getTag();

		if($name == 'mobilize-menu'){
			$menuValues = json_encode($this->_contentData['mobilize-menu-language']);
			foreach($this->_contentData['mobilize-menu-language'] as $id => $menuLang){

				if($menuLang == $language){
					$mobilizeMenuId = $id;
					$mobilizeMenuText = $this->getMenuName($id);
				}
			}

		}
		$value = json_encode(array($mobilizeMenuId => $mobilizeMenuText));
		if ($name != "mobilize-switcher")
		{
			$html = '<li>
					<a title="' . JText::_($title) . '" data-type="' . $name . '" onclick="return false;" class="link-menu-mobilize" data-value="' . $mobilizeMenuId . '" href="#">' . $text . '</a>
					<input type="hidden" class="data-mobilize" data-id="' . $mobilizeMenuId . '" name="jsnmobilize[' . $name . ']" value=\'' . $value . '\'/>';
			if($name == 'mobilize-menu'){
				$html .=	'<input type="hidden" class="data-mobilize-language" name="jsnmobilize[mobilize-menu-language]" value=\'' . $menuValues . '\'/>';
				$html .=	'<input type="hidden" class="data-language" name="jsnmobilize[site-language]" value=\'' . $language . '\'/>';
			}

			$html .= '</li>';
		}
		else
		{
			$text = !empty($mobilizeMenuId) ? $mobilizeMenuId : JText::_($title);
			$html = '<button title="' . JText::_($title) . '" onclick="return false;" data-value="' . $mobilizeMenuId . '"  data-state="' . $mobilizeMenuState . '" data-type="' . $name . '" class="btn btn-switcher">' . $text . '</button>
					<input type="hidden" class="data-mobilize" name="jsnmobilize[' . $name . ']" value=\'' . $value . '\'/>';
			if($name == 'mobilize-menu'){
				$html .=	'<input type="hidden" class="data-mobilize" data-id="' . $mobilizeMenuId . '" name="jsnmobilize[' . $name . ']" value=\'' . $value . '\'/>';
				$html .=	'<input type="hidden" class="data-language" name="jsnmobilize[site-language]" value=\'' . $language . '\'/>';
			}
		}

		return $html;
	}

	/**
	 * Get modules list.
	 *
	 * @return  array
	 */
	function getModules()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		//build the list of categories
		$query->select('*')->from('#__modules');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$modulesList = array();
		if (!empty($data) && count($data) > 1)
		{
			foreach ($data as $item)
			{
				$modulesList['getById'][$item->id] = $item->title;
				$modulesList['getByPosition'][$item->position][] = $item->title;
			}
		}
		return $modulesList;
	}

	/**
	 * Get default site template.
	 *
	 * @return  array
	 */
	function getTemplateDefault()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		//build the list of categories
		$query->select('template')->from('#__template_styles')->where("client_id=0")->where("home=1");
		$db->setQuery($query);
		return $db->loadResult();
	}

	function getMenuName($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		//build the list of categories
		$query->select('title')->from('#__menu_types')->where("id=".$id);
		$db->setQuery($query);
		return $db->loadResult();
	}
}
