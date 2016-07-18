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

class EasyBlogViewBlogger extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Check if rss is enabled
		if (!$this->config->get('main_rss')) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_FEEDS_DISABLED'));
		}

		// Check if the author's id is provided
		$id = $this->input->get('id', '', 'cmd');

		if (!$id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_FEEDS_INVALID_AUTHOR_ID'));
		}

		$author = EB::user($id);

		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('blogger', $author->id);
		$posts = EB::formatter('list', $posts);

		$this->doc->link = $author->getPermalink();
		$this->doc->setTitle(JText::sprintf('COM_EASYBLOG_FEEDS_BLOGGER_TITLE' , $author->getName()));
		$this->doc->setDescription(strip_tags($author->description));

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
			$item->authorEmail = $this->getRssEmail($author);

			$this->doc->addItem($item);
		}

	}
}
