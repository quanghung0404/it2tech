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

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/layout.php';


class JSNTplMMShortcodeRow extends JSNTplMMShortcodeLayout
{
	/**
	 * Constructor
	 *
	 * @return type
	 */
	public function __construct() 
	{		
		$this->type = 'layout';
		parent::__construct();
	}
	
	/**
	 * DEFINE configuration information of shortcode
	 */
	public function elementConfig() 
	{
		$this->config['shortcode'] = 'jsn_tpl_mm_row';
	}

	/**
	 * contain setting items of this element ( use for modal box )
	 *
	 */
	public function elementItems() {
		
		$this->items = array(
				'Notab' => array(
	
						
						array(
								'name'    => JText::_('JSN_TPLFW_MEGAMENU_CSS_CLASS_SUFFIX_TITLE', true),
								'id'      => 'css_suffix',
								'type'    => 'text_field',
								'std'     => 'jsn-tpl-mm-item',
								'tooltip' => JText::_('JSN_TPLFW_MEGAMENU_CSS_CLASS_SUFFIX_DESC', true),
						)
				)
		);
	}
		
	public function elementInMegamenu($content = '', $shortcodeData = '')
	{
		if (empty($content))
		{
			$column = new JSNTplMMShortcodeColumn();
			$columnHtml = $column->elementInMegamenu();
			$columnHtml = $columnHtml[0];
		} 
		else
		{
			$columnHtml = JSNTplMMHelperShortcode::doShortcodeAdmin($content);
		}
		
		if (empty($shortcodeData))
		{
			$shortcodeData = $this->config['shortcode_structure'];
		}
			
		$shortcodeData = explode( '][', $shortcodeData );
		$shortcodeData = $shortcodeData[0] . ']';
		
		// Remove empty value attributes of shortcode tag.
		$shortcodeData	= preg_replace('/\[*([a-z_]*[\n\s\t]*=[\n\s\t]*"")/', '', $shortcodeData);
		
		$customStyle = JSNTplMMHelperPlaceholder::getPlaceholder( 'custom_style' );
		
		$row[] = '<div class="jsn-row-container ui-sortable row-fluid shortcode-container" ' . $customStyle . '>
					<textarea class="hidden" data-sc-info="shortcode_content" name="shortcode_content[]" >' . $shortcodeData . '</textarea>
					<div class="jsn-iconbar left">
						<a href="javascript:void(0);" title="' . JText::_('JSN_TPLFW_MEGAMENU_MOVE_UP', true) . '" class="jsn-move-up disabled"><i class="icon-chevron-up"></i></a>
						<a href="javascript:void(0);" title="' . JText::_('JSN_TPLFW_MEGAMENU_MOVE_DOWN', true) . '" class="jsn-move-down disabled"><i class=" icon-chevron-down"></i></a>
					</div>
					<div class="jsn-mm-row-content">
						' . $columnHtml . '
					</div>
					<div class="jsn-iconbar jsn-vertical">
						<a href="javascript:void(0);" onclick="return false;" class="add-container" title="' . JText::_('JSN_TPLFW_MEGAMENU_ADD_COLUMN', true) . '"><i class="icon-plus"></i></a>
						<a href="javascript:void(0);" onclick="return false;" class="jsn-mm-item-delete row" title="' . JText::_('JSN_TPLFW_MEGAMENU_DELETE_ROW', true) . '"><i class="icon-trash"></i></a>
					</div>
					<textarea class="hidden" name="shortcode_content[]" >[/' . $this->config['shortcode'] . ']</textarea>
				</div>';
		return $row;			
	}
	
	/**
	 * get params & structure of shortcode
	 */
	public function shortcodeData() 
	{
		$this->config['params'] = JSNTplMMHelperShortcode::generateShortcodeParams( $this->items, null, null, false, true );
		$this->config['shortcode_structure'] = JSNTplMMHelperShortcode::generateShortcodeStructure( $this->config['shortcode'], $this->config['params'] );
	}
	
	public function elementShortcode($atts = null, $content = null)
	{	
		$extraClass 	= '';
		$html 			= '';
		$extraClass 	.= ! empty ($atts['css_suffix']) ? ' ' . $atts['css_suffix'] : '';
		$content 		= empty($content) ? JSNTplMMHelperShortcode::removeAutop($content) : JSNTplMMHelperShortcode::doShortcodeFrontend($content);
		$html 			.= '<div class="row jsn-tpl-mm-row-element' .  $extraClass . '">' . $content . '</div>';		
		return $html;
	}	
}