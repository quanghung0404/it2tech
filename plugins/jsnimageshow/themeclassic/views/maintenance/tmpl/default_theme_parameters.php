<?php
/**
 * @version    $Id: default_theme_parameters.php 16393 2012-09-25 08:26:45Z giangnd $
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

JHTML::stylesheet('plugins/jsnimageshow/themeclassic/assets/css/style.css');
$objJSNShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objLang 				= JFactory::getLanguage();
$objLang->load(strtolower('plg_jsnimageshow_themeclassic'));
$objJSNShowcaseTheme->importModelByThemeName('themeclassic');
$model = JModelLegacy::getInstance('themeclassic');
$data = $model->getParameters();

$lists 	 = array();
$rootURL = array(
		'0' => array('value' => '1',
		'text' => JText::_('PARAMETER_GLOBAL_URL_BASE_REQUEST')),
		'1' => array('value' => '2',
		'text' => JText::_('PARAMETER_GLOBAL_JURI_BASE')));

$outputMethod = array(
		'0' => array('value' => '0',
		'text' => JText::_('PARAMETER_GLOBAL_BY_TAGS_OBJECT_AND_EMBED')),
		'1' => array('value' => '1',
		'text' => JText::_('PARAMETER_GLOBAL_BY_SWFOBJECT_SCRIPT')));
$lists['rootURL'] 				= JHTML::_('select.genericList', $rootURL, 'root_url', 'class="jsn-master jsn-input-xxlarge-fluid"'. '', 'value', 'text', (@$data->root_url == '')?'1':@$data->root_url);
$lists['generalSwfLibrary'] 	= JHTML::_('select.genericList', $outputMethod ,'general_swf_library','class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text', @$data->general_swf_library);
?>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('PARAMETER_GLOBAL_HTML_OUTPUT_METHOD');?>
		<a class="hint-icon link-action" href="javascript:void(0);">(?)</a> </label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PARAMETER_GLOBAL_DESC_HTML_OUTPUT_METHOD');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close link-action">[x]</a>
			</div>
		</div>
		<?php echo $lists['generalSwfLibrary']; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('PARAMETER_GLOBAL_ROOT_URL_GENERATION_MODE');?>
		<a class="hint-icon link-action" href="javascript:void(0);">(?)</a> </label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PARAMETER_GLOBAL_DES_ROOT_URL_GENERATION_MODE');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close link-action">[x]</a>
			</div>
		</div>
		<?php echo $lists['rootURL']; ?>
	</div>
</div>
<input
	type="hidden" name="option" value="com_imageshow" />
<input
	type="hidden" name="controller" value="maintenance" />
<input
	type="hidden" name="task" value="savethemeparameter" id="task" />
<input
	type="hidden" name="theme_name" value="themeclassic" />
<input
	type="hidden" name="theme_table" value="parameter" />
<input
	type="hidden" name="id"
	value="<?php echo (@$data->id == '')?'0':@$data->id; ?>" />
		<?php echo JHTML::_('form.token'); ?>