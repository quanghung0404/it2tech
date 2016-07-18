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

class EasyBlogViewFeatured extends EasyBlogView
{
	function display( $tmpl = null )
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$model		= EB::model('Blog');
		$data		= $model->getFeaturedBlog();

		$document	= JFactory::getDocument();
		$document->link	= EBR::_('index.php?option=com_easyblog&view=featured');

		$document->setTitle( JText::_( 'COM_EASYBLOG_FEEDS_FEATURED_TITLE' ) );
		$document->setDescription( JText::sprintf( 'COM_EASYBLOG_FEEDS_FEATURED_DESC' , JURI::root() ) );

		if (empty($data)) {
			return;
		}

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		foreach ($data as $row) {

			$blog 	= EB::table('Blog');
			$blog->load( $row->id );

			$profile = EB::user($row->created_by);

			$created			= EB::date($row->created);
			$row->created		= $created->toSql();

			if ($this->config->get('main_rss_content') == 'introtext') {
				$row->text		= ( !empty( $row->intro ) ) ? $row->intro : $row->content;
				//read more for feed
				$row->text 		.= '<br /><a href=' . EBR::_('index.php?option=com_easyblog&view=entry&id=' . $row->id ).'>Read more</a>';
			} else {
				$row->text		= $row->intro . $row->content;
			}

			$row->text			= EB::videos()->strip($row->text);
			$row->text			= EB::adsense()->stripAdsenseCode($row->text);

			$post = EB::post($row->id);
			$category = EB::table('Category');

			// Get primary category
			$primaryCategory = $post->getPrimaryCategory();
			$category->load($primaryCategory->id);

			// Assign to feed item
			$title	= $this->escape($row->title);
			$title	= html_entity_decode($title);

			// load individual item creator class
			$item				= new JFeedItem();
			$item->title		= $title;
			$item->link			= EBR::_('index.php?option=com_easyblog&view=entry&id=' . $row->id );
			$item->description	= $row->text;

			// replace the image source to proper format so that feed reader can view the image correctly.
			$item->description = str_replace('src="//', 'src="' . $scheme . '//', $item->description);
			$item->description = str_replace('href="//', 'href="' . $scheme . '//', $item->description);			

			$item->date			= $row->created;
			$item->category		= $category->getTitle();
			$item->author		= $profile->getName();
			$item->authorEmail 	= $this->getRssEmail($profile);

			$document->addItem( $item );
		}

	}
}
