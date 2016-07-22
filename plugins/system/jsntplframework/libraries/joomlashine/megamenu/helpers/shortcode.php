<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

//include_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/loader.php';

class JSNTplMMHelperShortcode 
{
	/**
	 * Pattern variable
	 * 
	 * @var string
	 */
	static $pattern = "";
	
	/**
	 * Group shortcodes
	 * 
	 * @var array
	 */
	static $groupShortcodes = array("group","group_table", "table");
	
	/**
	 * Item HTML template
	 * 
	 * @var array
	 */
	static $itemHtmlTemplate = array(
	    "icon" => "<i class='_JSN_MM_STD_'></i>",
	);

	static $providers = array();

	public static function getShortcodeRegex() 
	{	
		$tagnames = array_keys(self::getShortcodeTags());
		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	
		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
		'\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagregexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
				.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
				.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
				.     ')*?'
				. ')'
				. '(?:'
				.     '(\\/)'                        // 4: Self closing tag ...
				.     '\\]'                          // ... and closing bracket
				. '|'
				.     '\\]'                          // Closing bracket
				.     '(?:'
				.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
				.             '[^\\[]*+'             // Not an opening bracket
				.             '(?:'
				.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
				.                 '[^\\[]*+'         // Not an opening bracket
				.             ')*+'
				.         ')'
				.         '\\[\\/\\2\\]'             // Closing shortcode tag
				.     ')?'
				. ')'
				. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
	
	public static function getshortcodeTags()
	{
		self::registerProvider();
			
		// Get list of shortcode directories
		$scPath = self::shortcodeDirs();

		foreach ($scPath as $path) 
		{
			JSNTplMMLoader::register($path, 'JSNTplMMShortcode');
		}

		// Get list of shortcodes
		$shortcodes = self::shortcodesList($scPath);
	
		return $shortcodes;
	}
	
	/**
	 * Get shortcodes in shortcode directories
	 * @param array $scPath
	 * @return array
	 */
	public static function shortcodesList( $scPath )
	{
		// store all shortcodes
		$shortcodes = array();
		
		//Add shortcodes define for purpose of loading speed
		if (defined('JSN_TPLFRAMEWORK_MEGAMENU_SHORTCODES')) 
		{
			$expShortcodes = explode('|', JSN_TPLFRAMEWORK_MEGAMENU_SHORTCODES);

			foreach ($expShortcodes as $expShortcode)
			{
				if ($expShortcode == 'jsn_tpl_mm_row' || $expShortcode == 'jsn_tpl_mm_column')
				{
					$parentPath = JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout/';
				}
				else
				{
					$parentPath = JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements/';
				}	
				
				$provider   = self::getProvider($parentPath);
				$type	    = $provider['type'];
				$shortcodes[ $expShortcode ] = array( 'type' => $type, 'provider' => $provider );
			}	
		}
		else
		{	
			if (empty($scPath))
			{
				return NULL;
			}
			
			if (! is_array($scPath))
			{
				$scPath = array($scPath);
			}
		
	
			// get list of directory by directory level
			$level_dirs = array();
			
			foreach ( $scPath as $path ) 
			{
				$level_dirs[ substr_count( $path, '/*' ) ][ ] = $path;
				
				while ( $d = glob( $path . '/*', GLOB_ONLYDIR ) ) 
				{
					$path .= '/*';
					
					if (substr_count( $path, '/*' ) <= 1)
					{	
						foreach ( $d as $adir ) 
						{
							$level_dirs[ substr_count( $path, '/*' ) ][ ] = $adir;
						}
					}
					else
					{
						break;
					}
				}
			}

			// traverse over array of path to get shortcode information
			foreach ( $level_dirs as $level => $dirs ) 
			{
				
				foreach ( $dirs as $dir ) 
				{
					// provider info
					$parentPath = str_replace( '/item', '', $dir );
					$provider    = self::getProvider($parentPath);
					
					// shortcode info
					//$type	    = ( $dir == WR_MEGAMENU_LAYOUT_PATH ) ? 'layout' : 'element';
					$type	    = $provider['type'];
					$this_level = ( intval( $level ) > 0 ) ? ( intval( $level ) - 1 ) : intval( $level );
					//
					$append	    = str_repeat( 'item_', $this_level );
					
					if ( is_array( glob( $dir . '/*.php' ) ) && count( glob( $dir . '/*.php' ) ) > 0 ) 
					{
						foreach ( glob( $dir . '/*.php' ) as $file ) 
						{
							$p							 = pathinfo( $file );
							$element					   = str_replace( '-', '_', $p[ 'filename' ] );
							$shortcodeName				= 'jsn_tpl_mm_' . $append . $element;
							$shortcodes[ $shortcodeName ] = array( 'type' => $type, 'provider' => $provider );	
							//self::$providers [$provider['dir']][]	   = $shortcodeName;
							//self::$providers [$provider['name']][]	   = $shortcodeName;
						}
					}
				}
			}
		}
		
		return $shortcodes;
	}	
	/**
	 * Generate options list of shortcode (from $this->items array) OR get value of a option
	 * @param array $arr ($this->items)
	 * @param string|null $paramID (get std of a option by ID)
	 * @param array $new_values (set std for some options ( "pram id" => "new std value" ) )
	 * @param bool $assign_content (set $option['std'] = $new_values['_shortcode_content'] of option which has role = 'content' )
	 * @param bool $extract_content (get $option['std'] of option which has role = 'content' )
	 * @param string $extract_title (get $option['std'] of option which has role|role_2 = 'title' )
	 * @return array
	 */
	static function generateShortcodeParams(&$arr, $paramID = NULL, $new_values = NULL, $assign_content = FALSE, $extract_content = FALSE, $extract_title = '')
	{
		$params = array();
		if ( $arr ) {
			foreach ( $arr as $tab => &$options ) {
				foreach ( $options as &$option ) {
					$type			= isset( $option[ 'type' ] ) ? $option[ 'type' ] : '';
					$option[ 'std' ] = ! isset( $option[ 'std' ] ) ? '' : $option[ 'std' ];
	
					// option has role = 'content'
					if ( isset( $option[ 'role' ] ) && $option[ 'role' ] == 'content' ) {
	
						// set std of this option
						if ( $assign_content ) {
							if ( ! empty( $new_values ) && isset( $new_values[ '_shortcode_content' ] ) ) {
								$option[ 'std' ] = $new_values[ '_shortcode_content' ];
							}
						}
	
						// get std of this option
						if ( $extract_content ) {
							$params[ 'extract_shortcode_content' ][ $option[ 'id' ] ] = $option[ 'std' ];
						} else {
							// remove option which role = content from shortcode structure ( except option which has another role: title )
							if ( ! ( ( isset( $option[ 'role' ] ) && $option[ 'role' ] == 'title' ) || ( isset( $option[ 'role_2' ] ) && $option[ 'role_2' ] == 'title' ) || ( isset( $option[ 'role' ] ) && $option[ 'role' ] == 'title_prepend' ) ) ) {
								unset( $option );
								continue;
							}
						}
					}
					if ( $type != 'preview' ) {
	
						// single option : $option['type'] => string
						if ( ! is_array( $type ) ) {
	
							// if is not parent/nested shortcode
							if ( ! in_array( $type, self::$groupShortcodes ) ) {
	
								// default content
								if ( empty( $new_values ) ) {
									if ( ! empty( $paramID ) ) {
										if ( $option[ 'id' ] == $paramID ) {
											return $option[ 'std' ];
										}
									} else {
										if ( isset( $option[ 'id' ] ) ) {
											$params[ $option[ 'id' ] ] = $option[ 'std' ];
										}
									}
								} // there are new values
								else {
									if ( isset( $option[ 'id' ] ) && array_key_exists( $option[ 'id' ], $new_values ) ) {
										$option[ 'std' ] = $new_values[ $option[ 'id' ] ];
									}
								}
	
								// extract title for element like Table
								if ( ! empty( $extract_title ) ) {
									// default std
									if ( strpos( $option[ 'std' ], JSNTplMMHelperPlaceholder::getPlaceholder( 'index' ) ) !== false ) {
										$option[ 'std' ]		   = '';
										$params[ 'extract_title' ] = JText::_('Untitled');
									} else {
										if ( ( isset( $option[ 'role' ] ) && $option[ 'role' ] == 'title' ) || ( isset( $option[ 'role_2' ] ) && $option[ 'role_2' ] == 'title' ) ) {
											if ( $option[ 'role' ] == 'title' ) {
												$params[ 'extract_title' ] = $option[ 'std' ];
											} else {
												$params[ 'extract_title' ] = JSNTplMMHelperCommon::sliceContent( $option[ 'std' ] );
											}
										} else {
											if ( ( isset( $option[ 'role' ] ) && $option[ 'role' ] == 'title_prepend' ) && ! empty( $option[ 'title_prepend_type' ] ) && ! empty( $option[ 'std' ] ) ) {
												$params[ 'extract_title' ] = JSNTplMMHelperPlaceholder::removePlaceholder( self::$item_html_template[ $option[ 'title_prepend_type' ] ], 'standard_value', $option[ 'std' ] ) . $params[ 'extract_title' ];
											}
										}
									}
								}
							} // nested shortcode
							else {
								// default content
								if ( empty( $new_values ) ) {
									foreach ( $option[ 'sub_items' ] as &$sub_items ) {
										$sub_items[ 'std' ] = ! isset( $sub_items[ 'std' ] ) ? '' : $sub_items[ 'std' ];
										if ( ! empty( $paramID ) ) {
											if ( $sub_items[ 'id' ] == $paramID ) {
												return $sub_items[ 'std' ];
											}
										} else {
											$params[ 'sub_items_content' ][ $option[ 'sub_item_type' ] ][ ] = $sub_items;
										}
									}
								} // there are new values
								else {
									$count_default = count( $option[ 'sub_items' ] );
									$count_real	   = isset( $new_values[ 'sub_items_content' ][ $option[ 'sub_item_type' ] ] ) ? count( $new_values[ 'sub_items_content' ][ $option[ 'sub_item_type' ] ] ) : 0;
									if ( $count_real > 0 ) {
										// there are new sub items
										if ( $count_default < $count_real ) {
											for ( $index = $count_default; $index < $count_real; $index++ ) {
												$option[ 'sub_items' ][ $index ] = array( 'std' => '' );
											}
										} // some sub items are deleted
										else {
											if ( $count_default > $count_real ) {
												for ( $index = $count_real; $index < $count_default; $index++ ) {
													unset( $option[ 'sub_items' ][ $index ] );
												}
											}
										}
	
										// update content for sub items
										array_walk( $option[ 'sub_items' ], array( 'self', 'arrWalkSubsc' ), $new_values[ 'sub_items_content' ][ $option[ 'sub_item_type' ] ] );
									}
								}
							}
						} // nested options : $option['type'] => Array of options
						else {
							// default content
							if ( empty( $new_values ) ) {
								foreach ( $option[ 'type' ] as &$sub_options ) {
									$sub_options[ 'std' ] = ! isset( $sub_options[ 'std' ] ) ? '' : $sub_options[ 'std' ];
	
									if ( ! empty( $paramID ) ) {
										if ( $sub_options[ 'id' ] == $paramID ) {
											return $sub_options[ 'std' ];
										}
									} else {
										$params[ $sub_options[ 'id' ] ] = $sub_options[ 'std' ];
									}
								}
							} // there are new values
							else {
								array_walk( $option[ 'type' ], array( 'self', 'arrWalk' ), $new_values );
							}
						}
	
						if ( isset( $option[ 'extended_ids' ] ) ) {
							foreach ( $option[ 'extended_ids' ] as $_id ) {
								$params[ $_id ] = isset( $option[ $_id ][ 'std' ] ) ? $option[ $_id ][ 'std' ] : '';
							}
						}
					}
				}
			}
		}
	
		return $params;
	}

	/**
	 * Generate shortcode structure from array of params and name of shortcode
	 * @param type $shortcode_name
	 * @param type $params
	 * @return type
	 */
	static function generateShortcodeStructure( $shortcodeName, $params, $content = '' )
	{
		$shortcodeStructure = "[$shortcodeName ";
	
		$arr			= array();
		$excludeParams = array( 'sub_items_content', 'extract_shortcode_content' );
		foreach ( $params as $key => $value ) {
			if ( ! in_array( $key, $excludeParams ) && $key != '' ) {
				$arr[ $key ] = $value;
			}
		}
	
		// get content of param which has: role = content
		if ( ! empty( $params[ 'extract_shortcode_content' ] ) ) {
			foreach ( $params[ 'extract_shortcode_content' ] as $paramId => $std ) {
				unset( $arr[ $paramId ] );
				$content = $std;
			}
		}
	
		foreach ( $arr as $key => $value ) {
			$shortcodeStructure .= "$key=\"$value\" ";
		}
		$shortcodeStructure .= ']';
		$shortcodeStructure .= $content;
		$shortcodeStructure .= "[/$shortcodeName]";
	
		return $shortcodeStructure;
	}
	
	public static function doShortcodeAdmin( $content = '', $column = false, $refine = false )
	{	
		//$content = '[jsn_tpl_mm_column span="span6"][/jsn_tpl_mm_column][jsn_tpl_mm_column span="span6"][/jsn_tpl_mm_column]';
		if ( empty( $content ) ) {
			return '';
		}
		// check if Free Shortcode Plugin is not installed
		// global $shortcode_tags;
		// if ( ! array_key_exists( 'wr_megamenu_text', $shortcode_tags ) ) {
		// return __( 'You have not activated <b>"WR Free Shortcodes"</b> plugin. Please activate it before using MegaMenu.', WR_MEGAMENU_TEXTDOMAIN );
		// }	
	
		$content = trim( $content );
	
		$content_flag = 'X';
		if ( $refine ) {
			// remove duplicator wrapper
			$row_start = '\[jsn_tpl_mm_row';
			$col_start = '\[jsn_tpl_mm_column';
			$row_end   = '\[\/jsn_tpl_mm_row\]';
			$col_end   = '\[\/jsn_tpl_mm_column\]';
			$content   = preg_replace( "/$row_start([^($row_start)|($col_start)]*)$col_start/", '[jsn_tpl_mm_row][jsn_tpl_mm_column', $content );
			$content   = preg_replace( "/$col_end([^($row_end)|($col_end)]*)$row_end/", '[/jsn_tpl_mm_column][/jsn_tpl_mm_row]', $content );
	
			// wrap alone shortcode ( added in Classic Editor )
			$pattern = self::shortcodesPattern( array( 'jsn_tpl_mm_row' => '', 'jsn_tpl_mm_column' => '' ) );
			$append_ = "[jsn_tpl_mm_row][jsn_tpl_mm_column]{$content_flag}[/jsn_tpl_mm_column][/jsn_tpl_mm_row]";
			$content = self::wrapContent( $pattern, $content, $content_flag, $append_ );
			
			
		}
		
		// wrap alone text
		self::$pattern = self::shortcodesPattern();
	
		$pattern = self::$pattern;
		$append_ = $column ? "[jsn_tpl_mm_text]{$content_flag}[/jsn_tpl_mm_text]" : "[jsn_tpl_mm_row][jsn_tpl_mm_column][jsn_tpl_mm_text]{$content_flag}[/jsn_tpl_mm_text][/jsn_tpl_mm_column][/jsn_tpl_mm_row]";
		$content = self::wrapContent( $pattern, $content, $content_flag, $append_ );
		
		return preg_replace_callback( self::$pattern, array( 'self', 'doShortcodeTag' ), $content );
	}

	public static function doShortcodeTag( $m )
	{
	
		// allow [[foo]] syntax for escaping a tag
		if ( $m[ 1 ] == '[' && $m[ 6 ] == ']' ) {
			return substr( $m[ 0 ], 1, -1 );
		}
	
		$tag     = $m[ 2 ];
		$content = isset( $m[ 5 ] ) ? trim( $m[ 5 ] ) : '';
		
		
		return call_user_func( array( 'self', 'shortcodeToMegamenu' ), $tag, $content, $m[ 0 ], $m[ 3 ] );
	}

	/**
	 * Get Shortcode class from shortcode name
	 * @param type $shortcode_name
	 * @return type
	 */
	static function getShortcodeClass( $shortcodeName )
	{
		$arr  		= explode('_', $shortcodeName);
		$postfix 	= ucwords(end($arr));
		
		//$shortcodeName = str_replace( 'jsn_tpl_mm_', 'JSNTPLMMShortcode', $shortcodeName );
		$shortcodeName 	= str_replace( $shortcodeName, 'JSNTplMMShortcode' . $postfix, $shortcodeName );
		//$shortcode	  = str_replace( '_', ' ', $shortcode_name );
		$shortcode		= $shortcodeName;	
		$class		  	= ucwords($shortcode);
		
		return $class;
	}
	/**
	 * Return html structure of shortcode in MegaMenu area
	 * @param type $shortcode_name
	 * @param type $attr
	 * @param type $content
	 */
	public static function shortcodeToMegamenu( $shortcode_name, $content = '', $shortcode_data = '', $shortcode_params = '' )
	{
		$class = self::getShortcodeClass( $shortcode_name );
		
		if ( class_exists( $class ) ) {
			
			//global $wr_megamenu_element;
			$objJSNTplMMElement = new JSNTplMMElement;
			$objJSNTplMMElement->init();
			$elements = $objJSNTplMMElement->getElements();
			
			$instance = isset( $elements[ 'element' ][ strtolower( $class ) ] ) ? $elements[ 'element' ][ strtolower( $class ) ] : null;
			
			if (! is_object($instance)) 
			{
				$instance = new $class();
			}
			$el_title = '';
			//if ( $class != 'WR_Megamenu_Widget' ) {
				// extract param of shortcode ( now for column )
				if ( isset( $instance->config[ 'extract_param' ] ) ) {
					parse_str( trim( $shortcode_params ), $output );
					foreach ( $instance->config[ 'extract_param' ] as $param ) {
						if ( isset( $output[ $param ] ) ) {
							$instance->params[ $param ] = JSNTplMMHelperCommon::removeQuotes( $output[ $param ] );
						}
					}
				}
	
				// get content in megamenu of shortcode: Element Title must always first option of Content tab
				if ( isset( $instance->items[ 'content' ] ) && isset( $instance->items[ 'content' ][ 0 ] ) ) {
					$title = $instance->items[ 'content' ][ 0 ];
					if ( @$title[ 'role' ] == 'title' ) {
						$params   = self::shortcodeParseAtts( $shortcode_params );
						$el_title = ! empty( $params[ $title[ 'id' ] ] ) ? $params[ $title[ 'id' ] ] : JText::_('(Untitled)');
					}
				}
//			} 			
// 			else 
// 			{
// 				$widget_info                     = WR_Megamenu_Helpers_Shortcode::extract_widget_params( $shortcode_data );
// 				$el_title                        = ! empty( $widget_info[ 'title' ] ) ? $widget_info[ 'title' ] : '';
// 				$params                          = WR_Megamenu_Helpers_Shortcode::extract_params( $shortcode_data );
// 				$instance->config[ 'shortcode' ] = $params[ 'widget_id' ];
// 				$instance->config[ 'el_type' ]   = 'widget';
// 			}
				
			$shortcode_view = $instance->elementInMegamenu($content, $shortcode_data, $el_title);
	
			return $shortcode_view[0];
		}
	}

	public static function shortcodeParseAtts($text) 
	{
		$atts = array();
		$pattern = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) && strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}
		return $atts;
	}	
	/**
	 * Split string by regular expression, then replace nodes by string ( [wrapper string]node content[/wrapper string] )
	 * @param type $pattern
	 * @param type $content
	 * @param type $content_flag
	 * @param type $append_
	 * @return type string
	 */
	private static function wrapContent( $pattern, $content, $content_flag, $append_ )
	{
		$nodes	  = preg_split( $pattern, $content, -1, PREG_SPLIT_OFFSET_CAPTURE );
		$idx_change = 0;
		foreach ( $nodes as $node ) {
			$replace   = $node[ 0 ];
			$empty_str = self::checkEmpty( $content );
			if ( strlen( trim( $replace ) ) && strlen( trim( $empty_str ) ) ) {
				$offset	   = intval( $node[ 1 ] ) + $idx_change;
				$replace_html = $replace;

				$content     = substr_replace( $content, str_replace( $content_flag, $replace_html, $append_ ), $offset, strlen( $replace ) );
				$idx_change += strlen( $append_ ) - strlen( $content_flag ) - ( strlen( $replace ) - strlen( $replace_html ) );
			}
		}

		return $content;
	}	
	
	/**
	 * Check if string is empty (no real content)
	 * @param type $content
	 * @return type
	 */
	public static function checkEmpty( $content )
	{
		$content = preg_replace( '/<p[^>]*?>/', '', $content );
		$content = preg_replace( '/<\/p>/', '', $content );
		$content = preg_replace( '/["\']/', '', $content );
		$content = str_replace( '&nbsp;', '', $content );
	
		return $content;
	}	
	/**
	 * Generate shortcode pattern ( for Wr shortcodes only )
	 * @global type $shortcode_tags
	 * @return pattern which contains only shortcodes of WR MegaMenu
	 */
	public static function shortcodesPattern( $tags = '' )
	{
		$pattern              = self::getShortcodeRegex();
		return "/$pattern/s";
	}

	/**
	 * Get shortcode directories of providers
	 * @return type
	 */
	public static function shortcodeDirs()
	{
		$shortcodeDirs = array();
		$shortcodeDirs[] = JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout';
		$shortcodeDirs[] = JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements';
		return $shortcodeDirs;
	}	
	/**
	 * Modify value in array of sub-shortcode
	 *
	 * @param type $value
	 * @param type $key
	 * @param type $new_values
	 */
	static function arrWalkSubsc(&$value, $key, $newValues) 
	{
		$value['std'] = $newValues[$key];
	}
	
	/**
	 * Modify value in array
	 *
	 * @param type $value
	 * @param type $key
	 * @param type $new_values
	 */
	static function arrWalk(&$value, $key, $newValues) 
	{
		if (array_key_exists($value['id'], $newValues))
		{
			$value['std'] = $newValues[$value['id']];
		}
	}
	
	/**
	 * Set information for WooRockets provider
	 * @return type
	 */
	public static function registerProvider() {
		
		$providers = array();
		
		$providers [JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout/'] = array(
					'path'             => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout/',
					'uri'              => JUri::root() . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/shortcodes/layout/',
					'name'             => 'Layout Elements',
					'type'			   => 'layout',
					'shortcode_dir'	   => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout',
					'js_shortcode_dir' => array(
						'path' => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/assets/js/shortcodes/layout',
						'uri'  => JUri::root() . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/shortcodes/assets/js/shortcodes/layout',
					),
				);

		$providers [JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements/'] = array(
				'path'             => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements/',
				'uri'              => JUri::root() . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/shortcodes/elements/',
				'name'             => 'Item Elements',
				'type'			   => 'element',
				'shortcode_dir'	   => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements',
				'js_shortcode_dir' => array(
						'path' => JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/assets/js/shortcodes/elements',
						'uri'  => JUri::root() . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/shortcodes/assets/js/shortcodes/elements',
				),
		);
		
		self::$providers = $providers;
	}
	
	/**
	 * Get provider name & path of a shortcode directory
	 * @param type $shortcode_dir
	 * @return type
	 */
	public static function getProvider( $shortcodeDir )
	{
		foreach (self::$providers as $dir => $provider) 
		{
			foreach ((array)$provider[ 'shortcode_dir' ] as $dir) 
			{
				if (strpos($shortcodeDir, $dir) !== false) 
				{
					return array(
							'name' => $provider['name'],
							'dir'  => $dir,
							'type'  => $provider['type'],
					);
				}
			}
		}
	}

	public static function shortcodeAtts($pairs, $atts, $shortcode = '') 
	{
		$atts = (array)$atts;
		$out = array();
		foreach($pairs as $name => $default) {
			if ( array_key_exists($name, $atts) )
				$out[$name] = $atts[$name];
			else {
// 				if ( is_string( $default ) && strpos( $default, 'PB_INDEX_TRICK' ) !== false ) {
// 					$out[$name] = '';
// 				} else {
// 					$out[$name] = $default;
// 				}

				$out[$name] = $default;
			}
		}
	
		if ( $shortcode )
			$out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts );
	
		return $out;
	}

	/**
	 * Return shortcode name without 'jsn_mm_' prefix
	 * @param type $wr_shortcode_name
	 * @return type
	 */
	public static function shortcodeName( $shortcodeName )
	{
		return str_replace( 'jsn_tpl_mm_', '', $shortcodeName );
	}
	
	/**
	 * Extract shortcode params from string
	 * Ex: [param-tag=h3&param-text=Your+heading+text&param-font=custom]
	 * @param type $param_str
	 * @return array
	 */
	public static function extractParams($paramStr, $strShortcode = '')
	{
		$paramStr = stripslashes($paramStr);
		$params	   = array();
		// get params of shortcode
		//preg_match_all( '/[A-Za-z0-9_-]+=\"[^"\']*\"/u', $paramStr, $tmpParams, PREG_PATTERN_ORDER );
		preg_match_all( '/[A-Za-z0-9_-]+="[^"]*"/u', $paramStr, $tmpParams, PREG_PATTERN_ORDER );
		foreach ( $tmpParams[ 0 ] as $paramValue ) 
		{
			$output = array();
			//preg_match_all( '/([A-Za-z0-9_-]+)=\"([^"\']*)\"/u', $paramValue, $output, PREG_SET_ORDER );
			preg_match_all( '/([A-Za-z0-9_-]+)="([^"]*)"/u', $paramValue, $output, PREG_SET_ORDER );
			foreach ( $output as $item ) 
			{
				if ( ! in_array( $item[1], array( 'disabled_el', 'css_suffix' ) ) || ! isset ( $params[ $item[ 1 ] ] ) ) {
					$params[ $item[1]] = urldecode($item[2]);
				}
			}
		}
		$pattern = self::getShortcodeRegex();
		preg_match_all( '/' . $pattern . '/s', $paramStr, $tmpParams, PREG_PATTERN_ORDER );
		$content                        = isset($tmpParams[5][ 0 ] ) ? trim($tmpParams[5][0]) : '';
		$content                        = preg_replace('/rich_content_param-[a-z_]+=/', '', $content);
		$params['_shortcode_content'] 	= $content;
	
		return $params;
	}

	/**
	 * Extract sub-shortcode content of a shortcode
	 * @param type $content
	 * @param type $recursion
	 * @return type
	 */
	public static function extractSubShortcode($content = '', $recursion = false)
	{
		if ( empty( self::$pattern ) ) 
		{
			self::$pattern = self::shortcodesPattern();
		}
		
		preg_match_all( self::$pattern, $content, $out );
		if ( $recursion ) 
		{
			return self::extractSubShortcode( $out[ 5 ][ 0 ] );
		}
	
		// categorize sub shortcodes content
		$subScTags = array();
	
		// sub sortcodes content
		$subScData = $out[ 0 ];
	
		foreach ( $subScData as $scSata )
		{
	
			// get shortcode name
			preg_match( '/\[([^\s]+)/', $scSata, $matches );
			if ( $matches ) 
			{
				$scClass					= self::getShortcodeClass( $matches[ 1 ] );
				$subScTags[ $scClass ][ ] = $scSata;
			}
		}
		
		return $subScTags;
	}	
	
	/**
	 * Render HTML code for shortcode's parameter type
	 * (used in shortcode setting modal)
	 * @param string $type Type name
	 * @param string $element
	 * @return string HTML
	 */
	public static function renderParameter($type, $element = '', $extraParams = null)
	{
		$typeString = self::ucname( $type );
		
		$class	   = 'JSNTplHelperHtml' . $typeString;
	
		if (class_exists($class)) 
		{
			return call_user_func(array( $class, 'render'), $element, $extraParams);
		}
	
		return false;
	}
	
	/**
	 * Move this function to a common file
	 * @param string $string
	 * @return string
	 */
	public static function ucname( $string )
	{
		$string = strtolower( $string );
		
		foreach (array( '_', '\'' ) as $delimiter)
		{
			if (strpos($string, $delimiter) !== false) 
			{
				$string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
				
			}
		}
	
		return strtolower(str_replace('_', '', $string));
	}	
	
	public static function doShortcodeFrontend($content)
	{	
		$content = trim( $content );		
		$contentFlag = 'X';
		$pattern 	= self::getShortcodeRegex();
		$content	= self::wrapContent("#{$pattern}#", $content, $contentFlag, $contentFlag);
		
		return preg_replace_callback("#{$pattern}#", array('self', 'doShortcodeTagFrontend'), $content);
	}

	public static function doShortcodeTagFrontend( $m )
	{
	
		// allow [[foo]] syntax for escaping a tag
		if ( $m[ 1 ] == '[' && $m[ 6 ] == ']' ) {
			return substr( $m[ 0 ], 1, -1 );
		}
	
		$tag     = $m[ 2 ];
		$content = isset( $m[ 5 ] ) ? trim( $m[ 5 ] ) : '';
	
	
		return call_user_func( array( 'self', 'shortcodeToMegamenuFrontend' ), $tag, $content, $m[ 0 ], $m[ 3 ] );
	}
	
	public static function shortcodeToMegamenuFrontend($shortcodeName, $content = '', $shortcodeData = '', $shortcodeParams = '')
	{
		$class = self::getShortcodeClass( $shortcodeName );
		
		if (class_exists($class) ) 
		{
			$tmpElements = array();
			//global $wr_megamenu_element;
			$objJSNTplMMElement = new JSNTplMMElement;
		
			$objJSNTplMMElement->init();

			$elements = $objJSNTplMMElement->getElements();
			$tmpElements = array_merge($tmpElements, $elements['layout']);
			$tmpElements = array_merge($tmpElements, $elements['element']);
			
			
			$instance = isset($tmpElements[strtolower($class)]) ? $tmpElements[strtolower($class)] : null;
			
			if (! is_object($instance))
			{
				$instance = new $class();
			}
			
			$el_title = '';
			
			if ( isset( $instance->config[ 'extract_param' ] ) ) {
				parse_str( trim( $shortcodeParams ), $output );
				foreach ( $instance->config[ 'extract_param' ] as $param ) {
					if ( isset( $output[ $param ] ) ) {
						$instance->params[ $param ] = JSNTplMMHelperCommon::removeQuotes( $output[ $param ] );
					}
				}
			}
			
			$params   = self::shortcodeParseAtts($shortcodeParams);
		
			$shortcodeView = $instance->elementShortcode($params, $content);
			
			return $shortcodeView;
		}		
	}
	
	public static function removeAutop($content) 
	{
		$content	= preg_replace('#<p>[\s\t\r\n]*(\[)#', '$1', $content);
		$content	= preg_replace('#(\])[\s\t\r\n]*</p>#', '$1', $content);
	
		$shortcode_tags = array();
		$tagregexp = join( '|', array_map( 'preg_quote', $shortcode_tags ) );
	
		// opening tag
		$content = preg_replace( "/(<p>)?\[($tagregexp)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
	
		// closing tag
		$content = preg_replace( "/(<p>)?\[\/($tagregexp)](<\/p>|<br \/>)?/", '[/$2]', $content );
	
	
		$content = preg_replace( '#^<\/p>|^<br\s?\/?>|<p>$|<p>\s*(&nbsp;)?\s*<\/p>#', '', $content );
	
		return ( $content );
	}	
}