<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$results	= array();
$results[] 	= JHTML::_('select.option', '0', '- '.JText::_('JSN_EASYSLIDER_PLG_EDITOR_FIELD_SELECT_SLIDER').' -', 'id', 'text');
$results 	= array_merge( $results, $this->objJSNEasySliderSliders->getSliders());
//$objUtils	= new JSNEasySliderUtils();
?>

<div class="jsn-easyslider-plg-editor-container jsn-bootstrap">
	<div class="jsn-easyslider-plg-editor-wrapper">
		<h3 class="jsn-section-header">
			<?php echo JText::_('JSN_EASYSLIDER_PLG_EDITOR_SLIDER_SETTINGS');?>
		</h3>
		<div class="setting">
			<ul>
				<li>
					<label><?php echo JText::_('JSN_EASYSLIDER_PLG_EDITOR_SLIDER_SLIDER');?></label>
					<div class="jsn-easyslider-plg-editor-setting-wrapper">
						<?php echo JHTML::_('select.genericList', $results, 'slider_id', 'class="span4 jsn-select-value" id="slider_id"', 'id', 'text', 0);?>
						<div class="jsn-easyslider-plg-editor-icon-wrapper">
							<a class="jsn-link-edit-slider disabled" id="jsn-link-edit-slider" href="javascript: void(0);" title=""><i class="icon-pencil" id="slider-icon-edit"></i></a>
							<?php
							if (strtolower($this->edition) == 'free' && $this->totalSliders !== false && $this->totalSliders >= 3)
							{
								?>
								<a href="javascript: void(0);" onclick="alert('<?php echo JText::_('JSN_EASYSLIDER_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SLIDERS_IN_FREE_EDITION', true); ?>')" title=""><i class="icon-plus" id="slider-icon-add"></i></a>
								<?php
							}
							else
							{
								?>
								<a href="index.php?option=com_easyslider&view=slider&layout=edit" target="_blank" title=""><i class="icon-plus" id="slider-icon-add"></i></a>
								<?php
							}
							?>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="insert">
			<div class="form-actions">
				<button disabled="disabled" id="btn_insert_button" onclick="window.parent.jsnSelectSlider(jQuery('#slider_id'), '<?php echo 'FOO';?>')" name="button_installation_data" type="button" class="btn">

					<?php echo JText::_('JSN_EASYSLIDER_PLG_EDITOR_INSERT');?>
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function($){
		$(document).ready(function ()
		{
			var JSNESSliders 	= new $.JSNESSliders();
			JSNESSliders.initialize();
		});
	})(jQuery);
</script>