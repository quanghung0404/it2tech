<?php
/**
 * @version    $Id: default_profile_picasa.php 16082 2012-09-17 03:13:08Z giangnd $
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

$externalSourceID 	= JRequest::getInt('external_source_id');
$objJSNPicasaSelect = JSNISFactory::getObj('sourcepicasa.classes.jsn_is_picasaselect', null, null, 'jsnplugin');
$params = JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_CONFIRM'));
?>
<script type="text/javascript">
var objISMaintenance = null;
require(['imageshow/joomlashine/maintenance'], function (JSNISMaintenance) {
	objISMaintenance = new JSNISMaintenance({
		language: <?php echo json_encode($params); ?>
	});
});

require(['jquery'], function ($) {
	$(function () {
		function onSubmit(ciframe, imageSourceLink)
		{
			var form 				= $('#frm-edit-source-profile');
			var params 				= {};
			params.username 		= $('input[name="picasa_username"]', form).val();
			params.profile_title	= $('input[name="external_source_profile_title"]', form).val();

			if (params.username == '' || params.profile_title == '')
			{
				alert("<?php echo JText::_('PICASA_MAINTENANCE_REQUIRED_FIELD_PROFILE_CANNOT_BE_LEFT_BLANK', true); ?>");
				return false;
			}
			else
			{
				var url  				= 'index.php?option=com_imageshow&controller=maintenance&task=checkEditProfileExist&source=picasa&external_source_profile_title=' + params.profile_title + '&external_source_id=' + <?php echo $this->sourceInfo->external_source_id; ?>;
				params.validate_url 	= 'index.php?option=com_imageshow&controller=maintenance&task=validateProfile&validate_screen=_maintenance&source=picasa&picasa_username=' + params.username;
				objISMaintenance.checkEditedProfile(url, params, ciframe, imageSourceLink);
			}
			return false;
		}

		function submitForm ()
		{
			var form = $('#frm-edit-source-profile');
				form.submit();
		}

		parent.gIframeOnSubmitFunc = onSubmit;
		gIframeSubmitFunc =submitForm;
	});
});
</script>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('PICASA_MAINTENANCE_TITLE_PROFILE_TITLE');?>
		<a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a>
	</label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PICASA_MAINTENANCE_DES_PROFILE_TITLE');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input type="text" class="jsn-master jsn-input-xxlarge-fluid"
			name="external_source_profile_title"
			id="external_source_profile_title"
			value="<?php echo @$this->sourceInfo->external_source_profile_title;?>" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('PICASA_MAINTENANCE_TITLE_PICASA_USER');?>
		<a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a>
	</label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PICASA_MAINTENANCE_DES_PICASA_USER');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input type="text"
		<?php echo ($this->countShowlist) ? 'disabled="disabled" class="jsn-readonly jsn-master jsn-input-xxlarge-fluid"' : 'class="jsn-master jsn-input-xxlarge-fluid"'; ?>
			value="<?php echo @$this->sourceInfo->picasa_username;?>"
			name="picasa_username" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('PICASA_MAINTENANCE_TITLE_THUMBNAIL_MAX_SIZE');?>
		<a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a>
	</label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PICASA_MAINTENANCE_THUMBNAIL_MAX_SIZE_DESC');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<?php
		$thumbSize = $objJSNPicasaSelect->getThumbnailSizeOptions();
		echo JHTML::_('select.genericList', $thumbSize, 'picasa_thumbnail_size', 'class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text', $this->sourceInfo->picasa_thumbnail_size);
		?>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('PICASA_MAINTENANCE_TITLE_IMAGE_MAX_SIZE');?>
		<a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a>
	</label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
			<?php echo JText::_('PICASA_MAINTENANCE_IMAGE_MAX_SIZE_DESC');?>
				<a href="javascript:void(0);"
					class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<?php
		$imageSize = $objJSNPicasaSelect->getImageSizeOptions();
		echo JHTML::_('select.genericList', $imageSize, 'picasa_image_size', 'class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text', $this->sourceInfo->picasa_image_size);
		?>
	</div>
</div>
<input
	type="hidden" name="option" value="com_imageshow" />
<input
	type="hidden" name="controller" value="maintenance" />
<input
	type="hidden" name="task" value="saveprofile" id="task" />
<input type="hidden"
	name="source" value="picasa" />
<input
	type="hidden" name="external_source_id"
	value="<?php echo $externalSourceID; ?>" id="external_source_id" />
<?php echo JHTML::_( 'form.token' ); ?>