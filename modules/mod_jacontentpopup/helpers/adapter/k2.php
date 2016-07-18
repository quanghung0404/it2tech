<?php
/**
 * ------------------------------------------------------------------------
 * JA Content Popup Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 


if (!class_exists('JAK2CHelper')) {
    class JAK2CHelper
    {
        /**
         *
         * Get Articles of K2
         * @param object $helper
         * @param object $params
         * @return object
         */
        function getList($params,&$helper,$jatotal=false,$catid = null)
        {
		
			$mainframe 	= JFactory::getApplication();
            $Itemid 	= JRequest::getInt('Itemid');
			
            if (! file_exists(JPATH_SITE . '/components/com_k2/helpers/route.php')) {
				return ;
			}
            require_once (JPATH_SITE . '/components/com_k2/helpers/route.php');

            //Category ids
            if(!$catid){
	            $catsid = $params->get('k2catsid');
	            $catids = array();
	            if (!is_array($catsid)) {
	                $catids[] = $catsid;
	            } else {
	                $catids = $catsid;
	            }

	            JArrayHelper::toInteger($catids);
	            if ($catids) {
	                if ($catids && count($catids) > 0) {
	                    foreach ($catids as $k => $cat) {
	                        if (!$cat)
	                            unset($catids[$k]);
	                    }
	                }
	            }
            }else {
            	$catids[] = $catid;
            }
            jimport('joomla.filesystem.file');
            $limit = (int) $helper->get('limit', 10);
            if (!$limit)
                $limit = 4;
            $limitstart = (int) $helper->get('limitstart', 0);
           
            $componentParams = JComponentHelper::getParams('com_k2');

            $user = JFactory::getUser();
            $app = JFactory::getApplication();
            $aid = $user->get('aid') ? $user->get('aid') : 1;
            $db = JFactory::getDBO();

            $jnow = JFactory::getDate();
            //$now = $jnow->toMySQL();
	        if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$now = $jnow->toSql();
				
			}
			else if (version_compare(JVERSION, '2.5', 'ge'))
			{
				$now = $jnow->toMySQL();
				
			}
			else
			{
				$now = $jnow->toMySQL();
				
			}
            $nullDate = $db->getNullDate();

			$query 	= "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.name as cattitle, c.params AS categoryparams";
            $query .= "\n FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";
            $query .= "\n WHERE i.published = 1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
            $query .= "\n AND ( i.publish_up = " . $db->Quote($nullDate) . " OR i.publish_up <= " . $db->Quote($now) . " )";
            $query .= "\n AND ( i.publish_down = " . $db->Quote($nullDate) . " OR i.publish_down >= " . $db->Quote($now) . " )";
			
			// language filter
			$lang = JFactory::getLanguage();
			$languageTag = $lang->getTag();
			if (JRequest::getVar('lang', false) || $mainframe->getLanguageFilter()) {
				$query .= " AND i.language IN ('{$languageTag}','*') ";
			}
			
            if ($catids) {
            	/*
                $catids_new = $catids;
                if ($params->get('getChildren', 1)) {
                    foreach ($catids as $k => $catid) {
                        $subcatids = JAK2Helper::getK2CategoryChildren($catid, true);
                        if ($subcatids) {
                            $catids_new = array_merge($catids_new, array_diff($subcatids, $catids_new));
                        }
                    }
                }
                $catids = implode(',', $catids_new);
                */
               	$catids = implode(',', $catids);
                $query .= "\n AND i.catid IN ($catids)";
            }
            
            // order by
			$order = $params->get('sort_order_field');
			$orderDir = $params->get('sort_order', 'DESC');
			switch ($order) {
				case 'created':
					$orderBy = "i.created";
					break;
				case 'ordering':
					$orderBy = "i.ordering";
					break;
				case 'hits':
					$orderBy = "i.hits";
					break;
				case 'rand':
					$orderBy = "RAND()";
					break;
				default:
					$orderBy = "i.created";
					break;
			}
            $query .= "\n ORDER BY " . $orderBy.' '.$orderDir;

			if(!$jatotal){				
				$db->setQuery($query, (int) JRequest::getInt('jalimitstart',0), (int) $params->get('limited', 2));
			}else{
				$db->setQuery($query);
			}
                
            
            $rows = $db->loadObjectList();
			
            if (count($rows)) {

                foreach ($rows as $j => $row) {

                    //Clean title
                    $row->title = JFilterOutput::ampReplace($row->title);

                    // Introtext
                    $row->text = $row->introtext;

                    //Read more link
                    $row->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($row->id . ':' . urlencode($row->alias), $row->catid . ':' . urlencode($row->categoryalias))));

                    //Images
                    $image = '';
                    if (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_XL.jpg'))
                        $image = 'media/k2/items/cache/' . md5("Image" . $row->id) . '_XL.jpg';

                    elseif (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_XS.jpg'))
                        $image = 'media/k2/items/cache/' . md5("Image" . $row->id) . '_XS.jpg';

                    elseif (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_L.jpg'))
                        $image = 'media/k2/items/cache/' . md5("Image" . $row->id) . '_L.jpg';

                    elseif (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_S.jpg'))
                        $image = 'media/k2/items/cache/' . md5("Image" . $row->id) . '_S.jpg';

                    elseif (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_M.jpg'))
                        $image = 'media/k2/items/cache/' . md5("Image" . $row->id) . '_M.jpg';

                    elseif (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $row->id) . '_Generic.jpg'))
                        $image =  'media/k2/items/cache/' . md5("Image" . $row->id) . '_Generic.jpg';

                    if ($image != '') {
						$row->images = array("image_intro"=>$image,"float_intro"=>"","image_intro_alt"=>"","image_intro_caption"=>"","image_fulltext"=>"","float_fulltext"=>"","image_fulltext_alt"=>"","image_fulltext_caption"=>"");					
						$row->images = json_encode($row->images);
                        $row->image = $helper->replaceImage($row);
                    } else {
                         $row->image = $helper->replaceImage($row);		
                    }
					
					$row->introtext = preg_replace('/<img[^>]*>/', '', $row->introtext);
					
					$row->text = $row->introtext;
					
					$row->jagroup	= false;
					
                    $rows[$j] = $row;
                }
            }
			if($jatotal && !$catid) return count($rows); 
			
            return $rows;
        }
		/**
         *
         * Get K2 category
         * @param int $catid
         * @return object
         */
        function getK2Category($catid) {
    		$user = JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
    		$catid = (int) $catid;
    		$db = JFactory::getDBO();
    		$query = "SELECT * FROM #__k2_categories WHERE id={$catid} AND published=1 AND trash=0 AND access<={$aid} ORDER BY ordering ";
    		$db->setQuery($query);
    		$rows = $db->loadObject();
    		return $rows;
    	}


        /**
         *
         * Get K2 category children
         * @param int $catid
         * @param boolean $clear if true return array which is removed value construction
         * @return array
         */
        function getK2CategoryChildren($catid, $clear = false) {

    		static $array = array();
    		if ($clear)
    		$array = array();
    		$user = JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
    		$catid = (int) $catid;
    		$db = JFactory::getDBO();
    		$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0 AND access<={$aid} ORDER BY ordering ";
    		$db->setQuery($query);
    		$rows = $db->loadObjectList();
    		foreach ($rows as $row) {
    			array_push($array, $row->id);
    			if (JAK2CHelper::hasK2Children($row->id)) {
    				JAK2CHelper::getK2CategoryChildren($row->id);
    			}
    		}
    		return $array;
    	}


    	/**
    	 *
    	 * Check category has children
    	 * @param int $id
    	 * @return boolean
    	 */
    	function hasK2Children($id) {

    		$user = JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
    		$id = (int) $id;
    		$db = JFactory::getDBO();
    		$query = "SELECT * FROM #__k2_categories WHERE parent={$id} AND published=1 AND trash=0 AND access<={$aid} ";
    		$db->setQuery($query);
    		$rows = $db->loadObjectList();

    		if (count($rows)) {
    			return true;
    		} else {
    			return false;
    		}
    	}
        
		function getCategories($params,$helper,$jatotal=false){
			$app 	= JFactory::getApplication();
        	$db		= JFactory::getDbo();
			$user = JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
			
	    	$query	= $db->getQuery(true);	    
	    	$query->select('id, name as title, parent,description,params,image');
	    	$query->from('#__k2_categories');
	    	
	    	$catsid = $params->get('k2catsid');
			
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }
			
            // Category filter
            if ($catids) {
                if ($catids[0] != "" && $catids[0] >0) {
                	$catids = implode(',',$catids);
                    $query->where('id IN ('.$catids.')');
                }
            }
            
	    	
	    	// Filter on the published state
	    	
	    	$query->where('published = 1');
	    	$query->where('trash = 0');
			$query->where("access<={$aid}");
			
			// order by
			$order = $params->get('sort_order_field_cat');
			$orderDir = $params->get('sort_order', 'DESC');
			switch ($order) {
				case 'name':
					$query->order('name '.$orderDir);
					break;
				case 'ordering':
					$query->order('ordering '.$orderDir);
					break;
				case 'rand':
					$query->order('RAND()');
					break;
				default:
					$query->order('ordering '.$orderDir);
					break;
			}
			
	    	
			
	    	if(!$jatotal){
            	$db->setQuery($query,(int) JRequest::getInt('jalimitstart',0),(int) $params->get('limited', 2));
			}
	    	else {
	    		$db->setQuery($query);
	    	}
	    	$items = $db->loadObjectList();
			
	    	foreach ($items as $i=>$item){
				
				$item->content 	= JAK2CHelper::getList($params,$helper,true,$item->id);
				
				$item->jagroup	= true;
				
				$item->text			= $item->description;
					
				$item->introtext 	= $item->description;
				
				$item->fulltext 	= $item->description;
				
				
				if (isset($item->image) && $item->image != null){
					$image 	= '/media/k2/categories/'.$item->image;					
					$item->images = array("image_intro"=>$image,"float_intro"=>"","image_intro_alt"=>"","image_intro_caption"=>"","image_fulltext"=>"","float_fulltext"=>"","image_fulltext_alt"=>"","image_fulltext_caption"=>"");					
					$item->images = json_encode($item->images);
					$item->image = $helper->replaceImage($item);
						
				}else if($helper->replaceImage($item)){
					$item->image 	= $helper->replaceImage($item);
				}else {
					$item->image	= '';
				}
				
				$item->text  		= preg_replace('/<img[^>]*>/', '', $item->description);
				
				if(count($item->content)>0){
					$content 			= $item->content[0];

					$item->link			= $content->link;
					
					$item->subtitle		= $content->title;
					
					$item->otherlink	= '';
					
					foreach ($item->content AS $c){
						if($c->link != $item->link){
							$item->otherlink .= '<a href="'.$c->link.'" data-ref="group_'.$item->id.'" target="yoxview" class="jacontent" style="display:none;" title="'.htmlspecialchars($c->title).'">'.$c->title.'</a>';
						}
					}
					$items[$i] = $item;
				}
				else {
					$item->groupcatid	= $item->id;
					
					$item->link 		= 'javascript:void(0)';
					
					$items[$i]= $item;
				}
				$i++;
			}
			
	    	if($jatotal) return count($items);
	    	
	    	return $items;
		}
        
    }
}

?>