<?php
defined('_JEXEC') or die('Restricted access');

$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

$image_attr = array(
					"class" => "image_115_llista"
					);
$link= MusColHelper::routeAlbum($this->detail_album->id);

$image = MusColHelper::createThumbnail($this->detail_album->image, $this->detail_album->name, $this->params->get('thumb_size_artist_2'), $image_attr);

?>

<div class='data row-fluid'> <a class='image pull-left' href='<?php echo $link; ?>'><?php echo $image; ?></a> <span class="pull-right">
  <?php $comments_image = JHTML::image('components/com_muscol/assets/images/comment.png',JText::_('COMMENTS'),array("title" => JText::_('COMMENTS') ));
						$songs_image = JHTML::image('components/com_muscol/assets/images/music.png',JText::_('SONGS'),array("title" => JText::_('SONGS') ));?>
  <?php echo $songs_image . " " . $this->detail_album->num_songs; ?> <?php echo $comments_image . " " . $this->detail_album->num_comments; ?></span>
  <div class="album_name"> <a href='<?php echo $link; ?>'> <?php echo $this->detail_album->name; ?> <?php echo $this->detail_album->subtitle; ?> </a> <span class="pull-right">
    <?php 
                                
                                for($k=0;$k < count($this->detail_album->tags); $k++){ 
								if(isset($this->detail_album->tags[$k]->tag_name)){
                                    $image_attr = array(
                                            "title" => JText::_( $this->detail_album->tags[$k]->tag_name )
                                            );
                                    $tag_image = JHTML::image('images/tags/' . $this->detail_album->tags[$k]->icon , JText::_( $this->detail_album->tags[$k]->tag_name ) , $image_attr );
                                    if($this->detail_album->tags[$k]->icon != "") echo " ".$tag_image;
                                
                             } }
							 ?>
    </span> </div>
  <div class="artist_name"> <?php echo $this->detail_album->artist_name; ?> <?php echo $this->detail_album->subartist; ?> </div>
  <div class="rating hidden-phone">
    <?php if($this->params->get('showalbum_adminrating', 1)){ ?>
    <?php echo MusColHelper::show_stars($this->detail_album->points,true); ?>
    <?php } ?>
    <?php if($this->params->get('showalbum_adminrating', 1) && $this->params->get('showalbum_userrating', 1)){ ?>
    /
    <?php } ?>
    <?php if($this->params->get('showalbum_userrating', 1)){ ?>
    <?php echo MusColHelper::show_stars($this->detail_album->average_rating); ?>
    <?php } ?>
    <?php if($this->params->get('showalbum_added', 1) == 1 || ($this->params->get('showalbum_added', 1) == 2 && !empty($this->detail_album->added))){ ?>
    <span class="hidden-phone pull-right">
    <?php $image_attr = array(
                                "title" => JText::_('ADDED_ON')
                                );
                            echo JHTML::image('components/com_muscol/assets/images/date.png', JText::_('ADDED_ON') , $image_attr );
?>
    <?php echo JHTML::_('date', $this->detail_album->added, JText::_('DATE_FORMAT_LC3')); ?>
    </span>
    <?php } ?>
  </div>
  <table cellpadding="0" cellspacing="0" width="100%"  class="details hidden-phone">
    <tr>
      <td valign="top" width="33%"><table cellpadding="0" cellspacing="0" width="100%">
          <?php if($this->params->get('showalbum_release', 1) == 1 || ($this->params->get('showalbum_types', 1) == 2 && !empty($this->detail_album->year))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'RELEASED' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->year; ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_format', 1) == 1 || ($this->params->get('showalbum_types', 1) == 2 && !empty($this->detail_album->format_name))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'FORMAT' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->format_name; ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_types', 1) == 1 || ($this->params->get('showalbum_types', 1) == 2 && !empty($this->detail_album->types))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'TYPE' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo implode(" / ",$this->detail_album->types); ?></td>
          </tr>
          <?php } ?>
        </table></td>
      <td valign="top" width="33%"><table cellpadding="0" cellspacing="0" width="100%">
          <?php if($this->params->get('showalbum_types', 1) == 1 || ($this->params->get('showalbum_types', 1) == 2 && !empty($this->detail_album->length))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'LENGTH' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo MusColHelper::time_to_string($this->detail_album->length); ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_ndisc', 1) == 1 || ($this->params->get('showalbum_ndisc', 1) == 2 && !empty($this->detail_album->ndisc))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'N_DISCS' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->ndisc; ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_label', 1) == 1 || ($this->params->get('showalbum_label', 1) == 2 && !empty($this->detail_album->label))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'LABEL' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->label; ?></td>
          </tr>
          <?php } ?>
        </table></td>
      <td valign="top" width="33%"><table cellpadding="0" cellspacing="0" width="100%">
          <?php if($this->params->get('showalbum_genre', 1) == 1 || ($this->params->get('showalbum_genre', 1) == 2 && !empty($this->detail_album->genre_name))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'GENRE' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->genre_name; ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_price', 1) == 1 || ($this->params->get('showalbum_price', 1) == 2 && !empty($this->detail_album->price))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'PRICE' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->price; ?> <?php echo $this->currency; ?></td>
          </tr>
          <?php } ?>
          <?php if($this->params->get('showalbum_catalog', 1) == 1 || ($this->params->get('showalbum_catalog', 1) == 2 && !empty($this->detail_album->catalog_number))){ ?>
          <tr>
            <td valign="top" class="label_detailed_album"><?php echo JText::_( 'CAT_N' ); ?></td>
            <td valign="top" class="value_detailed_album"><?php echo $this->detail_album->catalog_number; ?></td>
          </tr>
          <?php } ?>
        </table></td>
    </tr>
  </table>
  <?php
             if(count($this->detail_album->subalbums)){
                 ?>
  <div class="subalbums">
    <?php                 foreach($this->detail_album->subalbums as $subalbum) {
                    $image_attr = array(
                                "class" => "image_40_llista"
                                );
                    $link= MusColHelper::routeAlbum($subalbum->id );
					
					$image = MusColHelper::createThumbnail($subalbum->image, $subalbum->name, $this->params->get('thumb_size_artist_1'), $image_attr);
                    //$image = JHTML::image('images/albums/thumbs_40/' . $subalbum->image , $subalbum->name , $image_attr );
					?>
    <a href='<?php echo $link; ?>'><?php echo $image; ?></a>
    <?php } ?>
  </div>
  <?php } ?>
</div>
