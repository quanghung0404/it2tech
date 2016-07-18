<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser(); ?>

<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation('');
	}
?>


<form action="<?php echo JRoute::_('index.php'); ?>" method="get">
<div class="searchalbums">
    <?php echo MusColHelper::searchalbums_form_content($this->genre_list, $this->params->get('itemid')); ?>
    </div>
</form>


<form action="<?php echo JRoute::_('index.php'); ?>" method="get" name="adminForm" id="adminForm">
<div class="searchsongs">

    <?php echo MusColHelper::searchsongs_form_content($this->genre_list, $this->params->get('itemid')); ?>

</div>

<div class="pagination">
<?php echo $this->pagination->getResultsCounter(); ?>
</div>

<?php if( ( $user->id && $this->params->get('displayplayer') ) || $this->params->get('displayplayer') == 2 ){ ?>
<div class="player" align="center"><?php echo $this->player; ?></div>
<?php } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover table-striped">
    <tr>
        <td class="num_song" align="right" >#</td>
      <td class="nom_song"><?php echo JText::_('Song'); ?></td>
     
      <td class="song_player" align="right" ></td>
      <td class="buy_song" align="right"></td>
    </tr>
    <?php $k = 1; 
	$number = $this->pagination->limitstart + 1 ;
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
	for ($i = 0, $n=count( $this->songs ); $i < $n; $i++)	{ 
	
		$link_song = MusColHelper::routeSong($this->songs[$i]->id);
		$link_album = MusColHelper::routeAlbum($this->songs[$i]->album_id);
		$link_artist = MusColHelper::routeArtist($this->songs[$i]->artist_id);
		
		$file_link = MusColHelper::create_file_link($this->songs[$i]); 
		
		$play_button_start = strpos( $this->songs[$i]->player , "<!--PLAYBUTTON-->");
		$play_button_end = strpos( $this->songs[$i]->player ,"<!--/PLAYBUTTON-->" );
		
		$song =  $this->songs[$i] ;
		$player = $song->player ;
		
		if($play_button_end) {
			$play_button = substr($player, $play_button_start, $play_button_end + 18);
			$player = substr($player, 0, $play_button_start + 17) . substr($player, $play_button_end + 18);
		}
		
		$image_album = MusColHelper::createThumbnail($this->songs[$i]->image, $this->songs[$i]->album_name, 50, array("title" => $this->songs[$i]->album_name, "class" => "pull-left img-rounded image_song_list")); 
		
		?>
	 <tr class="tr_song_link" id="song_position_<?php echo $i; ?>">
        <td class='num_song'><span class='song_position'><?php echo $number; ?></span><?php echo $play_button; ?></td>
        <td class="nom_song">
      <?php echo $image_album; ?>
      <a class="the_song_name" href="<?php echo $link_song; ?>"><?php echo $this->songs[$i]->name; ?></a> · <a href="<?php echo $link_artist; ?>"><?php echo $this->songs[$i]->artist_name; ?></a>
      <br />
      <a href="<?php echo $link_album; ?>"><?php echo $this->songs[$i]->album_name; ?></a>
      </td>
     
      <td class="song_player"><?php if( ($user->id && $this->params->get('displayplayer')) || $this->params->get('displayplayer') == 2 ){ echo $player; } ?>
      
	  <?php if($this->songs[$i]->filename != "" && $this->params->get('allowsongdownload')){ 
				if( $user->id  || $this->params->get('allowsongdownload') == 2 ){ ?>
        <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" data-original-title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" rel="tooltip"><i class='icon-download-alt'></i></a>
        <?php } 
			else  echo "<i rel='tooltip' data-original-title=\"".JText::_("FILE_REGISTERED_USERS")."\" class='icon-download-alt'></i>" ;
             } ?>
        <?php if($this->params->get('allowsongbuy') && $this->songs[$i]->buy_link != ""){ ?>
        <a href="<?php echo $this->songs[$i]->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_SONG'); ?>" target="_blank"><?php echo JHTML::image('components/com_muscol/assets/images/buy.png','Buy'); ?></a>
        <?php 
		
		} ?></td>
             <td class='buy_song'>
		<?php         //new plugin access
        $dispatcher	= JDispatcher::getInstance();
        $plugin_ok = JPluginHelper::importPlugin('muscol');
        $results = $dispatcher->trigger('onDisplaySongAlbum', array ($this->songs[$i]->id));
        ?>
        </td>
	</tr>
    <?php $k = 3 - $k;
	$number++;
	 } ?>

</table>

<div class="muscol_pagination" align="center">
<?php echo $this->pagination->getListFooter(); ?>
</div>
</form>

<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>

