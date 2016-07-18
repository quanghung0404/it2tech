<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewCategories extends EasyBlogAdminView
{
	/**
	 * Displays the category listings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.category');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set the heading
		$this->setHeading('COM_EASYBLOG_TITLE_CATEGORIES');

		$filter_state = $this->app->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 		'filter_state', 	'*', 'word' );

		$search = $this->app->getUserStateFromRequest('com_easyblog.categories.search', 'search', '', 'string');
		$search = trim(JString::strtolower($search));

		$order = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order', 'filter_order', 'lft', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Should we display only published categories
		$publishedOnly = $this->input->get('p', 0, 'int');

		$category = EB::table('Category');
		$category->rebuildOrdering();

		// Get data from the model
		$ordering = array();
		$model = EB::model('Category');

		// Get the list of categories
		$result = $model->getData(true, $publishedOnly);
		$categories = array();

		if ($result) {
			foreach ($result as $row) {

				$category = EB::table('Category');
				$category->bind($row);

				$category->depth = $row->depth;
				$category->count = $category->getCount();
				$category->child_count = $model->getChildCount($row->id);

				$ordering[$row->parent_id][]	= $category->id;

				$categories[] = $category;
			}
		}

		// Get the pagination
		$pagination = $model->getPagination();

		// Build the states filter
		$filterState = JHTML::_('grid.state', $filter_state);

		// Retrieve items from query
		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertCategory', 'cmd');

		// Save ordering
		$saveOrder = $order == 'lft' && $orderDirection == 'asc';

		$this->set('browse', $browse);
		$this->set('browsefunction', $browsefunction);
		$this->set('categories', $categories);
		$this->set('pagination', $pagination);
		$this->set('state', $filterState);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('saveOrder', $saveOrder);
		$this->set('ordering', $ordering);
		$this->set('orderDirection', $orderDirection);

		parent::display('categories/default');
	}

	/**
	 * Displays the category form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		// Get the category id.
		$id = $this->input->get('id', 0, 'int');

		$category = EB::table('Category');
		$category->load($id);

		// Set the page heading
		$this->setHeading('COM_EASYBLOG_TITLE_CREATE_CATEGORY', '');

		if ($category->id) {
			$this->setHeading('COM_EASYBLOG_TITLE_EDIT_CATEGORY', '');
		}

		// If this is a new category, initialize the default property for the category
		if (!$category->created) {

			$date = EB::date();

			$category->created = $date->toSql();
			$category->published = true;
			$category->autopost = true;
		}

		// Get the rules for the category
		$acl = EB::table('CategoryAclItem');
		$rules = $acl->getAllRuleItems();

		// Get assigned acl
		$groups = $category->getAssignedACL();

		// Get the current site template
		$template = $this->getCurrentTemplate();

		// Get a list of custom template folders
		$themes = $this->getCustomThemes($template);

		// Get a list of custom field groups available
		$fieldModel	= EB::model('Fields');
		$fieldGroups = $fieldModel->getGroups();

		// Get a list of parents
		$parentList = EB::populateCategories('', '', 'select', 'parent_id', $category->parent_id , false , false , false , array($category->id) );

		// Get editor
		$editor = JFactory::getEditor();

		foreach ($rules as &$rule) {
            $rule->title = JText::_('COM_EASYBLOG_CATEGORIES_ACL_' . $rule->action . '_TITLE');
            $rule->desc = JText::_('COM_EASYBLOG_CATEGORIES_ACL_' . $rule->action . '_DESC');
		}

		// Get the category params
		$params = $category->getParams();

		// var_dump($params);

		// Get the param forms from the view manifest file
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/entry/tmpl/default.xml';

		$form = EB::form()->render($manifest, $params, false);

		$this->set('parentList', $parentList);
		$this->set('params', $params);
		$this->set('form', $form);
		$this->set('fieldGroups', $fieldGroups);
		$this->set('template', $template);
		$this->set('themes', $themes);
		$this->set('editor', $editor);
		$this->set('groups', $groups);
		$this->set('category', $category);
		$this->set('rules', $rules);

		parent::display('categories/form');
	}


	public function getCurrentTemplate()
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		$query .= ' WHERE ' . $db->nameQuote('home') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();

		return $template;
	}

	public function getCustomThemes($template)
	{
		$path 		= JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/themes';

		if (!JFolder::exists($path))	{
			return false;
		}

		$folders 	= JFolder::folders($path);

		return $folders;
	}

	/**
	 * Registers the toolbar
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{
		$layout = $this->getLayout();

		JToolBarHelper::title(JText::_('COM_EASYBLOG_CATEGORIES_TITLE' ), 'category');

		if ($layout == 'form') {
			JToolBarHelper::apply('category.apply');
			JToolbarHelper::save('category.save');
			JToolbarHelper::save2new('category.savenew');
			JToolBarHelper::cancel('category.cancel');

			return;
		}

		JToolbarHelper::addNew('category.create');
		JToolBarHelper::divider();
		JToolbarHelper::publishList('category.publish');
		JToolbarHelper::unpublishList('category.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', 'category.remove');

	}
}
