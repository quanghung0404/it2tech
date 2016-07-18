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

class EasyBlogViewCategories extends EasyBlogView
{
	public function __construct($options = array())
	{

		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', '', 'word');

		if ($layout == 'listings') {
			$this->paramsPrefix = 'category';
		}

		parent::__construct($options);
	}

	/**
	 * Displays the default categories layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Set meta tags for bloggers
		EB::setMeta(META_ID_GATEGORIES, META_TYPE_VIEW);

		// If the active menu is this view, we should not make the breadcrumb linkable.
		if (EasyBlogRouter::isCurrentActiveMenu('categories')) {
			$this->setPathway(JText::_('COM_EASYBLOG_CATEGORIES_BREADCRUMB'), '');
		}

		// Sorting options
		$defaultSorting = $this->config->get('layout_sorting_category', 'latest');
		$sort = $this->input->get('sort', $defaultSorting, 'cmd');

		// Load up our own models
		$model = EB::model('Category');

		// Test if there's any explicit inclusion of categories
		$menu = $this->app->getMenu()->getActive();
		$inclusion = '';

		if (is_object($menu) && stristr($menu->link , 'view=categories') !== false) {
			$inclusion = EB::getCategoryInclusion($menu->params->get('inclusion'));
		}

		// Get the number of categories to show per page
		$limit = $this->config->get('layout_pagination_categories_per_page');

		// Get the categories
		$categories = $model->getCategories($sort, $this->config->get('main_categories_hideempty'), $limit, $inclusion);

		// Get the pagination
		$pagination	= $model->getPagination();
		$pagination = $pagination->getPagesLinks();

		// Format the categories
		$categories = EB::formatter('categories', $categories);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_CATEGORIES_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog&view=categories');

		// Get the default pagination limit for authors
		$limit = $this->app->getCfg('list_limit');
		$limit = $limit == 0 ? 5 : $limit;

		$this->set('config', $menu);
		$this->set('limit', $limit);
		$this->set('categories', $categories);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);

		$namespace 	= 'blogs/categories/default';

		if ($this->getLayout() == 'simple') {
			$namespace 	= 'blogs/categories/default.simple';
		}

		parent::display($namespace);
	}

	/**
	 * Displays a list of blog posts on the site filtered by a category.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listings()
	{
		// Retrieve sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$id = $this->input->get('id', 0, 'int');

		// Try to load the category
		$category = EB::table('Category');
		$category->load($id);

		$menu = $this->app->getMenu()->getActive();

		// If the category isn't found on the site throw an error.
		if (!$id || !$category->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_CATEGORY_NOT_FOUND'));
		}

		// Set the meta description for the category
		EB::setMeta($category->id, META_TYPE_CATEGORY);

		// Set a canonical link for the category page.
		$this->canonical($category->getExternalPermalink());

		// Get the privacy
		$privacy = $category->checkPrivacy();

		if ($privacy->allowed || EB::isSiteAdmin() || (!$this->my->guest && $this->config->get('main_allowguestsubscribe'))) {
			$this->doc->addHeadLink($category->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$this->doc->addHeadLink($category->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}

		// Set views breadcrumbs
		// $this->setViewBreadcrumb($this->getName());

		// Set the breadcrumb for this category
		if (!EBR::isCurrentActiveMenu('categories', $category->id)) {
			// Always add the final pathway to the category title.
			$this->setPathway($category->title, '');
		}

		//get the nested categories
		$category->childs = null;

		// Build nested childsets
		EB::buildNestedCategories($category->id, $category, false, true);

		// Parameterize initial subcategories to display. Ability to configure from backend.
		$nestedLinks = '';
		$initialLimit = ($this->app->getCfg('list_limit') == 0) ? 5 : $this->app->getCfg('list_limit');

		if (count($category->childs) > $initialLimit) {
			$initialNestedLinks = '';
			$initialRow			= new stdClass();
			$initialRow->childs = array_slice($category->childs, 0, $initialLimit);

			EB::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

			$moreNestedLinks = '';
			$moreRow = new stdClass();
			$moreRow->childs = array_slice($category->childs, $initialLimit);

			EB::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

			// Hide more nested links until triggered
			$nestedLinks .= $initialNestedLinks;

			$nestedLinks .= '<span class="more-subcategories-toggle" data-more-categories-link> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript:void(0);">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($category->childs) - $initialLimit) . '</a></span>';
			$nestedLinks .= '<span class="more-subcategories" style="display: none;" data-more-categories>, ' . $moreNestedLinks . '</span>';

		} else {
			EB::accessNestedCategories($category, $nestedLinks, '0', '', 'link', ', ');
		}

		$catIds = array();
		$catIds[] = $category->id;

		// If user decided not to show posts from subcategories, we can skip this part.
		if ($menu && $menu->params->get('category_subcategories_posts', true)) {
			EB::accessNestedCategoriesId($category, $catIds);
		}

		$category->nestedLink = $nestedLinks;

		// Get the category model
		$model = EB::model('Category');

		// Get total posts in this category
		$category->cnt = $model->getTotalPostCount($category->id);

		$limit = EB::pagination('', '', '')->getLimit(EBLOG_PAGINATION_CATEGORIES);

		// Get the posts in the category
		$data = $model->getPosts($catIds, $limit);

		// Get the pagination
		$pagination = $model->getPagination();

		// Get allowed categories
		$allowCat = $model->allowAclCategory($category->id);

		// Format the data that we need
		$posts = array();

		// Ensure that the user is really allowed to view the blogs
		if (!empty($data)) {
			$posts = EB::formatter('list', $data);
		}

		// Check isCategorySubscribed
		$isCategorySubscribed = $model->isCategorySubscribedEmail($category->id, $this->my->email);
		$subscriptionId = '';

		if ($isCategorySubscribed) {
			$subscriptionModel = EB::model('Subscription');
			$subscriptionId = $subscriptionModel->getSubscriptionId($this->my->email, $category->id, EBLOG_SUBSCRIPTION_CATEGORY);
		}

		// If this category has a different theme, we need to output it differently
		if (!empty($category->theme)) {
			$this->setTheme($category->theme);
		}

		// Set the page title
		$title = EB::getPageTitle(JText::_($category->title));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Set the return url
		$return = base64_encode($category->getExternalPermalink());

		// Get the pagination
		$pagination = $pagination->getPagesLinks();

		$this->set('subscriptionId', $subscriptionId);
		$this->set('allowCat', $allowCat);
		$this->set('category', $category);
		$this->set('sort', $sort);
		$this->set('posts', $posts);
		$this->set('return', $return);
		$this->set('pagination', $pagination);
		$this->set('privacy', $privacy);
		$this->set('isCategorySubscribed', $isCategorySubscribed);

		parent::display('blogs/categories/item');
	}
}
