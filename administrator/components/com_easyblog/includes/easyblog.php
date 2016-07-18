<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/utils.php');


if (!defined('EASYBLOG_COMPONENT_CLI')) {
	require_once(__DIR__ . '/router.php');
	require_once(EBLOG_ROOT . '/router.php');
}

class EB
{
	public static $headersLoaded = null;

	/**
	 * Initializes EasyBlog's javascript framework
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public static function init($section = 'site')
	{
		$doc = JFactory::getDocument();

		// Get the cdn url
		$cdnRoot = EB::getCdnUrl();

		if ($cdnRoot) {
			FD50_FoundryFramework::defineComponentCDNConstants('EASYBLOG', $cdnRoot, $passiveCdn);
		}

		// Determines if we should compile the javascripts on the site
		$app = JFactory::getApplication();
		$input = $app->input;
		$compile = $input->get('compile', false, 'bool');

		if (EB::isSiteAdmin() && $compile) {

			// Determines if we need to minify the js
			$minify = $input->get('minify', false, 'bool');

			// Get section if not default one
			$section = $input->get('section', $section, 'cmd');

			// Get the compiler
			$compiler = EB::compiler();
			$result = $compiler->compile($section, $minify);

			if ($result !== false) {
				EB::info()->set('Javascript library recompiled successfully.', 'success');
				return $app->redirect(EBR::_('index.php?option=com_easyblog'));
			}
		}

		// If this is a non html view, skip this altogether
		if ($doc->getType() !=='html') {
			return;
		}

		if (!self::$headersLoaded) {
			$url = EB::getBaseUrl();

			// Get configuration instance
			$configuration = EB::configuration();

			// Attach configuration to headers
			$configuration->attach($section);

			self::$headersLoaded = true;
		}

		return self::$headersLoaded;
	}

	/**
	 * Retrieves the cdn url for the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCdnUrl()
	{
		$config = EB::config();

		// Override with CDN settings.
		if (!$config->get('enable_cdn')) {
			return false;
		}

		$url = $config->get('cdn_url');

		if (!$url) {
			return false;
		}

		// Ensure that the url contains http:// or https://
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = '//' . $url;
		}

		return $url;
	}

	/**
	 * Load EasyBlog's ACL
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function acl($userId = '')
	{
		static $acl = array();

		if (!$userId) {
			$userId = JFactory::getuser()->id;
		}

		if (!isset($acl[$userId])) {

			require_once(dirname(__FILE__) . '/acl/acl.php');

			$acl[$userId] = EasyBlogAcl::getRuleSet($userId);
		}

		return $acl[$userId];
	}

	/**
	 * Proxy for media manager
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function mediamanager($source = EBLOG_MEDIA_SOURCE_LOCAL)
	{
		require_once(dirname(__FILE__) . '/mediamanager/mediamanager.php');

		$media = new EasyBlogMediaManager($source);

		return $media;
	}

	/**
	 * Creates a new stylesheet instance
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function stylesheet($location, $name = null, $useOverride = false)
	{
		require_once(dirname(__FILE__) . '/stylesheet/stylesheet.php');

		if ($location == 'site') {

			//lets overrite the theme based on blogger theme
			$bloggerTheme = EB::getBloggerTheme();
			if ($bloggerTheme) {
				$name = $bloggerTheme;
			}

		}

		$stylesheet = new EasyBlogStylesheet($location, $name, $useOverride);

		return $stylesheet;
	}

	/**
	 * Creates a new instance of the exception library.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function exception($message='', $type = EASYBLOG_MSG_ERROR, $silent = false, $customErrorCode = null)
	{
		require_once(dirname(__FILE__) . '/exception/exception.php');

		$exception = new EasyBlogException($message, $type, $silent, $customErrorCode);

		return $exception;
	}

	/**
	 * Determines if the current URL is on blogger mode
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isBloggerMode()
	{
		static $itemIds	= null;

		$app = JFactory::getApplication();

		$itemId = $app->input->get('Itemid', 0, 'int');
		// $blogger = $app->input->get('blogger', 0, 'int');

		// if (!empty($blogger)) {
		// 	return $blogger;
		// }

		if (empty($itemId)) {
			return false;
		}

		if (!isset($itemIds[$itemId])) {
			$id = false;

			$menu = $app->getMenu();
			$params = $menu->getParams($itemId);

			if ($params->get('standalone_blog', false)) {
				$id = EBR::getBloggerIdFromMenu($itemId);
			}

			$itemIds[$itemId]   = $id;
		}

		return $itemIds[$itemId];
	}

	/**
	 * Retrieve the current language tag set
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLanguageTag()
	{
		static $tag = false;

		if (!$tag) {
			$tag = JFactory::getLanguage()->getTag();
		}

		return $tag;
	}

	/**
	 * Retrieve the current language tag set
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getMomentLanguage()
	{
		static $tag = false;

		if (!$tag) {
			$tag = JFactory::getLanguage()->getTag();

			$tag = explode('-', $tag);
			$tag = $tag[0];

			if ($tag == 'en') {
				$tag = 'en-gb';
			}
		}

		return $tag;
	}

	/**
	 * Determines if the site has multi lingual capability
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public static function isMultiLingual()
	{
		$enabled = JFactory::getApplication()->getLanguageFilter();

		return $enabled;
	}

	/**
	 * Load EasyBlog's router
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function router()
	{
		static $obj = null;

		if (is_null($obj)) {

			require_once(dirname(__FILE__) . '/router.php');
			$obj = new EasyBlogRouter();
		}

		return $obj;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function javascript(&$chain)
	{
		require_once(dirname(__FILE__) . '/javascript/javascript.php');

		$js 	= new EasyBlogJavascript($chain);

		return $js;
	}

	/**
	 * Get's the database object.
	 *
	 * @since	3.7
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function db()
	{
		static $db = null;

		if (!$db) {
			$db = new EasyBlogDbJoomla();
		}

		return $db;
	}

	/**
	 * Loads the date helper object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function date($current = '', $offset = null)
	{
		require_once(dirname(__FILE__) . '/date/date.php');

		$date = new EasyBlogDate($current, $offset);

		return $date;
	}

	/**
	 * Load's foundry's configuration library
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function configuration($section = 'site')
	{
		require_once(__DIR__ . '/configuration/configuration.php');

		$lib = EasyBlogConfiguration::getInstance($section);

		return $lib;
	}

	/**
	 * Load's EasyBlog's settings object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function config()
	{
		static $config	= null;

		if (is_null($config)) {

			// Load up default configuration file
			$file = EBLOG_ADMIN_ROOT . '/defaults/configuration.ini';
			$raw = JFile::read($file);

			$registry = new JRegistry($raw);

			// Get config stored in db
			$table = EB::table('Configs');
			$table->load('config');

			// Load the stored config as a registry
			$stored	= new JRegistry($table->params);

			$registry->merge($stored);

			if (!$stored->get('main_blocked_words')) {
				$registry->set('main_blocked_words', '');
			}

			$config = $registry;
		}

		return $config;
	}

	/**
	 * Renders the info object
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public static function info()
	{
		static $instance = null;

		require_once(dirname(__FILE__) . '/info/info.php');

		if (is_null($instance)) {

			$info = new EasyBlogInfo();

			$instance = $info;
		}

		return $instance;
	}

	/**
	 * Retrieves Joomla configuration object
	 *
	 * @deprecated 	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getJConfig()
	{
		$jConfig = JFactory::getConfig();

		return $jConfig;
	}

	/**
	 * Returns a new registry object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function registry($contents = '', $isFile = false)
	{
		$registry	= new JRegistry($contents, $isFile);

		return $registry;
	}

	/**
	 * Method to retrieve a registry object
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getRegistry($contents = '', $isFile = false)
	{
		return EB::registry($contents, $isFile);
	}

	/**
	 * Retrieves the token
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getToken()
	{
		$session = JFactory::getSession();
		$token = $session->getFormToken();

		return $token;
	}

	/**
	 * Loads a library dynamically
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function __callStatic($method, $arguments)
	{
		$file = dirname(__FILE__) . '/' . strtolower($method) . '/' . strtolower($method) . '.php';

		require_once($file);

		$class = 'EasyBlog' . ucfirst($method);

		$obj = new $class($arguments);

		return $obj;
	}

	/**
	 * Single point of entry for static calls.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The class name without prefix. E.g: (Themes)
	 * @param	string	The method name
	 * @param	Array	An array of arguments.
	 * @return
	 */
	public static function call($className , $method , $args = array() )
	{
		// We always want lowercased items.
		$item			= strtolower($className);
		$obj			= false;

		$path			= dirname(__FILE__) . '/' . $item . '/' . $item . '.php';

		require_once( $path );

		$class 	= 'EasyBlog' . ucfirst( $className );

		if (!class_exists($class)) {
			return false;
		}

		if (!method_exists($class, $method)) {
			return false;
		}

		return call_user_func_array(array($class, $method), $args);
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function blogimage($path, $uri)
	{
		require_once(dirname(__FILE__) . '/blogimage/blogimage.php');

		$image = new EasyBlogBlogImage($path, $uri);

		return $image;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function category()
	{
		require_once(dirname(__FILE__) . '/category/category.php');

		$category 	= new EasyBlogCategory();

		return $category;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @since	4.0
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyBlogXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function helper($helper)
	{
		static $obj	= array();

		if (!isset($obj[$helper])) {

			$file = dirname(__FILE__) . '/' . strtolower($helper) . '/' . strtolower($helper) . '.php';

			require_once($file);
			$class = 'EasyBlog' . ucfirst($helper);

			$obj[$helper] = new $class();
		}

		return $obj[$helper];
	}

	/**
	 * Retrieve the limit defined in the settings given the specific key
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLimit($key = 'listlength')
	{
		$app = JFactory::getApplication();

		// Get the default limit
		$default = EB::jconfig()->get('list_limit');

		if ($app->isAdmin()) {
			return $default;
		}

		// Get the current active menu
		$active = $app->getMenu()->getActive();
		$limit  = -2;

		if (is_object($active)) {
			$limit = $active->params->get('limit', '-2');
		}

		// if menu did not specify the limit, then we use easyblog setting.
		if ($limit == '-2') {

			// Use default configurations.
			$config = EB::config();

			// @rule: For compatibility between older versions
			if ($key == 'listlength') {
				$key 	= 'layout_listlength';
			} else {
				$key 	= 'layout_pagination_' . $key;
			}

			$limit      = $config->get($key);
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2') {

			return $default;
		}

		return $limit;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @deprecated 	4.0
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyBlogXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelper($helper)
	{
		return self::helper($helper);
	}

	/**
	 * Pagination for EasyBlog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function pagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature])) {

			require_once(dirname(__FILE__) . '/pagination/pagination.php');

			$pagination	= new EasyBlogPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	/**
	 * @deprecated since 3.5
	 *
	 */
	public static function getNotification()
	{
		return self::getHelper( 'Notification' );
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getOnlineParser()
	{
		$data = new stdClass();

		// Get the xml file
		$site = 'http://stackideas.com/updater/manifests/easyblog';

		$connector = EB::connector();
		$connector->addUrl($site);
		$connector->execute();

		$contents = $connector->getResult($site);

		$obj = json_decode($contents);

		return $obj;
	}

	/**
	 * Retrieves the default placeholder image
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlaceholderImage($protocol = false)
	{
		static $image = null;

		if (is_null($image)) {
			$app = JFactory::getApplication();
			$file = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easyblog/images/placeholder.png';

			// Default placeholder image.
			$default = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/placeholder-image.png';

			if (JFile::exists($file)) {
				$default = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easyblog/images/placeholder.png';
			}

			$image = $default;
		}

		return $image;
	}

	/**
	 * Retrieves local version
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLocalParser()
	{
		static $parser = null;

		if (is_null($parser)) {
			$contents = JFile::read(JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.xml');
			$parser = JFactory::getXML($contents, false);
		}

		return $parser;
	}

	/**
	 * Retrieves the installed version of EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$parser	= EB::getLocalParser();

			if (!$parser) {
				$version = false;
				return $version;
			}

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Retrieves the latest version of EasyBlog from our server
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLatestVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$data = EB::getOnlineParser();

			$version = $data->version;
		}

		return $version;
	}

	/**
	 * Method to retrieve a table object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The name of the table
	 * @return	Table
	 */
	public static function table($name)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/tables/table.php');
		$table 	= EasyBlogTable::getInstance($name);

		return $table;
	}


	/**
	 * Method to retrieve a EasyBlogUser object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser		The user's object
	 */
	public static function user( $ids = null , $debug = false )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/user/user.php';
		include_once($path);

		return EasyBlogUser::factory($ids, $debug);
	}


	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function request()
	{
		require_once(dirname(__FILE__) . '/request/request.php');

		$request 	= new EasyBlogRequest();

		return $request;
	}

	/**
	 * Method to retrieve a model
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string		The name of the model
	 * @return
	 */
	public static function model($name)
	{
		static $models	= array();

		$index = $name;

		if (!isset($models[$index])) {

			$file = strtolower($name);
			$path = JPATH_ROOT . '/administrator/components/com_easyblog/models/' . $file . '.php';

			require_once($path);

			$class = 'EasyBlogModel' . ucfirst($name);

			$obj = new $class();
			$models[$index] = $obj;
		}

		return $models[$index];
	}

	/**
	 * Proxy to EB::model
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getModel($name)
	{
		return EB::model($name);
	}

	/**
	 * Loads the configuration object for EasyBlog
	 *
	 * @deprecated 	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getConfig()
	{
		return EB::config();
	}

	/**
	 * Determines if the user is logged into the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isLoggedIn()
	{
		$my = JFactory::getUser();

		return !$my->guest;
	}

	/**
	 * Determines if the user is a super admin on the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int
	 * @return
	 */
	public static function isSiteAdmin($id = null)
	{
		static $items = array();

		$user = JFactory::getUser($id);

		if (!isset($items[$user->id])) {
			$items[$user->id] = $user->authorise('core.admin');
		}

		return $items[$user->id];
	}

	/**
	 * Determines if the user is a team admin
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isTeamAdmin()
	{
		static $admins = null;

		$my	= JFactory::getUser();

		if ($my->guest) {
			return false;
		}

		if (EB::isSiteAdmin()) {
			return true;
		}

		if (!isset($admisn[$my->id])) {

			$model = EB::model('TeamBlogs');
			$admins[$my->id] = $model->checkIsTeamAdmin($my->id);
		}

		return $admins[$my->id];
	}

	/**
	 * Retrieves the comment count for a post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCommentCount($id)
	{
		$post = EB::post($id);
		$count = EB::comment()->getCommentCount($post);

		return $count;
	}

	/**
	 * Removes the featured image tag from a block of text
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function removeFeaturedImage($text)
	{
		$pattern = '#<img class="featured[^>]*>#i';
		$pattern = '#<a class="easyblog-thumb-preview featured(.*)</a>#i';

		return preg_replace($pattern, '', $text, 1);
	}

	/**
	 * Removes the gallery tag from a block of text.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function removeGallery($text)
	{
		$pattern	= '#<div class="easyblog-placeholder-gallery"(.*)</div>#is';

		return preg_replace( $pattern , '' , $text );
	}

	/**
	 * Verifies the password for a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function verifyBlogPassword($crypt, $id)
	{
		if (!empty($crypt) && !empty($id)) {
			$session = JFactory::getSession();
			$password = $session->get('PROTECTEDBLOG_'.$id, '', 'EASYBLOG');

			if ($crypt == $password) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Formats microblog posts. Use EB::quickpost()->getAdapter('source')->format($blog);
	 *
	 * @deprecated	4.0
	 */
	public static function formatMicroblog(&$row)
	{
		$adapter = EB::quickpost()->getAdapter($row->posttype);

		if ($adapter === false) {
			return;
		}

		$adapter->format($row);
	}

	/**
	 * Reverse of strip_tags where it only strip necessary codes.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';
		if(!is_array($tags))
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if(end($tags) == '')
			{
				array_pop($tags);
			}
		}

		foreach($tags as $tag)
		{
			if ($stripContent)
			{
				$content = '(.+</'.$tag.'[^>]*>|)';
			}
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	}

	/**
	 * Truncate's blog post with the respective settings.
	 *
	 * @access	public
	 */
	public static function truncateContent( &$row , $loadVideo = false , $frontpage = false , $loadGallery = true )
	{
		$config			= EasyBlogHelper::getConfig();
		$truncate		= true;
		$maxCharacter	= $config->get('layout_maxlengthasintrotext', 150);

		// @task: Maximum characters should not be lesser than 0
		$maxCharacter	= $maxCharacter <= 0 ? 150 : $maxCharacter;

		// Check if truncation is really necessary because if introtext is already present, just use it.
		if( !empty($row->intro) && !empty($row->content) )
		{
			// We do not want the script to truncate anything since we'll just be using the intro part.
			$truncate			= false;
		}

		// @task: If truncation is not necessary or the intro text is empty, let's just use the content.
		if( !$config->get( 'layout_blogasintrotext' ) || !$truncate )
		{

			//here we process the video and get the links.
			if( $loadVideo )
			{
				$row->intro		= EB::videos()->processVideos( $row->intro );
				$row->content	= EB::videos()->processVideos( $row->content );
			}

			// @rule: Process audio files.
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->intro );
			$row->content		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->content );

			if( ( ( $config->get( 'main_image_gallery_frontpage' ) && $frontpage ) || !$frontpage ) && $loadGallery )
			{
				$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->content , $row->created_by );

				// Process jomsocial albums
				$row->intro		= EasyBlogHelper::getHelper( 'Album' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Album' )->process( $row->content , $row->created_by );
			}

			// @task: Strip out video tags
			$row->intro		= EB::videos()->strip( $row->intro );
			$row->content	= EB::videos()->strip( $row->content );

			// @task: Strip out audio tags
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

			// @task: Strip out gallery tags
			$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

			// @task: Strip out album tags
			$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

			// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
			// @2.0: <input class="easyblog-gallery"
			// @3.5: {ebgallery:'name'}
			$row->intro			= EasyBlogHelper::removeGallery( $row->intro );
			$row->content		= EasyBlogHelper::removeGallery( $row->content );

			if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
			{
				// Need to remove images, and videos.
				$row->intro = self::strip_only( $row->intro , '<img>' );
				$row->content = self::strip_only( $row->content , '<img>' );
			}


			$row->text			= empty( $row->intro ) ? $row->content : $row->intro;

			return $row;
		}

		// @rule: If this is a normal blog post, we match them manually
		if( isset($row->posttype) && ( !$row->posttype || empty( $row->posttype ) ) )
		{
			// @rule: Try to match all videos from the blog post first.
			$row->videos		= EB::videos()->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->galleries	= EasyBlogHelper::getHelper( 'Gallery' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->audios 		= EasyBlogHelper::getHelper( 'Audio' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->albums		= EasyBlogHelper::getHelper( 'Album' )->getHTMLArray( $row->intro . $row->content );
		}

		// @task: Here we need to strip out all items that are embedded since they are now not required because they'll be truncated.
		// @task: Strip out video tags
		$row->intro		= EB::videos()->strip( $row->intro );
		$row->content	= EB::videos()->strip( $row->content );

		// @task: Strip out audio tags
		$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

		// @task: Strip out gallery tags
		$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

		// @task: Strip out album tags
		$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

		// This is the combined content of the intro and the fulltext
		$content		= $row->intro . $row->content;

		switch( $config->get( 'main_truncate_type' ) )
		{
			case 'chars':

				// Remove uneccessary html tags to avoid unclosed html tags
				$content	= strip_tags( $content );

				// Remove blank spaces since the word calculation should not include new lines or blanks.
				$content	= trim( $content );

				// @task: Let's truncate the content now.
				$row->text	= JString::substr( $content , 0 , $maxCharacter);
			break;


			case 'words':
				$tag		= false;
				$count		= 0;
				$output		= '';

				// Remove uneccessary html tags to avoid unclosed html tags
				$content		= strip_tags( $content );

				$chunks		= preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

				foreach($chunks as $piece)
				{

					if( !$tag || stripos($piece, '>') !== false )
					{
						$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
					}

					if( !$tag && trim($piece) == '' )
					{
						$count++;
					}

					if( $count > $maxCharacter && !$tag )
					{
						break;
					}

					$output .= $piece;
				}

				unset($chunks);
				$row->text	= $output;

			break;



			case 'break':
				$position	= 0;
				$matches	= array();
				$tag		= '<br';

				$matches	= array();

				do
				{
					$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

					if( $position !== false )
					{
						$matches[]	= $position;
					}
				} while( $position !== false );

				$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

				if( count( $matches ) > $maxTag )
				{
					$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 6 );
					$row->readmore	= true;
				}
				else
				{
					$row->text	= $content;
					$row->readmore	= false;
				}

			break;


			default:
				$position	= 0;
				$matches	= array();
				$tag		= '</p>';

				// @task: If configured to not display any media items on frontpage, we need to remove it here.
				if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
				{
					// Need to remove images, and videos.
					$content 	= self::strip_only( $content , '<img>' );
				}

				do
				{
					$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

					if( $position !== false )
					{
						$matches[]	= $position;
					}
				} while( $position !== false );

				// @TODO: Configurable
				$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

				if( count( $matches ) > $maxTag )
				{
					$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 4 );

					$htmlTagPattern    		= array('/\<div/i', '/\<table/i');
					$htmlCloseTagPattern   	= array('/\<\/div\>/is', '/\<\/table\>/is');
					$htmlCloseTag   		= array('</div>', '</table>');

					for( $i = 0; $i < count($htmlTagPattern); $i++ )
					{

						$htmlItem   			= $htmlTagPattern[$i];
						$htmlItemClosePattern	= $htmlCloseTagPattern[$i];
						$htmlItemCloseTag		= $htmlCloseTag[$i];

						preg_match_all( $htmlItem , strtolower( $row->text ), $totalOpenItem );

						if( isset( $totalOpenItem[0] ) && !empty( $totalOpenItem[0] ) )
						{
							$totalOpenItem	= count( $totalOpenItem[0] );

							preg_match_all( $htmlItemClosePattern , strtolower( $row->text ) , $totalClosedItem );

							$totalClosedItem	= count( $totalClosedItem[0] );

							$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

							if( $totalItemToAdd > 0 )
							{
								for( $y = 1; $y <= $totalItemToAdd; $y++ )
								{
									$row->text 	.= $htmlItemCloseTag;
								}
							}
						}
					}

					$row->readmore	= true;
				}
				else
				{
					$row->text		= $content;
					$row->readmore	= false;
				}

			break;
		}

		//var_dump($row );exit;

		if( $config->get( 'main_truncate_ellipses' ) && isset( $row->readmore) && $row->readmore )
		{
			$row->text	.= JText::_( 'COM_EASYBLOG_ELLIPSES' );
		}

		if( isset($row->posttype) && ( !$row->posttype || empty( $row->posttype ) ) )
		{
			// @task: Determine the position of media items that should be included in the content.
			$embedHTML			= '';
			$embedVideoHTML		= '';
			$imgHTML            = '';

			if( !empty( $row->galleries ) )
			{
				$embedHTML		.= implode( '' , $row->galleries );
			}

			if( !empty( $row->audios ) )
			{
				$embedHTML		.= implode( '' , $row->audios );
			}

			if( !empty( $row->videos ) )
			{
				$embedVideoHTML		= implode( '' , $row->videos );
			}

			if( !empty( $row->albums ) )
			{
				$embedHTML		.= implode( '' , $row->albums );
			}

			// @legacy fix: For users prior to 3.5
			if( ( $config->get( 'main_truncate_type' ) == 'chars' ||  $config->get( 'main_truncate_type' ) == 'words' ) && !$row->getImage() )
			{
				// Append image in the post if truncation is done by characters
				if( ($config->get( 'main_teaser_image' ) && !$frontpage ) || ( $frontpage && $config->get('main_truncate_image_position') != 'hidden' ) )
				{
					// Match images that has preview.
					$pattern		= '/<a class="easyblog-thumb-preview"(.*?)<\/a>/is';

					preg_match( $pattern , $row->intro . $row->content , $matches );

					// Legacy images that doesn't have previews.
					if( empty( $matches ) )
					{
						$pattern		= '#<img[^>]*>#i';

						preg_match( $pattern , $row->intro . $row->content , $matches );
					}



					if( !empty( $matches ) )
					{
						if( $config->get( 'main_teaser_image_align' ) == 'float-l' || $config->get( 'main_teaser_image_align') == 'float-r' )
						{
							$imgHTML    = '<div class="teaser-image clearfix ' . $config->get( 'main_teaser_image_align' ) . '" style="margin:8px;max-width:98%;">' . $matches[ 0 ] . '</div>';
						}
						else
						{
							$imgHTML	= '<div class="teaser-image clearfix" style="margin:8px;max-width:98%;text-align: ' . $config->get( 'main_teaser_image_align' ) . ' !important;">' . $matches[ 0 ] . '</div>';

						}
					}
				}
			}



			// images
			if( $config->get( 'main_truncate_image_position') == 'top' && !empty( $imgHTML ) )
			{
				$row->text	= $imgHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_image_position') == 'bottom' && !empty( $imgHTML ) )
			{
				$row->text	= $row->text . $imgHTML;
			}


			// videos
			if( $config->get( 'main_truncate_video_position') == 'top' && !empty( $embedVideoHTML) )
			{
				$row->text	= $embedVideoHTML . '<br />' . $row->text;
			}
			else if( $config->get( 'main_truncate_video_position') == 'bottom' && !empty( $embedVideoHTML) )
			{
				$row->text	= $row->text . '<br />' . $embedVideoHTML;
			}


			// @task: Prepend the other media items in the start of the blog posts.
			if( $config->get( 'main_truncate_media_position') == 'top' && !empty( $embedHTML ) )
			{
				$row->text	= $embedHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_media_position') == 'bottom' && !empty( $embedHTML) )
			{
				$row->text	.= $embedHTML;
			}
		}

		return $row;
	}

	/**
	 * This method searches for built in tags and strips them off. This should only be used
	 * when you are trying to output some data that doesn't contain html tags.
	 */
	public static function stripEmbedTags( $content )
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );

		$pattern	= array('/\{video:.*?\}/',
							'/\{"video":.*?\}/',
							'/\[embed=.*?\].*?\[\/embed\]/'
							);

		$replace    = array('','','');


		return preg_replace( $pattern , $replace , $content );
	}

