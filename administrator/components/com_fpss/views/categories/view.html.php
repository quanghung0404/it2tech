<?php
/**
 * @version		$Id: view.html.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSViewCategories extends FPSSView
{

	function display($tpl = null)
	{
		JHTML::_('behavior.tooltip');
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$limit = $mainframe->getUserStateFromRequest("{$option}.{$view}.limit", 'limit', 20, 'int');
		$limitstart = $mainframe->getUserStateFromRequest("{$option}.{$view}.limitstart", 'limitstart', 0, 'int');
		$ordering = $mainframe->getUserStateFromRequest("{$option}.{$view}.ordering", 'filter_order', 'category.id', 'cmd');
		$orderingDir = $mainframe->getUserStateFromRequest("{$option}.{$view}.orderingDir", 'filter_order_Dir', 'DESC', 'word');
		$published = $mainframe->getUserStateFromRequest("{$option}.{$view}.published", 'published', -1, 'int');
		$language = $mainframe->getUserStateFromRequest("{$option}.{$view}.language", 'language', '', 'string');
		$search = $mainframe->getUserStateFromRequest("{$option}.{$view}.search", 'search', '', 'string');
		$search = JString::strtolower($search);

		$params = JComponentHelper::getParams('com_fpss');
		$this->assignRef('params', $params);

		$model = $this->getModel();
		$model->setState('limit', $limit);
		$model->setState('limitstart', $limitstart);
		$model->setState('ordering', $ordering);
		$model->setState('orderingDir', $orderingDir);
		$model->setState('published', $published);
		$model->setState('search', $search);
		$model->setState('language', $language);
		$categories = $model->getData();
		foreach ($categories as $category)
		{
			JFilterOutput::objectHTMLSafe($category);
			if(version_compare(JVERSION, '3.0', 'ge'))
            {
                $category->canChange = $user->authorise('core.edit.state', 'com_fpss.category.'.$category->id);
            }
		}
		$this->assignRef('rows', $categories);

		$total = $model->getTotal();
		$pagination = new JPagination($total, $limitstart, $limit);
		$this->assignRef('pagination', $pagination);

		$filters = array();
		$filters['search'] = $search;
		$filters['ordering'] = $ordering;
		$filters['orderingDir'] = $orderingDir;
		$options = array();
		$options[] = JHTML::_('select.option', -1, JText::_('FPSS_SELECT_PUBLISHING_STATE'));
		$options[] = JHTML::_('select.option', 1, JText::_('FPSS_PUBLISHED'));
		$options[] = JHTML::_('select.option', 0, JText::_('FPSS_UNPUBLISHED'));
		$filters['published'] = JHTML::_('select.genericlist', $options, 'published', '', 'value', 'text', $published);
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$languages = JHTML::_('contentlanguage.existing', true, true);
			array_unshift($languages, JHTML::_('select.option', '', JText::_('FPSS_SELECT_LANGUAGE')));
			$filters['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $language);
		}
		$this->assignRef('filters', $filters);

		if ($ordering == 'category.ordering')
		{
			$orderingFlag = true;
		}
		else
		{
			$orderingFlag = false;
		}
		$this->assignRef('orderingFlag', $orderingFlag);
		$title = JText::_('FPSS_CATEGORIES');
		$this->assignRef('title', $title);

		$this->loadHelper('html');
		FPSSHelperHTML::title($title);
		FPSSHelperHTML::toolbar();
		FPSSHelperHTML::subMenu();

		// Joomla! 3.0 drag-n-drop sorting
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if ($orderingFlag)
			{
				JHtml::_('sortablelist.sortable', 'fpssCategoriesList', 'adminForm', strtolower($this->filters['orderingDir']), 'index.php?option=com_fpss&view=categories&task=saveorder&format=raw');
			}
			$document = JFactory::getDocument();
			$document->addScriptDeclaration('
            Joomla.orderTable = function() {
                table = document.getElementById("sortTable");
                direction = document.getElementById("directionTable");
                order = table.options[table.selectedIndex].value;
                if (order != \''.$this->filters['ordering'].'\') {
                    dirn = \'asc\';
            } else {
                dirn = direction.options[direction.selectedIndex].value;
            }
            Joomla.tableOrdering(order, dirn, "");
            }');
		}
		parent::display($tpl);

	}

}
