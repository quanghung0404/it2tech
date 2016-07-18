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

class FPSSViewSlides extends FPSSView
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
		$ordering = $mainframe->getUserStateFromRequest("{$option}.{$view}.ordering", 'filter_order', 'slide.id', 'cmd');
		$orderingDir = $mainframe->getUserStateFromRequest("{$option}.{$view}.orderingDir", 'filter_order_Dir', 'DESC', 'word');
		$published = $mainframe->getUserStateFromRequest("{$option}.{$view}.published", 'published', -1, 'int');
		$featured = $mainframe->getUserStateFromRequest("{$option}.{$view}.featured", 'featured', -1, 'int');
		$catid = $mainframe->getUserStateFromRequest("{$option}.{$view}.catid", 'catid', 0, 'int');
		$author = $mainframe->getUserStateFromRequest("{$option}.{$view}.author", 'author', 0, 'int');
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
		$model->setState('catid', $catid);
		$model->setState('access', -1);
		$model->setState('featured', $featured);
		$model->setState('author', $author);
		$model->setState('categoryPublished', -1);
		$model->setState('language', $language);
		$model->setState('search', $search);

		$slides = $model->getData();
		$slideModel = FPSSModel::getInstance('slide', 'FPSSModel');
		foreach ($slides as $slide)
		{
			$slideModel->getSlideImages($slide);
			JFilterOutput::objectHTMLSafe($slide);
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$dateFormat = JText::_('FPSS_J16_DATE_FORMAT');
			}
			else
			{
				$dateFormat = JText::_('FPSS_DATE_FORMAT');
			}
			$slide->created = JHTML::_('date', $slide->created, $dateFormat);
			if ((int)$slide->modified)
			{
				$slide->modified = JHTML::_('date', $slide->modified, $dateFormat);
			}
			else
			{
				$slide->modified = JText::_('FPSS_NEVER');
			}

			switch($slide->referenceType)
			{
				default :
				case 'custom' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_CUSTOM_URL');
					break;
				case 'com_content' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_JOOMLA_ARTICLE');
					break;
				case 'com_menus' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_JOOMLA_MENU_ITEM');
					break;
				case 'com_k2' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_K2_ITEM');
					break;
				case 'com_virtuemart' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_VIRTUEMART_PRODUCT');
					break;
				case 'com_redshop' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_REDSHOP_PRODUCT');
					break;
				case 'com_tienda' :
					$slide->reference = JText::_('FPSS_COM_SOURCE_TIENDA_PRODUCT');
					break;
			}
			
			if(version_compare(JVERSION, '3.0', 'ge'))
            {
                $slide->canChange = $user->authorise('core.edit.state', 'com_fpss.slide.'.$slide->id);
            }

		}
		$this->assignRef('rows', $slides);

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
		$options = array();
		$options[] = JHTML::_('select.option', -1, JText::_('FPSS_SELECT_FEATURED_STATE'));
		$options[] = JHTML::_('select.option', 1, JText::_('FPSS_FEATURED'));
		$options[] = JHTML::_('select.option', 0, JText::_('FPSS_NOT_FEATURED'));
		$filters['featured'] = JHTML::_('select.genericlist', $options, 'featured', '', 'value', 'text', $featured);
		$this->loadHelper('html');
		$filters['category'] = FPSSHelperHTML::getCategoryFilter('catid', $catid);
		$filters['author'] = FPSSHelperHTML::getAuthorFilter('author', $author);
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$languages = JHTML::_('contentlanguage.existing', true, true);
			array_unshift($languages, JHTML::_('select.option', '', JText::_('FPSS_SELECT_LANGUAGE')));
			$filters['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $language);
		}
		$this->assignRef('filters', $filters);

		if ($ordering == 'slide.ordering' && $catid)
		{
			$orderingFlag = true;
		}
		else
		{
			$orderingFlag = false;
		}
		$this->assignRef('orderingFlag', $orderingFlag);

		if ($ordering == 'slide.featured_ordering' && $featured == 1)
		{
			$featuredOrderingFlag = true;
		}
		else
		{
			$featuredOrderingFlag = false;
		}
		$this->assignRef('featuredOrderingFlag', $featuredOrderingFlag);

		$template = $mainframe->getTemplate();
		$this->assignRef('template', $template);

		$document = JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_fpss/js/slimbox2.js');
		$title = JText::_('FPSS_SLIDES');
		$this->assignRef('title', $title);

		FPSSHelperHTML::title($title);
		FPSSHelperHTML::toolbar();
		FPSSHelperHTML::subMenu();
		
		// Joomla! 3.0 drag-n-drop sorting
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if ($orderingFlag)
			{
				JHtml::_('sortablelist.sortable', 'fpssSlidesList', 'adminForm', strtolower($this->filters['orderingDir']), 'index.php?option=com_fpss&view=categories&task=saveorder&format=raw');
			}
			if($featuredOrderingFlag)
			{
				JHtml::_('sortablelist.sortable', 'fpssSlidesList', 'adminForm', strtolower($this->filters['orderingDir']), 'index.php?option=com_fpss&view=categories&task=featuredsaveorder&format=raw');
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
