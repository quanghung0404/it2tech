<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

		$link_song = MusColHelper::routeSong($this->song->id);
		$link_album = MusColHelper::routeAlbum($this->song->album_id);
		$link_artist = MusColHelper::routeArtist($this->song->artist_id);
		
		$file_link = MusColHelper::create_file_link($this->song); 
		
		$play_button_start = strpos( $this->song->player , "<!--PLAYBUTTON-->");
		$play_button_end = strpos( $this->song->player ,"<!--/PLAYBUTTON-->" );
		
		$this->song =  $this->song ;
		$player = $this->song->player ;
		
		if($play_button_end) {
			$play_button = substr($player, $play_button_start, $play_button_end + 18);
			$player = substr($player, 0, $play_button_start + 17) . substr($player, $play_button_end + 18);
		}
		
		$image_album = MusColHelper::createThumbnail($this->song->image, $this->song->album_name, 50, array("title" => $this->song->album_name, "class" => "pull-left img-rounded image_song_list")); 
		
		?>
	 <tr class="tr_song_link" id="song_position_<?php echo $this->i; ?>">
        <td class='num_song'><span class='song_position'><?php echo $number; ?></span><?php echo $play_button; ?></td>
        <td class="nom_song">
      <?php echo $image_album; ?>
      <a class="the_song_name" href="<?php echo $link_song; ?>"><?php echo $this->song->name; ?></a> · <a href="<?php echo $link_artist; ?>"><?php echo $this->song->artist_name; ?></a>
      <br />
      <a href="<?php echo $link_album; ?>"><?php echo $this->song->album_name; ?></a>
      </td>
     
      <td class="song_player"><?php if( ($user->id && $this->params->get('displayplayer')) || $this->params->get('displayplayer') == 2 ){ echo $player; } ?>
      
	  <?php if($this->song->filename != "" && $this->params->get('allowsongdownload')){ 
				if( $user->id  || $this->params->get('allowsongdownload') == 2 ){ ?>
        <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" data-original-title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>" rel="tooltip"><i class='icon-download-alt'></i></a>
        <?php } 
			else  echo "<i rel='tooltip' data-original-title=\"".JText::_("FILE_REGISTERED_USERS")."\" class='icon-download-alt'></i>" ;
             } ?>
        <?php if($this->params->get('allowsongbuy') && $this->song->buy_link != ""){ ?>
        <a href="<?php echo $this->song->buy_link; ?>" title="<?php echo JText::_('BUY_THIS_SONG'); ?>" target="_blank"><?php echo JHTML::image('components/com_muscol/assets/images/buy.png','Buy'); ?></a>
        <?php 
		
		} ?></td>
             <td class='buy_song'>
		<?php         //new plugin access
        $dispatcher	= JDispatcher::getInstance();
        $plugin_ok = JPluginHelper::importPlugin('muscol');
        $results = $dispatcher->trigger('onDisplaySongAlbum', array ($this->song->id));
        ?>
        </td>
	</tr>