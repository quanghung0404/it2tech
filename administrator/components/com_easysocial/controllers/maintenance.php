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

FD::import('admin:/controllers/controller');

class EasySocialControllerMaintenance extends EasySocialController
{
    public function form()
    {
        // Check for request forgeries
        FD::checkToken();

        $ids = JRequest::getVar('cid');
        $ids = FD::makeArray($ids);

        if (empty($ids)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_MAINTENANCE_NO_SCRIPT_SELECTED'));
            return $this->view->call(__FUNCTION__);
        }

        return $this->view->call(__FUNCTION__, $ids);
    }

    public function runscript()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the key
        $key = $this->input->get('key', '', 'default');

        // Get the model
        $model = FD::model('Maintenance');
        $script = $model->getItemByKey($key);

        if (!$script) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_MAINTENANCE_SCRIPT_NOT_FOUND'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $classname = $script->classname;

        if (!class_exists($classname)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_MAINTENANCE_CLASS_NOT_FOUND'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $class = new $classname;

        try {
            $class->main();
        } catch (Exception $e) {
            $this->view->setMessage($e->getMessage(), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        return $this->view->call(__FUNCTION__);
    }

    public function getDatabaseStats()
    {
        $path = SOCIAL_ADMIN . '/updates';

        jimport('joomla.filesystem.file');

        $files = JFolder::files($path, '.json$', true, true);

        // 1.0.0 is a flag to execute table creation in synchronizeDatabase
        $versions = array('1.0.0');

        foreach ($files as $file) {
            $segments = explode('/', $file);

            $version = $segments[count($segments) - 2];

            if (!in_array($version, $versions) && version_compare($version, '1.0.0') > 0) {
                $versions[] = $version;
            }
        }

        return $this->view->call(__FUNCTION__, $versions);
    }

    public function synchronizeDatabase()
    {
        $version = $this->input->getString('version');

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        // Explicitly check for 1.0.0 since it is a flag to execute table creation
        if ($version === '1.0.0') {
            $path = SOCIAL_ADMIN . '/queries';

            if (!JFolder::exists($path)) {
                return $this->view->call(__FUNCTION__);
            }

            $files = JFolder::files($path, '.sql$', true, true);

            $result = array();

            $db = FD::db();

            foreach ($files as $file) {
                $contents = JFile::read($file);

                $queries = JInstallerHelper::splitSql($contents);

                foreach ($queries as $query) {
                    $query = trim($query);

                    if (!empty($query)) {
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }

            return $this->view->call(__FUNCTION__);
        }

        $path = SOCIAL_ADMIN . '/updates/' . $version;

        $files = JFolder::files($path, '.json$', true, true);

        $result = array();

        foreach ($files as $file) {
            $result = array_merge($result, FD::makeObject($file));
        }

        $tables = array();
        $indexes = array();
        $changes = array();
        $affected = 0;

        $db = FD::db();

        foreach ($result as $row) {
            $columnExist = true;
            $indexExist = true;
            $alterTable = false;

            if (isset($row->column)) {
                // Store the list of tables that needs to be queried
                if (!isset($tables[$row->table])) {
                    $tables[$row->table] = $db->getTableColumns($row->table);
                }

                // Check if the column is in the fields or not
                $columnExist = in_array($row->column , $tables[$row->table]);
            }

            if (isset($row->alter)) {
                $alterTable = true;
            }

            if (isset($row->index)) {
                if (!isset($indexes[$row->table])) {
                    $indexes[$row->table] = $db->getTableIndexes($row->table);
                }

                $indexExist = in_array($row->index, $indexes[$row->table]);
            }

            if ($alterTable || !$columnExist || !$indexExist) {
                $sql = $db->sql();
                $sql->raw($row->query);

                $db->setQuery($sql);
                $db->query();

                $affected += 1;
            }
        }

        return $this->view->call(__FUNCTION__);
    }
}
