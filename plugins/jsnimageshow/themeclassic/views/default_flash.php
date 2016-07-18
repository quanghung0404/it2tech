<?php
/**
 * @version    $Id: default_flash.php 17202 2012-10-18 07:38:25Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die( 'Restricted access' );
?>
<table id="theme-classic-flash" width="100%"
	class="jsn-showcase-theme-settings" cellpadding="0" cellspacing="0"
	border="0">
	<tr>
		<td valign="top" id="js-showcase-theme-detail-wrapper">
			<div class="jsn-showcase-theme-detail">
				<div class="jsn-showcase-tabs">
					<ul>
						<li><a class="image-container" href="#jsn-container-panel"><?php echo JText::_('GENERAL_CONTAINER'); ?>
						</a></li>
						<li><a class="image-panel" href="#jsn-image-panel"><?php echo JText::_('IMAGE_PANEL');?>
						</a></li>
						<li><a class="info-panel" href="#jsn-info-panel"><?php echo JText::_('INFORMATION_PANEL');?>
						</a></li>
						<li><a class="thumb-panel" href="#jsn-thumb-panel"><?php echo JText::_('THUMBNAIL_PANEL');?>
						</a></li>
						<li><a class="toolbar-panel" href="#jsn-toolbar-panel"><?php echo JText::_('TOOLBAR_PANEL');?>
						</a></li>
						<li><a class="slideshow-panel" href="#jsn-slideshow-panel"><?php echo JText::_('SLIDESHOW');?>
						</a></li>
					</ul>
					<div id="jsn-container-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="container-panel-general">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('GENERAL_TITLE_OUTSITE_BACKGROUND_COLOR'));?>::<?php echo htmlspecialchars(JText::_('GENERAL_DES_OUTSITE_BACKGROUND_COLOR')); ?>"><?php echo JText::_('GENERAL_TITLE_OUTSITE_BACKGROUND_COLOR');?>
											</label>
											<div class="controls">
												<input class="input-mini" type="text" size="10"
													readonly="readonly" name="general_background_color"
													id="general_background_color"
													value="<?php echo (!empty($items_flash->general_background_color))?$items_flash->general_background_color:'#ffffff'; ?>" />
												<div class="color-selector"
													id="general_background_color_link">
													<div style="background-color: <?php echo (!empty($items_flash->general_background_color))?$items_flash->general_background_color:'#ffffff'; ?>"></div>
												</div>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('GENERAL_TITLE_ROUND_CORNER'));?>::<?php echo htmlspecialchars(JText::_('GENERAL_DES_ROUND_CORNER')); ?>"><?php echo JText::_('GENERAL_TITLE_ROUND_CORNER'); ?>
											</label>
											<div class="controls">
												<input class="input-mini" type="text" size="5"
													name="general_round_corner_radius"
													value="<?php echo (!empty($items_flash->general_round_corner_radius))?$items_flash->general_round_corner_radius:'0'; ?>"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" />&nbsp;
													<?php echo JText::_('px'); ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('GENERAL_TITLE_BORDER_STOKE'));?>::<?php echo htmlspecialchars(JText::_('GENERAL_DES_BORDER_STOKE')); ?>"><?php echo JText::_('GENERAL_TITLE_BORDER_STOKE'); ?>
											</label>
											<div class="controls">
												<input class="input-mini" type="text" size="5"
													name="general_border_stroke"
													value="<?php echo (!empty($items_flash->general_border_stroke))?$items_flash->general_border_stroke:'0'; ?>"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" />&nbsp;
													<?php echo JText::_('px'); ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('GENERAL_TITLE_BORDER_COLOR'));?>::<?php echo htmlspecialchars(JText::_('GENERAL_DES_BORDER_COLOR')); ?>"><?php echo JText::_('GENERAL_TITLE_BORDER_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="input-mini" type="text" size="10"
													id="general_border_color" readonly="readonly"
													name="general_border_color"
													value="<?php echo (!empty($items_flash->general_border_color))?$items_flash->general_border_color:'#000000'; ?>" />
												<div class="color-selector" id="general_border_color_link">
													<div style="background-color: <?php echo (!empty($items_flash->general_border_color))?$items_flash->general_border_color:'#000000'; ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="jsn-image-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="image-panel-image-presentation">
								<a href="#"><?php echo JText::_('IMAGE_PRESENTATION'); ?> </a>
							</h3>
							<div class="form-horizontal" id="acc-image-presentation">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_DEFAULT_PRESENTATION_MOD'));?>::<?php echo htmlspecialchars(JText::_('DES_DEFAULT_PRESENTATION_MOD')); ?>"><?php echo JText::_('TITLE_DEFAULT_PRESENTATION_MOD'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelPresentationMode']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PRESENTATION_MODE_CONFIGURATION'));?>::<?php echo htmlspecialchars(JText::_('DES_PRESENTATION_MODE_CONFIGURATION')); ?>"><?php echo JText::_('TITLE_PRESENTATION_MODE_CONFIGURATION'); ?>
											</label>
											<div class="controls">
												<div id="acc-image-presentation-tabs">
													<ul>
														<li><a href="#acc-image-presentation-fit-in"><?php echo JText::_('FIT_IN');?>
														</a></li>
														<li><a href="#acc-image-presentation-expand-out"><?php echo JText::_('EXPAND_OUT');?>
														</a></li>
													</ul>
													<div id="acc-image-presentation-fit-in">
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_TRANSITION_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_TRANSITION_TYPE')); ?>">
																<?php echo JText::_('TITLE_IMAGE_TRANSITION_TYPE'); ?> </label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgTransitionTypeFit']; ?>
															</div>
														</div>
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_CLICK_ACTION'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_CLICK_ACTION')); ?>">
																<?php echo JText::_('TITLE_IMAGE_CLICK_ACTION'); ?> </label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgClickActionFit']; ?>
															</div>
														</div>
														<div id="jsn-img-open-link-in-fit" class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_OPEN_LINK_IN')); ?>">
																<?php echo JText::_('TITLE_IMAGE_OPEN_LINK_IN'); ?> </label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgOpenLinkInFit']; ?>
															</div>
														</div>
														<div id="jsn-img-black-shadow" class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_SHOW_IMAGE_SHADOW'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_SHOW_IMAGE_SHADOW')); ?>">
																<?php echo JText::_('TITLE_IMAGE_SHOW_IMAGE_SHADOW'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgShowImageShadowFit']; ?>
															</div>
														</div>
													</div>
													<div id="acc-image-presentation-expand-out">
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_TRANSITION_TYPE_EXPAND'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_TRANSITION_TYPE_EXPAND')); ?>">
																<?php echo JText::_('TITLE_IMAGE_TRANSITION_TYPE_EXPAND'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgTransitionTypeExpand']; ?>
															</div>
														</div>
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_MOTION_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_MOTION_TYPE')); ?>">
																<?php echo JText::_('TITLE_IMAGE_MOTION_TYPE'); ?> </label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgMotionTypeExpand']; ?>
															</div>
														</div>
														<?php
														if ($items_flash->imgpanel_img_motion_type_expand == "no-motion") {
															$zoomingStyle = ' style="display:none;" ';
														} else {
															$zoomingStyle = '';
														}
														?>
														<div id="jsn-image-zooming-type"
														<?php echo $zoomingStyle; ?> class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_ZOOMING_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_ZOOMING_TYPE')); ?>">
																<?php echo JText::_('TITLE_IMAGE_ZOOMING_TYPE'); ?> </label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgZoomingTypeExpand']; ?>
															</div>
														</div>
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_CLICK_ACTION'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_CLICK_ACTION')); ?>"><?php echo JText::_('TITLE_IMAGE_CLICK_ACTION'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgClickActionExpand']; ?>
															</div>
														</div>
														<div id="jsn-img-open-link-in-expand"
															class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_OPEN_LINK_IN')); ?>"><?php echo JText::_('TITLE_IMAGE_OPEN_LINK_IN'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_flash['imgPanelImgOpenLinkInExpand']; ?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="image-panel-background">
								<a href="#"><?php echo JText::_('BACKGROUND'); ?> </a>
							</h3>
							<div id="acc-background" class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BACKGROUND_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_BACKGROUND_TYPE')); ?>"><?php echo JText::_('TITLE_BACKGROUND_TYPE'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelBgType']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BACKGROUND_VALUE'));?>::<?php echo htmlspecialchars(JText::_('DES_BACKGROUND_VALUE')); ?>"><?php echo JText::_('TITLE_BACKGROUND_VALUE'); ?>
											</label>
											<div class="controls">
												<div id="jsn-bg-input-value-first">
													<input
														class="<?php echo $classImagePanel; echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient' || $items_flash->imgpanel_bg_type == 'solid-color')?' input-mini':'input-xxlarge'; ?>"
														type="text" value="<?php echo @$imgpanel_bg_value[0]; ?>"
														name="imgpanel_bg_value[]" id="imgpanel_bg_value_first"
														onchange="JSNISClassicTheme.changeValueFlash('imagePanel', this.name, this.value);"
														readonly="readonly" />
													<div class="color-selector" id="solid_value" style="<?php echo ($items_flash->imgpanel_bg_type == 'solid-color')?'display:"";':'display: none'; ?>;">
														<div style="background-color: <?php echo 'background-color:'.$imgpanel_bg_value[0];?>"></div>
													</div>
													<div class="color-selector" id="gradient_link_1" style="<?php echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient')?'display:"";':'display: none'; ?>;">
														<div style="<?php echo 'background-color:'.@$imgpanel_bg_value[0];?>;<?php echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient')?'display:""':'display: none'; ?>;"></div>
													</div>
												</div>
												<div id="jsn-bg-input-value-second">
													<input class="<?php echo $classImagePanel; ?> input-mini" type="text" value="<?php echo @$imgpanel_bg_value[1]; ?>" name="imgpanel_bg_value[]" id="imgpanel_bg_value_last" readonly="readonly" style='<?php echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient')?'display:""':'display: none'; ?>;' onchange="JSNISClassicTheme.changeValueFlash('imagePanel',  this.name, this.value);"/>
													<div class="color-selector" id="gradient_link_2" style="<?php echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient')?'display:"";':'display: none'; ?>;">
														<div style="<?php echo 'background-color:'.@$imgpanel_bg_value[1]; ?>;<?php echo ($items_flash->imgpanel_bg_type == 'linear-gradient' || $items_flash->imgpanel_bg_type == 'radial-gradient')?'display:""':'display: none'; ?>;"></div>
													</div>
												</div>
											</div>
											<p id="pattern_title" style="<?php echo ($items_flash->imgpanel_bg_type == 'pattern')?'display:"";':'display: none'; ?>;">
											<?php echo JText::_('SELECT_PATTERN')?>
												:&nbsp; <span id="pattern_value"> <a class="jsn-modal"
													rel="{handler: 'iframe', size: {x: 590, y: 320}}"
													href="index.php?option=com_imageshow&controller=media&tmpl=component&act=pattern&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>">
													<?php echo JText::_('PREDEFINED')?> </a>&nbsp;&nbsp;-&nbsp;
													<a class="jsn-modal"
													rel="{handler: 'iframe', size: {x: 590, y: 410}}"
													href="index.php?option=com_imageshow&controller=media&tmpl=component&act=custom&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>">
													<?php echo JText::_('CUSTOM')?> </a> </span>
											</p>
											<p id="image_title" style="<?php echo ($items_flash->imgpanel_bg_type == 'image')?'display:"";':'display: none'; ?>;">
												<span id="background_value"> <a class="jsn-modal"
													rel="{handler: 'iframe', size: {x: 590, y: 410}}"
													href="index.php?option=com_imageshow&controller=media&tmpl=component&act=custom&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>">
													<?php echo JText::_('SELECT_IMAGE')?> </a> </span>
											</p>
										</div>
									</div>
								</div>
							</div>
							<h3 id="image-panel-inner-shadow">
								<a href="#"><?php echo JText::_('INNER_SHADOW'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_INNER_SHADOW'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_INNER_SHADOW')); ?>"><?php echo JText::_('TITLE_SHOW_INNER_SHADOW');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelShowInnerShawdow']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INNER_SHADOW_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_INNER_SHADOW_COLOR')); ?>"><?php echo JText::_('TITLE_INNER_SHADOW_COLOR');?>
											</label>
											<div class="controls">
												<input class="<?php echo $classImagePanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->imgpanel_inner_shawdow_color))?$items_flash->imgpanel_inner_shawdow_color:'#000000'; ?>"
													readonly="readonly" name="imgpanel_inner_shawdow_color"
													id="imgpanel_inner_shawdow_color"
													onchange="JSNISClassicTheme.changeValueFlash('imagePanel',  this.name, this.value);" />
												<div class="color-selector"
													id="imgpanel_inner_shawdow_color_link">
													<div style="background-color: <?php echo (!empty($items_flash->imgpanel_inner_shawdow_color))?$items_flash->imgpanel_inner_shawdow_color:'#000000'; ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="image-panel-watermark">
								<a href="#"><?php echo JText::_('WATERMARK'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_WATERMARK'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_WATERMARK')); ?>"><?php echo JText::_('TITLE_SHOW_WATERMARK'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelShowWatermark']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_WATERMARK_PATH'));?>::<?php echo htmlspecialchars(JText::_('DES_WATERMARK_PATH')); ?>"><?php echo JText::_('TITLE_WATERMARK_PATH'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classImagePanel; ?> input-xxlarge"
													type="text" size="50"
													value="<?php echo $items_flash->imgpanel_watermark_path; ?>"
													name="imgpanel_watermark_path" readonly="readonly"
													id="imgpanel_watermark_path"
													onchange="JSNISClassicTheme.changeValueFlash('imagePanel',  this.name, this.value);" />
												<p id="watermark-title">
													<a class="jsn-modal"
														rel="{handler: 'iframe', size: {x: 590, y: 410}}"
														href="index.php?option=com_imageshow&controller=media&tmpl=component&act=watermark&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>">
														<?php echo JText::_('SELECT_WATERMARK')?> </a>
												</p>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_WATERMARK_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_WATERMARK_POSITION')); ?>"><?php echo JText::_('TITLE_WATERMARK_POSITION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelWatermarkPosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_WATERMARK_OFFSET'));?>::<?php echo htmlspecialchars(JText::_('DES_WATERMARK_OFFSET')); ?>"><?php echo JText::_('TITLE_WATERMARK_OFFSET'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classImagePanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->imgpanel_watermark_offset!='')?$items_flash->imgpanel_watermark_offset:10; ?>"
													name="imgpanel_watermark_offset"
													id="imgpanel_watermark_offset"
													<?php echo ($items_flash->imgpanel_watermark_position =='center')?'disabled="disabled"':''; ?>
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_WATERMARK_OPACITY'));?>::<?php echo htmlspecialchars(JText::_('DES_WATERMARK_OPACITY')); ?>"><?php echo JText::_('TITLE_WATERMARK_OPACITY'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classImagePanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->imgpanel_watermark_opacity!='')?$items_flash->imgpanel_watermark_opacity:75; ?>"
													name="imgpanel_watermark_opacity"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> %
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="image-panel-overlay-effect">
								<a href="#"><?php echo JText::_('OVERLAY_EFFECT'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_IMG_OVERLAY_EFFECT'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_IMG_OVERLAY_EFFECT')); ?>"><?php echo JText::_('TITLE_SHOW_IMG_OVERLAY_EFFECT');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelShowOverlayEffect']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_IMG_OVERLAY_EFFECT_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_IMG_OVERLAY_EFFECT_TYPE')); ?>"><?php echo JText::_('TITLE_IMG_OVERLAY_EFFECT_TYPE');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['imgPanelOverlayEffectType']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="jsn-thumb-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="thumb-panel-general">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_THUMBNAIL_PANEL'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_THUMBNAIL_PANEL')); ?>"><?php echo JText::_('TITLE_SHOW_THUMBNAIL_PANEL'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelShowPanel']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_PANEL_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_PANEL_POSITION')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_PANEL_POSITION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelPanelPosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_COLLAPSIBLE_THUMBNAIL_PANEL'));?>::<?php echo htmlspecialchars(JText::_('DES_COLLAPSIBLE_THUMBNAIL_PANEL')); ?>"><?php echo JText::_('TITLE_COLLAPSIBLE_THUMBNAIL_PANEL'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelCollapsiblePosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PANEL_BACKGROUND_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_PANEL_BACKGROUND_COLOR')); ?>"><?php echo JText::_('TITLE_PANEL_BACKGROUND_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->thumbpanel_thumnail_panel_color))?$items_flash->thumbpanel_thumnail_panel_color:'#000000'; ?>"
													readonly="readonly" name="thumbpanel_thumnail_panel_color"
													id="thumbpanel_thumnail_panel_color"
													onchange="JSNISClassicTheme.changeValueFlash('thumbnailPanel',  this.name, this.value);" />
												<div class="color-selector" id="thumnail_panel_color">
													<div style="background-color: <?php echo (!empty($items_flash->thumbpanel_thumnail_panel_color))?$items_flash->thumbpanel_thumnail_panel_color:'#000000'; ?>"></div>
												</div>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_NORMAL_STATE_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_NORMAL_STATE_COLOR')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_NORMAL_STATE_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->thumbpanel_thumnail_normal_state))?$items_flash->thumbpanel_thumnail_normal_state:'#ffffff'; ?>"
													readonly="readonly" name="thumbpanel_thumnail_normal_state"
													id="thumbpanel_thumnail_normal_state"
													onchange="JSNISClassicTheme.changeValueFlash('thumbnailPanel',  this.name, this.value);" />
												<div class="color-selector" id="thumnail_normal_state">
													<div style="background-color: <?php echo (!empty($items_flash->thumbpanel_thumnail_normal_state))?$items_flash->thumbpanel_thumnail_normal_state:'#ffffff'; ?>"></div>
												</div>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_ACTIVE_STATE_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_ACTIVE_STATE_COLOR')); ?>"><?php echo JText::_('TITLE_ACTIVE_STATE_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->thumbpanel_active_state_color))?$items_flash->thumbpanel_active_state_color:'#ff6200'; ?>"
													readonly="readonly" name="thumbpanel_active_state_color"
													id="thumbpanel_active_state_color"
													onchange="JSNISClassicTheme.changeValueFlash('thumbnailPanel',  this.name, this.value);" />
												<div class="color-selector" id="active_state_color">
													<div style="background-color: <?php echo (!empty($items_flash->thumbpanel_active_state_color))?$items_flash->thumbpanel_active_state_color:'#ff6200'; ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="thumb-panel-thumbnail">
								<a href="#"><?php echo JText::_('THUMBNAIL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_THUMBNAILS_STATUS'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_THUMBNAILS_STATUS')); ?>"><?php echo JText::_('TITLE_SHOW_THUMBNAILS_STATUS'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelShowThumbStatus']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAILS_PRESENTATION_MODE'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAILS_PRESENTATION_MODE')); ?>"><?php echo JText::_('TITLE_THUMBNAILS_PRESENTATION_MODE');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelPresentationMode']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAILS_BROWSING_MODE'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAILS_BROWSING_MODE')); ?>"><?php echo JText::_('TITLE_THUMBNAILS_BROWSING_MODE'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelThumbBrowsingMode']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_ROW'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_ROW')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_ROW'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_thumb_row!='')?$items_flash->thumbpanel_thumb_row:1; ?>"
													name="thumbpanel_thumb_row" id="thumbpanel_thumb_row"
													<?php echo ($items_flash->thumbpanel_thumb_browsing_mode =='sliding')?'readonly="readonlye"':''; ?>
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" />
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_WIDTH'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_WIDTH')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_WIDTH'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_thumb_width!='')?$items_flash->thumbpanel_thumb_width:50; ?>"
													name="thumbpanel_thumb_width"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_HEIGHT'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_HEIGHT')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_HEIGHT'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_thumb_height!='')?$items_flash->thumbpanel_thumb_height:40; ?>"
													name="thumbpanel_thumb_height"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_BORDER'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_BORDER')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_BORDER');?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_border!='')?$items_flash->thumbpanel_border:1; ?>"
													name="thumbpanel_border"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_OPACITY'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_OPACITY')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_OPACITY');?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_thumb_opacity!='')?$items_flash->thumbpanel_thumb_opacity:50; ?>"
													name="thumbpanel_thumb_opacity"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> %
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="thumb-panel-big-thumbnail">
								<a href="#"><?php echo JText::_('BIG_THUMBNAIL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_ENABLE_BIG_THUMBNAIL'));?>::<?php echo htmlspecialchars(JText::_('DES_ENABLE_BIG_THUMBNAIL')); ?>"><?php echo JText::_('TITLE_ENABLE_BIG_THUMBNAIL'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['thumbPanelEnableBigThumb']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BIG_THUMBNAIL_SIZE'));?>::<?php echo htmlspecialchars(JText::_('DES_BIG_THUMBNAIL_SIZE')); ?>"><?php echo JText::_('TITLE_BIG_THUMBNAIL_SIZE'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_big_thumb_size!='')?$items_flash->thumbpanel_big_thumb_size:150; ?>"
													name="thumbpanel_big_thumb_size"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BIG_THUMBNAIL_BORDER'));?>::<?php echo htmlspecialchars(JText::_('DES_BIG_THUMBNAIL_BORDER')); ?>"><?php echo JText::_('TITLE_BIG_THUMBNAIL_BORDER'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->thumbpanel_thumb_border!='')?$items_flash->thumbpanel_thumb_border:2; ?>"
													name="thumbpanel_thumb_border"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BIG_THUMBNAIL_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_BIG_THUMBNAIL_COLOR')); ?>"><?php echo JText::_('TITLE_BIG_THUMBNAIL_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classThumbPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->thumbpanel_big_thumb_color))?$items_flash->thumbpanel_big_thumb_color:'#ffffff'; ?>"
													readonly="readonly" name="thumbpanel_big_thumb_color"
													id="thumbpanel_big_thumb_color"
													onchange="JSNISClassicTheme.changeValueFlash('thumbnailPanel',  this.name, this.value);" />
												<div class="color-selector" id="big_thumb_color">
													<div style="background-color: <?php echo (!empty($items_flash->thumbpanel_big_thumb_color))?$items_flash->thumbpanel_big_thumb_color:'#ffffff'; ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="jsn-info-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="info-panel-general">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div id="acc-caption-general" class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_PANEL_PRESENTATION'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_PANEL_PRESENTATION')); ?>"><?php echo JText::_('TITLE_INFO_PANEL_PRESENTATION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelPresentation']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_PANEL_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_PANEL_POSITION')); ?>"><?php echo JText::_('TITLE_INFO_PANEL_POSITION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelPanelPosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PANEL_BACKGROUND_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_PANEL_BACKGROUND_COLOR')); ?>"><?php echo JText::_('TITLE_PANEL_BACKGROUND_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classInfoPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_flash->infopanel_bg_color_fill))?$items_flash->infopanel_bg_color_fill:'#000000'; ?>"
													readonly="readonly" name="infopanel_bg_color_fill"
													id="infopanel_bg_color_fill"
													onchange="JSNISClassicTheme.changeValueFlash('informationPanel',  this.name, this.value);" />
												<div class="color-selector" id="bg_color_fill">
													<div style="background-color: <?php echo (!empty($items_flash->infopanel_bg_color_fill))?$items_flash->infopanel_bg_color_fill:'#000000'; ?>"></div>
												</div>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PANEL_CLICK_ACTION'));?>::<?php echo htmlspecialchars(JText::_('DES_PANEL_CLICK_ACTION')); ?>"><?php echo JText::_('TITLE_PANEL_CLICK_ACTION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelPanelClickAction']; ?>
											</div>
										</div>
										<div id="jsn-info-open-link-in" class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_OPEN_LINK_IN')); ?>"><?php echo JText::_('TITLE_INFO_OPEN_LINK_IN'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelOpenLinkIn']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="info-panel-title">
								<a href="#"><?php echo JText::_("TITLE"); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_TITLE'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_TITLE')); ?>"><?php echo JText::_('TITLE_SHOW_TITLE'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelShowTitle']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TITLE_CSS'));?>::<?php echo htmlspecialchars(JText::_('DES_TITLE_CSS')); ?>"><?php echo JText::_('TITLE_TITLE_CSS'); ?>
											</label>
											<div class="controls">
												<textarea class="<?php echo $classInfoPanel; ?> input-xlarge" name="infopanel_title_css" rows="5"><?php echo $items_flash->infopanel_title_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="info-panel-description">
								<a href="#"><?php echo JText::_('DESCRIPTION'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_DESCRIPTION'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_DESCRIPTION')); ?>"><?php echo JText::_('TITLE_SHOW_DESCRIPTION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelShowDes']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_DESCRIPTION_LENGTH_LIMITATION'));?>::<?php echo htmlspecialchars(JText::_('DES_DESCRIPTION_LENGTH_LIMITATION')); ?>"><?php echo JText::_('TITLE_DESCRIPTION_LENGTH_LIMITATION'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classInfoPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->infopanel_des_lenght_limitation!='')?$items_flash->infopanel_des_lenght_limitation:50; ?>"
													name="infopanel_des_lenght_limitation"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" />
													<?php echo JText::_('WORDS'); ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_DESCRIPTION_CSS'));?>::<?php echo htmlspecialchars(JText::_('DES_DESCRIPTION_CSS')); ?>"><?php echo JText::_('TITLE_DESCRIPTION_CSS'); ?>
											</label>
											<div class="controls">
												<textarea class="<?php echo $classInfoPanel; ?> input-xlarge" name="infopanel_des_css" rows="5"><?php echo $items_flash->infopanel_des_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="info-panel-link">
								<a href="#"><?php echo JText::_('LINK'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_TITLE'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_TITLE')); ?>"><?php echo JText::_('TITLE_SHOW_TITLE'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['infoPanelShowLink']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_LINK_CSS'));?>::<?php echo htmlspecialchars(JText::_('DES_LINK_CSS')); ?>"><?php echo JText::_('TITLE_LINK_CSS'); ?>
											</label>
											<div class="controls">
												<textarea class="<?php echo $classInfoPanel; ?> input-xlarge" name="infopanel_link_css" rows="5"><?php echo $items_flash->infopanel_link_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="jsn-toolbar-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="toolbar-panel-general">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TOOLBAR_PANEL_PRESENTATION'));?>::<?php echo htmlspecialchars(JText::_('DES_TOOLBAR_PANEL_PRESENTATION')); ?>"><?php echo JText::_('TITLE_TOOLBAR_PANEL_PRESENTATION');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelPresentation']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TOOLBAR_PANEL_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_TOOLBAR_PANEL_POSITION')); ?>"><?php echo JText::_('TITLE_TOOLBAR_PANEL_POSITION');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelPanelPosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TOOLBAR_PANEL_SHOW_TOOLTIP'));?>::<?php echo htmlspecialchars(JText::_('DES_TOOLBAR_PANEL_SHOW_TOOLTIP')); ?>"><?php echo JText::_('TITLE_TOOLBAR_PANEL_SHOW_TOOLTIP');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelShowTooltip']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="toolbar-panel-functions">
								<a href="#"><?php echo JText::_('FUNCTIONS'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_IMAGE_NAVIGATION'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_IMAGE_NAVIGATION')); ?>"><?php echo JText::_('TITLE_SHOW_IMAGE_NAVIGATION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelShowImageNavigation']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_SLIDESHOW_PLAYER'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_SLIDESHOW_PLAYER')); ?>"><?php echo JText::_('TITLE_SHOW_SLIDESHOW_PLAYER');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelSlideShowPlayer']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_FULLSCREEN_SWITCHER'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_FULLSCREEN_SWITCHER')); ?>"><?php echo JText::_('TITLE_SHOW_FULLSCREEN_SWITCHER');?>
											</label>
											<div class="controls">
											<?php echo $lists_flash['toolBarPanelShowFullscreenSwitcher'];?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="jsn-slideshow-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="slideshow-panel-slideshow-presentation">
								<a href="#"><?php echo JText::_('SLIDESHOW_PRESENTATION'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_KENBURN'));?>::<?php echo htmlspecialchars(JText::_('DES_KENBURN')); ?>"><?php echo JText::_('TITLE_KENBURN');?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowEnableKenBurnEffect']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOWSTATUS'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOWSTATUS')); ?>"><?php echo JText::_('TITLE_SHOWSTATUS');?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowShowStatus']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SLIDE_HIDE_THUMBNAIL_PANEL'));?>::<?php echo htmlspecialchars(JText::_('DES_SLIDE_HIDE_THUMBNAIL_PANEL')); ?>"><?php echo JText::_('TITLE_SLIDE_HIDE_THUMBNAIL_PANEL'); ?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowHideThumbPanel']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SLIDE_HIDE_IMAGE_NAVIGATION'));?>::<?php echo htmlspecialchars(JText::_('DES_SLIDE_HIDE_IMAGE_NAVIGATION')); ?>"><?php echo JText::_('TITLE_SLIDE_HIDE_IMAGE_NAVIGATION');?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowHideImageNavigation']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="slideshow-panel-slideshow-process">
								<a href="#"><?php echo JText::_('SLIDESHOW_PROCESS'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SLIDE_TIMING'));?>::<?php echo htmlspecialchars(JText::_('DES_SLIDE_TIMING')); ?>"><?php echo JText::_('TITLE_SLIDE_TIMING');?>
											</label>
											<div class="controls">
												<input
													class="<?php echo $classSlideShowPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_flash->slideshow_slide_timing!='')?$items_flash->slideshow_slide_timing:8; ?>"
													name="slideshow_slide_timing"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" />
												<?php echo JText::_('SECONDS');?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_AUTO_PLAY'));?>::<?php echo htmlspecialchars(JText::_('DES_AUTO_PLAY')); ?>"><?php echo JText::_('TITLE_AUTO_PLAY');?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowProcess']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SLIDESHOW_LOOPING'));?>::<?php echo htmlspecialchars(JText::_('DES_SLIDESHOW_LOOPING')); ?>"><?php echo JText::_('TITLE_SLIDESHOW_LOOPING');?>
											</label>
											<div class="controls">
												<?php echo $lists_flash['slideShowLooping']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
		<td valign="top" style="width: 571px;" id="jsn-preview-wrapper">
			<div class="jsn-preview-wrapper">
				<?php include dirname(__FILE__).DS.'preview_flash.php'; ?>
			</div>
		</td>
	</tr>
</table>
