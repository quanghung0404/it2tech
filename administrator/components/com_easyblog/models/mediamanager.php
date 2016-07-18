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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelMediaManager extends EasyBlogAdminModel
{
	private $data = null;
	private $pagination = null;
	private $total = null;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves a list of articles on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPosts($userId = null)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);
		$query[] = 'and ' . $db->quoteName('state') . '!=' . $db->Quote(EASYBLOG_POST_NORMAL);




		// If user is a site admin, we want to show everything
		if (!EB::isSiteAdmin()) {
			$user = JFactory::getUser($userId);
			$query[] = 'AND ' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}
}
