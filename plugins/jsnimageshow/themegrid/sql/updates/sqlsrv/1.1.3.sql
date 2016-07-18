SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_grid]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_grid]
ADD 
	[container_transparent_background] [nvarchar](150) DEFAULT 'no'
END;