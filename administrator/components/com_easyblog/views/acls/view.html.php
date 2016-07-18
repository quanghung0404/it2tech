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

class EasyBlogViewAcls extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.acl');

		// Load layout if exists
		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		$this->setHeading('COM_EASYBLOG_TITLE_ACL', '', 'fa-lock');

		// Filtering
		$filter = new stdClass();
		$filter->search = $this->app->getUserStateFromRequest( 'com_easyblog.acls.search', 'search', '', 'string' );

		// Sorting
		$sort = new stdClass();
		$sort->order = $this->app->getUserStateFromRequest( 'com_easyblog.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$sort->orderDirection = $this->app->getUserStateFromRequest( 'com_easyblog.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$model = EB::model('Acls');
		$rulesets = $model->getRuleSets();
		$pagination = $model->getPagination();


		$this->doc->setTitle(JText::_("COM_EASYBLOG_ACL_JOOMLA_USER_GROUP"));
		JToolBarHelper::title(JText::_('COM_EASYBLOG_ACL_JOOMLA_USER_GROUP'), 'acl');

		$this->set('rulesets', $rulesets);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);

		parent::display('acls/default');
	}

	/**
	 * Displays the ACL form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		$model 	= EB::model('Acl');

		$id 	= JRequest::getInt('id');
		$add 	= JRequest::getVar('add');

		if (empty($id) && empty($add)) {
			EB::info()->set(JText::_('COM_EASYBLOG_ACL_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=acls');
		}

		// Get a list of rule sets.
		$ruleset 	= $model->getInstalledRules($id, $add);
		$groups		= $model->getGroups();

		$this->setHeading('COM_EASYBLOG_TITLE_ACL', '', 'fa-lock');

		$this->doc->setTitle(JText::_("COM_EASYBLOG_ACL_JOOMLA_USER_GROUP"));
		JToolBarHelper::title(JText::_('COM_EASYBLOG_ACL_JOOMLA_USER_GROUP'), 'acl');


		$filter 	= EB::table('ACLFilter');
		$filter->load($id);

		$this->set('groups', $groups);
		$this->set('filter', $filter);
		$this->set('ruleset', $ruleset);

		$this->set('add', $add);

		parent::display('acls/form');
	}

	public function registerToolbar()
	{
		$layout = $this->getLayout();

		if ($layout == 'form') {
			JToolBarHelper::apply('acl.apply');
			JToolBarHelper::save('acl.save');
			JToolBarHelper::divider();
			JToolBarHelper::custom('acl.enable', 'plus', '', JText::_( 'COM_EASYBLOG_ENABLE_ALL' ), false );
			JToolBarHelper::custom('acl.disable', 'minus', '', JText::_( 'COM_EASYBLOG_DISABLE_ALL' ), false );
			JToolBarHelper::divider();
			JToolBarHelper::cancel();

			return;
		}
	}

}
