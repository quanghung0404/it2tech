EasySocial.module("site/blocks/blocks", function($) {

	$(document).on("click.es.blocks.link", "[data-blocks-link]", function(){

		var target = $(this).data('target');

		EasySocial.dialog({

			content: EasySocial.ajax(
				"site/views/blocks/confirmBlock",
				{
					"target": target
				}),

				selectors: {
					"{reason}": "[data-block-reason]",
					"{blockButton}": "[data-block-button]",
					"{cancelButton}": "[data-cancel-button]"
				},

				bindings: {

					"{blockButton} click": function() {

						var reason = this.reason().val();

						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/controllers/blocks/store",
								{
									"target": target,
									"reason": reason
								})
							});
					},

					"{cancelButton} click": function() {
						EasySocial.dialog().close();
					}
				}
		});
	});


	$(document).on("click.es.unblock.link", "[data-unblock-link]", function(){

		var target = $(this).data('target');

		EasySocial.dialog({

			content: EasySocial.ajax(
				"site/views/blocks/confirmUnblock",
				{
					"target": target
				}),

				selectors: {
					"{unblockButton}": "[data-unblock-button]",
					"{cancelButton}": "[data-cancel-button]"
				},

				bindings: {

					"{unblockButton} click": function() {

						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/controllers/blocks/unblock",
								{
									"target": target
								})
								.done(function() {

									// remove the user from the listing page.
									$('[data-blocked-user-' + target + ']').remove();

								}),

							selectors: {
								"{cancelButton}": "[data-cancel-button]"
							},

							bindings: {
								"{cancelButton} click": function() {
									EasySocial.dialog().close();
								}
							}
						});
					},

					"{cancelButton} click": function() {
						EasySocial.dialog().close();
					}
				}
		});
	});

	this.resolve();

});
