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
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerLanguages extends EasyBlogController
{
    /**
     * Purges the cache of language items
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function purge()
    {
        // Check for request forgeries here
        EB::checkToken();

        // Get the model
        $model  = EB::model('Languages');
        $model->purge();

        EB::info()->set(JText::_('COM_EASYBLOG_LANGUAGE_PURGED_SUCCESSFULLY'), 'success');

        $this->app->redirect('index.php?option=com_easyblog&view=languages');
    }

    /**
     * Discovery of language files
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function discover()
    {
        $model = EB::model('Languages');
        $result = $model->discover();

        $this->info->set(JText::_('COM_EASYBLOG_LANGUAGE_DISCOVERED_SUCCESSFULLY'), 'success');

        return $this->app->redirect('index.php?option=com_easyblog&view=languages');
    }

    /**
     * Install language file on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function install()
    {
        // Check for request forgeries here
        EB::checkToken();

        // Get the language id
        $ids = $this->input->get('cid', array(), 'array');

        foreach ($ids as $id) {
            $table  = EB::table('Language');
            $table->load($id);

            $state = $table->install();

            if (!$state) {
                EB::info()->set($table->getError(), 'error');
                return $this->app->redirect('index.php?option=com_easyblog&view=languages');
            }
        }

        EB::info()->set(JText::_('COM_EASYBLOG_LANGUAGE_INSTALLED_SUCCESSFULLY'), 'success');

        $this->app->redirect('index.php?option=com_easyblog&view=languages');
    }
}
