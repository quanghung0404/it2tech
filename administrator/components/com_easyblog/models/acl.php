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

class EasyBlogModelAcl extends EasyBlogAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getTotal()
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
	function getPagination()
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
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getJoomlaGroupRulesets()
	{
		$sql = 'SELECT * FROM ';

		return $this->_data;
	}

	function getRules($key='')
	{
		$db = EasyBlogHelper::db();
		$sql = 'SELECT * FROM '.$db->nameQuote('#__easyblog_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}

	public function deleteRuleset($cid)
	{
		if (empty($cid)) {
			return false;
		}

		$db = EasyBlogHelper::db();

		$sql = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) . ' AND `type` = ' . $db->quote('group');
		$db->setQuery($sql);
		$result = $db->query();

		return $result;
	}

	function insertRuleset($cid, $saveData)
	{
		$db = EasyBlogHelper::db();

		$rules = $this->getRules('action');

		$type = 'group';

		$newruleset = array();

		foreach($rules as $rule)
		{
			$action = $rule->action;
			$str = "(".$db->quote($cid).", ".$db->quote($rule->id).", ".$db->quote($saveData[$action]).", ".$db->quote($type).")";
			array_push($newruleset, $str);
		}

		if(!empty($newruleset))
		{
			$sql = 'INSERT INTO ' . $db->nameQuote('#__easyblog_acl_group') . ' (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$sql .= implode(',', $newruleset);
			$db->setQuery($sql);

			return $result = $db->query();
		}

		return true;
	}

	/**
	 * Retrieve a list of acl groups
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		$db 	= EB::db();

		$query 	= 'SELECT DISTINCT(' . $db->nameQuote('group') . ') FROM ' . $db->nameQuote('#__easyblog_acl');
		$db->setQuery($query);

		$groups	= $db->loadColumn();

		return $groups;
	}

	/**
	 * Retrieve a list of rules on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getInstalledRules($cid, $add = false)
	{
		$db					= EB::db();
		$ruleset			= new stdClass();
		$ruleset->rules		= array();

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__easyblog_acl');
		$db->setQuery($query);
		$rules	= $db->loadObjectList();

		// Need to group up the rules accordingly.
		foreach ($rules as &$rule) {
			$ruleset->rules[$rule->group][$rule->action]	= (int) $rule->default;
		}

		if (!$add) {

			$query 	= $this->buildQuery($cid);

			$db->setQuery($query);

			$row	= $db->loadObject();

			// Add identifier for this rule set
			$ruleset->id 	= $row->id;
			$ruleset->name	= $row->name;
			$ruleset->level	= 0;

			// Get the stored acl group ruleset
			$query 	= 'SELECT a.*, b.' . $db->nameQuote('group') . ', b.' . $db->nameQuote('action') . ' FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote('#__easyblog_acl') . ' AS b '
					. 'ON a.' . $db->nameQuote('acl_id') . ' = b.' . $db->nameQuote('id') . ' '
					. 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($cid) . ' '
					. 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');
			$db->setQuery($query);

			$row = $db->loadObjectList();

			if(count($row) > 0) {

				foreach ($row as $data) {

					if (isset($ruleset->rules[$data->group][$data->action])) {

						$ruleset->rules[$data->group][$data->action]	= $data->status;
					}
				}
			}
		}

		return $ruleset;
	}

	function getRuleSet($cid, $add=false)
	{
		$db = EasyBlogHelper::db();



		return $rulesets;
	}

	function getRuleSets($cid='')
	{
		$db 		= EasyBlogHelper::db();

		$rulesets	= new stdClass();
		$ids		= array();

		$rules = $this->getRules('id');

		$type = 'group';

		//get user
		$query = $this->_buildQuery($cid);

		$pagination = $this->getPagination();
		$rows = $this->_getList($query, $pagination->limitstart, $pagination->limit );

		if(!empty($rows))
		{
			foreach($rows as $row)
			{
				$rulesets->{$row->id} 			= new stdClass();
				$rulesets->{$row->id}->id		= $row->id;
				$rulesets->{$row->id}->name		= $row->name;
				$rulesets->{$row->id}->level	= $row->level;

				foreach($rules as $rule)
				{
					$rulesets->{$row->id}->{$rule->action} = (INT)$rule->default;
				}

				array_push($ids, $row->id);
			}

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' WHERE '. $db->nameQuote('type') . ' = ' . $db->Quote('group') . ' AND `content_id` IN (' . implode( ' , ', $ids ) . ')';
			$db->setQuery($sql);
			$acl = $db->loadAssocList();

			foreach($acl as $data)
			{
				if(isset($rules[$data['acl_id']]))
				{
					$action = $rules[$data['acl_id']]->action;
					$rulesets->{$data['content_id']}->{$action} = $data['status'];
				}
			}
		}

		return $rulesets;
	}

	public function buildQuery($cid = '')
	{
		$db 	= EB::db();

		$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
		$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
		$query .= ' FROM #__usergroups AS a';
		$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';

		$where		= $this->_buildQueryWhere($cid);
		$orderby	= $this->_buildQueryOrderBy();

		$query .= $where . ' ' . $orderby;

		return $query;
	}

	function _buildQueryWhere($cid='')
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if(empty($cid) && $search)
		{
			$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
		}
		else
		{
			$where[] = 'a.`id` = ' . $db->quote($cid);
		}

		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order 		= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$orderby	 = ' GROUP BY a.id';
		$orderby	.= ' ORDER BY a.lft ASC';

		return $orderby;
	}
}
