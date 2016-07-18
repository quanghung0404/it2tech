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

class EasyBlogViewTags extends EasyBlogView
{
	/**
	 * Displays all tags on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set meta tags for tags view
		EB::setMeta(META_ID_TAGS, META_TYPE_VIEW);

		// Set breadcrumb
		$this->setViewBreadcrumb('tags');

		// Set page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_TAGS_PAGE_TITLE'));
		$this->setPageTitle($title, '', $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
		$this->canonical('index.php?option=com_easyblog&view=tags', array('ordering', 'sorting'));

		// Retrieve search values
		$search = $this->input->get('search', '', 'string');

		// Get the model
		$model = EB::model('Tags');

		// Get other sorting and filters
		$ordering = JString::strtolower($this->input->get('ordering', '', 'string'));
		$sorting = JString::strtolower($this->input->get('sorting', $this->config->get('main_tags_sorting'), 'string'));

		// Get the tags
		$result = $model->getTagCloud('', $ordering, $sorting, true, $search);

		// Format the tags
		$tags = array();

		if ($result) {
			foreach ($result as $row) {
				$tag = EB::table('Tag');
				$tag->bind($row);

				$tag->post_count = $row->post_count;
				$tags[] = $tag;
			}
		}

		$titleURL	= 'index.php?option=com_easyblog&view=tags&ordering=title';
		$titleURL	.= ( $sorting ) ? '&sorting=' . $sorting : '';
		$postURL	= 'index.php?option=com_easyblog&view=tags&ordering=postcount';
		$postURL	.= ( $sorting ) ? '&sorting=' . $sorting : '';

		$this->set('titleURL', $titleURL);
		$this->set('postURL', $postURL);
		$this->set('tags', $tags);
		$this->set('sorting', $sorting);
		$this->set('ordering', $ordering);

		parent::display('tags/default');
	}

	/**
	 * Displays blog listings by specific tags on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tag()
	{
		// Get the tag id
		$id = $this->input->get('id', '', 'default');

		// Add noindex for tags listing by default
		$this->doc->setMetadata('robots', 'noindex,follow');

		// Load the tag object
		$tag = EB::table('Tag');
		$tag->load($id);

		// The tag could be a permalink
		if (!$tag->id) {
			$tag->load($id, true);
		}

		// Set page title
		$this->setPageTitle($tag->getTitle(), '' , $this->config->get('main_pagetitle_autoappend'));

		// set meta tags for tags view
		EB::setMeta(META_ID_TAGS, META_TYPE_VIEW, $tag->getTitle() . ' - ' . EB::getPageTitle($this->config->get('main_title')) );

		// Set breadcrumb
		if (!EBR::isCurrentActiveMenu('tags')) {
			$this->setPathway(JText::_('COM_EASYBLOG_TAGS_BREADCRUMB'), EBR::_('index.php?option=com_easyblog&view=tags'));
		}
		$this->setPathway($tag->getTitle());

		// Get the blogs model
		$blogModel = EB::model('Blog');
		$tagModel = EB::model('Tags');

		// Get the blog posts now
		$rows = $blogModel->getTaggedBlogs($tag->id, false, '');

		// Get the pagination
		$pagination	= $blogModel->getPagination();

		if (is_object($pagination) && method_exists($pagination, 'setAdditionalUrlParam')) {
			$pagination->setAdditionalUrlParam('id', $tag->alias);
		}

		// Get total number of private blog posts
		$privateCount = 0;

		// Get total number of team blog count
		$teamblogCount = 0;

		if ($this->my->guest) {
			$privateCount = $tagModel->getTagPrivateBlogCount($id);
		}

		// Determines if we should get the team blog count
		if (!$this->config->get('main_includeteamblogpost')) {
			$teamblogCount = $tagModel->getTeamBlogCount($id);
		}

		// Format the blog posts using the standard list formatter
		$posts = EB::formatter('list', $rows);

		$return = base64_encode($tag->getPermalink());
		
		$this->set('return', $return);
		$this->set('tag', $tag);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('private', $privateCount);
		$this->set('team', $teamblogCount);

		parent::display('tags/item');
	}
}
