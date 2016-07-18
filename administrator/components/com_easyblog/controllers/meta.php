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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerMeta extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
		$this->registerTask('addIndexing', 'saveIndexing');
		$this->registerTask('removeIndexing', 'saveIndexing');
		$this->registerTask('delete', 'delete');
	}

	/**
	 * Saves a new meta object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('meta');

		// Default return url
		$return = JRoute::_('index.php?option=com_easyblog&view=metas' , false);

		$post = $this->input->getArray('post');

		if (!isset($post['id']) || empty($post['id'])) {
			$this->info->set('COM_EASYBLOG_INVALID_META_TAG_ID', 'error');

			return $this->app->redirect($return);
		}

		$meta = EB::table('Meta');
		$meta->load((int) $post['id']);

		$meta->bind($post);

		// Save the meta object
		$meta->store();

		$task = $this->getTask();

		if ($task == 'apply') {
			$return = 'index.php?option=com_easyblog&view=metas&layout=form&id=' . $meta->id;
		}

		$this->info->set('COM_EASYBLOG_META_SAVED', 'success');

		return $this->app->redirect($return);
	}

	public function saveIndexing()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'meta' );

		$app 		= JFactory::getApplication();
		$task 		= $this->getTask();
		$cid 		= JRequest::getVar( 'cid' );

		$meta 		= EB::table('Meta');
		$meta->load( $cid[ 0 ] );

		if( empty( $cid ) || !$meta->id )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=metas' , JText::_( 'COM_EASYBLOG_INVALID_ID_PROVIDED') , 'error' );
			$app->close();
		}

		$meta->indexing 	= $task == 'addIndexing' ? 1 : 0;
		$meta->store();

		$message 			= $task == 'addIndexing' ? JText::_( 'COM_EASYBLOG_META_ENABLED_INDEXING' ) : JText::_( 'COM_EASYBLOG_META_DISABLED_INDEXING' );

		$app->redirect( 'index.php?option=com_easyblog&view=metas' , $message );
	}

	/**
	 * Deletes metas from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('meta');

		// Get the list of metas to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('Invalid meta id'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=metas');
		}

		// Do whatever you need to do here
		foreach ($ids as $id) {

			$meta = EB::table('Meta');
			$meta->load((int) $id);

			// Delete the tag
			$meta->delete();
		}

		$this->info->set('COM_EASYBLOG_METAS_META_REMOVED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=metas');
	}
}
