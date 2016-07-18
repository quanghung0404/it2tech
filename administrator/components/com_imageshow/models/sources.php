<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: sources.php 11579 2012-03-07 04:21:07Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class ImageShowModelSources extends JModelLegacy
{
	var $_data = null;

	function __construct()
	{
		parent::__construct();
	}

	function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$query		= 'SELECT * FROM #__extensions'
		. $where;
		return $query;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db		= JFactory::getDBO();
		$where 	= array();

		$where [] = 'LOWER(element) LIKE \'source%\'';
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