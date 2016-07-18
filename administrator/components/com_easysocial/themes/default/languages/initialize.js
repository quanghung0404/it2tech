
EasySocial
.require()
.done(function($) {

	EasySocial.ajax( 'admin/controllers/languages/getLanguages', {
	}).done(function(result) {

        if (result.code == 404) {
            $('[data-languages-wrapper]').addClass('error-api');

            return;
        }

		window.location = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easysocial&view=languages';
	});

});