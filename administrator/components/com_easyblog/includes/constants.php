<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Site pathways
define('EBLOG_ROOT', JPATH_ROOT . '/components/com_easyblog');
define('EBLOG_ADMIN_ROOT', JPATH_ROOT . '/administrator/components/com_easyblog');
define('EBLOG_ASSETS', EBLOG_ROOT . '/assets');
define('EBLOG_HELPERS', EBLOG_ROOT . '/helpers');
define('EBLOG_MODELS', EBLOG_ROOT . '/models');
define('EBLOG_CONTROLLERS', EBLOG_ROOT . '/controllers');
define('EBLOG_CLASSES', EBLOG_ROOT . '/classes');
define('EBLOG_TABLES', EBLOG_ADMIN_ROOT . '/tables');
define('EBLOG_THEMES', EBLOG_ROOT . '/themes');
define('EBLOG_MEDIA', JPATH_ROOT . '/media/com_easyblog');
define('EBLOG_SCRIPTS', EBLOG_MEDIA . '/scripts');
define('EBLOG_RESOURCES', EBLOG_MEDIA . '/resources');
define('EBLOG_ADMIN_THEMES', EBLOG_ADMIN_ROOT . '/themes');
define('EBLOG_ADMIN_UPDATES', EBLOG_ADMIN_ROOT . '/updates');
define('EBLOG_ADMIN_INCLUDES', EBLOG_ADMIN_ROOT . '/includes');

// Toolbars path
define( 'EBLOG_TOOLBARS' , EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'toolbar' );

