<?php
/**
 * @version    $Id: form_profile_picasa.php 16118 2012-09-18 07:42:57Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

$objJSNPicasaSelect = JSNISFactory::getObj('sourcepicasa.classes.jsn_is_picasaselect', null, null, 'jsnplugin');
?>
<script type="text/javascript">
function submitFormProfile()
{
	var form 				= jQuery('#frm-edit-source-profile');
	var params 				= {};
	params.username 		= jQuery('input[name="picasa_username"]', form).val();
	params.profile_title	= jQuery('input[name="external_source_profile_title"]', form).val();

	if (params.username == '' || params.profile_title == '')
	{
		alert("<?php echo JText::_('PICASA_MAINTENANCE_REQUIRED_FIELD_PROFILE_CANNOT_BE_LEFT_BLANK', true); ?>");
		return false;
	}
	else
	{
		var url  				= 'index.php?option=com_imageshow&controller=maintenance&task=checkEditProfileExist&source=picasa&external_source_profile_title=' + params.profile_title + '&external_source_id=0&rand=' + Math.random();
		params.validate_url 	= 'index.php?option=com_imageshow&controller=maintenance&task=validateProfile&validate_screen=showlist&source=picasa&picasa_username=' + params.username + '&rand='+ Math.random();
		objISShowlist.checkEditedProfile(url, params);
	}
	return false;
}
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
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text"
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
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" value=""
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
		<?php echo $objJSNPicasaSelect->getSelectBoxThumbnailSize(); ?>
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
		<?php echo $objJSNPicasaSelect->getSelectBoxImageSize(); ?>
	</div>
</div>
