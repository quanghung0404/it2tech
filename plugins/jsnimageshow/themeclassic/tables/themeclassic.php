<?php
/**
 * @version    $Id: themeclassic.php 16394 2012-09-25 08:31:07Z giangnd $
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
class TableThemeClassic extends JTable
{
	var $theme_id 								= null;
	var $imgpanel_presentation_mode 			= 'fit-in';
	var $imgpanel_img_transition_type_fit 		= 'random';
	var $imgpanel_img_click_action_fit 			= 'image-zooming';
	var $imgpanel_img_open_link_in_fit	 		= 'new-browser';
	var $imgpanel_img_transition_type_expand 	= 'random';
	var $imgpanel_img_motion_type_expand 		= 'random';
	var $imgpanel_img_zooming_type_expand 		= 'center';
	var $imgpanel_img_click_action_expand 		= 'open-image-link';
	var $imgpanel_img_open_link_in_expand 		= 'new-browser';
	var $imgpanel_img_show_image_shadow_fit		= 'yes';
	var $imgpanel_bg_type 						= 'linear-gradient';
	var $imgpanel_bg_value	 					= '#595959,#262626';
	var $imgpanel_show_watermark 				= 'no';
	var $imgpanel_watermark_path 				= null;
	var $imgpanel_watermark_position 			= 'top-right';
	var $imgpanel_watermark_offset 				= 10;
	var $imgpanel_watermark_opacity 			= 75;
	var $imgpanel_show_overlay_effect 			= 'no';
	var $imgpanel_overlay_effect_type 			= 'horizontal-floating-bar';
	var $imgpanel_show_inner_shawdow 			= 'yes';
	var $imgpanel_inner_shawdow_color			= '#000000';
	var $thumbpanel_show_panel 					= 'on';
	var $thumbpanel_panel_position 				= 'bottom';
	var $thumbpanel_collapsible_position 		= 'yes';
	var $thumbpanel_thumb_browsing_mode 		= 'pagination';
	var $thumbpanel_show_thumb_status 			= 'yes';
	var $thumbpanel_active_state_color 			= '#ff6200';
	var $thumbpanel_presentation_mode 			= 'image';
	var $thumbpanel_border						= 2;
	var $thumbpanel_enable_big_thumb 			= 'yes';
	var $thumbpanel_big_thumb_size 				= 150;
	var $thumbpanel_big_thumb_color 			= '#ffffff';
	var $thumbpanel_thumb_row					= 1;
	var $thumbpanel_thumb_width					= 60;
	var $thumbpanel_thumb_height				= 50;
	var $thumbpanel_thumb_opacity				= 50;
	var $thumbpanel_thumb_border				= 2;
	var $thumbpanel_thumnail_panel_color		= '#000000';
	var $thumbpanel_thumnail_normal_state 		= '#ffffff';
	var $infopanel_panel_position 				= 'top';
	var $infopanel_presentation      			= 'auto';
	var $infopanel_bg_color_fill 				= '#000000';
	var $infopanel_panel_click_action			= 'no-action';
	var $infopanel_open_link_in					= 'new-browser';
	var $infopanel_show_title 					= 'yes';
	var $infopanel_title_css 					= "font-family: Verdana;\nfont-size: 12px;\nfont-weight: bold;\ntext-align: left;\ncolor: #E9E9E9;";
	var $infopanel_show_des 					= 'yes';
	var $infopanel_des_lenght_limitation 		= 50;
	var $infopanel_des_css 						= "font-family: Arial;\nfont-size: 11px;\nfont-weight: normal;\ntext-align: left;\ncolor: #AFAFAF;";
	var $infopanel_show_link 					= 'no';
	var $infopanel_link_css 					= "font-family: Verdana;\nfont-size: 11px;\nfont-weight: bold;\ntext-align: right;\ncolor: #E06614;";
	var $toolbarpanel_panel_position 			= 'bottom';
	var $toolbarpanel_presentation 				= 'auto';
	var $toolbarpanel_show_image_navigation 	= 'yes';
	var $toolbarpanel_slideshow_player 			= 'yes';
	var $toolbarpanel_show_fullscreen_switcher 	= 'yes';
	var $toolbarpanel_show_tooltip 				= 'no';
	var $toolbarpanel_show_counter              = 'no';
	var $slideshow_hide_thumb_panel 			= 'yes';
	var $slideshow_slide_timing					= 6;
	var $slideshow_hide_image_navigation		= 'yes';
	var $slideshow_auto_play					= 'no';
	var $slideshow_show_status					= 'yes';
	var $slideshow_looping						= 'yes';
	var $slideshow_enable_ken_burn_effect 		= 'yes';
	var $general_round_corner_radius			= 0;
	var $general_border_color					= '#cccccc';
	var $general_background_color				= '#efefef';
	var $general_border_stroke					= 1;
	function __construct(& $db) {
		parent::__construct('#__imageshow_theme_classic', 'theme_id', $db);
	}
}
?>