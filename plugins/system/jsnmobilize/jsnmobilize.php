<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting( 1 );
// Load client device detection library

/**
 * System plugin for initializing JSN Mobilize.
 *
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @since       1.0.0
 */
class PlgSystemJSNMobilize extends JPlugin {

	/**
	 * Application object.
	 *
	 * @var  $_app  object  An instance of JApplication class.
	 */
	private static $_app;

	/**
	 * JSN Mobilize global configuration.
	 *
	 * @var  $_cfg  object  An instance of JObject class.
	 */
	private static $_cfg;

	/**
	 * Real request URI after parsing for user preference.
	 *
	 * @var  $_request  string
	 */
	private static $_request;

	/**
	 * Detected user preference.
	 *
	 * @var  $_device  string
	 */
	private static $_device;

	protected static $mode_sef;

	/**
	 * Event handler to get user preference.
	 *
	 * @return  void
	 */
	public function onAfterInitialise() {
		// Initialize JSN Mobilize

		require_once dirname( __FILE__ ) . '/define.php';
		if ( ! class_exists( 'JSNConfigHelper' ) ) {
			return;
		}
		
		self::$_app = JFactory::getApplication();
		$get = self::$_app->input->getArray($_GET);
		if ( ! self::$_app->isAdmin() ) {
			require_once dirname( __FILE__ ) . '/libraries/joomlashine/client/mobiledetect.php';
			$detect = new JSN_Mobile_Detect;
			$deviceType = ( $detect->isMobile() ? 'mobilize' : 'desktop' );

			if(isset($get['switch_to_desktop_ui'])){
				$session =& JFactory::getSession();
				$session->set( 'switch', $get['switch_to_desktop_ui']);
			}else{
				$session =& JFactory::getSession();
				$switch = $session->get('switch');
				if(!isset($get[ 'jsn_mobilize_preview' ])){
					if($switch == 1 && $deviceType == 'mobilize'){
						$link = JURI::base().'?switch_to_desktop_ui=1';
						JFactory::getApplication()->redirect($link);
					}
				}
			}

			// Get parsed request URI object
			$jUri = JURI::getInstance();
			// Get application object

			$config = JFactory::getConfig();
			$rewrite = $config->get( "sef_rewrite" ) ? "/" : "/index.php/";
			// Get JSN Mobilize configuration
			$linkMobilize = JURI::root( true ) . $rewrite;
			self::$_cfg = JSNConfigHelper::get( 'com_mobilize');
			self::$_cfg->set( 'link_mobilize', $linkMobilize );
			// Check cookie
			$urlRequest = isset( $_SERVER[ "REQUEST_URI" ] ) ? $_SERVER[ "REQUEST_URI" ] : "";
			$getPreView = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? (int)$get[ 'jsn_mobilize_preview' ] : '';

			if ( $getPreView == 1 ) {
				$deviceType = 'mobilize';
			}
			if ( $getPreView != 1 && $deviceType == "desktop" ) {
				self::$_device = "";
			}
			// Continue only if not in administrator section
			if ( ! self::$_app->isAdmin() ) {
				// Does user prefer desktop site?
				if ( self::$_app->input->getInt( 'switch_to_desktop_ui' ) == 1 ) {
					self::$_device = 'desktop';
				}
				elseif ( self::$_app->input->getInt( 'switch_to_desktop_ui' ) == 2 ) {
					$device = $deviceType;
					self::$_device = $device;
				}
				if ( ! isset( self::$_device ) ) {
					self::$_device = $deviceType;
				}
				require_once JPATH_BASE . '/templates/jsn_mobilize/helpers/mobilize.php';
				if ( $getPreView == 1 ) {
					$mCfg = JSNMobilizeTemplateHelper::getConfig( self::$_device, true );
				}
				else {
					$mCfg = JSNMobilizeTemplateHelper::getConfig( self::$_device );
				}

				if ( ! empty( $device ) ) {
					$deviceUi = $device . "_ui_enabled";
				}
				else {
					$deviceUi = "";
				}

				// Check if mobile/tablet UI is enabled?
				if ( $getPreView != 1 && $deviceType == "desktop" && $deviceUi ) {

					if ( ! self::$_app->input->getInt( 'jsn_mobilize_preview' ) AND ! $mCfg->get( $deviceUi ) AND self::$_device == "mobilize" ) {
						self::$_device = 'desktop';
					}
				}

				// Do some preparation for mobile/tablet site rendering
				if ( self::$_device == "mobilize" ) {
					// Reparse request URI
					$jUri->parse( $jUri->toString() );
				}
				// Load language file
				$this->_loadLanguage();

				$router = self::$_app->getRouter();
				self::$mode_sef = ( $router->getMode() == JROUTER_MODE_SEF ) ? true : false;
				// attach build rules for Preview SEF
				$router->attachBuildRule( array( $this, 'buildRule' ) );
				// attach parse rules for language SEF
				$router->attachParseRule( array( $this, 'parseRule' ) );

			}

			if (JPluginHelper::isEnabled('system', 'cache') && version_compare(JVERSION, '3.0.0', '>='))
			{
				if ($getPreView != 1)
				{
					if ($detect->isMobile() && !$detect->isTablet())
					{
						$cacheKey = 'mobile';
					}
					elseif($detect->isTablet())
					{
						$cacheKey = 'tablet';
					}
					else
					{
						$cacheKey = 'desktop';
					}
				}
				else
				{
					$cacheKey = time();
				}				
				$dispatcher = JEventDispatcher::getInstance();
				$refObj = new ReflectionObject($dispatcher);
				$refProp = $refObj->getProperty('_observers');
				$refProp->setAccessible(true);
				$observers = $refProp->getValue($dispatcher);
				foreach($observers as $index => $object)
				{
					if(is_a($object, 'plgSystemCache'))
					{
						$object->_cache_key = 'jsnmobilize' . $cacheKey . 'jsnmobilize' . $object->_cache_key;
					}
				}
			}
		}
	}

