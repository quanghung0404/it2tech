<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewTypes extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.posttypes');

		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$model = ED::model('PostTypes', true);

		// Get a list of post types
		$types = $model->getTypes();
		$pagination = $model->getPagination();

		$state = $this->getUserState('types.filter_state', 'filter_state', '*', 'word');

		// Ordering
		$order = $this->getUserState('types.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('types.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$browse = $this->input->get('browse', 0,' int');
		$browseFunction = $this->input->get('browseFunction', '');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_POST_TYPES_TITLE');
		$this->desc('COM_EASYDISCUSS_POST_TYPES_TITLE_DESC');

		// Search query
		$search = $this->getUserState('types.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		$this->set('browseFunction', $browseFunction);
		$this->set('browse', $browse);
		$this->set('search', $search);
		$this->set('types', $types);
		$this->set('state', $state);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('types/default');
	}

	public function form()
	{
		$this->checkAccess('discuss.manage.posttypes');

		$id = $this->input->get('id', 0, 'int');
		$postTypes = ED::table('Post_types');

		$this->title('COM_EASYDISCUSS_ADD_POST_TYPES_TITLE');
		$this->desc('COM_EASYDISCUSS_ADD_POST_TYPES_TITLE_DESC');

		if($id) {
			$postTypes->load($id);
			$this->title('COM_EASYDISCUSS_EDIT_POST_TYPES_TITLE');
			$this->desc('COM_EASYDISCUSS_EDIT_POST_TYPES_TITLE_DESC');
		}

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolbarHelper::save2new();
		JToolBarHelper::cancel();

		$this->set('postTypes', $postTypes);

		// This will go to form.php
		parent::display('types/form');
	}
}
