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

class EasyBlogFormatterCategories extends EasyBlogFormatterStandard
{
	public function execute()
	{
		if (!$this->items) {
			return $this->items;
		}

		$limit = EB::call('Pagination', 'getLimit', array(EBLOG_PAGINATION_CATEGORIES));

		// lets cache these categories
		EB::cache()->insertCategories($this->items);

		$categories = array();

		// Get the category model
		$model = EB::model('Category');

		foreach ($this->items as $row) {

			// We want to load the table objects
			$category = EB::table('Category');
			$category->bind($row);

			// binding the extra info
			if (isset($row->cnt)) {
				$category->cnt = $row->cnt;
			}

			// Format the childs
			$category->childs = array();

			// Build childs list
			EB::buildNestedCategories($category->id, $category, false, true);


			// Parameterize initial subcategories to display. Ability to configure from backend.
			$nestedLinks = '';
			$subcategoryLimit = $this->app->getCfg('list_limit') == 0 ? 5 : $this->app->getCfg('list_limit');

			if (count($category->childs) > $subcategoryLimit) {

				$initialNestedLinks = '';
				$initialRow = new stdClass();
				$initialRow->childs = array_slice($category->childs, 0, $subcategoryLimit);

				EB::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

				$moreNestedLinks = '';
				$moreRow = new stdClass();
				$moreRow->childs = array_slice($category->childs, $subcategoryLimit);

				EB::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

				// Hide more nested links until triggered
				$nestedLinks .= $initialNestedLinks;
				$nestedLinks .= '<span class="more-subcategories-toggle"> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript:void(0);" onclick="eblog.categories.loadMore( this );">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($moreRow->childs) - $subcategoryLimit) . '</a></span>';
				$nestedLinks .= '<span class="more-subcategories" style="display: none;">, ' . $moreNestedLinks . '</span>';
			} else {
				EB::accessNestedCategories($category, $nestedLinks, '0', '', 'link', ', ');
			}

			// Set the nested links
			$category->nestedLink = $nestedLinks;

			// Get a list of nested categories and itself.
			$filterCategories = array($category->id);
			EB::accessNestedCategoriesId($category, $filterCategories);

			// Get a list of blog posts from this category
			$blogs = array();
			if (EB::cache()->exists($category->id, 'cats')) {
				$data = EB::cache()->get($category->id, 'cats');

				if (isset($data['post'])) {
					$blogs = $data['post'];
				}

			} else {
				$blogs = $model->getPosts($filterCategories, $limit);
			}

			// Format the blog posts
			$blogs = EB::formatter('list', $blogs);

			// Assign other attributes to the category object
			$category->blogs = $blogs;

			// Get the total number of posts in the category
			if (! isset($category->cnt)) {
				$category->cnt = $model->getTotalPostCount($filterCategories);
			}

			// Get a list of active authors within this category.
			$category->authors = $category->getActiveBloggers();

			// Check isCategorySubscribed
			$category->isCategorySubscribed = $model->isCategorySubscribedEmail($category->id, $this->my->email);

			// We need to get the subscription id
			if ($category->isCategorySubscribed) {
				$subscriptionModel = EB::model('Subscription');
				$category->subscriptionId = $subscriptionModel->getSubscriptionId($this->my->email, $category->id, EBLOG_SUBSCRIPTION_CATEGORY);
			}

			$categories[] = $category;
		}

		return $categories;
	}
}
