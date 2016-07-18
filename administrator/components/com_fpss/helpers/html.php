<?php
/**
 * @version		$Id: html.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSHelperHTML
{

	public static function title($title, $icon = 'fpss-logo.png')
	{
		JToolBarHelper::title(JText::_($title), $icon);
	}

	public static function toolbar()
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view', 'slides');
		$task = JRequest::getCmd('task');
		if ($view == 'slides' || $view == 'categories')
		{
			if ($view == 'slides' && (version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.edit.state', $option)))
			{
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					JToolBarHelper::custom('featured', 'featured.png', 'featured_f2.png', 'FPSS_TOGGLE_FEATURED_STATE', true);
				}
				else
				{
					JToolBarHelper::custom('featured', 'default.png', 'default_f2.png', 'FPSS_TOGGLE_FEATURED_STATE', true);
				}
			}
			if ($view == 'categories' || version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.edit.state', $option))
			{
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
			}
			if ($view == 'categories' || version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.create', $option))
			{
				JToolBarHelper::addNew();
			}
			if ($view == 'categories' || version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.edit', $option) || $user->authorise('core.edit.own', $option))
			{
				JToolBarHelper::editList();
			}
			if ($view == 'categories' || version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.delete', $option))
			{
				$warning = ($view == 'slides') ? 'FPSS_ARE_YOU_SURE_THAT_YOU_WANT_TO_DELETE_THESE_SLIDES_THIS_ACTION_CANNOT_BE_UNDONE' : 'FPSS_ARE_YOU_SURE_THAT_YOU_WANT_TO_DELETE_THESE_CATEGORIES_ASSIGNED_SLIDES_TO_THESE_CATEGORIES_WILL_BE_DELETED_TOO_THIS_ACTION_CANNOT_BE_UNDONE';
				JToolBarHelper::deleteList($warning);
			}
			JToolBarHelper::divider();
		}
		if ($view == 'slide' || $view == 'category')
		{
			JToolBarHelper::save();
			if ($view == 'slide')
			{
				JToolBarHelper::save('saveAndNew', 'FPSS_SAVE_AND_NEW');
			}
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
		}
		if ($view != 'slide' && $view != 'category')
		{
			if (version_compare(JVERSION, '1.6.0', 'lt') || $user->authorise('core.admin', $option))
			{
				JToolBarHelper::preferences('com_fpss', '480', '640', 'FPSS_OPTIONS');
			}
		}
	}

	public static function subMenu()
	{
		$view = JRequest::getCmd('view', 'slides');
		JSubMenuHelper::addEntry(JText::_('FPSS_SLIDES'), 'index.php?option=com_fpss&view=slides', $view == 'slides');
		JSubMenuHelper::addEntry(JText::_('FPSS_CATEGORIES'), 'index.php?option=com_fpss&view=categories', $view == 'categories');
		JSubMenuHelper::addEntry(JText::_('FPSS_INFORMATION'), 'index.php?option=com_fpss&view=info', $view == 'info');
	}

	public static function published(&$row, $i)
	{
		$db = JFactory::getDBO();
		if (!isset($row->publish_up))
		{
			$row->publish_up = $db->getNullDate();
		}
		if (!isset($row->publish_down))
		{
			$row->publish_down = $db->getNullDate();
		}
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			return JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb', $row->publish_up, $row->publish_down);
		}
		else
		{
			$db = JFactory::getDBO();
			$nullDate = $db->getNullDate();
			$now = JFactory::getDate();
			$config = JFactory::getConfig();
			$publish_up = JFactory::getDate($row->publish_up);
			$publish_down = JFactory::getDate($row->publish_down);
			$offset = version_compare(JVERSION, '3.0', 'ge') ? $config->get('offset') : $config->getValue('config.offset');
			$publish_up->setOffset($offset);
			$publish_down->setOffset($offset);
			$img = 'tick.png';
			if ($now->toUnix() <= $publish_up->toUnix() && $row->published == 1)
			{
				$img = 'publish_y.png';
			}
			else if (($now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate) && $row->published == 1)
			{
				$img = 'tick.png';
			}
			else if ($now->toUnix() > $publish_down->toUnix() && $row->published == 1)
			{
				$img = 'publish_r.png';
			}
			return JHTML::_('grid.published', $row, $i, $img);
		}
	}

	public static function featured(&$row, $i)
	{
		$mainframe = JFactory::getApplication();

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$states = array(
				1 => array(
					'featured',
					'FPSS_FEATURED',
					'FPSS_REMOVE_FEATURED_FLAG',
					'FPSS_FEATURED',
					false,
					'publish',
					'publish'
				),
				0 => array(
					'featured',
					'FPSS_NOT_FEATURED',
					'FPSS_FLAG_AS_FEATURED',
					'FPSS_NOT_FEATURED',
					false,
					'unpublish',
					'unpublish'
				),
			);
			$html = JHtml::_('jgrid.state', $states, $row->featured, $i, '');
		}
		else
		{
			$iconsPath = JURI::base(true).'/images/';
			$icon = $row->featured ? 'tick.png' : 'publish_x.png';
			$alt = $row->featured ? JText::_('FPSS_FEATURED') : JText::_('FPSS_NOT_FEATURED');
			$action = $row->featured ? JText::_('FPSS_REMOVE_FEATURED_FLAG') : JText::_('FPSS_FLAG_AS_FEATURED');
			$html = '
        	<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\'featured\')" title="'.$action.'">
        	<img src="'.$iconsPath.$icon.'" border="0" alt="'.$alt.'" /></a>';
		}
		return $html;
	}

	public static function getCategoryFilter($name, $active = NULL)
	{
		jimport('joomla.application.component.model');
		FPSSModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'.DS.'models');
		$model = FPSSModel::getInstance('categories', 'FPSSModel');
		$model->setState('published', -1);
		$model->setState('ordering', 'category.name');
		$model->setState('orderingDir', 'ASC');
		$categories = $model->getData();
		$option = new JObject();
		$option->id = 0;
		$option->name = JText::_('FPSS_SELECT_CATEGORY');
		array_unshift($categories, $option);
		return JHTML::_('select.genericlist', $categories, $name, '', 'id', 'name', $active);
	}

	public static function getAuthorFilter($name, $active)
	{
		$db = JFactory::getDBO();
		$query = "SELECT id AS value, name AS text FROM #__users WHERE block = 0 AND id IN(SELECT DISTINCT created_by FROM #__fpss_slides) ORDER BY name";
		$db->setQuery($query);
		$users[] = JHTML::_('select.option', '0', JText::_('FPSS_SELECT_AUTHOR'));
		$users = array_merge($users, $db->loadObjectList());
		$filter = JHTML::_('select.genericlist', $users, $name, 'class="inputbox" size="1" ', 'value', 'text', $active);
		return $filter;
	}

	public static function getJSON($array = array())
	{

		if (function_exists('json_encode'))
		{
			return json_encode($array);
		}

		$object = '{';
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}
			if (!is_array($v) && !is_object($v))
			{
				$object .= ' "'.$k.'": ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : '"'.$v.'"';
				$object .= ',';
			}
			else
			{
				$object .= ' '.$k.': '.FPSSModelSlide::getJSON($v).',';
			}
		}
		if (substr($object, -1) == ',')
		{
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}

}
