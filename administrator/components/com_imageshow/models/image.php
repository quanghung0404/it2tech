<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images.php 8411 2011-09-22 04:45:10Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
class ImageShowModelImage extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db			= JFactory::getDBO();
		$where 		= array();
		$imageID	= $mainframe->getUserStateFromRequest('com_imageshow.images.imageID', '', '', 'string');
		$imageID 	= str_replace('_jsnisdot_', '.',$imageID);
		$showlistID = $mainframe->getUserStateFromRequest('com_imageshow.images.showlistID', '', '', 'string');
		$where		= ' WHERE image_extid="'.$imageID.'"  AND showlist_id="'.$showlistID.'"';
		return $where;
	}
	function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$query		= 'SELECT * FROM #__imageshow_images'
		. $where;
		//. $orderby;
		return $query;
	}
	function getItem($imageID)
	{
		$db	= JFactory::getDBO();
		$query = $this->_buildQuery();
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
	function getItems($imageID,$showListID)
	{
		$db	= JFactory::getDBO();
		$imagesForEdit = explode("|",str_replace('_jsnisdot_', '.',$imageID));
		$countImages = count($imagesForEdit)-1;
		if($countImages>1){
			$where = '';
			for($i=0;$i<$countImages;$i++){
				$where .= 'image_extid="'.$imagesForEdit[$i].'"';
				if($i != $countImages-1)
				$where .= ' OR ';
				else
				$where = ' AND ('.$where.')';
			}
			$query = 'SELECT * FROM #__imageshow_images where showlist_id="'.$showListID.'"'.$where.' ORDER BY ordering';
			$db->setQuery($query);
			return $db->loadObjectList();
		}else{
			$query = $this->_buildQuery();
			$db->setQuery($query);
			return $db->loadObject();
		}
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
					$data.=ImageShowModelImage::_menuItem($menu,$data);
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
				$data.=ImageShowModelImage::_getTreeCate($cate);
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

	function saveImage($data = array())
	{
		$search 				= array('<B>', '</B>', '<I>', '</I>', '<S>', '</S>', '<b>', '</b>', '<i>', '</i>', '<s>', '</s>');
		$replace 				= array('<STRONG>', '</STRONG>', '<EM>', '</EM>', '<STRIKE>', '</STRIKE>', '<strong>', '</strong>', '<em>', '</em>', '<strike>', '</strike>');
		$imageID 				= JRequest::getVar('imageID','');
		$imageTitle				= JRequest::getVar('title','');
		$imageDescription 		= str_replace($search, $replace, JRequest::getVar('description','','post', 'string', JREQUEST_ALLOWHTML));
		$imageLink				= JRequest::getVar('link','');
		$showListID				= JRequest::getVar('showlistID');
		$originalTitle			= JRequest::getVar('originalTitle','');
		$originalDescription 	= str_replace($search, $replace, JRequest::getVar('originalDescription','','post', 'string', JREQUEST_ALLOWHTML));
		$originalLink			= JRequest::getVar('originalLink','');

		// process update information of image
		if($imageTitle!=$originalTitle||$imageDescription!=$originalDescription||$imageLink!=$originalLink){
			$db = JFactory::getDBO();
			$query = "UPDATE #__imageshow_images
					  SET image_title = ".$db->quote($db->escape($imageTitle), false).",
						image_description = ".$db->quote($db->escape($imageDescription), false ).",
						image_link=".$db->quote($db->escape($imageLink), false).",
						custom_data = 1
					  WHERE image_id = ".$db->quote($db->escape($imageID), false );
			$db->setQuery($query);
			$result = $db->query();
		}
	}
	function saveImages($data=array()){
		$search 				= array('<B>', '</B>', '<I>', '</I>', '<S>', '</S>', '<b>', '</b>', '<i>', '</i>', '<s>', '</s>');
		$replace 				= array('<STRONG>', '</STRONG>', '<EM>', '</EM>', '<STRIKE>', '</STRIKE>', '<strong>', '</strong>', '<em>', '</em>', '<strike>', '</strike>');
		$number			= JRequest::getVar('numberOfImages','');
		if($number>1){
			$db = JFactory::getDBO();
			for($i=0;$i<$number;$i++){
				$originalDescription 	= str_replace($search, $replace, $data["originalDescription"][$i]);
				$imageDescription 		= str_replace($search, $replace, $data["description"][$i]);

				if($data["title"][$i]!=$data["originalTitle"][$i]||$imageDescription!=$originalDescription||$data["image_link"][$i]!=$data["originalLink"][$i]){
					$query = "UPDATE #__imageshow_images
							  SET image_title = ".$db->quote($db->escape($data["title"][$i]), false).",
								image_description = ".$db->quote($db->escape($imageDescription), false ).",
								image_link=".$db->quote($db->escape($data["image_link"][$i]), false).", custom_data = 1 WHERE image_id = ".$db->quote($db->escape($data["imageID"][$i]), false );
					$db->setQuery($query);
					$db->query();
				}
			}
		}
		else{
			$this->saveImage($data);
		}
	}
	function PurgeAbsoleteImages($showListID, $imageID)
	{
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__imageshow_images WHERE showlist_id=".$db->quote($db->escape($showListID))." AND image_extid =".$db->quote($db->escape($imageID));
		$db->setQuery($query);
		$db->query();
	}

	function updateImageInformation($objInformation)
	{
		$db 	= JFactory::getDBO();
		$query 	= "UPDATE #__imageshow_images SET image_title = ". $db->quote($db->escape($objInformation->image_title), false). ",
					image_description = ". $db->quote($db->escape($objInformation->image_description), false) .",
					image_link = ". $db->quote($db->escape($objInformation->image_link), false). ",
					custom_data = ". $db->quote($db->escape($objInformation->custom_data), false). "
					WHERE image_id = " . $db->quote($db->escape($objInformation->image_id), false);
		$db->setQuery($query);
		return $db->query();
	}
}
?>