	/**
	 * Requires the user to be logged in
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function requireLogin()
	{
		$my = JFactory::getUser();

		if ($my->guest || $my->block) {

			$url = EB::_('index.php?option=com_easyblog&view=login', false);

			$app = JFactory::getApplication();
			return $app->redirect($url);
		}
	}

	/**
	 * Determines if a content requires a read more link.
	 *
	 * @access	public
	 * @param 	StdClas	$row
	 */
	public static function requireReadmore( &$row )
	{
		$config 		= EasyBlogHelper::getConfig();
		$maxCharacter   = $config->get('layout_maxlengthasintrotext', 150);

		// Decide whether or not to show read more link
		$readmore		= true;

		if( $config->get( 'layout_respect_readmore' ) )
		{
			// When introtext is not empty and content is empty
			if( !empty( $row->intro ) && empty($row->content) )
			{
				if( JString::strlen( strip_tags( $row->intro ) ) > $maxCharacter && $config->get( 'layout_blogasintrotext' ) )
				{
					$readmore 		= true;
				}
				else
				{
					$readmore 		= false;
				}
			}

			// Backward compatibility, this is probably from an older version
			if( empty( $row->intro ) && !empty($row->content) )
			{
				if( JString::strlen( strip_tags( $row->content ) ) > $maxCharacter )
				{
					$readmore 		= true;
				}
				else
				{
					$readmore		= false;
				}
			}

			// New way of doing things where user explicitly set the read more line.
			if( !empty($row->intro) && !empty($row->content) )
			{
				$readmore		= true;
			}
		}

		return $readmore;
	}


