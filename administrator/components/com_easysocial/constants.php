<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Joomla
define('SOCIAL_JOOMLA', JPATH_ROOT);
define('SOCIAL_JOOMLA_URI', rtrim(JURI::root(), '/'));
define('SOCIAL_JOOMLA_ADMIN', SOCIAL_JOOMLA . '/administrator');
define('SOCIAL_JOOMLA_ADMIN_URI', SOCIAL_JOOMLA_URI . '/administrator');

define('SOCIAL_JOOMLA_SITE_TEMPLATES', SOCIAL_JOOMLA . '/templates');
define('SOCIAL_JOOMLA_SITE_TEMPLATES_URI', SOCIAL_JOOMLA_URI . '/templates');
define('SOCIAL_JOOMLA_ADMIN_TEMPLATES', SOCIAL_JOOMLA_ADMIN . '/templates');
define('SOCIAL_JOOMLA_ADMIN_TEMPLATES_URI', SOCIAL_JOOMLA_ADMIN_URI . '/templates');
define('SOCIAL_JOOMLA_MODULES', SOCIAL_JOOMLA . '/modules');
define('SOCIAL_JOOMLA_MODULES_URI', SOCIAL_JOOMLA_URI . '/modules');

// Foundry
define('SOCIAL_FOUNDRY_VERSION', '4.0');
define('SOCIAL_FOUNDRY_BOOTCODE', 'FD40');
define('SOCIAL_FOUNDRY', SOCIAL_JOOMLA . '/media/foundry/' . SOCIAL_FOUNDRY_VERSION);
define('SOCIAL_FOUNDRY_URI', SOCIAL_JOOMLA_URI . '/media/foundry/' . SOCIAL_FOUNDRY_VERSION);
define('SOCIAL_FOUNDRY_CONFIGURATION', SOCIAL_FOUNDRY . '/joomla/configuration.php');

// Component
define('SOCIAL_COMPONENT_NAME', 'com_easysocial');

define('SOCIAL_SITE', SOCIAL_JOOMLA . '/components/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_SITE_URI', SOCIAL_JOOMLA_URI . '/components/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_ADMIN', SOCIAL_JOOMLA_ADMIN . '/components/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_ADMIN_DEFAULTS', SOCIAL_JOOMLA_ADMIN . '/components/com_easysocial/defaults');
define('SOCIAL_ADMIN_UPDATES', SOCIAL_ADMIN . '/updates');
define( 'SOCIAL_ADMIN_QUERIES', SOCIAL_ADMIN . '/queries' );
define('SOCIAL_ADMIN_URI', SOCIAL_JOOMLA_ADMIN_URI . '/components/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_MEDIA', SOCIAL_JOOMLA . '/media/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_MEDIA_URI', SOCIAL_JOOMLA_URI . '/media/' . SOCIAL_COMPONENT_NAME);
define('SOCIAL_SCRIPTS', SOCIAL_MEDIA . '/scripts');
define('SOCIAL_SCRIPTS_URI', SOCIAL_MEDIA_URI . '/scripts');
define('SOCIAL_RESOURCES', SOCIAL_MEDIA . '/resources');
define('SOCIAL_RESOURCES_URI', SOCIAL_MEDIA_URI . '/resources');
define('SOCIAL_APPS', SOCIAL_MEDIA  . '/apps');
define('SOCIAL_APPS_URI', SOCIAL_MEDIA_URI . '/apps');
define('SOCIAL_TMP', SOCIAL_MEDIA . '/tmp');
define('SOCIAL_TMP_URI', SOCIAL_MEDIA_URI . '/tmp');
define('SOCIAL_LIB', SOCIAL_ADMIN . '/includes');
define('SOCIAL_MODELS', SOCIAL_ADMIN . '/models');
define('SOCIAL_MANIFESTS', SOCIAL_ADMIN . '/manifests');
define('SOCIAL_TABLES', SOCIAL_ADMIN . '/tables');
define('SOCIAL_FIELDS', SOCIAL_APPS . '/fields');
define('SOCIAL_PROFILETYPES', SOCIAL_MEDIA . '/avatars/profiletypes');

// Default configuration path
define('SOCIAL_CONFIG_DEFAULTS', SOCIAL_ADMIN . '/defaults');

