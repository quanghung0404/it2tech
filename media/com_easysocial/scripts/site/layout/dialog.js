EasySocial.module( 'site/layout/dialog' , function($){

	var module = this;

	// Dialog
	EasySocial.require()
		.library('dialog')
		.view('site/dialog/default')
		.done(function($){

			EasySocial.dialog = function(options) {

				// TODO: Isolate this from global dialog
				if (window.parentDialog) {
					return window.parentDialog.update(options);
				}

				// Normalize arguments
				if (typeof options === "string" || $.isDeferred(options)) {
					var afterShow = arguments[1];
					options = {
						content: options,
						afterShow: ($.isFunction(afterShow)) ? afterShow : $.noop
					}
				}

				var dialogElement = $('[id=fd].es-dialog.global');

				if (dialogElement.length < 1) {

					dialogElement =
						$(EasySocial.View('site/dialog/default'))
							.addClass('global')
							.appendTo('body');
				};

				var defaultOptions = {
						showOverlay: false
					},
					options = $.extend(defaultOptions, options);

				var dialogController = dialogElement.controller("Dialog");

				if (!dialogController) {
					dialogController = dialogElement.addController("Dialog", options);
				} else {
					dialogController.update(options);
				}

				return dialogController;
			}

			module.resolve();
		});
});
