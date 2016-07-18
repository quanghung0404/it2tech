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
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewCategories extends EasyBlogView
{
	/**
	 * Proxy to the default layout
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listings()
	{
		return $this->display();
	}

	/**
	 * Default feed display method
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_FEEDS_DISABLED'));
		}

		$id = $this->input->get('id', '', 'cmd');
	    $category = EB::table('Category');
	    $category->load($id);

	    // Private category shouldn't allow to access.
		$privacy = $category->checkPrivacy();

	    if (!$privacy->allowed) {
	        return JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'));
	    }

		// Get the nested categories
		$category->childs = null;

		EB::buildNestedCategories($category->id, $category);

		$linkage = '';
		EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

		$catIds = array();
		$catIds[] = $category->id;
		EB::accessNestedCategoriesId($category, $catIds);

		$category->nestedLink    = $linkage;

		$model = EB::model('Blog');
        $sort = $this->input->get('sort', $this->config->get( 'layout_postorder' ), 'cmd');

		$posts = $model->getBlogsBy('category', $catIds, $sort);
		$posts = EB::formatter('list', $posts);

		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=categories&id=' . $id . '&layout=listings');
		$this->doc->setTitle($this->escape($category->getTitle()));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_CATEGORY_DESC', $this->escape($category->getTitle())));

		if (!$posts) {
			return;
		}

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		foreach ($posts as $post) {

			$image = '';

			if ($post->hasImage()) {
				$image = '<img src="' . $post->getImage('medium', true, true) . '" width="250" align="left" />';
			}

			$item = new JFeedItem();
			$item->title = $this->escape($post->title);
			$item->link = $post->getPermalink();
			$item->description = $image . $post->getIntro();

			// replace the image source to proper format so that feed reader can view the image correctly.
			$item->description = str_replace('src="//', 'src="' . $scheme . '//', $item->description);
			$item->description = str_replace('href="//', 'href="' . $scheme . '//', $item->description);			

			$item->date = $post->getCreationDate()->toSql();
			$item->category = $post->getPrimaryCategory()->getTitle();
			$item->author = $post->author->getName();
			$item->authorEmail = $this->getRssEmail($post->author);

			$this->doc->addItem($item);
		}

		// exit;
	}
}
