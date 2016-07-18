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

class EasySocialControllerStream extends EasySocialController
{
    /**
     * Class Constructor.
     *
     * @since   1.0
     * @access  public
     */
    public function __construct()
    {
        parent::__construct();

        // Register task aliases here.
        $this->registerTask( 'archive' , 'archive' );
        $this->registerTask( 'trash' , 'trash' );
        $this->registerTask( 'restoreTrash' , 'restoreTrash' );
    }

    public function archive()
    {
        // Check for request forgeries
        FD::checkToken();
        $ids = $this->input->get('cid', '', 'var');
        if ($ids) {
            $model = FD::model('Stream');
            $state = $model->archive($ids);

            if (!$state) {
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_ARCHIVE_STREAM_FAILED'), SOCIAL_MSG_ERROR);
                return $this->view->call(__FUNCTION__);
            }
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_ARCHIVE_STREAM_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    public function purge()
    {
        // Check for request forgeries
        FD::checkToken();

        $ids = $this->input->get('cid', '', 'var');

        if ($ids) {
            foreach ($ids as $id) {
                $model = FD::model('Stream');
                $state = $model->deleteStreamItem($id);

                if (!$state) {
                    $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_DELETE_STREAM_FAILED'), SOCIAL_MSG_ERROR);
                    return $this->view->call(__FUNCTION__);
                }
            }
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_DELETE_STREAM_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    public function restoreTrash()
    {
        // Check for request forgeries
        FD::checkToken();
        $ids = $this->input->get('cid', '', 'var');
        if ($ids) {
            $model = FD::model('Stream');
            $state = $model->restoreStreamItem($ids);

            if (!$state) {
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_RESTORE_STREAM_FAILED'), SOCIAL_MSG_ERROR);
                return $this->view->call(__FUNCTION__);
            }
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_RESTORE_STREAM_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    public function trash()
    {
        // Check for request forgeries
        FD::checkToken();
        $ids = $this->input->get('cid', '', 'var');
        if ($ids) {
            $model = FD::model('Stream');
            $state = $model->trashStreamItem($ids);

            if (!$state) {
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_TRASH_STREAM_FAILED'), SOCIAL_MSG_ERROR);
                return $this->view->call(__FUNCTION__);
            }
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_TRASH_STREAM_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    public function restore()
    {
        // Check for request forgeries
        FD::checkToken();
        $ids = $this->input->get('cid', '', 'var');
        if ($ids) {
            $model = FD::model('Stream');
            $state = $model->restoreArchivedItem($ids);

            if (!$state) {
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_RESTORE_STREAM_FAILED'), SOCIAL_MSG_ERROR);
                return $this->view->call(__FUNCTION__);
            }
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_RESTORE_STREAM_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }
}
