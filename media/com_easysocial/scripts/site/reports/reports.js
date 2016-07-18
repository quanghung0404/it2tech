EasySocial.module("site/reports/reports", function($) {

	$(document).on("click.es.reports.link", "[data-reports-link]", function(){

		var button = $(this);
		var props = "url,extension,uid,type,object,title,description".split(",");
		var data = {};

		$.each(props, function(i, prop){
			data[prop] = button.data(prop);
		});
		
		EasySocial.dialog({

			content: EasySocial.ajax("site/views/reports/confirmReport", {
					title: data.title,
					description: data.description
			}),
			selectors: {
				"{message}": "[data-reports-message]",
				"{reportButton}": "[data-report-button]",
				"{cancelButton}": "[data-cancel-button]"
			},
			bindings: {

				"{reportButton} click": function() {

					var message	= this.message().val();

					EasySocial.dialog({
						content: EasySocial.ajax("site/controllers/reports/store", {
								url      : data.url,
								extension: data.extension,
								uid      : data.uid,
								type     : data.type,
								title    : data.object,
								message  : message
							})
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
