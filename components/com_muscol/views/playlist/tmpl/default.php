<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser(); ?>
<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation('');
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>

 <?php if(($this->playlist->user_id == $user->id) || !$this->playlist->id): 
$edit_link  = JRoute::_('index.php?option=com_muscol&view=playlist&task=edit_playlist&id='. $this->playlist->id . $itemid);
 $delete_link = JRoute::_( 'index.php?option=com_muscol&task=remove_playlist&id='.$this->playlist->id ); 
  ?>

<div class="btn-group pull-right">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="<?php echo $edit_link; ?>"><i class="icon-pencil"></i> <?php echo JText::_("EDIT_THIS_PLAYLIST"); ?></a></li>
    <li><a href="<?php echo $delete_link; ?>"><i class="icon-trash"></i> <?php echo JText::_("DELETE_THIS_PLAYLIST"); ?></a></li>
  </ul>
</div>
        
 
  <?php endif; ?>
  
<div class="page-header">
  <h1 class="playlist_name"><?php echo $this->playlist->title; ?></h1>
  <div class="created_by"><?php echo JText::_('CREATED_BY') . " " . $this->playlist->username; ?></div>
 
  </div>
  <?php if( isset($this->playlist->description) ): ?>
  <div class="review"> <?php echo $this->playlist->description; ?> </div>
  <?php endif; ?>
  <?php if( ( $user->id && $this->params->get('displayplayer') ) || $this->params->get('displayplayer') == 2 ){ ?>
  <div class="player" align="center"><?php echo $this->player; ?> <a class='hasTooltip' data-original-title='<?php echo JText::_('SET_PLAYLIST_AS_CURRENT'); ?>' href="javascript:set_current_playlist(<?php echo $this->playlist->id; ?>);"><?php echo JHTML::image('components/com_muscol/assets/images/set_playlist.png','Set playlist as current', array("title" => JText::_("SET_PLAYLIST_AS_CURRENT"))) ; ?></a></div>
  <?php } ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover table-striped">
    <tr>
      <td class="sectiontableheader" align="right" width="5%">#</td>
      
      <td class="sectiontableheader" width="75%"><?php echo JText::_('SONG'); ?></td>
      
      <td class="sectiontableheader" align="right" ></td>
      <td class="sectiontableheader" ></td>
      
      <td class="sectiontableheader" align="right" ></td>
      
    </tr>
    <?php $k = 1; 
	
	$this->playlist->types = explode(",", $this->playlist->types );
	
	for ($i = 0, $n=count( $this->songs ); $i < $n; $i++)	{ 
		if(!empty($this->songs[$i])){
			
			$song =  $this->songs[$i] ;
			$player = $song->player ;
			
			$link_song = MusColHelper::routeSong($song->id);
			$link_album = MusColHelper::routeAlbum($song->album_id);
			$link_artist = MusColHelper::routeArtist($song->artist_id);
			$file_link = MusColHelper::create_file_link($song);
			$delete_link = JRoute::_( 'index.php?option=com_muscol&task=remove_song_playlist&id='.$this->playlist->id.'&song_positions[]='. $i ); 
			
			$play_button_start = strpos( $player , "<!--PLAYBUTTON-->");
		$play_button_end = strpos( $player ,"<!--/PLAYBUTTON-->" );
		
		if($play_button_end) {
			$play_button = substr($player, $play_button_start, $play_button_end + 18);
			$player = substr($player, 0, $play_button_start + 17) . substr($player, $play_button_end + 18);
		}
		
		$image_album = MusColHelper::createThumbnail($song->image, $song->album_name, 50, array("title" => $song->album_name, "class" => "pull-left img-rounded image_song_list")); ?>
  
      <tr class="sectiontableentry<?php echo $k; ?> tr_song_link" id="song_position_<?php echo $i; ?>">
    
       <td class='num_song'><span class='song_position'><?php echo ($i + 1); ?></span><?php echo $play_button; ?></td>
      
      <td class="nom_song">
      <?php echo $image_album; ?>
      <a class="the_song_name" href="<?php echo $link_song; ?>"><?php echo $song->name; ?></a>
      <br />
      <a href="<?php echo $link_artist; ?>"><?php echo $song->artist_name; ?></a> · <a href="<?php echo $link_album; ?>"><?php echo $song->album_name; ?></a>
      </td>
      
      <td class="song_type hidden-phone"><?php if($this->playlist->types[$i] == "v") echo "<i class='icon-facetime-video' rel='tooltip' data-original-title='".JText::_('Video')."'></i>" ; else echo "<i class='icon-music' rel='tooltip' data-original-title='".JText::_('Audio')."'></i>"; ?></td>
    
      <td class="song_player">
	  <!--div class="btn-group"-->
	  <?php if( ($user->id && $this->params->get('displayplayer')) || $this->params->get('displayplayer') == 2 ){ echo $player; } ?>
      
      <?php if($song->filename != "" && $this->params->get('allowsongdownload')){ 
					if( $user->id  || $this->params->get('allowsongdownload') == 2 ){ ?>
        <a class="" href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" data-original-title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" rel="tooltip"><i class='icon-download-alt'></i></a>
        <?php } 
				else  echo "<i rel='tooltip' data-original-title=\"".JText::_("FILE_REGISTERED_USERS")."\" class='icon-download-alt'></i>" ;
				 } ?>
        <?php if($this->params->get('allowsongbuy') && $song->buy_link != ""){ ?>
        <a href="<?php echo $song->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_SONG'); ?>" target="_blank"><?php echo JHTML::image('components/com_muscol/assets/images/buy.png','Buy'); ?></a>
        <?php } ?>
        
       
        <?php if($this->playlist->user_id == $user->id || !$this->playlist->id){ 
		
		?>
        <a class="" href="<?php echo $delete_link; ?>" rel="tooltip" data-original-title="<?php echo JText::_("DELETE_ITEM_PLAYLIST"); ?>"><i class="icon-trash"></i></a>
        <?php } ?>
        <!--/div-->
        </td>
        
      <td class='buy_song'><?php         //new plugin access
        $dispatcher	= JDispatcher::getInstance();
        $plugin_ok = JPluginHelper::importPlugin('muscol');
        $results = $dispatcher->trigger('onDisplaySongAlbum', array ($song->id));
        ?></td>
    </tr>
    <?php } // end if
	$k = 3 - $k; }
	 ?>
  </table>

<?php 		
		$modules = JModuleHelper::getModules("muscol_playlist_stats");
		$document	=JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod ) 
		{
			echo $renderer->render($mod, $attribs);
		}
		?>
<?php if($this->params->get('showplaylistcomments') && $this->playlist->id){ // show the comments 
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
    <br />
    <input type="submit" class="btn" value="<?php echo JText::_('POST_COMMENT'); ?>" />
    <input type="hidden" name="album_id" value="<?php echo $this->playlist->id; ?>" />
    <input type="hidden" name="task" value="save_comment" />
    <input type="hidden" name="comment_type" value="playlist" />
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
		$results = $dispatcher->trigger('onCommentsPlaylist', array ($this->playlist->id));
		?>
<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
