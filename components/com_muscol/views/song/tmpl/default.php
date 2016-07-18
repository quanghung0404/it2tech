<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser(); 
?>
<?php if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation($this->song->letter);
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>
<?php echo MusColHelper::edit_button_song($this->song->id); ?>
<?php 

$artist_link = MusColHelper::routeArtist($this->song->real_artist_id);
$album_link = MusColHelper::routeAlbum($this->song->album_id);
?>


<div class='page-header'>
<a class="pull-right thumbnail" href='<?php echo $album_link; ?>'> <?php echo MusColHelper::createThumbnail($this->song->image, $this->song->name, $this->params->get('thumb_size_song_1'));?> </a>
  <h2 class='artista_disc'> <a href='<?php echo $artist_link; ?>'>
    <?php if($this->song->real_artist_id != $this->song->artist_id){ 
						echo $this->song->real_artist_name; 
					}else{
						echo $this->song->artist_name . ' '. $this->song->subartist; 
					}?>
    </a> </h2>
  
  <h1 class='album_disc'><?php echo $this->song->name; ?></h1>
  <div class='year_disc'> <a href='<?php echo $album_link; ?>'><?php echo $this->song->album_name; ?></a> (<?php echo $this->song->year; ?>) </div>
  <?php if($this->params->get('showsongratings') ){ // show the rating system ?>
  <div class="rating"> <?php echo MusColHelper::show_stars_song($this->average_rating); ?> <span class="num_ratings"><?php echo $this->num_rating ; ?>
    <?php if( $this->num_rating == 1) echo JText::_('Rating'); else echo JText::_('Ratings'); ?>
    </span> </div>
  <?php } ?>
  <?php if($this->song->filename){ 
				$file_link = MusColHelper::create_file_link($this->song);
				?>
  <div class="player">
    <?php if($user->id){
						 ?>
    <?php if($this->params->get('displayplayer')) echo $this->player; ?>
    <?php if($this->params->get('allowsongdownload')){ ?>
    <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>"><?php echo JHTML::image('components/com_muscol/assets/images/music.png','File'); ?></a>
    <?php } ?>
    <?php if($this->params->get('allowsongbuy') && $this->song->buy_link != ""){ ?>
    <a href="<?php echo $this->song->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_SONG'); ?>" target="_blank"><?php echo JHTML::image('components/com_muscol/assets/images/buy.png','Buy'); ?></a>
    <?php } ?>
    <?php } else { ?>
    <?php if($this->params->get('displayplayer') == 2) echo $this->player; ?>
    <?php if($this->params->get('allowsongdownload') == 2){ ?>
    <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>"><?php echo JHTML::image('components/com_muscol/assets/images/music.png','File'); ?></a>
    <?php } else if($this->params->get('allowsongdownload') == 1){
							echo JHTML::image('components/com_muscol/assets/images/music.png','File',array("title" => JText::_("FILE_REGISTERED_USERS")));
						}?>
    <?php } ?>
  </div>

  <?php } ?>

  <?php if($this->params->get('showsong_tags', 1) == 1 ){ ?>
  <strong class="label_detailed_album"><?php echo JText::_( 'TAGS' ); ?></strong> <?php                     
      for($k=0;$k < count($this->song->tags); $k++){ 
        if(isset($this->song->tags[$k]->tag_name)){
         
          echo MusColHelper::renderTagSong($this->song->tags[$k])." ";
        }
      
     }  ?>
  <?php } ?>

  <?php
        //new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onDisplaySongAlbum', array ($this->song->id));
		?>
