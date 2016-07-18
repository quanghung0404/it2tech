<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser(); ?>
<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation($this->album->letter);
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>
<?php echo MusColHelper::edit_button_album($this->album->id); ?>
<?php $artist_link = MusColHelper::routeArtist($this->album->artist_id); ?>

<div class='cap page-header'>
  <h2 class='artista_disc'><a href='<?php echo $artist_link; ?>'>
    <?php 
			$image_attr = array("title" => $this->album->format_name);
				$format_image = JHTML::image('images/formats/' . $this->album->icon , $this->album->format_name, $image_attr );
				
			if($this->album->artist2){
				$image_attr = array(
							"title" => $this->album->artist_name
							);
				$image_for_artist = JHTML::image('images/album_extra/artist_name/' . $this->album->artist2 , $this->album->artist_name , $image_attr );
					echo $image_for_artist;
			}
				else echo $this->album->artist_name; 
				?>
    <small><?php echo $this->album->subartist; ?></small></a></h2>
  <span class='pull-right'><?php echo $format_image; ?></span>
  
  <h1 class='album_disc'>
    <?php if($this->album->name2){
				$image_attr = array(
							"title" => $this->album->name
							);
				$image_for_album = JHTML::image('images/album_extra/album_name/' . $this->album->name2 , $this->album->name , $image_attr );
					echo $image_for_album;

	}
				else echo $this->album->name; ?>
    <small class='subtitle_disc'><?php echo $this->album->subtitle; ?></small> </h1>
    
  <div class='year_disc'> <?php echo $this->album->year; ?>
    <?php if($this->params->get('showalbum_adminrating', 1)){ ?>
    <?php echo MusColHelper::show_stars($this->album->points,true); ?>
    <?php } ?>
    <?php if($this->params->get('showalbum_adminrating', 1) && $this->params->get('showalbum_userrating', 1)){ ?>
    /
    <?php } ?>
    <?php if($this->params->get('showalbum_userrating', 1)){ ?>
    <?php echo MusColHelper::show_stars($this->average_rating); ?> <span class="num_ratings"><?php echo $this->num_rating ; ?>
    <?php if( $this->num_rating == 1) echo JText::_('RATING'); else echo JText::_('RATINGS'); ?>
    </span>
    <?php } ?>
    <span class='pull-right'>
    <?php 										
					for($k=0;$k < count($this->album->tags); $k++){ 
						if(isset($this->album->tags[$k]->tag_name) && isset($this->album->tags[$k]->tag_icon)){
							$image_attr = array(
									"title" => JText::_( $this->album->tags[$k]->tag_name )
									);
							$tag_image = JHTML::image('images/tags/' . $this->album->tags[$k]->icon , JText::_( $this->album->tags[$k]->tag_name ) , $image_attr );
							if($this->album->tags[$k]->tag_name != "" && $this->album->tags[$k]->icon != "") echo " ".$tag_image;
						}
					
				 } ?>
    </span> </div>
