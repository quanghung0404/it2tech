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

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Check for access
		$this->checkAccess('easyblog.manage.blog');

		// Set page details
		$this->setHeading(JText::_('COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_TITLE'), JText::_('COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_DESC'));

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state', 	'*', 'word' );
		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');
		$filter_category = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_category', 'filter_category', 	'*', 'int' );
		$filterLanguage = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_language', 	'filter_language', 	'', '' );
		$search = trim(JString::strtolower( $search ) );
		$order = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_order_Dir',	'filter_order_Dir',	'desc', 'word' );
		$source = $this->input->get('filter_source', '-1', 'default');
		$filteredBlogger = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );

		//Get data from the model
		$model = EB::model('Blogs');
		$rows = $model->getData();

		$pagination = $model->getDataPagination();

		// Determines if the viewer is rendering this in a dialog
		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertBlog', 'cmd');

		// Get autoposting sites
		$consumers = array();
		$sites = array('twitter', 'facebook', 'linkedin');
		$centralizedConfigured = false;

		foreach ($sites as $site) {

			$consumer = EB::table('OAuth');
			$consumer->load(array('system' => 1, 'type' => $site));

			if (!empty($consumer->id) && $consumer->access_token) {
				$centralizedConfigured  = true;

				$consumers[] = $consumer;
			}
		}

		$blogs 	= array();

		// Assign the category object into the list of blogs
		foreach ($rows as &$row) {

			$post = EB::post($row->id);

			// Get the primary category
			$post->category = $post->getPrimaryCategory();

			// Process the contribution item
			$post->contributionDisplay = JText::_('COM_EASYBLOG_BLOGS_WIDE');

			$contribution = $post->getBlogContribution();

			if ($contribution !== false) {
				$post->contributionDisplay = $contribution->getTitle();
			}

			$post->featured = $post->isFeatured();

			$blogs[] = $post;
		}

		$this->set('consumers'	, $consumers );
		$this->set('centralizedConfigured', $centralizedConfigured);
		$this->set('source', $source);
		$this->set('filterBlogger', $filteredBlogger);
		$this->set('filterLanguage', $filterLanguage);
		$this->set('browse' , $browse );
		$this->set('browseFunction' , $browsefunction );
		$this->set('blogs' 		, $blogs );
		$this->set('pagination'	, $pagination );
		$this->set('filter_state', $filter_state);
		$this->set('filter_category', $filter_category);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/default');
	}

	/**
	 * Displays a list of pending blogs on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function pending()
	{
		$this->checkAccess('easyblog.manage.pending');

		$this->setHeading('COM_EASYBLOG_BLOGS_PENDING_BLOGS', '', 'fa-clipboard');

		$search = $this->app->getUserStateFromRequest('com_easyblog.pending.search', 'search', '', 'string');
		$search = trim(JString::strtolower($search));

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_category', 'filter_category', '*', 'int');
		$order = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_order', 'filter_order', 'ordering', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// Get data from the model
		$model = EB::model('Pending');
		$items = $model->getBlogs();
		$pagination = $model->getPagination();

		// Get the filters for category
		$filter = $this->getFilterCategory($categoryFilter);

		$posts = array();

		if ($items) {
			foreach ($items as $item) {

				$post = EB::post($item->post_id . '.' . $item->id);

				$posts[] = $post;
			}
		}

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/pending');
	}

	/**
	 * Displays a list of templates
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function templates()
	{
		// Ensure the user has access to manage templates
		$this->checkAccess('easyblog.manage.templates');

		$this->setHeading('COM_EASYBLOG_BLOGS_POST_TEMPLATES_TITLE', '', 'fa-clipboard');

		EB::loadLanguages();

		$model = EB::model('Templates');
		$rows = $model->getItems();
		$pagination = $model->getPagination();
		$templates = array();

		foreach ($rows as $row) {
			$template = EB::table('PostTemplate');
			$template->bind($row);

			$templates[] = $template;
		}

		$this->set('templates', $templates);
		$this->set('pagination', $pagination);

		parent::display('blogs/templates');
	}


	/**
	 * Displays a list of draft post on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function drafts()
	{
		$this->checkAccess('easyblog.manage.blog');

		$this->setHeading('COM_EASYBLOG_BLOGS_DRAFT_BLOGS', '', 'fa-file-o');

		$search = $this->app->getUserStateFromRequest('com_easyblog.drafts.search', 'search', '', 'string');
		$search = trim(JString::strtolower($search));

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '*', 'int');
		$authorFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_blogger', 'filter_blogger', '*', 'int');
		$order = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order', 'filter_order', 'ordering', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// Get the filters for category
		$filter = $this->getFilterCategory($categoryFilter);

		// Get the filters for category
		$authorfilter = $this->getFilterBlogger($authorFilter);

		// Get data from the model
		$model = EB::model('Revisions');
		$rows  = $model->getItems();
		$pagination = $model->getItemsPagination();
		$drafts = array();

		if ($rows) {
			foreach ($rows as &$row) {

				$uid = $row->post_id . '.' . $row->id;
				$post = EB::Post($uid);

				$post->revisionOrdering = $row->ordering;

				$drafts[]	= $post;
			}
		}

		$this->set('search', $search);
		$this->set('drafts', $drafts);

		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $filter);
		$this->set('authorFilter', $authorfilter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/drafts');
	}

	/**
	 * Displays a list of draft blogs on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function drafts_old()
	{
		// @rule: Test for user access if on 1.6 and above
		$this->checkAccess('easyblog.manage.pending');

		$this->setHeading('COM_EASYBLOG_BLOGS_DRAFT_BLOGS', '', 'fa-clipboard');

		$search				= $this->app->getUserStateFromRequest( 'com_easyblog.blogs.search', 			'search', 			'', 'string' );
		$search				= trim(JString::strtolower($search));
		$filter_category	= $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_category', 	'filter_category', 	'*', 'int' );
		$order				= $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_order', 		'filter_order', 	'ordering', 'cmd' );
		$orderDirection		= $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		// Get data from the model
		$model = EB::model('Revisions');
		$rows  = $model->getItems();
		$pagination = $model->getItemsPagination();
		$drafts = array();

		if ($rows) {
			foreach ($rows as &$row) {
				$rev = EB::table('Revision');
				$rev->load($row->id);

				$drafts[]	= $rev;
			}
		}

		$this->set('search', $search);
		$this->set('drafts', $drafts);
		$this->set('pagination', $pagination);

		parent::display('blogs/drafts');
	}

	public function getLanguageTitle( $code )
	{
		$db 	= EasyBlogHelper::db();
		$query	= 'SELECT ' . $db->nameQuote( 'title' ) . ' FROM '
				. $db->nameQuote( '#__languages' ) . ' WHERE '
				. $db->nameQuote( 'lang_code' ) . '=' . $db->Quote( $code );
		$db->setQuery( $query );

		$title 	= $db->loadResult();

		return $title;
	}

	public function getFilterBlogger($filter_type = '*')
	{
		$model = EB::model('Blogger');
		$authors = $model->getData('alphabet', null, 'showbloggerwithpost');
		$filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_BLOGGER'));

		foreach ($authors as $author) {
			$filter[] = JHTML::_('select.option', $author->id, $author->name);
		}

		return JHTML::_('select.genericlist', $filter, 'filter_blogger', 'class="form-control" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	public function getFilterCategory($filter_type = '*')
	{
		$filter[]	= JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_CATEGORY') );

		$model = EB::model('Category');
		$categories = $model->getAllCategories();

		foreach ($categories as $cat) {
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_category', 'class="form-control" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	public function getFilterState($filter_state='*')
	{
		$state[] = JHTML::_('select.option', '', '- '. JText::_('COM_EASYBLOG_SELECT_STATE') .' -' );
		$state[] = JHTML::_('select.option', 'P', JText::_('COM_EASYBLOG_PUBLISHED'));
		$state[] = JHTML::_('select.option', 'U', JText::_('COM_EASYBLOG_UNPUBLISHED'));
		$state[] = JHTML::_('select.option', 'S', JText::_('COM_EASYBLOG_SCHEDULED'));
		$state[] = JHTML::_('select.option', 'T', JText::_('COM_EASYBLOG_TRASHED'));
		$state[] = JHTML::_('select.option', 'F', JText::_('COM_EASYBLOG_STATE_FEATURED'));
		$state[] = JHTML::_('select.option', 'A', JText::_('COM_EASYBLOG_STATE_ARCHIVED'));
		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="form-control" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	/**
	 * Registers necessary buttons on the toolbar.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{
		if ($this->getLayout() == 'templates') {
			JToolBarHelper::title(JText::_('COM_EASYBLOG_BLOGS_POST_TEMPLATES_TITLE'), 'blogs');

			JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE_POST_TEMPLATES'), 'blogs.deletePostTemplates');
			return;
		}

		if ($this->getLayout() == 'pending') {
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_TITLE' ), 'blogs' );

			JToolbarHelper::deleteList(JText::_('Are you sure you want to delete these pending posts?'),'pending.remove');
			JToolbarHelper::publishList('blogs.publish', JText::_('COM_EASYBLOG_APPROVE'));
			JToolbarHelper::unpublishList('blogs.unpublish', JText::_('COM_EASYBLOG_REJECT'));
			return;
		}

		if ($this->getLayout() == 'drafts') {
			JToolBarHelper::title( JText::_('COM_EASYBLOG_BLOGS_DRAFT_BLOGS'), 'blogs');

			JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_BLOGS_DRAFT_DELETE_DRAFTS_CONFIRMATION'), 'drafts.remove');
			return;
		}

		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_TITLE' ), 'blogs' );

		JToolBarHelper::addNew('blogs.create');
		JToolBarHelper::divider();

		$state	= $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 'filter_state', '*', 'word' );

		if ($state != 'T') {

			JToolbarHelper::publishList('blogs.publish');
			JToolbarHelper::unpublishList('blogs.unpublish');
			JToolBarHelper::custom('blogs.feature', 'star' , '' , JText::_('COM_EASYBLOG_FEATURE_TOOLBAR'));
			JToolBarHelper::custom('blogs.unfeature', 'star-empty' , '' , JText::_('COM_EASYBLOG_UNFEATURE_TOOLBAR'));

			JToolbarHelper::archiveList('blogs.archive');
			JToolbarHelper::unarchiveList('blogs.unarchive');

			JToolbarHelper::custom('blogs.lock', 'lock', '', JText::_('COM_EASYBLOG_TOOLBAR_LOCK'));
			JToolbarHelper::custom('blogs.unlock', 'unlock', '', JText::_('COM_EASYBLOG_TOOLBAR_UNLOCK'));

			JToolBarHelper::custom('blogs.toggleFrontpage', 'featured.png', 'featured_f2.png', JText::_( 'COM_EASYBLOG_FRONTPAGE_TOOLBAR' ) , true);
			JToolBarHelper::divider();
		}

		// If this is on the trash view, we need to show empty trash icon
		if ($state == 'T') {
			JToolbarHelper::publishList('blogs.restore', JText::_('COM_EASYBLOG_RESTORE'));
			JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE'), 'blogs.remove');
		} else {
			JToolbarHelper::trash('blogs.trash');

			JToolBarHelper::custom('blogs.move' , 'move' , '' , JText::_('COM_EASYBLOG_MOVE') );
			JToolBarHelper::custom('blogs.copy' , 'copy' , '' , JText::_('COM_EASYBLOG_COPY') );
			JToolBarHelper::divider();

			JToolBarHelper::custom('blogs.changeAuthor' , 'users' , '' , JText::_('COM_EASYBLOG_CHANGE_AUTHOR'));
			JToolBarHelper::custom('blogs.resetHits' , 'purge' , '' , JText::_('COM_EASYBLOG_RESET_HITS'));
			JToolBarHelper::custom('blogs.resetRatings' , 'star-empty' , '' , JText::_('COM_EASYBLOG_RESET_RATINGS'));
		}
	}

}