	public static function triggerEvent( $event , &$row , &$params , $limitstart )
	{
		$dispatcher = JDispatcher::getInstance();
		$version	= EasyBlogHelper::getJoomlaVersion();
		$events		= array(
							'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
							'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
							'easyblog.commentCount'		=> 'onGetCommentCount',
							'prepareContent'			=> 'onContentPrepare',
							'afterDisplayTitle'			=> 'onContentAfterTitle',
							'beforeDisplayContent'		=> 'onContentBeforeDisplay',
							'afterDisplayContent'		=> 'onContentAfterDisplay',
							'beforeSave'				=> 'onContentBeforeSave'
					);

		// Need to make this behave like how Joomla category behaves.
		if( !isset( $row->catid ) )
		{
			$row->catid	= $row->category_id;
		}

		if (isset($events[$event])) {
			$result = $dispatcher->trigger($events[$event] , array( 'easyblog.blog' , &$row , &$params , $limitstart ) );
		} else {
			return false;
		}

		// Remove unwanted fields.
		unset($row->catid);

		return $result;
	}

	/**
	 * Retrieves plain links (Non SEF)
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getExternalLink($link, $xhtml = false)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EBR::_( $link, $xhtml , null, true ), '/');
	}

	/**
	 *
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isFeatured($type, $id)
	{
		$model 	= EB::model('Featured');
		return $model->isFeatured($type, $id);
	}

	/**
	 * Allows caller to upload a user's avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function uploadAvatar($profile, $isFromBackend = false, $file = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();

		$acl		= EB::acl();

		if (!$isFromBackend) {

			if (! $acl->get('upload_avatar')) {
				$url	= 'index.php?option=com_easyblog&view=dashboard&layout=profile';
				EB::info()->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'error');
				$mainframe->redirect(EBR::_($url, false));
			}
		}
		$avatar_config_path	= $config->get('main_avatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;

		if (!$file) {
			$file				= JRequest::getVar('Filedata', '', 'files', 'array' );
		}

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					EB::info()->set( JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false) );
				}
				else
				{
					//from backend
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=users', false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . DIRECTORY_SEPARATOR . 'index.html' ) )
				{
					$targetFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
					$destFile	= $upload_path . DIRECTORY_SEPARATOR .'index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $my->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));
			$isNew					= false;

			if (EB::image()->canUpload($file, $err)) {

				if (!$isFromBackend) {
					EB::info()->set( JText::_( $err ) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				} else {
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					EB::info()->set( $file['error'] , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= $profile->avatar;
			$tempAvatar	= '';

			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					EB::info()->set( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					EB::info()->set( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= EBLOG_AVATAR_LARGE_WIDTH;
			$configImageHeight	= EBLOG_AVATAR_LARGE_HEIGHT;

			$image 	= EB::simpleimage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				//if(JFile::exists( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar ))
				if(JFile::exists( $tempAvatar ))
				{
					//JFile::delete( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar );
					JFile::delete( $tempAvatar );
				}
			}

			if($isNew && !$isFromBackend)
			{
				if( $my->id != 0 && $config->get('main_jomsocial_userpoint') )
				{
					$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
					if( JFile::exists( $jsUserPoint ) )
					{
						require_once( $jsUserPoint );
						CUserPoints::assignPoint( 'com_easyblog.avatar.upload' , $my->id );
					}
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return 'default_blogger.png';
		}

	}

	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return EasyBlogHelper::uploadMediaAvatar( 'category', $category, $isFromBackend);
	}

	public static function uploadTeamAvatar( $team, $isFromBackend = false )
	{
		return EasyBlogHelper::uploadMediaAvatar( 'team', $team, $isFromBackend);
	}

	public static function uploadMediaAvatar( $mediaType, $mediaTable, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config 	= EB::config();
		$acl 		= EB::acl();


		// required params
		$layout_type			= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type				= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type	= ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';



		if (!$isFromBackend && $mediaType == 'category') {
			if(! $acl->get('upload_cavatar')) {

				$url  = 'index.php?option=com_easyblog&view=dashboard&layout='.$layout_type;
				EB::info()->set( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
				$mainframe->redirect(EBR::_($url, false));
			}
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path)) {

			if(! JFolder::create( $upload_path )) {

				// Redirect
				if(! $isFromBackend) {
					EB::info()->set( JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false) );
				} else {
					//from backend
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view='.$layout_type, false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
		}

		//makesafe on the file
		$file['name']	= $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));
			$isNew					= false;

			if (!EB::image()->canUpload($file, $error)) {
				if(! $isFromBackend)
				{
					EB::info()->set( JText::_( $err ) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					EB::info()->set( $file['error'] , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != $default_avatar_type)
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					EB::info()->set( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					EB::info()->set( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= EBLOG_AVATAR_LARGE_WIDTH;
			$configImageHeight	= EBLOG_AVATAR_LARGE_HEIGHT;

			$image 	= EB::simpleimage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != $default_avatar_type) {

				if(JFile::exists($tempAvatar)) {
					JFile::delete($tempAvatar);
				}
			}

			return JFile::makeSafe($file['name']);
		} else {
			return $default_avatar_type;
		}

	}

	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		foreach ($modules as $module) {
			$theme = EB::template();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/modules/item');
		}

		return $buffer;
	}

	/**
	 * Loads the default languages for EasyBlog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function loadLanguages($path = JPATH_ROOT)
	{
		static $loaded = array();

		if (!isset($loaded[$path])) {
			$lang = JFactory::getLanguage();

			// Load site's default language file.
			$lang->load('com_easyblog', $path);

			$loaded[$path] = true;
		}

		return $loaded[$path];
	}

	/**
	 * Loads module stylesheet
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function loadModuleCss()
	{
		static $loaded	= false;

		if (!$loaded) {
			$theme = EB::config()->get('theme_site');
			$stylesheet = EB::stylesheet('site', $theme);
			$stylesheet->attach();

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Given a username, retrieve the user's id.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUserId($username)
	{
		$db = EB::db();

		$query	= 'SELECT `id` FROM `#__easyblog_users` WHERE `permalink`=' . $db->Quote($username);
		$db->setQuery( $query );
		$result	= $db->loadResult();

		if (empty($result)) {
			$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->Quote( $username );
			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		return $result;
	}

	/**
	 * Alternative to setMeta
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setMetaData($keywords, $description)
	{

		if (!$keywords || !$description) {
			$menu = JFactory::getApplication()->getMenu()->getActive();
			$params = new JRegistry($menu->params);

			if (!$keywords) {
				$keywords = $params->get('menu-meta_keywords', '');
			}

			if (!$description) {
				$description = $params->get('menu-meta_description', '');
			}
		}

		if ($keywords) {
			$doc->setMetadata('keywords', $keywords);
		}

		if ($description) {
			$doc->setMetadata('description', $description);
		}
	}

	/**
	 * Allows caller to set the meta
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setMeta($id, $type, $defaultViewDesc = '')
	{
		$doc = JFactory::getDocument();
		$config = EB::config();

		// Try to load the meta for the content
		$meta = EB::table('Meta');
		$meta->load(array('type' => $type, 'content_id' => $id));

		// If the category was created without any meta, we need to automatically fill in the description
		if ($type == META_TYPE_CATEGORY && !$meta->id) {

			$category = '';
			if (EB::cache()->exists($id, 'category')) {
				$category = EB::cache()->get($id, 'category');
			} else {
				$category = EB::table('Category');
				$category->load($id);
			}

			$doc->setMetadata('description', strip_tags($category->description));
		}

		// If the blogger was created, try to get meta from blogger biography/title
		if ($type == META_TYPE_BLOGGER) {

			$author = '';
			if (EB::cache()->exists($id, 'author')) {
				$author = EB::cache()->get($id, 'author');
			} else {
				$author = EB::table('Profile');
				$author->load($id);
			}

			$doc->setMetadata('description', strip_tags($author->biography));

			if (!empty($author->biography) || !empty($author->title)) {
				$meta = new stdClass();
				$meta->keywords = $author->title;
				$meta->description = EB::string()->escape($author->biography);
			}
		}

		// Automatically fill meta keywords
		if ($type == META_TYPE_POST && (($config->get('main_meta_autofillkeywords') && empty($meta->keywords) )|| ($config->get( 'main_meta_autofilldescription')))) {

			// Retrieve data from cache
			$post = EB::post();
			$post->load($id);

			$category = $post->getPrimaryCategory();
			$keywords = array($category->getTitle());

			if ($config->get('main_meta_autofillkeywords') && empty($meta->keywords)) {

				$tags = $post->getTags();

				foreach ($tags as $tag) {
					$keywords[] = $tag->getTitle();
				}

				$meta->keywords = implode(',', $keywords);
			}

			// Automatically fill meta description
			if ($config->get( 'main_meta_autofilldescription' ) && empty($meta->description)) {

				$content = $post->getIntro(EASYBLOG_STRIP_TAGS);
				$content = strip_tags($content);

				// Remove newlines
				$content = str_ireplace("\n", "", $content);
				$content = trim($content);

				// Set description into meta headers
				$meta->description	= JString::substr($content , 0 , $config->get( 'main_meta_autofilldescription_length'));
				$meta->description	= EB::string()->escape($meta->description);
			}

			// Remove JFBConnect codes.
			if ($meta->description) {
				$pattern = '/\{JFBCLike(.*)\}/i';
				$meta->description = preg_replace($pattern , '' , $meta->description);
			}
		}

		// Check if the descriptin or keysword still empty or not. if yes, try to get from joomla menu.
		if (empty($meta->description) && empty($meta->keywords)) {
			$active = JFactory::getApplication()->getMenu()->getActive();

			if ($active) {
				$params = $active->params;

				$description = $params->get('menu-meta_description', '');
				$keywords = $params->get('menu-meta_keywords', '');

				if (!empty($description) || !empty($keywords)) {
					$meta = new stdClass();
					$meta->description = EB::string()->escape($description);
					$meta->keywords = $keywords;
				}
			}
		}

		if (!$meta) {
			return;
		}

		// If there's no meta description, try to get it from Joomla's settings
		if (!$meta->description && $defaultViewDesc) {
			$meta->description = $defaultViewDesc . ' - ' . EB::jconfig()->get('MetaDesc');
		}

		if ($meta->keywords) {
			$doc->setMetadata('keywords', $meta->keywords);
		}

		if ($meta->description) {
			$doc->setMetadata('description', $meta->description);
		}

		// Admin probably disabled indexing
		if (isset($meta->indexing) && !$meta->indexing) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

	}

	public static function getLikesAuthors($contentId, $type, $userId)
	{
		$db		= EB::db();
		$config	= EB::getConfig();

		$result = new stdClass();

		$displayFormat  = $config->get('layout_nameformat');
		$displayName    = '';

		switch($displayFormat){
			case "name" :
				$displayName = 'a.name';
				break;
			case "username" :
				$displayName = 'a.username';
				break;
			case "nickname" :
			default :
				$displayName = 'b.nickname';
				break;
		}

		$query	= 'select a.id as `user_id`, c.id, ' . $displayName . ' as `displayname`';
		$query	.= ' FROM `#__users` as a';
		$query	.= '  inner join `#__easyblog_users` as b';
		$query	.= '    on a.id = b.id';
		$query	.= '  inner join `#__easyblog_likes` as c';
		$query	.= '    on a.id = c.created_by';
		$query	.= ' where c.content_id = ' . $db->Quote($contentId);
		$query	.= ' and c.`type` = '. $db->Quote($type);
		$query	.= ' order by c.id desc';

		$db->setQuery($query);
		$list   = $db->loadObjectList();

		if (count($list) <= 0) {

			$result->string = '';
			$result->count = 0;

			return $result;
		}

		// else continue here
		$onwerInside = false;

		$names	= array();
		for ($i = 0; $i < count($list); $i++) {

			if ($list[$i]->user_id == $userId) {
				$onwerInside	= true;
				array_unshift($names, JText::_('COM_EASYBLOG_YOU') );
			} else {
				$names[]	= $list[$i]->displayname;
			}
		}

		$max	= 3;
		$total	= count($names);
		$break	= 0;

		if ($total == 1) {
			$break	= $total;
		} else {

			if ($max >= $total) {
				$break	= $total - 1;
			} elseif($max < $total) {
				$break	= $max;
			}
		}

		$main	= array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront	= implode(", ", $main);
		$returnString	= '';

		if(count($remain) > 1)
		{
			$returnString	= JText::sprintf('COM_EASYBLOG_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));
		}
		else if(count($remain) == 1)
		{
			$returnString	= JText::sprintf('COM_EASYBLOG_AND_LIKE_THIS', $stringFront, $remain[0]);
		}
		else
		{
			if( EasyBlogHelper::isLoggedIn() && $onwerInside )
			{
				$returnString	= JText::sprintf('COM_EASYBLOG_LIKE_THIS_SINGULAR', $stringFront);
			}
			else
			{
				$returnString	= JText::sprintf('COM_EASYBLOG_LIKE_THIS_PLURAL', $stringFront);
			}
		}

		$result->count = $total;
		$result->string = $returnString;

		return $result;
	}

	/**
	 * Given a page title, this method would try to find any existing menu items that are tied to the current page view.
	 * * If a page title is tied, it will then use the page title defined in the menu.
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPageTitle($default = '')
	{
		$config = EB::config();
		$app = JFactory::getApplication();
		$itemid = $app->input->get('Itemid', '');
		$originalTitle = $default;

		// @task: If we can't detect the item id, just return the default page title that was passed in.
		if (!$itemid) {
			return $default;
		}

		// Prepare Joomla's site title if necessary.
		$jConfig = EB::jConfig();
		$addTitle = $jConfig->get('sitename_pagetitles');

		// Only add Joomla's site title if it was configured to.
		if ($addTitle) {
			$siteTitle = $jConfig->get('sitename');

			// If the user does not want to append anything
			if (!$config->get('main_pagetitle_autoappend')) {
				$default = $siteTitle;
			} else {
				if ($addTitle == 1) {
					$default = $siteTitle . ' - ' . $default;
				}

				if ($addTitle == 2) {
					$default = $default . ' - ' . $siteTitle;
				}
			}
		}

		// @task: Let's find the menu item.
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($itemid);

		// @task: If configured to not append the blog title on the page, do not set any page title.
		if (!$config->get('main_pagetitle_autoappend') && $default == $config->get('main_title')) {
			$default 	= '';
			return $default;
		}

		// @task: If menu item cannot be found anywhere, just use the default
		if (!$item) {
			// @task: If default item is not empty just return the page title.
			return $default;
		}

		// @task: Let's get the page title from the menu.
		$title = $item->params->get('page_title', '');

		// @task: If a title is found, just use the configured title.
		if ($title) {
			return $title;
		}

		return $default;
	}

	// this function used to show the login form
	public static function showLogin($return='')
	{
		$my = JFactory::getUser();

		if ($my->id == 0) {
			$comUserOption	= 'com_users';
			$tasklogin		= 'user.login';
			$tasklogout		= 'user.logout';
			$viewRegister	= 'registration';
			$inputPassword	= 'password';

			if (empty($return)) {
				$currentUri = JRequest::getURI();
				$uri		= base64_encode($currentUri);
			} else {
				$uri		= $return;
			}


			$theme 	= EB::template();
			$theme->set( 'return' , $uri );

			$theme->set( 'comUserOption' , $comUserOption );
			$theme->set( 'tasklogin' , $tasklogin );
			$theme->set( 'tasklogout' , $tasklogout );
			$theme->set( 'viewRegister' , $viewRegister );
			$theme->set( 'inputPassword' , $inputPassword );

			echo $theme->output('site/login/default');
		}
	}


	public static function getThemeObject( $name )
	{
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );

		$file	= EBLOG_THEMES . '/' . $name . '/config.xml';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		$parser		= JFactory::getXML($file);

		$obj = new stdClass();
		$obj->element = $name;
		$obj->name = $name;
		$obj->path = $file;
		$obj->writable = is_writable($file);
		$obj->created = JText::_( 'Unknown' );
		$obj->updated = JText::_( 'Unknown' );
		$obj->author = JText::_( 'Unknown' );
		$obj->version = JText::_( 'Unknown' );
		$obj->desc = JText::_( 'Unknown' );

		if (EB::isJoomla30()) {

			$childrens		= $parser->children();

			foreach ($childrens as $key => $value) {
				if ($key == 'description') {
					$key = 'desc';
				}

				$obj->$key 	= (string) $value;
			}

			$obj->path = $file;
		} else {

			$contents = JFile::read( $file );

			$parser 	= JFactory::getXMLParser('Simple');
			$parser->loadString( $contents );

			$created = $parser->document->getElementByPath( 'created' );
			if ($created) {
				$obj->created = $created->data();
			}

			$updated = $parser->document->getElementByPath( 'updated' );
			if ($updated) {
				$obj->updated = $updated->data();
			}

			$author = $parser->document->getElementByPath( 'author' );
			if ($author) {
				$obj->author = $author->data();
			}

			$version = $parser->document->getElementByPath( 'version' );
			if ($version) {
				$obj->version = $version->data();
			}

			$description = $parser->document->getElementByPath( 'description' );
			if ($description)
			{
				$obj->desc = $description->data();
			}

			$obj->path = $file;
		}

		return $obj;
	}

	public static function getThemeInfo( $name )
	{
		jimport( 'joomla.filesystem.file' );

		$mainframe	= JFactory::getApplication();

		$file =	'';

		// We need to specify if the template override folder also have config.ini file
		if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini';
		}

		// then check the current theme folder
		elseif ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog'. DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini';
		}

		if( !empty( $file ) )
		{
			$raw		= JFile::read( $file );
			$registry	= EB::registry($raw);

			return $registry;
		}
		return EB::registry();
	}


	/**
	 * Generates a html code for category selection.
	 *
	 * @access	public
	 * @param	int		$parentId	if this option spcified, it will list the parent and all its childs categories.
	 * @param	int		$userId		if this option specified, it only return categories created by this userId
	 * @param	string	$outType	The output type. Currently supported links and drop down selection
	 * @param	string	$eleName	The element name of this populated categeries provided the outType os dropdown selection.
	 * @param	string	$default	The default selected value. If given, it used at dropdown selection (auto select)
	 * @param	boolean	$isWrite	Determine whether the categories list used in write new page or not.
	 * @param	boolean	$isPublishedOnly	If this option is true, only published categories will fetched.
	 * @param	array 	$exclusion	A list of excluded categories that it should not be including
	 */

