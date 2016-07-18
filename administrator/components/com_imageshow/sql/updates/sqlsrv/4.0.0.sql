SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_source_profile]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_source_profile](
	[external_source_profile_id] [int] IDENTITY(1,1) NOT NULL,
	[external_source_id] [int] NULL,
 CONSTRAINT [PK_#__imageshow_source_profile_external_source_profile_id] PRIMARY KEY CLUSTERED 
(
	[external_source_profile_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_profile]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_profile](
	[theme_id] [int] DEFAULT '0',
	[showcase_id] [int] DEFAULT '0',
    [theme_name] [nvarchar](255) DEFAULT ''
)
END;
