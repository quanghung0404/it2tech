<?php
/**
 * @version		$Id: slide.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSSlide extends JTable
{

	var $id = null;
	var $asset_id = null;
	var $title = null;
	var $catid = null;
	var $published = null;
	var $publish_up = null;
	var $publish_down = null;
	var $created = null;
	var $created_by = null;
	var $modified = null;
	var $modified_by = null;
	var $access = null;
	var $ordering = null;
	var $featured = null;
	var $featured_ordering = null;
	var $text = null;
	var $tagline = null;
	var $referenceType = null;
	var $referenceID = null;
	var $custom = null;
	var $video = null;
	var $hits = null;
	var $language = null;
	var $params = null;

	function __construct(&$db)
	{
		parent::__construct('#__fpss_slides', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_fpss.slide.'.(int)$this->$k;
	}

	protected function _getAssetParentId($table = null, $id = null)
	{
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('asset_id'));
		$query->from($this->_db->quoteName('#__fpss_categories'));
		$query->where($this->_db->quoteName('id').' = '.(int)$this->catid);
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		return (int)$result;
	}

	function check()
	{
		if (JString::trim($this->title) == '')
		{
			$this->setError(JText::_('FPSS_SLIDE_MUST_HAVE_A_TITLE'));
			return false;
		}
		return true;
	}

	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = array();
			foreach ((array) $array['rules'] as $action => $ids)
			{
				$rules[$action] = array();
				foreach ($ids as $id => $p)
				{
					if ($p !== '')
					{
						$rules[$action][$id] = ($p == '1' || $p == 'true') ? true : false;
					}
				}
			}
			if (class_exists('JRules'))
			{
				$this->setRules(new JRules($rules));
			}
			elseif (class_exists('JAccessRules'))
			{
				$this->setRules(new JAccessRules($rules));
			}
		}
		return parent::bind($array, $ignore);
	}

	function delete($id = null)
	{

		JArrayHelper::toInteger($id);
		$query = "DELETE FROM #__fpss_slides WHERE id IN(".implode(',', $id).")";
		$this->_db->setQuery($query);
		if ($this->_db->query())
			return true;
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

	}

	function getNextOrder($where = '', $column = 'ordering')
	{

		$query = "SELECT MAX({$column}) FROM #__fpss_slides";
		$query .= ($where ? " WHERE ".$where : "");
		$this->_db->setQuery($query);
		$maxord = $this->_db->loadResult();
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $maxord + 1;
	}

	function reorder($where = '', $column = 'ordering')
	{

		$k = $this->_tbl_key;
		$query = "SELECT {$this->_tbl_key}, {$column} FROM #__fpss_slides WHERE {$column}>0";
		$query .= ($where ? " AND ".$where : "");
		$query .= " ORDER BY {$column}";

		$this->_db->setQuery($query);
		if (!($orders = $this->_db->loadObjectList()))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		for ($i = 0, $n = count($orders); $i < $n; $i++)
		{
			if ($orders[$i]->$column >= 0)
			{
				if ($orders[$i]->$column != $i + 1)
				{
					$orders[$i]->$column = $i + 1;
					$query = "UPDATE #__fpss_slides SET {$column}=".(int)$orders[$i]->$column;
					$query .= ' WHERE '.$k.' = '.$this->_db->Quote($orders[$i]->$k);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}

		return true;
	}

	function move($dirn, $where = '', $column = 'ordering')
	{

		$k = $this->_tbl_key;

		$sql = "SELECT $this->_tbl_key, {$column} FROM $this->_tbl";

		if ($dirn < 0)
		{
			$sql .= ' WHERE '.$column.' < '.(int)$this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column.' DESC';
		}
		else
		if ($dirn > 0)
		{
			$sql .= ' WHERE '.$column.' > '.(int)$this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column;
		}
		else
		{
			$sql .= ' WHERE '.$column.' = '.(int)$this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column;
		}

		$this->_db->setQuery($sql, 0, 1);

		$row = null;
		$row = $this->_db->loadObject();

		if (isset($row))
		{
			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int)$row->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}

			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int)$this->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($row->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}
			$this->$column = $row->$column;
		}
		else
		{
			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int)$this->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}
		}
		return true;
	}

}
