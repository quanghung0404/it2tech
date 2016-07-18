EasySocial.module( 'site/points/history' , function(){

	var module	= this;

	EasySocial.require()
	.done(function($)
	{
		EasySocial.Controller(
			'Points.History',
			{
				defaultOptions :
				{
					"{loadMore}"	: "[data-points-history-pagination]",
					"{timeline}"	: "[data-points-history-timeline]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
					},

					"{loadMore} click" : function( el , event )
					{
						var current 	= $( el ).data( 'current' );

						EasySocial.ajax( 'site/views/points/getHistory' , 
						{
							"limitstart"	: current,
							"id"			: self.options.id
						}).done(function( contents , nextLimit , done )
						{
							self.timeline().append( contents );

							$( el ).data( 'current' , nextLimit );

							if( done )
							{
								$( el ).hide();
								// $( el ).attr( 'disabled' , 'disabled' );
							}
						});
					}
				}
			});

		module.resolve();
	});
});
