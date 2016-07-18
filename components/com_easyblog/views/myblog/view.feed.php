<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewMyBlog extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Get the default sorting behavior
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Load up the author profile
		$author = EB::user($this->my->id);

		// Get the blogs model
		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('blogger', $author->id, $sort);

		$this->doc->link = EB::_('index.php?option=com_easyblog&view=myblog');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_MYBLOG_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_MYBLOG_DESC', $author->user->name));

		// If there's no data, skip this altogether
		if (!$posts) {
			return;
		}

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		$posts = EB::formatter('list', $posts);

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

		return;
	}
}
