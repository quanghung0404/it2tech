<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
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
 * JFormFieldJSNLanguage field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/loader.php';
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers/megamenu.php';

class JFormFieldJSNLanguage extends JSNTPLFormField
{
	public $type = 'JSNLanguage';

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput ()
	{
		$app = JFactory::getApplication();
		$menus = $this->getMenuList();	
		$styleID = $app->input->getInt('id', 0);
		$megaMenus = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($styleID);
		if (count($megaMenus))
		{
			$this->value = $megaMenus->language_code;
		}
		
		$html = '<select id="jsn_megamenu_language" name="' . $this->name . '">';
		if (count($menus))
		{	
			$html .= '<option value="">'.JText::_('JSN_TPLFW_MEGAMENU_SELECT_LANGUAGE').'</option>';
			foreach ($menus as $menu)
			{
				$selected = '';
				if (htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') == $menu->value)
				{
					$selected = ' selected="selected"';
				}
				
				$html .= '<option value="'. $menu->value . '" ' . $selected . '>' . $menu->text . '</option>';		
			}
			
		}
		$html .= '</select>';
		
		return $html;
	}
	
	/**
	 * Get Joomla's menu list
	 * 
	 *  @return array
	 */
	public function getMenuList()
	{
		$menus = JHtml::_('contentlanguage.existing');
		$language_all = array(
				"value"        => "*",
				"text"         => "All",
				"title_native" => "All"
		);
		array_unshift($menus, (object) $language_all);
		
		return is_array($menus) ? $menus : array();
	}
	
}