	public function parseRule( &$router, &$uri ) {
		$app = JFactory::getApplication();
		$get = $app->input->getArray($_GET);
		
		$array = array();
		if ( ! empty( $_SERVER[ 'HTTP_REFERER' ] ) ) {

			if ( strpos( $_SERVER[ 'HTTP_REFERER' ], 'jsn_mobilize_preview=1' ) && ! isset( $get[ 'jsn_mobilize_preview' ] ) && empty( $_REQUEST[ 'format' ] ) ) {
				$uri->setVar( 'jsn_mobilize_preview', 1 );
				$app->redirect( JURI::base( true ) . '/index.php?' . $uri->getQuery() );
			}
		}
		return $array;
	}

	public function buildRule( &$router, &$uri ) {
		$app = JFactory::getApplication();
		$get = $app->input->getArray($_GET);
		
		$preview = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
		$switch = ! empty( $get[ 'switch_to_desktop_ui' ] ) ? $get[ 'switch_to_desktop_ui' ] : '';
		if ( ! empty( $preview ) ) {
			$uri->setVar( 'jsn_mobilize_preview', $preview );
		}
		if ( ! empty( $switch ) ) {
			$uri->setVar( 'switch_to_desktop_ui', $switch );
		}

	}

	/**
	 * Event handler to re-parse request URI.
	 *
	 * @return  void
	 */
	public function onAfterRoute() {
		self::$_app = JFactory::getApplication();
		$get = self::$_app->input->getArray($_GET);
		if ( ! class_exists( 'JSNConfigHelper' ) ) {
			return true;
		}
		// Continue only if not in administrator section and in mobile/tablet site
		if ( ! self::$_app->isAdmin() ) {
			require_once JPATH_BASE . '/templates/jsn_mobilize/helpers/mobilize.php';

			if ( self::$_device == "mobilize" ) {
				$getPreView = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
				// Get input object
				$input = self::$_app->input;
				// Set necessary variables to request array
				$urlRequest = isset( $_SERVER[ "REQUEST_URI" ] ) ? $_SERVER[ "REQUEST_URI" ] : "";
				if ( $getPreView == 1 ) {
					$mCfg = JSNMobilizeTemplateHelper::getConfig( self::$_device, true );
				}
				else {
					$mCfg = JSNMobilizeTemplateHelper::getConfig( self::$_device );
				}

				if ( ! empty( $mCfg ) ) {
					self::$_app->setTemplate( 'jsn_mobilize' );
					$input->set( '_device', self::$_device );
				}
			}
			if ( JSNMobilizeTemplateHelper::getConfig( "mobilize" ) ) {
				self::$_app->registerEvent( 'onAfterRender', 'jsnMobilizeFinalize' );
			}
		}
	}

