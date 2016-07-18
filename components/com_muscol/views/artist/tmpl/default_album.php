<?php
defined('_JEXEC') or die('Restricted access');

$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

$image_attr = array(
					"class" => "image_40_llista"
					);
$link= MusColHelper::routeAlbum($this->detail_album->id);

$image = MusColHelper::createThumbnail($this->detail_album->image, $this->detail_album->name, $this->params->get('thumb_size_artist_1'), $image_attr);

?>
<div class="data">
<a class='llista_artista' style='width:630px;' href='<?php echo $link; ?>'>
    <table border='0' cellpadding='0' cellspacing='0'>
        <tr>
            <td><?php echo $image; ?> </td>
            <td style='padding-left:5px;'><?php echo $this->detail_album->name; ?>
                <div class='year_llista'>
                <?php echo $this->detail_album->year." ".MusColHelper::show_stars($this->detail_album->points,false,false,false,true); ?>
                </div>
            </td>
        </tr>
    </table>
</a>
</div>