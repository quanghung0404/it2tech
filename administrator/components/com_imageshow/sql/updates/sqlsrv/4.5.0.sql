SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_messages]') AND type in (N'U'))
BEGIN
SP_RENAME N'[#__imageshow_messages]',N'[#__jsn_imageshow_messages]'
END;

SET QUOTED_IDENTIFIER ON;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_parameters]') AND type in (N'U'))
BEGIN
DROP TABLE #__imageshow_parameters
END;

SET QUOTED_IDENTIFIER ON;
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__jsn_imageshow_config]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__jsn_imageshow_config](
	[name] [nvarchar](255) NOT NULL,
	[value] [nvarchar](max) NOT NULL
)
END;