	/**
	 * Alter response body if necessary.
	 *
	 * - Mobile/tablet site: alter URI based on detected client device type.
	 * - Desktop site: inject switcher link if visitor viewing desktop site on either mobile device or tablet PC.
	 *
	 * @return  void
	 */
	public static function onAfterRender() {
		self::$_app = JFactory::getApplication();
		$get = self::$_app->input->getArray($_GET);
		if ( ! self::$_app->isAdmin() ) {
			require_once dirname( __FILE__ ) . '/libraries/joomlashine/client/mobiledetect.php';
			$detect = new JSN_Mobile_Detect;
			$deviceType = ( $detect->isMobile() ? 'mobilize' : 'desktop' );
			$getPreview = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
			$getSwitch = ! empty( $get[ 'switch_to_desktop_ui' ] ) ? $get[ 'switch_to_desktop_ui' ] : '';
			if ( JSNMobilizeTemplateHelper::getConfig( "mobilize" )){
				if ( ! defined( 'JSN_MOBILIZE_LAST_EXECUTION' ) ) {
					return;
				}
			}

			if ( ! self::$_app->isAdmin() && self::$_device == "mobilize" && ( $device = $deviceType ) == 'desktop' ) {
				// Alter and/or optimize response body based on detected client device type
				self::_finalizeResponse();
			}
			elseif ( self::$_device == 'desktop' && ( $device = $deviceType ) != 'desktop' || ( $getSwitch == 1 && $getPreview == 1 ) ) {
				// Inject UI switcher link if visitor viewing desktop site on either mobile device or tablet PC
				self::_injectUISwitcher( $device );

			}
			if ( self::$_device != 'desktop' ) {
				self::Optimize();
			}
		}
	}

	/**
	 * Optimize js,css,images.
	 *
	 * @param   string  $html  Response body generated by Joomla.
	 *
	 * @return  void
	 */
	private static function Optimize( $html = '' ) {
		// Initialize response body
		! empty( $html ) OR $html = JResponse::getBody();

		$detect = new JSN_Mobile_Detect;
		$session = JFactory::getSession();
		$profile = $session->get( 'jsn_mobilize_profile' );
		$profileMinify = ! empty( $profile->profile_minify ) ? $profile->profile_minify : '';
		$profileOptimizeImages = ! empty( $profile->profile_optimize_images ) ? $profile->profile_optimize_images : '';
		// Minify stylesheets and Javascript files
		if ($profileMinify != '' && ($detect->isMobile() || $detect->isTablet())) {
			// Load library to minify assets
			require_once dirname( dirname( __FILE__ ) ) . '/jsnmobilize/libraries/joomlashine/compress/helper.php';
			// Minify stylesheets
			if ( strpos( 'css + both', $profileMinify ) !== false ) {
				require_once dirname( dirname( __FILE__ ) ) . '/jsnmobilize/libraries/joomlashine/compress/css.php';
				$html = preg_replace_callback(
					'/(<link([^>]+)rel=["|\']stylesheet["|\']([^>]*)>\s*)+/i', array(
					'JSNMobilizeCompressCss',
					'compress'
				), $html
				);
			}

			// Minify Javascript files
			if ( strpos( 'js + both', $profileMinify ) !== false ) {
				require_once dirname( dirname( __FILE__ ) ) . '/jsnmobilize/libraries/joomlashine/compress/js.php';
				$html = preg_replace_callback(
					'/(<script([^>]+)src=["|\']([^"|\']+)["|\']([^>]*)>\s*<\/script>\s*)+/i', array(
					'JSNMobilizeCompressJs',
					'compress'
				), $html
				);
			}
		}

		// Optimize image files
		if ( $detect->isMobile() && ! $detect->isTablet() && $profileOptimizeImages != '' ) {
			// Load library to optimize image files
			require_once dirname( dirname( __FILE__ ) ) . '/jsnmobilize/libraries/joomlashine/response/image.php';

			// Initialize image file optimization
			$html = JSNResponseImage::init( JSN_MOBILIZE_PATH_OPTIMIZED_IMAGE, (int)$profileOptimizeImages, $html, false );
		}
		// Set manipulated HTML code
		JResponse::setBody( $html );
	}

