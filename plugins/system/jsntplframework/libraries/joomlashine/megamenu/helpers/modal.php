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

class JSNTplMMHelperModal
{
	private static $instance;

	/**
	 * Base URL of joomla instance
	 * @var string
	 */
	protected $baseUrl;
	
	protected $parent;
	
	public static function getInstance($parent = null) 
	{
		if (! self::$instance)
		{
			self::$instance = new JSNTplMMHelperModal($parent);
		}
		
		return self::$instance;
	}

	public function __construct($parent = null) 
	{
		$this->parent = $parent;
		$this->baseUrl = JURI::root(true) . '/plugins/system/jsntplframework/assets/';
		$this->app = JFactory::getApplication();
		$this->enqueueAssets();
	}
	
	public function enqueueAssets() 
	{
		
	}
	
	/**
	 * Get related content for each Modal
	 * @param type $page
	 */
	public function showModal($page = '')
	{
		//add_action( 'wr_megamenu_modal_page_content', array( &$this, 'content' . $page ) );
		call_user_func(array($this, 'renderContent' . ucfirst($page) . 'Page'));
		
	}
	
	public function renderContentElementPage()
	{
		$post = $this->app->input->getArray($_POST);
		extract($post);
		$objJSNTplMMElement = $this->parent->objJSNTplMMElement;
		include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/templates/modal.php';
	}

	/**
	 * Ignore settings key in array
	 * @param array $options
	 * @return array
	 */
	public static function ignoreSettings( $options ) 
	{
		if ( array_key_exists( 'settings', $options ) ) 
		{
			$options = array_slice( $options, 1 );
		}
	
		return $options;
	}

	/**
	 * Add setting data to a tag
	 * @param string $tag
	 * @param array $data
	 * @param string $content
	 * @return string
	 */
	static function tabSettings( $tag, $data, $content ) {
		$tag_data = array();
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ! empty( $value) ) {
					$tag_data[] = "$key = '$value'";
				}
			}
		}
		$tag_data = implode( ' ', $tag_data );
	
		return "<$tag $tag_data>$content</$tag>";
	}
	
	/**
	 * get HTML of Modal Settings Box of Shortcode
	 * @param array $options
	 * @return string
	 */
	public static function getShortcodeModalSettings( $settings, $shortcode = '', $input_params = null, $raw_shortcode = null ) 
	{
		$i    = 0;
		$tabs = $contents = $actions = $general_actions = array();
		
		if ($shortcode != '')
		{	
			$shortcodeName = strtolower(JSNTplMMHelperShortcode::shortcodeName($shortcode));
			
			if ($shortcodeName != '')
			{
				JSNTplMMLoader::register(JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/elements/' . $shortcodeName . '/html', 'JSNTplHelperHtml');
			}	
		}
		
		foreach ( $settings as $tab => $options ) {
			$options = self::ignoreSettings( $options );
			
			if ( $tab == 'action' ) {
				foreach ( $options as $option ) {
					$actions[] = JSNTplMMHelperShortcode::renderParameter( $option['type'], $option );
				}
			} else if ( $tab == 'generalaction' ) {
				foreach ( $options as $option ) {
					$option['id']      = isset($option['id']) ? ( 'param-' . $option['id']) : '';
					$general_actions[] = JSNTplMMHelperShortcode::renderParameter( $option['type'], $option );
				}
			} else {
				$active = ($i++ == 0) ? 'active' : '';
				if ( strtolower( $tab ) != 'notab' ) {
					$data_                = isset($settings[$tab]['settings']) ? $settings[$tab]['settings'] : array();
					$data_['href']		  = "#$tab";
					$data_['data-toggle'] = 'tab';
					$content_             = ucfirst( $tab );
					$tabs[]               = "<li class='$active'>" . self::tabSettings( 'a', $data_, $content_ ) . '</li>';
				}
	
				$has_margin = 0;
				$param_html = array();
				foreach ( $options as $idx => $option ) {
					// check if this element has Margin param (1)
					if (isset($option['name']) && $option['name'] == 'Margin' && $option['id'] != 'div_margin' )
						$has_margin = 1;
					// if (1), don't use the 'auto extended margin ( top, bottom ) option'
					if ($has_margin && isset($option['id']) && $option['id'] == 'div_margin' )
						continue;
	
					$type		 = $option['type'];
					$option['id'] = isset($option['id']) ? ( 'param-' . $option['id']) : "$idx";
					if ( ! is_array( $type ) ) {
						$param_html[$option['id']] = JSNTplMMHelperShortcode::renderParameter( $type, $option, $input_params );
					} else {
						$output_inner = '';
						foreach ( $type as $sub_options ) {
							$sub_options['id'] = isset( $sub_options['id'] ) ? ( 'param-' . $sub_options['id'] ) : '';
							/* for sub option, auto assign bound = 0 {not wrapped by <div class='controls'></div> } */
							$sub_options['bound'] = '0';
							/* for sub option, auto assign 'input-small' class */
							$sub_options['class'] = isset($sub_options['class']) ? ($sub_options['class']) : '';
							$type                 = $sub_options['type'];
							$output_inner        .= JSNTplMMHelperShortcode::renderParameter( $type, $sub_options );
						}
						$option                    = JSNTplMMHelperHtml::getExtraInfo( $option );
						$label                     = JSNTplMMHelperHtml::getLabel( $option );
						$param_html[$option['id']] = JSNTplMMHelperHtml::finalElement( $option, $output_inner, $label );
					}
				}
	
				if ( ! empty( $param_html['param-copy_style_from'] ) ) {
					array_pop( $param_html );
					// move "auto extended margin ( top, bottom ) option" to top of output
					$style_copy = array_shift( $param_html );
					// Shift Preview frame from the array
					$preview = array_shift( $param_html );
	
					if ( ! empty( $param_html['param-div_margin'] ) ) {
						$margin	    = $param_html['param-div_margin'];
						$param_html = array_merge(
								array(
										$preview,
										$style_copy,
										$margin,
								),
								$param_html
						);
					} else {
						$param_html = array_merge(
								array(
										$preview,
										$style_copy,
								),
								$param_html
						);
					}
				}
	
				$param_html  = implode( '', $param_html );
				$content_tab = "<div class='tab-pane $active jsn-mm-setting-tab' id='$tab'>$param_html</div>";
				$contents[]  = $content_tab;
			}
		}
	
		return self::settingTabHtml( $shortcode, $tabs, $contents, $general_actions, $settings, $actions );
	}

	/**
	 * Generate tab with content, use for generating Modal
	 * @return string
	 */
	static function settingTabHtml( $shortcode, $tabs, $contents, $general_actions, $settings, $actions )
	{
		$output = '<input type="hidden" value="' . $shortcode . '" id="shortcode_name" name="shortcode_name" />';
	
		/* Tab Content - Styling */
	
		$output .= '<div class="jsn-tabs">';
		if ( count( $tabs ) > 0 ) {
			$output .= '<ul class="" id="jsn_mm_option_tab">';
			$output .= implode( '', $tabs );
			$output .= '</ul>';
		}
		/* Tab Content */
	
		$output .= implode( '', $contents );
	
		$output .= "<div class='jsn-buttonbar jsn_mm_action_btn'>";
	
		/* Tab Content - General actions */
		if ( count( $general_actions ) ) {
			$data     = $settings['generalaction']['settings'];
			$content_ = implode( '', $general_actions );
			$output  .= self::tabSettings( 'div', $data_, $content_ );
		}
	
		$output .= implode( '', $actions );
		$output .= '</div>';
		$output .= '</div>';
	
		return $output;
	}	
}