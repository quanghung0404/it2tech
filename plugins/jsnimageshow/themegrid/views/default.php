<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: default.php 16892 2012-10-11 04:07:40Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
$url 		 = $objJSNUtils->overrideURL();
$user 		 = JFactory::getUser();
?>
<script type="text/javascript">
	(function($) {
		$(document).ready(function(){
			$('#jsn-themegrid-container').gridtheme();
			$('#jsn-is-themegrid').tabs();
			$('#jsn-themegrid-container').stickyfloat({
				   duration: 0
		    });

			$('#background-color-selector').ColorPicker({
				color: $('#background_color').val(),
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#background_color').val('#' + hex);
					$('#background-color-selector div').css('backgroundColor', '#' + hex);
					$('#jsn-themegrid-container').css('background-color', '#' + hex);
				}
			});

			$('#thumbnail-border-color-selector').ColorPicker({
				color: $('#thumbnail_border_color').val(),
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#thumbnail_border_color').val('#' + hex);
					$('#thumbnail-border-color-selector div').css('backgroundColor', '#' + hex);
					$('.jsn-themegrid-box').css('border-color', '#' + hex);
				}
			});

			$('#click_action').change(function() {
				if ($(this).val() == 'open_image_link') {
					$('#jsn-open-link-in').css('display', 'block');
				} else {
					$('#jsn-open-link-in').css('display', 'none');
				}
			});
			
			$('#click_action').trigger('change');
			if ($('#container_height_type').val() == 'inherited')
				$('#navigation_type').parent().parent().hide();
			
			$('#container_height_type').change(function() {
				if ($(this).val() == 'auto') {
					$('#navigation_type').parent().parent().show();
					if ($('#navigation_type').val() == 'load_more')
						$('#item_per_page').parent().parent().show();
				} else {
					$('#navigation_type').parent().parent().hide();
					$('#item_per_page').parent().parent().hide();
				}
			});

			if ($('#navigation_type').val() == 'show_all' || $('#container_height_type').val() == 'inherited')
				$('#item_per_page').parent().parent().hide();
			
			$('#navigation_type').change(function() {
				if ($('#navigation_type').val() == 'show_all' || $('#container_height_type').val() == 'inherited') {
					$('#item_per_page').parent().parent().hide();
				} else {
					$('#item_per_page').parent().parent().show();
				}
			});
		})
	})(jsnThemeGridjQuery);
</script>

