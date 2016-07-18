<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include main views file.
FD::import('admin:/views/views');

class EasySocialViewMaintenance extends EasySocialAdminView
{
    public function display($tpl = null)
    {
        $this->setHeading('COM_EASYSOCIAL_HEADING_MAINTENANCE');
        $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MAINTENANCE');

        // Add button
        JToolbarHelper::custom('form', 'refresh', '', JText::_('COM_EASYSOCIAL_MAINTENANCE_EXECUTE_SCRIPTS'));

        // Check if this is from after execution
        $success = JRequest::getInt('success');

        if (!empty($success)) {
            $plurality = $success > 1 ? '_PLURAL' : '_SINGULAR';

            FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT' . $plurality, $success), SOCIAL_MSG_SUCCESS);

            return $this->redirect('index.php?option=com_easysocial&view=maintenance');
        }

        $model = FD::model('maintenance', array('initState' => true));

        $version = $model->getState('version');
        $search = $model->getState('search');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');
        $limit = $model->getState('limit');

        $scripts = $model->getItems();

        $pagination = $model->getPagination();

        $versions = $model->getVersions();

        $this->set('scripts', $scripts);
        $this->set('version', $version);
        $this->set('search', $search);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('limit', $limit);
        $this->set('pagination', $pagination);
        $this->set('versions', $versions);

        echo parent::display('admin/maintenance/default');
    }

    public function form($keys = null)
    {
        // Check access
        if (!$this->authorise('easysocial.access.maintenance')) {
            $this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
        }

        if ($this->hasErrors() || empty($keys)) {
            FD::info()->set($this->getMessage());

            return $this->redirect('index.php?option=com_easysocial&view=maintenance');
        }

        $this->setHeading('COM_EASYSOCIAL_HEADING_MAINTENANCE_EXECUTING');
        $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MAINTENANCE_EXECUTING');

        $model = FD::model('maintenance');

        $scripts = $model->getItemByKeys($keys);

        $this->set('scripts', $scripts);

        echo parent::display('admin/maintenance/form');
    }

    public function database($tpl = null)
    {
        // Check access
        if (!$this->authorise('easysocial.access.maintenance')) {
            $this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
        }

        $this->setHeading('COM_EASYSOCIAL_HEADING_MAINTENANCE_DATABASE');
        $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MAINTENANCE_DATABASE');

        $model = FD::model('maintenance');

        echo parent::display('admin/maintenance/database');
    }
}
