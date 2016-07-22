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
 * JSNCheckbox field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */

class JFormFieldJSNMegaMenuType extends JSNTPLFormField
{
	public $type = 'JSNMegaMenuType';

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput ()
	{		
		$app     = JFactory::getApplication();
		$styleID = $app->input->getInt('style_id', 0);

		$languageCode = $app->input->getString('code', '');
		$menus        = $this->getMenuList($languageCode);
		$lang         = JFactory::getLanguage()->getTag();
		
		$megaMenus = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($styleID,$languageCode);
		if (count($megaMenus))
		{
			$this->value = $megaMenus->menu_type;
		}
		
		$style = '';
		$html = '';
		if (!$languageCode)
		{
			$html .= '<select id="jsn_megamenu_menutype" name="' . $this->name . '">';
			
		} else 
		{
			$html .= '<option value="" data-language="">'.JText::_('JSN_TPLFW_MEGAMENU_SELECT_MENU_TYPE').'</option>';
		}
		
		if (count($menus))
		{	
			foreach ($menus as $menu)
			{
				$selected = '';
				if (htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') == $menu->value)
				{
					$selected = ' selected="selected"';
				}
				
				if ($languageCode && $languageCode != '*' && $languageCode != $menu->language)
				{
					$style = 'style="display:none"';
				}
				
				$html .= '<option '. $style .' value="'. $menu->value . '" data-language="' . $menu->language .'"' . $selected . '>' . $menu->text . '</option>';		
			}
		}
		if (!$languageCode)
		{
			$html .= '</select>';
		}
		
		return $html;
	}
	
	/**
	 * Get Joomla's menu list
	 * 
	 *  @return array
	 */
	public function getMenuList($language = null)
	{
		$db = JFactory::getDbo();
			
		//get menu type list
		$query = $db->getQuery(true)
				->select('menutype AS value, title AS text')
				->from($db->quoteName('#__menu_types'))
				->order('title');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		
		// get published menu list
		$query = $db->getQuery(true)
		->select('menutype, language')
		->from($db->quoteName('#__menu'));
		if ($language && $language != '*')
		{
			$query->where('language = ' . $db->quote($language));
		}

		$query->where('published = ' . $db->quote((int) 1))
		->group('menutype');
		$db->setQuery($query);
		$menuLangs = $db->loadAssocList('menutype');	
		
		// get home menu
		$query = $db->getQuery(true)
		->select('menutype, language')
		->from($db->quoteName('#__menu'));
		if ($language && $language != '*')
		{
			$query->where('language = ' . $db->quote($language));
		}
		$query->where('home = ' . $db->quote((int) 1))
		->where('published = ' . $db->quote((int) 1));
		$db->setQuery($query);
		$homeLangs = $db->loadAssocList('menutype');
		
		if (is_array($menuLangs) && is_array($homeLangs))
		{
			$menuLangs = array_merge($menuLangs, $homeLangs);
		}
		
		if (is_array($menus) && is_array($menuLangs))
		{
			foreach ($menus as $key => $menu) 
			{
				if (!isset($menuLangs[$menu->value]))
				{
					unset($menus[$key]);
				}
				else 
				{
					$menu->text = $menu->text . ' [' . $menu->value . ']';
					$menu->language = isset($menuLangs[$menu->value]) ? $menuLangs[$menu->value]['language'] : '*';
				}
			}
		}
		
		return is_array($menus) ? $menus : array();
	}
}
