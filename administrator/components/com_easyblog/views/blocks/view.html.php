<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewBlocks extends EasyBlogAdminView
{
	/**
	 * Displays the blocks listings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.blocks');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Get the filters
		$search = $this->input->get('search', '', 'word');
		$filterGroup = $this->input->get('filter_group', '', 'word');
		$filterState = $this->input->get('filter_state', 'all', 'default');
		$filterState = $filterState == 'all' ? 'all' : (int) $filterState;

		$options = array('filter_state' => $filterState, 'search' => $search, 'filter_group' => $filterGroup);

		// Set the heading
		$this->setHeading('COM_EASYBLOG_TITLE_BLOCKS', '', 'fa-cubes');

		$model = EB::model('Blocks');
		$blocks = $model->getBlocks($options);
		$groups = $model->getGroups();
		$pagination = $model->getPagination($options);

		$this->set('filterState', $filterState);
		$this->set('filterGroup', $filterGroup);
		$this->set('groups', $groups);
		$this->set('pagination', $pagination);
		$this->set('blocks', $blocks);
		$this->set('search', $search);

		parent::display('blocks/default');
	}

	public function registerToolbar()
	{
		$layout	= JRequest::getVar('layout');

		JToolBarHelper::title(JText::_('COM_EASYBLOG_BLOCKS_TITLE'), 'blocks');

		JToolbarHelper::publishList('blocks.publish');
		JToolbarHelper::unpublishList('blocks.unpublish');

	}
}