</div>
<div class="row-fluid">
  <div class="first_col_content span7">
    <div class='div_imatge_gran'>
      <?php 
		$image_attr = array(
						"title" => $this->album->name ,
						"class" => "imatge_gran"
						);
		$image = JHTML::image('images/albums/' . $this->album->image , $this->album->name , $image_attr );
		
		echo $image;
	?>
    <div class="playbutton_div"><a href="javascript:toggle_player();" class="play-lg" id="play_button_album"></a></div>
    </div>
    <?php if(count( $this->songs )){ ?>
    <div align="center">
      <?php if($this->player != "" && ( ( $user->id && $this->params->get('displayplayer') ) || $this->params->get('displayplayer') == 2 )) echo $this->player ; ?>
      <?php if($this->album->album_file != "" && $this->params->get('allowsongdownload')){ 
		if( $user->id || $this->params->get('allowsongdownload') == 2 ){ ?>
      <a href="<?php echo $this->album->album_file; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_ALBUM'); ?>"><?php echo JHTML::image('components/com_muscol/assets/images/music.png','File'); ?></a>
      <?php } else  echo JHTML::image('components/com_muscol/assets/images/music.png','File',array("title" => JText::_("FILE_REGISTERED_USERS"))); 
	 } ?>
      <?php if($this->params->get('allowalbumbuy') && $this->album->buy_link != ""){ ?>
      <a href="<?php echo $this->album->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_ALBUM'); ?>" target="_blank"><?php echo JHTML::image('components/com_muscol/assets/images/buy.png','Buy'); ?></a>
      <?php } ?>
      <br />
      
      <table border='0' cellpadding='0' cellspacing='0' width="100%" class="table table-condensed table-striped table-hover">
        <?php
	$j = 0 ;
	$k = 0 ;
	for ($i=0, $n=count( $this->songs ); $i < $n; $i++)	{
		$song =  $this->songs[$i] ;
		$player = $song->player ;
		$play_button = "";
			
		$song_link = MusColHelper::routeSong($song->id);
		$file_link = MusColHelper::create_file_link($song);
		
		//print_r( $player);die;
		
		$play_button_start = strpos( $player , "<!--PLAYBUTTON-->");
		$play_button_end = strpos( $player ,"<!--/PLAYBUTTON-->" );
		//$players[] =$player ;
		if($play_button_end) {
			$play_button = substr($player, $play_button_start, $play_button_end + 18);
			$player = substr($player, 0, $play_button_start + 17) . substr($player, $play_button_end + 18);
		}
		
		?>
        <tr class='tr_song_link tr_song_link<?php echo $j; ?>' id="song_position_<?php if($song->filename) echo $k; ?>">
              <td class='num_song'><span class='song_position'><?php echo $song->position; ?></span><?php echo $play_button; ?></td>
              <td class='nom_artist_song'><?php if($song->artist_id != $this->album->artist_id) echo $song->artist_name; ?></td>
              <td class='nom_song'><a class='song_link' href="<?php echo $song_link; ?>"><?php echo $song->name; ?></a></td>
              <!--td class='nom_song'><?php if($song->lyrics != ""){ ?><a class='song_link' href="<?php echo $song_link; ?>"><?php echo $song->name; ?></a><?php }else{ echo $song->name;} ?></td-->
              <td class='time_song'><?php echo MusColHelper::time_to_string($song->length); ?></td>
              <td class='song_player'><?php if( ($user->id && $this->params->get('displayplayer')) || $this->params->get('displayplayer') == 2 ){ echo $player; } ?>
            <?php if($song->filename != "" && $this->params->get('allowsongdownload')){ 
				if( $user->id || $this->params->get('allowsongdownload') == 2 ){ ?>
            <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" data-original-title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" rel="tooltip"><i class='icon-download-alt'></i></a>
            <?php } else  echo "<i rel='tooltip' data-original-title=\"".JText::_("FILE_REGISTERED_USERS")."\" class='icon-download-alt'></i>" ;
             } ?>
             </td>
             <td class='buy_song'><?php if($this->params->get('allowsongbuy') && $song->buy_link != ""){ ?>
            
            <a href="<?php echo $song->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_SONG'); ?>" rel="tooltip" data-original-title="<?php echo JText::_('BUY_THIS_SONG'); ?>" target="_blank"><i class="icon-shopping-cart"></i></a>
            
			<?php } ?></td>
                
                <td class='buy_song'>
              	<?php 				//new plugin access
				$dispatcher	= JDispatcher::getInstance();
				$plugin_ok = JPluginHelper::importPlugin('muscol');
				$results = $dispatcher->trigger('onDisplaySongAlbum', array ($song->id));
				?>
                </td>
                
            </tr>
            <?php $j = 1 - $j;
			
			if($song->filename) $k++ ; 
	} ?>
          </table>
    </div>
    <br/>
    <?php } ?>

    <?php if($user->id && $this->params->get('showalbumratings') ){ // show the rating system ?>
    <?php if(!$this->is_rated){ ?>
    <div class="disc_details">
      <h3><?php echo JText::_('RATE_THIS_ALBUM'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_stars(0,false,$this->album->id); ?></div>
    </div>
    <?php } else{ ?>
    <div class="disc_details">
      <h3><?php echo JText::_('YOUR_RATING_ON_THIS_ALBUM'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_stars($this->is_rated,false,$this->album->id); ?></div>
    </div>
    <?php } } ?>
    <div class="disc_details">
      <?php 		
$modules = JModuleHelper::getModules("muscol_album_stats");
$document	=JFactory::getDocument();
$renderer	= $document->loadRenderer('module');
$attribs 	= array();
$attribs['style'] = 'xhtml';
foreach ( @$modules as $mod ) 
{
	echo $renderer->render($mod, $attribs);
}
?>
    </div>
    <?php if($this->params->get('showalbumbookmarks', 1) ){ // show the bookmark system ?>
    <div class="disc_details">
      <h3><?php echo JText::_('Bookmark'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_bookmarks(); ?></div>
    </div>
    <?php } ?>
  </div>
  <div class="span5">
    <?php if($this->params->get('showalbumdetails') ){ // show the album details 
$genre_link = MusColHelper::get_genre_link($this->album->genre_id); ?>
    <?php 		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onDisplayAlbum', array ($this->album->id));
		?>
    <div class="disc_details  well well-small">
      <h3><?php echo JText::_('Data'); ?></h3>
      <?php if($this->params->get('showalbum_release', 1) == 1 || ($this->params->get('showalbum_release', 1) == 2 && $this->album->year)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Released' ); ?></strong> <span class="value_detailed_album"><?php echo MusColHelper::month_name($this->album->month); ?> <?php echo $this->album->year; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_format', 1) == 1 || ($this->params->get('showalbum_format', 1) == 2 && $this->album->format_name)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Format' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->format_name; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_types', 1) == 1 || ($this->params->get('showalbum_types', 1) == 2 && !empty($this->album->types))){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Type' ); ?></strong> <span class="value_detailed_album"><?php if(is_array($this->album->types)) echo implode(" / ",$this->album->types); ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_added', 1) == 1 || ($this->params->get('showalbum_added', 1) == 2 && $this->album->added)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'ADDED_ON' ); ?></strong> <span class="value_detailed_album"><?php echo JHTML::_('date', $this->album->added, JText::_('DATE_FORMAT_LC1')); ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_genre', 1) == 1 || ($this->params->get('showalbum_genre', 1) == 2 && $this->album->genre_name)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Genre' ); ?></strong> <span class="value_detailed_album"><a href="<?php echo $genre_link; ?>" title="<?php echo $this->album->genre_name; ?>"><?php echo $this->album->genre_name; ?></a></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_price', 1) == 1 || ($this->params->get('showalbum_price', 1) == 2 && $this->album->price != 0)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Price' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->price; ?> <?php echo $this->currency; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_length', 1) == 1 || ($this->params->get('showalbum_length', 1) == 2 && $this->album->length)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Length' ); ?></strong> <span class="value_detailed_album"><?php echo MusColHelper::time_to_string($this->album->length); ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_ndisc', 1) == 1 || ($this->params->get('showalbum_ndisc', 1) == 2 && $this->album->ndisc)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'N_OF_DISCS' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->ndisc; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_editiondate', 1) == 1 || ($this->params->get('showalbum_editiondate', 1) == 2 && $this->album->edition_year)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'EDITION_DATE' ); ?></strong> <span class="value_detailed_album"><?php echo MusColHelper::month_name($this->album->edition_month); ?>
      <?php if($this->album->edition_year != "0000") echo $this->album->edition_year; else echo $this->album->year; ?>
      </span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_editioncountry', 1) == 1 || ($this->params->get('showalbum_editioncountry', 1) == 2 && $this->album->edition_country)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Country' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->edition_country; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_label', 1) == 1 || ($this->params->get('showalbum_label', 1) == 2 && $this->album->label)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'Label' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->label; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_catalog', 1) == 1 || ($this->params->get('showalbum_catalog', 1) == 2 && $this->album->catalog_number)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'CATALOG_NUMBER' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->catalog_number; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_editiondetails', 1) == 1 || ($this->params->get('showalbum_editiondetails', 1) == 2 && $this->album->edition_details)){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'EDITION_DETAILS' ); ?></strong> <span class="value_detailed_album"><?php echo $this->album->edition_details; ?></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_tags', 1) == 1 ){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'TAGS' ); ?></strong> <span class="value_detailed_album"><?php                     
      for($k=0;$k < count($this->album->tags); $k++){ 
        if(isset($this->album->tags[$k]->tag_name)){
         
          echo MusColHelper::renderTag($this->album->tags[$k])." ";
        }
      
     } ?></span><br />
      <?php } ?>
    </div>
    <?php } ?>

