SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_pile]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_pile](
  [theme_id] [int] IDENTITY(1,1) NOT NULL,
  [image_source] [nvarchar](150) NOT NULL DEFAULT 'thumbnail',
  [image_width] [nvarchar](11) DEFAULT '130',
  [image_height] [nvarchar](11) DEFAULT '130',
  [thumbnail_overlap] [nvarchar](11) DEFAULT '50',
  [thumbnail_rotation] [nvarchar](11) DEFAULT '45',
  [thumbnail_border_width] [nvarchar](11) DEFAULT '2',
  [thumbnail_border_color] [nvarchar](150) DEFAULT '#ffffff',
  [thumbnail_border_hover] [nvarchar](150) DEFAULT '#ffffff',
  [show_shadow] [nvarchar](11) DEFAULT '1',
  [thumbnail_shadow_color] [nvarchar](150) DEFAULT '#ffffff',
  [image_click_action] [nvarchar](150) DEFAULT 'show_original_image',
  [open_link_in] [nvarchar](150) DEFAULT 'current_browser',
  [fade_duration] [nvarchar](11) DEFAULT '200',
  [pickup_duration] [nvarchar](11) DEFAULT '500',
  [show_title] [nvarchar](11) DEFAULT '0',
  [title_css] [nvarchar](250) DEFAULT 'font-family: Verdana;\nfont-size: 12px;\nfont-weight: bold;\ntext-align: left;\ncolor: #E9E9E9;',
  [show_description] [nvarchar](11) DEFAULT '0',
  [description_css] [nvarchar](250) DEFAULT 'font-family: Arial;\nfont-size: 11px;\nfont-weight: normal;\ntext-align: left;\ncolor: #AFAFAF;',
 CONSTRAINT [PK_#__imageshow_theme_pile_theme_id] PRIMARY KEY CLUSTERED
(
	[theme_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;