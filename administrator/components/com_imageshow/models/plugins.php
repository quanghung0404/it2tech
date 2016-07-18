<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: plugins.php 16077 2012-09-17 02:30:25Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class ImageShowModelPlugins extends JModelLegacy
{

	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
		global $mainframe, $option;

		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.limitstart', 'limitstart', 0, 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getData()
	{
		$db	= JFactory::getDBO();

		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$items = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->translate($items);
			$this->_data = $items;
		}
		return $this->_data;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = @$this->_getListCount($query);
		}
		return $this->_total;
	}

	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), 0, 0);
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		$query		= 'SELECT * FROM #__extensions'
		. $where;
		//. $orderby;
		return $query;
	}

	function _buildContentOrderBy()
	{
		global $mainframe, $option;
		$filterOrder	= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.filter_order', 'filter_order', '', 'cmd');
		$filterOrderDir	= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.filter_order_Dir','filter_order_Dir', '', 'word');

		if ($filterOrder != '')
		{
			$orderby 	= ' ORDER BY '.$filterOrder.' '.$filterOrderDir;
		}
		else
		{
			$orderby 	= ' ORDER BY ordering ASC ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db						= JFactory::getDBO();
		$where 					= array();
		$filterState			= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.filter_state', 'filter_state', '', 'word');
		$filter_order			= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir		= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.filter_order_Dir','filter_order_Dir', '', 'word');
		$pluginName				= $mainframe->getUserStateFromRequest('com_imageshow.themesManager.plugin_name', 'plugin_name', '', 'string');
		$pluginName				= JString::strtolower($pluginName);

		if($pluginName)
		{
			$where[] = 'LOWER(name) LIKE '.$db->Quote('%'.$db->escape($pluginName, true).'%', false);
		}

		if($filterState)
		{
			if($filterState == 'P')
			{
				$where[] = 'enabled = 1';
			}
			else if($filterState == 'U')
			{
				$where[] = 'enabled = 0';
			}
		}
		$where [] = 'LOWER(element) LIKE \'theme%\'';
		$where [] = 'folder = \'jsnimageshow\'';

		$where 	= (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		return $where;
	}

	function translate(&$items)
	{
		foreach($items as &$item)
		{
			if (strlen($item->manifest_cache))
			{
				$data = json_decode($item->manifest_cache);
				if ($data)
				{
					foreach($data as $key => $value)
					{
						if ($key == 'type')
						{
							continue;
						}
						$item->$key = $value;
					}
				}
			}
			$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;
			$item->client = $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE');
			$item->name = JText::_($item->name);
			$item->description = JText::_(@$item->description);
		}
	}

	function getFullData()
	{
		$db	= JFactory::getDBO();

		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$items = $this->_getList($query);
			$this->translate($items);
			$this->_data = $items;
		}
		return $this->_data;
	}
}
?>