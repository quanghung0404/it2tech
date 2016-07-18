SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_source_profile]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_source_profile
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_images]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_images
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_log]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_log
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_showlist]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_showlist
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_showcase]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_showcase
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__jsn_imageshow_messages]') AND type in (N'U'))
BEGIN
DROP TABLE #__jsn_imageshow_messages
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__jsn_imageshow_config]') AND type in (N'U'))
BEGIN
DROP TABLE #__jsn_imageshow_config
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_profile]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_theme_profile
END;
