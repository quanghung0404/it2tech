<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>550</width>
	<height>500</height>
	<selectors type="json">
	{
		"{saveButton}"		: "[data-save-button]",
		"{cancelButton}"	: "[data-cancel-button]",
		"{form}"			: "[data-apps-settings-form]",
		"{id}"				: "[data-apps-settings-form-id]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function()
		{
			this.parent.close();
		},
		"{saveButton} click" : function()
		{
			var data	= this.form().serializeObject(),
				dialog 	= this.parent;

			dialog.loading( true );

			EasySocial.ajax( 'site/controllers/apps/saveSettings', { "data" : data , "id" : this.id().val() } )
			.done(function()
			{
				dialog.loading( false );

				EasySocial.dialog(
				{
					content : EasySocial.ajax( 'site/views/apps/saveSuccess' )
				});
			});
		}
	}
	</bindings>
	<title><?php echo JText::_( 'COM_EASYSOCIAL_APPS_APP_SETTINGS_DIALOG_TITLE' ); ?></title>
	<content>
		<form data-apps-settings-form class="form-horizontal">
			<?php echo $form;?>

			<input type="hidden" name="id" value="<?php echo $id;?>" data-apps-settings-form-id />
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_SAVE_BUTTON' ); ?></button>
	</buttons>
</dialog>