</div>
<div class="row-fluid">
  <div class="span8">
    <?php if($this->song->video != ""){ 
$video_pieces = explode("?",$this->song->video) ;
if(count($video_pieces) == 2 ){ // http://www.youtube.com/watch?v=6hzrDeceEKc
	$youtube_video_id = str_replace("v=", "", $video_pieces[1]);
}
else{ // http://www.youtube.com/v/6hzrDeceEKc OR 6hzrDeceEKc
	$youtube_video_id = str_replace("http://www.youtube.com/v/", "", $this->song->video);
}
$youtube_video_url = "http://www.youtube.com/v/" . $youtube_video_id ;
?>
    <div class='video' align="center">
      <object width="<?php echo $this->params->get('youtube_width'); ?>" height="<?php echo $this->params->get('youtube_height'); ?>">
        <param name="movie" value="<?php echo $youtube_video_url; ?>">
        </param>
        <param name="allowFullScreen" value="true">
        </param>
        <param name="allowscriptaccess" value="always">
        </param>
        <embed src="<?php echo $youtube_video_url; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<?php echo $this->params->get('youtube_width'); ?>" height="<?php echo $this->params->get('youtube_height'); ?>"></embed>
      </object>
    </div>
    <?php } ?>
    <?php if($user->id && $this->params->get('showsongratings') ){ // show the rating system ?>
    <?php if(!$this->is_rated){ ?>
    <div class="disc_details">
      <h3><?php echo JText::_('RATE_THIS_SONG'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_stars_song(0,false,$this->song->id); ?></div>
    </div>
    <?php } else{ ?>
    <div class="disc_details">
      <h3><?php echo JText::_('YOUR_RATING_ON_THIS_SONG'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_stars_song($this->is_rated,false,$this->song->id); ?></div>
    </div>
    <?php } } ?>
    <?php if($this->song->lyrics != ""){ ?>
    <div class="review">
      <h3><?php echo JText::_('Lyrics'); ?></h3>
      <div align='center'>
        <table>
          <tr>
            <td class='lyrics'><?php echo $this->song->lyrics; ?></td>
          </tr>
        </table>
        <br/>
      </div>
    </div>
    <?php } ?>
    <?php if($this->song->songwriters != ""){ ?>
    <div class='review songwriters'>
      <h3><?php echo JText::_('Songwriters'); ?></h3>
      <?php echo str_replace("\n","<br/>",$this->song->songwriters); ?> </div>
    <?php } ?>
    <?php if($this->song->chords != ""){ ?>
    <div class='review chords'>
      <h3><?php echo JText::_('Chords'); ?></h3>
      <pre>
<?php echo $this->song->chords; ?>
</pre>
    </div>
    <?php } ?>
    <div class="disc_details">
      <?php 		
$modules = JModuleHelper::getModules("muscol_song_stats");
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
    <?php if($this->params->get('showsongbookmarks', 1) ){ // show the bookmark system ?>
    <div class="disc_details">
      <h3><?php echo JText::_('Bookmark'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_bookmarks(); ?></div>
    </div>
    <?php } ?>
  </div>
  <div class="span4">
    <?php if($this->song->review != ""){ ?>
    <div class='review'>
      <h3><?php echo JText::_('Review'); ?></h3>
      <?php echo $this->song->review; ?> </div>
    <?php } ?>
    <?php if($this->params->get('showsongcomments') ){ // show the comments 
		switch($this->params->get('commentsystem')){ 
				 
			default: 
				if( $quants = count( $this->comments )){ ?>
    <h3 class="comments_title"><?php echo JText::_('Comments'). " (". $quants .")"; ?></h3>
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
        <textarea name="comment" class="span12"></textarea>
        <br />
        <input type="submit" class="btn" value="<?php echo JText::_('POST_COMMENT'); ?>" />
        <input type="hidden" name="album_id" value="<?php echo $this->song->id; ?>" />
        <input type="hidden" name="task" value="save_comment" />
        <input type="hidden" name="comment_type" value="song" />
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
		$results = $dispatcher->trigger('onCommentsSong', array ($this->song->id));
		?>
  </div>
</div>
<?php if($this->params->get('showhits') ){ ?>
<div align="center"> <?php echo JHTML::image('components/com_muscol/assets/images/hits.png',JText::_('Hits'), array("title" => JText::_('Hits'))); ?> <span class="num_hits"><?php echo $this->song->hits; ?></span> </div>
<?php } ?>
<?php
if($this->prev_song || $this->next_song){
	
	$prev_song_link= MusColHelper::routeSong($this->prev_song->id);
	$next_song_link= MusColHelper::routeSong($this->next_song->id);
?>
<table cellpadding='0' cellspacing='0' border='0' class='taula_next_album'>
  <tr>
    <td class='prev_album'><?php if($this->prev_song){ ?>
      <a href="<?php echo $prev_song_link; ?>">&laquo; <?php echo $this->prev_song->name; ?></a>
      <?php } ?></td>
    <td class='next_album_type'><?php echo $this->song->album_name; ?> <?php echo JText::_('Songs'); ?></td>
    <td class='next_album'><?php if($this->next_song){ ?>
      <a href="<?php echo $next_song_link; ?>"><?php echo $this->next_song->name; ?> &raquo;</a>
      <?php } ?></td>
  </tr>
</table>
<?php } ?>
<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
