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
$isWriable = true;
?>
<script type="text/javascript">
(function($){
	<?php if($offset==0 && $pagination){ ?>
	JSNISImageGrid.imageTotal = <?php echo ($countImages!='')?$countImages:-1;?>;
	<?php }?>
	<?php
		if($sourceName == 'folder'){
	?>
	JSNISImageGrid.generatedThumbnailNumber = 0;
	<?php if($selectMode == 'sync'){ ?>
	if(!JSNISImageGrid.applyButton.hasClass('disabledOnclick')&&JSNISImageGrid.imageTotal > 0){
		JSNISImageGrid.disableSaveButton(true);
	}
	<?php }?>
	$('#<?php echo $newProgressBarId; ?> .bar').css('width',0);
	$('#<?php echo $newProgressBarContainerId;?>').hide();
	$('img.image_img').load(function(){
		$(this).parent().removeClass('isloading');
		JSNISImageGrid.progressBar("<?php echo $offset;?>","<?php echo $newProgressBarContainerId?>","<?php echo $newProgressBarId?>");
	});
	if(JSNISImageGrid.imageTotal > 0){
		$('#<?php echo $newProgressBarContainerId?>').show();
	}
	<?php
	}
	?>
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
	<?php
	$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
	$baseURL 	= $objJSNUtils->overrideURL();
	$showlistTable = JTable::getInstance('showlist', 'Table');
	$showlistID = JRequest::getVar('showListID');
	$showlistTable->load($showlistID);
	$baseurl = '';
	?>
	<?php if ($showlistTable->image_source_name == 'folder') {?>
	<?php if (!$objJSNUtils->folderIsWritable('images')) {
		$isWriable = false;
		?>
<script type="text/javascript">
		alert('<?php echo JText::sprintf('SHOWLIST_FOLDER_IS_UNWRIABLE', DS.'images', array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false)); ?>')
	</script>
		<?php } elseif (!$objJSNUtils->folderIsWritable('images'.DS.'jsn_is_thumbs') && is_dir(JPATH_ROOT.DS.'images'.DS.'jsn_is_thumbs')) {
			$isWriable = false;
			?>
<script type="text/javascript">
		alert('<?php echo JText::sprintf('SHOWLIST_FOLDER_IS_UNWRIABLE', 'images/jsn_is_thumbs', array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false)); ?>')
	</script>
			<?php } ?>
			<?php } ?>
			<?php
			if(isset($images->images)){
				$totalimage = count($images->images);
				if ( $totalimage > 0){
					if ( $selectMode != ''){
						$selectMode = ' '.$selectMode;
					}
					$i = 1+$offset;
					$time = 100;
					foreach ($images->images as $image)
					{
						$image 	 		= (array) $image;
						$image['image_title'] = $objJSNUtils->convertSmartQuotes($image['image_title']);
						$image['image_description'] = $objJSNUtils->convertSmartQuotes($image['image_description']);

						$processedImage = array(
								'image_id'			=> (string) $image['image_extid'],
								'image_extid'		=> (string) $image['album_extid'],
								'image_small'		=> (string) $image['image_small'],
								'image_medium'		=> (string) $image['image_medium'],
								'image_big'			=> (string) $image['image_big'],
								'image_link'		=> (string) $image['image_link'],
								'album_extid'		=> (string) $image['album_extid'],
								'image_description'	=> strip_tags(trim((string) $image['image_description']), '<b><i><s><strong><em><strike><u><br>'),
								'image_title'		=> (string) $image['image_title']);
						$checked = $imageSource->checkImageSelected($image['image_extid']);
						if ($checked || $syncIsSelected)
						{
							$itemClass = 'media-item-is-selected';
						}
						else
						{
							$itemClass = 'media-item';
						}
						?>
						<?php if ($showlistTable->image_source_name == 'folder' && $isWriable) {?>
<script type="text/javascript">
			(function($){
				$(document).ready(function () {
					window.setTimeout("JSNISImageGrid.createThumbForPreview('item_id_<?php echo $i; ?>', 'input_image_thumb_id_<?php echo $i; ?>', '<?php echo urlencode($image['album_extid']);?>', '<?php echo urlencode($image['image_title']);?>', '<?php echo urlencode($image['image_big']);?>');", <?php echo $time;?>);
				});
			})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
			</script>
						<?php } ?>
<div class="<?php echo $itemClass;?><?php echo $selectMode;?>"
	id="<?php echo md5($image['image_extid']);?>">
	<input class="item_id" type="hidden"
		value="<?php echo urlencode($image['image_extid']);?>" /> <input
		class="item_extid" type="hidden"
		value="<?php echo urlencode($image['album_extid']);?>" /> <input
		class="item_detail" type="hidden"
		value="<?php echo htmlspecialchars(json_encode($processedImage), ENT_COMPAT, 'UTF-8');?>" />
	<input class="img_thumb" type="hidden"
		value="<?php echo urlencode($image['image_big']); ?>"
		id="input_image_thumb_id_<?php echo $i; ?>" />
	<div class="item-index">
	<?php echo $i.'/'.$totalimage;?>
	<?php if ( $selectMode != 'sync') {?>
		<button class="move-to-showlist pull-right icon-ok">&nbsp;</button>
		<?php }?>
	</div>
	<div class="item-thumbnail">
		<a
			class="item_link<?php echo ($showlistTable->image_source_name == 'folder')?' isloading':'';?>"
			title="<?php echo $image['image_title'];?>"> <?php
			$baseurl = ($showlistTable->image_source_type == 'external')?'':JURI::root();
			?> <?php if ($showlistTable->image_source_name != 'folder') {?> <img
			id="item_id_<?php echo $i; ?>" class="image_img"
			src="<?php echo $baseurl.$image['image_small'];?>"
			style="max-height: 60px; max-width: 80px;" alt="" /> <?php } else {?>

			<img id="item_id_<?php echo $i; ?>" class="image_img"
			src="<?php echo (!$isWriable)?$baseurl.$image["image_small"]:'';?>"
			style="max-height: 60px; max-width: 80px;" alt="" /> <?php } ?> </a>
	</div>
	<div class="item-info">
		<p>
			<strong><?php echo $objJSNUtils->wordLimiter(strip_tags($image['image_title']), 20);?>
			</strong>
		</p>
		<p>
		<?php echo $objJSNUtils->wordLimiter(strip_tags($image['image_description']), 30);?>
		</p>
		<p>
		<?php echo @$image['image_link'];?>
		</p>
	</div>
	<div class="clearbreak"></div>
</div>
		<?php
		$i++;
		$time = $time + 100;
					}
				}elseif($selectMode !='sync'){
					?>
<div class="jsn-bglabel item-no-found">
	<span class="jsn-icon64 jsn-icon-remove"></span>
	<?php echo JText::_('SHOWLIST_NOTICE_NO_IMAGES_FOUND'); ?>
</div>
<?php
	}else{
	?>
<?php } ?>
<?php } ?>
