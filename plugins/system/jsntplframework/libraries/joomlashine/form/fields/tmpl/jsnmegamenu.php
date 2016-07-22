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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


if (class_exists('JModelLegacy'))
{
	$templateModel = JModelLegacy::getInstance('Style', 'TemplatesModel');
}
else
{
	$templateModel = JModel::getInstance('Style', 'TemplatesModel');
}

$template 			= $templateModel->getItem(JFactory::getApplication()->input->getInt('id'));
$templateEdition 	= JSNTplHelper::getTemplateEdition($template->template);
$templateName 		= JText::_($template->template);

$megaMenus     = JSNTplMMHelperMegamenu::getMegamenuItemsByStyleId($template->id);
$language_code = '';
if (count($megaMenus))
{
	$language_code = $megaMenus->language_code;
}
?>
<div id="jsn-megamenu-builder" class="jsn-padding-mini jsn-rounded-mini jsn-box-shadow-mini">
	<!-- TOP LEVEL MENU ITEMS PANEL -->
	<div id="jsn-tpl-mm-top-level-menu-container"></div>
	
	<!-- MEGAMENU SETTING PANEL -->
	<div class="megamenu-builder-container">
		<div id="megamenu-setting-container">
			<!-- MegaMenu elements -->
			<div id="jsn-mm-form-design-content">
				<div class="jsn-mm-form-container jsn-layout">
					<a href="javascript:void(0);" id="jsn-mm-add-container" class="jsn-add-more"><i class="icon-add"></i><?php echo JText::_('JSN_TPLFW_MEGAMENU_ADD_ROW', true);?></a>
						<div class="row-fluid jsn-mm-layout-thumbs">
							   <?php
								$layouts = $this->layouts;
								foreach ($layouts as $columns) 
								{
									$columns_name = implode('x', $columns);
									$icon_class   = implode('-', $columns);
									$icon_class   = 'jsn-mm-layout-' . $icon_class;
									$icon         = "<i class='{$icon_class}'></i>";
									printf('<div class="thumb-wrapper col-md-1 col-xs-2" data-columns="%s" title="%s">%s</div>', implode(',', $columns), $columns_name, $icon);
								
								}
							?>
						</div>					
				</div>
				
			</div>
			<div class="clearfix"></div>
			<?php 
				$elements 			= $this->getElements();
				$megaMenuShortcodes = JSNTplMMHelperShortcode::getshortcodeTags();
				$elementsHtml 		= array();
				
				$categories = array("All");
				foreach ($elements['element'] as $element ) 
				{
					// don't show sub-shortcode
					if (!isset( $element->config['name'])) 
					{
						continue;
					}
				
					// get shortcode category
					$category = ''; // category name of this shortcode
					if ( ! empty($megaMenuShortcodes[$element->config['shortcode']])) 
					{
						$categoryName = $megaMenuShortcodes[$element->config['shortcode']]['provider']['name'] || '';
						$category      = strtolower(str_replace(' ', '', $categoryName));
						if (!array_key_exists($category, $categories)) 
						{
							$categories[$category] = $categoryName;
						}
					}
				
					$elementsHtml[] = $element->elementButton($category);
				}
			?>
			<div id="jsn-mm-add-element" class="jsn-mm-add-element add-field-dialog" style="display: none; background-color:#FFFFFF;">
				<div class="jsn-elementselector">
				<!-- Elements -->
				<ul class="jsn-items-list">
					<?php
						// shortcode elements
						foreach ($elementsHtml as $idx => $element) 
						{
							echo $element;
						}
					?>
				</ul>					
				</div>
			</div>
			<input type="hidden" name="jsn_tpl_mm_menu_options" id="jsn_tpl_mm_menu_options" value=""/>
			<input type="hidden" name="jsn_tpl_mm_selected_menu_id" id="jsn_tpl_mm_selected_menu_id" value=""/>
			<input type="hidden" name="jsn_tpl_mm_selected_menu_type" id="jsn_tpl_mm_selected_menu_type" value=""/>	
		</div>
		<div class="jsn-mm-form-msg-no-menu-item" id="jsn-mm-form-msg-no-menu-item"><?php echo JText::_('JSN_TPLFW_MEGAMENU_NO_MENU_ITEMS', true);?></div>
	</div>
</div>

<script type="text/javascript">
var JSNTPLMegamenuLangs = new Array();
JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_ROW'] = '<?php echo JText::_('JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_ROW', true)?>';
JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_COLUMN'] = '<?php echo JText::_('JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_COLUMN', true)?>';
JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_ELEMENT'] = '<?php echo JText::_('JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_ELEMENT', true)?>';
JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ELEMENT_TITLE_CANNOT_BE_BLANK'] = '<?php echo JText::_('JSN_TPLFW_MEGAMENU_ELEMENT_TITLE_CANNOT_BE_BLANK', true)?>';
JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_LANGUAGE_OR_MENU_CANNOT_BLANK'] = '<?php echo JText::_('JSN_TPLFW_MEGAMENU_LANGUAGE_OR_MENU_CANNOT_BLANK', true)?>';
					
	(function($) {
		$(document).ready(function() {
			new $.JSNTplMegaMenu({
				template: '<?php echo $template->template;?>',
				templateName: '<?php echo $templateName;?>',
				edition: '<?php echo $templateEdition;?>',
				styleId : '<?php echo $template->id;?>',
				token: '<?php echo JSession::getFormToken(); ?>',
				languageCode : '<?php echo $language_code;?>'
			});
		});
	})(jQuery);
</script>