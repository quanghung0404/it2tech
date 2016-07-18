EasyBlog.module("composer/location", function($){

var module = this;

EasyBlog.Controller("Composer.Location",
{
    elements: [
        "[data-eb-composer-location-{remove-button|label|textfield|places|autocomplete|form|map|form-message|detect-button}]",
        ".eb-composer-{field}-location"
    ],

    defaultOptions: {

        foursquare: {
            client_id: null,
            client_secret: null,
            v: "20140905",
            m: "foursquare",
            intent: "browse",
            radius: 800
        },

        geomode: "native", // native, external
        searchmode: "detect",

        "{container}": "[data-eb-composer-location]",
        "{place}": "[data-eb-composer-location-places] li",
        "{addLocationButton}": ".eb-document-add-location-button"
    }
},
function(self, opts, base, autocomplete, places) { return {

    init: function() {

        base = self.container();

        // Detect if user browser supports geolocation
        if (!navigator.geolocation) {
            opts.geomode = "external";
        }

        self.setupAutocomplete();
    },

    setupAutocomplete: function() {

        // Prepare autocomplete dropdown
        autocomplete = self.autocomplete().detach();
        places = autocomplete.find(self.places);
        textfield = self.textfield();

        $(document).on("click", function(event){

            var elements = $(event.target).parents().andSelf();

            if (elements.filter(textfield).length < 1) {
                autocomplete.detach();
            }
        });
    },

    searching: function(isSearching) {

        self.form().toggleClass("is-searching", isSearching);

        var textfield = self.textfield();

        textfield
            .attr("placeholder",
                isSearching ?
                    textfield.data("placeholder-searching") :
                    textfield.data("placeholder")
            );
    },

    userCoords: null,

    getUserCoords: function() {

        var task = $.Deferred();
        var userCoords = self.userCoords;

        // If we have user lat & lng, automatically resolve this.
        if (userCoords) {
            task.resolve(userCoords);
            return task;
        }

        if (opts.geomode=="native") {

            navigator.geolocation.getCurrentPosition(

                // If successful
                function(position) {
                    var coords = position.coords;

                    task.resolve({
                        latitude: coords.latitude,
                        longitude: coords.longitude
                    });
                },

                // If failed, get user coords by IP.
                function() {

                    self.getUserCoordsByIP()
                        .done(task.resolve)
                        .fail(task.reject);
                }

            );

        } else {
            task = self.getUserCoordsByIP();
        }

        return task.done(function(coords){
            self.userCoords = coords;
        });
    },

    getUserCoordsByIP: function() {

        var request =
            $.getJSON("//www.telize.com/geoip?callback=?")
                .then(function(data) {
                    return {latitude: data.latitude, longitude: data.longitude};
                });
    },

    // search: $.memoize(function(query) {

    //     console.log('search');

    //     var task =
    //         self.getUserCoords()
    //             .then(function(coords){

    //                 self.searching(true);

    //                 var request = EasyBlog.ajax("site/views/composer/getLocations", {
    //                         latitude: coords.latitude,
    //                         longitude: coords.longitude,
    //                         query: query || ""
    //                     });

    //                 return request;
    //             })
    //             .fail(function(message){

    //                 // Add error message
    //                 self.container().addClass('has-errors');
    //                 self.formMessage().html(message);

    //                 self.search.reset(query);
    //             })
    //             .always(function(){

    //                 self.searching(false);
    //             });


    //     console.log(task);

    //     return task;
    // }),

    search: function(query) {

        var task =
            self.getUserCoords()
                .then(function(coords){

                    self.searching(true);

                    var request = EasyBlog.ajax("site/views/composer/getLocations", {
                            latitude: coords.latitude,
                            longitude: coords.longitude,
                            query: query || ""
                        });

                    return request;
                })
                .fail(function(message){

                    // Add error message
                    self.container().addClass('has-errors');
                    self.formMessage().html(message);

                    self.search.reset(query);
                })
                .always(function(){

                    self.searching(false);
                });

        return task;
    },


    searchManual: $.memoize(function(query) {

        var task = $.Deferred();

        self.searching(true);

        var request = EasyBlog.ajax("site/views/composer/getLocations", {
                "query": query
            }).done(function(){
                task.resolve();
            })
            .always(function(){
                self.searching(false);
            });

        return request;
    }),

    populate: function() {

        var textfield = self.textfield();
        var query = $.trim(textfield.val());

        var mode = self.searchmode;

        if (mode == "detect") {

            self.search(query)
                .done($.debounce(function(venues) {

                    // Generate suggestions
                    var list = [];

                    $.each(venues, function(i, venue){

                        var item =
                            $.create("li")
                                .html("<strong>" + venue.name + "</strong><small>" + venue.address + "</small>")
                                .data("venue", venue)[0];

                        list.push(item);
                    });

                    // Add to places
                    places.empty().append(list);

                    // Display & reposition autocomplete
                    self.reposition();

                }, 50))
                .fail(function(msg) {

                });
        } else {

            if (query == "") {
                return;
            }

            self.searchManual(query)
                .done($.debounce(function(venues) {

                    // Generate suggestions
                    var list = [];

                    $.each(venues, function(i, venue){

                        var item =
                            $.create("li")
                                .html("<strong>" + venue.name + "</strong><small>" + venue.address + "</small>")
                                .data("venue", venue)[0];

                        list.push(item);
                    });

                    // Add to places
                    places.empty().append(list);

                    // Display & reposition autocomplete
                    self.reposition();

                }, 100))
                .fail(function(msg) {

                });
        }

    },

    _populate: $.debounce(function() {
        self.populate();
    }, 350),

    show: function() {
        self.composer.document.artboard.show("location");
    },

    hide: function() {
        self.composer.document.artboard.hide("location");
    },

    reposition: function() {

        var textfield = self.textfield();

        // Display autocomplete
        autocomplete
            .appendTo(self.composer.document.artboard.container())
            .css({
                width: textfield.outerWidth()
            })
            .position({
                my: "left top",
                at: "left bottom",
                of: textfield
            });
    },

    currentLocation: null,

    setLocation: function(venue) {

        self.currentLocation = venue;

        var field = self.field();

        // Update fields
        field.addClass("has-location");
        field.find("[name=address]").val(venue.name);
        field.find("[name=latitude]").val(venue.latitude);
        field.find("[name=longitude]").val(venue.longitude);
        self.label().html(venue.name);

        self.addLocationButton().addClass("has-location");
        base.addClass("has-location is-loading has-art");

        // Construct map url
        var map = self.map();
        var coords = venue.latitude + "," + venue.longitude;

        // Note: 1280x1280 is the largest size Google Maps offers
        var params = $.param({size: "1280x1280", sensor: true, scale: 2, zoom: 15});
        var url = "//maps.googleapis.com/maps/api/staticmap?" + params + "&center="  + coords + "&markers=" + coords;

        // When map is loaded, fade in.
        $.Image.get(url)
            .done(function(){
                map.css("backgroundImage", $.cssUrl(url));
                setTimeout(function(){
                    map.addClass("is-ready");
                }, 1);
            }).always(function(){
                base.removeClass("is-loading");
            });

    },

    removeLocation: function() {

        var map = self.map();
        map.removeClass("is-ready");
        base.removeClass("has-location has-art");
        self.addLocationButton().removeClass("has-location");

        // update field
        var field = self.field();
        field.removeClass("has-location");
        field.find("[name=address]").val('');
        field.find("[name=latitude]").val('');
        field.find("[name=longitude]").val('');
        self.label().html('');
    },

    "{window} resize": function() {

        if (autocomplete.parent().length < 1) return;

        self.reposition();
    },

    "{label} click": function(label) {
        self[!self.field().hasClass("active") ? "show" : "hide"]();
    },

    "{self} composerArtboardShow": function(el, event, id) {
        self.field().toggleClass("active", id=="location");
    },

    "{self} composerArtboardHide": function(el, event, id) {
        self.field().removeClass("active");
    },

    // "{textfield} focus": function(textfield) {
    //     // self.populate();
    // },

    "{textfield} input": function() {
        self.searchmode = "manual";

        autocomplete.detach();
        self._populate();
    },

    "{detectButton} click": function() {

        // Remove the value from the textbox.
        self.textfield().val('');

        self.searchmode = "detect";
        autocomplete.detach();
        self.populate();
    },

    "{place} click": function(place) {

        var venue = place.data("venue");

        self.setLocation(venue);
    },

    "{removeButton} click": function() {
        self.removeLocation();
    }

}});

module.resolve();

});
