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

class EasyBlogViewBlogger extends EasyBlogView
{
	/**
	 * Displays the all bloggers
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Set meta tags for bloggers
		EB::setMeta(META_ID_BLOGGERS, META_TYPE_VIEW);

		// Set the breadcrumbs only when necessary
		if (!EBR::isCurrentActiveMenu('blogger')) {
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , '' );
		}

		// Retrieve the current sorting options
		$sort = $this->input->get('sort','', 'cmd');
		$sortHTML = $this->getSorting($sort);

		// Retrieve the current filtering options.
		$filter = $this->input->get('filter', 'showallblogger', 'cmd');

		if ($this->config->get('main_bloggerlistingoption')) {
			$filter = $this->input->get('filter', 'showbloggerwithpost', 'cmd');
		}

		// Get the filter html codes
		$filterHTML = $this->getFilter($filter);

		// Retrieve search values
		$search = $this->input->get('search', '', 'string');

		// Retrieve the models.
		$bloggerModel = EB::model('Blogger');
		$blogModel = EB::model('Blog');
		$postTagModel = EB::model('PostTag');

		// Get limit
		$limit = $this->config->get('layout_pagination_bloggers_per_page');

		if (!$sort) {
			// Retrieve default sort from setting
			$sort = $this->config->get('layout_bloggerorder', 'latest');
		}

		// Retrieve the bloggers to show on the page.
		$results = $bloggerModel->getBloggers($sort, $limit, $filter , $search);
		$pagination = $bloggerModel->getPagination();

		// Determine the current page if there's pagination
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// Set the title of the page
		$title = JText::_('COM_EASYBLOG_BLOGGERS_PAGE_TITLE');
		$this->setPageTitle($title, $pagination, true);

		// Add canonical urls
		$this->canonical('index.php?option=com_easyblog&view=blogger');

		// Determine the default ordering for the posts
		$postsOrdering = $this->config->get('layout_postorder');
		$postsLimit = EB::call('Pagination', 'getLimit', array(EBLOG_PAGINATION_BLOGGERS));

		// Format the blogger listing.
		$authors = array();

		if (!empty($results)) {

			//preload users
			$ids = array();
			foreach ($results as $row) {
				$ids[] = $row->id;
			}

			EB::user($ids);

			// lets cache the bloggers
			EB::cache()->insertBloggers($results);

			// lets group the posts for posts caching first
			$tobeCached = array();
			$bloggerPosts = array();

			foreach($results as $row) {
				$bloggerId = $row->id;

				$items = $blogModel->getBlogsBy('blogger', $row->id, $postsOrdering, $postsLimit, EBLOG_FILTER_PUBLISHED);
				$bloggerPosts[$bloggerId] = $items;

				if ($items) {
					$tobeCached = array_merge($tobeCached, $items);
				}
			}

			// now we can cache the posts.
			if ($tobeCached) {
				EB::cache()->insert($tobeCached);
			}

			foreach ($results as $row) {

				// Load the author object
				$author = EB::user($row->id);

				// Retrieve blog posts from this user.
				$posts = $bloggerPosts[$row->id];
				$author->blogs = EB::formatter('list', $posts, false);

				// Get tags that are used by this author
				$tmpTags = $bloggerModel->getTagUsed($row->id);
				$tags = array();

				if ($tmpTags) {
					foreach ($tmpTags as $tagRow) {
						$tag = EB::table('Tag');
						$tag->bind($tagRow);

						$tags[]	= $tag;
					}
				}

				$author->tags = $tags;

				// Get categories that are used by this author
				$cats = $bloggerModel->getCategoryUsed($row->id);
				$author->categories = array();

				if ($cats) {
					foreach ($cats as $cat) {
						// $category = EB::table('Category');
						// $category->bind($cat);

						// $catgory->_post_count = $cat->post_count;

						$author->categories[] = $cat;
					}
				}

				// Get the twitter link for this author.
				$author->twitter = EB::socialshare()->getLink('twitter', $row->id);

				// Get total posts created by the author.
				$author->blogCount = $row->totalPost;

				// Get total posts created by the author.
				$author->featured = ($row->featured) ? 1 : 0;

				$author->isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($author->id, $this->my->email);

				// Messaging integrations
				$author->messaging = EB::messaging()->html($author);

				$authors[]	= $author;
			}
		}

		// Format the pagination
		$pagination = $pagination->getPagesLinks();

		$this->set('authors', $authors);
		$this->set('search', $search);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);
		$this->set('sortHTML', $sortHTML);
		$this->set('filterHTML', $filterHTML);

		parent::display('authors/default');
	}

	/**
	 * Displays blog posts created by specific users
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function listings()
	{
		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$id = $this->input->get('id', 0, 'int');

		// Load the author object
		$author = EB::user($id);

		// Disallow all users from being viewed
		if ((!$this->config->get('main_nonblogger_profile') && !EB::isBlogger($author->id)) || !$author->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_INVALID_AUTHOR_ID_PROVIDED'));
		}

		// Get the authors acl
		$acl = EB::acl($author->id);

		// Set meta tags for the author if allowed to
		if ($acl->get('allow_seo')) {
			EB::setMeta($author->id, META_TYPE_BLOGGER, true);
		}

		// Set the breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $author->id) && !EBR::isCurrentActiveMenu('blogger')) {
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , EB::_('index.php?option=com_easyblog&view=blogger') );

			$this->setPathway($author->getName());
		}

		// Get the current active menu
		$active = $this->app->getMenu()->getActive();

		// Excluded categories
		$excludeCats = array();

		if (isset($active->params)) {

			$excludeCats = $active->params->get('exclusion');

			// Ensure that this is an array
			if (!is_array($excludeCats) && $excludeCats) {
				$excludeCats = array($excludeCats);
			}
		}

		// Get the blogs model now to retrieve our blog posts
		$model = EB::model('Blog');

		// Get blog posts
		$posts = $model->getBlogsBy('blogger', $author->id, $sort, 0, '', false, false, '', false, false, false, $excludeCats);
		$pagination	= $model->getPagination();

		// Format the blogs with our standard formatter
		$posts = EB::formatter('list', $posts);

		// Add canonical urls
		$this->canonical('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id);

		// Add authors rss links on the header
		if ($this->config->get('main_rss')) {
			if ($this->config->get('main_feedburner') && $this->config->get('main_feedburnerblogger')) {
				$this->doc->addHeadLink(EB::string()->escape($author->getRssLink()), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
			} else {

				// Add rss feed link
				$this->doc->addHeadLink($author->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
				$this->doc->addHeadLink($author->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
			}
		}

		// Set the title of the page
		$title 	= EB::getPageTitle($author->getName());
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Check if subscribed
		$bloggerModel = EB::model('Blogger');
		$isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($author->id, $this->my->email);

		$return = base64_encode($author->getPermalink());

		// Generate pagination
		$pagination = $pagination->getPagesLinks();

		$this->set('pagination', $pagination);
		$this->set('return', $return);
		$this->set('author', $author);
		$this->set('posts', $posts);
		$this->set('sort', $sort);
		$this->set('isBloggerSubscribed', $isBloggerSubscribed);

		parent::display('authors/item');
	}

	/**
	 * Retrieves the sorting drop down list.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSorting($sorting_type = 'featured')
	{
		$filter[] = JHTML::_('select.option', 'featured', JText::_('COM_EASYBLOG_SORT_BY_FEATURED_BLOGGERS') );
		$filter[] = JHTML::_('select.option', 'latestpost', JText::_('COM_EASYBLOG_SORT_BY_LATEST_POST') );
		$filter[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SORT_BY_LATEST_BLOGGER') );
		$filter[] = JHTML::_('select.option', 'alphabet', JText::_('COM_EASYBLOG_SORT_BY_ALPHABETICAL') );

		return JHTML::_('select.genericlist', $filter, 'sort', 'size="1" class="form-control"', 'value', 'text', $sorting_type );
	}

	/**
	 * Retrieves the filter dropdown list
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilter($filter_type = 'showbloggerwithpost')
	{
		$filter[] = JHTML::_('select.option', 'showbloggerwithpost', JText::_('COM_EASYBLOG_FILTERS_BLOGGER_WITH_POST') );
		$filter[] = JHTML::_('select.option', 'showallblogger', JText::_('COM_EASYBLOG_FILTERS_ALL_BLOGGERS') );

		return JHTML::_('select.genericlist', $filter, 'filter', 'size="1"', 'value', 'text', $filter_type );
	}
}
