<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive.gzip');
//require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'archive'.DS.'gzip.php');


class AlbumsControllerAlbums extends AlbumsController
{
	
	var $new_method = false ;

	function __construct()
	{
		parent::__construct();

	}
	
	// votar amb AJAX
	
	function rate(){ 
 
		 $mainframe = JFactory::getApplication();
		 

		 $id = JRequest::getVar( 'album_id');
		 $points = JRequest::getVar( 'points');
		 $model = $this->getModel('album');
		 $return = $model->rate($id,$points);

		 echo $return;      
		 $mainframe->close();
 
  }
  
  
  function search_discogs(){

  	  error_reporting(E_ERROR);

	  $mainframe = JFactory::getApplication();
	  $params = JComponentHelper::getParams( 'com_muscol' );
	  $uri = JFactory::getURI();

	  require (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'assets'.DS.'oauthsimple-master'.DS.'php'.DS.'OAuthSimple.php');
	  $oauthObject = new OAuthSimple('bZaNCebFXkBrvtmiuprD', 'BXopzkqgzQHEUysXloujDuiCodZctmsY');

	  $signatures = array();
	  $signatures['oauth_token'] = $params->get('oauth_token');
      $signatures['oauth_secret'] = $params->get('access_token_secret');
		 
	 $query = JRequest::getVar('q');
	 $query = urlencode($query);
	 
	 $this->new_method = $params->get('curl', false) ;
	 
	 $url = 'http://api.discogs.com/database/search';

	 $result = $oauthObject->sign(array(
        'path'      => $url,
        'parameters'=> array(
            'type' => 'release',
            'q'    => $query),
        'signatures'=> $signatures));
	 
	 $search = JRequest::getVar('search');
		 switch($search){
			case "tracklist":
			$js_function = "get_discogs_release_tracklist" ;
			$button_title = JText::_('GET_TRACKLIST') ;
			break;
			default:
			$js_function = "get_discogs_release" ;
			$button_title = JText::_('GET_RELEASE_DATA') ;
			break;
		 }


	 
	 if($this->new_method){
	 /****************************/
	 /******** NEW method ********/
	 /****************************/

	 $ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
	curl_setopt($ch, CURLOPT_HEADER, $result['header']);
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection/2.4 +".$uri->base());
	$return = curl_exec ( $ch );
	curl_close($ch);

	//print_r($return);die;
	 
	 $check = json_decode($return) ;
	 
	 /****************************/
	 /******** NEW method ********/
	 /****************************/
	 }
	 else{
		 
	 /****************************/
	 /******** OLD method ********/
	 /****************************/
	 
	 $options = array('http' =>
		array(
			'method'  => 'GET',
			'header'  => 'Accept-Encoding: gzip\r\n'
		)
	);
	$context = stream_context_create($options);
	
	ini_set('user_agent', "Music Collection 2.4");
	
	$return = file_get_contents($result['signed_url'], false, $context);
	$check = json_decode($return) ;

	 
	 }
	 
		foreach($check->results as $result){
			
			
			if($result->type == "release"){
				echo "<div class='well well-small row-fluid'>" ;
				//echo "<img src='".str_replace("api.discogs.com", "s.pixogs.com",$result->thumb)."' style='float:left; padding-right:5px;' height='40' width='40' class='thumbnail' />" ;
				echo "<div class='pull-left span2'><strong>" . $result->title. "</strong><br />";
				echo $result->country ." - " .$result->label." - " .$result->catno . "</div>";
				echo "<a class='btn' href='javascript:".$js_function."(\"".$result->id."\")'>" . $button_title . "</a><br />";
				echo "</div>";
				if($search != "tracklist") echo "<div id='discogs_release_".$result->id."' class='individual_release'></div>";
			
			}
			
		}
			
	 //echo $return;      
	 $mainframe->close();
	  
  }
  