// Themes
define('SOCIAL_SITE_THEMES', SOCIAL_SITE . '/themes');
define('SOCIAL_SITE_THEMES_URI', SOCIAL_SITE_URI . '/themes');
define('SOCIAL_ADMIN_THEMES', SOCIAL_ADMIN . '/themes');
define('SOCIAL_ADMIN_THEMES_URI', SOCIAL_ADMIN_URI . '/themes');

// Avatar sizes
define('SOCIAL_AVATAR_SMALL', 'small');
define('SOCIAL_AVATAR_MEDIUM', 'medium');
define('SOCIAL_AVATAR_LARGE', 'large');
define('SOCIAL_AVATAR_SQUARE', 'square');

// Avatar dimensions
define('SOCIAL_AVATAR_SMALL_WIDTH', 32);
define('SOCIAL_AVATAR_SMALL_HEIGHT', 32);
define('SOCIAL_AVATAR_MEDIUM_WIDTH', 64);
define('SOCIAL_AVATAR_MEDIUM_HEIGHT', 64);
define('SOCIAL_AVATAR_LARGE_WIDTH', 180);
define('SOCIAL_AVATAR_SQUARE_LARGE_WIDTH', 180);
define('SOCIAL_AVATAR_SQUARE_LARGE_HEIGHT', 180);

// Cover sizes
define('SOCIAL_COVER_SMALL', 'small');
define('SOCIAL_COVER_LARGE', 'large');
define('SOCIAL_COVER_DEFAULT', 'default');

// Cover dimensions
define('SOCIAL_COVER_SMALL_WIDTH', 320);
define('SOCIAL_COVER_SMALL_HEIGHT', 120);
define('SOCIAL_COVER_LARGE_WIDTH', 960);
define('SOCIAL_COVER_LARGE_HEIGHT', 360);

// Location types
define('SOCIAL_LOCATION_SITE', 'site');
define('SOCIAL_LOCATION_ADMIN', 'admin');

// Default storage location for default images.
define('SOCIAL_DEFAULTS_URI', SOCIAL_JOOMLA_URI . '/media/com_easysocial/images/defaults');

// Error codes
define('ERROR_INSTALLER_XML', 100);

// Application types
define('SOCIAL_APPS_TYPE_APPS', 'apps');
define('SOCIAL_APPS_TYPE_FIELDS', 'fields');
define('SOCIAL_APPS_VIEW_TYPE_EMBED', 'embed');
define('SOCIAL_APPS_VIEW_TYPE_CANVAS', 'canvas');

// Application icons
define('SOCIAL_APPS_ICON_LARGE', 'large');
define('SOCIAL_APPS_ICON_SMALL', 'small');

// Application groups
define('SOCIAL_APPS_GROUP_USER', 'user');
define('SOCIAL_APPS_GROUP_GROUP', 'group');
define('SOCIAL_APPS_GROUP_EVENT', 'event');

// States
define('SOCIAL_STATE_TRASHED', -1);
define('SOCIAL_STATE_DEFAULT', 2);
define('SOCIAL_STATE_PUBLISHED', 1);
define('SOCIAL_STATE_UNPUBLISHED', 0);
define('SOCIAL_APP_STATE_DISCOVERED', -1);

// User states
define('SOCIAL_JOOMLA_USER_UNBLOCKED', 0);
define('SOCIAL_JOOMLA_USER_BLOCKED', 1);
define('SOCIAL_USER_STATE_DISABLED', 0);
define('SOCIAL_USER_STATE_ENABLED', 1);
define('SOCIAL_USER_STATE_ACTIVATION', 2);
define('SOCIAL_USER_STATE_PENDING', 3);

// Skeleton file
define('SOCIAL_SKELETON_INDEX', SOCIAL_MEDIA . '/index.html');

// Posted data
define('SOCIAL_POSTED_DATA', true);

// Mailer priorities
define('SOCIAL_MAILER_PRIORITY_IMMEDIATE', 5);
define('SOCIAL_MAILER_PRIORITY_HIGH', 3);
define('SOCIAL_MAILER_PRIORITY_NORMAL', 2);
define('SOCIAL_MAILER_PRIORITY_LOW', 1);

// Mailer sent status
define('SOCIAL_MAILER_SENT', true);
define('SOCIAL_MAILER_PENDING', 0);
define('SOCIAL_MAILER_NO_TEMPLATE', null);

