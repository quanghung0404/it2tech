<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.html.parameter');
jimport('joomla.access.access');
jimport('joomla.application.component.model');

// Include legacy object
require_once(__DIR__ . '/legacy.php');

// Include constants
require_once(__DIR__ . '/dependencies.php');

class ED
{
	/**
	 * Initializes the css, js and necessary dependencies for EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function init($location = 'site')
	{
		static $loaded = array();

		if (!isset($loaded[$location])) {

			$input = JFactory::getApplication()->input;

			// Determines if we should force compilationg (Only allow for super admin)
			$recompile = false;

			if (ED::isSiteAdmin()) {
				$recompile = $input->get('compile', false, 'bool');
			}

			// If location is provided, we should respect the location
			$customLocation = $input->get('location', $location, 'word');
			$locations = array($location);

			if ($recompile && $customLocation == 'all') {
				$locations = array('site', 'admin');
			}

			foreach ($locations as $location) {
				// Render the JS compiler
				$compiler = ED::compiler($location);

				if ($recompile) {
					$compiler->compile(true, true);
				}
			}

			// Attach those scripts onto the head of the page now.
			$compiler->attach();

			// Attach the stylesheets
			$stylesheet = ED::stylesheet($location);
			$stylesheet->attach();

			$loaded[$location] = true;
		}

		return $loaded[$location];
	}

	/**
	 * Formats and returns the appropriate cdn url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCdnUrl()
	{
		static $cdnUrl = false;

		if (!$cdnUrl) {
			$config = ED::config();
			$cdnUrl = $config->get('system_cdn_url');

			if (!$cdnUrl) {
				return $cdnUrl;
			}

			if (stristr($cdnUrl, 'http://') === false && stristr($cdnUrl, 'https://') === false) {
				$cdnUrl = 'http://' . $cdnUrl;
			}
		}

		return $cdnUrl;
	}

	/**
	 * Singleton version for the ajax library
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function ajax()
	{
		static $ajax = null;

		if (!$ajax) {

			require_once(__DIR__ . '/ajax/ajax.php');

			$ajax = new EasyDiscussAjax();
		}

		return $ajax;
	}

	public static function _()
	{
		return ED::getHelper( func_get_args() );
	}

	/**
	 * Retrieves the token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getToken($contents = '')
	{
		$token = JFactory::getSession()->getFormToken();

		return $token;
	}

	public static function getHash( $seed = '' )
	{
		if( DiscussHelper::getJoomlaVersion() >= '2.5' )
		{
			return JApplication::getHash( $seed );
		}

		return JUtility::getHash( $seed );
	}

	/**
	 * Retrieves a jdate object with the correct speficied timezone offset
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function dateWithOffSet($str='')
	{
		$userTZ = self::getOffSet();
		$date = ED::date($str);

		$user = JFactory::getUser();
		$config = ED::Config();
		$jConfig = ED::JConfig();

		// temporary ignore the dst in joomla 1.6

		if ($user->id != 0) {
			$userTZ	= $user->getParam('timezone');
		}

		if (empty($userTZ)) {
			$userTZ	= $jConfig->get('offset');
		}

		$tmp = new DateTimeZone($userTZ);
		$date->setTimeZone($tmp);

		return $date;
	}

	public static function getBBCodeParser() {
		require_once( DISCUSS_CLASSES . '/decoda.php');
		$decoda = new DiscussDecoda( '', array('strictMode'=>false) );
		return $decoda;
	}

	public static function getHelper()
	{
		static $helpers	= array();

		$args = func_get_args();

		if (func_num_args() == 0 || empty($args) || empty($args[0]))
		{
			return false;
		}

		$sig = md5(serialize($args));

		if( !array_key_exists($sig, $helpers) )
		{
			$helper	= preg_replace('/[^A-Z0-9_\.-]/i', '', $args[0]);
			$file = DISCUSS_HELPERS . '/' . JString::strtolower($helper) . '.php';

			if( JFile::exists($file) )
			{
				require_once($file);
				$class	= 'Discuss' . ucfirst( $helper ) . 'Helper';

				switch (func_num_args()) {
					case '2':
						$helpers[$sig]	= new $class($args[1]);
						break;
					case '3':
						$helpers[$sig]	= new $class($args[1], $args[2]);
						break;
					case '4':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3]);
						break;
					case '5':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4]);
						break;
					case '6':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4], $args[5]);
						break;
					case '7':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
						break;
					case '1':
					default:
						$helpers[$sig]	= new $class();
						break;
				}
			}
			else
			{
				$helpers[$sig]	= false;
			}
		}

		return $helpers[$sig];
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyDiscussXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelperLegacy( $helper )
	{
		static $obj	= array();

		if( !isset( $obj[ $helper ] ) )
		{
			$file	= DISCUSS_HELPERS . '/' . JString::strtolower( $helper ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'Discuss' . ucfirst( $helper ) . 'Helper';

				$obj[ $helper ]	= new $class();
			}
			else
			{
				$obj[ $helper ]	= false;
			}
		}

		return $obj[ $helper ];
	}

	public static function getRegistry( $data = '' )
	{
		if( ED::getJoomlaVersion() >= '1.6' )
		{
			$registry = new JRegistry($data);
		}
		else
		{
			require_once DISCUSS_CLASSES . '/registry.php';
			$registry = new DiscussRegistry($data);
		}

		return $registry;
	}

	public static function getXML($data, $isFile = true)
	{
		if( ED::getJoomlaVersion() >= '1.6' )
		{
			$xml = JFactory::getXML($data, true);
		}
		else
		{
			// Disable libxml errors and allow to fetch error information as needed
			libxml_use_internal_errors(true);

			if ($isFile)
			{
				// Try to load the XML file
				//$xml = simplexml_load_file($data, 'JXMLElement');
				$xml = simplexml_load_file($data);
			}
			else
			{
				// Try to load the XML string
				//$xml = simplexml_load_string($data, 'JXMLElement');
				$xml = simplexml_load_string($data);
			}

			if (empty($xml))
			{
				// There was an error
				JError::raiseWarning(100, JText::_('JLIB_UTIL_ERROR_XML_LOAD'));

				if ($isFile)
				{
					JError::raiseWarning(100, $data);
				}

				foreach (libxml_get_errors() as $error)
				{
					JError::raiseWarning(100, 'XML: ' . $error->message);
				}
			}
		}

		return $xml;
	}

	public static function getUnansweredCount( $categoryId = '0', $excludeFeatured = false )
	{
		$db		= DiscussHelper::getDBO();

		$excludeCats	= DiscussHelper::getPrivateCategories();
		$catModel		= ED::model('Categories');

		if( !is_array( $categoryId ) && !empty( $categoryId ))
		{
			$categoryId 	= array( $categoryId );
		}

		$childs 		= array();
		if( $categoryId )
		{
			foreach( $categoryId as $id )
			{
				$data 		= $catModel->getChildIds( $id );

				if( $data )
				{
					foreach( $data as $childCategory )
					{
						$childs[]	= $childCategory;
					}
				}
				$childs[]		= $id;
			}
		}

		if( !$categoryId )
		{
			$categoryIds 	= false;
		}
		else
		{
			$categoryIds	= array_diff($childs, $excludeCats);
		}

		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
		$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
		$query	.= '    ON a.`id`=b.`parent_id`';
		$query	.= '    AND b.`published`=' . $db->Quote('1');
		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');
		$query  .= ' AND  a.`answered` = 0';
		$query	.= ' AND a.`isresolve`=' . $db->Quote('0');
		$query	.= ' AND b.`id` IS NULL';


		if( $categoryIds )
		{
			if( count( $categoryIds ) == 1 )
			{
				$categoryIds 	= array_shift( $categoryIds );
				$query .= ' AND a.`category_id` = ' . $db->Quote( $categoryIds );
			}
			else
			{
				$query .= ' AND a.`category_id` IN (' . implode( ',', $categoryIds ) .')';
			}
		}

		if( $excludeFeatured )
		{
			$query 	.= ' AND a.`featured`=' . $db->Quote( '0' );
		}

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public static function getFeaturedCount( $categoryId )
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		$query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query  .= ' AND a.`published` = ' . $db->Quote('1');
		$query	.= ' AND a.`category_id`= ' . $db->Quote( $categoryId );

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Allows caller to queue a message
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setMessage($message, $type = 'info')
	{
		$session = JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message = JText::_($message);
		$msgObj->type = strtolower($type);

		//save messsage into session
		$session->set('discuss.message.queue', $msgObj, 'DISCUSS.MESSAGE');
	}

	public static function getMessageQueue()
	{
		$session	= JFactory::getSession();
		$msgObj		= $session->get('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		//clear messsage into session
		$session->set('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		return $msgObj;
	}

	public static function getAlias( $title, $type='post', $id='0' )
	{

		$items = explode( ' ', $title );
		foreach( $items as $index => $item )
		{
			if( strpos( $item, '*' ) !== false  )
			{
				$items[$index] = 'censored';
			}
		}

		$title = implode( $items, ' ' );

		$alias	= DiscussHelper::permalinkSlug($title);

		// Make sure no such alias exists.
		$i	= 1;
		while( DiscussRouter::_isAliasExists( $alias, $type, $id ) )
		{
			$alias	= DiscussHelper::permalinkSlug( $title ) . '-' . $i;
			$i++;
		}

		return $alias;
	}

	public static function permalinkSlug( $string, $uid = null )
	{
		$config		= DiscussHelper::getConfig();
		if ($config->get( 'main_sef_unicode' )) {

			if ($uid && is_numeric($uid)) {
				$string = $uid . ':' . $string;
			}

			// Unicode support.
			$alias  = DiscussHelper::permalinkUnicodeSlug($string);

		} else {
			// Replace accents to get accurate string
			//$alias	= DiscussRouter::replaceAccents( $string );
			// hÃ¤llÃ¶ wÃ¶rldÃŸ became hallo-world instead haelloe-woerld thus above line is commented
			// for consistency with joomla

			$alias	= JFilterOutput::stringURLSafe( $string );

			// check if anything return or not. If not, then we give a date as the alias.
			if(trim(str_replace('-', '', $alias)) == '') {
				$alias = ED::date()->format("Y-m-d-H-i-s");
			}
		}
		return $alias;
	}

	public static function permalinkUnicodeSlug( $string )
	{
		$slug	= '';
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$slug	= JFilterOutput::stringURLUnicodeSlug($string);
		}
		else
		{
			//replace double byte whitespaces by single byte (Far-East languages)
			$slug = preg_replace('/\xE3\x80\x80/', ' ', $string);

			// remove any '-' from the string as they will be used as concatenator.
			// Would be great to let the spaces in but only Firefox is friendly with this
			$slug = str_replace('-', ' ', $slug);

			// replace forbidden characters by whitespaces
			$slug = preg_replace( '#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug );

			//delete all '?'
			$slug = str_replace('?', '', $slug);

			//trim white spaces at beginning and end of alias, make lowercase
			$slug = trim(JString::strtolower($slug));

			// remove any duplicate whitespace and replace whitespaces by hyphens
			$slug =preg_replace('#\x20+#','-', $slug);
		}

		return $slug;
	}

	public static function getNotification()
	{
		static $notify = false;

		if( !$notify )
		{

			$notify	= ED::notifications();
		}
		return $notify;

	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if (!$mailq) {
			$mailq = ED::mailqueue();
		}

		return $mailq;
	}

	public static function getSiteSubscriptionClass()
	{
		static $sitesubscriptionclass = false;

		if( !$sitesubscriptionclass )
		{
			require_once DISCUSS_CLASSES . '/subscription.php';

			$sitesubscriptionclass	= new DiscussSubscription();
		}
		return $sitesubscriptionclass;
	}

	public static function getLoginHTML( $returnURL )
	{
		$tpl	= new DiscussThemes();
		$tpl->set( 'return'	, base64_encode( $returnURL ) );

		return $tpl->fetch( 'ajax.login.php' );
	}

	public static function getLocalParser()
	{
		$data		= new stdClass();

		$contents	= JFile::read( DISCUSS_ADMIN_ROOT . '/easydiscuss.xml' );

		$parser		= new DiscussXMLHelper( $contents );

		return $parser;
	}

	/**
	 * Retrieves the current version of EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {

			$manifest = DISCUSS_ADMIN_ROOT . '/easydiscuss.xml';

			$parser = JFactory::getXML($manifest, true);

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Retrieves the server's version of EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getVersion()
	{
		static $version = null;

		if (is_null($version)) {

			$connector = ED::connector();
			$connector->addUrl(ED_UPDATER);
			$connector->connect();

			$contents = $connector->getResult(ED_UPDATER);

			if (!$contents) {
				$version = false;

				return $version;
			}

			$obj = json_decode($contents);

			if (!$obj) {
				$version = false;

				return $version;
			}

			$version = $obj->version;
		}

		return $version;
	}

	/**
	 * Retrieves the default value from the configuration file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getDefaultConfigValue($key, $defaultVal = null)
	{
		static $defaults = null;

		if (is_null($defaults)) {

			$file = DISCUSS_ADMIN_ROOT . '/defaults/configuration.ini';
			$contents = JFile::read($file);

			$defaults = new JRegistry($contents);
		}

		return $defaults->get($key, $defaultVal);
	}

	/**
	 * Retrieves the core configuration object for EasyDiscuss.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	JRegistry
	 */
	public static function config()
	{
		if (defined('ED_CLI')) {
			return false;
		}

		static $config = null;

		if (is_null($config)) {

			// Render the data from the ini first.
			$raw = JFile::read(DISCUSS_ADMIN_ROOT . '/defaults/configuration.ini');

			$config = ED::getRegistry($raw);

			// Retrieve the data from the db
			$db = ED::db();
			$query = 'SELECT ' . $db->qn('params') . ' FROM ' . $db->qn('#__discuss_configs');
			$query .= 'WHERE ' . $db->qn('name') . '=' . $db->Quote('config');

			$db->setQuery($query);
			$result = $db->loadResult();

			$config->loadString($result, 'INI');
		}

		return $config;
	}

