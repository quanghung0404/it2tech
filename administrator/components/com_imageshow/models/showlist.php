<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: showlist.php 12585 2012-05-11 08:17:16Z hiennv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class ImageShowModelShowList extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$array  = JRequest::getVar('cid', array(0), '', 'array');
		$edit	= JRequest::getVar('edit',true);

		if ($edit) {
			$this->setId((int)$array[0]);
		}
	}

	function setId($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	function getData()
	{

		if ($this->_loadData()){
			return $this->_data;
		}else{
			return $this->_initData();
		}
	}

	function store($data)
	{
		$row = $this->getTable();

		if (!$row->bind($data)){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->setId($row->showlist_id);
		$row->reorder();
		return true;
	}

	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT * FROM #__imageshow_showlist WHERE showlist_id = '.(int) $this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			$result = (boolean) $this->_data;

			if($result)
			{
				$this->_data->aut_article_title 	= @$this->getArticleTitleByID($this->_data->alter_autid);
				return (boolean) $this->_data;
			}
			else
			{
				return $result;
			}
		}
		//return true;
	}

	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$showlist 							= new stdClass();
			$showlist->showlist_id 				= 0;
			$showlist->showlist_title			= null;
			$showlist->published 				= 0;
			$showlist->ordering 				= 0;
			$showlist->access					= 0;
			$showlist->hits 					= 0;
			$showlist->description 				= null;
			$showlist->showlist_link 			= null;
			$showlist->alter_autid 				= 0;
			$showlist->date_create 				= null;
			$showlist->date_modified 			= null;
			$showlist->image_source_name		= null;
			$showlist->image_source_type 		= null;
			$showlist->image_source_profile_id	= 0;
			$showlist->authorization_status		= 0;
			$showlist->override_title 			= 0;
			$showlist->override_description 	= 0;
			$showlist->override_link 			= 0;
			$showlist->image_loading_order 		= 0;
			$showlist->show_exif_data	 		= 'no';
			$this->_data = $showlist;
			return $this->_data;
		}
		return true;
	}

	function accesslevel(&$row)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__groups'
		. ' ORDER BY id, name'
		;
		$db->setQuery( $query );
		$groups = $db->loadObjectList();
		$access = JHTML::_('select.genericlist',   $groups, 'access', 'class="inputbox"', 'value', 'text', $row->access, '', 1 );

		return $access;
	}

	function getArticleTitleByID($id)
	{
		$query = 'SELECT title FROM #__content WHERE id = '.(int)$id;
		$this->_db->setQuery($query);
		$result = @$this->_db->loadObject();
		return $result->title;
	}
	/**
	 * get tree menu for popup
	 */
	function getTreeMenu($selectMenu = '')
	{
		$db = JFactory::getDBO();
		$query = 'SELECT id, menutype AS data, title FROM #__menu_types';
		$db->setQuery($query);
		$menus 	= $db->loadObjectList();
		$data 	= array();

		foreach ($menus as $menu) {
			$data[] = $menu;
		}

		return $data;
	}

	/**
	 * get tree menu for popup
	 */
	function _getListMenuItem($menuType)
	{
		$data = '';
		$db = JFactory::getDBO();
		$query = 'SELECT MIN(level) AS min_level FROM #__menu WHERE menutype ='.$db->quote($menuType) .' AND client_id = 0 ORDER BY lft ASC';
		//echo $query;
		$db->setQuery($query);
		$minLevel = $db->loadResult();
		if ($minLevel)
		{
			$data.= '<ul id="menu-item-list">';
			$query = 'SELECT id, title, lft, rgt, level, menutype, link, params, type FROM #__menu WHERE menutype ='.$db->quote($menuType) .' AND client_id = 0 AND level = '.(int)$minLevel.' ORDER BY lft ASC';
			$db->setQuery($query);
			$menus = $db->loadObjectList();
			$count = count($menus);

			foreach ($menus as $menu)
			{
				$data.='<li><a class="linkimage" id="link_'.$menu->id.'" href="'.$menu->link.'"><span>'.$menu->title.'</span></a>';
				$data.= $this->_menuItem($menu);
				$data.='</li>';
			}
			$data.='</ul>';
		}

		return $data;
	}

	function _menuItem($item)
	{
		$data = '';
		$db = JFactory::getDBO();
		$query = 'SELECT id, title, lft, rgt, level, link, params, type FROM #__menu WHERE lft >= '.$item->lft.' AND rgt <= '.$item->rgt.' AND client_id = 0 AND parent_id = '.$item->id.' ORDER BY lft ASC';
		//var_dump($item);
		$db->setQuery($query);
		$menus = $db->loadObjectList();

		$count = count($menus);

		if ($count)
		{
			for ($i = 0 ; $i < $count; $i++)
			{
				$menu  = $menus[$i];

				// only add element in same level
				if ($item->level + 1 == $menu->level)
				{
					$data.='<ul><li><a class="linkimage" href="'.$menu->link.'">';
					$data.=$menu->title;
					$data.=ImageShowModelShowList::_menuItem($menu,$data);
					$data.='</a></li></ul>';
				}
			}
		}
		return $data;
	}
	function getTreeArticle()
	{
		$data = '<ul id="article-item-list"> ';
		$db = JFactory::getDBO();
		$query = 'SELECT id, title, lft, rgt, level FROM #__categories WHERE id = 1';
		$db->setQuery($query);
		$root = $db->loadObject();
		$data.= $this->_getTreeCate($root);
		$data.='</ul>';
		return $data;
	}
	function _getTreeCate($root)
	{

		$db = JFactory::getDBO();
		$query = 'SELECT id, title, lft, rgt, level FROM #__categories WHERE lft >= '.$root->lft.' AND rgt <= '.$root->rgt.' AND extension = \'com_content\' AND parent_id='.$root->id.' ORDER BY lft ASC';
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		$data = '';
		$count = count($categories);

		for ($i = 0 ; $i < $count; $i++)
		{
			$cate  = $categories[$i];

			// only add element in same level
			if ($root->level + 1 == $cate->level)
			{
				if($root->id==1){
					$data.= '<li><a id="art_cat_'.$cate->id.'" class="art_cat" href="javascript:void(0);">';
				}else{
					$data.= '<ul><li><a id="art_cat_'.$cate->id.'" class="art_cat" href="javascript:void(0);">';
				}
				$data.=$cate->title;
				$data.=ImageShowModelShowList::_getTreeCate($cate);
				if($root->id==1){
					$data.= '</a></li>';
				}else{
					$data.= '</a></li></ul>';
				}
			}
		}
		return $data;
	}

	function _getListArticleItem($catID)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT id, title FROM #__content WHERE catid = ' .(int)$catID;
		$db->setQuery($query);
		$articles = $db->loadObjectList();
		$items = array();

		foreach ($articles as $article)
		{
			$item = new stdClass();
			$item->title = $article->title;
			$item->link = 'index.php?option=com_content&view=article&id='.$article->id;
			$items[] = $item;
		}

		return $items;
	}

}
?>