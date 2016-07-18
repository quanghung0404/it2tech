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

FD::import('admin:/views/views');

class EasySocialViewVideos extends EasySocialAdminView
{
	/**
	 * Main method to display the points view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 */
    public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_VIDEOS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_VIDEOS');

		// Add Joomla buttons here
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
		JToolbarHelper::divider();
		JToolbarHelper::custom('makeFeatured', 'featured', '', JText::_('COM_EASYSOCIAL_MAKE_FEATURED'));
		JToolbarHelper::custom('removeFeatured', 'star', '', JText::_('COM_EASYSOCIAL_REMOVE_FEATURED'));

		$model = ES::model('Videos', array('initState' => true));

		$filter = $model->getState('filter');
		$state = $model->getState('published');
		$limit = $model->getState('limit');
		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$search = $model->getState('search');

		if ($filter != 'all') {
			$filter = (int) $filter;
		}

		// Load a list of extensions so that users can filter them.
		$videos = $model->getItems();

		// Get pagination
		$pagination = $model->getPagination();

		$this->set('filter', $filter);
		$this->set('direction', $direction);
		$this->set('ordering', $ordering);
		$this->set('limit', $limit);
		$this->set('search', $search);
		$this->set('videos', $videos);
		$this->set('pagination', $pagination);
		$this->set('simple', $this->input->getString('tmpl') == 'component');

		parent::display('admin/videos/default');
	}

	/**
	 * Renders the video form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function form()
	{
		// Try to load the video that needs to be edited
		$id = $this->input->get('id', 0, 'int');

		$this->setHeading('COM_EASYSOCIAL_HEADING_VIDEOS_EDIT_VIDEO');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_EDIT_VIDEO');

		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table);

		// Load front end's language file
		ES::language()->loadSite();

		$model = ES::model('Videos');
		$categories = $model->getCategories();

		// Retrieve the privacy library
		$privacy = ES::privacy();

		// Retrieve the tags
		$tags = $video->getTags();

		$this->set('privacy', $privacy);
		$this->set('tags', $tags);
		$this->set('categories', $categories);
		$this->set('table', $table);
		$this->set('video', $video);

		// Add Joomla buttons here
		JToolbarHelper::apply('apply', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
		JToolbarHelper::save('save', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
		JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));

		parent::display('admin/videos/form');
	}

	/**
	 * Post process after a video is saved
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save(SocialVideo $video, $task)
	{
		$this->info->set($this->getMessage());

		$redirect = 'index.php?option=com_easysocial&view=videos';

		if ($task == 'apply') {
			$redirect .= '&layout=form&id=' . $video->id;
		}
		
		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is featured / unfeatured
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toggleDefault()
	{
		$this->info->set($this->getMessage());

		return $this->app->redirect('index.php?option=com_easysocial&view=videos');
	}

	/**
	 * Post process after a video has been published
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function publish()
	{
		$this->info->set($this->getMessage());

		return $this->app->redirect('index.php?option=com_easysocial&view=videos');
	}

	/**
	 * Post process after a video has been unpublished
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unpublish()
	{
		$this->info->set($this->getMessage());

		return $this->app->redirect('index.php?option=com_easysocial&view=videos');
	}

	/**
	 * Post process after a video has been deleted
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete()
	{
		return $this->app->redirect('index.php?option=com_easysocial&view=videos');
	}
}
