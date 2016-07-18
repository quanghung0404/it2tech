<?php
/**
 * @version		$Id: users.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSModelUsers extends FPSSModel
{

	function getData()
	{
		$db = $this->getDBO();
		$query = "SELECT a.*, g.name AS groupname FROM #__users AS a 
		INNER JOIN #__core_acl_aro AS aro ON aro.VALUE = a.id 
		INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id 
		INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id";
		$conditions = array();
		if ($this->getState('group'))
		{
			$conditions[] = "a.gid = ".(int)$this->getState('group');
		}
		if ($this->getState('search'))
		{
			$conditions[] = "LOWER(a.name) LIKE ".$db->Quote("%".$db->getEscaped($this->getState('search'), true)."%", false);
		}
		if (count($conditions))
		{
			$query .= " WHERE ".implode(' AND ', $conditions);
		}
		$query .= " GROUP BY a.id ORDER BY ".$this->getState('ordering')." ".$this->getState('orderingDir');
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$rows = $db->loadObjectList();
		return $rows;
	}

	function getTotal()
	{
		$db = $this->getDBO();
		$query = "SELECT COUNT(a.id) FROM #__users AS a";
		$conditions = array();
		if ($this->getState('group'))
		{
			$conditions[] = "a.gid = ".(int)$this->getState('group');
		}
		if ($this->getState('search'))
		{
			$conditions[] = "LOWER(a.name) LIKE ".$db->Quote("%".$db->getEscaped($this->getState('search'), true)."%", false);
		}
		if (count($conditions))
		{
			$query .= " WHERE ".implode(' AND ', $conditions);
		}
		$db->setQuery($query);
		$total = $db->loadresult();
		return $total;
	}

	function getUserGroups()
	{
		$db = $this->getDBO();
		$query = "SELECT id AS value, name AS text FROM #__core_acl_aro_groups 
		WHERE name != 'ROOT' AND name != 'USERS'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

}
