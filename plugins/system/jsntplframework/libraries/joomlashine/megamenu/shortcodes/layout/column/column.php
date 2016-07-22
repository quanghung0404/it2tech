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

class JSNTplMMShortcodeColumn extends JSNTplMMShortcodeLayout
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
		$this->config['shortcode'] = 'jsn_tpl_mm_column';
		$this->config['extract_param'] = array('span');
	}
	
	/**
	 *
	 * @param type $content			 : inner shortcode elements of this column
	 * @param string $shortcode_data
	 * @return string
	 */
	public function elementInMegamenu( $content = '', $shortcodeData = '' ) 
	{
		$columnHtml    = empty($content) ? '' : JSNTplMMHelperShortcode::doShortcodeAdmin( $content, true );
		$span           = ( ! empty($this->params['span'] ) ) ? $this->params['span'] : 'span12';
		$shortcodeData = '[' . $this->config['shortcode'] . ' span="' . $span . '"]';
			
		// Remove empty value attributes of shortcode tag.
		$shortcodeData	= preg_replace( '/\[*([a-z_]*[\n\s\t]*=[\n\s\t]*"")/', '', $shortcodeData );
			
		$rnd_id   = JSNTplMMHelperCommon::randomString();
		$column[] = '<div class="jsn-column-container clearafter shortcode-container ">
						<div class="jsn-column ' . $span . '">
							<div class="thumbnail clearafter">
								<textarea class="hidden" name="shortcode_content[]" >' . $shortcodeData . '</textarea>
								<div class="jsn-column-content item-container" data-column-class="' . $span . '" >
								<div class="jsn-handle-drag jsn-horizontal jsn-iconbar-trigger"><div class="jsn-iconbar layout"><a class="jsn-mm-item-delete column" onclick="return false;" title="' . JText::_('JSN_TPLFW_MEGAMENU_DELETE_COLUMN', true) . '" href="#"><i class="icon-trash"></i></a></div></div>
								<div class="jsn-element-container item-container-content">' . $columnHtml . '</div>
								<a class="jsn-add-more jsn-mm-more-element" href="javascript:void(0);"><i class="icon-plus"></i>' . JText::_('JSN_TPLFW_MEGAMENU_ADD_ELEMENT', true) . '</a>
							</div>
								<textarea class="hidden" name="shortcode_content[]" >[/' . $this->config['shortcode'] . ']</textarea>
							</div>
						</div>
					</div>';
		return $column;
	}
	
	public function elementShortcode($atts = null, $content = null)
	{
		extract(JSNTplMMHelperShortcode::shortcodeAtts(array('span' => 'span6', 'style' => ''), $atts));
		$span    = intval(substr($span, 4));	
		//$class   = "col-md-$span col-sm-$span col-xs-12";
		//$spanSmall	= intval($span * 3 / 2);
		$class   	= "col-md-$span col-sm-$span";
			
		$content = empty($content) ? JSNTplMMHelperShortcode::removeAutop($content) : JSNTplMMHelperShortcode::doShortcodeFrontend($content);
		$html 	= '';
		$html 	.= '<div class="jsn-tpl-mm-column-element ' . $class . '">' . $content . '</div>';
		return $html;		
	}
}