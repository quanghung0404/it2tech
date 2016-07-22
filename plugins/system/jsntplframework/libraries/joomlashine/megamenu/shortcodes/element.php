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

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/common.php';

class JSNTplMMShortcodeElement extends JSNTplMMShortcodeCommon
{
	/**
	 * Constructor
	 *
	 * @return type
	 */
	public function __construct() 
	{
		$this->type = 'element';
		$this->config['el_type'] = 'element';
		
		$this->elementConfig();
		$this->elementItems();
		$this->elementItemsExtra();
		$this->shortcodeData();
		parent::__construct();
	}

	/**
	 * Define html structure of shortcode in "Select Elements" Modal
	 *
	 * @param string $data_sort The string relates to Provider name to sort
	 * @return string
	 */
	public function elementButton($data_sort = '') 
	{
		// Prepare variables
		$type  = 'element';
		$dataValue = strtolower($this->config['name']);
	
		$extra = sprintf('data-value="%s" data-type="%s" data-sort="%s"', $dataValue, $dataValue, $dataValue);
	
		return self::elButton($extra, $this->config);
	}

	/**
	 * HTML output for a shortcode in Add Element popover
	 *
	 * @param string $extra
	 * @param array $config
	 * @return string
	 */
	public static function elButton($extra, $config) 
	{
		// Generate icon if necessary
		$icon = isset( $config['icon'] ) ? $config['icon'] : 'jsn-mm-icon-default';
	
		$icon = '<i class="jsn-mm-icon-formfields ' . $icon . '"></i> ';
	
		// Generate data-iframe attribute if needed
		$attr = '';
	
		if (isset( $config['edit_using_ajax'] ) && $config['edit_using_ajax'])
		{
			$attr = ' data-use-ajax="1"';
		}
		if (! isset( $config['description']))
		{
			$config['description'] = '';
		}
		
		return '<li class="jsn-item"' . ( empty( $extra ) ? '' : ' ' . trim( $extra ) ) . '>
		<button data-shortcode="' . $config['shortcode'] . '" class="shortcode-item btn btn-default" title="' . $config['description'] . '"' . $attr . '>
		' . $icon . $config['name'] . '
		<p class="help-block">' . $config['description'] . '</p>
		</button>
		</li>';
	}
	
	/**
	 * Define configuration information of shortcode
	 */
	public function elementConfig() 
	{
	
	}
	
	/**
	 * Define setting options of shortcode
	 */
	public function elementItems() 
	{
	
	}
	
	public function elementShortcode($atts = null, $content = null)
	{

	}

	public function elementItemsExtra() 
	{
		$shotcodeName = $this->config['shortcode'];
		// if not child element
		if (strpos($shotcodeName, 'item_' ) === false) 
		{
			$cssSuffix = array(
					'name'    => JText::_('JSN_TPLFW_MEGAMENU_CSS_CLASS_SUFFIX_TITLE', true),
					'id'      => 'css_suffix',
					'type'    => 'text_field',
					'std'     => '',
					'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_CSS_CLASS_SUFFIX_DESC', true)
			);

			$idWrapper = array(
					'name'    => JText::_('ID'),
					'id'      => 'id_wrapper',
					'type'    => 'text_field',
					'std'     => '',
					'tooltip' => JText::_('Add custom CSS ID for the wrapper div of this element'),
			);
		}


