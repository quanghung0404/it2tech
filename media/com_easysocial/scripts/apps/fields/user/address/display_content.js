EasySocial.module('apps/fields/user/address/display_content', function($) {
    var module = this;

    EasySocial
    .require()
    .library('gmaps')
    .done(function() {
        EasySocial.Controller('Field.Address.Display', {
            defaultOptions: {
                latitude: null,
                longitude: null,
                ratio: 1,

                '{base}': '[data-location-base]',

                '{map}': '[data-location-map]',
                '{mapImage}': '[data-location-map-image]'
            }
        }, function(self) {
            return {
                init: function() {
                    // Init params
                    var map = self.map();

                    self.options.latitude = map.data('latitude');
                    self.options.longitude = map.data('longitude');

                    self.setLayout();
                },

                '{window} resize': $.debounce(function() {
                    self.setLayout();
                }, 250),

                navigate: function(lat, lng) {
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

                    var url = url.replace(/http\:|https\:/, '');
                    
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

                setLayout: function() {
                    setTimeout(function() {
                        if (self.options.latitude && self.options.longitude) {
                            self.navigate(self.options.latitude, self.options.longitude);
                        }
                    }, 1);
                },

                '{self} onShow': function() {
                    self.setLayout();
                }
            }
        });

        module.resolve();
    });
});