<table class="jsn-showcase-theme-settings">
	<tr>
		<td valign="top" id="jsn-theme-parameters-wrapper">
			<div id="jsn-is-themegrid" class="jsn-tabs">
				<ul>
					<li><a href="#themegrid-container-tab"><?php echo JText::_('THEME_GRID_IMAGE_CONTAINER'); ?>
					</a></li>
					<li><a href="#themegrid-thumbnail-tab"><?php echo JText::_('THEME_GRID_IMAGE_PRESENTATION'); ?>
					</a></li>
											<li><a href="#themegrid-caption-tab"><?php echo JText::_('THEME_GRID_CAPTION'); ?></a></li>
						<li><a href="#themegrid-slideshow-tab"><?php echo JText::_('THEME_GRID_SLIDESHOW')?></a></li>
				</ul>
				<div id="themegrid-container-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_BACKGROUND_COLOR_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_BACKGROUND_COLOR_DESC')); ?>"><?php echo JText::_('THEME_GRID_BACKGROUND_COLOR_TITLE');?>
									</label>
									<div class="controls">
										<input type="text"
											value="<?php echo (!empty($items->background_color))?$items->background_color:'#ffffff'; ?>"
											readonly="readonly" name="background_color"
											id="background_color" class="input-mini" />
										<div class="color-selector" id="background-color-selector">
											<div style="background-color: <?php echo (!empty($items->background_color))?$items->background_color:'#ffffff'; ?>"></div>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_GRID_CONTAINER_TRANSPARENT_BACKGROUND_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_CONTAINER_TRANSPARENT_BACKGROUND_DESC')); ?>"><?php echo JText::_('THEME_GRID_CONTAINER_TRANSPARENT_BACKGROUND_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['containerTransparentBackground']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_GRID_CONTAINER_HEIGHT_TYPE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_CONTAINER_HEIGHT_TYPE_DESC')); ?>"><?php echo JText::_('THEME_GRID_CONTAINER_HEIGHT_TYPE_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['containerHeightType']; ?>
									</div>
								</div>								
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_GRID_PAGINATION_TYPE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_PAGINATION_TYPE_DESC')); ?>"><?php echo JText::_('THEME_GRID_PAGINATION_TYPE_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['navigationType']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_GRID_ITEM_PER_PAGE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_ITEM_PER_PAGE_DESC')); ?>"><?php echo JText::_('THEME_GRID_ITEM_PER_PAGE_TITLE');?></label>
									<div class="controls">
										<input type="number" id="item_per_page" name="item_per_page" class="input-mini effect-panel" value="<?php echo $items->item_per_page; ?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themegrid-thumbnail-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_IMAGE_SOURCE_TITLE');?>::<?php echo JText::_('THEME_GRID_IMAGE_SOURCE_DESC'); ?>"><?php echo JText::_('THEME_GRID_IMAGE_SOURCE_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['imageSource']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_LAYOUT_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_LAYOUT_DESC')); ?>"><?php echo JText::_('THEME_GRID_LAYOUT_TITLE');?>
									</label>
									<div class="controls">
									<?php echo $lists['imgLayout']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_WIDTH_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_WIDTH_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_WIDTH_TITLE');?>
									</label>
									<div class="controls">
										<input type="number" id="thumbnail_width"
											name="thumbnail_width" class="imagePanel input-mini"
											value="<?php echo $items->thumbnail_width; ?>" />
											<?php echo JText::_('THEME_GRID_PIXEL');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_HEIGHT_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_HEIGHT_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_HEIGHT_TITLE');?>
									</label>
									<div class="controls">
										<input type="number" name="thumbnail_height"
											id="thumbnail_height" class="imagePanel input-mini"
											value="<?php echo $items->thumbnail_height; ?>" />
											<?php echo JText::_('THEME_GRID_PIXEL');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_SPACE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_SPACE_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_SPACE_TITLE');?>
									</label>
									<div class="controls">
										<input type="number" name="thumbnail_space"
											id="thumbnail_space" class="imagePanel input-mini"
											value="<?php echo $items->thumbnail_space; ?>" />
											<?php echo JText::_('THEME_GRID_PIXEL');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_BORDER_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_BORDER_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_BORDER_TITLE');?>
									</label>
									<div class="controls">
										<input type="number" name="thumbnail_border"
											id="thumbnail_border" class="imagePanel input-mini"
											value="<?php echo $items->thumbnail_border; ?>" />
											<?php echo JText::_('THEME_GRID_PIXEL');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_ROUNDED_CORNER_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_ROUNDED_CORNER_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_ROUNDED_CORNER_TITLE');?>
									</label>
									<div class="controls">
										<input type="number" name="thumbnail_rounded_corner"
											id="thumbnail_rounded_corner" class="imagePanel input-mini"
											value="<?php echo $items->thumbnail_rounded_corner; ?>" />
											<?php echo JText::_('THEME_GRID_PIXEL');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_SHADOW_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_SHADOW_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_SHADOW_TITLE');?>
									</label>
									<div class="controls">
									<?php echo $lists['thumbnailShadow']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_BORDER_COLOR_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_GRID_THUMBNAIL_BORDER_COLOR_DESC')); ?>"><?php echo JText::_('THEME_GRID_THUMBNAIL_BORDER_COLOR_TITLE');?>
									</label>
									<div class="controls">
										<input class="thumbnailColor input-mini" type="text"
											value="<?php echo (!empty($items->thumbnail_border_color))?$items->thumbnail_border_color:'#F0F0F0'; ?>"
											readonly="readonly" name="thumbnail_border_color"
											id="thumbnail_border_color" />
										<div class="color-selector"
											id="thumbnail-border-color-selector">
											<div style="background-color: <?php echo (!empty($items->thumbnail_border_color))?$items->thumbnail_border_color:'#F0F0F0'; ?>"></div>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_CLICK_ACTION_TITLE');?>::<?php echo JText::_('THEME_GRID_CLICK_ACTION_DESC'); ?>"><?php echo JText::_('THEME_GRID_CLICK_ACTION_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['clickAction']; ?>
									</div>
								</div>
								<div id="jsn-open-link-in" class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_OPEN_LINK_IN_TITLE');?>::<?php echo JText::_('THEME_GRID_OPEN_LINK_IN_DESC'); ?>"><?php echo JText::_('THEME_GRID_OPEN_LINK_IN_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['openLinkIn']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themegrid-caption-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_SHOW_CAPTION_TITLE')?>::<?php echo JText::_('THEME_GRID_SHOW_CAPTION_DESC')?>"><?php echo JText::_('THEME_GRID_SHOW_CAPTION_TITLE') ?></label>
									<div class="controls">
										<?php echo $lists['showCaption'] ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_CAPTION_SHOW_DESCRIPTION_TITLE');?>::<?php echo JText::_('THEME_GRID_CAPTION_SHOW_DESCRIPTION_DESC'); ?>"><?php echo JText::_('THEME_GRID_CAPTION_SHOW_DESCRIPTION_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['captionShowDescription'] ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themegrid-slideshow-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_SHOW_THUMBS_TITLE') ?>::<?php echo JText::_('THEME_GRID_SHOW_THUMBS_DESC')?>"><?php echo JText::_('THEME_GRID_SHOW_THUMBS_TITLE') ?></label>
									<div class="controls">
										<?php echo $lists['showThumbs'] ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_SHOW_CLOSE_TITLE') ?>::<?php echo JText::_('THEME_GRID_SHOW_CLOSE_DESC')?>"><?php echo JText::_('THEME_GRID_SHOW_CLOSE_TITLE') ?></label>
									<div class="controls">
										<?php echo $lists['showClose'] ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_AUTO_PLAY_TITLE');?>::<?php echo JText::_('THEME_GRID_AUTO_PLAY_DESC'); ?>"><?php echo JText::_('THEME_GRID_AUTO_PLAY_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['autoPlay']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_GRID_SLIDE_TIMING_TITLE');?>::<?php echo JText::_('THEME_GRID_SLIDE_TIMING_DESC'); ?>"><?php echo JText::_('THEME_GRID_SLIDE_TIMING_TITLE');?></label>
									<div class="controls">
										<input type="number" id="slide_timing" name="slide_timing" class="input-mini effect-panel" value="<?php echo $items->slide_timing; ?>" /> <?php echo JText::_('SECONDS'); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
		<td id="jsn-theme-preview-wrapper">
			<div>
				<?php include dirname(__FILE__).DS.'preview.php'; ?>
			</div>
		</td>
	</tr>
</table>
<!--  important -->
<input
	type="hidden" name="theme_name"
	value="<?php echo strtolower($this->_showcaseThemeName); ?>" />
<input
	type="hidden" name="theme_id"
	value="<?php echo (int) @$items->theme_id; ?>" />
<!--  important -->
<div style="clear: both;"></div>
