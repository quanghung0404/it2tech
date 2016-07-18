SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_classic]') AND type in (N'U'))
BEGIN
SP_RENAME N'[#__imageshow_theme_classic]',N'[#__imageshow_theme_classic_flash]'
END;

SET QUOTED_IDENTIFIER ON;

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