	public static function populateCategories($parentId , $userId , $outType , $eleName , $default , $isWrite = false , $isPublishedOnly = false , $isFrontendWrite = false , $exclusion = array(), $attributes = '')
	{
		$catModel	= EB::model('Category');

		$parentCat	= null;

		if (!empty($userId)) {
			$parentCat	= $catModel->getParentCategories($userId, 'blogger', $isPublishedOnly, $isFrontendWrite , $exclusion );
		} else if(! empty($parentId)) {
			$parentCat	= $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $isFrontendWrite , $exclusion );
		} else {
			$parentCat	= $catModel->getParentCategories('', 'all', $isPublishedOnly, $isFrontendWrite , $exclusion );
		}

		$ignorePrivate	= false;

		switch($outType)
		{
			case 'link' :
				$ignorePrivate	= false;
				break;
			case 'popup':
			case 'select':
			default:
				$ignorePrivate	= true;
				break;
		}

		// Now let's do a loop to find it's child categories.
		if(! empty($parentCat))
		{
			for($i = 0; $i < count($parentCat); $i++)
			{
				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				EasyBlogHelper::buildNestedCategories( $parent->id, $parent, $ignorePrivate, $isPublishedOnly, $isFrontendWrite , $exclusion );
			}
		}

		if ($isWrite) {
			$defaultCatId	= EasyBlogHelper::getDefaultCategoryId();
			$default		= ( empty( $default ) ) ? $defaultCatId : $default;
		}

