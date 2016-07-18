<?php
/**
 * @version     $Id$
 * @package     JSN ImageShow
 * @subpackage  ThemePile
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
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
	(function($){
		$(document).ready(function () {
			$('#jsn-is-themepile').tabs();

            var initColorPicker = function (id) {
                var $inputColor = $("#" + id);
                var $selectColor = $("#" + id + "-selector");
                $selectColor.ColorPicker({
                    color: $inputColor.val(),
                    onShow: function (colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        $(colpkr).fadeOut(500);
                        return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                        $inputColor.val('#' + hex);
                        $inputColor.trigger('change');
                        $("#" + id + "-selector").find('div').css('backgroundColor', '#' + hex);
                    }
                });
            };

            initColorPicker('thumbnail-border-color');
            initColorPicker('thumbnail-border-hover');
            initColorPicker('thumbnail-shadow-color');

            var initSlider = function (id, max, unit) {
                var $inputSlider = $('#' + id);
                var $slider = $('#' + id + '-slider');
                var $sliderValue = $('#' + id + '-slider-value');
                unit = typeof unit == 'undefined' ? "" : unit;
                max = typeof max == 'undefined' ? 100 : max;

                $slider[0].slide = null;
                $slider.slider({
                    value: parseInt($inputSlider.val()),
                    min: 0,
                    max: max,
                    step: 1,
                    slide: function (event, ui) {
                        $sliderValue.html(ui.value + ' ' + unit);
                        $slider.val(ui.value);
                        $slider.trigger('change');
                        $inputSlider.val(ui.value);
                        $inputSlider.trigger('change');
                    }
                });
            };

            initSlider('thumbnail-rotation', 50, 'deg');
            initSlider('thumbnail-overlap', 45, 'px');

            var $imageClickActionSelect = $('#image_click_action');
			$imageClickActionSelect.change(function() {
				if ($(this).val() == 'open_image_link') {
					$('#jsn-open-link-in').css('display', 'block');
				} else {
					$('#jsn-open-link-in').css('display', 'none');
				}
			});
            $imageClickActionSelect.trigger('change');
		});
	})(jQuery);
</script>
<table class="jsn-showcase-theme-settings" style="width: 100%;">
	<tr>
		<td id="jsn-theme-parameters-wrapper">
			<div id="jsn-is-themepile" class="jsn-tabs">
				<ul>
                    <li><a href="#themepile-thumbnail-tab"><?php echo JText::_('THEME_PILE_THUMBNAIL'); ?></a></li>
                    <li><a href="#themepile-slide-show-tab"><?php echo JText::_('THEME_PILE_SLIDE_SHOW'); ?></a></li>
				</ul>
				<div id="themepile-thumbnail-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_SOURCES_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_SOURCES_DESC')); ?>"><?php echo JText::_('THEME_PILE_IMAGE_SOURCES_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <?php echo $lists['imageSource']; ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_MAX_WIDTH_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_MAX_WIDTH_DESC')); ?>"><?php echo JText::_('THEME_PILE_IMAGE_MAX_WIDTH_TITLE');?></label>
                                    <div class="controls">
                                        <input type="number" id="image-width" name="image_width" class="imagePanel input-mini" value="<?php echo $items->image_width; ?>" /> <?php echo JText::_('THEME_PILE_PIXEL');?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_MAX_HEIGHT_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_IMAGE_MAX_HEIGHT_DESC')); ?>"><?php echo JText::_('THEME_PILE_IMAGE_MAX_HEIGHT_TITLE');?></label>
                                    <div class="controls">
                                        <input type="number" id="image-height" name="image_height" class="imagePanel input-mini" value="<?php echo $items->image_height; ?>" /> <?php echo JText::_('THEME_PILE_PIXEL');?>
                                    </div>
                                </div>								
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_OVERLAP_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_OVERLAP_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_OVERLAP_TITLE');?></label>
									<div class="controls">
                                        <input type="hidden" id="thumbnail-overlap" name="thumbnail_overlap" class="input-mini effect-panel" value="<?php echo $items->thumbnail_overlap; ?>" />
                                        <div id="thumbnail-overlap-slider" class="flow-param-slider"></div><div id="thumbnail-overlap-slider-value" class="flow-param-slider-value"><?php echo $items->thumbnail_overlap; ?> px</div>
									</div>
								</div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_ROTATION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_ROTATION_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_ROTATION_TITLE');?></label>
                                    <div class="controls">
                                        <input type="hidden" id="thumbnail-rotation" name="thumbnail_rotation" class="input-mini effect-panel" value="<?php echo $items->thumbnail_rotation; ?>" />
                                        <div id="thumbnail-rotation-slider" class="flow-param-slider"></div><div id="thumbnail-rotation-slider-value" class="flow-param-slider-value"><?php echo $items->thumbnail_rotation; ?> deg</div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_WIDTH_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_WIDTH_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_BORDER_WIDTH_TITLE');?></label>
                                    <div class="controls">
                                        <input type="number" id="thumbnail-border-width"
                                               name="thumbnail_border_width" class="input-mini"
                                               value="<?php echo $items->thumbnail_border_width; ?>" />
                                        <?php echo JText::_('THEME_PILE_PIXEL');?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_COLOR_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_COLOR_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_BORDER_COLOR_TITLE');?></label>
                                    <div class="controls">
                                        <input class="thumbnailColor input-mini" type="text"
                                               value="<?php echo (!empty($items->thumbnail_border_color)) ? $items->thumbnail_border_color : '#F0F0F0'; ?>"
                                               readonly="readonly" name="thumbnail_border_color"
                                               id="thumbnail-border-color" />
                                        <div class="color-selector" id="thumbnail-border-color-selector">
                                            <div style="background-color: <?php echo (!empty($items->thumbnail_border_color)) ? $items->thumbnail_border_color : '#F0F0F0'; ?>"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_HOVER_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_BORDER_HOVER_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_BORDER_HOVER_TITLE');?></label>
                                    <div class="controls">
                                        <input class="thumbnailColor input-mini" type="text"
                                               value="<?php echo (!empty($items->thumbnail_border_hover)) ? $items->thumbnail_border_hover : '#F0F0F0'; ?>"
                                               readonly="readonly" name="thumbnail_border_hover"
                                               id="thumbnail-border-hover" />
                                        <div class="color-selector" id="thumbnail-border-hover-selector">
                                            <div style="background-color: <?php echo (!empty($items->thumbnail_border_hover)) ? $items->thumbnail_border_hover : '#F0F0F0'; ?>"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_SHADOW_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_SHADOW_DESC')); ?>"><?php echo JText::_('THEME_PILE_SHOW_SHADOW_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <?php echo $lists['showShadow']; ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_SHADOW_COLOR_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_THUMBNAIL_SHADOW_COLOR_DESC')); ?>"><?php echo JText::_('THEME_PILE_THUMBNAIL_SHADOW_COLOR_TITLE');?></label>
                                    <div class="controls">
                                        <input class="thumbnailColor input-mini" type="text"
                                               value="<?php echo (!empty($items->thumbnail_shadow_color)) ? $items->thumbnail_shadow_color : '#F0F0F0'; ?>"
                                               readonly="readonly" name="thumbnail_shadow_color"
                                               id="thumbnail-shadow-color" />
                                        <div class="color-selector" id="thumbnail-shadow-color-selector">
                                            <div style="background-color: <?php echo (!empty($items->thumbnail_shadow_color)) ? $items->thumbnail_shadow_color : '#F0F0F0'; ?>"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip" title="<?php echo JText::_('THEME_PILE_IMAGE_CLICK_ACTION_TITLE');?>::<?php echo JText::_('THEME_PILE_IMAGE_CLICK_ACTION_DESC'); ?>"><?php echo JText::_('THEME_PILE_IMAGE_CLICK_ACTION_TITLE');?></label>
                                    <div class="controls">
                                        <?php echo $lists['imageClickAction']; ?>
                                    </div>
                                </div>
                                <div id="jsn-open-link-in" class="control-group">
                                    <label class="control-label hasTip" title="<?php echo JText::_('THEME_PILE_OPEN_LINK_IN_TITLE');?>::<?php echo JText::_('THEME_PILE_OPEN_LINK_IN_DESC'); ?>"><?php echo JText::_('THEME_PILE_OPEN_LINK_IN_TITLE');?></label>
                                    <div class="controls">
                                        <?php echo $lists['openLinkIn']; ?>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
				</div>
                <div id="themepile-slide-show-tab" class="jsn-bootstrap">
                    <div class="form-horizontal">
                        <div class="row-fluid show-grid">
                            <div class="span12">
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_FADE_DURATION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_FADE_DURATION_DESC')); ?>"><?php echo JText::_('THEME_PILE_FADE_DURATION_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <input type="number" id="fade-duration"
                                               name="fade_duration" class="input-mini"
                                               value="<?php echo $items->fade_duration; ?>" />
                                        <?php echo JText::_('THEME_PILE_MILLISECOND');?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_PICKUP_DURATION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_PICKUP_DURATION_DESC')); ?>"><?php echo JText::_('THEME_PILE_PICKUP_DURATION_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <input type="number" id="pickup-duration"
                                               name="pickup_duration" class="input-mini"
                                               value="<?php echo $items->pickup_duration; ?>" />
                                        <?php echo JText::_('THEME_PILE_MILLISECOND');?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_TITLE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_TITLE_DESC')); ?>"><?php echo JText::_('THEME_PILE_SHOW_TITLE_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <?php echo $lists['showTitle']; ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_TITLE_CSS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_TITLE_CSS_DESC')); ?>"><?php echo JText::_('THEME_PILE_TITLE_CSS_TITLE'); ?>
                                    </label>
                                    <div class="controls">
                                        <textarea rows="5" name="title_css" class="informationPanel input-xlarge"><?php echo $items->title_css; ?></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_DESC_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_SHOW_DESC_DESC')); ?>"><?php echo JText::_('THEME_PILE_SHOW_DESC_TITLE');?>
                                    </label>
                                    <div class="controls">
                                        <?php echo $lists['showDescription']; ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label hasTip"
                                           title="<?php echo htmlspecialchars(JText::_('THEME_PILE_DESC_CSS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_PILE_DESC_CSS_DESC')); ?>"><?php echo JText::_('THEME_PILE_DESC_CSS_TITLE'); ?>
                                    </label>
                                    <div class="controls">
                                        <textarea rows="5" name="description_css" class="informationPanel input-xlarge"><?php echo $items->description_css; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</td>
		<td id="jsn-theme-preview-wrapper">
            <div class="preview-jsn-themepile-wrapper">
                <?php include dirname(__FILE__).DS.'preview.php'; ?>
                <div class="preview-cover"></div>
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
<input
	type="hidden" id="jsn-is-root-url"
	value="<?php echo JURI::root(true); ?>" />	
<!--  important -->
<div style="clear: both;"></div>
