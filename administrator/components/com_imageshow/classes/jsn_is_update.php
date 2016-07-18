<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_update.php 9659 2011-11-15 10:55:55Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JSNISUpdate{
	function JSNISUpdate()
	{
		$this->_db 		= JFactory::getDbo();
		$this->_methods = get_class_methods('JSNISUpdate');
	}

	function eventUpdate($event)
	{
		foreach ($this->_methods as $method)
		{
			$fn = '_'.$event.'Update';

			if (strpos($method, $fn) !== false) {
				$this->$method();
			}
		}
	}

	function _afterUpdateFixMenuViewShow()
	{
		$query = 'SELECT * FROM #__menu WHERE link LIKE \'index.php?option=com_imageshow&view=show\'';
		$this->_db->setQuery($query);
		$menus = $this->_db->loadObjectList();

		foreach ($menus as $key => $menu)
		{
			if ($menu->params)
			{
				$params = json_decode($menu->params);

				$showlistID 	= (isset($params->showlist_id)) ? $params->showlist_id : 0;
				$menu->link 	= $menu->link.'&showlist_id='.$showlistID;
				unset($params->showlist_id);

				$showcaseID 	= (isset($params->showcase_id)) ? $params->showcase_id : 0;
				$menu->link 	= $menu->link.'&showcase_id='.$showcaseID;
				unset($params->showcase_id);

				$jsnisid 	= time() + $key;
				$menu->link = $menu->link.'&jsnisid='.$jsnisid;

				$params = json_encode($params);

				$query = 'UPDATE #__menu
						  SET link ='.$this->_db->quote($menu->link, false).', params ='.$this->_db->quote($params , false).'
						  WHERE id = '.$menu->id.'
						  AND  link LIKE \'index.php?option=com_imageshow&view=show\'';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}


}
