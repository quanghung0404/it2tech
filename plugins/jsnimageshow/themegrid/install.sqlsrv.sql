SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_grid]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_grid](
  [theme_id] [int] IDENTITY(1,1) NOT NULL,  
  [image_source] [nvarchar](150) NOT NULL DEFAULT 'thumbnail',
  [show_caption] [nvarchar](150) DEFAULT 'yes',
  [caption_show_description] [nvarchar](150) DEFAULT 'yes',
  [show_close] [nvarchar](150) DEFAULT 'yes',
  [show_thumbs] [nvarchar](150) DEFAULT 'yes',
  [click_action] [nvarchar](150) DEFAULT 'show_original_image',
  [open_link_in] [nvarchar](150) DEFAULT 'current_browser',
  [img_layout] [nvarchar](50) DEFAULT 'fixed',
  [background_color] [nvarchar](50) DEFAULT '#ffffff',
  [thumbnail_width] [nvarchar](50) DEFAULT '50',
  [thumbnail_height] [nvarchar](30) DEFAULT '50',
  [thumbnail_space] [nvarchar](50) DEFAULT '10',
  [thumbnail_border] [nvarchar](50) DEFAULT '3',
  [thumbnail_rounded_corner] [nvarchar](50) DEFAULT '3',
  [thumbnail_border_color] [nvarchar](50) DEFAULT '#ffffff',
  [thumbnail_shadow] [nvarchar](50) DEFAULT '1',
  [container_height_type] [nvarchar](150) DEFAULT 'inherited',
  [container_transparent_background] [nvarchar](150) DEFAULT 'no',  
  [auto_play] [nvarchar](150) DEFAULT 'no',  
  [slide_timing] [nvarchar](150) DEFAULT '3',  
  [item_per_page] [nvarchar](150) DEFAULT '5',  
  [navigation_type] [nvarchar](150) DEFAULT 'show_all',  
 CONSTRAINT [PK_#__imageshow_theme_grid_theme_id] PRIMARY KEY CLUSTERED 
(
	[theme_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;