SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_classic_javascript]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_classic_javascript]
ADD
	[toolbarpanel_show_counter] [nvarchar](30) DEFAULT 'no',
END;