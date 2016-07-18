<?php
/**
 * @version    $Id: default_profile_parameters.php 16082 2012-09-17 03:13:08Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$objJSNPicasa 		= JSNISFactory::getObj('sourcepicasa.classes.jsn_is_picasa', null, null, 'jsnplugin');
$picasaParamsStr	= $objJSNPicasa->getSourceParameters();
$picasaParams		= json_decode($picasaParamsStr);
?>
<div class="control-group">
	<label class="control-label"> <?php echo JText::_('MAINTENANCE_SOURCE_PARAMETER_NUMBER_OF_IMAGES_ON_LOADING');?>
		<a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a>
	</label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('MAINTENANCE_SOURCE_DESC_NUMBER_OF_IMAGES_ON_LOADING');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text"
			name="number_of_images_on_loading" id="number_of_images_on_loading"
			value="<?php echo (isset($picasaParams->number_of_images_on_loading) && $picasaParams->number_of_images_on_loading !='')? $picasaParams->number_of_images_on_loading: '50';?>" />
	</div>
</div>

<input
	type="hidden" name="option" value="com_imageshow" />
<input
	type="hidden" name="controller" value="maintenance" />
<input
	type="hidden" name="task" value="saveProfileParameter" id="task" />
<input
	type="hidden" name="image_source" value="sourcepicasa" />
<input
	type="hidden" name="profile_parameter"
	value="<?php echo htmlspecialchars ($picasaParamsStr);?>" />
			<?php echo JHTML::_('form.token'); ?>