// ACL Types
define('SOCIAL_ACCESS_BOOLEAN', 'boolean');
define('SOCIAL_ACCESS_LIMIT', 'limits');
define('SOCIAL_ACCESS_HEADER', 'header');

// Session namespaces
define('SOCIAL_SESSION_NAMESPACE', 'com_easysocial');

// State for translating registration type
define('SOCIAL_TRANSLATE_REGISTRATION', true);

// Color states for info messages
define('SOCIAL_MSG_SUCCESS', 'success');
define('SOCIAL_MSG_WARNING', 'warning');
define('SOCIAL_MSG_ERROR', 'error');
define('SOCIAL_MSG_INFO', 'info');

// The initial step number
define('SOCIAL_REGISTER_SELECTPROFILE_STEP', 0);
define('SOCIAL_REGISTER_COMPLETED_STEP', 'completed');

// States for registration types
define('SOCIAL_REGISTER_AUTO', 1);
define('SOCIAL_REGISTER_LOGIN', 1);
define('SOCIAL_REGISTER_VERIFY', 2);
define('SOCIAL_REGISTER_APPROVALS', 3);

define('SOCIAL_REGISTER_AUTO_TEXT', 'auto');
define('SOCIAL_REGISTER_VERIFY_TEXT', 'verify');
define('SOCIAL_REGISTER_APPROVAL_TEXT', 'approval');

// Conversation types
define('SOCIAL_CONVERSATION_SINGLE', 1);
define('SOCIAL_CONVERSATION_MULTIPLE', 2);

// Conversation states
define('SOCIAL_CONVERSATION_READ', 1);
define('SOCIAL_CONVERSATION_UNREAD', 0);
define('SOCIAL_CONVERSATION_STATE_ARCHIVED', 0);
define('SOCIAL_CONVERSATION_STATE_PUBLISHED', 1);
define('SOCIAL_CONVERSATION_STATE_LEFT', 0);
define('SOCIAL_CONVERSATION_STATE_PARTICIPATING', 1);
define('SOCIAL_CONVERSATION_TYPE_LEAVE', 'leave');
define('SOCIAL_CONVERSATION_TYPE_JOIN', 'join');
define('SOCIAL_CONVERSATION_TYPE_MESSAGE', 'message');
define('SOCIAL_CONVERSATION_ATTACHMENTS_PUBLISHED', 1);

// Calendar/Date
define('SOCIAL_DATE_YEAR_LIST_LIMIT', 100);

// Custom field
define('SOCIAL_CUSTOM_FIELD_PREFIX', 'es-fields');

// Friends
define('SOCIAL_FRIENDS_STATE_PENDING', -1);
define('SOCIAL_FRIENDS_STATE_FRIENDS', 1);
define('SOCIAL_FRIENDS_STATE_REJECTED', 2);
define('SOCIAL_FRIENDS_SEARCH_NAME', 'name');
define('SOCIAL_FRIENDS_SEARCH_REALNAME', 'realname');
define('SOCIAL_FRIENDS_SEARCH_USERNAME', 'username');

// Friends list
define('SOCIAL_FRIENDS_LIST_PUBLISHED', 1);
define('SOCIAL_FRIENDS_LIST_PUBLISHED_CORE', 2);

