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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewDashboard extends EasyBlogView
{
	/**
	 * Default display for dashboard
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());

		$user = EB::user($this->my->id);

		// Get the page title for this page.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Retrieve a list of blog posts ordered by the popularity
		$blogModel = EB::model('Blog');
		$posts = $blogModel->getBlogsBy('blogger', $this->my->id, 'popular', 5);
		$posts = EB::formatter('list', $posts);

		// Get the most recent blog post by the current user.
		$latest = $blogModel->getLatestPostByAuthor($this->my->id);

		// Retrieve a list of categories created by the user
		$categoriesModel = EB::model('Categories');
		$categories = $categoriesModel->getCategoriesByBlogger($this->my->id);

		// Get total pending entries
		$pending = 0;

		// Retrieve the total number of pending posts
		if ($this->acl->get('manage_pending')) {

	        // Get total pending blog posts
	        $model = EB::model('Blogs');
	        $pending = $model->getTotalPending();
		}

		// Get most commented post from this author
		$mostCommentedPosts = $blogModel->getMostCommentedPostByAuthor($this->my->id, 5);

		// Get a list of recent comments made on the author's post
		$commentsModel = EB::model('Comments');
		$recentComments = $commentsModel->getRecentCommentsOnAuthor($this->my->id, 5);

		// Get a list of top commenters on the person's blog
		$topCommenters = $commentsModel->getTopCommentersForAuthorsPost($this->my->id, 5);

		// Get a list of top commenters on the person's blog
		$totalHits = $blogModel->getTotalHits($this->my->id);

		$this->set('topCommenters', $topCommenters);
		$this->set('recentComments', $recentComments);
		$this->set('mostCommentedPosts', $mostCommentedPosts);
		$this->set('pending', $pending);
		$this->set('latest', $latest);
		$this->set('posts', $posts);
		$this->set('categories', $categories);
		$this->set('totalHits', $totalHits);

		parent::display('dashboard/main/default');
	}

	public function bindTags($arrayData)
	{
		$result	= array();

		if( count( $arrayData ) > 0 )
		{
			foreach( $arrayData as $tag )
			{
				$obj		= new stdClass();
				$obj->title	= $tag;
				$result[]	= $obj;
			}
		}
		return $result;
	}

	public function bindContribute($contribution = '')
	{
		if( $contribution )
		{
			$contributed			= new stdClass();
			$contributed->team_id	= $contribution;
			$contributed->selected	= 1;

			return $contributed;
		}
		return false;
	}

	/**
	 * Deprecated since 5.0. Use @composer instead.
	 *
	 * @deprecated	5.0
	 */
	public function write()
	{
		$this->entries();
	}

	/**
	 * Retrieves the dropbox data for the current user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getFlickrData()
	{
		// Test if the user is already associated with dropbox
		$oauth  = EB::table('OAuth');

		// Test if the user is associated with flickr
		$state	= $oauth->loadByUser($this->my->id, EBLOG_OAUTH_FLICKR);

		$data   = new stdClass();
		$data->associated	= $state;
		$data->callback  = 'flickr' . rand();
		$data->redirect  = base64_encode(rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=media&layout=flickrLogin&tmpl=component&callback=' . $data->callback);

		// Default login to the site
		$data->login = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&controller=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect;


		if ($this->app->isAdmin()) {
			$data->login = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&c=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect . '&id=' . $this->my->id;
		}

		return $data;
	}

	/**
	 * Displays the edit profile screen
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function profile()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Get the page title for this page.
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_BREADCRUMB'), '');

		// Get editor
		$editor = JFactory::getEditor();

		// Load the user's profile
		$profile = EB::user($this->my->id);

		// Get feedburner data
		$feedburner	= EB::table('Feedburner');
		$feedburner->load($this->my->id);

		// Get user's adsense code
		$adsense	= EB::table('Adsense');
		$adsense->load($this->my->id);

		// Get meta info for this blogger
		$metasModel = EB::model('Metas');
		$meta		= $metasModel->getMetaInfo(META_TYPE_BLOGGER, $this->my->id);

		// Load twitter data for this user
		$twitter = EB::table('Oauth');
		$twitter->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_TWITTER, 'system' => 0));

		// Load linkedin data for this user
		$linkedin = EB::table('Oauth');
		$linkedin->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_LINKEDIN, 'system' => 0));

		// Load facebook data for this user
		$facebook = EB::table('Oauth');
		$facebook->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_FACEBOOK, 'system' => 0));

		// Load users params
		$params = $profile->getParam();

		$multithemes	= new stdClass();
		$multithemes->enable = $this->config->get('layout_enablebloggertheme', true);

		if( !is_array($this->config->get('layout_availablebloggertheme'))) {
			$multithemes->availableThemes	= explode('|', $this->config->get('layout_availablebloggertheme' ) );
		}

		$multithemes->selectedTheme = $params->get('theme', 'global');

		$this->set('params', $params);
		$this->set('editor', $editor);
		$this->set('feedburner', $feedburner);
		$this->set('adsense', $adsense);
		$this->set('profile', $profile);
		$this->set('meta', $meta);
		$this->set('multithemes', $multithemes);
		$this->set('facebook', $facebook);
		$this->set('linkedin', $linkedin);
		$this->set('twitter', $twitter);

		parent::display('dashboard/account/default');
	}

	/**
	 * Displays a list of blog posts created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function entries()
	{
		// Only allow logged in users on this page
		EB::requireLogin();

		// Ensure that the user has access to this section
		$this->checkAcl('add_entry');

		// Get the user group acl
		$aclLib = EB::acl();

		//check if this is coming from write layout or not.
		$isWrite = $this->getLayout() == 'write' ? 1 : 0;
		$defaultCategory = '';

		if ($isWrite) {
			$defaultCategory = $this->input->get('category', '', 'int');

			if ($defaultCategory) {
				$defaultCategory = '&category=' . $defaultCategory;
			} else {
				$defaultCategory = '';
			}
		}

		// Get the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_BREADCRUMB'), '');

		// Determines if the user is filtering posts by states
		$state = $this->input->get('filter', 'all', 'default');

		if ($state != 'all') {
			$state = (int) $state;
		}

		// Determines if the user is searching for post
		$search = $this->input->get('post-search', '', 'string');

		// Determines if the blog posts should be filtered by specific category
		$categoryFilter = $this->input->get('category', 0, 'int');

		// Get limit
		$limit = $this->config->get('layout_pagination_dashboard_post_per_page');

		// Retrieve the posts
		$model = EB::model('Dashboard');

		$userId = $this->my->id;

		// if the user have moderation entry permission, so it will show all the blog post on blog entries dashboard page.
		if ($aclLib->get('moderate_entry')) {
			$userId = '';
		}

		$result = $model->getEntries($userId, array('category' => $categoryFilter, 'state' => $state, 'search' => $search, 'limit' => $limit));

		// Get pagination
		$pagination = $model->getPagination();

		$pagination->setAdditionalUrlParam('view', 'dashboard');
		$pagination->setAdditionalUrlParam('layout', 'entries');

		if ($categoryFilter) {
			$pagination->setAdditionalUrlParam('category', $categoryFilter);
		}

		if ($state) {
			$pagination->setAdditionalUrlParam('filter', $state);
		}

		if ($search) {
			$pagination->setAdditionalUrlParam('post-search', $search);
		}

		// Format the posts
		$posts = EB::formatter('list', $result);

		// Get oauth clients
		$clients = array('twitter', 'facebook', 'linkedin');
		$oauthClients = array();

		foreach ($clients as $client) {
			$oauth 	= EB::table('OAuth');
			$exists = $oauth->load(array('user_id' => $userId, 'type' => $client));

			if ($exists && $this->acl->get("update_" . $client) && $this->config->get('integrations_' . $client . '_centralized_and_own')) {
				$oauthClients[]	= $oauth;
			}
		}

		// Get a list of categories on the site
		$categoryModel = EB::model('Category');
		$rows = $categoryModel->getAllCategories();
		$categories	= array();

		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$category	= EB::table('Category');
				$category->bind( $row );

				$categories[]	= $category;
			}
		}

		$revisionModel = EB::model('Revisions');

		// lets preload the revisions count.
		if ($posts) {
			$pIds = array();

			foreach($posts as $post) {
				$pIds[] = $post->id;
			}

			$revisionModel->getRevisionCount($pIds, 'cache');
		}

		// Get revisions for the post
		foreach ($posts as $post) {
			$versions = $revisionModel->getAllRevisions($post->id);
			$post->versions = $versions;
		}

		$rejectModel = EB::model('postreject');

		// Identify if the post gets rejected
		foreach ($posts as $post) {
			$rejected = $rejectModel->isRejected($post->id);
			$post->isRejected = $rejected;
		}		

		$this->set('pagination', $pagination);
		$this->set('posts', $posts);
		$this->set('search', $search);
		$this->set('categoryFilter', $categoryFilter);
		$this->set('categories', $categories);
		$this->set('oauthClients', $oauthClients);
		$this->set('state', $state);
		$this->set('isWrite', $isWrite);
		$this->set('defaultCategory', $defaultCategory);

		echo parent::display('dashboard/entries/default');
	}

	/**
	 * Displays a list of versions for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function compare()
	{
		// Only allow logged in users on this page
		EB::requireLogin();
		//Get Revisions table
		$revisionModel = EB::model('Revisions');
		// get blogid
		$blogId			= JRequest::getVar( 'blogid' , '' );

		//Load the version for the blog post
		$versions		= $revisionModel->getAllBlogs($blogId);

		$this->set('versions', $versions);

		echo parent::display('dashboard/version/default');
	}

	/**
	 * Displays a comparison of two versions for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function diff()
	{
		require(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/htmldiff/html_diff.php');

		// Only allow logged in users on this page
		EB::requireLogin();

		//Get revision table
		$Revision = EB::table('Revision');

		// get blogid
		$versionId			= JRequest::getVar( 'id' , '' );
		$postId			= JRequest::getVar( 'post_id' , '' );

		//Load the version for the blog post
		$currentBlog		= $Revision->getCurrentBlog($postId);
		$compareBlog		= $Revision->getCompareBlog($versionId);

		$currentData	= json_decode($currentBlog->params);
		$compareData	= json_decode($compareBlog->params);

		$diff	=	html_diff($currentData->intro,$compareData->intro, true);

		// Get category title
		$category = EB::table('Category');
		$category->load($currentData->category_id);
		$dataArr = array();
		$dataArr['catOld'] = $category->title;
		$category->load($compareData->category_id);
		$dataArr['catNew'] = $category->title;

		$this->set('currentData', $currentData);
		$this->set('compareData', $compareData);
		$this->set('dataArr', $dataArr);
		$this->set('diff', $diff);
		$this->set('blogId', $postId);
		$this->set('versionId', $versionId);

		echo parent::display('dashboard/version/compare');
	}

	/**
	 * Displays comments on the dashboard
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function comments()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_BREADCRUMB'), '');

		// Load up comments model
		$model = EB::model('Comment');

		// Filters
		$search = $this->input->get('post-search', '', 'string');
		$filter = $this->input->get('filter', 'all', 'word');
		$sort = 'latest';

		// Get limit
		$limit = $this->config->get('layout_pagination_dashboard_comment_per_page');

		// If the user is allowed to manage comments, allow them to view all comments
		if ($this->acl->get('manage_comment')) {
			$result = $model->getComments(0, '', $sort, '', $search, $filter, $limit);
		} else {

			// Only retrieve comments made by them
			$result = $model->getComments(0, $this->my->id, $sort, '', $search, $filter, $limit);
		}

		// Get pagination
		$pagination	= $model->getPagination();
		$comments = array();

		if ($result) {

			foreach ($result as $row) {

				$comment = EB::table('Comment');
				$comment->bind($row);

				$comment->isOwner = $this->my->id == $row->blog_owner;

				$comments[] = $comment;
			}
		}

		$this->set('search', $search);
		$this->set('filter', $filter);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);

		parent::display('dashboard/comments/default');
	}

	/**
	 * Displays categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function categories()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Check if the user is allowed to create categories
		$this->checkAcl('create_category');

		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB'), '');

		// Get model
		$model = EB::model('Categories');

		// Get filters
		$order = $this->input->get('order', '', 'cmd');
		$search = $this->input->get('search', '', 'default');


		$bloggerId = $this->my->id;
		$showAuthorName = false;

		// if user is a super admin, we do not need to filter the author of the categories.
		if (EB::isSiteAdmin()) {
			$bloggerId = 0;
			$showAuthorName = true;
		}


		// Get categories
		$rows = $model->getCategoriesByBlogger($bloggerId, $order, $search);

		$pagination = $model->getPaginationByBlogger($bloggerId, $search);

		$categories = array();

		$catRuleItems = EB::table('CategoryAclItem');
		$categoryRules = $catRuleItems->getAllRuleItems();

		$category = EB::table('Category');
		$assignedACL = $category->getAssignedACL();

		if (count($rows) > 0) {

			$authors = array();

			foreach ($rows as $row) {
				$category = EB::table('Category');
				$category->bind( $row );

				$authors[] = $category->created_by;
				$categories[]	= $category;
			}

			//preload users;
			EB::user($authors);
		}

		// Get editor
		$editor	= JFactory::getEditor();

		$this->set('editor', $editor);
		$this->set('order', $order);
		$this->set('search', $search);
		$this->set('categories', $categories);
		$this->set('pagination', $pagination);
		$this->set('categoryRules', $categoryRules);
		$this->set('assignedACL', $assignedACL);
		$this->set('showAuthorName', $showAuthorName);

		parent::display('dashboard/categories/default');
	}

	/**
	 * Displays a list of tags on the dashboard
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tags()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Ensure that the user has access to the tags page
		$this->checkAcl('create_tag');

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_TAGS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_TAGS_BREADCRUMB'));

		// Load the tags
		$model = EB::model('Tags');

		// Get the current search behavior
		$search = $this->input->get('search', '', 'default');

		// Get the current sorting behavior
		$sort = $this->input->get('sort', 'post', 'cmd');

		// Render the tags
		$tags = $model->getBloggerTags($this->my->id, $sort, $search);

		$this->set('search', $search);
		$this->set('sort', $sort);
		$this->set('tags', $tags);

		parent::display('dashboard/tags/default');
	}

	/**
	 * Displays a list of team requests
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function requests()
	{
		// Require the user to be logged in.
		EB::requireLogin();

		// Ensure that the user really has access to this listing
		if (!EB::isSiteAdmin() && !EB::isTeamAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_REQUESTS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_REQUESTS'));

		$model = EB::model('TeamBlogs');
		$userId = EB::isSiteAdmin() ? '' : $this->my->id;

		$requests = $model->getRequests($userId);

		foreach ($requests as &$request) {

			$request->user = EB::user($request->user_id);

			$request->team = EB::table('Teamblog');
			$request->team->load($request->team_id);

			$request->date = EB::date($request->created);
		}

		$this->set('requests', $requests);

		parent::display('dashboard/requests/default');
	}

	/**
	 * Displays a list of post revisions from user.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revisions()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_REVISIONS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_REVISIONS'));

		// Get filters
		$search = $this->input->get('post-search', '', 'string');
		$state = $this->input->get('state', '', 'string');

		$postId = $this->input->get('uid', 0, 'int');

		$options = array();
		$options['userId'] = $this->my->id;

		if ($search) {
			$options['search'] = $search;
		}

		if ($state != 'all') {
			$options['state'] = (int) $state;
		}

		if ($postId) {
			$options['postId'] = $postId;
		}

		// Get model
		$model = EB::model('Revisions');
		$rows  = $model->getRevisions($options);

		// Get pagination
		$pagination	= $model->getPagination();

		// Format our result
		$posts = array();
		if ($rows) {
			foreach ($rows as $row) {
				$uid = $row->post_id . '.' . $row->id;
				$post = EB::Post($uid);

				$post->revisionOrdering = $row->ordering;

				$posts[]	= $post;
			}
		}

		$activePost = '';
		if ($postId) {
			$activePost = EB::post($postId);
		}

		$this->set('activePost', $activePost);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('state', $state);

		parent::display('dashboard/revisions/default');
	}

	/**
	 * Displays a list of user's blog posts that are submitted for admin approval
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function pending()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_POST_UNDER_REVIEWS'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_POST_UNDER_REVIEWS'));

		// Get filters
		$search = $this->input->get('post-search', '', 'string');

		// Get model
		$model = EB::model('Blog');
		$rows  = $model->getPending($this->my->id, 'latest', 0, $search, false, '', true);

		// Get pagination
		$pagination	= $model->getPagination();

		// Format our result
		$posts = array();

		foreach ($rows as $row) {
			$uid = $row->post_id . '.' . $row->id;
			$post = EB::Post($uid);
			$post->tags = $post->getTags();

			$posts[]	= $post;
		}

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('search', $search);

		parent::display('dashboard/pending/default');
	}

	/**
	 * Displays a list of blog posts pending the admin's approval
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moderate()
	{
		// Require user to be logged in
		EB::requireLogin();

		// User must have access to view pending blog posts
		if (!$this->acl->get('manage_pending') && !$this->acl->get('publish_entry') && !EB::isSiteAdmin()) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_PENDING_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_PENDING_YOUR_REVIEW'));

		// Get filters
		$search = $this->input->get('post-search', '', 'string');

		$model = EB::model('Pending');
		$rows = $model->getBlogs();

		// Get pagination
		$pagination	= $model->getPagination();

		// Format our result
		$posts = array();

		foreach ($rows as $row) {
			$uid = $row->post_id . '.' . $row->id;
			$post = EB::Post($uid);
			$post->tags = $post->getTags();

			$posts[] = $post;
		}

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('search', $search);

		parent::display('dashboard/moderate/default');
	}

	/**
	 * Displays the quickpost layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function quickpost()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Test if microblogging is allowed
		if (!$this->config->get('main_microblog')) {
			$this->info->set(JText::_('COM_EASYBLOG_NOT_ALLOWED'), 'error');

			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Test ACL if add entry is allowed
		if (!$this->acl->get('add_entry') ) {
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_SHARE_A_STORY_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Get active tabs
		$active = $this->input->get('type', 'standard', 'word');

		// Get a list of available auto post sites
		$facebook = EB::oauth()->isUserAssociated('facebook', $this->my->id);
		$twitter = EB::oauth()->isUserAssociated('twitter', $this->my->id);
		$linkedin = EB::oauth()->isUserAssociated('linkedin', $this->my->id);

		// Retrieve existing tags
		$tagsModel = EB::model('Tags');
		$tags = $tagsModel->getTags();

		$this->set('facebook', $facebook);
		$this->set('twitter', $twitter);
		$this->set('linkedin', $linkedin);
		$this->set('active', $active);
		$this->set('tags', $tags);

		parent::display('dashboard/quickpost/default');
	}
}