  function get_discogs_release(){

	  $mainframe = JFactory::getApplication();
	  $params = JComponentHelper::getParams( 'com_muscol' );
	  $uri = JFactory::getURI();

	  require (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'assets'.DS.'oauthsimple-master'.DS.'php'.DS.'OAuthSimple.php');
	  $oauthObject = new OAuthSimple('bZaNCebFXkBrvtmiuprD', 'BXopzkqgzQHEUysXloujDuiCodZctmsY');

	  $signatures = array();
	  $signatures['oauth_token'] = $params->get('oauth_token');
      $signatures['oauth_secret'] = $params->get('access_token_secret');
	  
	  error_reporting(E_ERROR);
		 
	 $release_id = JRequest::getVar('release_id');
	 
	 $this->new_method = $params->get('curl', false) ;

	 $url = 'http://api.discogs.com/releases/'. $release_id;

	 $result = $oauthObject->sign(array(
        'path'      => $url,
        'parameters'=> array(),
        'signatures'=> $signatures));
	 
	 if($this->new_method){
	 /****************************/
	 /******** NEW method ********/
	 /****************************/
	 
	 
	 $ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
		curl_setopt($ch, CURLOPT_HEADER, $result['header']);
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection/2.4 +".$uri->base());
		$return = curl_exec ( $ch );
		curl_close($ch);
	 
	 $check = json_decode($return) ;
	 
	 /****************************/
	 /******** NEW method ********/
	 /****************************/
	 }
	 else{
		 
	 /****************************/
	 /******** OLD method ********/
	 /****************************/
	
	 $options = array('http' =>
		array(
			'method'  => 'GET',
			'header'  => 'Accept-Encoding: gzip\r\n'
		)
	);
	$context = stream_context_create($options);
	
	ini_set('user_agent', "Music Collection 2.4");
	
	 $return = file_get_contents( $result['signed_url'], false, $context);
	 
	 $check = json_decode($return) ;
	 
	 /****************************/
	 /******** OLD method ********/
	 /****************************/
	 
	 }
	 
	 $debug = false ;
	 
	 //print_r($xml);die;
	 
	 $album->name = $check->title ;
	 $album->edition_country = $check->country ;
	 $album->edition_details = $check->notes ;
	 
	 $released = explode("-", $check->released) ;
	 $album->year = $released[0] ;	
	 $album->month = $released[1] ;	
	
	 $album->edition_year = $released[0] ;	
	 $album->edition_month = $released[1] ;
	 
	 $album->format = $check->formats[0]->name ;
	 $album->ndisc = $check->formats[0]->qty ;
	 
	 $album->label = $check->labels[0]->name ;
	 $album->catalog_number = $check->labels[0]->catno ;
	 
	 $album->external_image = $check->images[0]->uri ;
	 
	 $album->genre = $check->genres[0] ;
	 
	 $album->artist = $check->artists[0]->name ;
	 
	 $i_track = 0;
	 foreach($check->tracklist as $track){
		
		if(strpos($track->position, "-")) $separador = "-";
		elseif(strpos($track->position, ".")) $separador = ".";
		else $separador = "-";
							
		$position = explode($separador, $track->position) ;
		if(count($position) == 2){
			$songs[$i_track]->position = $position[1] ;
			$songs[$i_track]->disc_num = $position[0] ;
		}
		elseif(count($position) == 1){
			$songs[$i_track]->position = $position[0] ;
		}
		
		$songs[$i_track]->name = $track->title ;
		
		$length = explode(":", $track->duration) ;
							
		if(count($length) == 2){
			$songs[$i_track]->minuts = $length[0] ;
			$songs[$i_track]->seconds = $length[1] ;
		}
		elseif(count($length) == 1){

			$songs[$i_track]->seconds = $length[0] ;
		}
		elseif(count($length) == 3){
			$songs[$i_track]->hours = $length[0] ;
			$songs[$i_track]->minuts = $length[1] ;
			$songs[$i_track]->seconds = $length[2] ;
		}
							
		$i_track++; 
	 }
	
	
	$this->album->genre = $album->genre ;

	$search = JRequest::getVar('search');
	switch($search){
		case "tracklist":
		$this->render_tracklist_form($songs); 
		break;
		default:
		$this->render_album_form($album,$songs); 
		break;
	 }
	
	 //echo $return;      
	 $mainframe->close();
	  
  }
 
  
  function render_album_form($album,$songs){
	  
	  $model = $this->getModel('album');
	  
	  $artists = $model->getArtistsData();
	  $formats = $model->getFormatsData();
	  $types = $model->getTypesData();
	  $genres = $model->getGenresData();
	  $tags = $model->getTagsData();
	  
	  $random_id = rand();
	  
	  
	  ?>
<div class="well well-small row-fluid">
<form action="index.php" method="post" name="album_discogs_<?php echo $random_id; ?>"  enctype="multipart/form-data" class="form-horizontal">
  <div class="span12">
  <a class="toolbar btn btn-primary" onclick="javascript: document.album_discogs_<?php echo $random_id; ?>.submit();" href="#">Save album</a>
  </div>
  <div class="span6">
  <fieldset>
    <legend><?php echo JText::_( 'Details' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'Album name' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="name" id="name" size="80" maxlength="250" value="<?php echo $album->name;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="subtitle"> <?php echo JText::_( 'Subtitle' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="subtitle" id="subtitle" size="80" maxlength="250" value="<?php echo $album->subtitle;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="artist"> <?php echo JText::_( 'Artist' ); ?>: </label>
      <div class="controls">
        <select name="artist_id" id="artist_id">
          <?php
			$artist_found = false;
			for ($i=0, $n=count( $artists );$i < $n; $i++)	{
			$row =$artists[$i];
			$selected = ""; 
			if($row->artist_name == $album->artist) {$selected = "selected"; $artist_found = true; }?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
          <?php } ?>
        </select>
        <?php if(!$artist_found && $album->artist){ ?>
        <div class="advice_discogs"><?php echo JText::_('Could not find artist matching') . " <strong>" . $album->artist . "</strong>. " . JText::_('Please select artist from list') ; ?>.</div>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="subartist"> <?php echo JText::_( 'Subartist' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="subartist" id="subartist" size="80" maxlength="250" value="<?php echo $album->subartist;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="format"> <?php echo JText::_( 'Format' ); ?>: </label>
      <div class="controls">
        <select name="format_id" id="format_id">
          <?php
			$format_found = false;
			for ($i=0, $n=count( $formats );$i < $n; $i++)	{
			$row =$formats[$i];
			$selected = ""; 
			if($row->format_name == $album->format){ $selected = "selected"; $format_found = true; }?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->format_name;?></option>
          <?php } ?>
        </select>
        <?php if(!$format_found && $album->format){ ?>
        <div class="advice_discogs"><?php echo JText::_('Could not find format matching') . " <strong>" . $album->format . "</strong>. " . JText::_('Please select format from list') ; ?>.</div>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="ndisc"> <?php echo JText::_( 'Number of discs' ); ?>: </label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="ndisc" id="ndisc" size="3" maxlength="3" value="<?php echo $album->ndisc;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="type"> <?php echo JText::_( 'Year' ); ?> / <?php echo JText::_( 'Month' ); ?>: </label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="year" id="year" size="5" maxlength="4" value="<?php echo $album->year;?>" />
        <select name="month" id="month">
          <option value="0"></option>
          <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $album->month) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo $this->displayMonth($i);?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="genre"> <?php echo JText::_( 'Genre' ); ?>: </label>
      <div class="controls">
        <select name="genre_id" id="genre_id">
          <?php echo $this->show_genre_tree($genres,0); ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="length"> <?php echo JText::_( 'Length' ); ?>: </label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="hours" id="hours" size="2" maxlength="2" value="<?php echo $album->hours;?>" />
        :
        <input class="inputbox input-mini" type="text" name="minuts" id="minuts" size="2" maxlength="2" value="<?php echo $album->minuts;?>" />
        :
        <input class="inputbox input-mini" type="text" name="seconds" id="seconds" size="2" maxlength="2" value="<?php echo $album->seconds;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="price"> <?php echo JText::_( 'Price' ); ?>: </label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="price" id="price" size="7" maxlength="8" value="<?php echo $album->price;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="album_file"> <?php echo JText::_( 'Album file' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="album_file" id="album_file" size="80" maxlength="250" value="<?php echo $album->album_file;?>" />
        <br />
        <span style="font-size:10px"><?php echo JText::_( 'Example' ); ?>: /images/complet_albums/albumXX.zip</span></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="album_file"> <?php echo JText::_( 'Buy link' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="buy_link" id="buy_link" size="80" maxlength="250" value="<?php echo $album->buy_link;?>" />
        <br />
        <span style="font-size:10px"><?php echo JText::_( 'External link to buy the album. Music Collection does NOT handle the buy process' ); ?></span></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="keywords"> <?php echo JText::_( 'Keywords' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="keywords" id="keywords" size="80" maxlength="250" value="<?php echo $album->keywords;?>" />
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend><?php echo JText::_( 'Edition details' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="type"> <?php echo JText::_( 'Edition date' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="edition_year" id="edition_year" size="5" maxlength="4" value="<?php echo $album->edition_year;?>" />
        <select name="edition_month" id="edition_month">
          <option value="0"></option>
          <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $album->edition_month) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo $this->displayMonth($i);?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'Country' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="edition_country" id="edition_country" size="80" maxlength="250" value="<?php echo $album->edition_country;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'Label' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="label" id="label" size="80" maxlength="250" value="<?php echo $album->label;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'Catalog Number' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="catalog_number" id="catalog_number" size="80" maxlength="250" value="<?php echo $album->catalog_number;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'Edition details' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="edition_details" id="edition_details" size="80" maxlength="250" value="<?php echo $album->edition_details;?>" />
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend><?php echo JText::_( 'Extra information' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="type"> <?php echo JText::_( 'Types' ); ?>: </label>
      <div class="controls">
        <select multiple="multiple" name="types[]" id="types">
          <?php
			for ($i=0, $n=count( $types );$i < $n; $i++)	{
			$row =$types[$i];
			$selected = ""; 
			if( in_array($row->id,$album->types) ) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->type_name;?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="genre"> <?php echo JText::_( 'Tags' ); ?>: </label>
      <div class="controls">
        <select multiple="multiple" name="tags[]" id="tags">
          <?php
			for ($i=0, $n=count( $tags );$i < $n; $i++)	{
			$row =$tags[$i];
			$selected = ""; 
			if( in_array($row->id,$album->tags) ) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->tag_name;?></option>
          <?php } ?>
        </select>
      </div>
    </div>
  </fieldset></div>
  <div class="span6">
  <fieldset>
    <legend><?php echo JText::_( 'Primary Image (front sleeve)' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="image"> <?php echo JText::_( 'Front sleeve' ); ?>: </label>
      <div class="controls"> <?php echo JText::_('This field is for the COVER of the album or front sleeve'); ?><br />
        <input class="inputbox" type="text" name="image" id="image" size="50" maxlength="250" value="<?php echo $album->image;?>" />
        <input type="file" name="image_file"/>
        <br />
        <?php if($album->external_image){ ?>
        <img src="<?php echo $album->external_image; ?>" />
        <input type="checkbox" name="use_external_image" checked="checked" />
        <?php echo JText::_('Use image from Discogs'); ?>
        <input type="hidden" name="external_image" id="external_image" value="<?php echo $album->external_image; ?>" />
        <?php } ?>
        <img style="max-width:300px;" src="../images/albums/<?php echo $album->image;?>"/>
      </div>
    </div>
    
   
  </fieldset>
  <fieldset>
    <legend><?php echo JText::_( 'Secondary Images' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="name2"> <?php echo JText::_( 'Substitutive Image for album name' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="name2" id="name2" size="50" maxlength="250" value="<?php echo $album->name2;?>" />
        <input type="file" name="name_image_file"/>
        <?php
		if($album->name2 != "") {?>
        <img style="max-height:30px;" src="../images/album_extra/album_name/<?php echo $album->name2;?>"/>
        <?php } ?>
        <br />
        <?php echo JText::_('This field will usually be empty'); ?></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="artist2"> <?php echo JText::_( 'Substitutive Image for artist name' ); ?>: </label>
      <div class="controls">
        <input class="inputbox" type="text" name="artist2" id="artist2" size="50" maxlength="250" value="<?php echo $album->artist2;?>" />
        <input type="file" name="artist_image_file"/>
        <?php
		if($album->artist2 != "") {?>
        <img style="max-height:30px;" src="../images/album_extra/artist_name/<?php echo $album->artist2;?>"/>
        <?php } ?>
        <br />
        <?php echo JText::_('This field will usually be empty'); ?></div>
    </div>
  </fieldset>
  <fieldset>
    <legend><?php echo JText::_( 'Songs' ); ?></legend>
    <div class="advice"><?php echo JText::_('ADVICE: Uploading too many files simultaneously can exhaust your server uploading limits and cause undesired behavior.') ; ?><br />
      <?php echo JText::_('upload_max_filesize defined in php.ini ') . ini_get('upload_max_filesize'); ?></div>
    <table class="adminlist table table-striped" id="songs_table">
      <thead>
        <tr>
          <th width="40"> <?php echo JText::_( 'Disc Num' ); ?> </th>
          <th width="40"> <?php echo JText::_( 'Song Num' ); ?> </th>
          <th width="40"> <?php echo JText::_( 'Position' ); ?> </th>
          <th> <?php echo JText::_( 'Name' ); ?> </th>
          <th> <?php echo JText::_( 'File' ); ?> </th>
          <th> <?php echo JText::_( 'Length' ); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	for ($i=0, $n=count( $songs ); $i < $n; $i++)	{
		$song =$songs[$i];
		$song->id = $i + 1 ;
		if($song->position) $song->num = $i + 1 ;
		
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td><input class="inputbox input-mini" type="text" name="0_disc_num_<?php echo $song->id;?>" id="0_disc_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->disc_num;?>" /></td>
        <td><input class="inputbox input-mini" type="text" name="0_num_<?php echo $song->id;?>" id="0_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->num;?>" /></td>
        <td><input class="inputbox input-mini" type="text" name="0_position_<?php echo $song->id;?>" id="0_position_<?php echo $song->id;?>" size="3" maxlength="6" value="<?php echo $song->position;?>" /></td>
        <td><input class="inputbox" type="text" name="0_song_<?php echo $song->id;?>" id="0_song_<?php echo $song->id;?>" size="50" maxlength="250" value="<?php echo $song->name;?>" /></td>
        <td><input class="inputbox" type="text" name="0_filename_<?php echo $song->id;?>" id="0_filename_<?php echo $song->id;?>" size="32" maxlength="255" value="<?php echo $song->filename;?>" />
          <input type="file" name="0_song_file_<?php echo $song->id;?>" id="0_song_file_<?php echo $song->id;?>"/></td>
        <td><input class="inputbox input-mini" type="text" name="0_hours_<?php echo $song->id;?>" id="0_hours_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->hours;?>" />
          :
          <input class="inputbox input-mini" type="text" name="0_minuts_<?php echo $song->id;?>" id="0_minuts_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->minuts;?>" />
          :
          <input class="inputbox input-mini" type="text" name="0_seconds_<?php echo $song->id;?>" id="0_seconds_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->seconds;?>" /></td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
    </table>
  </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="0" />
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="controller" value="album" />
</form>
</div>
<?php 	  
  }
  
  function render_tracklist_form($songs){

	  $random_id = rand();
	  
	  ?>
<div class="title_discogs"><?php echo JText::_('Tracklist obtained from Discogs Database'); ?>:</div>
<table class="adminlist" id="songs_table_discogs">
  <thead>
    <tr>
      <th width="5"> </th>
      <th width="20"> </th>
      <th width="40"> <?php echo JText::_( 'Disc Num' ); ?> </th>
      <th width="40"> <?php echo JText::_( 'Song Num' ); ?> </th>
      <th width="40"> <?php echo JText::_( 'Position' ); ?> </th>
      <th> <?php echo JText::_( 'Name' ); ?> </th>
      <th> <?php echo JText::_( 'File' ); ?> </th>
      <th> <?php echo JText::_( 'Length' ); ?> </th>
      <th width="20"> </th>
      <th width="20"> </th>
      <th> </th>
    </tr>
  </thead>
  <?php
	$k = 0;
	for ($i=0, $n=count( $songs ); $i < $n; $i++)	{
		$song =$songs[$i];
		$song->id = $i + 1 ;
		if($song->position) $song->num = $i + 1 ;
		
		?>
  <tr class="<?php echo "row$k"; ?>">
    <td></td>
    <td></td>
    <td><input class="inputbox" type="text" name="0_disc_num_<?php echo $song->id;?>" id="0_disc_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->disc_num;?>" /></td>
    <td><input class="inputbox" type="text" name="0_num_<?php echo $song->id;?>" id="0_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->num;?>" /></td>
    <td><input class="inputbox" type="text" name="0_position_<?php echo $song->id;?>" id="0_position_<?php echo $song->id;?>" size="3" maxlength="6" value="<?php echo $song->position;?>" /></td>
    <td><input class="inputbox" type="text" name="0_song_<?php echo $song->id;?>" id="0_song_<?php echo $song->id;?>" size="50" maxlength="250" value="<?php echo $song->name;?>" /></td>
    <td><input class="inputbox" type="text" name="0_filename_<?php echo $song->id;?>" id="0_filename_<?php echo $song->id;?>" size="32" maxlength="255" />
      <input type="file" name="0_song_file_<?php echo $song->id;?>" id="0_song_file_<?php echo $song->id;?>"/></td>
    <td><input class="inputbox" type="text" name="0_hours_<?php echo $song->id;?>" id="0_hours_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->hours;?>" />
      :
      <input class="inputbox" type="text" name="0_minuts_<?php echo $song->id;?>" id="0_minuts_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->minuts;?>" />
      :
      <input class="inputbox" type="text" name="0_seconds_<?php echo $song->id;?>" id="0_seconds_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->seconds;?>" /></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php
		$k = 1 - $k;
	}
	?>
</table>
<?php 	  
  }
  
  function show_genre_tree($genres,$level){
		
		$return = "";
		
		for($i = 0; $i < count($genres); $i++){
			$return .= $this->render_option($genres[$i]->id,$genres[$i]->genre_name,$level);
			$level ++;
			if(!empty($genres[$i]->sons)){
				$return .= 	$this->show_genre_tree($genres[$i]->sons,$level);
			}
			$level --;
		}
		//echo $return;
		return $return;
		
	}
	
	function render_option($id, $name, $level){
		$indent = "";
		
		for($i = 0; $i < $level; $i++){
			$indent .= "&nbsp;&nbsp;";	
		}
		
		$selected = ""; 
		if( $name == $this->album->genre ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
	
	function displayMonth($month){
		$month_array = array(
			1 => "January" , 
			2 => "February" , 
			3 => "March" , 
			4 => "April" , 
			5 => "May" , 
			6 => "June", 
			7 => "July", 
			8 => "August" , 
			9 => "September", 
			10 => "October" , 
			11 => "November", 
			12 => "December"
		);
		return JText::_( $month_array[$month] );
	}
	
	function time_to_array($total_time){
	 
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
	
	function scan_folder(){
	  $mainframe = JFactory::getApplication();
		 
	 $folder = JRequest::getVar('folder');
	 
	 $params =JComponentHelper::getParams( 'com_muscol' );
	 
	 $folder = str_replace("/", DS, $folder);
	 $songspath = str_replace("/", DS, $params->get('songspath'));
	 
	 $folder = str_replace("\\", DS, $folder);
	 $songspath = str_replace("\\", DS, $songspath);
	 
	 $folder_complet = JPATH_SITE  . $songspath . DS . $folder ;
	 //echo $folder_complet ;
	 
	 $files = JFolder::files($folder_complet);
	 
	 $folders = JFolder::folders($folder_complet);
	 
	 $parents = explode(DS, $folder);
	 
	 $indent = "";
	 
	 $parent_acumulation = "";
	 $i = 0;
	 foreach($parents as $parent){
		 
		 for($j = 0; $j < $i; $j++) $indent .= "&nbsp;&nbsp;&nbsp;";
		 
		 echo  $indent ;
		 
		 echo "<a href='javascript:scan_folder(\"". $parent_acumulation . $parent."\");'>" . $parent . "</a><br />" ;
		 $parent_acumulation .= $parent . DS;
		 $i++;
	 }
	 
	
	 $indent .= "&nbsp;&nbsp;&nbsp;";
	 
	 
	 if(!empty($folders)) {
		 echo "<div class='well well-small span4'>".$indent . "<strong>" . JText::_('Folders found') . "</strong>:<br />";
	 }
	 foreach($folders as $folder_inside){
		echo   $indent . "<a href='javascript:scan_folder(\"". str_replace("\\", "/", $folder). "/" . $folder_inside."\");'>".$folder_inside."</a><br />" ; 
	 }
	 if(!empty($folders)) {
		 echo "</div>";
	 }
	 
	 echo "<br />";
	 
	 $allowed_filetypes = array('mp3', 'mp4', 'flv', 'mpg', 'aac', 'm4r', 'wmv', 'm4a', 'MP3', 'MP4', 'FLV', 'MPG', 'AAC', 'M4R', 'WMV', 'M4A');
	 
	 echo "<div class='files_in_folder files_in_folder".$i." well well-small span4'>" ;
	 if(!empty($files)) echo $indent . "<strong>" . JText::_('Files found') . "</strong>:<br />";
	 foreach($files as $file){
		 if(in_array(JFile::getExt($file), $allowed_filetypes)) echo $indent ."&bull; ". $file . "<br />" ; 
	 }
	 echo "</div>";
	
	 $id3_route = JRoute::_('index.php?option=com_muscol&controller=albums&task=process_id3&folder='.str_replace("\\", "/", $folder));
	 
	 echo "<div class='span4'><a class='btn btn-primary' href='javascript:new_album_folder(\"".str_replace("\\", "/", $folder)."\")'>".JText::_('CREATE_NEW_ALBUM')."</a>";
	 echo "<a class='btn btn-primary' href='".$id3_route."'>".JText::_('PROCESS_FILES_ID3')."</a>";
	 
	 $id3_route_multiple = JRoute::_('index.php');
	 echo "<form method='get' action='".$id3_route_multiple."'>
	 <input type='submit' class='btn btn-primary' value='".JText::_('PROCESS_SUBFOLDERS_ID3')."' />
	 <input type='hidden' name='option' value='com_muscol' />
	 <input type='hidden' name='controller' value='albums' />
	 <input type='hidden' name='task' value='process_id3_multiple' />
	 <input type='hidden' name='folder' value='".str_replace("\\", "/", $folder)."' />
	 </form>
	 </div>
	 ";

	 //echo $return;      
	 $mainframe->close();
	  
  }
  
  
  function new_album_folder(){
	  
	  $mainframe = JFactory::getApplication();
		 
	 $folder = JRequest::getVar('folder');
	 
	 $params =JComponentHelper::getParams( 'com_muscol' );
	 
	 $folder = str_replace("/", DS, $folder);
	 $songspath = str_replace("/", DS, $params->get('songspath'));
	 
	 $folder = str_replace("\\", DS, $folder);
	 $songspath = str_replace("\\", DS, $songspath);
	 
	 $folder_complet = JPATH_SITE  . $songspath . DS. $folder ;
	 
	 $files = JFolder::files($folder_complet);
	 
	 $allowed_filetypes = array('mp3', 'mp4', 'flv', 'mpg', 'aac', 'm4r', 'wmv', 'm4a', 'MP3', 'MP4', 'FLV', 'MPG', 'AAC', 'M4R', 'WMV', 'M4A');
	 
	 if($folder) $folder = $folder . DS ;
	 if(substr($folder, 0, 1) == DS) $folder = substr($folder, 1) ;
	 
	 $i = 0;
	 foreach($files as $file){
		if(in_array(JFile::getExt($file), $allowed_filetypes)){
			
			$songs[$i] = new stdClass();

			$songs[$i]->filename = str_replace("\\", "/", $folder.$file) ;
			$songs[$i]->name = ucwords(strtolower( str_replace("-", " ", JFile::stripExt($file)) ));
			$songs[$i]->num = $i + 1 ;
			$songs[$i]->position = $i + 1 ;
			$songs[$i]->disc_num = 1 ;
			
			$i++;
		}
	 }
	 
	 $parents = explode(DS, $folder);
	 $album_name = $parents[count($parents) - 1] ;
	 $artist_name = $parents[count($parents) - 2] ;
	 
	 $album = new stdClass();

	 $album->name = $album_name ;
	 $album->artist = $artist_name ;
	 
	 $this->render_album_form($album,$songs); 
	 
	 $mainframe->close();
	  
  }

  function process_id3_multiple(){
  	error_reporting(E_ERROR);
	  $mainframe = JFactory::getApplication();
	  $folder = JRequest::getVar('folder');
	  $ajax = JRequest::getInt('ajax');
	 
	  $orig_folder = $folder  ;
	  
	  $params =JComponentHelper::getParams( 'com_muscol' );
	  
	  $folder = str_replace("/", DS, $folder);
		 $songspath = str_replace("/", DS, $params->get('songspath'));
		 
		 $folder = str_replace("\\", DS, $folder);
		 $songspath = str_replace("\\", DS, $songspath);
		 
		 $folder_complet = JPATH_SITE  . $songspath . DS. $folder ;
		 
		 $files = JFolder::files($folder_complet);
		 
		 $allowed_filetypes = array('mp3', 'mp4', 'flv', 'mpg', 'aac', 'm4r', 'wmv', 'm4a', 'Mp3');
		 
		 if($folder) $folder = $folder . DS ;
		 if(substr($folder, 0, 1) == DS) $folder = substr($folder, 1) ;
	  
	  $folder_complet = str_replace("//", "/", $folder_complet) ;
	  $tree = JFolder::listFolderTree($folder_complet, "", 6);
	  //print_r($tree) ; die;
	  
	  $total = count($tree);
	  $start = JRequest::getInt('start',0);
	  $i = $start ;
	  $end = $start + 1 ;

	  $num_files = JRequest::getInt('num_files',0);

	  for($i = $start; $i < $end; $i++){
		  
		  if($tree[$i]["fullname"]){
			  $short_folder_name = str_replace(JPATH_SITE  . $songspath, "", $tree[$i]["fullname"] ) ;
			  //echo $short_folder_name;
			  $num_files += $this->process_id3_recursive($short_folder_name);
		  }
	  }
	  
	   if($ajax){

	   	$return = new stdClass();

	   	if($tree[$i]["fullname"]){
			  
			   $return->recursive = 1 ;
			   $return->start = $end ;
			   $return->total = $total ;
			   $return->folder = $orig_folder ;
			   $return->importing = $short_folder_name ;
			   $return->num_files = $num_files ;
		  }
		  else {
			 
			   $return->recursive = 0 ;
			   $return->total = $total ;
			   $return->start = $total ;
			   $return->num_files = $num_files ;
		  }

		echo json_encode($return) ;

	   	$mainframe->close();

	   }
	   else{
		  
		  if($tree[$i]["fullname"]){
			   $link = 'index.php?option=com_muscol&recursive=1&start='.$end.'&total='.$total.'&folder='.$orig_folder;
		  }
		  else {
			  $msg = JText::sprintf('IMPORT_PROCESS_FINISHED', $total) ;
			  $link = 'index.php?option=com_muscol';
			  
		  }
		  
		  $this->setRedirect($link, $msg); 
		}
	  
  }
  
  function process_id3_recursive($folder = false){
  	error_reporting(E_ERROR);
	  $mainframe = JFactory::getApplication();
	
	
	 $params =JComponentHelper::getParams( 'com_muscol' );
	 
	 $id3 = MusColHelper::ID3active() ;
	 
	 $db = JFactory::getDBO();
	 
	 if($id3){
	 
		 $folder = str_replace("/", DS, $folder);
		 $songspath = str_replace("/", DS, $params->get('songspath'));
		 
		 $folder = str_replace("\\", DS, $folder);
		 $songspath = str_replace("\\", DS, $songspath);
		 
		 //$folder_complet = JPATH_SITE  . $songspath . DS. $folder ;
		 $folder_complet = JPATH_SITE  . $songspath . DS. $folder ;
		 
		 $files = JFolder::files($folder_complet);
		 
		 $allowed_filetypes = array('mp3', 'mp4', 'flv', 'mpg', 'aac', 'm4r', 'wmv', 'm4a', 'Mp3');
		 
		 if($folder) $folder = $folder . DS ;
		 if(substr($folder, 0, 1) == DS) $folder = substr($folder, 1) ;
		 
		 $model = $this->getModel('album');
		 
		
		 
		 $i = 0;
		 foreach($files as $file){
			if(in_array(JFile::getExt($file), $allowed_filetypes)){
									  
				$songs[$i]['id'] = 0 ;
				$songs[$i]['filename'] = str_replace("\\", "/", $folder.$file) ;
				
				$query = ' SELECT id FROM #__muscol_songs WHERE filename = "'.$folder.$file.'" ' ;
				$db->setQuery($query) ;
				$exists = $db->loadResult() ;
				
				if(!$exists){
				
					if ($album_id = $model->save_song($songs[$i])) {
						$msg = JText::_( 'SONG_SAVED' );
					} else {
						$msg = JText::_( 'ERROR_SAVING_SONG' );
					}
					
					//$mainframe->enqueueMessage($msg);
				
				}
				
				$i++;
			}
		 }
		 
		 //$link = 'index.php?option=com_muscol';
	 	return $i ;
	 
	 
	 }
	 else{
		$link = 'index.php?option=com_muscol';
		
	 	$msg = JText::_( 'SONGS_NOT_PROCESSED_ID3' );
	 	
	 }
	 
	 
  }
  
  function process_id3(){
	  $mainframe = JFactory::getApplication();
		 
	 $folder = JRequest::getVar('folder');
	 
	 $params =JComponentHelper::getParams( 'com_muscol' );
	 
	 $id3 = MusColHelper::ID3active() ;
	 
	 if($id3){
	 
		 $folder = str_replace("/", DS, $folder);
		 $songspath = str_replace("/", DS, $params->get('songspath'));
		 
		 $folder = str_replace("\\", DS, $folder);
		 $songspath = str_replace("\\", DS, $songspath);
		 
		 $folder_complet = JPATH_SITE  . $songspath . DS. $folder ;
		 
		 $files = JFolder::files($folder_complet);
		 
		 $allowed_filetypes = array('mp3', 'mp4', 'flv', 'mpg', 'aac', 'm4r', 'wmv', 'm4a', 'MP3', 'MP4', 'FLV', 'MPG', 'AAC', 'M4R', 'WMV', 'M4A');
		 
		 if($folder) $folder = $folder . DS ;
		 if(substr($folder, 0, 1) == DS) $folder = substr($folder, 1) ;
		 
		 $model = $this->getModel('album');
		 
		 //print_r($files);die;
		 
		 $i = 0;
		 foreach($files as $file){
			if(in_array(JFile::getExt($file), $allowed_filetypes)){
									  
				$songs[$i]['id'] = 0 ;
				$songs[$i]['filename'] = str_replace("\\", "/", $folder.$file) ;
				
				if ($album_id = $model->save_song($songs[$i])) {
					$msg = JText::_( 'SONG_SAVED' );
				} else {
					$msg = JText::_( 'ERROR_SAVING_SONG' );
				}
				
				$mainframe->enqueueMessage($msg);
				
				$i++;
			}
		 }
		 
		 $link = 'index.php?option=com_muscol';
	 
	 	$this->setRedirect($link);
	 
	 }
	 else{
		$link = 'index.php?option=com_muscol';
		
	 	$msg = JText::_( 'SONGS_NOT_PROCESSED_ID3' );
	 	$this->setRedirect($link, $msg, 'warning'); 
	 }
	 
	 
  }

  
}