<?php if($this->params->get('showzip') ){
      echo MusColHelper::zip_download($this->album->id); } ?>
      
    <div class="disc_details">
      <h3><?php echo JText::_('Review'); ?></h3>
      <?php echo $this->album->review; ?> </div>
    <?php if($this->params->get('showalbumcomments') ){ // show the comments 
		switch($this->params->get('commentsystem')){ 
			
			default: 
				if( $quants = count( $this->comments )){ ?>
    <div class="comments_title"><?php echo JText::_('Comments'). " (". $quants .")"; ?></div>
    <div class="comments">
      <?php $k = 0; 
						foreach($this->comments as $comment){ ?>
      <div class="comment comment_<?php echo $k; ?>">
        <div class="comment_name"><?php echo $comment->username; ?></div>
        <div class="date"><?php echo JHTML::_('date', $comment->date, JText::_('DATE_FORMAT_LC2')); ?></div>
        <div class="comment_text"><?php echo $comment->comment; ?></div>
      </div>
      <?php $k = 1 - $k;
						} ?>
    </div>
    <?php } ?>
    <?php if($user->id){ ?>
    <div class="well well-small">
      <h3 class="post_comment_title"><?php echo JText::_('POST_A_COMMENT'); ?></h3>
      <?php $uri = JFactory::getURI(); ?>
      <form action="<?php echo JRoute::_('index.php'); ?>" method="post">
        <textarea name="comment" class="span12" rows="5"></textarea>
        <input type="submit" class="btn" value="<?php echo JText::_('POST_COMMENT'); ?>" />
        <input type="hidden" name="album_id" value="<?php echo $this->album->id; ?>" />
        <input type="hidden" name="task" value="save_comment" />
        <input type="hidden" name="comment_type" value="album" />
        <input type="hidden" name="option" value="com_muscol" />
      </form>
    </div>
    <?php } ?>
    <?php break;
		}?>
    <?php } // end of show comments IF ?>
    <?php 		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onCommentsAlbum', array ($this->album->id));
		?>
  </div>
