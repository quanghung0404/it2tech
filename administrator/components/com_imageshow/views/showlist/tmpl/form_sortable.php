<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: form_sources.php 13727 2012-07-02 08:06:10Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
$task 			= JRequest::getWord('task','','post');
$showlistID 	= JRequest::getVar('cid');
$showlistID 	= $showlistID[0];
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$baseURL 		= $objJSNUtils->overrideURL();
$showlistTable 	= JTable::getInstance('showlist', 'Table');
$showlistTable->load($showlistID);
if ($showlistTable->image_source_name != '')
{
	$this->imageSource->loadScript();
	$sync = $this->imageSource->getShowlistMode();
}
else
{
	$sync = false;
}
if ($this->selectMode != '')
{
	$selectMode = ' '.$this->selectMode;
}
else
{
	$selectMode	='';
}
?>
<script type="text/javascript">
	JSNISImageShow.checkThumbCallBack = function()
	{
		<?php
			if ($this->totalImage)
			{
		?>
		JSNISImageGrid.reloadImageSource('<?php echo $this->albumID; ?>');
		<?php
			}
		?>
	};
</script>
<div class="jsn-showlist-media" id="jsn-showlist-media-layout">
	<div class="ui-layout-west" id="panel-west">
		<div class="source-media-header">
			<h3>
			<?php echo JText::_('SHOWLIST_SHOWLIST_SOURCE_IMAGES'); ?>
			</h3>
		</div>
		<div class="source-media-panel-container">
			<div class="panel-right jsn-bootstrap">
				<div class="panel-header clearafter jsn-iconbar"
					id="source-media-header">
					<a href="javascript: void(0);" class="media-show-grid pull-left"
						title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_SELECT_THUMB_PRESENTATION_MODE'); ?>">
						<i class="jsn-icon16 jsn-icon-layout jsn-icon-thumbnails disabled"></i>
					</a> <a href="javascript: void(0);"
						class="media-show-list pull-left"
						title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_SELECT_DETAILS_PRESENTATION_MODE'); ?>">
						<i
						class="jsn-icon16 jsn-icon-layout jsn-icon-thumbnaildetails disabled"></i>
					</a> <a href="javascript: void(0);" class="pull-right"
						id="move-selected-media-source"
						title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_ADD_SELECTED_IMAGE'); ?>">
						<i class="jsn-icon16 jsn-icon-ok disabled"></i> </a>
				</div>
				<div class="source-media-container" id="source-media-container">
					<div class="source-items showgrid" id="source-items">
					<?php if($this->imageSource->_source['sourceDefine']->pagination):?>
						<div id="show-more-items">
							<button id="show-more-items-btn" class="btn">
							<?php echo JText::_('SHOWLIST_IMAGE_LOAD_MORE_IMAGES'); ?>
							</button>
							<input type="hidden" id="cateNameInShowlist" value="" />
						</div>
						<?php endif;?>
					</div>
				</div>
			</div>

			<?php
			$imageFolder = $this->imageSource->getCategories();
			$folderlists = json_decode(json_encode((array) simplexml_load_string('<nodes>'.$imageFolder.'</nodes>')),1);
			$imageFolder  =  @$this->imageSource->convertXmlToTreeMenu($folderlists['node'],$this->catSelected);
			?>
			<div class="panel-left">
				<div class="panel-header clearafter jsn-bootstrap"
					id="jsn-header-tree-control">
					<h3 class="pull-left">
					<?php echo $showlistTable->image_source_name;?>
					</h3>
					<?php if($this->imageSource->_source['sourceDefine']->sync):?>
					<button
						class="btn btn-small pull-right sync<?php echo ($sync=='sync')?' btn-success':'';?>">
						<?php echo JText::_('SYNC_UPPERCASE', true);?>
						:
						<?php echo ($sync=='sync')?JText::_('ON_UPPERCASE', true):JText::_('OFF_UPPERCASE', true);?>
					</button>
					<?php endif;?>
				</div>
				<div class="source-media-container">
					<div class="jsn-jtree" id="jsn-jtree-categories">
						<ul>
						<?php echo @$imageFolder;?>
						</ul>
					</div>
				</div>
			</div>
			<div class="clearbreak"></div>
		</div>
	</div>
	<?php
	$config = array('showlist_id' => $showlistID);
	if (trim($selectMode)=='sync')
	{
		$imagesStored = $this->imageSource->getSyncImages($config);
	}
	else
	{
		$imagesStored = $this->imageSource->getImages($config);
	}
	$countImagesStored = count($imagesStored);
	?>
	<div class="ui-layout-center" id="panel-center">
		<div class="source-media-header">
			<h3>
			<?php echo JText::_('SHOWLIST_SHOWLIST_S_IMAGES');?>
			</h3>
		</div>
		<div class="source-media-panel-container jsn-bootstrap">
			<div class="panel-header clearafter jsn-iconbar"
				id="showlist-media-header">
				<a href="javascript: void(0);" class="media-show-grid pull-left"
					title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_SELECT_THUMB_PRESENTATION_MODE'); ?>">
					<i class="jsn-icon16 jsn-icon-layout jsn-icon-thumbnails disabled"></i>
				</a> <a href="javascript: void(0);"
					class="media-show-list pull-left"
					title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_SELECT_DETAILS_PRESENTATION_MODE'); ?>">
					<i
					class="jsn-icon16 jsn-icon-layout jsn-icon-thumbnaildetails disabled"></i>
				</a> <a href="javascript: void(0);" class="pull-right"
					id="delete-media-showlist"
					<?php if ($sync=='sync'){ echo 'style="display:none;"';}?>
					title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_DELETE_SELECTED_IMAGE'); ?>">
					<i class="jsn-icon16 jsn-icon-trash disabled"></i> </a> <a
					href="javascript: void(0);" class="pull-right"
					id="edit-media-showlist"
					<?php if ($sync=='sync'){ echo 'style="display:none;"';}?>
					title="<?php echo JText::_('SHOWLIST_IMAGE_MANAGER_EDIT_SELECTED_IMAGE'); ?>">
					<i class="jsn-icon16 jsn-icon-pencil disabled"></i> </a>
				<div class="clearbreak"></div>
			</div>
			<div class="source-media-container" id="showlist-media-container">
				<div
					class="showlist-items showgrid <?php if (!count($imagesStored)){ echo 'jsn-section-empty'; } ?>"
					id="showlist-items">

					<?php
					if ($countImagesStored)
					{
						foreach ($imagesStored as $image)
						{
							$image = (array) $image;
							$image['original_title'] 		= $objJSNUtils->convertSmartQuotes($image['original_title']);
							$image['original_description'] 	= $objJSNUtils->convertSmartQuotes($image['original_description']);
							$processedImage = array(
								'image_id'			=> (string) $image['image_id'],
								'image_title'		=> '',
								'image_small'		=> (string) $image['image_small'],
								'image_medium'		=> (string) $image['image_medium'],
								'image_big'			=> (string) $image['image_big'],
								'image_description'	=> '',
								'image_link'		=> (string) $image['image_link'],
								'image_extid'		=> (string) $image['image_extid'],
								'album_extid'		=> (string) $image['album_extid'],
								'image_size'		=> (string) $image['image_size'],
								'custom_data'		=> (string) $image['custom_data'],
								//'exif_data'			=> (string) $image['exif_data'],
								'original_title'	=> (string) $image['original_title'],
								'original_description' => (string) $image['original_description'],
								'original_link'		=> (string) $image['original_link']);
							?>
					<div class="media-item"
						id="<?php echo md5($image['image_extid']); ?>">
						<input class="item_id" type="hidden"
							value="<?php echo urlencode($image['image_extid']); ?>" /> <input
							type="hidden" value="<?php echo $image['original_link']; ?>"
							id="linkcheck" name="linkcheck" /> <input class="item_extid"
							type="hidden"
							value="<?php echo urlencode($image['album_extid']); ?>"> <input
							class="item_detail" type="hidden"
							value="<?php echo htmlspecialchars(json_encode($processedImage), ENT_COMPAT, 'UTF-8'); ?>" />
						<div class="item-index">&nbsp;</div>
						<?php echo (isset($image['custom_data']) && $image['custom_data']==1) ? '<div class="modified"></div>' : ''; ?>
						<div class="item-thumbnail">
							<a class="item_link" title="<?php echo $image['image_title']; ?>">
							<?php
							$baseurl = ($showlistTable->image_source_type=='external') ? '' : JURI::root();
							?> <img src="<?php echo  $baseurl.$image['image_small'];?>"
								style="max-height: 60px; max-width: 80px;" /> </a>
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
							<?php echo $image['image_link'];?>
							</p>
						</div>
						<div class="clearbreak"></div>
					</div>
					<?php
						}
					}
					else
					{
						?>
					<div
						class="jsn-bglabel <?php echo (trim($selectMode) == 'sync')?'showlist-sync-item-notice':'showlist-drag-drop-item-notice'?>">
						<?php
						if (trim($selectMode) == 'sync')
						{
							?>
						<span class="jsn-icon64 jsn-icon-refresh"></span>
						<?php echo JText::_('SHOWLIST_NOTICE_IMAGES_ARE_SYNCED');?>
						<?php
						}
						else
						{
							?>
						<span class="jsn-icon64 jsn-icon-pointer"></span>
						<?php echo JText::_('SHOWLIST_NOTICE_DRAG_AND_DROP');?>
						<?php
						}
						?>
					</div>
					<?php
					}
					?>
				</div>
			</div>
			<div class="clearbreak"></div>
		</div>
	</div>
	<input type="hidden" value="" id="start" name="start" /> <input
		type="hidden" value="" id="stop" name="stop" /> <input type="hidden"
		value="" id="start_item_showlist" name="start" /> <input type="hidden"
		value="" id="stop_item_showlist" name="stop" />
</div>
