<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewIndex extends EasyDiscussView
{
	/**
	 * Displays a list of recent posts list.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$categoryId = $this->input->get('category_id', 0, 'int');
		$registry = new JRegistry();

		// Perform redirection if there is a category_id in the index view.
		if ($categoryId) {
			$redirect = EDR::_('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $categoryId, false);
			return $this->app->redirect($redirect);
		}

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// If there is an active menu, render the params
		if ($activeMenu && !$categoryId) {
			$registry->loadString($activeMenu->params);

			if ($registry->get('category_id')) {
				$categoryId	= $registry->get('category_id');
			}
		}

		// Todo: Perhaps we should fix the confused naming of filter and sort to type and sort
		$activeFilter = 'all';

		$filter = $this->input->get('filter', $registry->get('filter'), 'string');
		if ($filter) {
			$activeFilter = $filter;
		}

		// Determines if we should be sorting the view
		$sort = $this->input->get('sort', $registry->get('sort'), 'string');

		// Get the pagination limit
		$limit = $registry->get( 'limit' );
		$limit = ( $limit == '-2' ) ? ED::getListLimit() : $limit;
		$limit = ( $limit == '-1' ) ? $this->jconfig->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();

		// Set page title.
		ED::setPageTitle();

		// Set the meta of the page.
		ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=index');

		// Get list of categories on the site.
		$catModel = ED::model('Categories');

		// Pagination is by default disabled.
		$pagination = false;

		// Get the model.
		$postModel = ED::model('Posts');

		$cats = array();

		if ($categoryId) {
			if (is_array($categoryId)) {
				$cats = array_merge($cats, $categoryId);
			} else {
				$cats[] = $categoryId;
			}
		}

		// Get featured posts from this particular category.
		$featured = array();

		if ($this->config->get('layout_featuredpost_frontpage')) {

			$options 	= array(
									'pagination' => false,
									'category' => $cats,
									'sort' => 'latest',
									'filter' => $this->config->get('layout_frontpage_sorting'),
									'limit' => $this->config->get( 'layout_featuredpost_limit' , $limit ),
									'featured' => true
							);
			$featured	= $postModel->getDiscussions( $options );
			if (is_null($featured)) {
				$featured = array();
			}
		}

		// Get normal discussion posts.
		$options 	= array(
						'sort'		=> $sort,
						'category'	=> $cats,
						'filter'	=> $filter,
						'limit'		=> $limit,
						'featured'	=> false
					);

		$posts = $postModel->getDiscussions($options);

		if (is_null($posts)) {
			$posts = array();
		}

		$authorIds = array();
		$topicIds = array();
		$tmpPostsArr = array_merge($featured, $posts);

		if ($tmpPostsArr) {

			//preload posts
			ED::post($tmpPostsArr);

			// foreach ($tmpPostsArr as $tmpArr) {
			// 	$authorIds[] = $tmpArr->user_id;
			// 	$topicIds[] = $tmpArr->id;
			// }
		}

		$pagination = $postModel->getPagination();

		// $postLoader = ED::getTable('Posts');
		// $postLoader->loadBatch( $topicIds );

		// $postTagsModel = ED::model( 'PostsTags' );
		// $postTagsModel->setPostTagsBatch( $topicIds );

		// $model = ED::model('Posts');
		// $lastReplyUser = $model->setLastReplyBatch( $topicIds );
		// $lastReplyUser = array();

		// // Reduce SQL queries by pre-loading all author object.
		// $authorIds = array_merge($lastReplyUser, $authorIds);
		// $authorIds = array_unique($authorIds);

		// //preload users.
		// ED::user($authorIds);

		// Format featured entries.
		$featured = ED::formatPost($featured);

		// Format normal entries
		$posts = ED::formatPost($posts);

		// Let's render the layout now.
		$this->set('activeFilter', $activeFilter);
		$this->set('activeSort', $sort);
		$this->set('categories', $categoryId);
		$this->set('posts', $posts);
		$this->set('featured', $featured);
		$this->set('pagination', $pagination);

		parent::display('frontpage/default');
	}
}
