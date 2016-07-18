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

class EasyBlogModelComments extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;

	/**
	 * Category data array
	 *
	 * @public array
	 */
	public $_data = null;

	public function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_easyblog.comments.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		//$limitstart = $mainframe->getUserStateFromRequest( 'com_easyblog.limitstart', 'limitstart', 0, 'int' );
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();

		$query	= 'SELECT a.*, b.`title` AS `blog_name`'
				. ' FROM `#__easyblog_comment` AS a LEFT JOIN `#__easyblog_post` AS b'
				. ' ON a.`post_id` = b.`id`'
				. $where . ' '
				. $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.comments.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.comments.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '0' );
			}
			else if ($filter_state == 'M' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '2' );
			}
		}


		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.comments.filter_order', 		'filter_order', 	'created DESC', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.comments.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		if ( !empty($filter_order) OR !empty($filter_order_Dir) ) {
			$orderby .= ', created DESC, ordering';
		}

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish( &$pks , $publish = 1 )
	{
		if( count( $pks ) > 0 )
		{
			$db		= EasyBlogHelper::db();

			$tags	= implode( ',' , $pks );

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_comment' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Get the total number of comments that are awaiting moderation
	 **/
	public function getTotalPending()
	{
		$db 	= EasyBlogHelper::db();
		$query 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_comment' );
		$query	.= ' WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( EBLOG_COMMENT_MODERATE );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve a list of top commenters for author's posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTopCommentersForAuthorsPost($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();

		$query[] = 'SELECT a.' . $db->quoteName('created_by') . ', COUNT(a.' . $db->quoteName('id') . ') AS ' . $db->quoteName('total') . ' FROM ' . $db->quoteName('#__easyblog_comment') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('post_id') . ' = b.' . $db->quoteName('id');
		$query[] = 'WHERE b.' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('created_by') . '!=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('created_by') . '!=' . $db->Quote(0);
		$query[] = 'AND a.' . $db->quoteName('published') . '=' . $db->Quote(1);

		$query[] = 'AND b.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query[] = 'GROUP BY a.' . $db->quoteName('created_by');
		$query[] = 'ORDER BY ' . $db->quoteName('total') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);

		// echo str_ireplace('#__', 'jos_', $query);
		// exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		//preload users
		$ids = array();
		foreach ($result as $item) {
			$ids[] = $item->created_by;
		}

		EB::user($ids);

		foreach ($result as &$row) {
			$row->author = EB::user($row->created_by);
		}

		return $result;
	}

	/**
	 * Get a list of recent comments posted on the author's post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRecentCommentsOnAuthor($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();

		$query[] = 'SELECT b.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_comment') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');
		$query[] = 'WHERE a.' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'AND b.' . $db->quoteName('published') . '=' . $db->Quote(1);
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$comments = array();

		foreach ($result as $row) {

			$comment = EB::table('Comment');
			$comment->bind($row);

			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Delete comments from particular post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deletePostComments($postId)
	{
		$db = EB::db();
		$config	= EB::getConfig();
		
		// if komento exist and check the integration option
		$komentoEngine = JPATH_ROOT . '/components/com_komento/helpers/helper.php';

		if (JFile::exists($komentoEngine) && $config->get('comment_komento') == true) {

			require_once($komentoEngine);
			$model = Komento::getModel('comments');

			// delete comment based on the article id
			$model->deleteArticleComments('com_easyblog', $postId);
		}

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_comment');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}
}
