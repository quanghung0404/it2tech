EasySocial.require()
.script('admin/profiles/form')
.done(function($){

	$('[data-profile-form]').addController('EasySocial.Controller.Profiles.Profile', {
		id: <?php echo !empty( $profile->id ) ? $profile->id : 0; ?>
	});

	// Add active tab state
	$('[data-form-tabs]').on('click', function() {
		var input = $('[data-tab-active]');

		if (input) {
			var selected = $(this).data('item');

			input.val(selected);
		}
	});

	$.Joomla('submitbutton', function(task) {

		<?php if ($profile->id) { ?>
		var performSave = function(id) {
			var result = [];

			// Define all custom saving process here

			// Prepare data to save fields
			result.push($('.profileFieldForm').controller().save(task));

			if (result.length > 0) {
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

					hasError = true;

					EasySocial.dialog({
						content: EasySocial.ajax('admin/views/profiles/getAclErrorDialog', {"key": key})
					});
				}
			});

			if (hasError) {
				return false;
			}

			return true;
		}

		if (task == 'save' || task == 'savenew' || task == 'apply') {
			if (validateUploadSize()) {
				performSave(<?php echo $profile->id; ?>);
			}

			return false;
		}

		if (task == 'savecopy') {
			// Make ajax call to create copy of profile
			EasySocial.ajax('admin/controllers/profiles/createBlankProfile')
				.done(function(id) {

					// lets update the form element cid value.
					var input = $('input[name="cid"]');
					input.attr( 'value', id );
					performSave(id);
				});

			return false;
		}
		<?php } ?>

		if (task == 'cancel') {
			window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&view=profiles';
			return;
		}

		$.Joomla('submitform', [task]);
	});
});
