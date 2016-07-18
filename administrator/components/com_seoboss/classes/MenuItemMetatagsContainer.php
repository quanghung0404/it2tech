<?php
/*------------------------------------------------------------------------
# SEO Boss
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

require_once "MetatagsContainer.php";
class MenuItemMetatagsContainer extends MetatagsContainer{
	public $code=15;
        public function getMetatags($lim0, $lim, $filter=null){
          $mainframe = JFactory::getApplication();
          $mainframe->enqueueMessage("This feature is fully available in SEO Boss Pro only. You can't edit the metadata.", "error"); 
		$db = JFactory::getDBO();
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id as id, c.{$this->getField()} AS title,
		        ( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        ) AS metakey,
		        m.description as metadesc, 
		        m.title as metatitle, 
		        m.title_tag as title_tag
		        FROM 
		        #__menu c
		        LEFT JOIN
		        #__seoboss_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE 1";
		
		$search = JRequest::getVar("filter_search", "");
                $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
                $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
                $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");
        
        if($search != ""){
        	if(is_numeric($search)){
        		$sql .= " AND c.id=".$db->quote($search);
        	}else{
        		$sql .= " AND c.{$this->getField()} LIKE ".$db->quote('%'.$search.'%');
        	}
        }
        if( $menu_type ){
        	$sql .= " AND c.menutype=".$db->quote($menu_type);
        }
        
        if($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND ( ISNULL(m.description) OR m.description='') ";
        }
        if($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " HAVING ( ISNULL(metakey) OR metakey='') ";
        }
        //Sorting
        $order = JRequest::getCmd("filter_order", "title");
        $order_dir = JRequest::getCmd("filter_order_Dir", "ASC");
        switch($order){
            case "meta_title":
                $sql .= " ORDER BY metatitle ";
                break;
            case "meta_key":
                $sql .= " ORDER BY metakey ";
                break;
            case "meta_desc":
                $sql .= " ORDER BY metadesc ";
                break;
            case "title_tag":
                $sql .= " ORDER BY title_tag ";
                break;
            default:
                $sql .= " ORDER BY title ";
                break;
        }
        if($order_dir == "asc"){
        	$sql .= " ASC";
        }else{
        	$sql .= " DESC";
        }

        
        
	    $db->setQuery( $sql, $lim0, $lim );
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        for($i = 0 ; $i < count($rows);$i++){
            $rows[$i]->edit_url = "index.php?option=com_menus&task=item.edit&id={$rows[$i]->id}";
        }
        return $rows;
	}
	
  public function getPages($lim0, $lim, $filter=null){
          $mainframe = JFactory::getApplication();
          $mainframe->enqueueMessage("This feature is fully available in SEO Boss Pro only. You can't edit the metadata.", "error"); 
    $db = JFactory::getDBO();
    $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.{$this->getField()} as title,
            ( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
                        FROM #__seoboss_keywords k, 
                        #__seoboss_keywords_items ki 
                        WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
                            AND ki.keyword_id=k.id
                    )  as metakey, c.published as state,
    '' AS content 
     FROM 
    #__menu c WHERE 1
    ";
        
    $search = JRequest::getVar("filter_search", "");
    $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
    $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
    $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");
        
    if($search != ""){
      if(is_numeric($search)){
        $sql .= " AND c.id=".$db->quote($search);
      }else{
        $sql .= " AND c.{$this->getField()} LIKE ".$db->quote('%'.$search.'%');
      }
    }
    if( $menu_type ){
      $sql .= " AND c.menutype=".$db->quote($menu_type);
    }
        
    if($com_content_filter_show_empty_descriptions != "-1"){
      $sql .= " AND ( ISNULL(m.description) OR m.description='') ";
    }
    if($com_content_filter_show_empty_keywords != "-1"){
      $sql .= " HAVING ( ISNULL(metakey) OR metakey='') ";
    }
    $db->setQuery( $sql, $lim0, $lim );
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
      echo $db->stderr();
      return false;
    }
    for($i = 0 ; $i < count($rows);$i++){
      if($rows[$i]->metakey){
        $rows[$i]->metakey = explode(",", $rows[$i]->metakey);
      }else{
        $rows[$i]->metakey = array("");
      }
      $rows[$i]->edit_url = "index.php?option=com_menus&task=item.edit&id={$rows[$i]->id}";
    }
    return $rows;
  }
    
  public function saveMetatags($ids
            ,$metatitles
            ,$metadescriptions
            ,$metakeys
            ,$title_tags=null		
            ){
  }
  public function saveKeywords($keys, $id, $itemTypeId=null){
  }

  public function copyKeywordsToTitle($ids){
  }
	
  public function copyTitleToKeywords($ids){
  }
	
  public function copyItemTitleToTitle($ids){
    }
    
  public function copyItemTitleToKeywords($ids){
    }
    
    public function GenerateDescriptions($ids){
    }
	
  public function getFilter(){
    $db = JFactory::getDBO();
    
    $search = JRequest::getVar("filter_search", "");
    $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
    $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
    $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");

    $result =  'Filter:                        
    <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter Menu ID"/> 
  <button id="Go" onclick="this.form.submit();">Go</button> 
  <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>
                  
  &nbsp;&nbsp;&nbsp;';
          $sql = "SELECT menutype, title from #__menu_types ORDER BY title";
          $db->setQuery($sql);
          $sections = $db->loadObjectList();
          
  $result .= '<select name="filter_menu_type" id="filter_menutype" class="inputbox" size="1" onchange="document.adminForm.submit();">';
          
  foreach($sections as $section){
          $result .= '<option value="'.$section->menutype.'" '.($menu_type==$section->menutype?'selected="true"':'').'>'.$section->title.'</option>';
  }
  
  
  $result .= '</select>
  
  
  <br/>
  <label>Show only Menu Items with empty keywords</label>
  <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_keywords" '.($com_content_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>                
  <label>Show only Menu Items with empty descriptions</label>
  <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_descriptions" '.($com_content_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
  return $result;
   
  }
	
	public function getItemData($id){
		$db = JFactory::getDBO();
		$sql = "SELECT c.id as id, c.{$this->getField()} as title, 
		( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        ) as metakeywords,
         m.description as metadescription, m.title as metatitle 
         FROM 
        #__menu c
        LEFT JOIN
        #__seoboss_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE c.id=".$db->quote($id);
		$db->setQuery($sql);
		return $db->loadAssoc();
	}
	
	
  public function setItemData($id, $data){
  }
  
  public function getTypeId(){
    return $this->code;
  }
  
  function getMetadataByRequest($query){
    $result = null;
    return $result;
  }
    
  public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($query, $params);
      if( isset($params["Itemid"]) && $params["Itemid"]){
        $this->setMetadata($params["Itemid"], $data);
      }
    }
  
  private $field=null;
        private function getField(){
          if($this->field==null){
	        jimport("joomla.version");
	        $version = new JVersion();
                if($version->RELEASE=="1.5"){
                  $this->field = "name";
                }else{
                  $this->field = "title";
                }
          }
          return $this->field;
        } 
   public function isAvailable(){
     return true;
   }     
}
?>
