EasySocial.module("albums/editor/sortable", function($){

	var module = this;

	EasySocial.require()
		.library(
			"ui/sortable"
		)
		.done(function(){

			var Controller = 

			EasySocial.Controller("Albums.Editor.Sortable",
			{
				defaultOptions: {

				}
			},
			function(self) { return {

				init: function() {

					return;

					self.photoItemGroup()
						.sortable({
							forcePlaceholderSize: true,
							items: self.photoItem.selector,
							placeholder: 'es-photo-item placeholder',
							tolerance: 'pointer',
							delay: 150
						});
				},

				getPhotoOrdering: function() {

					var ordering = {};

					self.photoItem().each(function(i){
						var id = $(this).data("photoId");
						ordering[id] = i;
					});

					return ordering;
				},				

				"{parent.photoItemGroup} sortstart": function(el, event, ui) {

					ui.item.addClass("dragging");
					el.addClass("ordering");
					self.setLayout();
				},

				"{parent.photoItemGroup} sortchange": function(el, event, ui) {
					self.setLayout();
				},

				"{parent.photoItemGroup} sortstop": function(el, event, ui) {
					ui.item.removeClass("dragging");
					el.removeClass("ordering");
					self.setLayout();

					EasySocial.ajax(
						"site/controllers/photos/reorder",
						{
							id: ui.item.controller().id,
							order: ui.item.index()
						});					
				}
				
			}});

			module.resolve(Controller);

		});
});
