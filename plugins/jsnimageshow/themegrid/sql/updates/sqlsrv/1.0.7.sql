SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_grid]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_grid]
ADD 
	[image_source] [nvarchar](150) DEFAULT 'thumbnail',
	[show_caption] [nvarchar](150) DEFAULT 'yes',
	[caption_show_description] [nvarchar](150) DEFAULT 'yes',
	[show_close] [nvarchar](150) DEFAULT 'yes',
	[show_thumbs] [nvarchar](150) DEFAULT 'yes',
  	[click_action] [nvarchar](150) DEFAULT 'show_original_image',
  	[open_link_in] [nvarchar](150) DEFAULT 'current_browser'
END;