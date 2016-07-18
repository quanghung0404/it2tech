<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
 
  //new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
 
class MusColHelper{
	
	var $itemid;
	
static function show_stars($points,$admin = false,$album_id = false,$ajax=false,$small=false){
		$grey="";
		$show_small = "";
		$return = "";
		if($ajax) $funcio = "puntua";
		else $funcio = "canvia_estrelles";
		/*
		if($admin) $text = "Owner's Rating";
		else $text = "User's average Rating";
		*/
		if($small) $show_small = "_small";
		
		$points = round($points * 2) / 2;
		
		for($i=1;$i<6;$i++){
			if($i > $points){
				$grey = "_grey";
				if($points > ($i-1)) $grey = "_half_grey";
			}
			
			$image_attr = array(
								"title" => "$points ".JText::_('out of')." 5"
								);
			
			$image_attr_java = array(
								"title" => "$points ".JText::_('out of')." 5" ,
								"id" => "star".$i."_".$album_id ,
								//"onclick" => $funcio."($i,$album_id);" ,
								"onmouseover" => "stars($i,$album_id);"
								);
			
			if($album_id) {
				$image_attr = $image_attr_java;
				$return .= "<a href='".JURI::root(true)."/index.php?option=com_muscol&task=rate&album_id=$album_id&points=$i&type=album'>".JHTML::image('components/com_muscol/assets/images/star' . $grey.$show_small. '.png' , "$i ".JText::_('out of')." 5" , $image_attr )."</a>";
	
			}
			else{
				$return .= JHTML::image('components/com_muscol/assets/images/star' . $grey.$show_small. '.png' , "$points ".JText::_('out of')." 5" , $image_attr );
			}
		}
		return $return;
	}
	
	static function show_stars_admin($points,$admin = false,$album_id = false,$ajax=false,$small=false){
		$grey="";//"_".$points;
		$src = "";
		$show_small = "";
		$return = "";
		if($ajax) $funcio = "puntua";
		else $funcio = "canvia_estrelles";
		
		if($admin) $src = "../";
		if($small) $show_small = "_small";
		
		for($i=1;$i<6;$i++){
			if($i > $points) $grey="_grey";
			$java = "id='star".$i."_".$album_id."' onclick='".$funcio."($i,$album_id);' onmouseover='stars($i,$album_id);' ";
			$add_java = "";
			if($album_id) $add_java = $java;
			$return .= "<img ".$add_java."src='".$src."components/com_muscol/assets/images/star".$grey.$show_small.".png'/>";
		}
		return $return;
	}
	
	static function show_stars_song($points,$admin = false,$album_id = false,$ajax=false,$small=false){
		$grey="";
		$show_small = "";
		$return = "";
		if($ajax) $funcio = "puntua";
		else $funcio = "canvia_estrelles";
		/*
		if($admin) $text = "Owner's Rating";
		else $text = "User's average Rating";
		*/
		if($small) $show_small = "_small";
		
		$points = round($points * 2) / 2;
		
		for($i=1;$i<6;$i++){
			if($i > $points){
				$grey = "_grey";
				if($points > ($i-1)) $grey = "_half_grey";
			}
			
			$image_attr = array(
								"title" => "$points ".JText::_('out of')." 5"
								);
			
			$image_attr_java = array(
								"title" => "$points ".JText::_('out of')." 5" ,
								"id" => "star".$i."_".$album_id ,
								//"onclick" => $funcio."($i,$album_id);" ,
								"onmouseover" => "stars($i,$album_id);"
								);
			
			if($album_id) {
				$image_attr = $image_attr_java;
				$return .= "<a href='".JURI::root(true)."/index.php?option=com_muscol&task=rate_song&album_id=$album_id&points=$i&type=song'>".JHTML::image('components/com_muscol/assets/images/star' . $grey.$show_small. '.png' , "$i ".JText::_('out of')." 5" , $image_attr )."</a>";
	
			}
			else{
				$return .= JHTML::image('components/com_muscol/assets/images/star' . $grey.$show_small. '.png' , "$points ".JText::_('OUT_OF')." 5" , $image_attr );
			}
		}
		return $return;
	}
	
	static function li_inicial($lletra,$selected,$pos){
		$cadena = "";
		if($selected){
			if($pos=="") $cadena .= "<li class='selected'>";
			else if($pos=="left") $cadena .= "<li class='left_selected'>";
			else if($pos=="right") $cadena .= "<li class='right_selected'>";
		}
		else{
			if($pos=="") $cadena .= "<li>";
			else if($pos=="left") $cadena .= "<li class='left'>";
			else if($pos=="right") $cadena .= "<li class='right'>";
		}
		
		$link = JRoute::_( 'index.php?option=com_muscol&view=artists&letter='. substr($lletra,0,1) . $this->itemid);
		$cadena .= "<a href='".$link."'>".$lletra." </a></li>\n";
		return $cadena;
	}
	
	static function month_name($month){
		
		if(!$month) return "" ;
		$month_array = array(
			 1 => JText::_( "January" ), 
			 2 => JText::_( "February" ), 
			 3 => JText::_( "March" ), 
			 4 => JText::_( "April" ), 
			 5 => JText::_( "May" ), 
			 6 => JText::_( "June" ), 
			 7 => JText::_( "July" ), 
			 8 => JText::_( "August" ), 
			 9 => JText::_( "September" ), 
			 10 => JText::_( "October" ), 
			 11 => JText::_( "November" ), 
			 12 => JText::_( "December" ) );
		return $month_array[$month];
	}
	
