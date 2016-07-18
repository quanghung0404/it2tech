EasySocial.module('apps/fields/user/address/maps', function($) {
    var module = this;

    // Create search template first
    $.template('easysocial/maps.suggestion', '<div class="es-location-suggestion" data-location-suggestion><span class="formatted_address">[%= location.formatted_address %]</span></div>');

    EasySocial
    .require()
    .library('gmaps', 'placeholder', 'image')
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

        EasySocial.Controller('Field.Address.Maps', {
            defaultOptions: {
                required            : null,

                zoom                : 2,

                latitude            : null,
                longitude           : null,
                address             : null,

                singleLocation      : true,

                required            : false,

                ratio               : 3,

                '{field}'           : '[data-field-address]',

                '{base}'            : '[data-location-base]',

                '{map}'             : '[data-location-map]',
                '{mapImage}'        : '[data-location-map-image]',

                '{detectButton}'    : '[data-location-detect]',
                '{removeButton}'    : '[data-location-remove]',

                '{form}'            : '[data-location-form]',
                '{textbox}'         : '[data-location-textbox]',
                '{textField}'       : '[data-location-textfield]',

                '{autocomplete}'    : '[data-location-autocomplete]',
                '{suggestions}'     : '[data-location-suggestions]',
                '{suggestion}'      : '[data-location-suggestion]',

                '{source}'          : '[data-location-source]',

                view: {
                    suggestion: 'maps.suggestion'
                }
            }
        }, function(self) {
            return {
                init: function() {
                    if (navigator.geolocation) {
                        self.base().addClass("is-detectable");
                        // self.detectButton().show();
                    }

                    // Add placeholder support for IE9
                    self.textField().placeholder();

                    // Allow textField input only when controller is implemented
                    self.textField().removeAttr("disabled");

                    if (!$.isEmpty(self.source().val())) {
                        var data = JSON.parse(self.source().val());

                        if (data.latitude && data.longitude) {
                            self.navigate(data.latitude, data.longitude);

                            self.base().addClass("has-location");
                        }
                    }
                },

                "{window} resize": $.debounce(function() {

                    var data = JSON.parse(self.source().val());

                    if (!data.latitude || !data.longitude) {
                        return;
                    }

                    var mapImage = self.mapImage();

                    if (mapImage.data("width") !== mapImage.width()) {
                        self.navigate(data.latitude, data.longitude);
                    }

                }, 250),

                '{self} onShow': function() {

                    var data = JSON.parse(self.source().val());

                    if (!data.latitude || !data.longitude) {
                        return;
                    }

                    var mapImage = self.mapImage();

                    if (mapImage.data("width") !== mapImage.width()) {
                        self.navigate(data.latitude, data.longitude);
                    }
                },

                navigate: function(lat, lng) {
                    self.field().css({
                        "max-width": "none"
                    });

                    var mapImage = self.mapImage(),
                        width = Math.floor(mapImage.width()),
                        height = Math.floor(mapImage.height()),
                        url = $.GMaps.staticMapURL({
                            size: [1280, 1280],
                            lat: lat,
                            lng: lng,
                            sensor: true,
                            scale: 2,
                            markers: [
                                {lat: lat, lng: lng}
                            ]
                        });

                    // When map is loaded, fade in.
                    $.Image.get(url)
                        .done(function(){
                            mapImage.css({
                                "backgroundImage": $.cssUrl(url),
                                "backgroundSize": "cover",
                                "backgroundPosition": "center center"
                            });
                            self.base().addClass("has-location");
                        })
                        .always(function(){
                            self.base().removeClass("is-loading");
                        });
                },

                locations: {},

                lastQueryAddress: null,

                results: [],

                result: null,

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

                    $.GMaps.geocode({
                        address: address,
                        callback: function(locations, status) {

                            self.base().removeClass("is-busy");

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

                suggest: function(locations) {

                    var suggestions = self.suggestions();

                    // Clear location suggestions
                    suggestions
                        .empty();

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

                    self.showSuggestions();
                },

                showSuggestions: function() {

                    self.focusSuggestion = true;

                    self.element.find(".es-story-footer")
                        .addClass("swap-zindex");

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
                        location.geometry.location.lat(),
                        location.geometry.location.lng()
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

                    var lat = location.geometry.location.lat(),
                        lng = location.geometry.location.lng();

                    self.navigate(lat, lng);

                    var address = location.formatted_address;

                    self.textField().val(address);

                    self.lastQueryAddress = address;

                    self.base().addClass("has-location");

                    // Set the source here
                    self.result = location;
                    var data = self.getResult('source');
                    self.source().val(JSON.stringify(data));
                },

                unset: function() {

                    self.currentLocation = null;

                    self.textField().val('');

                    self.mapImage().attr("src", "");

                    self.base().removeClass("has-location");

                    self.source().val('');
                },

                detectTimer: null,

                "{detectButton} click": function() {

                    var textbox = self.textbox();

                    self.base().addClass("is-busy");

                    clearTimeout(self.detectTimer);

                    self.detectTimer = setTimeout(function() {
                        self.base().removeClass("is-busy");
                    }, 8000);

                    $.GMaps.geolocate({
                        success: function(position) {
                            $.GMaps.geocode({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                callback: function(locations, status) {
                                    if (status=="OK") {
                                        self.suggest(locations);
                                        self.textField().focus();
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

                            // story.setMessage(message);
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

                getResult: function(type) {
                    if(!self.result) {
                        if(self.results.length === 0) {
                            return false;
                        }

                        self.result = self.results[0];
                    }

                    var r = self.result;

                    if(type === undefined) {
                        return r;
                    }

                    switch(type) {
                        case 'coords':
                            return {
                                lat: r.geometry.location.lat(),
                                lng: r.geometry.location.lng()
                            }
                        break;

                        case 'lat':
                        case 'latitude':
                            return r.geometry.location.lat();
                        break;

                        case 'lng':
                        case 'longitude':
                            return r.geometry.location.lng();
                        break;

                        case 'address':
                            return r.formatted_address;
                        break;

                        case 'viewport':
                            return r.geometry.viewport;
                        break;

                        case 'bounds':
                            return r.geometry.bounds || r.geometry.viewport;
                        break;

                        case 'source':
                            var components = {};

                            $.each(r.address_components, function(index, component) {
                                if(component.types[0]) {
                                    components[component.types[0]] = component.long_name;
                                }
                            });

                            var mapping = {
                                'address1': ['street_address', 'route'],
                                'address2': ['intersection', 'colloquial_area', 'neighborhood', 'premise', 'subpremise'],
                                'city': ['locality', 'sublocality', 'sublocality_level_1', 'sublocality_level_2', 'sublocality_level_3', 'sublocality_level_4', 'sublocality_level_5'],
                                'state': ['administrative_area_level_1', 'administrative_area_level_2', 'administrative_area_level_3'],
                                'zip': 'postal_code',
                                'country': 'country'
                            };

                            // Based on the mapping we build the legacy data
                            var legacy = {};

                            $.each(mapping, function(key, value) {

                                // Init with empty data
                                legacy[key] = '';

                                if ($.isArray(value)) {
                                    $.each(value, function(i, v) {

                                        // Search if components[v] exists
                                        if (components[v] !== undefined) {

                                            // Use it if it exists
                                            legacy[key] = components[v];

                                            // Break out and ignore other possible keys
                                            return false;
                                        } else {

                                            // Continue finding
                                            return true;
                                        }
                                    });

                                    // Continue on to the next key
                                    return true;
                                }

                                if (components[value] !== undefined) {
                                    legacy[key] = components[value];
                                }
                            });

                            var data = $.extend(legacy, {
                                components: components,
                                address: r.formatted_address,
                                latitude: r.geometry.location.lat(),
                                longitude: r.geometry.location.lng()
                            });

                            return data;
                        break;
                    }
                }
            }
        });

        module.resolve();
    });
});
