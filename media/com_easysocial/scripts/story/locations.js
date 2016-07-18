EasySocial.module("story/locations", function($){

	var module = this;

	EasySocial.require()
	.library(
		"gmaps",
		"scrollTo",
		"image"
	)
	.view("site/location/story.suggestion")
	.language(
		"COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR",
		"COM_EASYSOCIAL_LOCATION_TIMEOUT_ERROR",
		"COM_EASYSOCIAL_LOCATION_UNAVAILABLE_ERROR",
		"COM_EASYSOCIAL_STREAM_AT"
	)
	.done(function() {

		// Constants
		var KEYCODE = {
			BACKSPACE: 8,
			COMMA: 188,
			DELETE: 46,
			DOWN: 40,
			ENTER: 13,
			ESCAPE: 27,
			LEFT: 37,
			RIGHT: 39,
			SPACE: 32,
			TAB: 9,
			UP: 38
		};

		EasySocial.Controller("Story.Locations",
		{
			defaultOptions: {

				view: {
					suggestion: "site/location/story.suggestion"
				},

				map: {
					lat: 0,
					lng: 0
				},

				"{base}": "[data-story-location]",
				"{form}": "[data-story-location-form]",

				"{textField}"   	: "[data-story-location-textField]",
				"{detectButton}"	: "[data-story-location-detect-button]",

				"{autocomplete}"	: "[data-story-location-autocomplete]",
				"{suggestions}"		: "[data-story-location-suggestions]",
				"{suggestion}"		: "[data-story-location-suggestion]",

				"{textbox}"			: "[data-story-location-textbox]",
				"{removeButton}"	: "[data-story-location-remove-button]",

				"{map}"     : "[data-story-location-map]",
				"{mapImage}": "[data-story-location-map-image]",

				"{meta}": "[data-story-meta-location]",
			}
		},
		function(self, opts) { return {

			init: function() {

				// Only show auto-detect button if the browser supports geolocation
				if (navigator.geolocation) {
					self.base().addClass("is-detectable");
					// self.detectButton().show();
				}

				// Add placeholder support for IE9
				self.textField().placeholder();

				// Allow textfield input only when controller is implemented
				self.textField().removeAttr("disabled");
			},

			"{window} resize": $.debounce(function() {

				var currentLocation = self.currentLocation;

				if (!currentLocation) return;

				var mapImage = self.mapImage();

				if (mapImage.data("width") !== mapImage.width()) {

					var coords = currentLocation.geometry.location,
						lat = coords.lat(),
						lng = coords.lng();

					self.navigate(lat, lng);
				}

			}, 250),

			navigate: function(lat, lng) {

				var mapImage = self.mapImage(),
					width    = mapImage.width(),
					height   = mapImage.height(),
					url =
						$.GMaps.staticMapURL({
							size: [width, height],
							lat: lat,
							lng: lng,
							sensor: true,
							scale: 2,
							markers: [
								{lat: lat, lng: lng}
							]
						});

				$.Image.get(url)
					.done(function() {
						mapImage.css({
							"backgroundImage": $.cssUrl(url),
							"backgroundSize": "cover",
							"backgroundPosition": "center center"
						});

						self.base().addClass("has-location");
					});
			},

			// Memoized locations
			locations: {},

			lastQueryAddress: null,

			"{textField} keypress": function(textField, event) {

				switch (event.keyCode)
				{
					case KEYCODE.UP:

						var prevSuggestion = $(
							self.suggestion(".active").prev(self.suggestion.selector)[0] ||
							self.suggestion(":last")[0]
						);

						// Remove all active class
						self.suggestion().removeClass("active");

						prevSuggestion
							.addClass("active")
							.trigger("activate");

						self.suggestions()
							.scrollTo(prevSuggestion, {
								offset: prevSuggestion.height() * -1
							});

						event.preventDefault();

						break;

					case KEYCODE.DOWN:

						var nextSuggestion = $(
							self.suggestion(".active").next(self.suggestion.selector)[0] ||
							self.suggestion(":first")[0]
						);

						// Remove all active class
						self.suggestion().removeClass("active");

						nextSuggestion
							.addClass("active")
							.trigger("activate");

						self.suggestions()
							.scrollTo(nextSuggestion, {
								offset: nextSuggestion.height() * -1
							});

						event.preventDefault();

						break;

					case KEYCODE.ENTER:

						var activeSuggestion = self.suggestion(".active"),
							location = activeSuggestion.data("location");
							self.set(location);

						self.hideSuggestions();
						break;

					case KEYCODE.ESCAPE:
						self.hideSuggestions();
						break;
				}

			},

			"{textField} keyup": function(textField, event) {

				switch (event.keyCode) {

					case KEYCODE.UP:
					case KEYCODE.DOWN:
					case KEYCODE.LEFT:
					case KEYCODE.RIGHT:
					case KEYCODE.ENTER:
					case KEYCODE.ESCAPE:
						// Don't repopulate if these keys were pressed.
						break;

					default:
						var address = $.trim(textField.val());

						if (address==="") {
							self.base().removeClass("has-location");
							self.hideSuggestions();
						}

						// if (address==self.lastQueryAddress) return;

						var locations = self.locations[address];

						// If this location has been searched before
						if (locations) {

							// And set our last queried address to this address
							// so that it won't repopulate the suggestion again.
							self.lastQueryAddress = address;

							// Just use cached results
							self.suggest(locations);

						// Else ask google to find it out for us
						} else {

							self.lookup(address);
						}
						break;
				}
			},

			lookup: $.debounce(function(address) {

				self.base().addClass("is-busy");

				EasySocial.ajax('site/controllers/location/getLocations', {
					query: address
				}).done(function(locations) {
					self.base().removeClass("is-busy");

					// Store a copy of the results
					self.locations[address] = locations;

					// Suggestion locations
					self.suggest(locations);

					self.lastQueryAddress = address;
				}).fail(function(msg) {
					console.log(msg);
				});
			}, 250),

			suggest: function(locations) {

				var suggestions = self.suggestions();

				// Clear location suggestions
				suggestions
					.empty();

				if (locations.length < 0) return;

				$.each(locations, function(i, location) {

					// Create suggestion and append to list
					self.view.suggestion({
							location: location
						})
						.data("location", location)
						.appendTo(suggestions);
				});

				self.showSuggestions();
			},

			showSuggestions: function() {

				self.focusSuggestion = true;

				self.element.find(".es-story-footer")
					.addClass("swap-zindex");

				self.story.clearMessage();

				setTimeout(function(){

					self.autocomplete().addClass("active");

					var doc = $(document),
						hideOnClick = "click.es.story.location";

					doc
						.off(hideOnClick)
						.on(hideOnClick, function(event){

							// Collect list of bubbled elements
							var targets = $(event.target).parents().andSelf();

							if (targets.filter(self.element).length > 0) return;

							doc.off(hideOnClick);

							self.hideSuggestions();
						});

				}, 500);
			},

			hideSuggestions: function() {

				self.focusSuggestion = false;

				self.autocomplete().removeClass("active");

				$(document).off("click.es.story.location");

				setTimeout(function(){

					if (self.focusSuggestion) return;

					self.element.find(".es-story-footer")
						.removeClass("swap-zindex");

				}, 500);
			},

			"{suggestion} activate": function(suggestion, event) {

				var location = suggestion.data("location");

				self.navigate(
					location.latitude,
					location.longitude
				);
			},

			"{suggestion} mouseover": function(suggestion) {

				// Remove all active class
				self.suggestion().removeClass("active");

				suggestion
					.addClass("active")
					.trigger("activate");
			},

			"{suggestion} click": function(suggestion, event) {

				var location = suggestion.data("location");

				self.set(location);

				self.hideSuggestions();
			},

			set: function(location) {

				self.currentLocation = location;

				var process = $.Deferred();

				if ($.isEmpty(location.fulladdress)) {
					self.getAddress(location.latitude, location.longitude)
						.done(function(address) {
							location.fulladdress = location.name + ', ' + address;

							process.resolve(location);
						});
				} else {
					process.resolve(location);
				}

				process.done(function(location) {
					self.navigate(location.latitude, location.longitude);

					self.textField().val(location.fulladdress);

					self.lastQueryAddress = location.address;

					var link =
							$('<a href="javascript: void(0);" data-story-meta-location>')
								.html(location.fulladdress)
								.toHTML(),

						caption = $.language("COM_EASYSOCIAL_STREAM_AT") + " " + link;

					self.story.setMeta("location", caption);

					self.base().addClass("has-location");
				});
			},

			unset: function() {

				self.currentLocation = null;

				self.textField().val('');

				self.story.removePanelCaption("locations");

				self.mapImage().attr("src", "");

				self.story.setMeta("location", "");

				self.base().removeClass("has-location");
			},

			activatePanel: function() {

				setTimeout(function(){
					self.textField().focus();
				}, 500);
			},

			deactivatePanel: function() {

				var location = self.currentLocation;

				if (location) {
					self.set(location);
				}
			},

			detectTimer: null,

			"{detectButton} click": function() {

				var story = self.story,
					textbox = self.textbox();

				self.base().addClass("is-busy");

				clearTimeout(self.detectTimer);

				self.detectTimer = setTimeout(function(){
					story.clearMessage();
					story.setMessage($.language("COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR"));
					self.base().removeClass("is-busy");
				}, 8000);



				$.GMaps.geolocate({
					success: function(position) {

						// story.clearMessage();

						EasySocial.ajax('site/controllers/location/getLocations', {
							latitude: position.coords.latitude,
							longitude: position.coords.longitude
						}).done(function(locations) {
							self.suggest(locations);
							self.textField().focus();
						}).fail(function(msg) {
							console.log(msg);
						});
					},
					error: function(error) {

						story.clearMessage();

						var message = "";

						switch (error.code) {

							case 1:
								message = $.language("COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR");
								break;

							case 2:
								message = $.language("COM_EASYSOCIAL_LOCATION_TIMEOUT_ERROR");
								break;

							case 3:
							default:
								message = $.language("COM_EASYSOCIAL_LOCATION_UNAVAILABLE_ERROR");
								break;
						}

						story.setMessage(message);
					},
					always: function() {
						clearTimeout(self.detectTimer);
						self.base().removeClass("is-busy");
					}
				});
			},

			"{removeButton} click": function() {
				self.unset();
				self.hideSuggestions();
			},

			"{meta} click": function() {
				self.story.activateMeta("location");
			},

			"{story} activateMeta": function(el, event, meta) {

				if (meta.name==="location") {
					setTimeout(function(){
						self.textField().focus();
					}, 1);
				}
			},

			"{story} save": function(event, element, save) {

				var currentLocation = self.currentLocation;

				if (!currentLocation) return;

				var task = save.addTask('saveLocation');

				if ($.isEmpty(currentLocation.fulladdress)) {
					self.getAddress(currentLocation.latitude, currentLocation.longitude).done(function(address) {
						currentLocation.fulladdress = currentLocation.name + ', ' + address;

						self.save(task, currentLocation);
					});
				} else {
					self.save(task, currentLocation);
				}
			},

			getAddress: $.memoize(function(latitude, longitude) {
				var process = $.Deferred(),
					geocoder = new google.maps.Geocoder(),
					latlng = new google.maps.LatLng(latitude, longitude);

				geocoder.geocode({
					'latLng': latlng
				},
				function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						process.resolve(results[0].formatted_address);
					}
				});

				return process;
			}, function(lat, lng) {
				return lat + ',' + lng;
			}),

			save: function(task, location) {
				task.save.data['locations_short_address'] = location.name;
				task.save.data['locations_lat'] = location.latitude;
				task.save.data['locations_lng'] = location.longitude;
				task.save.data['locations_formatted_address'] = location.fulladdress;
				task.save.data['locations_data'] = JSON.stringify(location);

				task.resolve();
			},

			"{story} clear": function() {

				self.unset();
				self.hideSuggestions();
			}
		}});

		// Resolve module
		module.resolve();

	});

});
