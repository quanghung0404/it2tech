EasySocial.module('site/locations/popbox', function($){

	EasySocial.module("locations/popbox", function($){

		this.resolve(function(popbox){

			var button = popbox.button,
				lat = button.data("lat"),
				lng = button.data("lng"),
				link = "//maps.google.com/?q=" + lat + "," + lng,
				language = $('meta[property="foundry:location:language"]').attr("content") || 'en',
				url = "//maps.googleapis.com/maps/api/staticmap?size=400x200&sensor=true&zoom=15&center=" + lat + "," + lng + "&markers=" + lat + "," + lng + "&language=" + language;

			return {
				id: "fd",
				component: "es",
				type: "location",
				content: '<a href="' + link + '" target="_blank"><img src="' + url + '" width="400" height="200" /></a>'
			}
		});

	});

	this.resolve();

});
