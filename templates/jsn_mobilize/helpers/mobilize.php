<?php

/**
 * @version     $Id: mobilize.php 19770 2012-12-28 08:26:19Z thailv $
 * @package     JSN_Mobilize
 * @subpackage  Template
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import Joomla module helper library
jimport( 'joomla.application.module.helper' );

/**
 * Helper class for rendering module instance.
 *
 * @package     JSN_Mobilize
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNMobilizeTemplateHelper {

	/**
	 * Device specific parameters.
	 *
	 * @var  array
	 */
	protected static $config;

	/**
	 * Get configuration for device in use.
	 *
	 * @param   array    $device   Device in use: mobile or tablet?
	 * @param   boolean  $preview  Get config from cookie for backend previewing?
	 *
	 * @return  JObject
	 */
	public static function getConfig( $device = '', $preview = false ) {
		// Initialize client device type
		! empty( $device ) OR $device = JFactory::getApplication()->input->getCmd( '_device' );
		if ( ! isset( self::$config ) OR ! isset( self::$config[ $device ] ) ) {
			// Get JSN Mobilize configuration

			if ( $preview ) {
				$db = JFactory::getDbo();
				$query = $db->getQuery( true );
				$query->select( '*' )->from( '#__jsn_mobilize_config' )->where( "name = 'tmp_config'" );
				$db->setQuery( $query, 0, 1 );
				$tmpConfig = $db->loadObject();
				if ( ! empty( $tmpConfig->value ) ) {
					$config = json_decode( $tmpConfig->value );
				}
			}
			else {
				$detect = new JSN_Mobile_Detect;
				$checkIOS = '';
				if ( $detect->isiOS() ) {
					if ( $detect->isTablet() ) {
						$version = $detect->version( 'iPad' );
					}
					else {
						$version = $detect->version( 'iPhone' );
					}
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'ios', $version, $device );
				}
				elseif ( $detect->isAndroidOS() ) {
					$version = $detect->version( 'Android' );
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'android', $version, $device );
				}
				elseif ( $detect->isWindowsPhoneOS() ) {
					$version = $detect->version( 'Windows Phone OS' );
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'wmobilie', $version, $device );
				}
				elseif ( $detect->isBlackBerryOS() ) {
					$version = $detect->version( 'BlackBerry' );
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'blackberry', $version, $device );
				}
				else {
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'other', '', $device );
				}
				if ( $config === false ) {
					$config = JSNMobilizeTemplateHelper::getOSSupport( 'other', '', $device );
				}
			}
			if ( ! empty( $config ) ) {
				foreach ( get_object_vars( $config ) AS $k => $v ) {
					isset( $tmp ) OR $tmp = new JObject;

					if ( strpos( $k, "{$device}-" ) === 0 ) {
						// Shorten parameter name
						$k = substr( $k, strlen( "{$device}-" ) );

						// Set new parameter with cleaned name
						$tmp->set( $k, is_object( $v ) ? ( (array)$v ) : $v );
					}
					elseif ( ! preg_match( '/^(mobilize)-/', $k ) ) {
						// Store shared parameter also
						$tmp->set( $k, is_object( $v ) ? ( (array)$v ) : $v );
					}
				}
			}
			// Store device specific parameters
			if ( ! empty( $tmp ) ) {
				self::$config[ $device ] = $tmp;
			}
			// Get device specific parameters

		}
		return self::$config[ $device ];
	}

	/**
	 * Render a content block.
	 *
	 * @param   array    $param   Content block parameters.
	 * @param   boolean  $return  Return rendered content or echo immediately.
	 *
	 * @return  string
	 */
	public static function renderBlock( $param, $return = false ) {
		// Preset return value
		$html = array();

		if ( is_array( $param ) AND count( $param ) ) {
			foreach ( $param AS $k => $v ) {
				if ( $v == 'position' AND JFactory::getDocument()->countModules( $k ) ) {
					$html[ ] = '<jdoc:include type="modules" style="jsnmodule" class="jsn-roundedbox" name="' . $k . '" />';
				}
				elseif ( $v == 'module' AND ( $tmp = self::renderModule( $k, array( 'style' => 'jsnmodule' ), false, true ) ) != '' ) {
					$html[ ] = $tmp;
				}
			}
		}
		// Finalize return value
		$html = count( $html ) > 0 ? implode( "\n", $html ) : '';
		if ( $return ) {
			return $html;
		}
		echo $html;
	}

	/**
	 * Render menu instance(s) by ID.
	 *
	 * @param   mixed  $id  Menu instance ID or array of Menu instance ID to be rendered.
	 *
	 * @return  string
	 */
	public static function renderMenu( $id ) {
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$active = $menu->getActive();
		$active_id = isset( $active ) ? $active->id : $menu->getDefault()->id;
		$path = isset( $active ) ? $active->tree : array();
		$db = JFactory::getDbo();
		$query = $db->getQuery( true )->select( 'menutype' )->from( '#__menu_types' )->where( 'id = ' . $db->Quote( $id ) );
		$db->setQuery( $query );
		$menuType = $db->loadResult();
		$items = $menu->getItems( 'menutype', $menuType );
		$lastitem = 0;
		$start = 1;
		$end = 0;
		$showAll = 1;
		if ( $items ) {
			foreach ( $items as $i => $item ) {
				if ( ( $start && $start > $item->level ) || ( $end && $item->level > $end ) || ( ! $showAll && $item->level > 1 && ! in_array( $item->parent_id, $path ) ) || ( $start > 1 && ! in_array( $item->tree[ $start - 2 ], $path ) ) ) {
					unset( $items[ $i ] );
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;

				if ( isset( $items[ $lastitem ] ) ) {
					$items[ $lastitem ]->deeper = ( $item->level > $items[ $lastitem ]->level );
					$items[ $lastitem ]->shallower = ( $item->level < $items[ $lastitem ]->level );
					$items[ $lastitem ]->level_diff = ( $items[ $lastitem ]->level - $item->level );
				}

				$item->parent = (boolean)$menu->getItems( 'parent_id', (int)$item->id, true );

				$lastitem = $i;
				$item->active = false;
				$item->flink = $item->link;

				// Reverted back for CMS version 2.5.6
				switch ( $item->type ) {
					case 'separator':
						// No further action needed.
						continue;
					case 'url':
						if ( ( strpos( $item->link, 'index.php?' ) === 0 ) && ( strpos( $item->link, 'Itemid=' ) === false ) ) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get( 'aliasoptions' );
						break;

					default:
						$router = JSite::getRouter();
						if ( $router->getMode() == JROUTER_MODE_SEF ) {
							$item->flink = 'index.php?Itemid=' . $item->id;
						}
						else {
							$item->flink .= '&Itemid=' . $item->id;
						}
						break;
				}

				if ( strcasecmp( substr( $item->flink, 0, 4 ), 'http' ) && ( strpos( $item->flink, 'index.php?' ) !== false ) ) {
					$item->flink = JRoute::_( $item->flink, true, $item->params->get( 'secure' ) );
				}
				else {
					$item->flink = JRoute::_( $item->flink );
				}

				$item->title = htmlspecialchars( $item->title, ENT_COMPAT, 'UTF-8', false );
				$item->anchor_css = htmlspecialchars( $item->params->get( 'menu-anchor_css', '' ), ENT_COMPAT, 'UTF-8', false );
				$item->anchor_title = htmlspecialchars( $item->params->get( 'menu-anchor_title', '' ), ENT_COMPAT, 'UTF-8', false );
				$item->menu_image = $item->params->get( 'menu_image', '' ) ? htmlspecialchars( $item->params->get( 'menu_image', '' ), ENT_COMPAT, 'UTF-8', false ) : '';
			}

			if ( isset( $items[ $lastitem ] ) ) {
				$items[ $lastitem ]->deeper = ( ( $start ? $start : 1 ) > $items[ $lastitem ]->level );
				$items[ $lastitem ]->shallower = ( ( $start ? $start : 1 ) < $items[ $lastitem ]->level );
				$items[ $lastitem ]->level_diff = ( $items[ $lastitem ]->level - ( $start ? $start : 1 ) );
			}
		}
		echo "<ul class=\"menu jsn-menu jsn-menu-mobile jsn-toggle menu-stickymenu\">";
		foreach ( $items as $i => &$item ) {
			$class = 'item-' . $item->id;
			if ( $item->id == $active_id ) {
				$class .= ' current';
			}

			if ( in_array( $item->id, $path ) ) {
				$class .= ' active';
			}
			elseif ( $item->type == 'alias' ) {
				$aliasToId = $item->params->get( 'aliasoptions' );
				if ( count( $path ) > 0 && $aliasToId == $path[ count( $path ) - 1 ] ) {
					$class .= ' active';
				}
				elseif ( in_array( $aliasToId, $path ) ) {
					$class .= ' alias-parent-active';
				}
			}

			if ( ! empty( $item->deeper ) ) {
				$class .= ' deeper';
			}

			if ( ! empty( $item->parent ) ) {
				$class .= ' parent';
			}

			if ( ! empty( $class ) ) {
				$class = ' class="' . trim( $class ) . '"';
			}

			echo '<li' . $class . '>';

			// Render the menu item.

			// Note. It is important to remove spaces between elements.
			$class = ! empty( $item->anchor_css ) ? 'class="' . $item->anchor_css . '" ' : '';
			$title = ! empty( $item->anchor_title ) ? 'title="' . $item->anchor_title . '" ' : '';
			if ( ! empty( $item->menu_image ) ) {
				$item->params->get( 'menu_text', 1 ) ? $linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' : $linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
			}
			else {
				$linktype = $item->title;
			}
			$flink = ! empty( $item->flink ) ? $item->flink : "";
			$flink = JFilterOutput::ampReplace( htmlspecialchars( $flink ) );

			switch ( $item->browserNav ) :
				default:
				case 0:
					?>
				<a <?php echo $class; ?>href="<?php echo $flink; ?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
					break;
				case 1:
					// _blank
					?>
				<a <?php echo $class; ?>href="<?php echo $flink; ?>" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
					break;
			endswitch;
			// The next item is deeper.
			if ( ! empty( $item->deeper ) ) {
				echo '<ul>';
			}
			// The next item is shallower.
			elseif ( ! empty( $item->shallower ) ) {
				echo '</li>';
				echo str_repeat( '</ul></li>', $item->level_diff );
			}
			// The next item is on the same level.
			else {
				echo '</li>';
			}
		}
		echo "</ul>";
	}

	/**
	 * Render module instance(s) by ID.
	 *
	 * @param   mixed    $id          Module instance ID or array of Module instance ID to be rendered.
	 * @param   array    $attributes  Module chrome attributes.
	 * @param   boolean  $hideTitle   Set to true to always hide module title regardless of module instance settings.
	 * @param   boolean  $return      Return rendered content or echo immediately.
	 *
	 * @return  string
	 */
	public static function renderModule( $id, $attributes = array(), $hideTitle = false, $return = false ) {
		// Preset return value
		$html = '';

		// Get database object
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt( 'Itemid' );
		// Load Access User group view level
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
		// Build query to load module data
		$query = $db->getQuery( true );
		$query->select( 'm.*' );
		$query->from( '#__modules AS m' );
		$query->where( 'id = ' . (int)$id );
		$query->where('m.access IN (' . $groups . ')');
		$query->join( 'LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id' );
		$query->where( 'm.published = 1' );
		$query->where( '(mm.menuid = ' . (int)$Itemid . ' OR mm.menuid = 0)' );
		// Query for module data
		$db->setQuery( $query );

		if ( $row = $db->loadObject() ) {
			// Is module title visible?
			! $hideTitle OR $row->showtitle = 0;

			// Render the module instance
			$html = JModuleHelper::renderModule( $row, $attributes );
		}

		if ( $return ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Render content block.
	 *
	 * @param   string   $name     Content block name.
	 * @param   string   $device   Client device type.
	 * @param   integer  $preview  Previewing or not?
	 * @param   String   $IDBlock  ID Block
	 * @param   String   $class    Class css
	 *
	 * @return  void
	 */
	public static function renderHtmlBlock($bgstyke = NULL, $name, $device, $preview, $IDBlock = '', $class = 'row-fluid' ) {
		// Initialize variables
		$mCfg = JSNMobilizeTemplateHelper::getConfig( $device, $preview );
		$detect = new JSN_Mobile_Detect;
		$classPreview = "";
		$urlRequest = isset( $_SERVER[ "REQUEST_URI" ] ) ? $_SERVER[ "REQUEST_URI" ] : "";
		
		$app = JFactory::getApplication();
		$get = $app->input->getArray($_GET);
		
		$getPreView = ! empty( $get[ 'jsn_mobilize_preview' ] ) ? $get[ 'jsn_mobilize_preview' ] : '';
		if ( $getPreView == 1 ) {
			$classPreview = "jsn-mobilize-block ";
		}
		if ( ! empty( $name ) ) {
			$nameLeft = $name . '-left';
			$nameRight = $name . '-right';

			// Counting block content
			$counted[ $name ] = is_array( $mCfg->get( $name ) ) ? count( $mCfg->get( $name ) ) : 0;
			$counted[ $nameLeft ] = is_array( $mCfg->get( $nameLeft ) ) ? count( $mCfg->get( $nameLeft ) ) : 0;
			$counted[ $nameRight ] = is_array( $mCfg->get( $nameRight ) ) ? count( $mCfg->get( $nameRight ) ) : 0;
			$htmlBlock = "";

			// Render block content
			$html[ $nameLeft ] = JSNMobilizeTemplateHelper::renderBlock( $mCfg->get( $nameLeft ), true );
			$html[ $nameRight ] = JSNMobilizeTemplateHelper::renderBlock( $mCfg->get( $nameRight ), true );

			$classSpan = 'span6';

			if ( ! empty( $html[ $nameLeft ] ) OR ! empty( $html[ $nameRight ] ) ) {
				//echo '<div id="jsn-mobilize-' . str_replace('_', '-', $name) . '" class="row-fluid">';
				if ( ! empty( $html[ $nameLeft ] ) ) {
					$htmlBlock .= "\t" . '<div id="jsn-mobilize-' . str_replace( '_', '-', $nameLeft ) . '" class="' . $classPreview . ( ! empty( $html[ $nameRight ] ) ? $classSpan : 'span12' ) . '">' . "\n\t\t" . $html[ $nameLeft ] . "\n\t" . '</div>';
				}
				if ( ! empty( $html[ $nameRight ] ) ) {
					$htmlBlock .= "\t" . '<div id="jsn-mobilize-' . str_replace( '_', '-', $nameRight ) . '" class=" ' . $classPreview . ( ! empty( $html[ $nameLeft ] ) ? $classSpan : 'span12' ) . '">' . "\n\t\t" . $html[ $nameRight ] . "\n\t" . '</div>';
				}
				//echo '</div>';
			}

			if ( ! empty( $htmlBlock ) ) {
				$class = ! empty( $class ) ? 'class="' . $class . '"' : '';
				echo '<div id="' . $IDBlock . '"' . $class . '>'.$bgstyke.'<div class="row-fluid">' . $htmlBlock . '</div></div>';
			}
		}
	}

	/**
	 * Get options design os support
	 *
	 * @param   String  $type     Type OS
	 * @param   String  $version  Version OS
	 * @param   String  $device   Device
	 *
	 * @return stdClass
	 */
	public static function getOSSupport( $type, $version, $device ) {
		// Get database object
		$db = JFactory::getDbo();

		// Build query to load module data
		$query = $db->getQuery( true );
		$query->select( '*' );
		$query->from( '#__jsn_mobilize_os' );
		$query->where( 'os_type = ' . $db->Quote( $type ) );

		$db->setQuery( $query );
		$lisOS = $db->loadObjectList();

		$support = "";
		if ( ! empty( $version ) ) {
			$ver = explode( ".", $version );
			if ( ! empty( $ver[ 1 ] ) ) {
				$version = $ver[ 0 ] . '.' . $ver[ 1 ];
			}
			else {
				$version = $ver[ 0 ];
			}
		}

		if ( $type != "other" ) {
			foreach ( $lisOS as $os ) {
				$osValue = json_decode( $os->os_value );
				if ( ! empty( $osValue ) ) {
					foreach ( $osValue as $value ) {
						if ( ! empty( $value[ 1 ] ) && ! empty( $value[ 0 ] ) && $value[ 1 ] == "<" ) {
							if ( is_float( $value[ 0 ] ) && version_compare( $value[ 0 ], $version, ">=" ) ) {
								$support = $os->os_id;
							}
							elseif ( version_compare( $value[ 0 ], (int)$version, ">=" ) ) {
								$support = $os->os_id;
							}
						}
						elseif ( ! empty( $value[ 1 ] ) && ! empty( $value[ 0 ] ) && $value[ 1 ] == ">" ) {
							if ( is_float( $value[ 0 ] ) && version_compare( $value[ 0 ], $version, "<=" ) ) {
								$support = $os->os_id;
							}
							elseif ( version_compare( $value[ 0 ], (int)$version, "<=" ) ) {
								$support = $os->os_id;
							}
						}
						elseif ( empty( $value[ 1 ] ) && ! empty( $value[ 0 ] ) ) {
							if ( is_float( $value[ 0 ] ) && version_compare( $value[ 0 ], $version, "=" ) ) {
								$support = $os->os_id;
							}
							elseif ( version_compare( $value[ 0 ], (int)$version, "=" ) ) {
								$support = $os->os_id;
							}
						}
						elseif ( ! empty( $value[ 1 ] ) && ! empty( $value[ 0 ] ) ) {
							if ( version_compare( $value[ 0 ], $version, "=" ) || version_compare( $value[ 1 ], $version, "=" ) ) {
								$support = $os->os_id;
							}
						}
					}
				}
			}
			if ( empty( $support ) ) {
				return false;
			}
		}
		else {
			$support = $lisOS[ 0 ]->os_id;
		}
        $detect = new JSN_Mobile_Detect;
        $pfl_device = ($detect->isTablet()) ? '"jsn_tablet"' : '"jsn_mobile"';
		$query = $db->getQuery( true );
		$query->select( '*' );
		$query->from( '#__jsn_mobilize_profiles AS p' );
		$query->join( 'INNER', '#__jsn_mobilize_os_support AS s ON s.profile_id = p.profile_id' );
		$query->where( 's.os_id = ' . $db->Quote( $support ) );
		$query->where( 'p.profile_state = 1 AND p.profile_device = '.$pfl_device );
//		$query->order( "p.ordering" );
		$db->setQuery( $query, 0, 1 );
		$profile = $db->loadObject();
		$profileID = ! empty( $profile->profile_id ) ? $profile->profile_id : '';
		$query = $db->getQuery( true );
		$query->select( '*' );
		$query->from( '#__jsn_mobilize_design' );
		$query->where( 'profile_id = ' . $db->Quote( $profileID ) );
		$db->setQuery( $query );
		$dataDesign = $db->loadObjectList();
		$optionDesign = new stdClass;
		if ( ! empty( $dataDesign ) ) {
			foreach ( $dataDesign as $item ) {
				$name = $item->name;
				$value = json_decode( $item->value );
				
				if ( ! empty( $value ) ) {
					$optionDesign->$name = $value;
				}
				else {
					if ($name == 'mobilize-css') {
						$item->value = self::addDomainPrefix($item->value);
					}
					$optionDesign->$name = $item->value;
				}
			}
		}
		$session = JFactory::getSession();
		$session->set( 'jsn_mobilize_profile', $profile );

		return $optionDesign;
	}
	
	/**
	 *  Add domain prefix before background url
	 *
	 * @return string
	 */
	public static function addDomainPrefix($contents)
	{
		$patternUrl = 'background:url\(\/';
		$replaceWithUrl = 'background:url('.JURI::root();
		
		$patternImage = 'background-image:url\(\/';
		$replaceWithImage = 'background-image:url('.JURI::root();

		if ($contents) {
			preg_match('/'.$patternUrl.'/', $contents, $r1);
			if ( count($r1))
			{
				$contents = preg_replace('/'.$patternUrl.'/', $replaceWithUrl, $contents);
			}

			preg_match('/'.$patternImage.'/', $contents, $r2);
			if ( count($r2))
			{
				$contents = preg_replace('/'.$patternImage.'/', $replaceWithImage, $contents); 
			}

		}
		return $contents;
	}
	

	/**
	 *  Check Version Joomla
	 *
	 * @return mixed
	 */
	public static function isJoomla3() {
		$version = new JVersion;
		$isJoomla3 = version_compare( $version->getShortVersion(), '3.0', '>=' );
		return $isJoomla3;
	}

	/**
	 * Wrap first word inside a <span>
	 *
	 * @param   String  $value  Value
	 *
	 * @return null|string
	 */
	public function wrapFirstWord( $value ) {
		$processed_string = null;
		$explode_string = explode( ' ', trim( $value ) );
		for ( $i = 0; $i < count( $explode_string ); $i ++ ) {
			if ( $i == 0 ) {
				$processed_string .= '<span>' . $explode_string[ $i ] . '</span>';
			}
			else {
				$processed_string .= ' ' . $explode_string[ $i ];
			}
		}

		return $processed_string;
	}

	/**
	 * Check item menu is the last menu
	 *
	 * @param   Object  $item  item menu
	 *
	 * @return bool
	 */
	public static function isLastMenu( $item ) {
		if ( isset( $item->tree[ 0 ] ) && isset( $item->tree[ 1 ] ) ) {
			$db = JFactory::getDbo();
			$q = $db->getQuery( true );

			$q->select( 'lft, rgt' );
			$q->from( '#__menu' );
			$q->where( 'id = ' . (int)$item->tree[ 0 ], 'OR' );
			$q->where( 'id = ' . (int)$item->tree[ 1 ] );

			$db->setQuery( $q );

			$results = $db->loadObjectList();

			if ( $results[ 1 ]->rgt == ( (int)$results[ 0 ]->rgt - 1 ) && $item->deeper ) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Check if current page is rendered by a specific component and/or has any module of that specific component assigned to.
	 *
	 * @param   string  $option  Component folder name, e.g. com_k2
	 * @param   string  $module  Prefix of module belonging to the specified component above, e.g. mod_k2_
	 *
	 * @return  boolean
	 */
	protected function checkExt($option, $module = '')
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Check if current page is generated by specified component
		if ($input->getCmd('option') == $option)
		{
			return true;
		}

		if ( ! empty($module))
		{
			// Get current page menu item id
			$itemId = $input->getInt('Itemid', 0);

			// Get template positions
			static $positions;

			if ( ! isset($positions))
			{
				// Read template manifest file for available positions
				$xml = simplexml_load_file(JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate(true)->template . '/templateDetails.xml');

				foreach ($xml->xpath('positions/position') AS $position)
				{
					$positions[] = (string) $position;
				}
			}

			// Get Joomla database object
			$db	= JFactory::getDbo();

			// First query for module instances that are always hidden in current page
			$q	= $db->getQuery(true);

			$q->select('m.id');
			$q->from('#__modules AS m');
			$q->join('INNER', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$q->where('m.client_id = 0');
			$q->where('m.published = 1');
			$q->where('m.module LIKE ' . $q->quote("{$module}%"));
			$q->where('(mm.menuid < 0 AND mm.menuid = ' . (0 - $itemId) . ')');

			if (isset($positions))
			{
				$q->where('m.position IN ("' . implode('", "', $positions) . '")');
			}

			$db->setQuery($q);

			$excludes = is_array($excludes = $db->loadColumn()) ? $excludes : array();

			// Then query for modules instances that are assigned to show in all page or current page
			$q	= $db->getQuery(true);

			$q->select('m.id');
			$q->from('#__modules AS m');
			$q->join('INNER', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$q->where('m.client_id = 0');
			$q->where('m.published = 1');
			$q->where('m.module LIKE ' . $q->quote("{$module}%"));
			$q->where('(mm.menuid = ' . $itemId . ' OR mm.menuid = 0 OR (mm.menuid < 0 AND mm.menuid != ' . (0 - $itemId) . '))');
			$q->group('m.id');

			if (isset($positions))
			{
				$q->where('m.position IN ("' . implode('", "', $positions) . '")');
			}

			$db->setQuery($q);

			if (is_array($includes = $db->loadColumn()))
			{

				// Compare include and exclude arrays
				$includes = array_diff($includes, $excludes);

				return count($includes);
			}
		}

		return false;
	}

	/**
	 * Check if current page is rendered by K2 component and/or has any K2 module assigned to.
	 *
	 * @return  boolean
	 */
	public function checkK2()
	{
		return self::checkExt('com_k2', 'mod_k2_');
	}

	/**
	 * Check if current page is rendered by VirtueMart component and/or has any VirtueMart module assigned to.
	 *
	 */
	public function checkVM()
	{
		return self::checkExt('com_virtuemart', 'mod_virtuemart_');
	}

	/**
	 * Check if current page is rendered by VirtueMart component and/or has any VirtueMart module assigned to.
	 *
	 */
	public function checkMTREE()
	{
		return self::checkExt('com_mtree', 'mod_mtree_');
	}
	/**
	 * Convert color HEX to rgba
	 *
	 * @param   string  $hex  color Hex
 	 * @param   string  $op opacity
	 *
	 * @return  string color rgba
	 */
	public function hex2rgb($hex,$op) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
		   $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		   $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		   $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
		   $r = hexdec(substr($hex,0,2));
		   $g = hexdec(substr($hex,2,2));
		   $b = hexdec(substr($hex,4,2));
		}
		$rgb = "rgba(".$r.",". $g .",". $b .",". $op .")";
		
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}
	/**
	 * Render opacity for background module
	 * @param   int  $cm  
	 * @param   int  $preview
	 * @param   array  $cookieStyle
	 * @param   array  $cookieStl
	 * @param   string  $keyworld
 	 * @param   string  $str div id
	 *
	 * @return  string 
	 */
	public function renderOpcity($cm = NULL,$preview,$cookieStyle,$cookieStl,$keyworld) {
			if($preview == 1){
				$arrStyle = $cookieStyle[$keyworld];
			}else{
				$arrStyle = json_decode($cookieStl[$keyworld]);
			}
			$radius ='';
			if (count($arrStyle))
			{
				foreach ($arrStyle as $temp){
					
					if($temp->key === $keyworld.'_container_bo_border_radius'){
						$radius = $temp->value;
					}
					if($temp->key == $keyworld . '_container_ba_backgroundType'){
						if($temp->value == 'img'){
							$cmd=1;
						}
					}
					if(isset($cmd) && !empty($cmd)){
						if($temp->key == $keyworld . '_container_image'){
							$pathImg = $temp->value;
						}
						if($temp->key === $keyworld.'_container_effectColor'){
							$colorEffect = $temp->value;
						}
						if($temp->key === $keyworld.'_container_opacity'){
							$colorOpacity = $temp->value;
						}
						if($temp->key === $keyworld.'_container_imageWidth'){
							$imgW = $temp->value;
						}
						if($temp->key === $keyworld.'_container_imageHeight'){
							$imgH = $temp->value;
						}
						if($pathImg !=''){
							if (strpos($pathImg,'http://') !== false) {
								$pathImg = $pathImg;
							}
							else{
								/* if($_SERVER['SERVER_PORT'] !== ''){
									$pathImg = 'http://' . $_SERVER['SERVER_NAME'] .':'.$_SERVER['SERVER_PORT']. JURI::root(true) .'/'. $pathImg;
								}else{
									$pathImg = 'http://' . $_SERVER['SERVER_NAME'] . JURI::root(true) .'/'. $pathImg;
								} */
								
								$pathImg = JURI::root(true) .'/'. $pathImg;
							}
							$cssBG = ' position:relative;background:url('.$pathImg.');background-size: '.$imgW .' '. $imgH .';';
							$pathImg ='';
						}
					}
				}
			}
			$str ='';
			if($cm ==1 && $keyworld == 'jsn_template'){
				$str.= '<style>#jsn-master'.$cssBG .';#'.str_replace("_", "-", $keyworld).' .row-fluid{position:relative}</style>';
			}else{
				$str.= '<style>#'.str_replace("_", "-", $keyworld).'.row-fluid{position:relative} #'.str_replace("_", "-", $keyworld).' .row-fluid{position:relative}</style>';
			}
			if(isset($colorEffect) && !empty($colorEffect)){
				$str.= '<div class="divOpacity" style="border-radius:'. $radius .';width:100%;height:100%;position:absolute;top:0;left:0;background:'.$this->hex2rgb($colorEffect,$colorOpacity). '"></div>';
			}
		return $str;
	}
}
