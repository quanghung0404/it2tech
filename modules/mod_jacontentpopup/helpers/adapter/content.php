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

if (!class_exists('JANewsHelper')) {
    class JANewsHelper
    {


        /**
         *
         * Get data Article
         * @param object $helper object from JAHelperFP
         * @param object $params
         * @return object $helper object include data of Article
         */
		
        public static function getList($params,$helper,$jatotal=false,$catid = null)
        {
           	
            
        	$mainframe 	= JFactory::getApplication();
            $Itemid 	= JRequest::getInt('Itemid');
            // Get the dbo
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            // Get an instance of the generic articles model
           
	        if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
				
			}
			else if (version_compare(JVERSION, '2.5', 'ge'))
			{
				$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
				
			}
			else
			{
				$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
				
			}
            //set list.select state
			$model->setState(
				'list.select',
				'a.id, a.title, a.alias, a.images, a.introtext,a.fulltext, a.state, ' .
				'a.checked_out, a.checked_out_time, ' .
				'a.catid, a.created, a.created_by, a.created_by_alias, a.language,' .
				// use created if modified is 0
				'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, uam.name as modified_by_name,' .
				// use created if publish_up is 0
				'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up,' .
					'a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access, ' .
					'a.hits, a.xreference, a.featured,'.' '.$query->length('a.fulltext').' AS readmore'
			) ;
            // Set application parameters in model
            $appParams = JFactory::getApplication()->getParams();
            
            $model->setState('params', $appParams);

            $model->setState('filter.published', 1);

            // Access filter
            $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
            $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
            $model->setState('filter.access', $access);
            
            // Filter by language.
            if (JRequest::getVar('lang', false) || $mainframe->getLanguageFilter())
				$model->setState('filter.language', (JRequest::getVar('lang', false) || $mainframe->getLanguageFilter()));
			
            if(!$catid){
	            $catsid = $params->get('catsid');
				
	            $catids = array();
	            if (!is_array($catsid)) {
	                $catids[] = $catsid;
	            } else {
	                $catids = $catsid;
	            }
            }else {
            	$catids[] = $catid;
            }
            // Category filter
            if ($catids) {
                if ($catids[0] != "") {
                    $model->setState('filter.category_id', $catids);
                }
            }            
            // order by
			$order = $params->get('sort_order_field');
			$orderDir = $params->get('sort_order', 'DESC');
			switch ($order) {
				case 'created':
					$orderBy = "a.created";
					break;
				case 'ordering':
					$orderBy = "a.ordering";
					break;
				case 'hits':
					$orderBy = "a.hits";
					break;
				case 'rand':
					$orderBy = " RAND() ";
					break;
				default:
					$orderBy = "a.created";
					break;
			}
			$model->setState('list.ordering', $orderBy);
            $model->setState('list.direction', $orderDir);
			
			if(!$jatotal){
            	$model->setState('list.start', (int) JRequest::getInt('jalimitstart',0));           
           		$model->setState('list.limit', (int) $params->get('limited', 2));
			}
			
            //$jalimitstart = JRequest::getInt('jalimitstart',0);
            
			//$limited = (int) $params->get('limited', 2);
			
			//var_dump($model);
            $rows = $model->getItems();
			
           	$j = 0;
			
            foreach ($rows as $i => $row) {
            	
            	JPluginHelper::importPlugin('content');
                $dispatcher = JDispatcher::getInstance();
                $com_params = $mainframe->getParams('com_content');
            	
                $row->cat_link = JRoute::_(ContentHelperRoute::getCategoryRoute($row->catid));
               
                
                $row->slug = $row->id . ':' . $row->alias;
                $row->catslug = $row->catid . ':' . $row->category_alias;
                
                if ($access || in_array($row->access, $authorised)) {
                    // We know that user has the privilege to view the article
                    if(strpos(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug),'Itemid') === false && isset($Itemid)){
						$articlelink		= ContentHelperRoute::getArticleRoute($row->slug, $row->catslug).'&Itemid='.$Itemid;
						$row->link 	= JRoute::_($articlelink);
					}
					else{
						$row->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
					}
                    
                } else {
                    $row->link = JRoute::_('index.php?option=com_user&view=login');
                }
                
                $row->image = $helper->replaceImage($row);
                
                $row->introtext = JHtml::_('content.prepare', $row->introtext, '', 'mod_janews.content');
                
                $results = $mainframe->triggerEvent('onContentAfterDisplay', array('com_content.article', &$row, &$com_params, 1));
				
                $row->afterDisplayTitle = trim(implode("\n", $results));
	
				$results = $mainframe->triggerEvent('onContentBeforeDisplay', array('com_content.article', &$row, &$com_params, 1));
				
				$row->beforeDisplayContent = trim(implode("\n", $results));
                
				$row->introtext = preg_replace('/<img[^>]*>/', '', $row->introtext);
				
				$row->text = $row->introtext;
				
				$row->jagroup	= false;
				//$row->text = $helper->trimString($row->introtext,$params->get('limittext',60),"<img />");
				
				$rows[$i] = $row;
	                	
            }
            
            if($jatotal && !$catid) return count($rows);  
                   
            return $rows;
        }
        public static function getCategories($params,$helper,$jatotal=false){
        	
        	$app 	= JFactory::getApplication();
        	$db		= JFactory::getDbo();
	    	$query	= $db->getQuery(true);
	    
	    	$query->select('a.id, a.title, a.level, a.parent_id,a.description,a.params');
	    	$query->from('#__categories AS a');
	    	$query->where('a.parent_id > 0');
	    	
	    	$catsid = $params->get('catsid');
			
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }
           
            // Category filter
            if ($catids) {
                if ($catids[0] != "") {
                	$catids = implode(',',$catids);
                    $query->where('a.id IN ('.$catids.')');
                }
            }
            
	    	// Filter on extension.
	    	$query->where('extension = '.$db->quote('com_content'));
	    
	    	// Filter on the published state
	    	
	    	$query->where('a.published = 1');
	    	
			// order by
			$order = $params->get('sort_order_field_cat');
			$orderDir = $params->get('sort_order', 'DESC');
			switch ($order) {
				case 'name':
					$query->order('a.title '.$orderDir);
					break;
				case 'ordering':
					$query->order('a.lft '.$orderDir);
					break;
				case 'rand':
					$query->order('RAND()');
					break;
				default:
					$query->order('a.lft '.$orderDir);
					break;
			}
			
	    	if(!$jatotal){
            	$db->setQuery($query,(int) JRequest::getInt('jalimitstart',0),(int) $params->get('limited', 2));
			}
	    	else {
	    		$db->setQuery($query);
	    	}
	    	
	    	$items = $db->loadObjectList();
	    	
	    	$return = array();
	    	foreach ($items as $i=>$item){	
	    				
	    		$item->content 	= JANewsHelper::getList($params,$helper,true,$item->id);
	    		
	    		$item->jagroup	= true;
	    		
	    		$item->text			= $item->description;
		    		
	    		$item->introtext 	= $item->description;
	    		
	    		$item->fulltext 	= $item->description;
	    		
	    		$images 			= json_decode($item->params);
	    		
	    		if (isset($images->image) && $images->image != null){
	    			$item->image 	= $images->image;
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
	    	}
	    	
	    	if($jatotal) return count($items);
	    	
	    	return $items;
        }
    }
}
?>