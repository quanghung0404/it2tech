<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');

class TableAlbum extends JTable
{

	var $id = null;

	var $name = null;
	var $artist_id = null;
	var $year = null;
	var $month = null;
	var $format_id = null;
	var $image = null;
	var $ndisc = null;
	var $types = null;
	var $name2 = null;
	var $artist2 = null;
	var $subtitle = null;
	var $subartist = null;
	var $points = null;
	var $added = null;
	var $price = null;
	var $genre_id = null;
	var $tags = null;
	var $review = null;
	var $keywords = null;
	var $edition_year = null;
	var $edition_month = null;
	var $edition_details = null;
	var $edition_country = null;
	var $label = null;
	var $catalog_number = null;
	var $length = null;
	var $part_of_set = null;
	var $show_separately = null;
	var $user_id = null;
	var $hits = null;
	var $buy_link = null;
	var $album_file = null;
	
	var $image_file = null;
	var $name_image_file = null;
	var $artist_image_file = null;
	var $hours = null;
	var $minuts = null;
	var $seconds = null;
	
	var $external_image = null;
	var $use_external_image = null;
	
	var $metakeywords = null;
	var $metadescription = null;

	function TableAlbum(& $db) {
		parent::__construct('#__muscol_albums', 'id', $db);
	}
	
	function check(){
		
		if($this->id == 0){
			$this->added =  date('Y-m-d H:i:s') ;
			
			$user = JFactory::getUser();
			
			$this->user_id = $user->id;
			
			if($this->edition_year == null && $this->edition_month == null ){
				$this->edition_year = $this->year;
				$this->edition_month = $this->month;				
			}

			$artist_keywords = $this->get_artist_keywords($this->artist_id)." ".$this->get_keywords($this->subartist);
			$album_keywords = $this->get_keywords($this->name." ".$this->subtitle);
			$this->keywords = $this->get_keywords($artist_keywords." ".$album_keywords." ".$this->keywords);
		}
		
		$allowed_types = array("image/jpeg", "image/png", "image/gif", "image/jpg");

		if($this->name_image_file["name"] != "" && in_array($this->name_image_file["type"], $allowed_types)){
			$success_image=false;
			$success_image = $this->guardarImatgeNomAlbum($this->name_image_file);
			if($success_image) $this->name2 = $success_image;
		}
		if($this->artist_image_file["name"] != "" && in_array($this->artist_image_file["type"], $allowed_types)){
			$success_image=false;
			$success_image = $this->guardarImatgeNomArtista($this->artist_image_file);
			if($success_image) $this->artist2 = $success_image;
		}
		if($this->image_file["name"] != "" && in_array($this->image_file["type"], $allowed_types)){
			$success_image=false;
			$success_image = $this->guardarImatge($this->image_file);
			if($success_image) $this->image = $success_image;
		}
		elseif($this->use_external_image == "on" && $this->external_image){
			//$external_image = file_get_contents($this->external_image);

			$params = JComponentHelper::getParams( 'com_muscol' );
			  $uri = JFactory::getURI();

			  require (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'assets'.DS.'oauthsimple-master'.DS.'php'.DS.'OAuthSimple.php');
			  $oauthObject = new OAuthSimple('bZaNCebFXkBrvtmiuprD', 'BXopzkqgzQHEUysXloujDuiCodZctmsY');

			  $signatures = array();
			  $signatures['oauth_token'] = $params->get('oauth_token');
		      $signatures['oauth_secret'] = $params->get('access_token_secret');

			$result = $oauthObject->sign(array(
		        'path'      => $this->external_image,
		        'parameters'=> array(),
		        'signatures'=> $signatures));
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
			curl_setopt($ch, CURLOPT_HEADER, $result['header']);
			curl_setopt($ch, CURLOPT_ENCODING , "gzip");
			curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection/2.4 +".$uri->base());
			$external_image = curl_exec ( $ch );
			curl_close($ch);
			
			//MC 2.4.7
			if(!$external_image){

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $this->external_image);
				curl_setopt($ch, CURLOPT_HEADER, $result['header']);
				curl_setopt($ch, CURLOPT_ENCODING , "gzip");
				curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection/2.4 +".$uri->base());
				$external_image = curl_exec ( $ch );
				curl_close($ch);

			}
			
			$pieces = explode("/", $this->external_image);
			$filename = $pieces[count($pieces) - 1] ;
			
			if (file_exists(JPATH_SITE . "/images/albums/" . $filename)){
				  $filename = time()."_".$filename;
			}
			
	 		JFile::write(JPATH_SITE . "/images/albums/" . $filename, $external_image);
			
			$this->image = $filename ;
			
		}
		
