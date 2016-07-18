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
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewFeeds extends EasyBlogAdminView
{

	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.feeds');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		$this->setHeading('COM_EASYBLOG_BLOGS_FEEDS_TITLE', '', 'fa-rss-square');

		$model = EB::model('Feeds');
		$feeds = $model->getData();
		$pagination = $model->getPagination();

		if ($feeds) {

			foreach ($feeds as &$feed) {

				if ($feed->last_import == '0000-00-00 00:00:00') {
					$feed->import_text = JText::_('COM_EASYBLOG_NEVER');
				} else {
					$feed->import_text = $feed->last_import;
				}
			}
		}

		$filter_state = $this->app->getUserStateFromRequest( 'com_easyblog.feeds.filter_state', 		'filter_state', 	'*', 'word' );
		$search = $this->app->getUserStateFromRequest( 'com_easyblog.feeds.search', 			'search', 			'', 'string' );
		$search = trim(JString::strtolower($search));

		$this->set('state', JHTML::_('grid.state', $filter_state));
		$this->set('search', $search);
		$this->set('feeds', $feeds);
		$this->set('pagination', $pagination);

		parent::display('feeds/default');
	}

	/**
	 * Displays the new feed form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		// Load the feed table
		$feed = EB::table('Feed');
		$id = $this->input->get('id', 0, 'int');

		$feed->load($id);

		JHTML::_('behavior.modal');

		if (!empty($post)) {
			$feed->bind($post);
		}

		$title 	= 'COM_EASYBLOG_FEEDS_CREATE_NEW_TITLE';

		if ($feed->id) {
			$title 	= 'COM_EASYBLOG_FEEDS_EDIT_TITLE';
		}

		JToolBarHelper::title(JText::_($title));
		$this->setHeading($title, '', 'fa-rss-square');

		$category 	= '';
		$author 	= '';
		$teamName 	= '';

		if (!empty($feed->item_category)) {
			$category = $feed->getCategoryName();
		}

		if (!empty($feed->item_creator)) {
			$user = JFactory::getUser($feed->item_creator);
			$author	= $user->name;
		}

		if (!empty($feed->item_team)) {
			$team = EB::table('TeamBlog');
	   		$team->load($feed->item_team);
	   		$teamName = $team->title;
		}
		
		// Get a new params object
		$params = EB::getRegistry($feed->params);

		$this->set('params', $params);
		$this->set('feed', $feed);
		$this->set('category', $category);
		$this->set('author', $author);
		$this->set('teamName', $teamName);

		parent::display('feeds/form');
	}

	/**
	 * Displays the Joomla toolbar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{

		$layout = $this->getLayout();

		if ($layout == 'form') {


			// Set the toolbars
			JToolBarHelper::apply('feeds.apply');
			JToolBarHelper::save('feeds.save');
			JToolbarHelper::save2new('feeds.savenew');
			JToolBarHelper::divider();
			JToolbarHelper::cancel('feeds.cancel');

			return;
		}

		// Set page details
		$this->setHeading('COM_EASYBLOG_BLOGS_FEEDS_TITLE', '', 'fa-rss-square');

		JToolBarHelper::title(JText::_('COM_EASYBLOG_BLOGS_FEEDS_TITLE'), 'feeds');

		JToolbarHelper::addNew('feeds.add');
		JToolBarHelper::divider();
		JToolbarHelper::publishList('feeds.publish');
		JToolbarHelper::unpublishList('feeds.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_FEEDS_DELETE_CONFIRMATION'), 'feeds.remove');

	}
}