		$formEle		= '';

		if( $outType == 'select' && $isWrite )
		{
			$selected	= !$default ? ' selected="selected"' : '';
			$formEle	.= '<option value="0"' . $selected . '>' . JText::_( 'COM_EASYBLOG_SELECT_A_CATEGORY' ) . '</option>';
		}

		if( $parentCat )
		{
			foreach($parentCat as $category)
			{
				if($outType == 'popup')
				{
					$formEle	.= '<div class="category-list-item" id="'.$category->id.'"><a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $category->id. '\')">' .$category->title . '</a>';
					$formEle	.= '<input type="hidden" id="category-list-item-'.$category->id.'" value="'.$category->title.'" />';
					$formEle	.= '</div>';
				}
				else
				{
					$selected	= ($category->id == $default) ? ' selected="selected"' : '';
					$formEle	.= '<option value="'.$category->id.'" ' . $selected. '>' . JText::_( $category->title ) . '</option>';
				}

				EasyBlogHelper::accessNestedCategories($category, $formEle, '0', $default, $outType);
			}
		}

		$html	= '';
		$html	.= '<select name="' . $eleName . '" id="' . $eleName .'" class="form-control" ' . $attributes . '>';
		if(! $isWrite)
			$html	.=	'<option value="0">' . JText::_('COM_EASYBLOG_SELECT_PARENT_CATEGORY') . '</option>';
		$html	.=	$formEle;
		$html	.= '</select>';