	/**
	 * Finalize response body for rendering mobile/tablet UI.
	 *
	 * @param   string  $html  Response body generated by Joomla.
	 *
	 * @return  void
	 */
	private static function _finalizeResponse( $html = '' ) {
		self::$_app = JFactory::getApplication();
		$get = self::$_app->input->getArray($_GET);
		
		// Initialize response body
		! empty( $html ) OR $html = JResponse::getBody();

		// Build regular expression to parse response
		$regEx = '#<(a|form|img)[^>]*(href|action|src)=("|\')(' . JURI::root() . '|' . JURI::root( true ) . ')*([^\s]*)("|\')[^>]*>#i';
		$getPreView = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
		// Get input object
		$input = self::$_app->input;
		// Set necessary variables to request array
		$urlRequest = isset( $_SERVER[ "REQUEST_URI" ] ) ? $_SERVER[ "REQUEST_URI" ] : "";
		// Get all a and form tag from responce

		if ( preg_match_all( $regEx, $html, $matches, PREG_SET_ORDER ) ) {

			// var_dump($matches);
			foreach ( $matches AS $match ) {
				if ( strpos( $match[ 0 ], ' id="jsn-mobilize-ui-switcher"' ) === false ) {
					// Check if this is a direct link
					if ( ! empty( $match[ 5 ] ) && $match[ 5 ] != '/' && strpos( $match[ 5 ], '/index.php' ) !== 0 && ( is_readable( JPATH_ROOT . $match[ 5 ] ) || ( ( $pos = strpos( $match[ 5 ], '?' ) ) !== false AND is_readable( JPATH_ROOT . substr( $match[ 5 ], 0, $pos ) ) ) ) ) {
						continue;
					}

					// Build mobile/tablet URI
					if ( $match[ 1 ] != 'img' ) {
						if ( substr( $link = self::$_cfg->get( 'link_' . self::$_device ), 0, 1 ) == '/' ) {
							$uri = str_replace( JURI::root( true ), '/' . trim( $link, '/' ), $match[ 4 ] );
						}
						else {
							// Get parsed request URI object
							$jUri = JURI::getInstance();

							if ( preg_match( '/^https?:/i', $match[ 4 ] ) ) {
								$uri = str_replace( $jUri->getHost(), $link, $match[ 4 ] );
							}
							elseif ( substr( $match[ 4 ], 0, 1 ) == '/' ) {
								$uri = ( $uri = $jUri->getScheme() ) . ( empty( $uri ) ? '' : '://' ) . $link . $match[ 4 ];
							}
						}
						// Finalize link
						$uri .= str_replace( '/index.php', '', $match[ 5 ] );
					}
					else {
						if ( ! preg_match( '/^https?:/', $match[ 5 ] ) ) {
							$uri = JURI::root( true ) . '/' . $match[ 5 ];
						}
						else {
							$uri = $match[ 5 ];
						}
					}

					if ( $getPreView == 1 && $match[ 1 ] != 'img' && strpos( $match[ 5 ], 'javascript:' ) === false && $match[ 5 ] != "#" ) {
						if ( $pos = strpos( $match[ 5 ], '?' ) == false ) {
							$uri = $uri . '?jsn_mobilize_preview=1';
						}
						else if ( strpos( $uri, 'jsn_mobilize_preview=' ) == false ) {
							$uri = $uri . '&jsn_mobilize_preview=1';
						}

					}
					// Create replacement
					$replace = str_replace( $match[ 4 ] . $match[ 5 ], $uri, $match[ 0 ] );
					//var_dump( $match, $uri );
					// Replace original link with link for mobile/tablet site
					$html = str_replace( $match[ 0 ], $replace, $html );
				}
			}
		}
		// Set manipulated HTML code
		JResponse::setBody( $html );
	}

