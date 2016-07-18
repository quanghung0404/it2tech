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
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{form}"          : "[data-avatar-form]",
		"{file}"          : "[data-avatar-file]",
		"{filename}"      : "[data-avatar-filename]",
		"{uploadButton}"  : "[data-upload-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{uploadButton} click": function(button)
		{

			var self     = this,
				dialog   = self.parent,
				form     = self.form(),
				file     = self.file(),
				filename = self.filename()
				path     = file.val().replace(/.*(\/|\\)/, '');

			// If no file was chosen, stop.
			if (path=="") return;

			// Prevent user from clicking upload button twice
			button.disabled(true);

			// Add loading class to upload form
			// This will hide file input because in Chrome
			// cloned file inputs does not carry over filenames
			// and will show "No file chosen".
			form.addClass("loading");

			// Show a filename instead
			filename.html(path);

			var task =
				EasySocial.ajax(
					"site/controllers/photos/createAvatarFromFile",
					{
						uid 	: "<?php echo $uid;?>",
						type	: "<?php echo $type;?>",
						files	: file
					},
					{
						type: 'iframe'
					})
					.done(function(photo, avatars)
					{
						if (!photo)
						{
							return;
						}

						EasySocial.photos.createAvatar(photo.id);
					})
					.fail(function(message, type)
					{

						if (!message) {
							message = '<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_UPLOAD_ERROR_DEFAULT'); ?>';
						}

						dialog.setMessage(message, type);
					})
					.always(function(){

						// Enable upload button
						button.disabled(false);

						// Remove loading class
						form.removeClass("loading");

						// Reset filename
						filename.html("");
					});

			self.trigger("upload", [task, path]);
		},

		"{cancelButton} click": function()
		{
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_UPLOAD_AVATAR'); ?></title>
	<content>
		<div class="es-avatar-form" data-avatar-form>
			<p><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_AVATAR_INSTRUCTIONS'); ?></p>
			<input class="es-avatar-file" name="avatar_file" type="file" data-avatar-file />
			<span class="es-avatar-filename fd-loading" data-avatar-filename></span>
		</div>
	</content>
	<loading><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_UPLOADING'); ?></loading>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-upload-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_BUTTON'); ?></button>
	</buttons>
</dialog>