</div>
<?php if(count( $this->compilation )){ ?>
<div class="disc_details">
  <h3><?php echo JText::_('ITEMS_IN_THIS_COMPILATION'); ?></h3>
</div>
<?php foreach($this->compilation as $this->detail_album){ 
		echo $this->loadTemplate('album');
}?>
<br />
<?php } ?>
<?php if($this->params->get('showhits') ){ ?>
<div align="center"> <?php echo JHTML::image('components/com_muscol/assets/images/hits.png',JText::_('Hits'), array("title" => JText::_('Hits'))); ?> <span class="num_hits"><?php echo $this->album->hits; ?></span> </div>
<?php } ?>
<?php if($this->params->get('showalbumchrono') ){ // show the album chronology ?>
<?php
if($this->prev_album || $this->next_album){
	$prev_album_link= MusColHelper::routeAlbum($this->prev_album->id);
	$next_album_link= MusColHelper::routeAlbum($this->next_album->id);
	
?>
<table cellpadding='0' cellspacing='0' border='0' class='taula_next_album'>
  <tr>
    <td class='prev_album'><?php if($this->prev_album){  
				$image_attr = array(
								);
				$image_prev = MusColHelper::createThumbnail($this->prev_album->image, $this->prev_album->name, $this->params->get('thumb_size_album_1'), $image_attr);
			?>
      <a href="<?php echo $prev_album_link; ?>"><?php echo $image_prev; ?> &laquo; <?php echo $this->prev_album->name; ?> </a>
      <?php } ?></td>
    <td class='next_album_type'><?php echo $this->album->artist_name ." ". $this->album->display_group_name . " " . JText::_("CHRONOLOGY");  ?></td>
    <td class='next_album'><?php if($this->next_album){ 
				$image_attr = array(
								);
				$image_next = MusColHelper::createThumbnail($this->next_album->image, $this->next_album->name, $this->params->get('thumb_size_album_1'), $image_attr);
			?>
      <a href="<?php echo $next_album_link; ?>"><?php echo $this->next_album->name; ?> &raquo; <?php echo $image_next; ?></a>
      <?php } ?></td>
  </tr>
</table>
<?php } ?>
<?php } // end of show chronology IF ?>
<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
