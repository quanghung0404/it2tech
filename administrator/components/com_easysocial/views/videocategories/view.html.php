<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/views/views');

class EasySocialViewVideoCategories extends EasySocialAdminView
{
	/**
	 * Main method to display the video categories
	 *
	 * @since	1.4
	 * @access	public
	 * @return	null
	 */
    public function display($tpl = null)
    {
        $this->setHeading('COM_EASYSOCIAL_HEADING_VIDEOS_CATEGORIES');
        $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_VIDEOS_CATEGORIES');

        // Insert Joomla buttons
        JToolbarHelper::addNew();
        JToolbarHelper::divider();
        JToolbarHelper::publishList('publish');
        JToolbarHelper::unpublishList('unpublish');
        JToolbarHelper::deleteList();

        // Get the model
        $model = FD::model('Videos', array('initState' => true));

        // Remember the states
        $search = $model->getState('search');
        $limit = $model->getState('limit');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');

        // Get the categories
        $categories = $model->getCategories(array('search' => $search, 'administrator' => true));

        // Get the pagination 
        $pagination = $model->getPagination();

        $this->set('simple', $this->input->getString('tmpl') == 'component');
        $this->set('categories', $categories);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('limit', $limit);
        $this->set('pagination', $pagination);
        $this->set('search', $search);

        return parent::display('admin/videocategories/default');
    }

    /**
     * Displays the category form
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function form()
    {
        $id = $this->input->get('id', 0, 'int');

        $this->setHeading('COM_EASYSOCIAL_HEADING_VIDEOS_CATEGORIES_CREATE');
        $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_VIDEOS_CATEGORIES_CREATE');

        $category = ES::table('VideoCategory');
        $category->load($id);

        if ($id) {
            $this->setHeading('COM_EASYSOCIAL_HEADING_VIDEOS_CATEGORIES');
            $this->setDescription('COM_EASYSOCIAL_DESCRIPTION_VIDEOS_CATEGORIES');            
        } else {
            // If new record, it should be published by default.
            $category->state = SOCIAL_STATE_PUBLISHED;
        }

        // Get the active category
        $activeTab = $this->input->get('active', 'settings', 'cmd');

        // Get the acl for creation access
        $createAccess = $category->getProfileAccess();

        // Insert Joomla buttons
        JToolbarHelper::apply();
        JToolbarHelper::save();
        JToolbarHelper::save2new();
        JToolbarHelper::cancel();

        $this->set('createAccess', $createAccess);
        $this->set('activeTab', $activeTab);
        $this->set('category', $category);

        return parent::display('admin/videocategories/forms/default');
    }

    /**
     * Post process after publishing
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function publish()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories');
    }

    /**
     * Post process after unpublishing
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function unpublish()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories');
    }

    /**
     * Post process after deleting category
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function delete()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories');
    }

    /**
     * Post process after saving
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function save()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories');
    }

    /**
     * Post process after saving
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function save2new()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories&layout=form');
    }

    /**
     * Post process after a category is set as default
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function toggleDefault()
    {
        $this->info->set($this->getMessage());

        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories');
    }

    /**
     * Post process after apply is clicked
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function apply($category)
    {
        $this->info->set($this->getMessage());
        
        return $this->app->redirect('index.php?option=com_easysocial&view=videocategories&layout=form&id=' . $category->id);
    }
}
