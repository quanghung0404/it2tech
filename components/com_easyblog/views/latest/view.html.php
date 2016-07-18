<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * Displays the frontpage blog listings on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{

		// Add the RSS headers on the page
		EB::feeds()->addHeaders('index.php?option=com_easyblog');

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		// Get the current active menu's properties.
		$params = $this->theme->params;
		$inclusion	= '';

		if ($params) {

			// Get a list of category inclusions
			$inclusion	= EB::getCategoryInclusion($params->get('inclusion'));

			if ($params->get('includesubcategories', 0) && !empty($inclusion)) {

				$tmpInclusion = array();

				foreach ($inclusion as $includeCatId) {

					// Retrieve nested categories
					$category = new stdClass();
					$category->id = $includeCatId;
					$category->childs = null;

					EB::buildNestedCategories($category->id, $category);

					$linkage = '';
					EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

					$catIds = array();
					$catIds[] = $category->id;
					EB::accessNestedCategoriesId($category, $catIds);

					$tmpInclusion = array_merge($tmpInclusion, $catIds);
				}

				$inclusion = $tmpInclusion;
			}
		}

		// Sorting for the posts
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$model = EB::model('Blog');

		$tobeCached = array();

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog($inclusion);
		$excludeIds = array();

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		if (!$this->theme->params->get('post_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		// Admin might want to display the featured blogs on all pages.
		$start = $this->input->get('start', 0, 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		if (!$this->theme->params->get('featured_slider_all_pages') && ($start != 0 || $limitstart != 0)) {
			$featured = array();
		}

		if ($featured) {
			$tobeCached = array_merge($tobeCached, $featured);
		}

		// Try to retrieve any categories to be excluded.
		$excludedCategories	= $this->config->get('layout_exclude_categories');
		$excludedCategories	= ( empty( $excludedCategories ) ) ? '' : explode( ',' , $excludedCategories );

		// Fetch the blog entries.
		$data = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, $excludedCategories, $inclusion, null, 'listlength', $this->theme->params->get('post_pin_featured', false));

		if ($data) {
			$tobeCached = array_merge($tobeCached, $data);
		}

		// we will cache it here.
		if ($tobeCached) {
			EB::cache()->insert($tobeCached);
		}

		// Get the pagination
		$pagination	= $model->getPagination();

		if ($featured) {
			// Format the featured items without caching
			$featured = EB::formatter('featured', $featured, false);
		}

		// Perform blog formatting without caching
		$posts = EB::formatter('list', $data, false);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_LATEST_PAGE_TITLE'));

		// Set the page title
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog');

		// Retrieve the pagination for the latest view
		$pagination	= $pagination->getPagesLinks();

		// Meta should be set later because formatter would have cached the post already.
		EB::setMeta(META_ID_LATEST, META_TYPE_VIEW);

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog', false);

		$this->set('return', $return);
		$this->set('posts', $posts);
		$this->set('featured', $featured);
		$this->set('pagination', $pagination);

		parent::display('blogs/latest/default');
	}
}
