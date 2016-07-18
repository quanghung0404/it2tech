EasySocial.module( 'site/search/dating' , function($){
	var module	= this;

	EasySocial.require()
	.script( 'site/search/map' )
	.done( function(){

		EasySocial.Controller(
		'Search.Dating',
		{
			defaultOptions:
			{
				"{itemCriteria}" 	: "[data-itemCriteria]",
				"{itemDataKey}" 	: "[data-itemDataKey]",
				"{itemOperator}" 	: "[data-itemOperator]",
				"{itemCondition}" 	: "[data-itemCondition]",

				"{dateStart}" 		: "[data-start]",
				"{dateEnd}" 		: "[data-end]",
				"{dataCondition}" 	: "[data-condition]",

				"{dataGender}" : "[data-gender-radio]",

                '{frmDistance}' : '[data-distance]',
                '{frmAddress}' : '[data-address]',
                '{frmLatitude}' : '[data-latitude]',
                '{frmLongitude}' : '[data-longitude]',

				"{locationLabel}" : "[data-location-label]",
				"{textField}" : "[data-location-textfield]"
			}
		},
		function( self ){
			return {

				init : function()
				{
					self.element.addController(EasySocial.Controller.Search.Map);

					if (self.frmAddress().val() != '') {
                    	self.textField().val(self.frmAddress().val());
					}
				},

				"{frmDistance} change" : function() {
                    var distance = self.frmDistance().val();
                    var address = self.frmAddress().val();
                    var lat = self.frmLatitude().val();
                    var lng = self.frmLongitude().val();

                    var computedVal = distance + '|' + lat + '|' + lng + '|' + address;
                    self.dataCondition().val(computedVal);
				},

				"{dataGender} click" : function(el) {
					self.dataCondition().val( el.val() );
				},

				"{dateStart} change" : function() {
					start 	= self.dateStart().val();
					end 	= self.dateEnd().val();

					var data = start;

					if( end.length > 0 )
					{
						data = data + '|' + end;
					}

					// update value
					self.dataCondition().val( data );
				},

				"{dateEnd} change" : function() {
					start 	= self.dateStart().val();
					end 	= self.dateEnd().val();

					var data = end;

					if( start.length > 0 )
					{
						data = start + '|' + data;
					}

					// update value
					self.dataCondition().val( data );
				}


			} //return
		});

		module.resolve();

	});

});