	static function letter_navigation($inicial){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		if($params->get('letterbartype') == 'old'){
	
			$return .= "<ul class='inicials'>";
	
			$inicials = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","123");
			
			if($inicials[0] != $inicial) $return .= MusColHelper::li_inicial($inicials[0],false,"left");
			else $return .= MusColHelper::li_inicial($inicials[0],true,"left");
			
			for($i=1;$i<sizeof($inicials)-1;$i++){
				if($inicials[$i] != $inicial) $return .= MusColHelper::li_inicial($inicials[$i],false,"");
				else $return .= MusColHelper::li_inicial($inicials[$i],true,"");
				
			}
			
			if(substr($inicials[sizeof($inicials)-1], 0, 1) != $inicial) 
				$return .= MusColHelper::li_inicial($inicials[sizeof($inicials)-1],false,"right");
			else $return .= MusColHelper::li_inicial($inicials[sizeof($inicials)-1],true,"right");
	
			$return .= "</ul>";
		
		}
		else{
			$return = MusColHelper::new_letter_navigation($inicial);
		}
		
		return $return;
	}
	
	static function new_letter_navigation($inicial){
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'alphabets.php');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$characters = MusColAlphabets::get_characters();
		
		$inicials 	= $characters['internal'] ;
	
		$change 	= $characters['external'] ;
		
		$width = count($change) ;
		
		$width = round( 100 / $width, 1 );
		
		$return = "";
		
		for($i = 0, $n = count($inicials); $i<$n; $i++){
			
			$lletra = $inicials[$i] ;
			
			if($lletra == $inicial) $class = "active" ;
			else $class = "";
			
			$lletra = $inicials[$i] ;
			
			if($i == 0) $class .= " first";
			if($i == ($n -1)){
				$class .= " last";
			}
			
			$link = JRoute::_( 'index.php?option=com_muscol&view=artists&letter='. $lletra . $itemid);
			$cadena = "<a href='".$link."'>".$change[$i]." </a>\n";
			
			$return .= "<td class='".$class."' width='".$width."%'>".$cadena."</td>";
		}
		
		$return = "<table class='table_letterbar' width='100%' cellpadding='0' cellspacing='0' border='0'><tr>".$return."</tr></table>" ;
		
