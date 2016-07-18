EasySocial.module( 'site/search/map' , function($){
	var module	= this;

    // Create search template first
    $.template('easysocial/maps.suggestion', '<div class="es-location-suggestion" data-location-suggestion><span class="formatted_address">[%= location.formatted_address %]</span></div>');

	EasySocial.require()
	.library('gmaps')
	.done( function(){

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

		EasySocial.Controller(
		'Search.Map',
		{
			defaultOptions:
			{
                "{icon}" : "[data-loaction-icon]",
                "{locationLabel}" : "[data-location-label]",
                '{textField}'       : '[data-location-textfield]',

                "{detectButton}" : "[data-location-detect]",
                "{suggestions}"  : "[data-location-suggestions]",
                "{suggestion}"      : "[data-location-suggestion]",
                "{autocomplete}" : "[data-location-autocomplete]",

                // form elements
                "{dataCondition}" : "[data-condition]",
                "{frmDistance}" : "[data-distance]",
                "{frmAddress}" : "[data-address]",
                "{frmLatitude}" : "[data-latitude]",
                "{frmLongitude}" : "[data-longitude]",

                view: {
                    suggestion: 'maps.suggestion'
                }
			}
		},
		function( self ){
			return {

				init : function()
				{

				},

                locations: {},

                lastQueryAddress: null,

                results: [],

                result: null,

                "{detectButton} click": function() {

                    self.icon()
                            .removeClass('fa fa-flash')
                            .addClass('btn-loading');

                    clearTimeout(self.detectTimer);

                    // self.detectTimer = setTimeout(function() {
                    //     self.base().removeClass("is-busy");
                    // }, 8000);

                    $.GMaps.geolocate({
                        success: function(position) {
                            $.GMaps.geocode({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                callback: function(locations, status) {
                                    if (status=="OK") {
                                        self.suggest(locations);
                                    }
                                }
                            });
                        },
                        error: function(error) {
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

                            EasySocial.dialog({
                                content: message
                            });
                        },
                        always: function() {
                            clearTimeout(self.detectTimer);

                            self.icon()
                                    .removeClass('btn-loading')
                                    .addClass('fa fa-flash');
                        }
                    });
                },

                lookup: $.debounce(function(address) {

                    // self.base().addClass("is-busy");

                    $.GMaps.geocode({
                        address: address,
                        callback: function(locations, status) {

                            // self.base().removeClass("is-busy");

                            if (status=="OK") {

                                // Store a copy of the results
                                self.locations[address] = locations;

                                // Suggestion locations
                                self.suggest(locations);

                                self.lastQueryAddress = address;
                            }
                        }
                    });

                }, 250),


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

                            event.preventDefault();
                            break;

                        case KEYCODE.ESCAPE:
                            self.hideSuggestions();
                            event.preventDefault();
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
                                // self.base().removeClass("has-location");
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

                set: function(location) {

                    var lat = location.geometry.location.lat(),
                        lng = location.geometry.location.lng(),
                        address = location.formatted_address,
                        distance = self.frmDistance().val();

                    self.frmAddress().val(address);
                    self.frmLatitude().val(lat);
                    self.frmLongitude().val(lng);

                    var computedVal = distance + '|' + lat + '|' + lng + '|' + address;
                    self.dataCondition().val(computedVal);

                    self.textField().val(address);
                    // self.locationLabel().removeClass('hide');

                    self.hideSuggestions();

                },

                "{suggestion} click": function(suggestion, event) {
                    var location = suggestion.data("location");
                    self.set(location);
                },

                suggest: function(locations) {

                    self.hideSuggestions();

                    var suggestions = self.suggestions();

                    if (locations.length < 0) return;

                    self.results = locations;

                    $.each(locations, function(i, location){
                        // Create suggestion and append to list
                        self.view.suggestion({
                                location: location
                            })
                            .data("location", location)
                            .appendTo(suggestions);
                    });

                    // self.autocomplete().addClass('active');
                    self.showSuggestions();
                },

                showSuggestions: function() {

                    self.focusSuggestion = true;

                    // self.element.find(".es-story-footer")
                    //     .addClass("swap-zindex");

                    setTimeout(function(){

                        self.autocomplete().addClass("active");

                        var doc = $(document),
                            hideOnClick = "click.es.advancedsearch.location";

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

                    // Clear location suggestions
                    self.suggestions().empty();

                    self.focusSuggestion = false;

                    self.autocomplete().removeClass("active");

                    $(document).off("click.es.advancedsearch.location");

                }


			} //return
		});

		module.resolve();

	});

});
