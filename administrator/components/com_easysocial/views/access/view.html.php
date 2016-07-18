<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/views/views');

class EasySocialViewAccess extends EasySocialAdminView
{
	/**
	 * Default access rules listing page.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  string    $tpl The template to load
	 */
	public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_ACCESS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_ACCESS');

		// Add Joomla buttons here
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();

		$model = FD::model('accessrules' , array('initState' => true));

		// Selected filters
		$state = $model->getState('published');
		$extension 	= $model->getState('filter');
		$group = $model->getState('group');
		$limit = $model->getState('limit');
		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$search = $model->getState('search');

		$access = $model->getItems();

		// Load a list of groups so that users can filter rules by groups
		$groups = $model->getGroups();

		// Load a list of extensions so that users can filter them.
		$extensions	= $model->getExtensions();

		// Get pagination
		$pagination = $model->getPagination();

		$this->set('group', $group);
		$this->set('groups', $groups);
		$this->set('access', $access);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('limit', $limit);
		$this->set('selectedExtension', $extension);
		$this->set('search', $search);
		$this->set('pagination', $pagination);
		$this->set('extensions', $extensions);
		$this->set('extension', $extension);
		$this->set('state', $state);

		echo parent::display('admin/access/default');
	}

	public function discover($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_DISCOVER_ACCESS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_ACCESS');

		JToolbarHelper::custom('discover', 'download', '', JText::_('COM_EASYSOCIAL_DISCOVER_BUTTON'), false);

		echo parent::display('admin/access/discover');
	}

	public function install($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_INSTALL_ACCESS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_ACCESS');

		echo parent::display('admin/access/install');
	}

	public function upload()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=access&layout=install');
	}

	public function publish()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=access');
	}

	public function remove()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=access');
	}
}
