SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_grid]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_grid]
ADD 
	[auto_play] [nvarchar](150) DEFAULT 'no',
	[slide_timing] [nvarchar](150) DEFAULT '3',
	[item_per_page] [nvarchar](150) DEFAULT '5',
	[navigation_type] [nvarchar](150) DEFAULT 'show_all'	
END;