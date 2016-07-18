<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewFields extends EasyBlogAdminView
{
	/**
	 * Displays available custom fields.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.fields');

		$layout 	= $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_TITLE', '', 'fa-list-alt');

		$filter_groups	= $this->app->getUserStateFromRequest( 'com_easyblog.fields.filter_groups', 'filter_groups', '*', 'string' );
		$search			= $this->app->getUserStateFromRequest( 'com_easyblog.fields.search', 'search', '', 'string' );

		$search			= trim(JString::strtolower( $search ) );

		JToolBarHelper::title(JText::_('COM_EASYBLOG_FIELDS_TITLE'), 'feeds');
		JToolbarHelper::addNew('fields.add');
		JToolbarHelper::divider();
		JToolbarHelper::publishList('fields.publish');
		JToolbarHelper::unpublishList('fields.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_FIELDS_DELETE_CONFIRMATION'), 'fields.delete');

		// Get the list of custom fields on the site
		$model 	= EB::model('Fields');
		$rows 	= $model->getItems();
		$fields = array();


		if ($rows) {
			foreach ($rows as $row) {
				$field = EB::table('Field');
				$field->bind($row);

				$fields[]	= $field;
			}
		}

		$filterGroups = $this->getFilterGroups($filter_groups);

		// $search = $this->input->get('search', '', 'string');

		$pagination = $model->getPagination();

		$this->set('search', $search);
		$this->set('filterGroups', $filterGroups);
		$this->set('pagination', $pagination);
		$this->set('fields', $fields);


		parent::display('fields/default');
	}

	/**
	 * Displays available custom field groups
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groups($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');

		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_GROUPS_TITLE', '', 'fa-list-alt');

		JToolBarHelper::title(JText::_('COM_EASYBLOG_FIELDS_GROUPS_TITLE'), 'feeds');

		$search			= $this->app->getUserStateFromRequest('com_easyblog.fields.search', 'search', '', 'string');

		$search			= trim(JString::strtolower($search));

		// Get custom field groups
		$model 	= EB::model('FieldGroups');
		$result	= $model->getItems(true);
		$pagination = $model->getPagination();

		// Bind the result with the table
		$groups = array();

		if ($result) {
			foreach ($result as $row) {

				$group 	= EB::table('FieldGroup');
				$group->bind($row);

				$groups[]	= $group;
			}
		}

		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('groups', $groups);

		JToolbarHelper::addNew('fields.addGroup');
		JToolbarHelper::divider();
		JToolbarHelper::publishList('fields.publishGroup');
		JToolbarHelper::unpublishList('fields.unpublishGroup');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE_GROUPS'), 'fields.removeGroup');

		parent::display('fields/groups');
	}

	/**
	 * Displays the new group form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');

		$id = $this->input->get('id', 0, 'int');

		// Get the field table
		$field = EB::table('Field');
		$field->load($id);

		if (!$id) {
			$title 	= 'COM_EASYBLOG_FIELDS_CREATE_TITLE';
		} else {
			$title 	= 'COM_EASYBLOG_FIELDS_EDIT_TITLE';
		}

		// Set page details
		$this->setHeading($title, '', 'fa-list-alt');

		JToolBarHelper::title(JText::_($title), 'fields');
		JToolbarHelper::apply('fields.apply');
		JToolbarHelper::save('fields.save');
		JToolbarHelper::save2new('fields.savenew');
		JToolBarHelper::cancel('fields.cancel');

		// Get the fieldsgroup model
		$model = EB::model('Fields');
		$groups = $model->getGroups();

		if (!$groups) {
			return parent::display('fields/form.nogroups');
		}

		// Get the custom fields library
		$fields = EB::fields()->getItems();

		// Get the current active field form
		$form = '';

		if ($field->id) {
			$form = EB::fields()->get($field->type)->admin($field);
		}

		$this->set('fields', $fields);
		$this->set('form', $form);
		$this->set('field', $field);
		$this->set('groups', $groups);


		parent::display('fields/form');
	}

	/**
	 * Displays the new group form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupForm($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');
		
		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_GROUPS_TITLE', '', 'fa-list-alt');

		JToolBarHelper::title(JText::_('COM_EASYBLOG_FIELDS_GROUPS_TITLE'), 'feeds');


		JToolbarHelper::apply('fields.applyGroup');
		JToolbarHelper::save('fields.saveGroup');
		JToolbarHelper::save2new('fields.saveNewGroup');
		JToolBarHelper::cancel('fields.cancelGroup');

		// Get the group id
		$id = $this->input->get('id', 0, 'int');

		$group = EB::table('FieldGroup');
		$group->load($id);

		$this->set('group', $group);

		parent::display('fields/groups.form');
	}

	public function getFilterGroups($filter_type = '*')
	{
		$model 	= EB::model('FieldGroups');
		$fieldGroups = $model->getItems();
		$filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_FIELDS_FILTER_BY_GROUP'));

		foreach ($fieldGroups as $fieldGroup) {
			$filter[] = JHTML::_('select.option', $fieldGroup->id, $fieldGroup->title);
		}

		return JHTML::_('select.genericlist', $filter, 'filter_groups', 'class="form-control" onchange="submitform( );"', 'value', 'text', $filter_type );
	}
}
