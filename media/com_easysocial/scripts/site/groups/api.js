EasySocial.module('site/groups/api', function($){

	var module = this;

	EasySocial.require()
	.library('dialog')
	.done(function() {

		// Data API
		$(document)
			.on('click.es.groups.join', '[data-es-groups-join]', function() {

				var element = $(this);
				var groupId = element.data('id');

				EasySocial.dialog({
					"content": EasySocial.ajax('site/controllers/groups/joinGroup', {"api": 1, "id": groupId}),
					"bindings": {}
				});
			});

		module.resolve();
	});
});