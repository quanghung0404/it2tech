EasySocial.module("locations", function($){

	var module = this;

	EasySocial
		.require()
		.library("scrollTo", "image", "gmaps")
		.view("site/location/story.suggestion")
		.language("COM_EASYSOCIAL_AT_LOCATION")
		.done(function(){

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

			EasySocial.Controller("Locations", {
				defaultOptions: {

					view: {
						suggestion: "site/location/story.suggestion"
					},

					map: {
						lat: 0,
						lng: 0
					},

					latitude: null,
					longitude: null,

					"{textField}": "[data-location-textField]",
					"{detectLocationButton}": "[data-detect-location-button]",

					'{autocomplete}'    : '[data-location-autocomplete]',
					"{suggestions}": "[data-location-suggestions]",
					"{suggestion}": "[data-story-location-suggestion]",

					"{mapImage}": "[data-location-map-image]",

					"{latitude}" : "[data-location-lat]",
					"{longitude}": "[data-location-lng]",

					"{removeButton}": "[data-location-remove-button]"
				}
			}, function(self, opts, base) { return {

				init: function() {

					// I have access to:
					// self.panelButton()
					// self.panelContent()

					// Only show auto-detect button if the browser supports geolocation
					if (navigator.geolocation) {
						self.detectLocationButton().show();
					}

					// Allow textfield input only when controller is implemented
					EasySocial.require().library("gmaps")
						.done(function(){
							self.textField().removeAttr("disabled");
						});


					// If caller specified a latitude or longitude, navigate the map
				    setTimeout(function() {
				        if (opts.latitude && opts.longitude) {
				            self.navigate(opts.latitude, opts.longitude);

				            self.detectLocationButton().hide();
				        }
				    }, 1);
				},

				navigate: function(lat, lng) {

					var mapImage = self.mapImage();
					var width = mapImage.width();
					var height = mapImage.height();

					var	url =
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

							base.addClass("has-location");
						});
				},

				// Memoized locations
				locations: {},

				lastQueryAddress: null,

				"{textField} keypress": function(textField, event) {

					switch (event.keyCode) {

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

							// self.suggestions().hide();
							break;

						case KEYCODE.ESCAPE:
							break;
					}

				},

				"{textField} keyup": function(textField, event) {

					switch (event.keyCode) {

						case KEYCODE.UP:
						case KEYCODE.DOWN:
						case KEYCODE.ENTER:
						case KEYCODE.ESCAPE:
							// Don't repopulate if these keys were pressed.
							break;

						default:
							var address = $.trim(textField.val());

							if (address==="") {
								// self.suggestions().hide();

							}

							if (address==self.lastQueryAddress) {
								return;
							}

							var locations = self.locations[address];

							// If this location has been searched before
							if (locations) {

								// Just use cached results
								self.suggest(locations);

								// And set our last queried address to this address
								// so that it won't repopulate the suggestion again.
								self.lastQueryAddress = address;

							// Else ask google to find it out for us
							} else {

								self.lookup(address);
							}
							break;
					}
				},

				lookup: $._.debounce(function(address){

					// TODO: difine is-busy

					EasySocial.ajax('site/controllers/location/suggestLocations', {
						"address": address,
					}).done(function(locations) {

						// Store a copy of the results
						self.locations[address] = locations;

						self.suggest(locations);
						self.textField().focus();
						self.element.addClass("has-suggested");
					});

				}, 250),

				suggest: function(locations) {

					var suggestions = self.suggestions();
					
					// Clear location suggestions
					suggestions.empty();

					$.each(locations, function(i, location) {

						// Create suggestion and append to list
						self.view.suggestion({
								"location": location
							})
							.data("location", location)
							.appendTo(suggestions);
					});

					self.autocomplete().addClass("active");
				},

				"{suggestion} activate": function(suggestion, event) {

					var location = suggestion.data("location");

					self.navigate(location.latitude, location.longitude);
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

					// Remove active class on the auto complete
					self.autocomplete().removeClass('active');
					
					// self.suggestions().hide();
				},

				set: function(location) {

					self.currentLocation = location;

					var address = location.name;

					self.textField().val(address);

					self.latitude()
						.val(location.latitude);

					self.longitude()
						.val(location.longitude);

					self.detectLocationButton().hide();
					
					self.trigger("locationChange", [location]);

				},

				unset: function() {
                    self.currentLocation = null;

                    self.textField().val('');

                    self.mapImage().attr("src", "");

                    self.element.removeClass("has-location");

                    self.detectLocationButton().show();
				},

				"{detectLocationButton} click": function() {

					var map = self.map;

					self.element.addClass("is-busy");

					$.GMaps.geolocate({
						success: function(position) {
							EasySocial.ajax('site/controllers/location/getLocations', {
								latitude: position.coords.latitude,
								longitude: position.coords.longitude
							}).done(function(locations) {

								self.element.removeClass("is-busy");

								self.element.addClass("has-suggested");

								self.suggest(locations);
								self.textField().focus();
							});
						},
						error: function(error) {
							// error.message
						},
						always: function() {

						}
					});
				},

				"{removeButton} click": function(removeButton, event) {
					self.unset();
				},

				"{story} save": function(el, element, save) {

					var currentLocation = self.currentLocation;

					if (!currentLocation) {
						return;
					}

					save.addData(self, {
						short_address    : currentLocation.name,
						formatted_address: currentLocation.address,
						lat              : currentLocation.latitude,
						lng              : currentLocation.longitude
					});
				},

				"{story} clear": function() {
					self.unset();
				}

			}});

			// Resolve module
			module.resolve();

		});

});