		return $html;
	}


	public static function buildNestedCategories($parentId, &$parent, $ignorePrivate = false, $isPublishedOnly = false, $isWrite = false , $exclusion = array() )
	{
		$my = JFactory::getUser();

		$childs = array();

		//lets try to get from cache if there is any
		if (EB::cache()->exists($parentId, 'cats')) {
			$data = EB::cache()->get($parentId, 'cats');

			if (isset($data['child'])) {
				$childs = $data['child'];
			} else {
				return false;
			}
		} else {
			$catModel = EB::model( 'Categories');
			$childs = $catModel->getChildCategories($parentId, $isPublishedOnly, $isWrite , $exclusion );
		}

		if (!$childs) {
			return false;
		}

		$items = array();

		foreach($childs as $child) {
			$items[$child->id] = $child;
		}

		$parent->childs = array();

		$catLib = EB::category();
		$catLib::addChilds($parent, $items);

		return false;
	}


	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '')
	{
		if (isset($arr->childs) && is_array($arr->childs)) {
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'popup') {
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child	= $arr->childs[$j];

				$cat = EB::table('Category');
				$cat->bind($child);

				if($type == 'select') {
					$selected	= ($child->id == $default) ? ' selected="selected"' : '';

					$html	.= '<option value="'.$child->id.'" ' . $selected . '>' . $space . $sup . JText::_($child->title)  . '</option>';
				} else if($type == 'popup') {
					$html	.= '<div class="category-list-item" id="'.$child->id.'">' . $space . $sup . '<a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $child->id. '\')">' . JText::_($child->title) . '</a>';
					$html	.= '<input type="hidden" id="category-list-item-'.$child->id.'" value="'. JText::_($child->title) .'" />';
					$html	.= '</div>';
				} else {
					$str	= '<a href="' . $cat->getPermalink() . '">' . $cat->getTitle() . '</a>';
					$html	.= (empty($html)) ? $str : $ld . $str;
				}

				EB::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter);
			}
		}

		return false;
	}



	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs)) {

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child		= $arr->childs[$j];
				$newArr[]	= $child->id;
				EB::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 * function to retrieve the linkage backward from a child id.
	 * return the full linkage from child up to parent
	 */

	public static function populateCategoryLinkage($childId)
	{
		$arr		= array();
		$category	= EB::table('Category');
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]		= $obj;

		if((!empty($category->parent_id)))
		{
			EasyBlogHelper::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr		= array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= EB::table('Category');
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;



		$arr[]		= $obj;

		if((!empty($category->parent_id)))
		{
			EasyBlogHelper::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Get post title by ID
	 */
	public static function getPostTitle($id)
	{
		$db = EasyBlogHelper::db();

		$query = 'SELECT ' . $db->nameQuote('title') . ' FROM ' . $db->nameQuote('#__easyblog_post') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function storeSession($data, $key, $ns = 'COM_EASYBLOG')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'COM_EASYBLOG')
	{
		$data	= null;
		$mySess	= JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		}
		else
		{
			return $data;
		}
	}

	public static function clearSession($key, $ns = 'COM_EASYBLOG')
	{
		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$mySess->clear($key, $ns);
		}
		return true;
	}

	public static function ajax()
	{
		static $ajax = null;

		if (!$ajax) {

			require_once(__DIR__ . '/ajax/ajax.php');

			$ajax = new EasyBlogAjax();
		}

		return $ajax;
	}

	public static function isTeamBlogJoined($userId, $teamId)
	{
		$teamIds	= EasyBlogHelper::getViewableTeamIds($userId);
		return in_array($teamId, $teamIds);
	}

	/**
	 * Disallow users to view the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function showAccessDenied($type='', $access = '0')
	{
		$message = JText::_('COM_EASYBLOG_SORRY_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW');

		if ($type == 'teamblog' && $access == '1') {
			$message = JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY');
		}

		$theme = EB::template();
		$theme->set('message', $message);

		$result = $theme->output('site/blogs/access.denied');

		return $result;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];


		return $jVersion;
	}

	public static function isJoomla31()
	{
		return EasyBlogHelper::getJoomlaVersion() >= '3.1';
	}

	public static function isJoomla30()
	{
		return EasyBlogHelper::getJoomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return EasyBlogHelper::getJoomlaVersion() >= '1.6' && EasyBlogHelper::getJoomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return EasyBlogHelper::getJoomlaVersion() == '1.5';
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */

	public static function getSAUsersIds()
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[]  = $group;
			}
		}


		//now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	public static function getDefaultSAIds()
	{
		$saUserId = '62';

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= EasyBlogHelper::getSAUsersIds();
			$saUserId	= $saUsers[0];
		}

		return $saUserId;
	}

	public static function getFirstImage( $content )
	{
		//try to search for the 1st img in the blog
		$img		= '';
		$pattern	= '#<img[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			$img	= $matches[0];
		}


		//image found. now we process further to get the absolute image path.
		if(! empty($img) )
		{
			//get the img src

			$pattern = '/src\s*=\s*"(.+?)"/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath	= $matches[1];
				$imgSrc		= EB::image()->rel2abs($imgPath, JURI::root());

				return $imgSrc;
			}
		}

		return false;
	}

	public static function getBloggerTheme()
	{
		$id		= EasyBlogRouter::isBloggerMode();

		if( empty( $id ) )
		{
			return false;
		}

		$profile = EB::user($id);

		$userparams	= EB::registry($profile->params);

		$userTheme = $userparams->get('theme', false);

		return (!$userTheme || $userTheme == 'global') ? false : $userTheme;
		// return $userparams->get('theme', false);
	}

	public static function getFeaturedImage( $content )
	{
		$pattern = '#<img class="featured[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If all else fail, try to use the default image
		return false;
	}

	public static function getJoomlaUserGroups( $cid = '' )
	{
		$db = EasyBlogHelper::db();

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		}
		else
		{
			$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
		}

		// condition
		$where  = array();

		// we need to filter out the ROOT and USER dummy records.
		if(EasyBlogHelper::getJoomlaVersion() < '1.6')
		{
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query .= $where;

		// grouping and ordering
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$query 	.= ' ORDER BY a.id';
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getUserGids( $userId = '' )
	{
		$user	= '';

		if( empty($userId) )
		{
			$user	= JFactory::getUser();
		}
		else
		{
			$user	= JFactory::getUser($userId);
		}

		$grpId		= array();

		if ($user->id == 0) {
			$grpId	= JAccess::getGroupsByUser(0, false);
		} else {
			$grpId	= JAccess::getGroupsByUser($user->id, false);
		}

		if( empty($grpId) )
		{
			//this case shouldn't happen but it happened. sigh.
			$grpId[] = '1';
		}

		return $grpId;

	}

	/**
	 * Retrieves Joomla's cache object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCache()
	{
		$jconfig = EB::jconfig();

		$options = array(
			'defaultgroup'	=> '',
			'storage' 		=> $jconfig->get('cache_handler', ''),
			'caching'		=> true,
			'cachebase'		=> $jconfig->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('', $options);

		return $cache;
	}



	public static function getAccessibleCategories( $parentId = 0 )
	{
		$db			= EasyBlogHelper::db();
		$my			= JFactory::getUser();

		$gids		= '';
		$catQuery	= 	'select distinct a.`id`, a.`private`';
		$catQuery	.=  ' from `#__easyblog_category` as a';
		$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0');

		if( $my->id != 0 )
		{
			$catQuery	.=  ' OR a.`private` = ' . $db->Quote('1');
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$gid	= array();
			if( $my->id == 0 )
			{
				$gid	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid	= JAccess::getGroupsByUser($my->id, false);
			}

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$catQuery	.= ' OR a.`id` IN (';
			$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
			$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
			$catQuery	.= ')';
		}
		else
		{
			$gid    = array();

			if( $my->id == 0 )
			{
				$gid[] = '0';
			}
			else
			{
				$gid	= EasyBlogHelper::getUserGids();
			}

			$gid	= EasyBlogHelper::getUserGids();


			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}


				$catQuery	.= ' OR a.`id` IN (';
				$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
				$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
				$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
				$catQuery	.= ')';
			}

		}

		if( $parentId )
		{
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);
		}

		$db->setQuery($catQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getPrivateCategories()
	{
		$db				= EasyBlogHelper::db();
		$my				= JFactory::getUser();
		$excludeCats	= array();

		$catQuery = '';
		// get all private categories id
		if($my->id == 0)
		{
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__easyblog_category` as a';
			$catQuery	.=	' 	left join `#__easyblog_category_acl` as b on a.`id` = b.`category_id` and b.`acl_id` = ' . $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

			$gid	= array();
			$gids	= '';

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
			{
				$gid	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid	= EasyBlogHelper::getUserGids();
			}

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
				$catQuery	.= ' and b.`category_id` NOT IN (';
				$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
				$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
				$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
			}

		}
		else
		{
			$gid	= EasyBlogHelper::getUserGids();
			$gids   = '';
			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$catQuery = 'select id from `#__easyblog_category` as a';
			$catQuery .= ' where not exists (';
			$catQuery .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$catQuery .= '			where b.category_id = a.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery .= '			and b.type = ' . $db->Quote('group');
			$catQuery .= '			and b.content_id IN (' . $gids . ')';
			$catQuery .= '      )';
			$catQuery .= ' and a.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		}

		if( !empty( $catQuery ) )
		{

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();

			for($i=0; $i < count($result); $i++)
			{
				$item			=& $result[$i];
				$item->childs	= null;

				EasyBlogHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				EasyBlogHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}
		}

		return $excludeCats;
	}

	public static function getViewableTeamIds($userId = '')
	{

		$db	= EasyBlogHelper::db();
		$my	= '';

		if( empty($userId) )
		{
			$my = JFactory::getUser();
		}
		else
		{
			$my = JFactory::getUser($userId);
		}

		$teamBlogIds = '';


		if( $my->id == 0)
		{
			//get team id with access == 3
			$query	= 'select `id` FROM `#__easyblog_team` where `access` = ' . $db->Quote( '3' );
			$query	.= ' and `published` = ' . $db->Quote( '1' );
			$db->setQuery($query);

			$result	= $db->loadResultArray();
			return $result;
		}
		else
		{

			$gid	= EasyBlogHelper::getUserGids( $userId );
			$gids	= '';
			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			// get the teamid from this user.
			$query	= 'select distinct `id` from `#__easyblog_team` as t left join `#__easyblog_team_users` as tu on t.id = tu.team_id';
			$query	.= ' left join `#__easyblog_team_groups` as tg on t.id = tg.team_id';
			$query	.= ' where t.`published` = ' . $db->Quote( '1' );
			$query	.= ' and (tu.`user_id` = ' . $db->Quote( $my->id );
			$query	.= ' OR t.`access` IN (2, 3)';
			$query	.= ' OR tg.group_id IN (' . $gids . ')';
			$query	.= ')';

			$db->setQuery($query);

			$result = $db->loadResultArray();
			return $result;
		}

	}

	public static function getDefaultCategoryId()
	{
		$db = EasyBlogHelper::db();

		$gid	= EasyBlogHelper::getUserGids();
		$gids	= '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query	= 'SELECT a.id';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote( '1' );
		$query	.= ' AND a.`default` = ' . $db->Quote( '1' );
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		$query	.= ' ORDER BY a.`lft` LIMIT 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty( $result ) ) ? '0' : $result ;
	}

	public static function isBlogger( $userId )
	{
		if( empty( $userId ) )
			return false;

		$acl = EB::acl($userId);
		if ($acl->get('add_entry')) {
			return true;
		} else {
			return false;
		}

	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while( JFile::exists($path.DIRECTORY_SEPARATOR.$uniqueFilename.$ext) )
		{
			// $uniqueFilename	= JFile::stripExt($originalFilename) . '-' . $i;
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . EB::date()->toFormat( "%Y%m%d-%H%M%S" );
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	/**
	 * Retrieves the current language
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCurrentLanguage()
	{
		static $language = null;

		if (JFactory::getApplication()->isAdmin()) {
			return false;
		}

		if (is_null($language)) {

			$language = false;

			// When language filter is enabled, we need to detect the appropriate contents
			$multiLanguage = JFactory::getApplication()->getLanguageFilter();

			if ($multiLanguage) {
				$language = JFactory::getLanguage()->getTag();
			}
		}

		return $language;
	}

	public static function getCategoryMenuBloggerId()
	{
		$itemId			= JRequest::getInt('Itemid', 0);
		$menu			= JFactory::getApplication()->getMenu();
		$menuparams		= $menu->getParams( $itemId );
		$catBloggerId	= $menuparams->get('cat_bloggerid', '');

		return $catBloggerId;
	}

	/**
	 * Adds canonical URL to satisfy google bots in case they think that it's a duplicated content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri = JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		jimport('joomla.filter.filterinput');
		$inputFilter	= JFilterInput::getInstance();
		$preferredURL	= $inputFilter->clean($preferredURL, 'string');

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}

	public static function addScriptDeclaration( $code='' )
	{
		return '<script type="text/javascript">EasyBlog.ready(function($){' . $code . '});</script>';
	}

	public static function getUnsubscribeLink($subdata, $external=false, $isAllType = false, $email = '')
	{
		$easyblogItemId	= EasyBlogRouter::getItemId( 'latest' );

		if ($isAllType && $email) {

			$types = array();
			$ids = array();
			$created = array();

			foreach($subdata as $type => $id) {
				$types[] = $type;

				$tmpId = explode('|', $id);
				$ids[] = $tmpId[0];
				$created[] = $tmpId[1];
			}

			$stype = implode(',', $types);
			$sid = implode(',', $ids);
			$screated = implode(',', $created);

			$unsubdata		= base64_encode("type=".$stype."\r\nsid=".$sid."\r\nuid=".$email."\r\ntoken=".$screated);

		} else {
			$unsubdata		= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->user_id."\r\ntoken=".md5($subdata->id.$subdata->created));
		}

		return EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&task=subscription.unsubscribe&data='.$unsubdata.'&Itemid=' . $easyblogItemId, false, $external);
	}

	/**
	 * Retrieves the editor
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getEditor()
	{
		$config = EB::config();

		// If configured the respect author's editor, pickup the correct editor.
		$useAuthorEditor = $config->get('layout_editor_author');

		if ($useAuthorEditor) {
			$my = JFactory::getUser();

			$registry = new JRegistry($my->params);
			$editor = JFactory::getEditor($registry->get('editor', $config->get('layout_editor')));

			return $editor;
		}

		// If use system editor, we should check if the configured editor exists or enabled.
		$selectedEditor = $config->get('layout_editor');

		// Test if the plugin is enabled
		$enabled = JPluginHelper::isEnabled('editors', $selectedEditor);

		// If the editor isn't enabled, we need to intelligently find one that is enabled.
		if (!$enabled) {

			$model = EB::model('Settings');
			$randomEditor = $model->getAvailableEditor();

			if (!$randomEditor) {
				// No editors enabled on the site. WTF?
				EB::info()->set(JText::_('COM_EASYBLOG_NO_EDITORS_ENABLED_ON_SITE'), 'error');

				return false;
			}

			// Use the random enabled editor
			$selectedEditor = $randomEditor;

			// Show some error message that the configured editor isn't available.
			EB::info()->set(JText::sprintf('COM_EASYBLOG_SELECTED_EDITOR_NOT_ENABLED', $selectedEditor, $randomEditor), 'error');
		}

		$editor = JFactory::getEditor($selectedEditor);

		return $editor;
	}

	public static function getEditProfileLink()
	{
		$default 	= EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile');
		$config 	= EasyBlogHelper::getConfig();

		if( $config->get( 'integrations_easysocial_editprofile' ) )
		{
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

			if( $easysocial->exists() )
			{
				$default 	= FRoute::profile( array( 'layout' => 'edit' ) );
			}
		}

		return $default;
	}

	/**
	 * Determines if user registration is enabled in Joomla
	 *
	 * @since	5.0.18
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isRegistrationEnabled()
	{
		$params = JComponentHelper::getParams('com_users');

		if ($params->get('allowUserRegistration')) {
			return true;
		}

		return false;
	}

	public static function getRegistrationLink()
	{
		$config = EasyBlogHelper::getConfig();
		$default = JRoute::_( 'index.php?option=com_users&view=registration' );

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':
				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::registration();
				}
				else
				{
					$link 	= $default;
				}

				break;

			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=registers' );
				break;
			break;

			case 'joomla':

				$link 	= $default;

			break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
			break;
		}

		return $link;
	}

	public static function getLoginLink( $returnURL = '' )
	{
		$config 	= EasyBlogHelper::getConfig();

		if( !empty( $returnURL ) )
		{
			$returnURL	= '&return=' . $returnURL;
		}

		$default 	= EBR::_( 'index.php?option=com_user&view=login' . $returnURL );


		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default 	= EBR::_('index.php?option=com_users&view=login' . $returnURL );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::login();
				}
				else
				{
					$link 	= $default;
				}

			break;

			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=login' . $returnURL);
				break;
			break;

			case 'joomla':
			case 'jomsocial':
				$link 	= $default;
			break;
		}

		return $link;
	}

	public static function getResetPasswordLink()
	{
		$config		= EasyBlogHelper::getConfig();
		$default	= JRoute::_( 'index.php?option=com_user&view=reset' );

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=reset' );
		}


		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::account( array( 'layout' => 'forgetPassword' ) );
				}
				else
				{
					$link 	= $default;
				}

			break;

			case 'cb':
				$link 		= JRoute::_( 'index.php?option=com_comprofiler&task=lostpassword' );
			break;

			case 'joomla':
			case 'jomsocial':

				$link 	= $default;
			break;
		}

		return $link;
	}

	public static function getRemindUsernameLink()
	{
		$config 	= EasyBlogHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=remind' );

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=remind' );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::account( array( 'layout' => 'forgetUsername' ) );
				}
				else
				{
					$link 	= $default;
				}

			break;

			default:

				$link 	= $default;

			break;
		}

		return $link;
	}

	public static function log( $var = '', $force = 0 )
	{
		$debugroot = EBLOG_HELPERS . '/debug';

		$firephp = false;
		$chromephp = false;

		if( JFile::exists( $debugroot . '/fb.php' ) && JFile::exists( $debugroot . '/FirePHP.class.php' ) )
		{
			include_once( $debugroot . '/fb.php' );
			fb( $var );
		}

		if( JFile::exists( $debugroot . '/chromephp.php' ) )
		{
			include_once( $debugroot . '/chromephp.php' );
			ChromePhp::log( $var );
		}
	}

	/**
	 * Legacy method for installations prior to 3.5
	 *
	 * @deprecated 3.5
	 * @since 3.5
	 */
	public function processVideos( $content , $created_by )
	{
		return EB::videos()->processVideos( $content );
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCategoryInclusion($categories)
	{
		if (!$categories) {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (!empty($categories) && $categories == 'all') {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (is_array($categories) && in_array('all', $categories)) {
			return '';
		}

		if (is_array($categories)) {
			return $categories;
		}

		$inclusion = explode(',', $categories);

		return $inclusion;
	}


	public static function uniqueLinkSegments( $urls = '' )
	{
		if( $urls )
		{
			$container  = explode('/', $urls);
			$container	= array_unique($container);
			$urls = implode('/', $container);
		}
		return $urls;
	}

	/**
	 * Retrieves the base url
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getBaseUrl()
	{
		static $url = null;

		if (is_null($url)) {

			$app = JFactory::getApplication();
			$config = EB::jConfig();

			$uri = JFactory::getURI();
			$language = $uri->getVar('lang', 'none');
			$router = $app->getRouter();

			// Ensure that the language doesn't contain any strange characters.
			$language = str_ireplace(array('"',"'"), '', $language);

			$url = rtrim(JURI::base(), '/') . '/';

			// If we should use http://site.com/index.php for the ajax calls, we need to append it here
			if (EB::config()->get('ajax_use_index')) {
				$url .= 'index.php';
			}

			$url .= '?option=com_easyblog&lang=' . $language;

			$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

			// When SEF is enabled, the URL should be different otherwise Joomla will keep redirecting
			if ($router->getMode() == JROUTER_MODE_SEF && $pluginEnabled && !EBR::isSh404Enabled()) {
				$rewrite = $config->get('sef_rewrite');

				// Reset the base url
				$base = str_ireplace(JURI::root(true), '', $uri->getPath());

				// Fix the path
				$path = $rewrite ? $base : JString::substr($base, 10);
				$path = JString::trim($path, '/');

				$parts = explode('/', $path);

				// The first segment will always be the language filter
				$language = 'none';

				if ($parts) {
					$language = reset($parts);
				}

				// Build the final url
				$url = rtrim(JURI::root(), '/') . '/index.php';
				if ($language) {
					$url .= '/' . $language;
				}
				$url .= '/?option=com_easyblog';

				// When rewrite is enabled, we need to use the proper paths
				if ($rewrite) {
					$url = rtrim(JURI::root(), '/') . '/' . $language . '/?option=com_easyblog';
					$language = 'none';
				}
			}

			// Append the item id if necessary
			$activeMenu = $app->getMenu()->getActive();

			if ($activeMenu && isset($activeMenu->id)) {
				$url .= '&Itemid=' . $activeMenu->id;
			}

			// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
			$currentUrl = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

			if ($currentUrl) {

				// When the url contains www and the current accessed url does not contain www, fix it.
				if (stristr($currentUrl, 'www') === false && stristr($url, 'www') !== false) {
					$url = str_ireplace('www.', '', $url);
				}

				// When the url does not contain www and the current accessed url contains www.
				if (stristr($currentUrl, 'www') !== false && stristr($url, 'www') === false) {
					$url = str_ireplace('://', '://www.', $url);
				}
			}
		}

		return $url;
	}

	/**
	 * Adds a stylesheet on the page
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function addStyleSheet($path)
	{
		$doc = JFactory::getDocument();
		$configuration = EB::configuration();

		// Default url to use.
		$url = EASYBLOG_JOOMLA_URI;

		// When cdn is enabled, we should use the respected cdn site instead
		if ($configuration->enableCdn && !$configuration->passiveCdn) {
			$url = EASYBLOG_JOOMLA_CDN;
		}

		// Merge the path and the url
		$url = rtrim($url, '/') . '/' . ltrim($path, '/');

		$doc->addStyleSheet($url);
	}

	public static function addScript($path)
	{

		$document = JFactory::getDocument();

		$configuration = EB::configuration();

		$root = EASYBLOG_JOOMLA_URI;

		if ($configuration->enableCdn && !$configuration->passiveCdn) {
			$root = EASYBLOG_JOOMLA_CDN;
		}

		$document->addScript($root . $path);
	}

	/**
	 * Creates a new template object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	Explicitly load specific theme
	 * @param	Array	An array of theme options
	 * @return
	 */
	public static function template($theme = false, $options = array())
	{
		require_once(dirname(__FILE__) . '/themes/themes.php');

		$theme = new EasyBlogThemes($theme, $options);

		return $theme;
	}

	public static function getTemplate($theme = false, $options = array())
	{
		return EB::template($theme, $options);
	}

	/**
	 * Determines if the provided theme is a legacy theme
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isLegacyTheme($theme)
	{
		$file 	= EBLOG_THEMES . '/' . $theme . '/config.json';

		$exists = JFile::exists($file);

		return !$exists;
	}

	/**
	 * Loads a list of services
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function loadServices()
	{
		$files = JFolder::files(JPATH_ROOT . '/components/com_easyblog/services', '.', false, true);

		if (!$files) {
			return;
		}

		foreach ($files as $file) {
			require_once($file);
		}

	}

	/**
	 * Check for valid token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkToken()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit( 'Invalid Token' );
	}

	/**
	 * Content formatter for the blogs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatter($type, $items, $cache = true)
	{
		require_once(dirname(__FILE__) . '/formatter/formatter.php');

		$formatter 	= new EasyBlogFormatter($type, $items, $cache);

		return $formatter->execute();
	}

	/**
	 * Converts to sef links
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function _($link, $xhtml = true)
	{
		require_once(dirname(__FILE__) . '/router.php');

		return EBR::_($link, $xhtml);
	}

	/**
	 * Detects if the folder exist based on the path given. If it doesn't exist, create it.
	 *
	 * @since	4.0
	 * @param	string	$path		The path to the folder.
	 * @return	boolean				True if exists (after creation or before creation) and false otherwise.
	 */
	public static function makeFolder( $path )
	{
		jimport('joomla.filesystem.folder');

		// If folder exists, we don't need to do anything
		if (JFolder::exists($path)) {
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		if (JFolder::create($path)) {
			return true;
		}

		return false;
	}

	// /**
	//  * Shorthand to load a user
	//  *
	//  * @since	5.0
	//  * @access	public
	//  * @param	string
	//  * @return
	//  */
	// public static function user($id = null)
	// {
	// 	static $users = array();

	// 	if ($id == null) {
	// 		$id = JFactory::getUser()->id;
	// 	}

	// 	if (!isset($users[$id])) {
	// 		$user = EB::table('Profile');
	// 		$user->load($id);

	// 		$users[$id] = $user;
	// 	}

	// 	return $users[$id];
	// }

	/**
	 * Proxy for post library
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function post($uid = null, $revisionId = null)
	{
		require_once(dirname(__FILE__) . '/post/post.php');

		$post = new EasyBlogPost($uid, $revisionId);

		return $post;
	}

	/**
	 * Proxy for document library
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function document($json=null)
	{
		require_once(dirname(__FILE__) . '/document/document.php');

		$document = new EasyBlogDocument($json);

		return $document;
	}

	/**
	 * Proxy for location library.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function location($provider = null)
	{
		require_once(dirname(__FILE__) . '/location/location.php');

		$service = new EasyBlogLocation($provider);

		return $service;
	}

	/**
	 * Retrieves the profiler
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function profiler()
	{
		static $profiler = null;

		if (!$profiler) {
			require_once(__DIR__ . '/profiler/profiler.php');

			$profiler = new EasyBlogProfiler();
		}

		return $profiler;
	}

	public static function isAssociationEnabled()
	{
		// Flag to avoid doing multiple database queries.
		static $tested = false;

		// Status of language filter parameter.
		static $enabled = false;

		if (EasyBlogHelper::getJoomlaVersion() >= '3.0' ) {
			//return JLanguageAssociations::isEnabled();

			if (JLanguageMultilang::isEnabled())
			{
				// If already tested, don't test again.
				if (!$tested)
				{
					$params = new JRegistry(JPluginHelper::getPlugin('system', 'languagefilter')->params);

					$enabled  = (boolean) $params->get('item_associations', true);
					$tested = true;
				}
			}

			return $enabled;

		} else {
			return false;
		}
	}


	/**
	 * cache for post related items.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			require_once(__DIR__ . '/cache/cache.php');

			$cache = new EasyBlogCache();
		}

		return $cache;
	}

	/**
	 * math lib
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function math()
	{
		static $math = null;

		if (!$math) {
			require_once(__DIR__ . '/math/math.php');

			$math = new EasyBlogMath();
		}

		return $math;
	}



	/**
	 * function to check mysql version to determine if collation change is need or not.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isCollationValid()
	{
		static $valid = null;

		if (is_null($valid)) {

			$valid = true;
			$jVersion = EasyBlogHelper::getJoomlaVersion();

			if ($jVersion >= '3.5') {
				$jConfig = JFactory::getConfig();
				$dbType = $jConfig->get('dbtype');

				if ($dbType == 'mysql' || $dbType == 'mysqli') {
					$db = EB::db();
					$dbversion = $db->getVersion();
					$dbversion = (float) $dbversion;

					if ($dbversion >= '5.1' && $dbversion < '5.5') {
						$valid = false;
						return $valid;
					}
				}
			}
		}

		return $valid;
	}





	/**
	 * This method will intelligently determine which menu params this post should be inheriting from
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getMenuParams($id, $type)
	{
		static $items = array();

		if (!isset($items[$id])) {

			$config = EB::config();

			$model = EB::model('Menu');

			// If there is an article menu item associated with this post, use this
			$menuId = 0;

			if ($type == 'listing') {

				$listingParams = $model->getDefaultXMLParams();

				$arrTmpParams = $listingParams->toArray();

				foreach($arrTmpParams as $key => $val) {

					// this mean we inherit from global setting.
					$listingParams->set($key, $config->get($type . '_' . $key));
				}

				$items[$id] = $listingParams;
				return $items[$id];
			}

			if ($type == 'category') {
				$menuId = $model->getMenusByCategoryId($id);
			}

			if ($type == 'tag') {
				$menuId = $model->getMenusByTagId($id);
			}

			if ($type == 'blogger') {
				$menuId = $model->getMenusByBloggerId($id);
			}

			$params = null;

			$tmpTable = EB::table($type);
			$tmpParams = $tmpTable->getParams();

			$arrTmpParams = $tmpParams->toArray();

			if (empty($arrTmpParams)) {
				// look like the params in global is empty. let try to get from xml file.
				$tmpParams = $tmpTable->getDefaultParams();
				$arrTmpParams = $tmpParams->toArray();
			}

			foreach($arrTmpParams as $key => $val) {
				if ($val == '-1') {
					// this mean we inherit from global setting.

					$tmpParams->set($key, $config->get($type . '_' . $key));
				}
			}

			if ($menuId) {

				$params = $model->getMenuParamsById($menuId);
				$arrParams = $params->toArray();

				if (! isset($arrParams['post_image'])) {
					$items[$id] = $tmpParams;

				} else {

					foreach($arrParams as $key => $val) {
						if ($val == '-1') {
							// this mean we inherit from global setting. lets get the value from global params.
							$params->set($key, $tmpParams->get($key));

						}
					}

					$items[$id] = $params;
				}

			} else {
				// If there's no menu associated with the post, associate the params with the primary category
				$items[$id] = $tmpParams;
			}

		}

		return $items[$id];
	}


}


class EasyBlogHelper extends EB {}