	public static function getPostAccess( DiscussPost $post , DiscussCategory $category )
	{
		static $access	= null;

		if( is_null( $access[ $post->id ] ) )
		{
			// Load default ini data first
			$access[ $post->id ] = new DiscussPostAccess( $post , $category);
		}

		return $access[ $post->id ];
	}

	/*
	 * Method used to determine whether the user a guest or logged in user.
	 * return : boolean
	 */
	public static function isLoggedIn()
	{
		$my	= JFactory::getUser();
		$loggedIn	= (empty($my) || $my->id == 0) ? false : true;
		return $loggedIn;
	}

	public static function isSiteAdmin($userId = null)
	{
		static  $loaded = array();

		$sig    = is_null($userId) ? 'me' : $userId ;

		if(! isset( $loaded[$sig] ) )
		{
			$my	= JFactory::getUser( $userId );

			$admin = false;
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				$admin	= $my->authorise('core.admin');
			}
			else
			{
				$admin	= $my->usertype == 'Super Administrator' || $my->usertype == 'Administrator' ? true : false;
			}

			$loaded[ $sig ] = $admin;
		}

		return $loaded[ $sig ];
	}

	public static function isMine($uid)
	{
		$my	= JFactory::getUser();

		if($my->id == 0)
			return false;

		if( empty($uid) )
			return false;

		$mine	= $my->id == $uid ? 1 : 0;
		return $mine;
	}

	public static function getUserId( $username )
	{
		static $userids = array();

		if( !isset( $userids[ $username ] ) || empty($userids[$username]) )
		{
			$db		= DiscussHelper::getDBO();

			// first get from user alias
			$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `alias` = ' . $db->quote( $username );
			$db->setQuery( $query );
			$userid	= $db->loadResult();

			// then get from user nickname
			if (!$userid) {
				$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `nickname` = ' . $db->quote( $username );
				$db->setQuery( $query );
				$userid	= $db->loadResult();
			}

			// then get from username
			if (!$userid) {
				$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->quote( $username );
				$db->setQuery( $query );

				$userid	= $db->loadResult();
			}

			if (!$userid) {
				$query	= 'SELECT `id` FROM `#__users` WHERE `name`=' . $db->quote( $username );
				$db->setQuery( $query );

				$userid	= $db->loadResult();
			}



			$userids[$username] = $userid;
		}

		return $userids[$username];
	}

	public static function getAjaxURL()
	{
		$uri		= JFactory::getURI();
		$language	= $uri->getVar('lang', 'none');
		$app		= JFactory::getApplication();
		$config		= DiscussHelper::getJConfig();
		$router		= $app->getRouter();
		$url		= rtrim( JURI::base() , '/' );

		$url 		= $url . '/index.php?option=com_easydiscuss&lang=' . $language;

		if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
		{
			$rewrite	= $config->get('sef_rewrite');

			$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
			$path		=  $rewrite ? $base : JString::substr( $base , 10 );
			$path		= JString::trim( $path , '/' );
			$parts		= explode( '/' , $path );

			$language = addslashes($language);

			if($parts) {
				// First segment will always be the language filter.
				$language	= reset( $parts );
			} else {
				$language	= 'none';
			}

			if ($rewrite) {
				$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easydiscuss';
				$language	= 'none';
			} else {
				$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easydiscuss';
			}
		}

		return $url;
	}

	public static function getBaseUrl()
	{
		static $url;

		if (isset($url)) return $url;

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$uri		= JFactory::getURI();
			$language	= $uri->getVar( 'lang' , 'none' );
			$app		= JFactory::getApplication();
			$config		= DiscussHelper::getJConfig();
			$router		= $app->getRouter();
			$url		= rtrim( JURI::base() , '/' );

			$url 		= $url . '/index.php?option=com_easydiscuss&lang=' . $language;

			if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
			{
				$rewrite	= $config->get('sef_rewrite');

				$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
				$path		=  $rewrite ? $base : JString::substr( $base , 10 );
				$path		= JString::trim( $path , '/' );
				$parts		= explode( '/' , $path );

				if( $parts )
				{
					// First segment will always be the language filter.
					$language	= reset( $parts );
				}
				else
				{
					$language	= 'none';
				}

				if( $rewrite )
				{
					$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easydiscuss';
					$language	= 'none';
				}
				else
				{
					$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easydiscuss';
				}
			}
		}
		else
		{

			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easydiscuss';
		}

		$menu = JFactory::getApplication()->getmenu();

		if( !empty($menu) )
		{
			$item = $menu->getActive();
			if( isset( $item->id) )
			{
				$url    .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
		// Need to sort them out here.
		$currentURL		= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

		if( !empty( $currentURL ) )
		{
			// When the url contains www and the current accessed url does not contain www, fix it.
			if( stristr($currentURL , 'www' ) === false && stristr( $url , 'www') !== false )
			{
				$url	= str_ireplace( 'www.' , '' , $url );
			}

			// When the url does not contain www and the current accessed url contains www.
			if( stristr( $currentURL , 'www' ) !== false && stristr( $url , 'www') === false )
			{
				$url	= str_ireplace( '://' , '://www.' , $url );
			}
		}

		return $url;
	}

	/**
	 * Loads the default languages for EasyDiscuss
	 *
	 * @since	4.0
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
			$lang->load('com_easydiscuss', $path);

			$loaded[$path] = true;
		}

		return $loaded[$path];
	}

	public static function getDurationString($dateTimeDiffObj)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss', JPATH_ROOT);

		$data = $dateTimeDiffObj;
		$returnStr = '';

		if ($data->daydiff <= 0) {
			$timeDate = explode(':', $data->timediff);

			// ensure all has a colon
			if (!isset($timeDate[1])) {
				$timeDate[1] = null;
			}

			if (intval($timeDate[0], 10) >= 1) {
				$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_HOURS_AGO', intval($timeDate[0], 10), true);

			} else if(intval($timeDate[1], 10) >= 2) {
				$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_MINUTES_AGO', intval($timeDate[1], 10), true);

			} else {
				$returnStr = JText::_('COM_EASYDISCUSS_LESS_THAN_A_MINUTE_AGO');
			}

		} else if (($data->daydiff >= 1) && ($data->daydiff < 7)) {
			$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_DAYS_AGO', $data->daydiff, true);

		} else if ($data->daydiff >= 7 && $data->daydiff <= 30) {
			$returnStr = (intval($data->daydiff/7, 10) == 1 ? JText::_('COM_EASYDISCUSS_ONE_WEEK_AGO') : JText::sprintf('COM_EASYDISCUSS_WEEKS_AGO', intval($data->daydiff/7, 10)));

		} else {
			$returnStr = JText::_('COM_EASYDISCUSS_MORE_THAN_A_MONTH_AGO');
		}

		return $returnStr;
	}

	public static function storeSession($data, $key, $ns = 'com_easydiscuss')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'com_easydiscuss')
	{
		$data = null;

		$mySess = JFactory::getSession();
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

	public static function isNew( $noofdays )
	{
		$config	= DiscussHelper::getConfig();
		$isNew	= ($noofdays <= $config->get('layout_daystostaynew', 7)) ? true : false;

		return $isNew;
	}

	public static function getExternalLink($link)
	{
		$uri = JURI::getInstance();
		$domain	= $uri->toString(array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EDR::_($link, false), '/');
	}

	public static function uploadAvatar($profile, $isFromBackend = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$config = ED::config();

		$avatar_config_path	= $config->get('main_avatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		// Get the upload path
		$upload_path = JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path = $avatar_config_path;

		$error = null;
		$file = JRequest::getVar('Filedata', '', 'files', 'array');

		// Check whether the upload folder exist or not. if not create it.
		if (!JFolder::exists($upload_path)) {
			if (!JFolder::create($upload_path)) {
				// Redirect
				if (!$isFromBackend) {
					ED::setMessageQueue(JText::_( 'COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER' ), 'error');
					$mainframe->redirect( EDR::_('index.php?option=com_easydiscuss&view=profile', false));
					return;
				}

				// From backend
				$mainframe->redirect( EDR::_('index.php?option=com_easydiscuss&view=users', false), JText::_('COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error');
				return;
			}
		}

		// Makesafe on the file
		$date = ED::date();
		$file_ext = ED::Image()->getFileExtention($file['name']);
		$file['name'] = $my->id . '_' . JFile::makeSafe(md5($file['name'].$date->toSql())) . '.' . strtolower($file_ext);


		if (isset($file['name'])) {
			$target_file_path = $upload_path;
			$relative_target_file = $rel_upload_path . '/' . $file['name'];
			$target_file = JPath::clean($target_file_path . '/' . JFile::makeSafe($file['name']));
			$original = JPath::clean($target_file_path . '/' . 'original_' . JFile::makeSafe($file['name']));

			$isNew = false;

			if (!ED::Image()->canUpload($file, $error)) {
				if (!$isFromBackend) {
					ED::setMessageQueue(JText::_($error), 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=profile&layout=edit', false));
					return;
				}

				$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=users', false), JText::_($error), 'error');

				return;
			}

			if ((int)$file['error'] != 0) {
				if (!$isFromBackend) {
					ED::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=profile&layout=edit', false));
					return;
				}
				//from backend
				$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=users', false), $file['error'], 'error');

				return;
			}

			//rename the file 1st.
			$oldAvatar = $profile->avatar;
			$tempAvatar	= '';
			$isNew = ($oldAvatar == 'default.png')? true : false ;

			if (!$isNew) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				// Test if old original file exists. If exist, remove it.
				if (JFile::exists($target_file_path . '/original_' . $oldAvatar)) {
					JFile::delete($target_file_path . '/original_' . $oldAvatar);
				}

				JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
			}

			if (JFile::exists($target_file) || JFolder::exists($target_file)) {
				if (!$isNew) {
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=profile', false));
					return;
				}

				//from backend
				$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=users', false), JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				return;
			}

			// image size should be in ratio of 1:1
			$configImageWidth = $config->get('layout_avatarwidth', 160);
			$configImageHeight = $configImageWidth;
			$originalImageWidth = $config->get('layout_originalavatarwidth', 400);
			$originalImageHeight = $originalImageWidth;

			// Copy the original image files over
			$image = ED::simpleimage();
			$image->load($file['tmp_name']);

			//$image->resizeToFill( $originalImageWidth , $originalImageHeight );

			// By Kevin Lankhorst
			$image->resizeOriginal($originalImageWidth, $originalImageHeight, $configImageWidth, $configImageHeight);

			$image->save($original, $image->image_type);
			unset($image);

			$image = ED::simpleimage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if (!$isNew) {
				if (JFile::exists($tempAvatar)) {
					JFile::delete($tempAvatar);
				}
			}

			return JFile::makeSafe($file['name']);
		} else {
			return 'default.png';
		}

	}

	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return ED::uploadMediaAvatar( 'category', $category, $isFromBackend);
	}

	public static function uploadMediaAvatar($mediaType, $mediaTable, $isFromBackend = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$config = ED::getConfig();

		// required params
		$layout_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type = ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';

		if (!$isFromBackend && $mediaType == 'category') {
			$url = 'index.php?option=com_easydiscuss&view=categories';
			ED::setMessage(JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			$mainframe->redirect(EDR::_($url, false));
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path = JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path = $avatar_config_path;

		$err = null;
		$file = JRequest::getVar('Filedata', '', 'files', 'array');

		//check whether the upload folder exist or not. if not create it.
		if (!JFolder::exists($upload_path)) {
			if (!JFolder::create($upload_path)) {
				// Redirect
				if(!$isFromBackend) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$this->setRedirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					$this->setRedirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error');
				}
				return;
			} else {
				// folder created. now copy index.html into this folder.
				if (!JFile::exists( $upload_path . '/index.html')) {
					$targetFile	= DISCUSS_ROOT . '/index.html';
					$destFile = $upload_path . '/index.html';

					if(JFile::exists($targetFile))
						JFile::copy($targetFile, $destFile);
				}
			}
		}

		//makesafe on the file
		$file['name'] = $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$target_file_path = $upload_path;
			$relative_target_file = $rel_upload_path . '/' . $file['name'];
			$target_file = JPath::clean($target_file_path . '/' . JFile::makeSafe($file['name']));
			$isNew = false;

			require_once(__DIR__ . '/image/image.php');
			require_once(__DIR__ . '/simpleimage/simpleimage.php');

			if (!EasyDiscussImage::canUpload($file, $err)) {
				if(!$isFromBackend) {
					ED::setMessage( JText::_($err), 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					// From backend
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories'), JText::_($err), 'error');
				}
				return;
			}

			if (0 != (int)$file['error']) {
				if (!$isFromBackend) {
					ED::setMessage($file['error'], 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					// From backend
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), $file['error'], 'error');
				}
				return;
			}

			// Rename the file 1st.
			$oldAvatar = (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar = '';
			if ($oldAvatar != $default_avatar_type) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar = JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
			} else {
				$isNew  = true;
			}


			if (JFile::exists($target_file)) {
				if ($oldAvatar != $default_avatar_type) {
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					ED::setMessage(JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file)) {

				if ($oldAvatar != $default_avatar_type) {
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					ED::setMessage(JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file), 'error');
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					$mainframe->redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth  = DISCUSS_AVATAR_LARGE_WIDTH;
			$configImageHeight = DISCUSS_AVATAR_LARGE_HEIGHT;

			$image = new EasyDiscussSimpleImage();
			$image->load($file['tmp_name']);
			$image->resize($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if ($oldAvatar != $default_avatar_type) {
				if (JFile::exists($tempAvatar)) {
					JFile::delete($tempAvatar);
				}
			}

			return JFile::makeSafe($file['name']);
		} else {
			return $default_avatar_type;
		}

	}

	/**
	 * Applies word filtering
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function wordFilter($text)
	{
		$config = ED::Config();

		if (empty($text)) {
			return $text;
		}

		if (trim($text) == '') {
			return $text;
		}

		if ($config->get('main_filterbadword', 1) && $config->get('main_filtertext', '') != '') {

			require_once DISCUSS_HELPERS . '/filter.php';
			// filter out bad words.
			$bwFilter		= new BadWFilter();
			$textToBeFilter	= explode(',', $config->get('main_filtertext'));

			// lets do some AI here. for each string, if there is a space,
			// remove the space and make it as a new filter text.
			if( count($textToBeFilter) > 0 )
			{
				$newFilterSet   = array();
				foreach( $textToBeFilter as $item)
				{
					if( JString::stristr($item, ' ') !== false )
					{
						$newKeyWord 	= JString::str_ireplace(' ', '', $item);
						$newFilterSet[] = $newKeyWord;
					}
				} // foreach

				if( count($newFilterSet) > 0 )
				{
					$tmpNewFitler	= array_merge($textToBeFilter, $newFilterSet);
					$textToBeFilter	= array_unique($tmpNewFitler);
				}

			}//end if

			$bwFilter->strings	= $textToBeFilter;

			//to be filtered text
			$bwFilter->text		= $text;
			$new_text			= $bwFilter->filter();

			$text				= $new_text;
		}

		return $text;
	}

	/**
	 * Formats a discussion object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatPost($rows, $isSearch = false , $isFrontpage = false)
	{
		// If there is no items, skip this altogether
		if (!$rows) {
			return $rows;
		}

		$posts = array();

		foreach ($rows as $row) {

			// Load it into our post library
			$post = ED::post($row);

			$posts[] = $post;
		}

		return $posts;
	}

	public static function formatComments( $comments )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/events/events.php';
		include_once($path);


		$config 	= DiscussHelper::getConfig();

		if( !$comments )
		{
			return false;
		}

		$result 	= array();

		foreach( $comments as $row )
		{
			$duration			= new StdClass();
			$duration->daydiff	= $row->daydiff;
			$duration->timediff	= $row->timediff;

			$comment 	= DiscussHelper::getTable( 'Comment' );
			$comment->bind($row);

			$comment->duration  = DiscussHelper::getDurationString( $duration );

			$creator = ED::user($comment->user_id);
			$comment->creator	= $creator;

			if ( $config->get( 'main_content_trigger_comments' ) )
			{
				// process content plugins
				$comment->content	= $comment->comment;

				EasyDiscussEvents::importPlugin( 'content' );
				EasyDiscussEvents::onContentPrepare('comment', $comment);

				$comment->event = new stdClass();

				$results	= EasyDiscussEvents::onContentBeforeDisplay('comment', $comment);
				$comment->event->beforeDisplayContent	= trim(implode("\n", $results));

				$results	= EasyDiscussEvents::onContentAfterDisplay('comment', $comment);
				$comment->event->afterDisplayContent	= trim(implode("\n", $results));

				$comment->comment	= $comment->content;
				unset($comment->content);

				$comment->comment = ED::badwords()->filter($comment->comment);
			}

			$result[]	= $comment;
		}

		return $result;
	}

	/**
	 * Formats the necessary output for reply items
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatReplies($result, $category = null, $pagination = true, $acceptedReply = false)
	{
		$config = ED::config();

		if (!$result) {
			return $result;
		}

		$limitstart = JFactory::getApplication()->input->get('limitstart', 0);
		$replies = array();

		foreach ($result as $key => $row) {
			$reply = ED::post($row);

			$reply->permalink = EDR::getReplyRoute($reply->parent_id, $reply->id);

			// Default reply permalink title; specifically for accepted answer.
			$reply->seq = JText::_('COM_EASYDISCUSS_REPLY_PERMALINK_TITLE');

			if (!$acceptedReply) {
				$reply->seq = $key + 1;

				if ($pagination) {
	                $reply->seq = $limitstart ? $key + $limitstart + 1 : $key + 1;
	            }
			}

			if ($config->get('main_comment')) {
				$commentLimit = $config->get('main_comment_pagination') ? $config->get('main_comment_pagination_count') : null;
				$reply->comments = $reply->getComments($commentLimit);

				// get post comments count
				$reply->commentsCount = $reply->getTotalComments();
			}

			$replies[] = $reply;
		}

		return $replies;
	}

	public static function formatUsers( $result )
	{
		if( !$result )
		{
			return $result;
		}

		$total	= count( $result );

		$authorIds  = array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$item			= $result[ $i ];
			$authorIds[] 	= $item->id;
		}

		// Reduce SQL queries by pre-loading all author object.
		$authorIds  = array_unique($authorIds);
		ED::user($authorIds);

		$users	= array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$row	=& $result[ $i ];

			$user = ED::user($row->id);
			$users[] = $user;
		}

		return $users;
	}

	public static function getVoters($id, $limit='5')
	{
		$config	= DiscussHelper::getConfig();

		$table	= DiscussHelper::getTable( 'Post' );
		$voters	= $table->getVoters($id, $limit);

		$data					= new stdClass();
		$data->voters			= '';
		$data->shownVoterCount	= '';

		if(!empty($voters))
		{
			$data->shownVoterCount = count($voters);

			foreach($voters as $voter)
			{
				$displayname = $config->get('layout_nameformat');

				switch($displayname)
				{
					case "name" :
						$votername = $voter->name;
						break;
					case "username" :
						$votername = $voter->username;
						break;
					case "nickname" :
					default :
						$votername = (empty($voter->nickname)) ? $voter->name : $voter->nickname;
						break;
				}

				if(!empty($data->voters))
				{
					$data->voters .= ', ';
				}

				$data->voters .= '<a href="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $voter->user_id ) . '">' . $votername . '</a>';
			}
		}

		return $data;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function isJoomla31()
	{
		return DiscussHelper::getJoomlaVersion() >= '3.1';
	}

	public static function isJoomla30()
	{
		return DiscussHelper::getJoomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return DiscussHelper::getJoomlaVersion() >= '1.6' && DiscussHelper::getJoomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return DiscussHelper::getJoomlaVersion() == '1.5';
	}

	public static function getDefaultSAIds()
	{
		$saUserId	= '62';

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= DiscussHelper::getSAUsersIds();
			$saUserId	= $saUsers[0];
		}

		return $saUserId;
	}

	/**
	 * Used in J1.5!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds15()
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT `id` FROM `#__users`';
		$query .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query .= ' OR `gid` = ' . $db->Quote('25') . ')';
		$query .= ' ORDER BY `id` ASC';

		$db->setQuery($query);
		$result = $db->loadResultArray();

		$result = (empty($result)) ? array( '62' ) : $result;

		return $result;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
	{
		if( ED::getJoomlaVersion() < '1.6' ) {
			return ED::getSAUsersIds15();
		}

		$db = DiscussHelper::getDBO();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup    = array();
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
				$userArr	= JAccess::getUsersByGroup($sag->id);
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

	public static function populateCategories($parentId, $userId, $outType, $eleName, $default = false, $isWrite = false, $isPublishedOnly = false, $showPrivateCat = true , $disableContainers = false , $customClass = 'form-control', $exclusion = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$model = ED::model('Categories');
		$parentCat	= null;

		if (!empty($userId)) {
			$parentCat = $model->getParentCategories($userId, 'poster', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType);

		} else if (!empty($parentId)) {
			$parentCat = $model->getParentCategories($parentId, 'category', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType);

		} else {
			$parentCat = $model->getParentCategories('', 'all', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType);
		}

		// If the result == null
		if (empty($parentCat)) {
			return;
		}

		$ignorePrivate = false;

		switch($outType) {
			case 'link' :
				$ignorePrivate = false;
				break;
			case 'select':
			default:
				$ignorePrivate = true;
				break;
		}

		$selectACLOnly = false;

		if ($isWrite) {
			$ignorePrivate = false;
			$selectACLOnly = true;
		}

		if (!empty($parentCat)) {

			for ($i = 0; $i < count($parentCat); $i++) {

				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				ED::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $showPrivateCat, $selectACLOnly, $exclusion);
			}//for $i
		}//end if !empty $parentCat

		$formEle = '';

		foreach ($parentCat as $category) {

			$selected = ($category->id == $default) ? ' selected="selected"' : '';

			if ($default === false) {
				$selected = $category->default ? ' selected="selected"' : '';
			}

			$style = '';
			$disabled = '';

			// @rule: Test if the category should just act as a container
			if ($disableContainers) {
				$disabled = $category->container ? ' disabled="disabled"' : '';
				$style = $disabled ? ' style="font-weight:700;"' : '';
			}

			$formEle .= '<option value="' . $category->id . '" ' . ' data-ed-move-post-category-id=' . $category->id . ' ' . $selected . $disabled . $style . '>' . JText::_( $category->title ) . '</option>';

			ED::accessNestedCategories($category, $formEle, '0', $default, $outType , '' , $disableContainers);
		}

		$selected = empty($default) ? ' selected="selected"' : '';

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $customClass . '">';

		if (!$isWrite)
			$html .=	'<option value="0">' . JText::_('COM_EASYDISCUSS_SELECT_PARENT_CATEGORY') . '</option>';
		else
			$html .= '<option value="0" ' . $selected . '>' . JText::_('COM_EASYDISCUSS_SELECT_CATEGORY') . '</option>';
			$html .= $formEle;
			$html .= '</select>';

		return $html;
	}

	public static function buildNestedCategories($parentId, $parent, $ignorePrivate = false, $isPublishedOnly = false, $showPrivate = true, $selectACLOnly = false, $exclusion = array())
	{
		$catsModel = ED::model('Categories');

		// [model:category]
		$catModel = ED::model('Category');

		$childs	= $catsModel->getChildCategories($parentId, $isPublishedOnly, $showPrivate, $exclusion);

		$aclType = ( $selectACLOnly ) ? DISCUSS_CATEGORY_ACL_ACTION_SELECT : DISCUSS_CATEGORY_ACL_ACTION_VIEW;

		$accessibleCatsIds = ED::getAccessibleCategories($parentId, $aclType);

		if (!empty($childs)) {

			for($j = 0; $j < count($childs); $j++) {
				$child = $childs[$j];
				$child->count = $catModel->getTotalPostCount($child->id);
				$child->childs = null;

				if (!$ignorePrivate) {

					if (count($accessibleCatsIds) > 0) {

						$access = false;

						foreach ($accessibleCatsIds as $canAccess) {

							if ($canAccess->id == $child->id) {
								$access = true;
							}
						}

						if (!$access)
							continue;
					} else {
						continue;
					}
				}

				if (!ED::buildNestedCategories($child->id, $child, $ignorePrivate, $isPublishedOnly, $showPrivate, $selectACLOnly, $exclusion)) {
					$parent->childs[] = $child;
				}
			}// for $j

			if (!empty($parent->childs)) {
				$parent->childs	= array_reverse($parent->childs);
			}
		} else {
			return false;
		}
	}

	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '' , $disableContainers = false )
	{
		$config = DiscussHelper::getConfig();
		if(isset($arr->childs) && is_array($arr->childs))
		{
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'list')
			{
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '<ul>';
			}

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child  = $arr->childs[$j];

				switch($type)
				{
					case 'select':
						$selected    = ($child->id == $default) ? ' selected="selected"' : '';

						if( !$default )
						{
							$selected   = $child->default ? ' selected="selected"' : '';
						}

						$disabled 		= '';
						$style 			= '';

						// @rule: Test if the category should just act as a container
						if( $disableContainers )
						{
							$disabled	= $child->container	? ' disabled="disabled"' : '';
							$style		= $disabled ? ' style="font-weight:700;"' : '';
						}

						$html   	.= '<option value="'.$child->id.'" ' . $selected . $disabled . $style . '>' . $space . $sup . $child->title . '</option>';
						break;
					case 'list':
						$expand 	= !empty($child->childs)? '<span onclick="EasyDiscuss.$(this).parents(\'li:first\').toggleClass(\'expand\');">[+] </span>' : '';
						$html 		.= '<li><div>' . $space . $sup . $expand . '<a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">' . $child->title . '</a> <b>(' . $child->count . ')</b></div>';
						break;
					case 'listlink':
						$str = '<li><a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">';
						$str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str        .= '</a></li>';
						$html   	.= $str;
						break;
					default:
						$str    	 = '<a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">';
						//str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str        .= '</a></li>';
						$html   	.= $str;
				}

				if( !$config->get('layout_category_one_level', 0) )
				{
					DiscussHelper::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter , $disableContainers );
				}


				if($type == 'list')
				{
					$html .= '</li>';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '</ul>';
			}
		}
		else
		{
			return false;
		}
	}

	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			//$modelSubscribe	= ED::model( 'Subscribe' );
			//$subscribers	= $modelSubscribe->getSiteSubscribers('instant');

			for($j = 0; $j < count($arr->childs); $j++)
			{
				$child = $arr->childs[$j];

				$newArr[] = $child->id;
				DiscussHelper::accessNestedCategoriesId($child, $newArr);
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
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr    = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= DiscussHelper::getTable( 'Category' );

		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}

	/**
	 * $post - post jtable object
	 * $parent - post's parent id.
	 * $isNew - indicate this is a new post or not.
	 */

	public static function sendNotification( $post, $parent = 0, $isNew, $postOwner, $prevPostStatus)
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$config = DiscussHelper::getConfig();
		$notify	= DiscussHelper::getNotification();

		$emailPostTitle = $post->title;
		$modelSubscribe		= ED::model( 'Subscribe' );

		//get all admin emails
		$adminEmails = array();
		$ownerEmails = array();
		$newPostOwnerEmails = array();
		$postSubscriberEmails = array();
		$participantEmails = array();

		$catSubscriberEmails = array();

		if( empty( $parent ) )
		{
			// only new post we notify admin.
			if($config->get( 'notify_admin' ))
			{
				$admins = $notify->getAdminEmails();

				if(! empty($admins))
				{
					foreach($admins as $admin)
					{
						$adminEmails[]   = $admin->email;
					}
				}
			}

			// notify post owner too when moderate is on
			if( !empty( $postOwner ) )
			{
				$postUser    			= JFactory::getUser( $postOwner );
				$newPostOwnerEmails[]  	= $postUser->email;
			}
			else
			{
				$newPostOwnerEmails[]	= $post->poster_email;
			}

		}
		else
		{
			// if this is a new reply, notify post owner.
			$parentTable		= DiscussHelper::getTable( 'Post' );
			$parentTable->load( $parent );

			$emailPostTitle = $parentTable->title;

			$oriPostAuthor  = $parentTable->user_id;

			if( !$parentTable->user_id )
			{
				$ownerEmails[]	= $parentTable->poster_email;
			}
			else
			{
				$oriPostUser    = JFactory::getUser( $oriPostAuthor );
				$ownerEmails[]  = $oriPostUser->email;
			}
		}

		$emailSubject	= ( empty( $parent ) ) ? JText::sprintf('COM_EASYDISCUSS_NEW_POST_ADDED', $post->id , $emailPostTitle ) : JText::sprintf( 'COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent, $emailPostTitle );
		$emailTemplate	= ( empty( $parent ) ) ? 'email.subscription.site.new.php' : 'email.post.reply.new.php';

		//get all site's subscribers email that want to receive notification immediately
		$subscriberEmails	= array();
		$subscribers		= array();


		// @rule: Specify the default name and avatar
		$authorName 			= $post->poster_name;
		$authorAvatar 			= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_avatar.png';



		// @rule: Only process author items that belongs to a valid user.
		if (!empty($postOwner)) {
			$user = ED::user($postOwner);

			$authorName 		= $user->getName();
			$authorAvatar 		= $user->getAvatar();
		}

		if( $config->get('main_sitesubscription') && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{
			$subscribers        = $modelSubscribe->getSiteSubscribers('instant','',$post->category_id);
			$postSubscribers	= $modelSubscribe->getPostSubscribers( $post->parent_id );

			// This was added because the user allow site wide notification (as in all subscribers should get notified) but category subscribers did not get it.
			$catSubscribers		= $modelSubscribe->getCategorySubscribers( $post->id );

			if(! empty($subscribers))
			{
				foreach($subscribers as $subscriber)
				{
					$subscriberEmails[]   = $subscriber->email;
				}
			}
			if(! empty($postSubscribers))
			{
				foreach($postSubscribers as $postSubscriber)
				{
					$postSubscriberEmails[]   = $postSubscriber->email;
				}
			}
			if(! empty($catSubscribers))
			{
				foreach($catSubscribers as $catSubscriber)
				{
					$catSubscriberEmails[]   = $catSubscriber->email;
				}
			}
		}


		// Notify Participants if this is a reply
		if( !empty( $parent ) && $config->get( 'notify_participants' ) && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{
			$participantEmails = DiscussHelper::getHelper( 'Mailer' )->_getParticipants( $post->parent_id );

			$participantEmails  = array_unique( $participantEmails );

			// merge into owneremails. dirty hacks.
			if( count( $participantEmails ) > 0 )
			{
				$newPostOwnerEmails = array_merge( $newPostOwnerEmails, $participantEmails );
			}
		}


		if( !empty( $adminEmails ) || !empty( $subscriberEmails ) || !empty( $newPostOwnerEmails ) || !empty( $postSubscriberEmails ) || $config->get( 'notify_all' ) )
		{
			$emails = array_unique(array_merge($adminEmails, $subscriberEmails, $newPostOwnerEmails, $postSubscriberEmails, $catSubscriberEmails));

			// prepare email content and information.
			$emailData						= array();
			$emailData['postTitle']			= $emailPostTitle;
			$emailData['postAuthor']		= $authorName;
			$emailData['postAuthorAvatar']	= $authorAvatar;
			$emailData['replyAuthor']		= $authorName;
			$emailData['replyAuthorAvatar']	= $authorAvatar;
			$emailData['comment']			= $post->content;
			$emailData['postContent' ]		= $post->trimEmail( $post->content );
			$emailData['replyContent']		= $post->trimEmail( $post->content );

			$attachments	= $post->getAttachments();
			$emailData['attachments']	= $attachments;

			// get the correct post id in url, the parent post id should take precedence
			$postId	= empty( $parent ) ? $post->id : $parentTable->id;

			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $postId, false, true);

			if( $config->get( 'notify_all' ) && $post->published == DISCUSS_ID_PUBLISHED )
			{
				$emailData['emailTemplate']	= 'email.subscription.site.new.php';
				$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $post->id , $post->title);
				DiscussHelper::getHelper( 'Mailer' )->notifyAllMembers( $emailData, $newPostOwnerEmails );
			}
			else
			{
				//insert into mailqueue
				foreach ($emails as $email)
				{

					if ( in_array($email, $subscriberEmails) || in_array($email, $postSubscriberEmails) || in_array($email, $newPostOwnerEmails) )
					{
						$doContinue = false;

						// these are subscribers
						if (!empty($subscribers))
						{
							foreach ($subscribers as $key => $value)
							{
								if ($value->email == $email)
								{
									$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscribers[$key], true, true);
									$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
									$doContinue = true;
									break;
								}
							}
						}

						if( $doContinue )
							continue;

						if (!empty($postSubscribers))
						{

							foreach ($postSubscribers as $key => $value)
							{
								if ($value->email == $email)
								{

									$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $postSubscribers[$key], true, true);
									$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
									$doContinue = true;
									break;
								}
							}
						}

						if( $doContinue )
							continue;


						if (!empty($newPostOwnerEmails))
						{

							$emailSubject = JText::sprintf( 'COM_EASYDISCUSS_NEW_POST_ADDED', $emailPostTitle, $post->id );

							foreach ($newPostOwnerEmails as $ownerEmail)
							{

								//$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $ownerEmail, true, true);
								$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
								$doContinue = true;
								break;
							}
						}

					}
					else
					{

						// non-subscribers will not get the unsubscribe link
						$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
					}
				}
			}
		}
	}

	public static function getUserRepliesHTML( $postId, $excludeLastReplyUser	= false)
	{
		$model		= ED::model( 'Posts' );
		$replies	= $model->getUserReplies($postId, $excludeLastReplyUser);

		$html = '';
		if( !empty( $replies ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'replies'	, $replies );
			$html	=  $tpl->fetch( 'main.item.replies.php' );
		}

		return $html;
	}

	public static function getUserAcceptedReplyHTML( $postId )
	{
		$model	= JED::model( 'Posts' );
		$reply	= $model->getAcceptedReply( $postId );

		$html	= '';
		if( ! empty( $reply ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'reply'	, $reply );
			$html	=  $tpl->fetch( 'main.item.answered.php' );
		}

		return $html;
	}

	public static function isSiteSubscribed( $userId )
	{
		if( !class_exists( 'EasyDiscussModelSubscribe') )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'subscribe' , DISCUSS_MODELS );
		}
		$model	= ED::model( 'Subscribe' );

		$user	= JFactory::getUser( $userId );

		$subscription = array();
		$subscription['type']	= 'site';
		$subscription['email']	= $user->email;
		$subscription['cid']	= 0;

		$result = $model->isSiteSubscribed( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isPostSubscribed( $userId, $postId )
	{
		$model	= ED::model( 'Subscribe' );

		$user	= JFactory::getUser( $userId );

		$subscription = array();
		$subscription['type']	= 'post';
		$subscription['userid']	= $user->id;
		$subscription['email']	= $user->email;
		$subscription['cid']	= $postId;

		$result = $model->isPostSubscribedEmail( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isMySubscription( $userid, $type, $subId)
	{
		$model 		= ED::model( 'Subscribe' );
		return $model->isMySubscription($userid, $type, $subId);
	}

	public static function hasPassword( $post )
	{
		$session	= JFactory::getSession();
		$password	= $session->get( 'DISCUSSPASSWORD_' . $post->id , '' , 'com_easydiscuss' );

		if( $password == $post->password )
		{
			return true;
		}
		return false;
	}

	public static function getUserComponent()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';
	}

	public static function getUserComponentLoginTask()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'user.login' : 'login';
	}

	public static function getAccessibleCategories( $parentId = 0, $type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $customUserId = '' )
	{
		static $accessibleCategories = array();

		if( !empty($customUserId) )
		{
			$my = JFactory::getUser( $customUserId );
		}
		else
		{
			$my	= JFactory::getUser();
		}

		// $sig 	= serialize( array($type, $my->id, $parentId) );

		$sig    = (int) $my->id . '-' . (int) $parentId . '-' . (int) $type;


		//if( !array_key_exists($sig, $accessibleCategories) )
		if(! isset( $accessibleCategories[$sig] ) )
		{

			$db	= DiscussHelper::getDBO();

			$gids		= '';
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__discuss_category` as a';


			if( $my->id == 0 )
			{
				$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0') . ' OR ';
			}
			else
			{
				$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0') . ' OR a.`private` = ' . $db->Quote('1') . ' OR ';
			}


			$gid	= array();
			$gids	= '';

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$gid    = array();
				if( $my->id == 0 )
				{
					$gid 	= JAccess::getGroupsByUser(0, false);
				}
				else
				{
					$gid 	= JAccess::getGroupsByUser($my->id, false);
				}
			}
			else
			{
				$gid	= DiscussHelper::getUserGids();
			}


			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}

				$catQuery   .=	'  a.`id` IN (';
				$catQuery .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
				$catQuery .= '			where b.`category_id` = a.`id` and b.`acl_id` = '. $db->Quote( $type );
				$catQuery .= '			and b.`type` = ' . $db->Quote('group');
				$catQuery .= '			and b.`content_id` IN (' . $gids . ')';

				//logged in user
				if( $my->id != 0 )
				{
					$catQuery .= '			union ';
					$catQuery .= '			select b.`category_id` from `#__discuss_category_acl_map` as b';
					$catQuery .= '				where b.`category_id` = a.`id` and b.`acl_id` = ' . $db->Quote( $type );
					$catQuery .= '				and b.`type` = ' . $db->Quote('user');
					$catQuery .= '				and b.`content_id` = ' . $db->Quote( $my->id );
				}
				$catQuery   .= ')';

			}

			$catQuery   .= ')';
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();

			$accessibleCategories[ $sig ] = $result;

		}

		return $accessibleCategories[ $sig ];
	}

	public static function getPrivateCategories( $acltype = DISCUSS_CATEGORY_ACL_ACTION_VIEW )
	{
		$db 			= DiscussHelper::getDBO();
		$my 			= JFactory::getUser();
		static $result	= array();

		$excludeCats	= array();

		$sig    = (int) $my->id . '-' . (int) $acltype;

		if(! isset( $result[ $sig ] ) )
		{
			if($my->id == 0)
			{
				$catQuery	= 	'select distinct a.`id`, a.`private`';
				$catQuery	.=  ' from `#__discuss_category` as a';
				$catQuery	.=	' 	left join `#__discuss_category_acl_map` as b on a.`id` = b.`category_id`';
				$catQuery	.=	' 		and b.`acl_id` = ' . $db->Quote( $acltype );
				$catQuery	.=	' 		and b.`type` = ' . $db->Quote( 'group' );
				$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

				$gid	= array();
				$gids	= '';


				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					// $gid	= JAccess::getGroupsByUser(0, false);

					$gid	= DiscussHelper::getUserGroupId($my);
				}
				else
				{
					$gid	= DiscussHelper::getUserGids();
				}

				if( count( $gid ) > 0 )
				{
					foreach( $gid as $id)
					{
						$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
					}
					$catQuery	.= ' and a.`id` NOT IN (';
					$catQuery	.= '     SELECT c.category_id FROM `#__discuss_category_acl_map` as c ';
					$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( $acltype );
					$catQuery	.= '        AND c.type = ' . $db->Quote('group');
					$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
				}

				$db->setQuery($catQuery);
				$result = $db->loadObjectList();
			}
			else
			{
				$result = ED::getAclCategories ( $acltype, $my->id );
			}

			for($i=0; $i < count($result); $i++)
			{
				$item =& $result[$i];
				$item->childs = null;

				DiscussHelper::buildNestedCategories($item->id, $item, true);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}

			$result[ $sig ] = $excludeCats;
		}

		return $result[ $sig ];
	}

	public static function getAclCategories ($type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $userId = '', $parentId = false)
	{
		static $categories = array();

		//$sig = serialize( array($type, $userId, $parentId) );
		$sig = (int) $type . '-' . (int) $userId . '-' . (int) $parentId;

		//if( !array_key_exists($sig, $categories) )
		if (!isset($categories[$sig])) {
			$db = ED::db();
			$gid = JAccess::getGroupsByUser($userId, false);

			if (ED::getJoomlaVersion() >= '1.6') {
				if ($userId == '') {
					$gid = JAccess::getGroupsByUser(0, false);
				} else {
					$gid = JAccess::getGroupsByUser($userId, false);
				}
			}

			$gids = '';
			if (count($gid) > 0) {
				$gids = implode( ',', $gid );
			}

			$query = 'select c.`id` from `#__discuss_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
			$query .= '			where b.`category_id` = c.`id` and b.`acl_id` = '. $db->Quote($type);
			$query .= '			and b.`type` = ' . $db->Quote('group');
			$query .= '			and b.`content_id` IN (' . $gids . ')';

			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote(DISCUSS_PRIVACY_ACL);
			if( $parentId !== false )
				$query .= ' and c.`parent_id` = ' . $db->Quote($parentId);

			$db->setQuery($query);

			$categories[$sig] = $db->loadObjectList();
		}

		return $categories[$sig];
	}

	/**
	 * Renders a JTable object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function table($name, $prefix = 'Discuss', $config = array())
	{
		// Sanitize and prepare the table class name.
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$className = $prefix . ucfirst($type);

		// Only try to load the class if it doesn't already exist.
		if (!class_exists($className)) {

			// Search for the class file in the JTable include paths.
			$path = DISCUSS_TABLES . '/' . strtolower($type) . '.php';

			// Import the class file.
			include_once($path);
		}

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Retrieves the model
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The model's name.
	 * @return	mixed
	 */
	public static function model($name)
	{
		static $models = array();

		$key = $name;

		if (!isset($models[$key])) {

			$file = strtolower($name) . '.php';

			$path = DISCUSS_MODELS . '/' . $file;

			if (!JFile::exists($path)) {
				return JError::raiseWarning(500, JText::sprintf('Requested model %1$s is not found.', $file));
			}

			$className = 'EasyDiscussModel' . ucfirst($name);

			if (!class_exists($className)) {
				require_once($path);
			}

			$models[$key] = new $className();
		}

		return $models[$key];
	}

	/**
	 * Retrieves the pagination object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature])) {
			$pagination = ED::pagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = $pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieve @JUser object based on the given email address.
	 *
	 * @access	public
	 * @param	string $email	The user's email address.
	 * @return	JUser			@JUser object.
	 **/
	public static function getUserByEmail( $email )
	{
		$email	= strtolower( $email );

		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__users' ) . ' '
				. 'WHERE LOWER(' . $db->nameQuote( 'email' ) . ') = ' . $db->Quote( $email );
		$db->setQuery( $query );
		$id		= $db->loadResult();

		if( !$id )
		{
			return false;
		}

		return JFactory::getUser( $id );
	}

	public static function getUserGids( $userId = '' )
	{
		$userId = empty($userId) ? null : $userId;
		$user = JFactory::getUser($userId);

		$groups = JAccess::getGroupsByUser($user->id);
		$ids = array();

		foreach ($groups as $group) {
			$ids[] = $group;
		}

		return $ids;
	}

	public static function getJoomlaUserGroups( $cid = '' )
	{
		$db = DiscussHelper::getDBO();

		if(ED::getJoomlaVersion() >= '1.6')
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

		// Condition
		$where  = array();

		// We need to filter out the ROOT and USER dummy records.
		if(ED::getJoomlaVersion() < '1.6')
		{
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query  .= $where;

		// Grouping and ordering
		if( ED::getJoomlaVersion() >= '1.6' )
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

		if( DiscussHelper::getJoomlaVersion() < '1.6' )
		{
			$guest = new stdClass();
			$guest->id		= '0';
			$guest->name	= 'Public';
			$guest->level	= '0';
			array_unshift( $result, $guest );
		}

		return $result;
	}

	public static function getUnsubscribeLink($subdata, $external = false, $html = false)
	{
		$unsubdata	= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->userid."\r\ntoken=".md5($subdata->id.$subdata->created));

		$link = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$unsubdata, false, $external);

		return $link;
	}

	/*
	 * Return class name according to user's group.
	 * e.g. 'reply-usergroup-1 reply-usergroup-2'
	 *
	 */
	public static function userToClassname($jUserObj, $classPrefix = 'reply', $delimiter = '-')
	{
		if (is_numeric($jUserObj))
		{
			$jUserObj	= JFactory::getUser($jUserObj);
		}

		if( !$jUserObj instanceof JUser )
		{
			return '';
		}

		static $classNames;

		if (!isset($classNames))
		{
			$classNames = array();
		}

		$signature = serialize(array($jUserObj->id, $classPrefix, $delimiter));

		if (!isset($classNames[$signature]))
		{
			$classes	= array();

			$classes[]	= $classPrefix . $delimiter . 'user' . $delimiter . $jUserObj->id;

			if (property_exists($jUserObj, 'gid'))
			{
				$classes[]	= $classPrefix . $delimiter . 'usergroup' . $delimiter . $jUserObj->get( 'gid' );
			}
			else
			{
				$groups		= $jUserObj->getAuthorisedGroups();

				foreach($groups as $id)
				{
					$classes[] = $classPrefix . $delimiter . 'usergroup' . $delimiter . $id;
				}
			}

			$classNames[$signature] = implode(' ', $classes);
		}

		return $classNames[$signature];
	}

	/**
	 * Ensures that the user is logged in
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function requireLogin()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if ($user->guest) {
			ED::setMessageQueue(JText::_( 'COM_EASYDISCUSS_SIGNIN_PLEASE_LOGIN' ), 'info');
			return $app->redirect(EDR::_('view=index', false));
		}
	}

	/**
	 * Retrieve similar question based on the keywords
	 *
	 * @access	public
	 * @param	string	$keywords
	 */
	public static function getSimilarQuestion( $text = '' )
	{

		$config = ED::config();

		if (empty($text)) {
			return '';
		}

		if (! $config->get('main_similartopic', 0)){
			return '';
		}


		$options = array();
		$options['limi'] = $config->get('main_similartopic_limit', '5');
		$options['includePrivatePost'] = $config->get('main_similartopic_privatepost', 0);

		$model = ED::model('Posts');
		$posts = $model->getSimilarQuestion($text, $options);

		if ($posts) {
			//preload posts
			$posts = ED::formatPost($posts);
		}

		return $posts;

	}

	/**
	 * Retrieves the html block for board statistics.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function getBoardStatistics()
	{
		$config = ED::config();
		$allowed = true;
		$disallowedGroups = $config->get('main_exclude_frontend_statistics');

		if (!$config->get('main_frontend_statistics')) {
			return;
		}

		if (!empty($disallowedGroups)) {

			//Remove whitespace
			$disallowedGroups = trim($disallowedGroups);
			$disallowedGroups = explode(',', $disallowedGroups);

			$my = JFactory::getUser();
			$groups = $my->groups;

			$result = array_intersect($groups, $disallowedGroups);

			$allowed = !$result ? true : false;
		}

		if (!$allowed) {
			return;
		}

		$theme = ED::themes();

		$postModel = ED::model('Posts');
		$totalPosts	= $postModel->getTotal();

		$resolvedPosts = $postModel->getTotalResolved();
		$unresolvedPosts = $postModel->getUnresolvedCount();

		$userModel = ED::model('Users');
		$totalUsers	= $userModel->getTotalUsers();


		$ids = $userModel->getLatestUser();
		$latestMember = ED::user($ids);

		// Total guests
		$totalGuests = $userModel->getTotalGuests();

		// Online users
		$onlineUsers = $userModel->getOnlineUsers();

		$theme->set('latestMember', $latestMember);
		$theme->set('unresolvedPosts', $unresolvedPosts);
		$theme->set('resolvedPosts', $resolvedPosts);
		$theme->set('totalUsers', $totalUsers);
		$theme->set('totalPosts', $totalPosts);
		$theme->set('onlineUsers', $onlineUsers);
		$theme->set('totalGuests', $totalGuests);

		return $theme->output('site/frontpage/stats');
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
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/user/user.php';
		include_once($path);

		return EasyDiscussUser::factory($ids, $debug);
	}

	/**
	 * Retrieve the html block for who's viewing this page.
	 *
	 * @access	public
	 * @param	string	$url
	 */
	public static function getWhosOnline($uri = '')
	{
		$config = ED::config();
		$enabled = $config->get('main_viewingpage');

		if (!$enabled) {
			return;
		}

		// Default hash
		$hash = md5(JRequest::getURI());

		if (!empty($uri)) {
			$hash = md5($uri);
		}

		$model = ED::model('Users');
		$users = $model->getPageViewers($hash);

		if (!$users) {
			return;
		}

		$theme = ED::themes();
		$theme->set('users', $users);

		return $theme->output('site/post/default.viewers');
	}

	public static function getListLimit()
	{
		$app		= JFactory::getApplication();
		$default 	= ED::jconfig()->get( 'list_limit' );

		if( $app->isAdmin() )
		{
			return $default;
		}

		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$limit	= -2;

		if( is_object( $menu ) )
		{
			$params	= DiscussHelper::getRegistry( $menu->params );
			$limit	= $params->get( 'limit' , '-2' );
		}

		if( $limit == '-2' )
		{
			// Use default configurations.
			$config	= DiscussHelper::getConfig();
			$limit	= $config->get( 'layout_list_limit', '-2' );
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2' )
		{
			$limit		= $default;
		}

		return $limit;
	}

	public static function getRegistrationLink()
	{
		$config	= DiscussHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=register' );
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=registration' );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'joomla':
				$link	= $default;
				break;

			case 'cb':
				$link	= JRoute::_( 'index.php?option=com_comprofiler&task=registers' );
				break;

			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$link = FRoute::registration();
				} else {
					$link = $default;
				}

				break;

			case 'jomsocial':
 				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
				$file 	= JPATH_ROOT . '/components/com_community/libraries/core.php';

				if (JFile::exists($file)) {
					require_once( $file );
					$link 	= CRoute::_( 'index.php?option=com_community&view=register' );
				}
			break;
		}

		return $link;
	}

	public static function getEditProfileLink()
	{
		$config	= ED::config();

		$link = EDR::_('view=profile&layout=edit');

		if ($config->get('integration_easysocial_toolbar_profile')) {
			$easysocial = ED::easysocial();

			if ($easysocial->exists()) {
				$link = FRoute::profile(array('layout' => 'edit'));
			}
		}

		return $link;
	}

	public static function getResetPasswordLink()
	{
		$config 	= DiscussHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=reset' );

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=reset' );
		}


		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$link = FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				} else {
					$link = $default;
				}
				break;
			case 'joomla':
			case 'cb':
			case 'jomsocial':
			default:
				$link	= $default;
				break;
		}

		return $link;
	}

	public static function getDefaultRepliesSorting()
	{
		$config = ED::config();
		$defaultFilter = $config->get('layout_replies_sorting');

		if ($defaultFilter == 'voted' && !$config->get('main_allowvote')) {
			$defaultFilter = 'oldest';
		}

		if ($defaultFilter == 'likes' && !$config->get('main_likes_replies')) {
			$defaultFilter = 'oldest';
		}

		return $defaultFilter;
	}

	/**
	 * Allows caller to set the page title.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setPageTitle($text = '')
	{
		$text = JText::_($text);

		// now check if site name is needed or not.
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		$menu = $app->getMenu();
		$item = $menu->getActive();

		if( empty( $text ) )
		{
			// use menu item title
			if( is_object( $item ) )
			{
				$params			= $item->params;

				if(! $params instanceof JRegistry )
				{
					$params			= DiscussHelper::getRegistry( $item->params );
				}

				$text = 	$params->get('page_title', '');

				if( empty( $text ) )
				{
					if( isset( $item->title ) )
					{
						$text = 	$item->title;
					}
					else
					{
						$text = 	$item->name;
					}
				}
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($text)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$text = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $text);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$text = JText::sprintf('JPAGETITLE', $text, $app->getCfg('sitename'));
		}

		$doc->setTitle($text);
	}


	public static function setMeta()
	{
		$config	= DiscussHelper::getConfig();
		$db		= DiscussHelper::getDBO();


		$menu	= JFactory::getApplication()->getMenu();
		$item	= $menu->getActive();

		$result	= new stdClass();
		$result->description	= $config->get( 'main_description' );
		$result->keywords		= '';

		$description 	= '';

		if( is_object( $item ) )
		{
			$params			= $item->params;

			if(! $params instanceof JRegistry )
			{
				$params			= DiscussHelper::getRegistry( $item->params );
			}

			$description	= $params->get( 'menu-meta_description' , '' );
			$keywords		= $params->get( 'menu-meta_keywords' , '' );

			if( ! empty ( $description ) )
			{
				$result->description	= $description;
			}

			if( ! empty ( $keywords ) )
			{
				$result->keywords	= $keywords;
			}
		}

		$document = JFactory::getDocument();
		if ( empty( $result->keywords ) && empty( $result->description ) )
		{
			// Get joomla default description.
			$jConfig	= ED::jconfig();
			$joomlaDesc	= $jConfig->get('MetaDesc');

			$metaDesc	= $description . ' - ' . $joomlaDesc;
			$document->setMetadata('description', $metaDesc);
		}
		else
		{
			if ( !empty( $result->keywords ) )
			{
				$document->setMetadata('keywords', $result->keywords);
			}

			if ( !empty( $result->description ) )
			{
				$document->setMetadata('description', $result->description);
			}
		}
	} //end function setMeta

	public static function getFrontpageCategories()
	{
		$catModel		= ED::model( 'Categories' );
		$newPostCount	= 0;

		if( !$categories = $catModel->getCategories() )
		{
			return array();
		}

		foreach ($categories as $category)
		{
			$postModel = ED::model( 'Posts' );
			$category->newCount = $postModel->getNewCount( '' , $category->id , null , false );
			$newPostCount += $category->newCount;
		}

		// Temporary store in user state.
		$app = JFactory::getApplication();
		$app->setUserState( 'com_easydiscuss.helper.totalnewpost', $newPostCount );

		return $categories;
	}

	public static function log( $var = '', $force = 0 )
	{
		$debugroot = DISCUSS_HELPERS . '/debug';

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
	 * Determines if the user is a moderator of the forum
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isModerator($categoryId = null, $userId = null)
	{
		$moderator = ED::moderator()->isModerator($categoryId, $userId);

		return $moderator;
	}

	public static function getUserGroupId(JUser $user)
	{
		$groups = JAccess::getGroupsByUser($user->id);

		return $groups;
	}

	/**
	 * Method determines if the content needs to be parsed through any parser or not.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The content's string.
	 */
	public static function parseContent( $content, $forceBBCode=false )
	{
		$config = ED::config();

		$content = ED::string()->escape($content);

		// Pass it to bbcode parser.
		$content = ED::parser()->bbcode( $content );
		$content = nl2br($content);

		//Remove BR in pre tag
		$content = preg_replace_callback('/<pre.*?\>(.*?)<\/pre>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );
		$content = preg_replace_callback('/<ol.*?\>(.*?)<\/ol>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );
		$content = preg_replace_callback('/<ul.*?\>(.*?)<\/ul>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );

		$content = str_ireplace("</pre><br />", '</pre>', $content);
		$content = str_ireplace("</ol><br />", '</ol>', $content);
		$content = str_ireplace("</ol>\r\n<br />", '</ol>', $content);
		$content = str_ireplace("</ul><br />", '</ul>', $content);
		$content = str_ireplace("</ul>\r\n<br />", '</ul>', $content);
		$content = str_ireplace("</pre>\r\n<br />", '</pre>', $content);
		$content = str_ireplace("</blockquote><br />", '</blockquote>', $content);
		$content = str_ireplace("</blockquote>\r\n<br />", '</blockquote>', $content);

		return $content;
	}

	/**
	 * Triggers plugins.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function triggerPlugins( $type , $eventName , &$data ,$hasReturnValue = false )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/events/events.php';
		include_once($path);

		EasyDiscussEvents::importPlugin($type);

		$args = array( 'post' , &$data );

		$returnValue = call_user_func_array( 'EasyDiscussEvents::' . $eventName , $args );

		if ($hasReturnValue) {
			return trim( implode( "\n" , $returnValue ) );
		}

		return;
	}

	/**
	 * Renders a module in the component
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderModule($position, $attributes = array(), $content = null)
	{
		jimport('joomla.application.module.helper');

		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');
		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		foreach ($modules as $module) {

			// Get the module output
			$output = $renderer->render($module, $attributes, $content);

			$theme = ED::themes();
			$theme->set('position', $position);
			$theme->set('output', $output);

			$buffer .= $theme->output('site/widgets/module');
		}

		return $buffer;
	}

	public static function getEditorType( $type = '' )
	{
		// Cater for #__discuss_posts column content_type
		$config = ED::getConfig();

		if ($config->get('layout_editor') == 'bbcode') {
			return 'bbcode';
		} else {
			return 'html';
		}
	}

	/**
	 * Formats the content of a post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatContent($post)
	{
		$config = ED::config();

		// @ 4.0 we need to check if this post has the 'preview' or not. if yes, we just use it. if not, lets format it.
		if ($post->preview) {

			// Apply word censorship on the content
			$content = ED::badwords()->filter($post->preview);

			return $content;
		}

		// Determine the current editor
		$editor = $config->get('layout_editor', 'bbcode');

		// If the post is bbcode source and the current editor is bbcode
		if (($post->content_type == 'bbcode' || is_null($post->content_type)) && $editor == 'bbcode') {

			$content = $post->content;

			// Allow syntax highlighter even on html codes.
			$content = ED::parser()->replaceCodes($content);

			$content = ED::parser()->bbcode($content , true);

			// Since this is a bbcode content and source, we want to replace \n with <br /> tags.
			$content = nl2br($content);
		}

		// If the admin decides to switch from bbcode to wysiwyg editor, we need to format it back
		if( $post->content_type == 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content;

			//strip this kind of tag -> &nbsp; &amp;
			$content = strip_tags(html_entity_decode($content));

			// Since the original content is bbcode, we don't really need to do any replacements.
			// Just feed it in through bbcode formatter.
			$content	= ED::parser()->bbcode( $content );
		}

		// If the admin decides to switch from wysiwyg to bbcode, we need to fix the content here.
		if( $post->content_type != 'bbcode' && !is_null($post->content_type) && $editor == 'bbcode' )
		{
			$content	= $post->content;

			// Switch html back to bbcode
			$content 	= ED::parser()->html2bbcode( $content );

			// Update the quote messages
			$content 	= ED::parser()->quoteBbcode( $content );
		}

		// If the content is from wysiwyg and editor is also wysiwyg, we only do specific formatting.
		if( $post->content_type != 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content;

			// Allow syntax highlighter even on html codes.
			$content 	= ED::parser()->replaceCodes( $content );
		}

		// Apply word censorship on the content
		$content	= ED::badwords()->filter($content);

		return $content;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			require_once(__DIR__ . '/cache/cache.php');

			$cache = new EasyDiscussCache();
		}

		return $cache;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function rest()
	{
		static $rest = null;

		if (!$rest) {
			require_once(__DIR__ . '/rest/rest.php');

			$rest = new EasyDiscussRest();
		}

		return $rest;
	}


	/**
	 * include akisment class file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function akismet()
	{
		if (! class_exists('Akismet')) {
			require_once(__DIR__ . '/akismet/akismet.php');
		}
	}

	/**
	 * Ensures that the token is valid
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkToken($method = 'post')
	{
		JRequest::checkToken($method) or die('Invalid Token');
	}

	// For displaying on frontend
	public static function bbcodeHtmlSwitcher( $post = '', $type = '', $isEditing = false )
	{
		$config = ED::config();

		if( $type == 'signature' || $type == 'description' ) {
			$temp = $post;
			$post = new stdClass();
			$post->content = $temp;
			$post->content_type = 'bbcode';
			$editor = 'bbcode';
		} else {
			$editor = $config->get( 'layout_editor' );
		}

		if ($editor != 'bbcode') {
			$editor = 'html';
		}

		if ($post->content_type == 'bbcode') {
			if ($editor == 'bbcode') {

				$content = $post->content;

				//If content_type is bbcode and editor is bbcode
				if (! $isEditing) {
					$content = ED::parser()->bbcode($content);
					$content = ED::parser()->removeBrTag($content);
				}
			} else {
				//If content_type is bbcode and editor is html
				// Need content raw to work
				$content = $post->post->content;
				$content = ED::parser()->bbcode($content);
				$content = ED::parser()->removeBrTag($content);
			}
		} else {
			// content_type is html

			if ($editor == 'bbcode') {

				$content = $post->content;

				//If content_type is html and editor is bbcode
				if ($isEditing) {
					$content = ED::parser()->quoteBbcode($content);
					$content = ED::parser()->smiley2bbcode($content); // we need to parse smiley 1st before we parse htmltobbcode.
					$content = ED::parser()->html2bbcode($content);
				} else {

					//Quote all bbcode here
					$content = ED::parser()->quoteBbcode($content);
				}
			} else {
				//If content_type is html and editor is html
				$content = $post->content;
			}
		}

		// Apply censorship
		$content = ED::badwords()->filter($content);

		return $content;
	}

	public static function getLoginLink($returnURL = '')
	{
		if (!empty($returnURL)) {
			$returnURL = '&return=' . $returnURL;
		}

		$link = DiscussRouter::_('index.php?option=com_users&view=login' . $returnURL);

		return $link;
	}

	public static function getPostStatusAndTypes( $posts = null)
	{
		if (empty($posts)) {
			return;
		}

		foreach ($posts as $post) {
			$user = ED::user($post->getOwner()->id);

			$post->badges = $user->getBadges();

			// Translate post status from integer to string
			switch($post->post_status) {
				case '0':
					$post->post_status_class = '';
					$post->post_status = '';
					break;
				case '1':
					$post->post_status_class = '-on-hold';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ON_HOLD' );
					break;
				case '2':
					$post->post_status_class = '-accept';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ACCEPTED' );
					break;
				case '3':
					$post->post_status_class = '-working-on';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_WORKING_ON' );
					break;
				case '4':
					$post->post_status_class = '-reject';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_REJECT' );
					break;
				default:
					$post->post_status_class = '';
					$post->post_status = '';
					break;
			}

			$alias = $post->post_type;
			$modelPostTypes = ED::model('Posttypes');

			// Get each post's post status title
			$title = $modelPostTypes->getTitle($alias);
			$post->post_type = $title;

			// Get each post's post status suffix
			$suffix = $modelPostTypes->getSuffix($alias);
			$post->suffix = $suffix;
		}

		return $posts;
	}

	/**
	 * Determines if the user falls under the moderation threshold
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isModerateThreshold($userId = null)
	{
		$user = ED::user($userId);

		return $user->moderateUsersPost();
	}

	/**
	 * Backwards compatibility purpose
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function __callStatic($method, $args)
	{
		// If the called method exists in the legacy, we should just use it
		if (method_exists('EDLegacy', $method)) {
			return call_user_func_array(array('EDLegacy', $method), $args);
		}

		// Here, we are under the assumption, the library exists
		$file = dirname(__FILE__) . '/' . strtolower($method) . '/' . strtolower($method) . '.php';

		require_once($file);

		$class = 'EasyDiscuss' . ucfirst($method);

		if (count($args) == 1) {
			$args = $args[0];
		}

		if (!$args) {
			$args = null;
		}

		$obj = new $class($args);

		return $obj;
	}

	/**
	 * Gets the current timezone of the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getTimeZone()
	{
		// Get server's timezone
		$user = JFactory::getUser();
		$jconfig = ED::jconfig();
		$timezone = $jconfig->get('offset');

		// If user is a member, get their timezone
		if ($user->id) {
			$timezone = $user->getParam('timezone', $timezone);
		}

		return $timezone;
	}

	/**
	 * Gets the current timezone of the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getTimeZoneOffset()
	{
		$date = ED::date();

		$timezone = ED::getTimeZone();
		$timezone = new DateTimeZone($timezone);

		$date->setTimezone($timezone);

		$offset = $date->getOffsetFromGmt(true);

		return $offset;
	}


	/**
	 * Renders the DiscussProfile table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function profile($id = null)
	{
		$user = ED::user($id);
		return $user;
	}

	public static function validateUserType($usertype)
	{
		$config = ED::config();
		$acl = ED::acl();

		switch($usertype)
		{
			case 'guest':
				$enable = $acl->allowed('add_reply', 0);
				break;
			case 'twitter':
				$enable = $config->get('integration_twitter_enable');
				break;
			case 'facebook':
				$enable = $config->get('integration_facebook_enable1');
				break;
			case 'linkedin':
				$enable = $config->get('integration_linkedin_enable1');
				break;
			default:
				$enable = false;
		}

		return $enable;
	}

	/**
	 * Retrieves the default avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getDefaultAvatar()
	{
		$uri = rtrim(JURI::root(), '/');
		$file = '/media/com_easydiscuss/images/default_avatar.png';

		// @TODO: Allow overrides

		$uri = $uri . $file;

		return $uri;
	}

	public static function getThemeObject($name)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$file = DISCUSS_THEMES . '/' . $name . '/config.xml';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		$parser = JFactory::getXML($file);

		$obj = new stdClass();
		$obj->element = $name;
		$obj->name = $name;
		$obj->path = $file;
		$obj->writable = is_writable($file);
		$obj->created = JText::_('Unknown');
		$obj->updated = JText::_('Unknown');
		$obj->author = JText::_('Unknown');
		$obj->version = JText::_('Unknown');
		$obj->desc = JText::_('Unknown');

		if (ED::isJoomla30()) {

			$childrens = $parser->children();

			foreach ($childrens as $key => $value) {
				if ($key == 'description') {
					$key = 'desc';
				}

				$obj->$key = (string) $value;
			}

			$obj->path = $file;
		} else {

			$contents = JFile::read($file);

			$parser = JFactory::getXMLParser('Simple');
			$parser->loadString($contents);

			$created = $parser->document->getElementByPath('created');
			if ($created) {
				$obj->created = $created->data();
			}

			$updated = $parser->document->getElementByPath('updated');
			if ($updated) {
				$obj->updated = $updated->data();
			}

			$author = $parser->document->getElementByPath('author');
			if ($author) {
				$obj->author = $author->data();
			}

			$version = $parser->document->getElementByPath('version');
			if ($version) {
				$obj->version = $version->data();
			}

			$description = $parser->document->getElementByPath('description');
			if ($description)
			{
				$obj->desc = $description->data();
			}

			$obj->path = $file;
		}

		return $obj;
	}

	/**
	 * Parses a csv file to array of data
	 *
	 * @since	4.0
	 * @param	string	Filename to parse
	 * @return	Array	Arrays of the data
	 */
	public static function parseCSV($file, $firstRowName = true, $firstColumnKey = true)
	{
		if (!JFile::exists($file)) {
			return array();
		}

		$handle = fopen($file, 'r');

		$line = 0;

		$columns = array();

		$data = array();

		while (($row = fgetcsv($handle)) !== false) {

			if ($firstRowName && $line === 0) {
				$columns = $row;
			} else {
				$tmp = array();

				if ($firstRowName) {
					foreach ($row as $i => $v) {
						$tmp[$columns[$i]] = $v;
					}
				} else {
					$tmp = $row;
				}

				if ($firstColumnKey) {
					if ($firstRowName) {
						$data[$tmp[$columns[0]]] = $tmp;
					} else {
						$data[$tmp[0]] = $tmp;
					}
				} else {
					$data[] = $tmp;
				}
			}

			$line++;
		}

		fclose($handle);

		return $data;
	}

	/**
	 * Includes a file given a particular namespace in POSIX format.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	$file		Eg: admin:/tables/table will include /administrator/components/com_easydiscuss/tables/table.php
	 * @return	boolean				True on success false otherwise
	 */
	public static function import( $namespace )
	{
		static $locations	= array();

		if( !isset( $locations[ $namespace ] ) )
		{
			// Explode the parts to know exactly what to lookup for
			$parts		= explode( ':' , $namespace );

			// Non POSIX standard.
			if( count( $parts ) <= 1 )
			{
				return false;
			}

			$base 		= $parts[ 0 ];

			switch( $base )
			{
				case 'admin':
					$basePath	= DISCUSS_ADMIN_ROOT;
				break;
				case 'site':
				default:
					$basePath	= DISCUSS_ROOT;
				break;
			}

			// Replace / with proper directory structure.
			$path 		= str_ireplace( '/' , DIRECTORY_SEPARATOR , $parts[ 1 ] );

			// Get the absolute path now.
			$path 		= rtrim($basePath, '/') . '/' . $path . '.php';

			// Include the file now.
			include_once( $path );

			$locations[ $namespace ]	= true;
		}

		return true;
	}

	/**
	 * Generates the query string for language selection.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLanguageQuery($column = 'language')
	{
		$language = JFactory::getLanguage();
		$tag = $language->getTag();
		$query = '';

		$column = (!$column)? 'language' : $column;

		if (!empty($tag) && $tag != '*') {
			$db = ED::db();
			$query = ' (' . $db->qn($column) . '=' . $db->Quote($tag) . ' OR ' . $db->qn($column) . '=' . $db->Quote('') . ' OR ' . $db->qn($column) . '=' . $db->Quote('*') . ')';
		}

		return $query;
	}
}

// Backwards compatibility
class DiscussHelper extends ED {}
