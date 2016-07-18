<?php
/**
 * @version    $Id: form_profile.php 16115 2012-09-18 05:21:34Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

$app 			= JFactory::getApplication();
$showlistID 	= JRequest::getInt('showlist_id', 0);
$sourceIdentify = JRequest::getVar('source_identify', '');
$sourceType		= JRequest::getVar('image_source_type', '');
$return			= JRequest::getVar('return', '', 'get');

if (base64_decode($return) != @$_SERVER['HTTP_REFERER'])
{
	$app->redirect(base64_decode($return));
	return;
}
$availableProfile = array();

if ($sourceIdentify != '')
{
	$imageSource 		= JSNISFactory::getSource($sourceIdentify, $sourceType, $showlistID);
	$availableProfile 	= $imageSource->getAvaiableProfiles();
}
$availableProfile 			= array_reverse($availableProfile);
$exsitedAvailableProfile 	= count($availableProfile);
$availableProfile[] 		= array('value' => 0,
									'text' => ' - '.JText::_('SHOWLIST_PROFILE_SELECT_PROFILE').' - ');
$availableProfile 			= array_reverse($availableProfile);

$params = JSNUtilsLanguage::getTranslated(array(
		'JSN_IMAGESHOW_SAVE',
		'JSN_IMAGESHOW_CLOSE',
		'JSN_IMAGESHOW_CONFIRM'));
?>
<script type="text/javascript">
var objISShowlist = null;
require(['imageshow/joomlashine/showlist'], function (JSNISShowlist) {
	objISShowlist = new JSNISShowlist({
		language: <?php echo json_encode($params); ?>
	});
});

(function($){
	$(document).ready(function () {
	    $("#accordion").accordion({
	        header: "h3",
	        autoHeight: false,
	        clearStyle: true,
	        changestart: function(event, ui)
	        {
	        	$('.jsn-accordion-radio',ui.oldHeader).removeAttr('checked');
				$('.jsn-accordion-radio',ui.newHeader).attr('checked','checked');
	        },
	        create: function(event, ui) {
				$('.ui-state-active', this).removeClass('ui-state-default');
		    }
	    });

	    var objISImageShow = new $.JQJSNISImageShow();
		objISImageShow.showHintText();

		function onSubmit()
		{
			if ($("#profile_type_new").attr("checked") != undefined && $("#profile_type_new").attr("checked") == 'checked')
			{

				$('#task').val('createprofile');
				var externalSourceID = parseInt($('option:selected', $('#external_source_id')).val());
				if (externalSourceID)
				{
					$('option:selected', $('#external_source_id')).val(0);
				}
				submitFormProfile();
			}

			if ($("#profile_type_available").attr("checked") != undefined && $("#profile_type_available").attr("checked") == 'checked')
			{
				$('#task').val('changeprofile');
				var externalSourceID = parseInt($('option:selected', $('#external_source_id')).val());
				if (!externalSourceID)
				{
					alert("<?php echo JText::_('SHOWLIST_PROFILE_SELECT_AVAILABLE_PROFILE', true); ?>");
					return;
				}
				objISShowlist.submitProfileForm();
			}
		}
		parent.gIframeOnSubmitFunc = onSubmit;
	})
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);

</script>
<div id="jsn-showlist-install-sources-verify"
	class="ui-dialog-contentpane">
	<div class="jsn-bootstrap">
		<form name="adminForm" id="frm-edit-source-profile" action="index.php"
			method="post" onsubmit="return false;">
			<div id="accordion">
			<?php if ($exsitedAvailableProfile) { ?>
				<h3>
					<a href="#"><input id="profile_type_available"
						class="jsn-accordion-radio" type="radio" value="available"
						checked="checked" name="profile_type"> <?php echo JText::_('SHOWLIST_PROFILE_SELECT_AVAILABLE_PROFILE')?>
					</a>
				</h3>
				<div id="jsn-showlist-available-profile">
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('SHOWLIST_PROFILE_SELECT_AVAILABLE_PROFILE')?>
						</label>
						<div class="controls">
						<?php echo JHTML::_('select.genericList', $availableProfile, 'external_source_id', 'class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text');?>
						</div>
					</div>
				</div>
				<?php } ?>
				<h3>
					<a href="#"><input id="profile_type_new"
						class="jsn-accordion-radio" type="radio" value="available"
						name="profile_type"
						<?php echo (!$exsitedAvailableProfile)?' style="display: none;" checked="checked"':'';?>>
						<?php echo JText::_('SHOWLIST_PROFILE_CREATE_NEW_PROFILE')?> </a>
				</h3>
				<div id="jsn-showlist-profile-params">
				<?php
				$this->addTemplatePath(JPATH_PLUGINS.DS.'jsnimageshow'.DS.'source'.$sourceIdentify.DS.'views'.DS.'showlist'.DS.'tmpl');
				echo $this->loadTemplate($sourceIdentify);
				?>
					<div class="content-center">
						<span class="jsn-source-icon-loading" id="jsn-create-source"></span>
					</div>
				</div>
			</div>
			<input type="hidden" id="task" name="task" value="" /> <input
				type="hidden" name="source_identify"
				value="<?php echo $sourceIdentify; ?>" /> <input type="hidden"
				name="image_source_type" value="external" /> <input type="hidden"
				name="showlist_id" value="<?php echo $showlistID; ?>" /> <input
				type="hidden" name="option" value="com_imageshow" /> <input
				type="hidden" name="controller" value="showlist" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
</div>
