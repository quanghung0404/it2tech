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

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_images]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_images](
	[image_id] [int] IDENTITY(1,1) NOT NULL,
	[showlist_id] [int] NULL,
    [image_extid] [nvarchar](max) NULL,
    [album_extid] [nvarchar](max) NULL,    
    [image_small] [nvarchar](max) NULL,   
    [image_medium] [nvarchar](max) NULL, 
    [image_big] [nvarchar](max) NULL,    
    [image_title] [nvarchar](255) NULL,
    [image_description] [nvarchar](max) NULL,  
    [image_link] [nvarchar](255) NULL,
    [ordering] [int] DEFAULT '0',
    [custom_data] [smallint] DEFAULT '0',
    [sync] [smallint] DEFAULT '0',
    [image_size] [nvarchar](25) NULL,
    [exif_data] [nvarchar](max) NULL,
 CONSTRAINT [PK_#__imageshow_images_image_id] PRIMARY KEY CLUSTERED 
(
	[image_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_log]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_log](
	[log_id] [int] IDENTITY(1,1) NOT NULL,
	[user_id] [int] NULL,
    [url] [nvarchar](255) NULL,
    [result] [nvarchar](255) NULL,    
    [screen] [nvarchar](255) NULL,   
    [action] [nvarchar](255) NULL, 
    [time_created] [datetime] NULL,    
 CONSTRAINT [PK_#__imageshow_log_log_id] PRIMARY KEY CLUSTERED 
(
	[log_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_showcase]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_showcase](
	[showcase_id] [int] IDENTITY(1,1) NOT NULL,
	[showcase_title] [nvarchar](255) NULL,  
    [published] [smallint] DEFAULT '0',
    [ordering] [int] DEFAULT '0',
    [general_overall_width] [nvarchar](50) NULL,   
    [general_overall_height] [nvarchar](50) NULL, 
    [date_created] [datetime] NULL, 
    [date_modified] [datetime] NULL, 
 CONSTRAINT [PK_#__imageshow_showcase_showcase_id] PRIMARY KEY CLUSTERED 
(
	[showcase_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_theme_profile]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_theme_profile](
	[theme_id] [int] DEFAULT '0',
	[showcase_id] [int] DEFAULT '0',
    [theme_name] [nvarchar](255) DEFAULT '',
	[theme_style_name] [nvarchar](255) DEFAULT ''
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_showlist]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_showlist](
	[showlist_id] [int] IDENTITY(1,1) NOT NULL,
    [showlist_title] [nvarchar](255) NULL,
    [published] [smallint] DEFAULT '0',
    [override_title] [smallint] DEFAULT '0',
    [override_description] [smallint] DEFAULT '0',
    [override_link] [smallint] DEFAULT '0',
    [ordering] [int] DEFAULT '0',   
    [access] [smallint] NULL,  
    [hits] [int] NULL,  
    [description] [nvarchar](max) NULL, 
    [showlist_link] [nvarchar](max) NULL, 
    [alter_autid] [int] DEFAULT '0', 
    [date_create] [datetime] NULL,  
    [image_source_type] [nvarchar](max) NULL, 
    [image_source_name] [nvarchar](max) NULL,
    [image_source_profile_id] [int] DEFAULT '0',   
    [authorization_status] [smallint] DEFAULT '0',   
    [date_modified] [datetime] NULL,  
    [image_loading_order] [nvarchar](30) NULL,
    [show_exif_data] [nvarchar](100) NULL,    
 CONSTRAINT [PK_#__imageshow_showlist_showlist_id] PRIMARY KEY CLUSTERED 
(
	[showlist_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__jsn_imageshow_messages]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__jsn_imageshow_messages](
	[msg_id] [int] IDENTITY(1,1) NOT NULL,
    [msg_screen] [nvarchar](255) NULL,
    [published] [smallint] DEFAULT '0',
    [ordering] [int] DEFAULT '0',       
 CONSTRAINT [PK_#__jsn_imageshow_messages_msg_id] PRIMARY KEY CLUSTERED 
(
	[msg_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__jsn_imageshow_config]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__jsn_imageshow_config](
	[name] [nvarchar](255) NOT NULL,
	[value] [nvarchar](max) NOT NULL
)
END;