// Node types
define('SOCIAL_TYPE_USER', 'user');
define('SOCIAL_TYPE_FIELD', 'field');
define('SOCIAL_TYPE_USERGROUP', 'usergroup');
define('SOCIAL_TYPE_PROFILES', 'profiles');
define('SOCIAL_TYPE_GROUP', 'group');
define('SOCIAL_TYPE_GROUPS', 'groups');
define('SOCIAL_TYPE_CLUSTERS', 'clusters');
define('SOCIAL_TYPE_STREAM', 'stream');
define('SOCIAL_TYPE_LIKES', 'likes');
define('SOCIAL_TYPE_COMMENTS', 'comments');
define('SOCIAL_TYPE_CONVERSATIONS', 'conversations');
define('SOCIAL_TYPE_FRIEND', 'friends');
define('SOCIAL_TYPE_LISTS', 'lists');
define('SOCIAL_TYPE_REGISTRATIONS', 'registrations');
define('SOCIAL_TYPE_FOLLOWERS', 'followers');
define('SOCIAL_TYPE_STORY', 'story');
define('SOCIAL_TYPE_LINKS', 'links');
define('SOCIAL_TYPE_ALBUM', 'albums');
define('SOCIAL_TYPE_PHOTO', 'photos');
define('SOCIAL_TYPE_BADGES', 'badges');
define('SOCIAL_TYPE_SHARE', 'shares');
define('SOCIAL_TYPE_AVATAR', 'avatar');
define('SOCIAL_TYPE_FACEBOOK', 'facebook');
define('SOCIAL_TYPE_USERS', 'users');
define('SOCIAL_TYPE_APPS', 'apps');
define('SOCIAL_TYPE_ACTIVITY', 'activity');
define('SOCIAL_TYPE_FILES', 'files');
define('SOCIAL_TYPE_EVENT', 'event');
define('SOCIAL_TYPE_EVENTS', 'events');
define('SOCIAL_TYPE_POLLS', 'polls');
define('SOCIAL_TYPE_VIDEO', 'video');
define('SOCIAL_TYPE_VIDEOS', 'videos');

// Region types
define('SOCIAL_REGION_TYPE_COUNTRY', 'country');
define('SOCIAL_REGION_TYPE_STATE', 'state');
define('SOCIAL_REGION_TYPE_CITY', 'city');

// Model pagination
define('SOCIAL_PAGINATION_ENABLE', true);
define('SOCIAL_PAGINATION_NO_LIMIT', -1);

// Notification states
define('SOCIAL_NOTIFICATION_STATE_READ', 1);
define('SOCIAL_NOTIFICATION_STATE_HIDDEN', 2);
define('SOCIAL_NOTIFICATION_STATE_UNREAD', 0);
define('SOCIAL_NOTIFICATION_AGGREGATE_ITEMS', true);
define('SOCIAL_NOTIFICATION_GROUP_ITEMS', true);


define('SOCIAL_PRIVACY_PUBLIC', 0);
define('SOCIAL_PRIVACY_MEMBER', 10);
define('SOCIAL_PRIVACY_FRIENDS_OF_FRIEND', 20);
define('SOCIAL_PRIVACY_FRIEND', 30);
define('SOCIAL_PRIVACY_ONLY_ME', 40);
define('SOCIAL_PRIVACY_CUSTOM', 100);
define('SOCIAL_PRIVACY_0', 'public');
define('SOCIAL_PRIVACY_10', 'member');
define('SOCIAL_PRIVACY_20', 'friends_of_friend');
define('SOCIAL_PRIVACY_30', 'friend');
define('SOCIAL_PRIVACY_40', 'only_me');
define('SOCIAL_PRIVACY_100', 'custom');

define('SOCIAL_PRIVACY_TYPE_USER', 'user');
define('SOCIAL_PRIVACY_TYPE_PROFILES', 'profiles');
define('SOCIAL_PRIVACY_TYPE_ITEM', 'item');

// Services Server
define('SOCIAL_SERVICE_NEWS', 'http://stackideas.com/updater/manifests/easysocial');

// Define theme variables
define('SOCIAL_THEME_COMPILE_AUTO', 'auto');
define('SOCIAL_THEME_COMPILE_MANUAL', 'manual');
define('SOCIAL_THEME_COMPILE_CACHE', 'cache');

// Define custom fields constants
define('SOCIAL_FIELDS_GROUP_USER', 'user');
define('SOCIAL_FIELDS_GROUP_GROUP', 'group');
define('SOCIAL_FIELDS_GROUP_EVENT', 'event');
define('SOCIAL_FIELDS_PREFIX', 'es-fields-');

// Profiles
define('SOCIAL_PROFILES_VIEW_MINI_REGISTRATION', 'mini_registration');
define('SOCIAL_PROFILES_VIEW_REGISTRATION', 'registration');
define('SOCIAL_PROFILES_VIEW_EDIT', 'edit');
define('SOCIAL_PROFILES_VIEW_DISPLAY', 'display');

