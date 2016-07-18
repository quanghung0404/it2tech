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
                
<li class="grid_album">
    <div class="grid_album" align="center">
     <a href="<?php echo $link; ?>" class="<?php echo strtolower($this->detail_album->format_name); ?>">
        <div class="<?php echo strtolower($this->detail_album->format_name); ?>" align="center">
        <?php echo $image; ?>  
        </div>          
        
        <span class="artist"><?php echo $this->detail_album->artist_name; ?></span>
        <span class="name"><?php echo $this->detail_album->name; ?></span>
        <span class="year"><?php echo $this->detail_album->year; ?></span>
        <span class="year"><?php echo MusColHelper::show_stars($this->detail_album->points,false,false,false,true); ?></span>
         
    </a>
    </div>
</li>