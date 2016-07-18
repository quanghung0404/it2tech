<?php
/**
 * @version    $Id: showlists.php 16522 2012-09-28 03:41:55Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
class ImageShowModelShowLists extends JModelList
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('sl.showlist_id', 'sl.showlist_title', 'sl.published', 'sl.ordering', 'sl.hits',);
		}
		parent::__construct($config);
		$app 		= JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_imageshow.limitstart', 'limitstart', 0, 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getItems()
	{
		$db	= JFactory::getDBO();

		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = @$this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			if (count($this->_data))
			{
				for ($i=0, $n=count($this->_data); $i < $n; $i++)
				{
					$sourceTitle = '<br/>' . JText::_('N/A');
					$row = $this->_data[$i];
					if ($row->image_source_name != '')
					{
						$imageSource = JSNISFactory::getSource($row->image_source_name, $row->image_source_type, $row->showlist_id);
						$sourceTitle = $imageSource->getProfileTitle();
						if ($sourceTitle != '')
						{
							$sourceTitle = '<em>[' . $imageSource->_source['sourceIdentify'] . ']</em><br/>' . $sourceTitle;
						}
						else
						{
							$sourceTitle = '<br/>' . JText::_('N/A');
						}
					}
					$row->image_source_title = $sourceTitle;
					$this->_data[$i] = $row;
				}
			}
		}

		return $this->_data;
	}

	public function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = @$this->_getListCount($query);
		}

		return $this->_total;
	}

	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	private function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		$strColunms = 'sl.showlist_id, sl.showlist_title, sl.published, sl.ordering, sl.access, sl.hits, sl.image_source_name, sl.image_source_type';
		$query		= 'SELECT ' . $strColunms . ', ag.title AS access_level, COUNT(img.showlist_id) AS totalimage
						FROM #__imageshow_showlist AS sl'.
					  ' LEFT JOIN #__imageshow_images img ON sl.showlist_id = img.showlist_id
						LEFT JOIN #__viewlevels AS ag ON ag.id = sl.access'
						. $where
						. ' GROUP BY ' . $strColunms . ', ag.title' . $orderby ;
						return $query;

	}

	private function _buildContentOrderBy()
	{
		$app 				= JFactory::getApplication();
		$filterOrder		= $app->getUserStateFromRequest('com_imageshow.showlist.filter_order', 'filter_order', '', 'cmd');
		$filterOrderDir		= $app->getUserStateFromRequest('com_imageshow.showlist.filter_order_Dir','filter_order_Dir', '', 'word');

		if ($filterOrder != '')
		{
			$orderby = ' ORDER BY ' . $filterOrder . ' ' . $filterOrderDir;
		}
		else
		{
			$orderby = ' ORDER BY sl.ordering ASC ';
		}

		return $orderby;
	}

	private function _buildContentWhere()
	{
		$app 					= JFactory::getApplication();
		$db						= JFactory::getDBO();
		$where					= array();
		$filterState			= $app->getUserStateFromRequest('com_imageshow.showlist.filter_state', 'filter_state', '', 'string');
		$filter_order			= $app->getUserStateFromRequest('com_imageshow.showlist.filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir		= $app->getUserStateFromRequest('com_imageshow.showlist.filter_order_Dir','filter_order_Dir', '', 'word');
		$showlistTitle			= $app->getUserStateFromRequest('com_imageshow.showlist.showlist_stitle', 'showlist_stitle', '', 'string');
		$showlistAccess			= $app->getUserStateFromRequest('com_imageshow.showlist.filter_access', 'access', '', 'string');
		$showlistTitle			= JString::strtolower($showlistTitle);

		if ($showlistTitle)
		{
			$where[] = 'LOWER(sl.showlist_title) LIKE ' . $this->_db->Quote('%' . $this->_db->escape($showlistTitle, true) . '%', false);
		}
		if ($filterState != '')
		{
			$where[] = 'sl.published = ' . (int) $filterState;

		}

		if ($showlistAccess !='')
		{
			$where[] = 'sl.access = ' . $showlistAccess;
		}

		$where 	= (count($where) ? ' WHERE '. implode(' AND ', $where) : '' );

		return $where;
	}

	public function delete($cid = array())
	{
		$result = false;

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);

			$showlistTable = JTable::getInstance('showlist', 'Table');

			foreach ($cid as $showlistID)
			{
				$showlistTable->load($showlistID);

				$imageSource = JSNISFactory::getSource($showlistTable->image_source_name, $showlistTable->image_source_type, $showlistID);

				$imageSource->removeShowlist();
			}
		}
		return true;
	}

	public function approve($cid = array(), $publish = 1)
	{
		if (!$this->canEditState())
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'error');
			return false;
		}

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			$query = 'UPDATE #__imageshow_showlist'
			. ' SET published = ' . (int) $publish
			. ' WHERE showlist_id IN (' . $cids . ')';
			$this->_db->setQuery( $query );

			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function accessmenu($id, $access)
	{
		$row = $this->getTable('showlist');
		$row->showlist_id = $id;
		$row->access = $access;

		if ( !$row->check() ) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if ( !$row->store() ) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function accesslevel( &$row )
	{
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT id AS value, name AS text'
		. ' FROM #__groups'
		. ' ORDER BY id, name';

		$this->_db->setQuery($query);
		$groups 	= $this->_db->loadObjectList();
		$results[] 	= JHTML::_('select.option', '', '- '.JText::_('Select Access Level').' -', 'value', 'text');
		$results 	= array_merge($results, $groups);
		$access 	= JHTML::_('select.genericlist',   $results, 'access', 'class="inputbox" onchange="document.adminForm.submit();"', 'value', 'text', $row, '', 1);
		return $access;
	}

	protected function canEditState()
	{
		$user = JFactory::getUser();
		return $user->authorise('core.edit.state', $this->option);
	}
}