// Groups
define('SOCIAL_GROUPS_VIEW_REGISTRATION', 'registration');
define('SOCIAL_GROUPS_VIEW_EDIT', 'edit');
define('SOCIAL_GROUPS_VIEW_DISPLAY', 'display');
define('SOCIAL_GROUPS_PUBLIC_TYPE', 1);
define('SOCIAL_GROUPS_PRIVATE_TYPE', 2);
define('SOCIAL_GROUPS_INVITE_TYPE', 3);
define('SOCIAL_GROUPS_MEMBER_PUBLISHED', 1);
define('SOCIAL_GROUPS_MEMBER_PENDING', 2);
define('SOCIAL_GROUPS_MEMBER_INVITED', 3);
define('SOCIAL_GROUPS_MEMBER_BEING_JOINED', 4);

// Define points constants
define('SOCIAL_POINTS_EVERY_TIME', 0);

// Define stream display modes.
define('SOCIAL_STREAM_DISPLAY_FULL', 'full');
define('SOCIAL_STREAM_DISPLAY_MINI', 'mini');

// define stream actor type
define('SOCIAL_STREAM_ACTOR_TYPE_USER', 'user');

// defune stream context type core.
define('SOCIAL_STREAM_CONTEXT_TASKS', 'tasks');
define('SOCIAL_STREAM_CONTEXT_LISTS', 'lists');
define('SOCIAL_STREAM_CONTEXT_FRIENDS', 'friends');
define('SOCIAL_STREAM_CONTEXT_PHOTOS', 'photos');
define('SOCIAL_STREAM_CONTEXT_ALBUMS', 'albums');

//define stream hide type
define('SOCIAL_STREAM_HIDE_TYPE_STREAM', 'stream');
define('SOCIAL_STREAM_HIDE_TYPE_ACTIVITY', 'activity');

// define stream tagging type
define('SOCIAL_STREAM_TAGGING_TYPE_USER', 'user');

//define subsription type.
define('SOCIAL_SUBSCRIPTION_TYPE_USER', 'user');
define('SOCIAL_SUBSCRIPTION_TYPE_STREAM', 'stream');

// Photos
define('SOCIAL_PHOTOS_SQUARE_WIDTH', 128);
define('SOCIAL_PHOTOS_SQUARE_HEIGHT', 128);
define('SOCIAL_PHOTOS_THUMB_WIDTH', 256);
define('SOCIAL_PHOTOS_THUMB_HEIGHT', 256);
define('SOCIAL_PHOTOS_FEATURED_WIDTH', 512);
define('SOCIAL_PHOTOS_FEATURED_HEIGHT', 512);
define('SOCIAL_PHOTOS_LARGE_WIDTH', 1280);
define('SOCIAL_PHOTOS_LARGE_HEIGHT', 1280);
define('SOCIAL_PHOTOS_META_PATH', 'path');
define('SOCIAL_PHOTOS_META_EXIF', 'exif');
define('SOCIAL_PHOTOS_META_TRANSFORM', 'transform');
define('SOCIAL_PHOTOS_LARGE', 'large');
define('SOCIAL_PHOTOS_THUMB', 'thumb');
define('SOCIAL_PHOTOS_STATE_TMP', '-1');
define('SOCIAL_PHOTOS_META_WIDTH', 'width');
define('SOCIAL_PHOTOS_META_HEIGHT', 'height');
define('SOCIAL_PHOTOS_META_SIZE', 'size');

// stream type
define('SOCIAL_STREAM_CONTEXT_TYPE_ALL', 'all');

// Define notification property.
define('SOCIAL_NOTIFICATION_FORMAT_ITEM', true);

define('SOCIAL_LIKES_MAX_NAME', 3);

// define indexer core type.
define('SOCIAL_INDEXER_TYPE_LISTS', 'lists');
define('SOCIAL_INDEXER_TYPE_USERS', 'users');
define('SOCIAL_INDEXER_TYPE_PHOTOS', 'photos');
define('SOCIAL_INDEXER_TYPE_ALBUMS', 'albums');
define('SOCIAL_INDEXER_TYPE_GROUPS', 'groups');
define('SOCIAL_INDEXER_TYPE_EVENTS', 'events');

// Album constants
define('SOCIAL_ALBUM_PROFILE_PHOTOS', 1);
define('SOCIAL_ALBUM_PROFILE_COVERS', 2);
define('SOCIAL_ALBUM_STORY_ALBUM', 3);

// Updater
define('SOCIAL_UPDATER_LANGUAGE', 'http://lang.stackideas.com/easysocial');

