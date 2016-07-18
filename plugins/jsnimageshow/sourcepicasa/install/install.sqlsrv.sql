SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_external_source_picasa]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_external_source_picasa](
	[external_source_id] [int] IDENTITY(1,1) NOT NULL,  
    [external_source_profile_title] [nvarchar](255) NULL, 
    [picasa_username] [nvarchar](255) NULL,
    [picasa_thumbnail_size] [nvarchar](30) NULL,
    [picasa_image_size] [nvarchar](30) NULL,
 CONSTRAINT [PK_#__imageshow_external_source_picasa_external_source_id] PRIMARY KEY CLUSTERED 
(
	[external_source_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;