// Spinner path
define( 'EBLOG_SPINNER' , rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/images/loader.gif' );



define( 'EBLOG_MEDIA_URI', rtrim( JURI::root() , '/' ) . '/media/com_easyblog/' );

// Updates server
define( 'EBLOG_UPDATES_SERVER' , 'stackideas.com' );

define( 'BLOG_PRIVACY_PUBLIC', '0');
define( 'BLOG_PRIVACY_PRIVATE', '1');
define( 'EBLOG_FILTER_ALL', 'all' );
define( 'EBLOG_FILTER_PUBLISHED', 'published' );
define( 'EBLOG_FILTER_UNPUBLISHED', 'unpublished' );
define( 'EBLOG_FILTER_SCHEDULE', 'scheduled' );
define( 'EBLOG_FILTER_DRAFT', 'draft' );
define( 'EBLOG_FILTER_PENDING', 'pending' );
define( 'EBLOG_MAX_FEATURED_POST' , '10' );

// Subscription types
define( 'EBLOG_SUBSCRIPTION_SITE'		, 'site' );
define( 'EBLOG_SUBSCRIPTION_CATEGORY'	, 'category' );
define( 'EBLOG_SUBSCRIPTION_BLOGGER'	, 'blogger' );
define( 'EBLOG_SUBSCRIPTION_TEAMBLOG'	, 'team' );
define( 'EBLOG_SUBSCRIPTION_ENTRY'		, 'entry' );

// Meta Tag ID for views
define('META_ID_LATEST', '1');
define('META_ID_GATEGORIES', '2');
define('META_ID_TAGS', '3');
define('META_ID_BLOGGERS', '4');
define('META_ID_TEAMBLOGS', '5');
define('META_ID_FEATURED', '6');
define('META_ID_ARCHIVE', '7');
define('META_ID_SEARCH', '8');

define('META_TYPE_POST', 'post');
define('META_TYPE_VIEW', 'view');
define('META_TYPE_BLOGGER', 'blogger');
define('META_TYPE_TEAM', 'team');
define('META_TYPE_SEARCH', 'search');
define('META_TYPE_CATEGORY', 'category');

// Comment statuses
define( 'EBLOG_COMMENT_UNPUBLISHED' , 0 );
define( 'EBLOG_COMMENT_PUBLISHED' 	, 1 );
define( 'EBLOG_COMMENT_MODERATE'	, 2 );

// Oauth integrations
define( 'EBLOG_OAUTH_LINKEDIN'	, 'linkedin' );
define( 'EBLOG_OAUTH_FACEBOOK'	, 'facebook' );
define( 'EBLOG_OAUTH_TWITTER'	, 'twitter' );
define( 'EBLOG_OAUTH_FLICKR'	, 'flickr' );
define('EBLOG_OAUTH_DROPBOX', 'dropbox');

// category privacy follow acl status
define( 'CATEGORY_PRIVACY_ACL'		, '2');

// category acl item actions
define( 'CATEGORY_ACL_ACTION_VIEW'		, '1');
define( 'CATEGORY_ACL_ACTION_SELECT'	, '2');

//bbcode emoticons path
define ("EBLOG_EMOTICONS_DIR", rtrim(JURI::root(), '/' ) . '/components/com_easyblog/themes/wireframe/images/smilies/');

//powered by link
define ('EBLOG_POWERED_BY_LINK', '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">Powered by EasyBlog for Joomla!</a></div>');

// Ratings
define('EASYBLOG_RATINGS_ENTRY', 'entry');

// teamblog access
define('EBLOG_TEAMBLOG_ACCESS_MEMBER', 1);
define('EBLOG_TEAMBLOG_ACCESS_REGISTERED', 2);
define('EBLOG_TEAMBLOG_ACCESS_EVERYONE', 3);

define( 'EBLOG_COMMENT_STATUS_UNPUBLISH' , 0 );
define( 'EBLOG_COMMENT_STATUS_PUBLISHED' , 1 );
define( 'EBLOG_COMMENT_STATUS_MODERATED' , 2 );

define( 'EBLOG_AVATAR_LARGE_WIDTH' , 160 );
define( 'EBLOG_AVATAR_LARGE_HEIGHT' , 160 );
define( 'EBLOG_AVATAR_THUMB_WIDTH' , 60 );
define( 'EBLOG_AVATAR_THUMB_HEIGHT' , 60 );

// Featured settings
define('EBLOG_FEATURED_BLOG', 'post');
define('EBLOG_FEATURED_BLOGGER', 'blogger');
define('EBLOG_FEATURED_TEAMBLOG', 'teamblog');

// @since 2.1 (Media manager type)
define( 'EBLOG_MEDIA_FOLDER', 'folder' );
define( 'EBLOG_MEDIA_IMAGE'	, 'image' );
define( 'EBLOG_MEDIA_FILE'	, 'file' );
define( 'EBLOG_VIDEO_FILE' , 'video' );
define( 'EBLOG_MEDIA_THUMBNAIL_PREFIX' , 'thumb_' );
define( 'EBLOG_MEDIA_PAGINATION_TOTAL' , 200 );
define( 'EBLOG_MEDIA_PERMISSION_ERROR' , -300 );
define( 'EBLOG_MEDIA_UPLOAD_SUCCESS' , 5 );
define( 'EBLOG_MEDIA_TRANSPORT_ERROR' , -200 );
define( 'EBLOG_MEDIA_SECURITY_ERROR' , -400 );
define( 'EBLOG_MEDIA_FILE_EXTENSION_ERROR' , -601 );
define( 'EBLOG_MEDIA_FILE_TOO_LARGE' , -600 );
define( 'EBLOG_GALLERY_EXTENSION' , '.jpg|.png|.gif|.JPG|.PNG|.GIF|.jpeg|.JPEG' );

// @since 2.1 (Update server)
define( 'EBLOG_UPDATER_SERVER' , 'http://node.stackideas.co:80/' );

// @since 2.1 (API Keys)
define( 'EBLOG_LOGGING_API' , '3a126c77e2abbc4c74bc34457a7808bb35cc81ea' );

// @since 3.1.7506 (Reporting)
define( 'EBLOG_REPORTING_POST' , 'post' );

// @since 3.5.7531
// EasyDiscuss notification types.
define( 'EBLOG_NOTIFICATIONS_TYPE_COMMENT'	, 'comment' );
define( 'EBLOG_NOTIFICATIONS_TYPE_BLOG'		, 'blog' );
define( 'EBLOG_NOTIFICATIONS_TYPE_RATING'	, 'rating' );

// @since 3.5.7706
// Microblogging types
define('EBLOG_MICROBLOG_EMAIL', 'email');
define( 'EBLOG_MICROBLOG_TEXT' , 'text' );
define( 'EBLOG_MICROBLOG_PHOTO', 'photo' );
define( 'EBLOG_MICROBLOG_QUOTE', 'quote' );
define( 'EBLOG_MICROBLOG_LINK' , 'link' );
define( 'EBLOG_MICROBLOG_VIDEO', 'video' );
define( 'EBLOG_MICROBLOG_TWITTER' , 'twitter' );

define( 'EBLOG_STREAM_NUM_ITEMS', 3 ); // in days
define( 'EBLOG_SOCIAL_BUTTONS', 'twitter,facebook,googleone,digg,linkedin,stumbleupon,pinit' );

// @since 3.5
// Pagination types
define( 'EBLOG_PAGINATION_BLOGGERS' , 'bloggers' );
define( 'EBLOG_PAGINATION_CATEGORIES' , 'categories' );
define( 'EBLOG_PAGINATION_TEAMBLOGS' , 'teamblogs' );


// @since 3.5
define( 'EBLOG_BLOG_IMAGE_PREFIX' , '2e1ax' );
define( 'EBLOG_USER_VARIATION_PREFIX' , 'a1sx2' );
define( 'EBLOG_SYSTEM_VARIATION_PREFIX' , 'b2ap3');
define( 'EBLOG_VARIATION_USER_TYPE' , 'user' );
define( 'EBLOG_VARIATION_SYSTEM_TYPE' , 'system' );

// @since 3.5
// Media sources
define('EBLOG_MEDIA_SOURCE_LOCAL'	, 'local' );
define( 'EBLOG_MEDIA_SOURCE_FLICKR'	, 'flickr' );
define( 'EBLOG_MEDIA_SOURCE_JOMSOCIAL' , 'jomsocial' );
define( 'EBLOG_MEDIA_SOURCE_EASYSOCIAL' , 'easysocial' );

// @since 3.5
// Media types
define( 'EBLOG_MEDIA_TYPE_IMAGE' 	, 'image' );
define( 'EBLOG_MEDIA_TYPE_FOLDER'	, 'folder' );
define( 'EBLOG_MEDIA_TYPE_VIDEO'	, 'video' );
define( 'EBLOG_MEDIA_TYPE_AUDIO'	, 'audio' );
define( 'EBLOG_MEDIA_TYPE_FILE'		, 'file' );

// default resize type
define( 'EBLOG_IMAGE_DEFAULT_RESIZE', 'within' );

// @since 4.0
// Session namespace
define('EBLOG_SESSION_NAMESPACE', 'easyblog');

// @since 4.0
// Language server
define('EBLOG_LANGUAGES_SERVER', 'http://lang.stackideas.com/easyblog');
define('EBLOG_LANGUAGES_INSTALLED' , 1 );
define('EBLOG_LANGUAGES_NOT_INSTALLED' , 0 );
define('EBLOG_LANGUAGES_NEEDS_UPDATING' , 3 );

// Exceptions
define('EASYBLOG_EXCEPTION_MESSAGE', 'message');
define('EASYBLOG_EXCEPTION_UPLOAD', 'upload');
define('EASYBLOG_MSG_SUCCESS', 'success');
define('EASYBLOG_MSG_WARNING', 'warning');
define('EASYBLOG_MSG_ERROR', 'error');
define('EASYBLOG_MSG_INFO', 'info');
define('EASYBLOG_ERROR_CODE_FIELDS', -500);

// Revisions
define('EASYBLOG_REVISION_FINALIZED', 0);
define('EASYBLOG_REVISION_DRAFT', 1);
define('EASYBLOG_REVISION_PENDING', 2);

// Publishing state
define('EASYBLOG_POST_BLANK'      , 9);
define('EASYBLOG_POST_DRAFT'      , 3);
define('EASYBLOG_POST_PENDING'    , 4);
define('EASYBLOG_POST_PUBLISHED'  , 1);
define('EASYBLOG_POST_SCHEDULED'  , 2);
define('EASYBLOG_POST_UNPUBLISHED', 0);

// Post state
define('EASYBLOG_POST_NORMAL', 0);
define('EASYBLOG_POST_TRASHED', 1);
define('EASYBLOG_POST_ARCHIVED', 2);

// Post source
define('EASYBLOG_POST_SOURCE_SITEWIDE', 'easyblog.sitewide');
define('EASYBLOG_POST_SOURCE_TEAM', 'easyblog.team');
define('EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP', 'jomsocial.group');
define('EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT', 'jomsocial.event');
define('EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT', 'easysocial.event');
define('EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP', 'easysocial.group');

// Block type
define('EASYBLOG_BLOCK_MODE_DIFF', 'compare');
define('EASYBLOG_BLOCK_MODE_VIEWABLE', 'viewable');
define('EASYBLOG_BLOCK_MODE_EDITABLE', 'editable');
define('EASYBLOG_POST_DOCTYPE_LEGACY', 'legacy');

// Blog rendering modes
define('EASYBLOG_VIEW_ENTRY', 'entry');
define('EASYBLOG_VIEW_LIST', 'list');
define('EASYBLOG_NO_DATE', '0000-00-00 00:00:00');

//no. of teamblog post to retrive for each team in team listing page.
define('EASYBLOG_TEAMBLOG_LISTING_NO_POST', 5);

// Post properties
define('EASYBLOG_STRIP_TAGS', true);

// Composer blocks publishing state
define('EASYBLOG_COMPOSER_BLOCKS_UNPUBLISHED', 0);
define('EASYBLOG_COMPOSER_BLOCKS_PUBLISHED', 1);
define('EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE', 2);

define('EASYBLOG_POST_TEMPLATE_CORE', 1);
define('EASYBLOG_POST_TEMPLATE_NOT_CORE', 0);

// Foundry
require_once(JPATH_ROOT . '/media/foundry/5.0/joomla/framework.php');
FD50_FoundryFramework::defineComponentConstants('EasyBlog');