// Languages
define('SOCIAL_LANGUAGES_INSTALLED', 1);
define('SOCIAL_LANGUAGES_NOT_INSTALLED', 0);
define('SOCIAL_LANGUAGES_NEEDS_UPDATING', 3);

// stream hard limit for pagination
define('SOCIAL_STREAM_HARD_LIMIT', 25);
define('SOCIAL_STREAM_GUEST_LIMIT', 10);


// Remote storage contants
define('SOCIAL_STORAGE_DEFAULT_CONTAINER', 'easysocial');
define('SOCIAL_STORAGE_JOOMLA', 'joomla');

// Exceptions
define('SOCIAL_EXCEPTION_MESSAGE', 'message');
define('SOCIAL_EXCEPTION_UPLOAD', 'upload');

// Clusters
define('SOCIAL_CLUSTER_PUBLISHED', 1);
define('SOCIAL_CLUSTER_UNPUBLISHED', 0);
define('SOCIAL_CLUSTER_PENDING', 2);

// Events
define('SOCIAL_EVENT_VIEW_REGISTRATION', 'registration');
define('SOCIAL_EVENT_VIEW_EDIT', 'edit');
define('SOCIAL_EVENT_VIEW_DISPLAY', 'display');
define('SOCIAL_EVENT_TYPE_PUBLIC', 1);
define('SOCIAL_EVENT_TYPE_PRIVATE', 2);
define('SOCIAL_EVENT_TYPE_INVITE', 3);

// Event guests state
define('SOCIAL_EVENT_GUEST_INVITED', 0);
define('SOCIAL_EVENT_GUEST_GOING', 1);
define('SOCIAL_EVENT_GUEST_PENDING', 2);
define('SOCIAL_EVENT_GUEST_MAYBE', 3);
define('SOCIAL_EVENT_GUEST_NOT_GOING', 4);
define('SOCIAL_EVENT_GUEST_NOTGOING', 4);

define('SOCIAL_STREAM_STATE_TRASHED', '0');
define('SOCIAL_STREAM_STATE_PUBLISHED', '1');
define('SOCIAL_STREAM_STATE_RESTORED', '2');
define('SOCIAL_STREAM_STATE_ARCHIVED', '3');
define('SOCIAL_STREAM_STATE_MODERATE', '5');

define('SOCIAL_CLUSTER_CATEGORY_MEMBERS_LIMIT', 10);

define('SOCIAL_TASK_UNPUBLISHED', 0);
define('SOCIAL_TASK_UNRESOLVED', 1);
define('SOCIAL_TASK_OPEN', 1);
define('SOCIAL_TASK_RESOLVED', 2);
define('SOCIAL_TASK_CLOSED', 2);

// Source: http://www.phpro.org/tutorials/Geo-Targetting-With-PHP-And-MySQL.html
define('SOCIAL_LOCATION_UNIT_MILE', 69);
define('SOCIAL_LOCATION_UNIT_KM', 111);
define('SOCIAL_LOCATION_RADIUS_MILE', 3959);
define('SOCIAL_LOCATION_RADIUS_KM', 6371);

define('SOCIAL_STREAM_LAST_ACTION_LIKE', 'like');
define('SOCIAL_STREAM_LAST_ACTION_COMMENT', 'comment');

define('SOCIAL_VIDEO_PUBLISHED', 1);
define('SOCIAL_VIDEO_UNPUBLISHED', 0);
define('SOCIAL_VIDEO_PENDING', 2);
define('SOCIAL_VIDEO_PROCESSING', 3);
define('SOCIAL_VIDEO_FEATURED', 1);
define('SOCIAL_VIDEO_UPLOAD', 'upload');
define('SOCIAL_VIDEO_LINK', 'link');

define('SOCIAL_ACCESS_LIMIT_INTERVAL_NO', 0);
define('SOCIAL_ACCESS_LIMIT_INTERVAL_DAILY', 1);
define('SOCIAL_ACCESS_LIMIT_INTERVAL_WEEKLY', 2);
define('SOCIAL_ACCESS_LIMIT_INTERVAL_MONTHLY', 3);
define('SOCIAL_ACCESS_LIMIT_INTERVAL_YEARLY', 4);

// Foundry
require_once(JPATH_ROOT . '/media/foundry/4.0/joomla/framework.php');
FD40_FoundryFramework::defineComponentConstants('EasySocial');
