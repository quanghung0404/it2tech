<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerFeeds extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask( 'publish' , 'publish' );
		$this->registerTask( 'unpublish' , 'unpublish' );
	}

	public function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		return $this->app->redirect( 'index.php?option=com_easyblog&view=feeds' );

	}

	public function add()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
	}

	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$feeds	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		if (count($feeds) <= 0) {

			$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

		} else {

			for ($i = 0; $i < count($feeds); $i++) {
				$id     = $feeds[$i];

				$feed	= EB::table('Feed');
				$feed->load($id);

				if (!$feed->delete()) {
					$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_DELETE', 'error');

					return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

				}
			}

		}

		$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_DELETE_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

	}

	/**
	 * Stores a new rss feed import
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$post = JRequest::get('post');
		$id = $this->input->get('id', 0, 'int');

		$feed = EB::table('Feed');
		$feed->load($id);
		$feed->bind($post);

		if (!$feed->item_creator) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_AUTHOR', 'error');

			$session 	= JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->item_category) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_CATEGORY', 'error');

			$session 	= JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->url) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_URL', 'error');

			$session 	= JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->title) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_TITLE', 'error');

			$session 	= JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		// Store the allowed tags here.
		$allowed 		= JRequest::getVar('item_allowed_tags' , '' , 'REQUEST' , 'none' , JREQUEST_ALLOWRAW);
		$copyrights		= JRequest::getVar( 'copyrights' , '' );
		$sourceLinks	= JRequest::getVar( 'sourceLinks' , '0' );
		$feedamount		= JRequest::getVar( 'feedamount' , '0' );
		$autopost 		= JRequest::getVar( 'autopost' , 0 );

		$params = EB::getRegistry();
		$params->set('allowed', $allowed);
		$params->set('copyrights', $copyrights);
		$params->set('sourceLinks', $sourceLinks);
		$params->set('autopost', $autopost);
		$params->set('feedamount', $feedamount);
		$params->set('item_get_fulltext', $this->input->get('item_get_fulltext', '', 'default'));
		$params->set('notify', $this->input->get('notify', '', 'default'));
		$feed->params	= $params->toString();

		$state 	= $feed->store();

		if (!$state) {
			EB::info()->set($feed->getError(), 'error');

			$session 	= JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_SAVE_SUCCESS', 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form&id=' . $feed->id);
		}

		if ($task == 'save') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
		}

		if ($task == 'savenew') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}
	}

	public function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$feeds	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if (count($feeds) <= 0) {
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID');
			$type		= 'error';

		} else {

			$model		= EB::model( 'Feeds' );

			if ($model->publish($feeds, 1)) {
				$message	= JText::_('Feed(s) published');
			} else {
				$message	= JText::_('Error publishing feed');
				$type		= 'error';
			}

		}

		$this->info->set($message, $type);

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$feeds		= JRequest::getVar('cid', array(0), 'POST');

		$message	= '';
		$type		= 'message';

		if (count($feeds) <= 0) {
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID');
			$type		= 'error';
		} else {
			$model		= EB::model( 'Feeds' );

			if ($model->publish($feeds, 0)) {
				$message	= JText::_('Feed(s) unpublished');
			} else {
				$message	= JText::_('Error unpublishing feed');
				$type		= 'error';
			}
		}

		$this->info->set($message, $type);

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

	}
}
