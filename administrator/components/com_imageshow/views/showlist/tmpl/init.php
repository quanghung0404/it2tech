<?php
/**
 * @version    $Id: view.html.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
$baseURL 	= $objJSNUtils->overrideURL();
$showlistTable = JTable::getInstance('showlist', 'Table');
$showlistID = JRequest::getVar('showListID');
$showlistTable->load($showlistID);
$baseurl = '';
if(isset($images->images)){
	$totalimage = count($images->images);
	if ( $totalimage > 0){
		if ( $selectMode != ''){
			$selectMode = ' '.$selectMode;
		}
		$i = 1;
		foreach ($images->images as $image)
		{
			$image = (array) $image;
			$checked 							= $this->imageSource->checkImageSelected($image['image_extid']);
			//$checked  = 1;
			$imageObj['isSelected']				= $checked;
			if ( $checked){
				$itemClass = 'media-item-is-selected';
			}else{
				$itemClass = 'media-item';
			}
			?>
<div class="<?php echo $itemClass;?><?php echo $selectMode;?>"
	id="<?php echo $image['image_extid'];?>">
	<?php
	$imageinfo = array(
								'image_id'			=> $image['image_extid'],
								'image_extid'		=> $image['album_extid'],
								'image_small'		=> $image['image_small'],
								'image_medium'		=> $image['image_medium'],
								'image_big'			=> $image['image_big'],
								'image_link'		=> $image['image_link'],
								'album_extid'		=> $image['album_extid'],
								'image_description'	=> $image['image_description'],
	);
	?>
	<div class="image_extid" name="image_extid"
		id="cat_<?php echo $image['album_extid'];?>"></div>
	<input class="item_detail" type="hidden"
		value="<?php echo  urlencode(json_encode($imageinfo));?>" />
	<div class="item-index">
	<?php echo $i.'/'.$totalimage;?>
	<?php if ( $selectMode != 'sync') {?>
		<button class="move-to-showlist">&nbsp;</button>
		<?php }?>
	</div>
	<div class="item-thumbnail">
		<a title="<?php echo $image['image_title'];?>"> <?php
		$baseurl = ($showlistTable->image_source_type == 'external')?'':JURI::root();
		?> <img src="<?php echo $baseurl.$image['image_small'];?>" width="80"
			style="max-height: 60px; max-width: 80px;" alt="image thumbnail" /> </a>
	</div>
	<div id="item-info-<?php echo $image['image_extid'];?>"
		class="item-info">
		<p>
		<?php echo $image['image_title'];?>
		</p>
		<p>
		<?php
		if ( strlen($image['image_description']) > 100 ){
			$i = 99;
			while($image['image_description'][$i] != ' ' && $i < strlen($image['image_description']) - 1){
				$i++;
			}
			echo substr($image['image_description'], 0, $i).'...';
		}else{
			echo $image['image_description'];
		}
		?>
		</p>
	</div>
	<div class="clearbreak"></div>
</div>
		<?php
		$i++;
		}
	}elseif($selectMode !='sync'){
		?>
<div class="jsn-bglabel item-no-found">
	<span class="jsn-icon64 jsn-icon-remove"></span>
	<?php echo JText::_('SHOWLIST_NOTICE_NO_IMAGES_FOUND');?>
</div>
	<?php
	}
	?>
<div class="clearbreak"></div>
	<?php } ?>