		$this->use_external_image = null ;
		$this->external_image = null ;

		$this->tags = implode(",",$this->tags);
		$this->types = implode(",",$this->types);
		
		$this->keywords = $this->get_keywords(" ". $this->keywords . " ");
		
		$this->length = $this->hours * 3600 + $this->minuts * 60 + $this->seconds ;
		$this->hours = null;
		$this->minuts = null;
		$this->seconds = null;
		
		return true;
	}
	
	function get_artist_keywords($artist_id){
		
		$query = " SELECT keywords FROM #__muscol_artists WHERE id = $artist_id LIMIT 1 ";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}

	function get_keywords($keywords){

		$return = JFilterOutput::stringURLSafe($keywords);
		$return = str_replace("-"," ",$return);
		$return = $this->erase_multiple_whitespaces(" ".$return." ");

		return $return;
	}
	

	function erase_multiple_whitespaces($cadena){
		$cadena2 = str_replace("  "," ",$cadena,$times);
		if($times!=0) $cadena2 = $this->erase_multiple_whitespaces($cadena2);
		return $cadena2;
	}
	
	function createThumb( $filename , $path , $ample)
	{
		  // load image and get image size
		  $img = imagecreatefromjpeg(JPATH_SITE . "/images/albums/".$filename);
		  $width = imagesx( $img );
		  $height = imagesy( $img );
	
		  // calculate thumbnail size
		  $new_width = $ample;
		  $new_height = floor( $height * ( $new_width / $width ) );
	
		  // create a new temporary image
		  $tmp_img = imagecreatetruecolor( $new_width, $new_height );
		 
		  // copy and resize old image into new image
		  imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
 
		  // save thumbnail into a file
		  
		  imagejpeg( $tmp_img, $path.$filename, 100 );

	}
	function guardarImatge($file){
		$filename = $file["name"];

	   if ($file["error"] > 0){
		  return false;
		}
	  else{
		if (file_exists(JPATH_SITE . "/images/albums/" . $filename)){
		
			  $filename = time()."_".$filename;
			  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/albums/" . $filename);
			  $this->createThumb($filename,JPATH_SITE . "/images/albums/thumbs_115/",115); // creem la miniatura de 115px
		  	  $this->createThumb($filename,JPATH_SITE . "/images/albums/thumbs_40/",40); // creem la miniatura de 40 px
			  return $filename;
		  }
		else{
		  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/albums/" . $filename);
		  
		  $this->createThumb($filename,JPATH_SITE . "/images/albums/thumbs_115/",115); // creem la miniatura de 115px
		  $this->createThumb($filename,JPATH_SITE . "/images/albums/thumbs_40/",40); // creem la miniatura de 40 px
		  return $filename;
		  }
		}
	  }
	
	
	
	function guardarImatgeNomAlbum($file){
		$filename = $file["name"];

	  if ($file["error"] > 0){

		  return false;
		}
	  else{
		if (file_exists(JPATH_SITE . "/images/album_extra/album_name/" . $filename)){
	
			  $filename = time()."_".$filename;
			  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/album_extra/album_name/" . $filename);

			  return $filename;
		  }
		else{
		  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/album_extra/album_name/" . $filename);
		  return $filename;
		  }
		}
	  }
	
	
	function guardarImatgeNomArtista($file){
		$filename = $file["name"];

	  if ($file["error"] > 0){

		  return false;
		}
	  else{
		if (file_exists(JPATH_SITE . "/images/album_extra/album_name/" . $filename)){
		
			  $filename = time()."_".$filename;
			  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/album_extra/artist_name/" . $filename);
			
			  return $filename;
		  }
		else{
		  move_uploaded_file($file["tmp_name"],JPATH_SITE . "/images/album_extra/artist_name/" . $filename);
		  return $filename;
		  }
		}
	  }
	  
}