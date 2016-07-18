EasySocial.require()
.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SAVE_ERROR',
		  'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_PHOTOS_MAXSIZE',
		  'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_FILES_MAXSIZE',
		  'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_VIDEOS_MAXSIZE',
		  'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_PHOTOS_UPLOADER_MAXSIZE')
.done(function($){

	$.Joomla( 'submitbutton' , function(task)
	{
		if( task == 'cancel' )
		{
			window.location	= 'index.php?option=com_easysocial&view=groups&layout=categories';

			return false;
		}

		<?php if( $category->id ) { ?>
		var performSave = function(id)
		{
			var result = [];

			// Define all custom saving process here

			// Prepare data to save fields
			result.push($('.profileFieldForm').controller().save(task));

			if(result.length > 0)
			{
				$.when.apply(null, result).done(function() {
					$.Joomla('submitform', [task]);
				});

				return;
			}

			$.Joomla('submitform', [task]);

			return;
		}

		var validateUploadSize = function() {

			var hasError = false;

			$('[data-maxupload-check]').each(function(idx, ele) {
				var maxvalue = $(this).data('maxupload');
				var key = $(this).data('maxupload-key');
				var curvalue = $(this).val();

				if (curvalue > maxvalue) {
					// console.log('invalid value for ' + label);

					hasError = true;

					var errorText = 'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_' + key;

					EasySocial.dialog({
						content: $.language(errorText)
					});
				}
			});

			if (hasError) {
				return false;
			} else {
				return true;
			}

		}

		if( task == 'applyCategory' || task == 'saveCategory' || task == 'saveCategoryNew' )
		{
			if (validateUploadSize()) {
				performSave(<?php echo $category->id; ?>);
			}

			return false;
		}

		<?php } ?>

		$.Joomla( 'submitform' , [task] );
	});

	$( '[data-category-avatar-remove-button]' ).on( 'click' , function()
	{
		var id 		= $( this ).data( 'id' ),
			button	= $( this );

		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/groups/confirmRemoveCategoryAvatar' , { "id" : id }),
			bindings 	:
			{
				"{deleteButton} click" : function()
				{
					EasySocial.ajax( 'admin/controllers/groups/removeCategoryAvatar' ,
					{
						"id" : id
					})
					.done(function()
					{
						$( '[data-category-avatar-image]' ).remove();

						button.remove();

						EasySocial.dialog().close();
					});
				}
			}
		});
	});
});
