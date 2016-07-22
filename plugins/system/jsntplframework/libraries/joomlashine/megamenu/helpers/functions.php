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

class JSNTplMMHelperFunctions 
{
	/**
	 * Get html item
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	static function getElementItemHtml( $data ) {
		
		$default = array(
				'element_wrapper' => '',
				'modal_title'     => '',
				'element_type'    => '',
				'name'            => '',
				'shortcode'       => '',
				'shortcode_data'  => '',
				'content_class'   => '',
				'content'         => '',
				'action_btn'      => '',
				'has_preview'     => true,
				'this_'           => '',
		);
		$data = array_merge( $default, $data );
		extract( $data );
	
		$preview_html = '';
		if ( $has_preview ) {
			$preview_html = '<div class="shortcode-preview-container" style="display: none">
			<div class="shortcode-preview-fog"></div>
			<div class="jsn-overlay jsn-bgimage image-loading-24"></div>
			</div>';
		}
	
		$extra_class  = JSNTplMMHelperPlaceholder::getPlaceholder( 'extra_class' );
		
		$custom_style = JSNTplMMHelperPlaceholder::getPlaceholder( 'custom_style' );
		$other_class  = '';
	
		$tag = $shortcode;
	

		preg_match_all( '/\[' . $tag . '\s+([A-Za-z0-9_-]+=\"[^"\']*\"\s*)*\s*\]/', $shortcode_data, $rg_sc_params );
	
		if ( ! empty( $rg_sc_params[0] ) ) {
			$sc_name_params = ! empty( $rg_sc_params[0][0] ) ? $rg_sc_params[0][0] : $rg_sc_params[0];
			if ( strpos( $sc_name_params , 'disabled_el="yes"' ) !== false ) {
				$other_class = 'disabled';
			}
		}
	
		// Remove empty value attributes of shortcode tag.
		$shortcode_data = preg_replace( '/\[*([a-z_]*[\n\s\t]*=[\n\s\t]*""\s)/', '', $shortcode_data );
	
		// Content
		$content = $content;
	
		//$content = apply_filters( 'jsn_mm_content', $content, $shortcode_data, $shortcode );
	
		// action buttons
		$buttons = array(
				'edit'       => '<a href="#" onclick="return false;" title="' . JText::_('JSN_TPLFW_MEGAMENU_EDIT_ELEMENT', true) . '" data-shortcode="' . $shortcode . '" class="element-edit"><i class="icon-pencil"></i></a>',
				'clone'      => '<a href="#" onclick="return false;" title="' . JText::_('JSN_TPLFW_MEGAMENU_DUPLICATE_ELEMENT', true) . '" data-shortcode="' . $shortcode . '" class="element-clone"><i class="icon-copy"></i></a>',
				//'deactivate' => '<a href="#" onclick="return false;" title="' . JText::_('Deactivate element') . '" data-shortcode="' . $shortcode . '" class="element-deactivate"><i class="icon-checkbox-unchecked"></i></a>',
				'delete'     => '<a href="#" onclick="return false;" title="' . JText::_('JSN_TPLFW_MEGAMENU_DELETE_ELEMENT', true) . '" class="element-delete"><i class="icon-trash"></i></a>'
		);
	
		/*if ( ! empty ( $other_class ) ) {
			$buttons = array_merge(
					$buttons, array(
							'deactivate' => '<a href="#" onclick="return false;" title="' . JText::_('Activate element') . '" data-shortcode="' . $shortcode . '" class="element-deactivate"><i class="icon-checkbox-partial"></i></a>',
					)
			);
		}*/
	
		$action_btns = ( empty( $action_btn ) ) ? implode( '', $buttons ) : $buttons[$action_btn];
		//$buttons     = apply_filters( 'jsn_mm_button', "<div class='jsn-iconbar'>$action_btns</div>", $shortcode_data, $shortcode );
		$buttons	= "<div class='jsn-iconbar'>$action_btns</div>";
	
		return "<$element_wrapper class='jsn-item jsn-element ui-state-default jsn-iconbar-trigger shortcode-container $extra_class $other_class' $modal_title $element_type data-name='$name' $custom_style>
		<i class='drag-element-icon'></i>
		<textarea class='hidden shortcode-content' shortcode-name='$shortcode' data-sc-info='shortcode_content' name='shortcode_content[]' >$shortcode_data</textarea>
		<div class='$content_class'>$content</div>
		$buttons
		$preview_html
		</$element_wrapper>";
	}

	/**
	 * Check if current page is modal page
	 *
	 * @return type
	 */
	public static function isModal() {
		
		$app = JFactory::getApplication();
		$modal = $app->input->getVar('modal', '');
		
		if ($modal == 'yes') return true;
		
		return false;
		
	}
	
	public static function printAssetTag($src, $type = 'css', $media = 'screen', $inline = false, $echo = true) 
	{
		$tag = '';
		if ($type == 'css') 
		{
			if (!$inline) 
			{
				$tag = '<link rel="stylesheet" href="' . $src . '" type="text/css" media="' . $media . '" />';
			}
			else
			{
				$tag = '<style type="text/css">' . $src . '</style>';
			}
				
		}
		else if ($type == 'js')
		{
			if (!$inline) 
			{
				$tag = ' <script src="' . $src . '" type="text/javascript"></script>';
			}
			else
			{
				$tag = '<script type="text/javascript">' . $src . '</script>';
			}
		}
		if ($echo)
		{
			echo $tag;
		}
		else
		{
			return $tag;
		}
	}	
}