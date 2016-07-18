SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_classic_flash]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_classic_flash](
  [theme_id] [int] IDENTITY(1,1) NOT NULL,  
  [imgpanel_presentation_mode] [nvarchar](30) DEFAULT '0',
  [imgpanel_img_transition_type_fit] [nvarchar](30) DEFAULT '',
  [imgpanel_img_click_action_fit] [nvarchar](30) DEFAULT '',
  [imgpanel_img_open_link_in_fit] [nvarchar](30) DEFAULT 'new-browser',
  [imgpanel_img_transition_type_expand] [nvarchar](30) DEFAULT '',
  [imgpanel_img_motion_type_expand] [nvarchar](30) DEFAULT '',
  [imgpanel_img_zooming_type_expand] [nvarchar](30) DEFAULT 'center',
  [imgpanel_img_click_action_expand] [nvarchar](30) DEFAULT '',
  [imgpanel_img_open_link_in_expand] [nvarchar](30) DEFAULT 'new-browser',
  [imgpanel_img_show_image_shadow_fit] [nvarchar](30) DEFAULT 'yes',
  [imgpanel_bg_type] [nvarchar](30) DEFAULT '',
  [imgpanel_bg_value] [nvarchar](255) NULL,
  [imgpanel_show_watermark] [nvarchar](30) DEFAULT 'no',
  [imgpanel_watermark_path] [nvarchar](255) NULL,
  [imgpanel_watermark_position] [nvarchar](30) DEFAULT '',
  [imgpanel_watermark_offset] [nvarchar](30) NULL,
  [imgpanel_watermark_opacity] [nvarchar](30) NULL,
  [imgpanel_show_overlay_effect] [nvarchar](30) DEFAULT 'no',
  [imgpanel_overlay_effect_type] [nvarchar](30) NULL,
  [imgpanel_show_inner_shawdow] [nvarchar](30) DEFAULT 'yes',
  [imgpanel_inner_shawdow_color] [nvarchar](30) NULL,
  [thumbpanel_show_panel] [nvarchar](30) NULL,
  [thumbpanel_panel_position] [nvarchar](30) DEFAULT '',
  [thumbpanel_collapsible_position] [nvarchar](30) DEFAULT 'yes',
  [thumbpanel_thumb_browsing_mode] [nvarchar](30) DEFAULT '',
  [thumbpanel_show_thumb_status] [nvarchar](30) DEFAULT 'yes',
  [thumbpanel_active_state_color] [nvarchar](30) NULL,
  [thumbpanel_presentation_mode] [nvarchar](30) DEFAULT '',
  [thumbpanel_border] [nvarchar](30) NULL,
  [thumbpanel_enable_big_thumb] [nvarchar](30) DEFAULT 'yes',
  [thumbpanel_big_thumb_size] [nvarchar](30) NULL,
  [thumbpanel_thumb_row] [nvarchar](30) NULL,
  [thumbpanel_thumb_width] [nvarchar](30) NULL,
  [thumbpanel_thumb_height] [nvarchar](30) NULL,
  [thumbpanel_thumb_opacity] [nvarchar](30) DEFAULT '50',
  [thumbpanel_big_thumb_color] [nvarchar](30) NULL,
  [thumbpanel_thumb_border] [nvarchar](30) NULL,
  [thumbpanel_thumnail_panel_color] [nvarchar](30) NULL,
  [thumbpanel_thumnail_normal_state] [nvarchar](30) NULL,
  [infopanel_panel_position] [nvarchar](30) DEFAULT '',
  [infopanel_presentation] [nvarchar](30) NULL,
  [infopanel_bg_color_fill] [nvarchar](30) NULL,
  [infopanel_panel_click_action] [nvarchar](30) NULL,
  [infopanel_open_link_in] [nvarchar](30) DEFAULT 'new-browser',
  [infopanel_show_title] [nvarchar](30) DEFAULT 'yes',
  [infopanel_title_css] [nvarchar](255) NULL,
  [infopanel_show_des] [nvarchar](30) DEFAULT 'yes',
  [infopanel_des_lenght_limitation] [nvarchar](30) DEFAULT '',
  [infopanel_des_css] [nvarchar](255) NULL,
  [infopanel_show_link] [nvarchar](30) DEFAULT 'no',
  [infopanel_link_css] [nvarchar](255) NULL,
  [toolbarpanel_panel_position] [nvarchar](30) DEFAULT '',
  [toolbarpanel_presentation] [nvarchar](30) DEFAULT '0',
  [toolbarpanel_show_image_navigation] [nvarchar](30) DEFAULT 'yes',
  [toolbarpanel_slideshow_player] [nvarchar](30) DEFAULT 'yes',
  [toolbarpanel_show_fullscreen_switcher] [nvarchar](30) DEFAULT 'yes',
  [toolbarpanel_show_tooltip] [nvarchar](30) DEFAULT 'no',
  [slideshow_hide_thumb_panel] [nvarchar](30) DEFAULT 'yes',
  [slideshow_slide_timing] [nvarchar](255) NULL,
  [slideshow_hide_image_navigation] [nvarchar](30) DEFAULT 'yes',
  [slideshow_auto_play] [nvarchar](30) DEFAULT 'no',
  [slideshow_show_status] [nvarchar](30) DEFAULT 'yes',
  [slideshow_enable_ken_burn_effect] [nvarchar](30) DEFAULT 'yes',
  [slideshow_looping] [nvarchar](30) DEFAULT 'yes',
  [general_round_corner_radius] [nvarchar](30) DEFAULT '',
  [general_border_color] [nvarchar](30) DEFAULT '',
  [general_background_color] [nvarchar](30) DEFAULT '',
  [general_border_stroke] [nvarchar](30) DEFAULT '',
 CONSTRAINT [PK_#__imageshow_theme_classic_flash_theme_id] PRIMARY KEY CLUSTERED 
(
	[theme_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_classic_parameters]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_classic_parameters](
  [id] [int] IDENTITY(1,1) NOT NULL,  
  [general_swf_library] [smallint] DEFAULT '0',   
  [root_url] [smallint] DEFAULT '1',   
 CONSTRAINT [PK_#__imageshow_theme_classic_parameters_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_classic_javascript]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_classic_javascript](
  [theme_id] [int] IDENTITY(1,1) NOT NULL,  
  [imgpanel_presentation_mode] [nvarchar](30) DEFAULT '0',
  [imgpanel_img_click_action_fit] [nvarchar](30) DEFAULT '',
  [imgpanel_img_open_link_in_fit] [nvarchar](30) DEFAULT 'new-browser',
  [imgpanel_img_click_action_expand] [nvarchar](30) DEFAULT '',
  [imgpanel_img_open_link_in_expand] [nvarchar](30) DEFAULT 'new-browser',
  [imgpanel_bg_type] [nvarchar](30) DEFAULT '',
  [imgpanel_bg_value] [nvarchar](255) NULL,
  [thumbpanel_show_panel] [nvarchar](30) NULL,
  [thumbpanel_panel_position] [nvarchar](30) DEFAULT '',
  [thumbpanel_active_state_color] [nvarchar](30) NULL,
  [thumbpanel_thumnail_normal_state] [nvarchar](30) NULL,
  [thumbpanel_border] [nvarchar](30) NULL,
  [thumbpanel_thumb_width] [nvarchar](30) NULL,
  [thumbpanel_thumb_height] [nvarchar](30) NULL,
  [thumbpanel_thumnail_panel_color] [nvarchar](30) NULL,
  [infopanel_panel_position] [nvarchar](30) DEFAULT '',
  [infopanel_presentation] [nvarchar](30) NULL,
  [infopanel_bg_color_fill] [nvarchar](30) NULL,
  [infopanel_panel_click_action] [nvarchar](30) NULL,
  [infopanel_open_link_in] [nvarchar](30) DEFAULT 'new-browser',
  [infopanel_show_title] [nvarchar](30) DEFAULT 'yes',
  [infopanel_title_css] [nvarchar](255) NULL,
  [infopanel_show_des] [nvarchar](30) DEFAULT 'yes',
  [infopanel_des_lenght_limitation] [nvarchar](30) DEFAULT '',
  [infopanel_des_css] [nvarchar](255) NULL,
  [infopanel_show_link] [nvarchar](30) DEFAULT 'no',
  [infopanel_link_css] [nvarchar](255) NULL,
  [toolbarpanel_presentation] [nvarchar](30) DEFAULT '0',
  [toolbarpanel_show_counter] [nvarchar](30) DEFAULT 'no',
  [slideshow_slide_timing] [nvarchar](255) NULL,
  [slideshow_auto_play] [nvarchar](30) DEFAULT 'no',
  [slideshow_looping] [nvarchar](30) DEFAULT 'yes',
  [general_round_corner_radius] [nvarchar](30) DEFAULT '',
  [general_border_color] [nvarchar](30) DEFAULT '',
  [general_background_color] [nvarchar](30) DEFAULT '',
  [general_border_stroke] [nvarchar](30) DEFAULT '',
 CONSTRAINT [PK_#__imageshow_theme_classic_javascript_theme_id] PRIMARY KEY CLUSTERED 
(
	[theme_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;