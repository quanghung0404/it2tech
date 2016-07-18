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

class EasyBlogViewArchive extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Checks if rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Get the archives model
		$model = EB::model('Archive');

		// Get a list of posts
		$posts = $model->getPosts();

		// Format the posts
		$posts = EB::formatter('list', $posts);

		// Set the link for this feed
		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=archive');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_ARCHIVED_POSTS'));
		$this->doc->setDescription(JText::_('COM_EASYBLOG_ARCHIVED_POSTS_DESC'));

		if (!$posts) {
			return;
		}

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		foreach ($posts as $post) {

			$image = '';
			if ($post->hasImage()) {
				$image = '<img src=' . $post->getImage('medium', true, true) . '" alt="' . $post->title . '" />';
			}

			$item = new JFeedItem();
			$item->title = $post->title;
			$item->link = $post->getPermalink();
			$item->description = $image . $post->getIntro();

			// replace the image source to proper format so that feed reader can view the image correctly.
			$item->description = str_replace('src="//', 'src="' . $scheme . '//', $item->description);
			$item->description = str_replace('href="//', 'href="' . $scheme . '//', $item->description);

			$item->date = $post->getCreationDate()->format();
			$item->category = $post->getPrimaryCategory()->getTitle();
			$item->author = $post->author->getName();
			$item->authorEmail = $this->getRssEmail($post->author);

			$this->doc->addItem($item);
		}
	}
}