	/**
	 * Inject UI switcher into default desktop template.
	 *
	 * @param   string  $device  Detected client device type.
	 * @param   string  $html    Response body generated by Joomla.
	 *
	 * @return  void
	 */
	private static function _injectUISwitcher( $device, $html = '' ) {
		$lang = JFactory::getLanguage();
		self::$_app = JFactory::getApplication();
		
		$lang->load( 'plg_system_jsnmobilize', JPATH_ADMINISTRATOR );
		$detect = new JSN_Mobile_Detect;
		$textSwitcher = '';
		if ( $detect->isMobile() && ! $detect->isTablet() ) {
			$textSwitcher = 'JSN_MOBILIZE_SWITCH_TO_WEB_UI_FOR_MOBILE';
		}
		else if ( $detect->isMobile() && $detect->isTablet() ) {
			$textSwitcher = 'JSN_MOBILIZE_SWITCH_TO_WEB_UI_FOR_TABLET';
		}
		else {
			$textSwitcher = 'Switch To Mobile';
		}
		// Initialize response body
		! empty( $html ) OR $html = JResponse::getBody();

		// Get parsed request URI object
		$jUri = JURI::getInstance();

		// Build URI for switching back to mobile/tablet site
		if ( substr( $link = self::$_cfg->get( "link_{$device}" ), 0, 1 ) == '/' ) {
			$switch = str_replace( JURI::root( true ), '/' . trim( $link, '/' ), $jUri->toString() );
		}
		else {
			$switch = str_replace( $jUri->getHost(), $link, $jUri->toString() );
		}

		$get = self::$_app->input->getArray($_GET);
		$getPreView = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
		
		// Get input object
		$input = self::$_app->input;
		// Set necessary variables to request array
		$urlRequest = isset( $_SERVER[ "REQUEST_URI" ] ) ? $_SERVER[ "REQUEST_URI" ] : "";
		if ( $getPreView == 1 ) {
			$url = JURI::root() . '?switch_to_desktop_ui=0&jsn_mobilize_preview=1';
		}
		else {
			$url = JURI::root() . '?switch_to_desktop_ui=0';
		}

		$switch = preg_replace( '/(\?|&)switch_to_desktop_ui=1/', '', $switch );

		// Get user selected style
		$style = self::$_cfg->get( 'style', 'default' );
		// Inject UI switcher assets
		$html = str_replace( '</head>', "\t" . '<link media="screen" type="text/css" href="' . JURI::root( true ) . '/templates/jsn_mobilize/css/switcher.css" rel="stylesheet" />' . "\n</head>", $html );
		// Inject UI switcher link
		$html = str_replace( '</body>', "\t" . '<div class="mobilize-ui-switcher"><a id="jsn-mobilize-ui-switcher" class="btn" href="' . $url . '" title="' . JText::_( $textSwitcher ) . '">' . JText::_( $textSwitcher ) . '</a></div>' . "\n</body>", $html );

		// Set manipulated HTML code
		JResponse::setBody( $html );
	}

	/**
	 * Load plugin language.
	 *
	 * @return  void
	 */
	private function _loadLanguage() {
		// Get active language
		$language = JFactory::getLanguage();
		self::$_app = JFactory::getApplication();
		// Check if language file exists for active language
		if ( ! file_exists( JPATH_ROOT . '/administrator/language/' . $language->getDefault() . '/' . $language->getDefault() . '.plg_system_jsnmobilize.ini' ) ) {
			// If requested component has the language file, install then load it
			if ( file_exists( JPATH_ROOT . '/administrator/components/' . self::$_app->input->getCmd( 'option' ) . '/language/admin/' . $language->getDefault() . '/' . $language->getDefault() . '.plg_system_jsnmobilize.ini' ) ) {
				JSNLanguageHelper::install( (array)$language->getDefault(), false, true );
				$language->load( 'plg_system_jsnmobilize', JPATH_BASE, null, true );
			}
			// Otherwise, try to load language file from plugin directory
			else {
				$language->load( 'plg_system_jsnmobilize', dirname( __FILE__ ), null, true );
			}
		}
		else {
			$language->load( 'plg_system_jsnmobilize', JPATH_BASE, null, true );
		}
	}

}

/**
 * Finalize response body.
 *
 * @return  void
 */
function jsnMobilizeFinalize() {
	define( 'JSN_MOBILIZE_LAST_EXECUTION', 1 );
	PlgSystemJSNMobilize::onAfterRender();
}