		if ( isset ( $this->items['appearance'] ) )
		{		
			$this->items['appearance'] = array_merge(
					$this->items['appearance'], array(

							$cssSuffix,
							$idWrapper,

					)
			);
		
		}
		else
		{
			if (isset($this->items['Notab']))
			{
				$this->items['Notab'] = array_merge($this->items['Notab'], array($cssSuffix));
			}
		}
	}
	/**
	 * Get params & structure of shortcode
	 */
	public function shortcodeData() 
	{
		$params = JSNTplMMHelperShortcode::generateShortcodeParams($this->items, null, null, false, true);
		// add Margin parameter for Not child shortcode
		if (strpos($this->config['shortcode'], '_item') === false) 
		{
			if ($this->config['shortcode'] == 'jsn_mm_submenu')
			{
				$this->config['params'] = array_merge(array('disabled_el' => 'no', 'css_suffix' => '', 'id_wrapper' => '' ), $params);
			}
			else
			{
				$this->config['params'] = array_merge(array('div_margin_top' => '10', 'div_margin_bottom' => '10', 'disabled_el' => 'no', 'css_suffix' => '', 'id_wrapper' => ''), $params );
			}
		}
		else 
		{
			$this->config['params'] = $params;
		}
	
		$this->config['shortcode_structure'] = JSNTplMMHelperShortcode::generateShortcodeStructure($this->config['shortcode'], $this->config['params']);
	}

	public function elementWrapper($htmlElement, $arrParams, $extraClass = '', $customStyle = '') 
	{
		$shortcodeName = JSNTplMMHelperShortcode::shortcodeName( $this->config['shortcode'] );
		// extract margin here then insert inline style to wrapper div
		$styles = array();
		if ( ! empty ( $arrParams['div_margin_top'] ) ) {
			$styles[] = 'margin-top:' . intval( $arr_params['div_margin_top'] ) . 'px';
		}
		if ( ! empty ($arrParams['div_margin_bottom'] ) ) {
			$styles[] = 'margin-bottom:' . intval( $arrParams['div_margin_bottom'] ) . 'px';
		}
		$style = count( $styles ) ? implode( '; ', $styles ) : '';
		if ( ! empty( $style ) || ! empty( $customStyle ) ){
			$style = "style='$style $customStyle'";
		}
	
		$class        = "jsn-mm-element-container jsn-mm-element-$shortcodeName";
		$extraClass .= ! empty ( $arrParams['css_suffix'] ) ? ' ' . esc_attr( $arrParams['css_suffix'] ) : '';
		$class       .= ! empty ( $extraClass ) ? ' ' . ltrim( $extraClass, ' ' ) : '';
		$extra_id     = ! empty ( $arrParams['id_wrapper'] ) ? ' ' . esc_attr( $arrParams['id_wrapper'] ) : '';
		$extra_id     = ! empty ( $extra_id ) ? "id='" . ltrim( $extra_id, ' ' ) . "'" : '';
		return "<div $extra_id class='$class' $style>" . $htmlElement . '</div>';
	}

	/**
	 * DEFINE html structure of shortcode in MegaMenu area
	 *
	 * @param string $content
	 * @param string $shortcode_data: string stores params (which is modified default value) of shortcode
	 * @param string $el_title: Element Title used to identifying elements in WR MegaMenu
	 * Ex:  param-tag=h6&param-text=Your+heading&param-font=custom&param-font-family=arial
	 * @return string
	 */
	public function elementInMegamenu($content = '', $shortcode_data = '', $el_title = '', $index = '') {
		$shortcode		  = $this->config['shortcode'];
		$is_sub_element   = ( isset( $this->config['sub_element'] ) ) ? true : false;
		$parent_shortcode = ( $is_sub_element ) ? str_replace( 'jsn_tpl_mm_item_', '', $shortcode ) : $shortcode;
		$type			  = ! empty( $this->config['el_type'] ) ? $this->config['el_type'] : 'widget';
	
		// Empty content if this is not sub element
		if ( ! $is_sub_element )
			$content = '';
	
		$exception   = isset( $this->config['exception'] ) ? $this->config['exception'] : array();
		$content     = ( isset( $exception['default_content'] ) ) ? $exception['default_content'] : $content;
		$modal_title = '';
		
		// if content is still empty, Generate it
		if ( empty( $content ) ) {
			if ( ! $is_sub_element )
				$content = ucfirst( str_replace( 'jsn_tpl_mm_', '', $shortcode ) );
			else {
				if ( isset( $exception['item_text'] ) ) {
					if ( ! empty( $exception['item_text'] ) )
						$content = JSNTplMMHelperPlaceholder::addPlaceholder($exception['item_text'] . ' %s', 'index');
				} else
					$content = JSNTplMMHelperPlaceholder::addPlaceholder(ucfirst( $parent_shortcode ) . ' ' . 'Item' . ' %s', 'index');
			}
		}
		
		$content = ! empty( $el_title ) ? ( $content . ': ' . "<span>$el_title</span>" ) : $content;
	
		// element name
		if ( $type == 'element' ) {
			if ( ! $is_sub_element )
				$name = ucfirst( str_replace( 'jsn_tpl_mm_', '', $shortcode ) );
			else
				$name = ucfirst( $parent_shortcode ) . ' ' . 'Item';
		}
		else {
			$name = $content;
		}
		
		if ( empty($shortcode_data) )
			$shortcode_data = $this->config['shortcode_structure'];
	
		// Process index for subitem element
		if ( ! empty( $index ) ) {
			$shortcode_data = str_replace( '_JSN_TPL_MM_INDEX_' , $index, $shortcode_data );
		}
	
		$shortcode_data  = stripslashes( $shortcode_data );
		$element_wrapper = ! empty( $exception['item_wrapper'] ) ? $exception['item_wrapper'] : ( $is_sub_element ? 'li' : 'div' );
		$content_class   = ( $is_sub_element ) ? 'jsn-item-content' : 'jsn-mm-element';
		$modal_title     = empty ( $modal_title ) ? ( ! empty( $exception['data-modal-title'] ) ? "data-modal-title='{$exception['data-modal-title']}'" : '' ) : $modal_title;
		$element_type    = "data-el-type='$type'";
	
		$data = array(
				'element_wrapper' => $element_wrapper,
				'modal_title' => $modal_title,
				'element_type' => $element_type,
				'name' => $name,
				'shortcode' => $shortcode,
				'shortcode_data' => $shortcode_data,
				'content_class' => $content_class,
				'content' => $content,
				'action_btn' => empty( $exception['action_btn'] ) ? '' : $exception['action_btn'],
		);
		$extra = array();
		if ( isset( $this->config['exception']['disable_preview_container'] ) ) {
			$extra = array(
					'has_preview' => FALSE,
			);
		}
		$data = array_merge( $data, $extra );
		$html_preview = JSNTplMMHelperFunctions::getElementItemHtml( $data );
		return array(
				$html_preview
		);
	}	
}