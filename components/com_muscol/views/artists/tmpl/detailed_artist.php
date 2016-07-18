<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php 
$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

$link = MusColHelper::routeArtist($this->artist->id);
?>
        <div class="artist_detailed">
            <div class="artist_name">
                <a href='<?php echo $link; ?>'><?php echo $this->artist->artist_name; ?></a>
            </div>
            <table width="100%" cellpadding="0" cellspacing="0">
            	<tr>
                	<td valign="top" width="20%">
                        <div class="num_albums">
                        <?php echo JText::_('Albums'); ?>: <?php echo $this->artist->num_albums; ?>
                        </div>
                        <div class="num_songs">
                        <?php if($this->artist->num_songs){
							$link_songs =  MusColHelper::routeSongs($this->artist->id);
							$songs = "<a href='".$link_songs."'>".JText::_('Songs')."</a>";	
						}
						else $songs = JText::_('Songs'); ?>
                        <?php echo $songs; ?>: <?php echo $this->artist->num_songs; ?>
                        </div>
                    </td>
                    <td valign="top" style="text-align:left;">
                    <?php foreach($this->artist->albums as $album){
						$link_album = MusColHelper::routeAlbum($album->id);
						$image_album = MusColHelper::createThumbnail($album->image, $album->name, $this->params->get('thumb_size_artists_1'), array("title" => $album->name)); ?>
                        <a class="artists_album" href="<?php echo $link_album; ?>"><?php echo $image_album; ?></a>
                    <?php } ?>
                    </td>
                </tr>
            </table>            
		</div>