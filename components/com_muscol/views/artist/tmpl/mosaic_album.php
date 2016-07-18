<?php
defined('_JEXEC') or die('Restricted access');

$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

$image_attr = array(
					"class" => "image_115_llista"
					);
$link= MusColHelper::routeAlbum($this->detail_album->id);
$image = MusColHelper::createThumbnailWH($this->detail_album->image, $this->detail_album->name, $this->params->get('thumb_size_artist_2'), $this->params->get('thumb_size_artist_2'), $image_attr);
if(!(($this->i + 1) % 4)) $class = "last_in_row" ;
else $class = "";
?>
<a href="<?php echo $link; ?>" class="mosaic_image <?php echo $class; ?>" title="<?php echo $this->detail_album->name; ?>" ><?php echo $image; ?></a>