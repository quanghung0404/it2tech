<?php
/**
 * @version    $Id: showcases.php 16537 2012-09-29 03:36:46Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.modellist');
class ImageShowModelShowCases extends JModelList
{
	var $_data = null;
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('showcase_id', 'showcase_title', 'published', 'ordering');
		}

		parent::__construct($config);
		$app 		= JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_imageshow.showcase.limitstart', 'limitstart', 0, 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getItems()
	{
		$db	= JFactory::getDBO();

		if (empty($this->_data))
		{
			$query			= $this->_buildQuery();
			$this->_data	= @$this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			if (count($this->_data))
			{
				for ($i=0, $n=count($this->_data); $i < $n; $i++)
				{
					$row					= $this->_data[$i];
					$objJSNShowcaseTheme	= JSNISFactory::getobj('classes.jsn_is_showcasetheme');
					$themeProfile			= $objJSNShowcaseTheme->getThemeProfile($row->showcase_id);
					$part					= explode('theme', @$themeProfile->theme_name);
					if (isset($part[1]))
					{
						$row->theme_title	= 'Theme '.ucfirst($part[1]);
					}
					else
					{
						$row->theme_title	= JText::_('N/A');
					}

					$this->_data[$i] = $row;
				}
			}
		}

		return $this->_data;
	}

	private function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query		= 'SELECT * FROM #__imageshow_showcase'
		. $where
		. $orderby;
		return $query;

	}

	private function _buildContentOrderBy()
	{
		$app 			= JFactory::getApplication();
		$filterOrder	= $app->getUserStateFromRequest('com_imageshow.showcase.filter_order', 'filter_order', '', 'cmd');
		$filterOrderDir	= $app->getUserStateFromRequest('com_imageshow.showcase.filter_order_Dir','filter_order_Dir', '', 'word');

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

	private function _buildContentWhere()
	{
		$app 				= JFactory::getApplication();
		$db					= JFactory::getDBO();
		$where 				= array();
		$filterState		= $app->getUserStateFromRequest('com_imageshow.showcase.filter_state', 'filter_state', '', 'string');
		$filter_order		= $app->getUserStateFromRequest('com_imageshow.showcase.filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_imageshow.showcase.filter_order_Dir','filter_order_Dir', '', 'word');
		$showcaseTitle		= $app->getUserStateFromRequest('com_imageshow.showcase.showcase_title', 'showcase_title', '', 'string');
		$showcaseTitle		= JString::strtolower($showcaseTitle);

		if ($showcaseTitle)
		{
			$where[] = 'LOWER(showcase_title) LIKE '.$db->Quote('%'.$db->escape( $showcaseTitle, true ).'%', false);
		}

		if ($filterState != '')
		{
			$where[] = 'published = ' . (int) $filterState;

		}

		$where 	= (count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '');
		return $where;
	}

	public function delete($cid = array())
	{
		$result = false;

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__imageshow_showcase'
			. ' WHERE showcase_id IN (' . $cids . ')';
			$this->_db->setQuery( $query );

			if(!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
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
			$cids 	= implode(',', $cid);

			$query 	= 'UPDATE #__imageshow_showcase'
			. ' SET published = ' . (int) $publish
			. ' WHERE showcase_id IN (' . $cids . ')';
			$this->_db->setQuery( $query );

			if (!$this->_db->query()){
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
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

	protected function canEditState()
	{
		$user = JFactory::getUser();
		return $user->authorise('core.edit.state', $this->option);
	}
}
