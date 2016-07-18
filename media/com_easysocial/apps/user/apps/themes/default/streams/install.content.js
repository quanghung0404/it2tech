EasySocial.ready(function($) {

	$('[data-install-app-<?php echo $uid; ?>]').on('click', function() {
		var installButton = $(this),
			id = installButton.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/apps/getTnc'),
			bindings: {
				'{cancelButton} click': function()
				{

					EasySocial.dialog().close();
				},

				'{installButton} click': function()
				{
					var agreed 			= this.agreeCheckbox().is(':checked'),
						requireTerms	= <?php echo $this->config->get( 'apps.tnc.required' ) ? 'true' : 'false';?>;

					if( !agreed && requireTerms )
					{
						this.termsError().show();
						return;
					}

					var installing = EasySocial.ajax('site/controllers/apps/installApp', {
						id: id
					});

					EasySocial.dialog({
						content: installing,
						bindings:
						{
							"{closeButton} click" : function(){
								EasySocial.dialog().close();
							}
						}
					});

					installing.done(function() {
						installButton.hide();

						setTimeout(function() {
							EasySocial.dialog().close();
						}, 2000);
					});
				}
			}
		});
	});
});
