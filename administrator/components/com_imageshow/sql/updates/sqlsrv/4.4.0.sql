IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_profile]') AND type in (N'U'))
BEGIN
ALTER TABLE [#__imageshow_theme_profile] ADD [theme_style_name] NVARCHAR(255) DEFAULT ''
END;

UPDATE [#__imageshow_theme_profile] SET [theme_style_name] = 'flash' WHERE [theme_name] = 'themeclassic' AND [theme_style_name] = '';