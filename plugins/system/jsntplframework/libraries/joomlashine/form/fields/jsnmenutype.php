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
class JFormFieldJSNMenuType extends JSNTPLFormField
{
	public $type = 'JSNMenuType';

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput ()
	{
		$menus = $this->getMenuList();	
		$html = '<select id="' . strtolower($this->id) . '" name="' . $this->name . '">';
		
		if (count($menus))
		{	
			foreach ($menus as $menu)
			{
				$selected = '';
				if (htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') == $menu->value)
				{
					$selected = ' selected="selected"';
				}
				
				$html .= '<option value="'. $menu->value . '" data-language="' . $menu->language .'"' . $selected . '>' . $menu->text . '</option>';		
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
		->from($db->quoteName('#__menu'))
		->where('published = ' . $db->quote((int) 1))
		->group('menutype');
		$db->setQuery($query);
		$menuLangs = $db->loadAssocList('menutype');

		// get home menu
		$query = $db->getQuery(true)
		->select('menutype, language')
		->from($db->quoteName('#__menu'))
		->where('home = ' . $db->quote((int) 1))
		->where('published = ' . $db->quote((int) 1));
		$db->setQuery($query);
		$homeLangs = $db->loadAssocList('menutype');
		
		if (is_array($menuLangs) && is_array($homeLangs))
		{
			$menuLangs = array_merge($menuLangs, $homeLangs);
		}
		
		if (is_array($menus) && is_array($menuLangs))
		{
			foreach ($menus as $menu) 
			{
				$menu->text = $menu->text . ' [' . $menu->value . ']';
				$menu->language = isset($menuLangs[$menu->value]) ? $menuLangs[$menu->value]['language'] : '*';
			}
		}
		
		return is_array($menus) ? $menus : array();
	}
}
