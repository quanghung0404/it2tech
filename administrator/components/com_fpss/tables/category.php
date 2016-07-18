<?php
/**
 * @version		$Id: category.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSCategory extends JTable
{

	var $id = null;
	var $asset_id = null;
	var $name = null;
	var $published = null;
	var $ordering = null;
	var $language = null;
	var $params = null;

	function __construct(&$db)
	{
		parent::__construct('#__fpss_categories', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_fpss.category.'.(int)$this->$k;
	}

	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_fpss');
		return $asset->id;
	}

	function check()
	{
		if (JString::trim($this->name) == '')
		{
			$this->setError(JText::_('FPSS_CATEGORY_MUST_HAVE_A_NAME'));
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
		$query = "DELETE FROM #__fpss_categories WHERE id IN(".implode(',', $id).")";
		$this->_db->setQuery($query);
		if ($this->_db->query())
			return true;
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

	}

	function truncate($id)
	{

		JArrayHelper::toInteger($id);
		$query = "DELETE FROM #__fpss_slides WHERE catid IN(".implode(',', $id).")";
		$this->_db->setQuery($query);
		if ($this->_db->query())
			return true;
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

	}

}
