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

$submodal = ! empty($submodal) ? 'submodal_frame' : '';
if (! empty($shortcode)) 
{
?>
	<div id="jsn-mm-element-<?php echo JSNTplMMHelperShortcode::shortcodeName($shortcode); ?>">
		<div class="jsn-mm-form-container jsn-bootstrap">
			<div id="modalOptions" class="form-horizontal <?php echo $submodal; ?>">
				<?php 
				
				if (!empty($params)) 
				{
					$params = stripslashes($params);
					$params = urldecode($params);
				}
				
				if ($el_type == 'element') 
				{
					// get shortcode class
					$class = JSNTplMMHelperShortcode::getShortcodeClass($shortcode);

					if (class_exists($class) && $this->parent != null) 
					{
						$elements = $objJSNTplMMElement->getElements();
						$instance = isset($elements['element'][strtolower($class)]) ? $elements['element'][strtolower($class)] : null;
						
						if (!is_object($instance)) 
						{
							$instance = new $class();
						}
						
						if (!empty($params))
						{
							$extractParams = JSNTplMMHelperShortcode::extractParams($params, $shortcode);
							
							// if have sub-shortcode, extract sub shortcodes content
							if (!empty($instance->config['has_subshortcode']))
							{
								$subScData                         = JSNTplMMHelperShortcode::extractSubShortcode($params, true);								
								$extractParams['sub_items_content'] = true;
							}
							
							JSNTplMMHelperShortcode::generateShortcodeParams($instance->items, null, $extractParams, true);
							
							// if have sub-shortcode, re-generate shortcode structure
							if (!empty( $instance->config['has_subshortcode'])) 
							{
								$instance->shortcodeData();
							}
						}
						
						$settings 		= $instance->items;
						
						$settingsHtml 	= '';
						
						if ($shortcode == 'jsn_tpl_mm_row') 
						{
							$settingsHtml .= '<div class="col-sm-12 jsn-mm-row-setting">' . JSNTplMMHelperModal::getShortcodeModalSettings($settings, $shortcode, $extractParams, $params) . '</div>';
						}
						else 
						{
							$settingsHtml .= '<div class="jsn-tpl-mm-setting-resize">' . JSNTplMMHelperModal::getShortcodeModalSettings( $settings, $shortcode, $extractParams, $params ) . '</div>';
							//$settingsHtml .= '<div class="wr-preview-resize">' . WR_Megamenu_Helpers_Shortcode::render_parameter( 'preview' ) . '</div>';
						}
					}
					
					echo $settingsHtml;
				?>
				<form id="frm_shortcode_settings" action="" method="post">
					<?php 
					foreach ($post as $k => $v) 
					{
						echo '<input type="hidden" id="hid-' . $k . '" name="' . $k . '" value="' . urlencode($v) . '" />';
					}
					echo '<input type="hidden" id="hid-init_tab" name="init_tab" value="appearance" />';
					?>
				</form>			
				<?php 
				}
				?>
				<div id="modalAction" class="jsn-tpl-mm-setting-tab"></div>
				<textarea class="hidden" id="shortcode_content"><?php echo $params; ?></textarea>
				<textarea class="hidden" id="jsn_mm_share_data"></textarea>
				<textarea class="hidden" id="jsn_mm_merge_data"></textarea>
				<textarea class="hidden" id="jsn_mm_extract_data"></textarea>
				<input id="shortcode_type" type="hidden" value="<?php echo $el_type; ?>" />
				<input id="shortcode_name" type="hidden" value="<?php echo addslashes($shortcode); ?>" />	
				<div class="jsn-modal-overlay"></div>
				<div class="jsn-modal-indicator"></div>			
			</div>
		</div>
	</div>
	<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			if ($.JSNMMHandleSetting && $.JSNMMHandleSetting.init) $.JSNMMHandleSetting.init();
		});
	})(jQuery);
	</script>
	<?php 
	if (is_object($instance))
	{
		$instance->backendElementAssets();
	}	
	?>
<?php	
}
?>