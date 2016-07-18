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

class EasyBlogViewTeamBlog extends EasyBlogView
{
	function display( $tmpl = null )
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$id			= $this->input->get('id', '', 'cmd');
		$team		= EB::table('TeamBlog');
		$team->load( $id );

		// if ($team->access != EBLOG_TEAMBLOG_ACCESS_EVERYONE ) {
		// 	return;
		// }

		$sort		= JRequest::getCmd('sort', $this->config->get( 'layout_postorder' ) );
		$model		= EB::model('Blog');
		$data		= $model->getBlogsBy('teamblog', $id, $sort);

		// $model = EB::model('TeamBlogs');
		// $posts = $model->getPosts($team->id);
		// $posts = EB::formatter('list', $posts);

		$document	= JFactory::getDocument();
		$document->link	= EBR::_('index.php?option=com_easyblog&view=latest');

		$document->setTitle( JText::sprintf( 'COM_EASYBLOG_FEEDS_TEAMBLOGS_TITLE' , $team->title ) );
		$document->setDescription( JText::sprintf( 'COM_EASYBLOG_FEEDS_TEAMBLOGS_DESC' , $team->title ) );

		if (empty($data)) {
			return;
		}

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		$data = EB::formatter('list', $data);

		foreach ($data as $post) {

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
			$item->authorEmail = $this->getRssEmail($profile);

			$this->doc->addItem($item);
		}

	}
}
