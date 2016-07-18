
EasySocial.require()
.library( 'gmaps' )
.done(function($)
{
	// Render the map first
	var map = new $.GMaps({
							div: '#user-locations',
							zoom: 1,

							// Default latitude and longitude
							lat: -12.043333,
							lng: -77.028333
						});

	EasySocial.ajax( 'admin/controllers/easysocial/getCountries',
	{
	})
	.done(function( countries , content )
	{
		var newContent 	= $( content );

		$.each( countries, function( index , value )
		{
			$.GMaps.geocode(
			{
				address: value,
				callback: function(results, status)
				{
					if( status == 'OK' )
					{
						$( newContent )
							.find( '[data-stat-country="' + value + '"]' )
							.html( results[ 0 ].formatted_address );


						var latlng	= results[ 0 ].geometry.location;

						map.addMarker(
						{
							lat: latlng.lat(),
							lng: latlng.lng()
						});
					}
				}
			});
		});

		$( '[data-map-table-wrapper]' ).html( newContent );

	});

});
