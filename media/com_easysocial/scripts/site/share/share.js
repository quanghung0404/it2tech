EasySocial.module("site/share/share", function($){

	$(document)
		.on("click.es.share.button", "[data-es-share-button]", function(){

			var button = $(this);

			EasySocial.dialog({
				title: button.text(),
				width:500,
				content:
					EasySocial.ajax(
						"site/views/sharing/shareDialog",
						{
							url: button.data("url"),
							title: button.data("title")
						})
			});
		});

	this.resolve();
});
