<?php
/**
 * @version    $Id: default_javascript.php 17202 2012-10-18 07:38:25Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

?>
<table id="theme-classic-javascript" width="100%"
	class="jsn-showcase-theme-settings" cellpadding="0" cellspacing="0"
	border="0">
	<tr>
		<td valign="top" id="js-showcase-theme-detail-wrapper">
			<div class="jsn-showcase-theme-detail">
				<div class="jsn-showcase-tabs">
					<ul>
						<li><a class="js-image-container" href="#js-jsn-container-panel"><?php echo JText::_('GENERAL_CONTAINER'); ?>
						</a></li>
						<li><a class="js-image-panel" href="#js-jsn-image-panel"><?php echo JText::_('IMAGE_PANEL');?>
						</a></li>
						<li><a class="js-info-panel" href="#js-jsn-info-panel"><?php echo JText::_('INFORMATION_PANEL');?>
						</a></li>
						<li><a class="js-thumb-panel" href="#js-jsn-thumb-panel"><?php echo JText::_('THUMBNAIL_PANEL');?>
						</a></li>
						<li><a class="js-toolbar-panel" href="#js-jsn-toolbar-panel"><?php echo JText::_('TOOLBAR_PANEL');?>
						</a></li>
						<li><a class="js-slideshow-panel" href="#js-jsn-slideshow-panel"><?php echo JText::_('SLIDESHOW');?>
						</a></li>
					</ul>
					<div id="js-jsn-container-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-container-panel-general">
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
													id="js_general_background_color"
													value="<?php echo (!empty($items_javascript->general_background_color))?$items_javascript->general_background_color:'#ffffff'; ?>" />
												<div class="color-selector"
													id="js_general_background_color_link">
													<div style="background-color: <?php echo (!empty($items_javascript->general_background_color))?$items_javascript->general_background_color:'#ffffff'; ?>"></div>
												</div>
												<!--
												<a href="" id="js_general_background_color_link"><span id="js_span_general_background_color" class="jsn-icon-view-color" style="<?php echo (!empty($items_javascript->general_background_color))?'background:'.$items_javascript->general_background_color.';':'background:#ffffff;'; ?>"></span><span class="color-selection"><?php echo JText::_('SELECT_COLOR')?></span></a>
												 -->
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('GENERAL_TITLE_ROUND_CORNER'));?>::<?php echo htmlspecialchars(JText::_('GENERAL_DES_ROUND_CORNER')); ?>"><?php echo JText::_('GENERAL_TITLE_ROUND_CORNER'); ?>
											</label>
											<div class="controls">
												<input class="input-mini" type="text" size="5"
													name="general_round_corner_radius"
													value="<?php echo (!empty($items_javascript->general_round_corner_radius))?$items_javascript->general_round_corner_radius:'0'; ?>"
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
													value="<?php echo (!empty($items_javascript->general_border_stroke))?$items_javascript->general_border_stroke:'0'; ?>"
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
													id="js_general_border_color" readonly="readonly"
													name="general_border_color"
													value="<?php echo (!empty($items_javascript->general_border_color))?$items_javascript->general_border_color:'#000000'; ?>" />
												<div class="color-selector"
													id="js_general_border_color_link">
													<div style="background-color: <?php echo (!empty($items_javascript->general_border_color))?$items_javascript->general_border_color:'#000000'; ?>"></div>
												</div>
												<!--
												<a href="" id="js_general_border_color_link"><span id="js_span_general_border_color" class="jsn-icon-view-color" style="<?php echo (!empty($items_javascript->general_border_color))?'background:'.$items_javascript->general_border_color.';':'background:#000000;'; ?>"></span><span class="color-selection"><?php echo JText::_('SELECT_COLOR')?></span></a>
												 -->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="js-jsn-image-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-image-panel-image-presentation">
								<a href="#"><?php echo JText::_('IMAGE_PRESENTATION'); ?> </a>
							</h3>
							<div class="form-horizontal" id="js-acc-image-presentation">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_DEFAULT_PRESENTATION_MOD'));?>::<?php echo htmlspecialchars(JText::_('DES_DEFAULT_PRESENTATION_MOD')); ?>"><?php echo JText::_('TITLE_DEFAULT_PRESENTATION_MOD'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsImgPanelPresentationMode']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PRESENTATION_MODE_CONFIGURATION'));?>::<?php echo htmlspecialchars(JText::_('DES_PRESENTATION_MODE_CONFIGURATION')); ?>"><?php echo JText::_('TITLE_PRESENTATION_MODE_CONFIGURATION'); ?>
											</label>
											<div class="controls">
												<div id="js-acc-image-presentation-tabs">
													<ul>
														<li><a href="#js-acc-image-presentation-fit-in"><?php echo JText::_('FIT_IN');?>
														</a></li>
														<li><a href="#js-acc-image-presentation-expand-out"><?php echo JText::_('EXPAND_OUT');?>
														</a></li>
													</ul>
													<div id="js-acc-image-presentation-fit-in">
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_CLICK_ACTION'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_CLICK_ACTION')); ?>"><?php echo JText::_('TITLE_IMAGE_CLICK_ACTION'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_javascript['jsImgPanelImgClickActionFit']; ?>
															</div>
														</div>
														<div id="js-jsn-img-open-link-in-fit"
															class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_OPEN_LINK_IN')); ?>"><?php echo JText::_('TITLE_IMAGE_OPEN_LINK_IN'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_javascript['jsImgPanelImgOpenLinkInFit']; ?>
															</div>
														</div>
													</div>
													<div id="js-acc-image-presentation-expand-out">
														<div class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_CLICK_ACTION'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_CLICK_ACTION')); ?>"><?php echo JText::_('TITLE_IMAGE_CLICK_ACTION'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_javascript['jsImgPanelImgClickActionExpand']; ?>
															</div>
														</div>
														<div id="js-jsn-img-open-link-in-expand"
															class="control-group">
															<label class="control-label hasTip"
																title="<?php echo htmlspecialchars(JText::_('TITLE_IMAGE_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_IMAGE_OPEN_LINK_IN')); ?>"><?php echo JText::_('TITLE_IMAGE_OPEN_LINK_IN'); ?>
															</label>
															<div class="controls">
															<?php echo $lists_javascript['jsImgPanelImgOpenLinkInExpand']; ?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="js-image-panel-background">
								<a href="#"><?php echo JText::_('BACKGROUND'); ?> </a>
							</h3>
							<div id="js-acc-background" class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BACKGROUND_TYPE'));?>::<?php echo htmlspecialchars(JText::_('DES_BACKGROUND_TYPE')); ?>"><?php echo JText::_('TITLE_BACKGROUND_TYPE'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsImgPanelBgType']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_BACKGROUND_VALUE'));?>::<?php echo htmlspecialchars(JText::_('DES_BACKGROUND_VALUE')); ?>"><?php echo JText::_('TITLE_BACKGROUND_VALUE'); ?>
											</label>
											<div class="controls">
												<input
													class="<?php echo $classJSImagePanel; echo ($items_javascript->imgpanel_bg_type == 'solid-color')?' input-mini':' input-xxlarge'; ?>"
													type="text"
													value="<?php echo $items_javascript->imgpanel_bg_value; ?>"
													name="imgpanel_bg_value[]" id="js_imgpanel_bg_value"
													onchange="JSNISClassicTheme.ChangeVisual(this.name, this.value);"
													readonly="readonly" />
												<div class="color-selector" id="js_solid_color" style="display:<?php echo ($items_javascript->imgpanel_bg_type == 'solid-color')?'':'none'; ?>;">
													<div style="background-color: <?php echo (@$items_javascript->imgpanel_bg_value)?@$items_javascript->imgpanel_bg_value:'#595959'; ?>"></div>
												</div>
												<p id="js_pattern_title" style="<?php echo ($items_javascript->imgpanel_bg_type == 'pattern')?'display:"";':'display: none'; ?>;">
												<?php echo JText::_('SELECT_PATTERN')?>
													:&nbsp; <span id="js_pattern_value" style="<?php echo ($items_javascript->imgpanel_bg_type == 'pattern')?'display:"";':'display: none'; ?>;">
														<a class="jsn-modal"
														rel="{handler: 'iframe', size: {x: 590, y: 320}}"
														href="index.php?option=com_imageshow&controller=media&tmpl=component&act=pattern&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>"><?php echo JText::_('PREDEFINED')?>
													</a>&nbsp;&nbsp;-&nbsp; <a class="jsn-modal"
														rel="{handler: 'iframe', size: {x: 590, y: 410}}"
														href="index.php?option=com_imageshow&controller=media&tmpl=component&act=custom&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>"><?php echo JText::_('CUSTOM')?>
													</a> </span>
												</p>
												<p id="js_image_title" style="<?php echo ($items_javascript->imgpanel_bg_type == 'image')?'display:"";':'display: none'; ?>;">
													<span id="js_background_value" style="<?php echo ($items_javascript->imgpanel_bg_type == 'image')?'display:"";':'display: none'; ?>;">
														<a class="jsn-modal"
														rel="{handler: 'iframe', size: {x: 590, y: 410}}"
														href="index.php?option=com_imageshow&controller=media&tmpl=component&act=custom&e_name=text&event=loadMedia&theme=<?php echo $this->_showcaseThemeName; ?>">
														<?php echo JText::_('SELECT_IMAGE')?> </a> </span>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="js-jsn-thumb-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-thumb-panel-thumbnail">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="form-horizontal">
									<div class="row-fluid show-grid">
										<div class="span12">
											<div class="control-group">
												<label class="control-label hasTip"
													title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_THUMBNAIL_PANEL'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_THUMBNAIL_PANEL')); ?>"><?php echo JText::_('TITLE_SHOW_THUMBNAIL_PANEL'); ?>
												</label>
												<div class="controls">
												<?php echo $lists_javascript['jsThumbPanelShowPanel']; ?>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label hasTip"
													title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_PANEL_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_PANEL_POSITION')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_PANEL_POSITION'); ?>
												</label>
												<div class="controls">
												<?php echo $lists_javascript['jsThumbPanelPanelPosition']; ?>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label hasTip"
													title="<?php echo htmlspecialchars(JText::_('TITLE_PANEL_BACKGROUND_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_PANEL_BACKGROUND_COLOR')); ?>"><?php echo JText::_('TITLE_PANEL_BACKGROUND_COLOR'); ?>
												</label>
												<div class="controls">
													<input class="<?php echo $classJSThumbPanel;?> input-mini"
														type="text" size="15"
														value="<?php echo (!empty($items_javascript->thumbpanel_thumnail_panel_color))?$items_javascript->thumbpanel_thumnail_panel_color:'#000000'; ?>"
														readonly="readonly" name="thumbpanel_thumnail_panel_color"
														id="js_thumbpanel_thumnail_panel_color"
														onchange="JSNISClassicTheme.ChangeVisual(this.name, this.value);" />
													<div class="color-selector" id="js_thumnail_panel_color">
														<div style="background-color: <?php echo (!empty($items_javascript->thumbpanel_thumnail_panel_color))?$items_javascript->thumbpanel_thumnail_panel_color:'#000000'; ?>"></div>
													</div>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label hasTip"
													title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_NORMAL_STATE_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_NORMAL_STATE_COLOR')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_NORMAL_STATE_COLOR'); ?>
												</label>
												<div class="controls">
													<input class="<?php echo $classJSThumbPanel; ?> input-mini"
														type="text" size="15"
														value="<?php echo (!empty($items_javascript->thumbpanel_thumnail_normal_state))?$items_javascript->thumbpanel_thumnail_normal_state:'#ffffff'; ?>"
														readonly="readonly"
														name="thumbpanel_thumnail_normal_state"
														id="js_thumbpanel_thumnail_normal_state"
														onchange="JSNISClassicTheme.ChangeVisual(this.name, this.value);" />
													<div class="color-selector" id="js_thumnail_normal_state">
														<div style="background-color: <?php echo (!empty($items_javascript->thumbpanel_thumnail_normal_state))?$items_javascript->thumbpanel_thumnail_normal_state:'#ffffff'; ?>"></div>
													</div>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label hasTip"
													title="<?php echo htmlspecialchars(JText::_('TITLE_ACTIVE_STATE_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_ACTIVE_STATE_COLOR')); ?>"><?php echo JText::_('TITLE_ACTIVE_STATE_COLOR'); ?>
												</label>
												<div class="controls">
													<input class="<?php echo $classJSThumbPanel; ?> input-mini"
														type="text" size="15"
														value="<?php echo (!empty($items_javascript->thumbpanel_active_state_color))?$items_javascript->thumbpanel_active_state_color:'#ff6200'; ?>"
														readonly="readonly" name="thumbpanel_active_state_color"
														id="js_thumbpanel_active_state_color"
														onchange="JSNISClassicTheme.ChangeVisual(this.name, this.value);" />
													<div class="color-selector" id="js_active_state_color">
														<div style="background-color: <?php echo (!empty($items_javascript->thumbpanel_active_state_color))?$items_javascript->thumbpanel_active_state_color:'#ff6200'; ?>"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="js-thumb-panel-general">
								<a href="#"><?php echo JText::_('THUMBNAIL'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_WIDTH'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_WIDTH')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_WIDTH'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classJSThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_javascript->thumbpanel_thumb_width!='')?$items_javascript->thumbpanel_thumb_width:50; ?>"
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
												<input class="<?php echo $classJSThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_javascript->thumbpanel_thumb_height!='')?$items_javascript->thumbpanel_thumb_height:40; ?>"
													name="thumbpanel_thumb_height"
													id="js_thumbpanel_thumb_height"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_THUMBNAIL_BORDER'));?>::<?php echo htmlspecialchars(JText::_('DES_THUMBNAIL_BORDER')); ?>"><?php echo JText::_('TITLE_THUMBNAIL_BORDER');?>
											</label>
											<div class="controls">
												<input class="<?php echo $classJSThumbPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_javascript->thumbpanel_border!='')?$items_javascript->thumbpanel_border:1; ?>"
													id="js_thumbpanel_border" name="thumbpanel_border"
													onchange="checkInputValue(this, 0);"
													onfocus="getInputValue(this);" /> px
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="js-jsn-info-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-info-panel-general">
								<a href="#"><?php echo JText::_('GENERAL'); ?> </a>
							</h3>
							<div id="js-acc-caption-general" class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_PANEL_PRESENTATION'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_PANEL_PRESENTATION')); ?>"><?php echo JText::_('TITLE_INFO_PANEL_PRESENTATION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsInfoPanelPresentation']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_PANEL_POSITION'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_PANEL_POSITION')); ?>"><?php echo JText::_('TITLE_INFO_PANEL_POSITION'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsInfoPanelPanelPosition']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_PANEL_BACKGROUND_COLOR'));?>::<?php echo htmlspecialchars(JText::_('DES_PANEL_BACKGROUND_COLOR')); ?>"><?php echo JText::_('TITLE_PANEL_BACKGROUND_COLOR'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classJSInfoPanel; ?> input-mini"
													type="text" size="15"
													value="<?php echo (!empty($items_javascript->infopanel_bg_color_fill))?$items_javascript->infopanel_bg_color_fill:'#000000'; ?>"
													readonly="readonly" name="infopanel_bg_color_fill"
													id="js_infopanel_bg_color_fill"
													onchange="JSNISClassicTheme.ChangeVisual(this.name, this.value);" />
												<div class="color-selector" id="js_bg_color_fill">
													<div style="background-color: <?php echo (!empty($items_javascript->infopanel_bg_color_fill))?$items_javascript->infopanel_bg_color_fill:'#000000'; ?>"></div>
												</div>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_INFO_OPEN_LINK_IN'));?>::<?php echo htmlspecialchars(JText::_('DES_INFO_OPEN_LINK_IN')); ?>"><?php echo JText::_('TITLE_INFO_OPEN_LINK_IN'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsInfoPanelOpenLinkIn']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="js-info-panel-title">
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
											<?php echo $lists_javascript['jsInfoPanelShowTitle']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TITLE_CSS'));?>::<?php echo htmlspecialchars(JText::_('DES_TITLE_CSS')); ?>"><?php echo JText::_('TITLE_TITLE_CSS'); ?>
											</label>
											<div class="controls">
												<textarea class="<?php echo $classJSInfoPanel; ?> input-xlarge" name="infopanel_title_css" rows="5"><?php echo $items_javascript->infopanel_title_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="js-info-panel-description">
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
											<?php echo $lists_javascript['jsInfoPanelShowDes']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_DESCRIPTION_LENGTH_LIMITATION'));?>::<?php echo htmlspecialchars(JText::_('DES_DESCRIPTION_LENGTH_LIMITATION')); ?>"><?php echo JText::_('TITLE_DESCRIPTION_LENGTH_LIMITATION'); ?>
											</label>
											<div class="controls">
												<input class="<?php echo $classJSInfoPanel; ?> input-mini"
													type="text" size="5"
													value="<?php echo ($items_javascript->infopanel_des_lenght_limitation!='')?$items_javascript->infopanel_des_lenght_limitation:50; ?>"
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
												<textarea class="<?php echo $classJSInfoPanel; ?> input-xlarge" name="infopanel_des_css" rows="5"><?php echo $items_javascript->infopanel_des_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3 id="js-info-panel-link">
								<a href="#"><?php echo JText::_('LINK'); ?> </a>
							</h3>
							<div class="form-horizontal">
								<div class="row-fluid show-grid">
									<div class="span12">
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SHOW_LINK'));?>::<?php echo htmlspecialchars(JText::_('DES_SHOW_LINK')); ?>"><?php echo JText::_('TITLE_SHOW_LINK'); ?>
											</label>
											<div class="controls">
											<?php echo $lists_javascript['jsInfoPanelShowLink']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_TITLE_CSS'));?>::<?php echo htmlspecialchars(JText::_('DES_TITLE_CSS')); ?>"><?php echo JText::_('TITLE_TITLE_CSS'); ?>
											</label>
											<div class="controls">
												<textarea class="<?php echo $classJSInfoPanel; ?> input-xlarge" name="infopanel_link_css" rows="5"><?php echo $items_javascript->infopanel_link_css; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="js-jsn-toolbar-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-toolbar-panel-general">
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
											<?php echo $lists_javascript['jsToolBarPanelPresentation']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
											       title="<?php echo htmlspecialchars(JText::_('TITLE_TOOLBAR_PANEL_SHOW_COUNTER'));?>::<?php echo htmlspecialchars(JText::_('DES_TOOLBAR_PANEL_SHOW_COUNTER')); ?>"><?php echo JText::_('TITLE_TOOLBAR_PANEL_SHOW_COUNTER');?>
											</label>
											<div class="controls">
												<?php echo $lists_javascript['jsToolBarPanelCounter']; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="js-jsn-slideshow-panel" class="jsn-bootstrap">
						<div class="jsn-accordion">
							<div class="jsn-accordion-control">
								<span class="expand-all"><?php echo JText::_('EXPAND_ALL');?> </span>&nbsp;&nbsp;|&nbsp;
								<span class="collapse-all"><?php echo JText::_('COLLAPSE_ALL');?> </span>
							</div>
							<h3 id="js-slideshow-panel-slideshow-process">
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
													value="<?php echo ($items_javascript->slideshow_slide_timing!='')?$items_javascript->slideshow_slide_timing:8; ?>"
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
												<?php echo $lists_javascript['jsslideShowProcess']; ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label hasTip"
												title="<?php echo htmlspecialchars(JText::_('TITLE_SLIDESHOW_LOOPING'));?>::<?php echo htmlspecialchars(JText::_('DES_SLIDESHOW_LOOPING')); ?>"><?php echo JText::_('TITLE_SLIDESHOW_LOOPING');?>
											</label>
											<div class="controls">
												<?php echo $lists_javascript['jsslideShowLooping']; ?>
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
				<?php include dirname(__FILE__).DS.'preview_javascript.php'; ?>
			</div>
		</td>
	</tr>
</table>
<div style="clear: both;"></div>