		return $return ;
		
	}
	
	
	static function time_to_string($total_time){
	 
	  $segons = $total_time % 60;
	  
	  if($segons < 10) $segons = "0".$segons;
	  
	  $minuts = ($total_time - $segons)/60;
	  
	  if($minuts >= 60){
	  $minuts_60 = $minuts % 60;
	  $hores = ($minuts - $minuts_60)/60;
	   if($minuts_60 < 10) $minuts_60 = "0".$minuts_60;
	  }
	  else $hores=0;
	  
	  if($hores>0){
	  $total_time = $hores.":".$minuts_60.":".$segons;
	  }
	  else $total_time = $minuts.":".$segons;
	  
	  return $total_time;
	}
	
	static function time_to_array($total_time){
	 
	  $segons = $total_time % 60;
	  
	  $minuts = ($total_time - $segons)/60;
	  
	  if($minuts >= 60){
	  $minuts_60 = $minuts % 60;
	  $hores = ($minuts - $minuts_60)/60;
	  }
	  else {
		  $hores=0;
		  $minuts_60 = $minuts;
	  }
	  
	  $return["hours"] = $hores;
	  $return["minuts"] = $minuts_60;
	  $return["seconds"] = $segons;	  
	  
	  return $return;
	}
	
	static function createThumbnail($file, $alt, $width, $image_attr = array(), $params = array()){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('thumbs_mode')){
			
			return MusColHelper::image(MusColHelper::getThumbnailSrc($file, $width), $alt , $image_attr );
			
		}else{
			if($width < 60) {
				return MusColHelper::image('images/albums/thumbs_40/' . $file, $alt , $image_attr );
				
			}
			else {
				return MusColHelper::image('images/albums/thumbs_115/' . $file, $alt , $image_attr );
				
			}
		}
		
	}
	
	static function getThumbnailSrc($file, $width, $height = ""){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$mainframe = JFactory::getApplication();
		
		$system = false ;
		$cache = $params->get('cache', 1) ;
		
		if($system)
		return 'index.php?option=com_muscol&task=thumbnail&type=album&file=' . $file .'&width=' . $width .'&height=' . $height ;
		elseif($cache && $file){
			require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'graphics.php');
			if($mainframe->isSite()) return MusColGraphics::renderThumb ($file , $width , $height ) ;
			else return MusColGraphics::renderThumb ($file , $width , $height ) ;
		}
		else
		return 'components/com_muscol/helpers/image.php?file=' .JPATH_SITE.DS.'images'.DS.'albums'.DS. $file .'&width=' . $width .'&height=' . $height ;
		
	}
	
	static function getArtistThumbnailSrc($file, $width, $height = ""){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$mainframe = JFactory::getApplication();
		
		$system = false ;
		$cache = $params->get('cache', 1) ;
		
		if($system)
		return 'index.php?option=com_muscol&task=thumbnail&type=artist&file=' . $file .'&width=' . $width .'&height=' . $height ;
		elseif($cache && $file){
			require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'graphics.php');
			if($mainframe->isSite()) return MusColGraphics::renderThumb ($file , $width , $height, "artist" ) ;
			else return MusColGraphics::renderThumb ($file , $width , $height , "artist" );
		}
		else
		return 'components/com_muscol/helpers/image.php?file=' .JPATH_SITE.DS.'images'.DS.'artists'.DS. $file .'&width=' . $width .'&height=' . $height ;
		
	}
	
	static function createThumbnailArtist($file, $alt, $width, $image_attr = array(), $params = array()){
		
		
		return MusColHelper::image(MusColHelper::getArtistThumbnailSrc($file, $width), $alt , $image_attr );
		
		
	}
	
	static function createThumbnailArtistWH($file, $alt, $width, $height, $image_attr = array(), $params = array()){
		
		
		return MusColHelper::image(MusColHelper::getArtistThumbnailSrc($file, $width, $height), $alt , $image_attr );
		
		
	}
	
	static function createThumbnailWH($file, $alt, $width, $height, $image_attr = array(), $params = array()){
		
		
		return MusColHelper::image(MusColHelper::getThumbnailSrc($file, $width, $height), $alt , $image_attr );
		
		
	}
	
	static function image($file, $alt , $image_attr){
		if (is_array($image_attr)) {
			$image_attr = JArrayHelper::toString($image_attr);
		}
		
		$mainframe = JFactory::getApplication();
		if(!$mainframe->isSite()){
			$uri = JURI::base(false).'/';
			$file = $uri . $file ;	
			$file = str_replace("administrator/", "", $file) ;
		}
		
		
		return '<img src="'.$file.'" alt="'.$alt.'" '.$image_attr.' />';
	}
	
	
	static function showMusColFooter(){
		require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'version.php');
		return MusColVersion::show_footer();
	}
	
	static function create_file_link($song){
		
		$file_link = JRoute::_( 'index.php?option=com_muscol&view=file&format=raw&id='. $song->id );
		
		$uri = JFactory::getURI();
		$params =JComponentHelper::getParams( 'com_muscol' );

		$dirname = $params->get('songspath');
		if(substr($dirname, 0, 1) == "/") $dirname = substr($dirname, 1);
		if(substr($dirname, -1) != "/") $dirname = $dirname . "/";
		
		$base_path = $params->get('songsserver');
		
		if($base_path == "") $base_path = $uri->base() ;
		
		if(substr($base_path, -1) != "/") $base_path = $base_path . "/";
		
		$song_base = $base_path . $dirname ;
		
		$song->filename = str_replace($song_base, "", $song->filename) ;
			
		if(strpos($song->filename,"://")){
			
			if(!strpos($song->filename,"://")) $song_path_complet = $song_base . $song->filename ;
			else $song_path_complet = $song->filename ;
			
			$file_link = $song_path_complet ;
		}
		return $file_link;
	}
	
	static function artist_tabs($artist_id, $scope = "albums", $options = array()){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$db = JFactory::getDBO();
		$query = 	' SELECT COUNT(*) '.
					' FROM #__muscol_albums '.
					' WHERE artist_id = '.$artist_id
					;
		$db->setQuery( $query );
		$num_albums = $db->loadResult();	
		
		$query = 	' SELECT COUNT(*) '.
					' FROM #__muscol_songs as s '.
					' WHERE s.artist_id = '.$artist_id
					;
		$db->setQuery( $query );
		$num_songs = $db->loadResult();
		
		switch($scope){
			case "songs":
			$link_albums = MusColHelper::routeArtist($artist_id);
			
			$return = '	<dt class="closed tab_albums"><a href="'.$link_albums.'" title="'.JText::_('VIEW_ALBUMS_ARTIST').'">'.JText::_('Albums').' ('.$num_albums.')</a></dt>
						<dt class="open tab_songs">'.JText::_('Songs').' ('.$num_songs.')</dt>' ;
			break;
			case "albums": default:
			$link_songs = MusColHelper::routeSongs($artist_id);
			$return = '	<dt class="open tab_albums">'.JText::_('Albums').' ('.$num_albums.')</dt>
						<dt class="closed tab_songs"><a href="'.$link_songs.'" title="'.JText::_('VIEW_SONGS_ARTIST').'">'.JText::_('Songs').' ('.$num_songs.')</a></dt>' ;
			break;
		}
		
		$return = '
			<div class="view_songs">
				<dl class="tabs">
				'.$return.'
				</dl>
			</div>';
			
		return $return ;
	}
	
	static function format_dropdown($list = false, $value_selected = false, $fieldname = 'format_id'){
		if(!$list) $list = MusColHelper::getFormatList() ;
		if(!$value_selected) $value_selected = JRequest::getVar('format_id') ;
		
		$return = "" ;
		
		 foreach($list as $format){ 
			if($format->id == $value_selected) $selected = "selected"; else $selected = ""; 
			$return .= '<option value="'. $format->id.'" '.$selected.'>'.$format->format_name.'</option>' ;
		 } 
                        
		$return = '<select name="'.$fieldname.'" class="chzn-select span2">
						<option value="">'. JText::_('ALL_FORMATS').'</option>
						'.$return.'
					</select>' ;
		return $return;
	}
	
	static function artist_dropdown($list = false, $value_selected = false, $fieldname = 'artist_id', $user_id = 0){
		if(!$list) $list = MusColHelper::getArtistList($user_id) ;
		if(!$value_selected) $value_selected = JRequest::getVar('artist_id') ;
		
		$return = "" ;
		
		 foreach($list as $artist){ 
			if($artist->id == $value_selected) $selected = "selected"; else $selected = ""; 
			$return .= '<option value="'. $artist->id.'" '.$selected.'>'.$artist->artist_name.'</option>' ;
		 } 
                        
		$return = '<select name="'.$fieldname.'" class="chzn-select span2">
						<option value="">'. JText::_('ALL_ARTISTS').'</option>
						'.$return.'
					</select>' ;
		return $return;
	}
	
	static function type_dropdown($list = false, $value_selected = false, $fieldname = 'type_id'){
		if(!$list) $list = MusColHelper::getTypeList() ;
		if(!$value_selected) $value_selected = JRequest::getVar('type_id') ;
		
		$return = "" ;
		
		 foreach($list as $type){ 
			if($type->id == $value_selected) $selected = "selected"; else $selected = ""; 
			$return .= '<option value="'. $type->id.'" '.$selected.'>'.$type->type_name.'</option>' ;
		 } 
                        
		$return = '<select name="'.$fieldname.'" class="chzn-select span2">
						<option value="">'. JText::_('ALL_TYPES').'</option>
						'.$return.'
					</select>' ;
		return $return;
	}
	
	static function tag_dropdown($list = false, $value_selected = false, $fieldname = 'tag_id'){
		if(!$list) $list = MusColHelper::getTagList() ;
		if(!$value_selected) $value_selected = JRequest::getVar('tag_id') ;
		
		$return = "" ;
		
		 foreach($list as $tag){ 
			if($tag->id == $value_selected) $selected = "selected"; else $selected = ""; 
			$return .= '<option value="'. $tag->id.'" '.$selected.'>'.$tag->tag_name.'</option>' ;
		 } 
                        
		$return = '<select name="'.$fieldname.'" class="chzn-select span2">
						<option value="">'. JText::_('ALL_TAGS').'</option>
						'.$return.'
					</select>' ;
		return $return;
	}
	
	static function genre_dropdown($list = false, $value_selected = false,  $fieldname = 'genre_id'){
		if(!$list) $list = MusColHelper::getGenresData() ;
		if(!$value_selected) $value_selected = JRequest::getVar('genre_id') ;
		            
		$return = '<select name="'.$fieldname.'" class="chzn-select span2">
						<option value="">'. JText::_('ALL_GENRES').'</option>
						'.MusColHelper::show_genre_tree($list,0, $value_selected).'
					</select>' ;
		return $return;
	}
	
	static function orderby_dropdown($value_selected = false, $fieldname = 'orderby', $submit_on_change = true){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$return = "" ;
		
		$list['year_asc'] 	= JText::_('YEAR_ASC') ;
		$list['year_desc'] 	= JText::_('YEAR_DESC') ;
		$list['name_asc'] 	= JText::_('NAME_ASC') ;
		$list['name_desc'] 	= JText::_('NAME_DESC') ;
		$list['date_asc'] 	= JText::_('DATE_ADDED_ASC') ;
		$list['date_desc'] 	= JText::_('DATE_ADDED_DESC') ;
			
		if(!$value_selected) $value_selected = JRequest::getVar('orderby') ;
		if(!$value_selected) $value_selected = $params->get('orderby_search', 'date_desc') ;
		
		 foreach($list as $key => $value){ 
			if($key == $value_selected) $selected = "selected"; else $selected = ""; 
			$return .= '<option value="'. $key.'" '.$selected.'>'.$value.'</option>' ;
		 } 
		 
		if($submit_on_change) $submit_on_change = 'onchange="this.form.submit();"' ;
		else $submit_on_change = "" ;
                        
		$return = '<select name="'.$fieldname.'" '.$submit_on_change.'  class="chzn-select">
					'.$return.'
					</select>' ;
		return $return;
	}
	
	static function getGenresData(){
		
		$db = JFactory::getDBO();
			
		$query = ' SELECT * FROM #__muscol_genres '.
				 ' WHERE parents = "0" '
				 ;
		$db->setQuery( $query );
		$genres_data = $db->loadObjectList();
		
		for($i = 0; $i < count( $genres_data ) ; $i++){
			$genres_data[$i]->sons = MusColHelper::get_descendants($genres_data[$i]);	
		}
			
			
		return $genres_data;
	
	}
	
	static function get_descendants($genre){
		
		$db = JFactory::getDBO();

		$query = 	' SELECT * FROM #__muscol_genres '.
					' WHERE '.
					' ( parents LIKE "%,'.$genre->id.',%"'.
							' OR parents LIKE "'.$genre->id.',%" '.
							' OR parents LIKE "%,'.$genre->id.'" '.
							' OR parents LIKE "'.$genre->id.'" ) '
					;
		$db->setQuery( $query );
		$return = $db->loadObjectList();

		if(!empty( $return )){
			for($i = 0; $i < count( $return ) ; $i++){
				$return[$i]->sons = MusColHelper::get_descendants($return[$i]);	
			}

		}
		
		return $return;
		
	}
	
	static function show_genre_tree($genres,$level, $selected_genre_id){
		
		$return = "" ;
		
		for($i = 0; $i < count($genres); $i++){
			$return .= MusColHelper::render_option($genres[$i]->id,$genres[$i]->genre_name,$level, $selected_genre_id);
			$level ++;
			if(!empty($genres[$i]->sons)){
				$return .= 	MusColHelper::show_genre_tree($genres[$i]->sons,$level, $selected_genre_id);
			}
			$level --;
		}
		//echo $return;
		return $return;
		
	}
	
	static function render_option($id, $name, $level, $selected_genre_id){
		$indent = "";
		
		for($i = 0; $i < $level; $i++){
			$indent .= "&nbsp;&nbsp;";	
		}
		
		$selected = ""; 
		if( $id == $selected_genre_id ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
	
	static function getFormatList(){
		
		$db = JFactory::getDBO();
		$query = 	' SELECT id,format_name FROM #__muscol_format '.
					' WHERE display_group = 0 ' .
					' ORDER BY order_num ' ;
		$db->setQuery( $query );
		$format_list = $db->loadObjectList();
				
		return $format_list;
	
	}
	
	static function getArtistList($user_id = 0){
		
		$where = "" ;
		
		if($user_id) $where = ' WHERE user_id = ' . $user_id ;
		
		$db = JFactory::getDBO();
		$query = 	' SELECT id,artist_name FROM #__muscol_artists '.
					$where .
					' ORDER BY letter,class_name ' ;
		$db->setQuery( $query );
		$artist_list = $db->loadObjectList();

		return $artist_list;
	
	}
	
	static function getTypeList(){
		$db = JFactory::getDBO();
		$query = 	' SELECT * FROM #__muscol_type '.
					' ORDER BY type_name ' ;
		$db->setQuery( $query );
		$type_list = $db->loadObjectList();
			
		return $type_list;
	
	}
	
	static function getTagList(){
		
		$db = JFactory::getDBO();
			
		$query = 	' SELECT * FROM #__muscol_tags '.
					' ORDER BY tag_name ' ;
		$db->setQuery( $query );
		$tag_list = $db->loadObjectList();
			
		return $tag_list;
	
	}
	
	static function searchalbums_form_content($genre_list, $itemid, $options = array()){
		
		if(empty($options)){
			$params = JComponentHelper::getParams( 'com_muscol' );
			
			$options['showsearchword'] 	= $params->get('showsearchwordalbumsearch', 1) ;
			$options['showgenre'] 		= $params->get('showgenrealbumsearch', 1) ;
			$options['showformat'] 		= $params->get('showformatalbumsearch', 1) ;
			$options['showtype'] 		= $params->get('showtypealbumsearch', 1) ;
			$options['showtag'] 		= $params->get('showtagalbumsearch', 1) ;
			
			$options['separator']		= " " ;
		
		}
		
		$return = "";
		$return .= $options['showsearchword'] ? '<input type="text" class="inputbox span2" name="searchword" id="keyword_search_input" size="32" maxlength="255" value="'.htmlspecialchars(JRequest::getVar('searchword')).'"/>'.$options['separator'] : '' ;
		$return .= $options['showformat'] ? MusColHelper::format_dropdown().$options['separator'] : '' ;		
		$return .= $options['showgenre'] ? MusColHelper::genre_dropdown($genre_list).$options['separator'] : '' ;				
		$return .= $options['showtype'] ? MusColHelper::type_dropdown().$options['separator'] : '' ;			
		$return .= $options['showtag'] ? MusColHelper::tag_dropdown().$options['separator'] : '' ;
		
		$return .= '<input type="submit" class="btn" value="'. JText::_('SEARCH_ALBUMS').'" />
			
					<input type="hidden" name="option" value="com_muscol" />
					<input type="hidden" name="search" value="albums" />
					<input type="hidden" name="view" value="search" />
					<input type="hidden" name="Itemid" value="'. $itemid.'" />';
		
		return $return ;
	}
	
	static function searchsongs_form_content($genre_list, $itemid, $options = array()){
		
		if(empty($options)){
			$params = JComponentHelper::getParams( 'com_muscol' );
			
			$options['showsearchword'] 	= $params->get('showsearchwordsongsearch', 1) ;
			$options['showgenre'] 		= $params->get('showgenresongsearch', 1) ;
			$options['showartist'] 		= $params->get('showartistsongsearch', 1) ;
			$options['showtag'] 		= $params->get('showtagsongsearch', 1) ;
			
			$options['separator']		= " " ;
		
		}
		
		$return = "";
		
		$return .= $options['showsearchword'] ? '<input type="text" class="inputbox span2" name="searchword" id="keyword_search_input" size="13" maxlength="255" value="'.htmlspecialchars(JRequest::getVar('searchword')).'"/>'.$options['separator'] : '' ;
		$return .= $options['showartist'] ? MusColHelper::artist_dropdown().$options['separator'] : '' ;		
		$return .= $options['showgenre'] ? MusColHelper::genre_dropdown($genre_list).$options['separator'] : '' ;				
		$return .= $options['showtag'] ? MusColHelper::tag_dropdown().$options['separator'] : '' ;

		$return .= '<input type="submit" class="btn" value="'. JText::_('SEARCH_SONGS').'" />
			
					<input type="hidden" name="option" value="com_muscol" />
					<input type="hidden" name="search" value="songs" />
					<input type="hidden" name="view" value="search" />
					<input type="hidden" name="Itemid" value="'. $itemid.'" />';
		
		return $return ;
	}
	
	static function edit_button_song($id){
		$user = JFactory::getUser();
		
		if(!$user->id) return "";
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT user_id FROM #__muscol_songs WHERE id = ' .$id;
		$db->setQuery( $query ) ;
		$owner = $db->loadResult();
		
		if($user->id != $owner) return "";
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$return = '
			<div class="btn-group pull-right">
			  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
			  <ul class="dropdown-menu">
				<li><a class="muscol_edit" href="'.JRoute::_('index.php?option=com_muscol&view=song&layout=form&id=' . $id . $itemid).'"><i class="icon-pencil"></i> '.JText::_('EDIT_THIS_SONG').'</a></li>
				
			  </ul>
			</div><br />
			';
			
			return $return ;
		
		//return "<div class='muscol_edit'><a href='".JRoute::_('index.php?option=com_muscol&view=song&layout=form&id=' . $id . $itemid)."' title='".JText::_('EDIT_THIS_SONG')."'>" . JHTML::image('components/com_muscol/assets/images/page_white_edit.png', JText::_('Edit')) . "</a></div>";
	}
	
	static function edit_button_album($id){
		$user = JFactory::getUser();
		
		if(!$user->id) return "";
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT user_id FROM #__muscol_albums WHERE id = ' .$id;
		$db->setQuery( $query ) ;
		$owner = $db->loadResult();
		
		if($user->id != $owner) return "";
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$return = '
			<div class="btn-group pull-right">
			  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
			  <ul class="dropdown-menu">
				<li><a class="muscol_edit" href="'.JRoute::_('index.php?option=com_muscol&view=album&layout=form&id=' . $id . $itemid).'"><i class="icon-pencil"></i> '.JText::_('EDIT_THIS_ALBUM').'</a></li>
				<li><a href="'.JRoute::_('index.php?option=com_muscol&task=erase_album&id=' . $id ).'"><i class="icon-trash"></i> '.JText::_('DELETE_THIS_ALBUM').'</a></li>
			  </ul>
			</div>
			';
			
		return $return ;
		
		//return "<div class='muscol_edit'><a href='".JRoute::_('index.php?option=com_muscol&view=album&layout=form&id=' . $id . $itemid)."' title='".JText::_('EDIT_THIS_ALBUM')."'>" . JHTML::image('components/com_muscol/assets/images/page_white_edit.png', JText::_('Edit')) . "</a> <a href='".JRoute::_('index.php?option=com_muscol&task=erase_album&id=' . $id )."' title='".JText::_('DELETE_THIS_ALBUM')."'>" . JHTML::image('components/com_muscol/assets/images/delete.png', JText::_('Delete')) . "</a></div>";
	}
	
	static function edit_button_artist($id){
		$user = JFactory::getUser();
		
		if(!$user->id) return "";
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT user_id FROM #__muscol_artists WHERE id = ' .$id;
		$db->setQuery( $query ) ;
		$owner = $db->loadResult();
		
		if($user->id != $owner) return "";
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$return = '
			<div class="btn-group pull-right">
			  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
			  <ul class="dropdown-menu">
				<li><a class="muscol_edit" href="'.JRoute::_('index.php?option=com_muscol&view=artist&layout=form&id=' . $id . $itemid).'"><i class="icon-pencil"></i> '.JText::_('EDIT_THIS_ARTIST').'</a></li>
				<li><a href="'.JRoute::_('index.php?option=com_muscol&task=erase_artist&id=' . $id ).'"><i class="icon-trash"></i> '.JText::_('DELETE_THIS_ARTIST').'</a></li>
			  </ul>
			</div>
			';
			
		return $return ;
		
		//return "<div class='muscol_edit'><a href='".JRoute::_('index.php?option=com_muscol&view=artist&layout=form&id=' . $id . $itemid)."' title='".JText::_('EDIT_THIS_ARTIST')."'>" . JHTML::image('components/com_muscol/assets/images/page_white_edit.png', JText::_('Edit')) . "</a> <a href='".JRoute::_('index.php?option=com_muscol&task=erase_artist&id=' . $id )."' title='".JText::_('DELETE_THIS_ARTIST')."'>" . JHTML::image('components/com_muscol/assets/images/delete.png', JText::_('Delete')) . "</a></div>";
	}
	
	static function getSongFileURL($song){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$uri = JFactory::getURI();
		
		$dirname = $params->get('songspath');
		if(substr($dirname, 0, 1) == "/") $dirname = substr($dirname, 1);
		if(substr($dirname, -1) != "/") $dirname = $dirname . "/";
		
		$base_path = $params->get('songsserver');
		if($base_path == "") $base_path = $uri->base() ;
		if(substr($base_path, -1) != "/") $base_path = $base_path . "/";
		$song_base = $base_path . $dirname ;
		
		if(!strpos($song->filename,"://")) $song_path_complet = $song_base . $song->filename ;
		else $song_path_complet = $song->filename ;
		
		return $song_path_complet ;
		
	}
	
	static function getSongFileURLslashes($song){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$uri = JFactory::getURI();
		
		$song->filename = addslashes($song->filename);
		
		$dirname = $params->get('songspath');
		if(substr($dirname, 0, 1) == "/") $dirname = substr($dirname, 1);
		if(substr($dirname, -1) != "/") $dirname = $dirname . "/";
		
		$base_path = $params->get('songsserver');
		if($base_path == "") $base_path = $uri->base() ;
		if(substr($base_path, -1) != "/") $base_path = $base_path . "/";
		$song_base = $base_path . $dirname ;
		
		if(!strpos($song->filename,"://")) $song_path_complet = $song_base . $song->filename ;
		else $song_path_complet = $song->filename ;
		
		return $song_path_complet ;
		
	}
	
	static function show_bookmarks(){
		require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'bookmarks.php');
		
		return MusColBookmarks::show_bookmarks();
	}
	
	static function get_genre_link($genre_id){
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		return JRoute::_( 'index.php?option=com_muscol&view=search&search=albums&genre_id='. $genre_id  . $itemid);	
	}
	
	static function routeArtist($id, $itemid = false){
		if(!$itemid){
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');	
		}
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		return JRoute::_( 'index.php?option=com_muscol&view=artist&id='. $id  . $itemid);	
	}
	
	static function routeAlbum($id, $itemid = false){
		if(!$itemid){
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');	
		}
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		return JRoute::_( 'index.php?option=com_muscol&view=album&id='. $id  . $itemid);	
	}
	
	static function routeSong($id, $itemid = false){
		if(!$itemid){
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');	
		}
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		return JRoute::_( 'index.php?option=com_muscol&view=song&id='. $id  . $itemid);	
	}
	
	static function routeSongs($id, $itemid = false){
		if(!$itemid){
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');	
		}
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		return JRoute::_( 'index.php?option=com_muscol&view=songs&id='. $id  . $itemid);	
	}
	
	static function ID3active(){
		
		$params =JComponentHelper::getParams( 'com_muscol' );

		if($params->get('id3')){
	
			if ( function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()) {
				//die('magic_quotes_runtime is enabled, getID3 will not run.');
				return false ;
			}
			if ( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
				//die('magic_quotes_gpc is enabled, getID3 will not run.');
				return false ;
			}
			
			return true ;
		
		}
		
		return false ;
		
	}
	
	static function getID3data($data, $filename){
		$new_data = $data ;
		
		if($filename){
			// include getID3() library (can be in a different directory if full path is specified)
			require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'libraries'.DS.'getid3'.DS.'getid3'.DS.'getid3.php');
			
			// Initialize getID3 engine
			$getID3 = new getID3;
	
			$ThisFileInfo = $getID3->analyze($filename);
			/*
			print_r($ThisFileInfo) ; 
			print_r($new_data) ; die;
			*/
			
			if(isset($ThisFileInfo['tags']['id3v2'])){
				//general id3v2
				
				$root = $ThisFileInfo['tags']['id3v2'] ;
				
				if(!$data['name']) 			$new_data['name'] = 		$root['title'][0] ;
				$new_data['artist_name'] = 	$root['artist'][0] ;
				$new_data['album_name'] = 	$root['album'][0] ;
				$new_data['genre_name'] = 	$root['genre'][0] ;
				if(!$data['num']) 			$new_data['num'] = 		$root['track_number'][0] ;
				$new_data['position'] = 	$new_data['num'] ;
				$new_data['year'] = 		$root['year'][0] ;
				
				if(!$data['lyrics']) 			$new_data['lyrics'] = 		str_replace("\n", "<br />", $root['unsynchronised_lyric'][0]) ;
				
				
			}
			elseif(isset($ThisFileInfo['tags']['id3v1'])){
				//general id3v1
				
				$root = $ThisFileInfo['tags']['id3v1'] ;
				
				if(!$data['name']) 			$new_data['name'] = 		$root['title'][0] ;
				$new_data['artist_name'] = 	$root['artist'][0] ;
				$new_data['album_name'] = 	$root['album'][0] ;
				$new_data['genre_name'] = 	$root['genre'][0] ;
				if(!$data['num']) 			$new_data['num'] = 		$root['track'][0] ;
				$new_data['position'] = 	$new_data['num'] ;
				$new_data['year'] = 		$root['year'][0] ;
				
				
			}
			elseif(isset($ThisFileInfo['tags']['quicktime'])){
				//general Quicktime encoding (iTunes)
				
				$root = $ThisFileInfo['tags']['quicktime'] ;
				
				if(!$data['name']) 			$new_data['name'] = 		$root['title'][0] ;
				$new_data['artist_name'] = 	$root['artist'][0] ;
				$new_data['album_name'] = 	$root['album'][0] ;
				$new_data['genre_name'] = 	$root['genre'][0] ;
				if(!$data['num']) 			$new_data['num'] = 		$root['track_number'][0] ;
				$new_data['position'] = 	$new_data['num'] ;
				$new_data['year'] = 		$root['creation_date'][0] ;
				
				
				if(!$data['lyrics']) 			$new_data['lyrics'] = 		str_replace("\n", "<br />", $root['lyrics'][0]) ;
				
			}
			
			//length
			if($data['hours'] == 0 && $data['minutes'] == 0 && $data['seconds'] == 0) 		$new_data['length'] = 		(int)($ThisFileInfo['playtime_seconds']);
			
			//image
			if(isset($ThisFileInfo['comments']['picture'][0]['data'])){
				$new_data['image_file']['data'] = $ThisFileInfo['comments']['picture'][0]['data'] ;
				$new_data['image_file']['type'] = $ThisFileInfo['comments']['picture'][0]['image_mime'] ;
			}
			
			//format
			if(!$data['format_id']) $new_data['format_id'] = 1 ;
			
			if(!$new_data['name']) $new_data['name'] = $filename ;
			
			//artist and album name if not on ID3 tags
			if(!$new_data['artist_name']) $new_data['artist_name'] = $new_data['name'] ;
			if(!$new_data['album_name']) $new_data['album_name'] = $new_data['name'] ;
			
			//print_r($ThisFileInfo) ; 
			//print_r($new_data) ; die;
			
			
			if(!$data['genre_id']){
				$genre_id = MusColHelper::getGenreID($new_data) ;
				$new_data['genre_id'] = $genre_id ;
			}
			else{
				$new_data['genre_id'] = $data['genre_id'] ;
			}
			
			if(!$data['artist_id']){
				$artist_id = MusColHelper::getArtistID($new_data) ;
				$new_data['artist_id'] = $artist_id ;
			}
			else{
				$new_data['artist_id'] = $data['artist_id'] ;
			}
			
			if(!$data['album_id']){
				$album_id = MusColHelper::getAlbumID($new_data) ;
				$new_data['album_id'] = $album_id ;
			}
			else{
				$new_data['album_id'] = $data['album_id'] ;
			}
			/*
			print_r($ThisFileInfo) ;
			prin
			*/
		} // end if filename
		return $new_data ;
	}
	
	static function getArtistID($data){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$only_own_artists = $params->get('add_albums_own_artists');
		
		if($only_own_artists && $mainframe->isSite()){
			$query = ' SELECT id FROM #__muscol_artists WHERE artist_name = "'.addslashes($data['artist_name']).'" AND user_id = ' . $user->id;
		}
		else{
			$query = ' SELECT id FROM #__muscol_artists WHERE artist_name = "'.addslashes($data['artist_name']).'" ' ;	
		}
		
		
		$db->setQuery($query);
		$artist_id = $db->loadResult();
		if($artist_id) return $artist_id ;
		else{
			
			$artist['id'] = 0 ;
			$artist['artist_name'] = $data['artist_name'] ;
			$album['genre_id'] = $data['genre_id'] ;
			$album['added'] = date('Y-m-d');
			
			// we create the artist
			require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'models'.DS.'artist.php');
			$model = new ArtistsModelArtist() ;
			$artist_id = $model->store($artist);
			
			$mainframe->enqueueMessage(JText::sprintf('ARTIST_X_CREATED', $artist['artist_name']));
			
			return $artist_id ;
		}
		
	}
	
	static function getAlbumID($data){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($mainframe->isSite()){ //on frontend we can only add on OUR albums
			$query = ' SELECT id FROM #__muscol_albums WHERE name = "'.addslashes($data['album_name']).'" AND artist_id = ' . $data['artist_id'].' AND user_id = ' . $user->id;
		}
		else{
			$query = ' SELECT id FROM #__muscol_albums WHERE name = "'.addslashes($data['album_name']).'" AND artist_id = ' . $data['artist_id'];
		}
		
		$db->setQuery($query);
		$album_id = $db->loadResult();
		if($album_id) return $album_id ;
		else{
			
			$album['id'] = 0 ;
			$album['name'] = $data['album_name'] ;
			$album['artist_id'] = $data['artist_id'] ;
			$album['year'] = $data['year'] ;
			$album['format_id'] = $data['format_id'] ;
			$album['genre_id'] = $data['genre_id'] ;
			$album['added'] = date('Y-m-d');
			
			if(!empty($data['image_file']['data'])){
				
				$im = imagecreatefromstring($data['image_file']['data']);
				
				$rand = rand();
				
				$ext = MusColHelper::getExtensionFromMime($data['image_file']['type']) ;
				
				$tmp_file = JPATH_SITE . DS .'images' . DS .'albums' . DS . $rand . ".".$ext;
				$success = imagejpeg($im, $tmp_file , 100) ;
				
				if($success && file_exists($tmp_file)){
				
					$album['image'] = $rand . ".".$ext ;
					
				}
				
				//print_r($datafiles) ; die;	
				
			}
			
			// we create the album
			require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'models'.DS.'album.php');
			$model = new ArtistsModelAlbum() ;
			$album_id = $model->store_simple($album);
			
			$mainframe->enqueueMessage(JText::sprintf('ALBUM_X_CREATED', $album['name']));
			
			return $album_id ;
		}
		
	}
	
	static function getExtensionFromMime($mime){
		switch($mime){
			case 'image/jpeg':
			$return = 'jpg' ;	
			case 'image/png':
			$return = 'png' ;	
			case 'image/gif':
			$return = 'gif' ;	
			break;
		}
		return $return ;
	}
	
	static function getGenreID($data){
		$db = JFactory::getDBO();
		$query = ' SELECT id FROM #__muscol_genres WHERE genre_name = "'.addslashes($data['genre_name']).'" ' ;
		$db->setQuery($query);
		$genre_id = $db->loadResult();

		if($genre_id) return $genre_id ;
		else{
			
			$genre['id'] = 0 ;
			$genre['genre_name'] = $data['genre_name'] ;
			
			// we create the artist
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'models'.DS.'genre.php');
			$model = new GenresModelGenre() ;
			
			if($data['genre_name']){
				$genre_id = $model->store($genre);
			}
			//print_r($genre_id);die;
			return $genre_id ;
			
		}
		
	}

	static function getTagIDArrayFromString($tagstring){

		$db = JFactory::getDBO();
		$tags = array();

		$tagstring = explode(",", $tagstring);

		foreach($tagstring as $tagname){
			$tags[] = MusColHelper::getTagID($tagname);
		}

		return $tags ;

	}

	static function getTagID($tagname){
		$db = JFactory::getDBO();
		$query = ' SELECT id FROM #__muscol_tags WHERE tag_name = "'.addslashes($tagname).'" ' ;
		$db->setQuery($query);
		$tagid = $db->loadResult();

		if($tagid) return $tagid ;
		else{

			$tag = array();
			$tag['id'] = 0 ;
			$tag['tag_name'] = $tagname ;
			
			// we create the tag
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'models'.DS.'tag.php');
			$model = new TagsModelTag() ;
			$tag_id = $model->store($tag);
			
			return $tag_id ;
			
		}
		
	}

	static function renderTag($tag){
		
		$return = '<a class="muscol_tags" href="'.JRoute::_('index.php?option=com_muscol&view=search&search=albums&tag_id='.$tag->id).'"><span class="label label-info ">'.$tag->tag_name.'</span></a>';
		//$return = '<a class="btn btn-mini btn-info muscol_tags" href="'.JRoute::_('index.php?option=com_muscol&view=search&search=albums&tag_id='.$tag->id).'">'.$tag->tag_name.'</a>';

		return $return ;
	}

	static function renderTagSong($tag){
		
		$return = '<a class="muscol_tags" href="'.JRoute::_('index.php?option=com_muscol&view=search&search=songs&tag_id='.$tag->id).'"><span class="label label-info ">'.$tag->tag_name.'</span></a>';
		//$return = '<a class="btn btn-mini btn-info muscol_tags" href="'.JRoute::_('index.php?option=com_muscol&view=search&search=albums&tag_id='.$tag->id).'">'.$tag->tag_name.'</a>';

		return $return ;
	}

	static function zip_download($album_id){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$db = JFactory::getDBO();
		$query = " SELECT count(id) FROM #__muscol_songs WHERE filename != '' AND album_id = ". $album_id ;
		$db->setQuery($query);
		$aretheresongs = $db->loadResult();
		
		$return = "";
		
		if($aretheresongs) $return = "<p align='center'><a class='btn' href='".JRoute::_('index.php?option=com_muscol&view=file&format=raw&zip=1&id='.$album_id)."' ><i class='icon-download-alt'></i> ".JText::_('DOWNLOAD_ENTIRE_ALBUM')."</a></p>" ;
		
		return $return;
		
	}
	
}