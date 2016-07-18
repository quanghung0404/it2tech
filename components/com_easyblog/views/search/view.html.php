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

class EasyBlogViewSearch extends EasyBlogView
{
	/**
	 * Default search view for EasyBlog
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Set the meta tags for search
		EB::setMeta(META_ID_SEARCH, META_TYPE_SEARCH);

		// Set the page title
		$title 	= EasyBlogHelper::getPageTitle(JText::_('COM_EASYBLOG_SEARCH_PAGE_TITLE'));
		$this->setPageTitle($title);

		// Set the view's breadcrumbs
		$this->setViewBreadcrumb('search');

		// Get any existing query
		$query = $this->input->get('query', '', 'default');
		$query = trim($query);
		$Itemid	= $this->input->get('Itemid', '', 'int');

		$posts = array();
		$pagination = '';

		if (!empty($query)) {

			// Get the model
			$model 	= EB::model('Search');
			$result	= $model->getData();
			$total	= count($result);

			if ($total > 0) {

				$searchworda	= preg_replace('#\xE3\x80\x80#s', ' ', $query);
				$searchwords	= preg_split("/\s+/u", $searchworda);
				$needle			= $searchwords[0];
				$searchwords	= array_unique($searchwords);

				// var_dump($result[0]);

				// Format the post
				$posts = EB::formatter('list', $result);

				// var_dump($posts[0]);
				// exit;

				// Remove all unecessary codes from the output
				foreach ($posts as &$row) {

					// var_dump($row->content);

					// Strip videos
					$row->intro = EB::videos()->strip($row->intro);
					$row->content = EB::videos()->strip($row->content);

					// strip gallery
					$row->intro = EB::gallery()->strip($row->intro);
					$row->content = EB::gallery()->strip($row->content);

					// strip jomsocial album
					$row->intro = EB::album()->strip($row->intro);
					$row->content = EB::album()->strip($row->content);

					// strip audio
					$row->intro = EB::audio()->strip($row->intro);
					$row->content = EB::audio()->strip($row->content);

					// Format the content so that we can apply our search highlighting
					$content = preg_replace('/\s+/', ' ', strip_tags($row->content));

					if (empty($content)) {
						$content = preg_replace('/\s+/', ' ', strip_tags($row->intro));
					}

					// We only want a snippet of the content
					$content = JString::substr(strip_tags($content), 0, 350);
					$pattern = '#(';

					$x 	= 0;

					foreach ($searchwords as $key => $value) {
						$pattern 	.= $x == 0 ? '' : '|';
						$pattern	.= preg_quote($value, '#' );
						$x++;
					}

					$pattern 		.= ')#iu';

					$row->title 	= preg_replace($pattern, '<span class="search-highlight">\0</span>', $row->title);
					$row->content 	= preg_replace($pattern, '<span class="search-highlight">\0</span>', $content);
				}
			}


			$pagination	= $model->getPagination();
		}


		$this->set('query', $query);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('Itemid', $Itemid);

		parent::display('search/default');
	}

	public function parseQuery()
	{
		$mainframe	= JFactory::getApplication();
		$query		= JRequest::getVar('query', '');
		$query		= rtrim( $query , '.' );
		$mainframe->redirect(EBR::_( 'index.php?option=com_easyblog&view=search&query='.$query, false ));
		$mainframe->close();
	}
}
