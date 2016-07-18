EasySocial.module( 'site/search/advanced.criteria' , function(){
	var module	= this;

	EasySocial.require()
	.view( 'site/loading/small' )
	.script( 'site/search/map' )
	.language( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ADDRESS_DISTANCE_NOTICE' )
	.done( function($){

		EasySocial.Controller(
		'Search.Advanced.Criteria',
		{
			defaultOptions:
			{

				"{addButton}" 		: "[data-criteria-add-button]",
				"{removeButton}" 	: "[data-criteria-remove-button]",

				"{itemConditionDiv}" : "[data-itemConditionDiv]",
				"{itemCriteria}" 	: "[data-itemCriteria]",
				"{itemDataKey}" 	: "[data-itemDataKey]",
				"{itemOperator}" 	: "[data-itemOperator]",
				"{itemCondition}" 	: "[data-itemCondition]",

				"{dateStart}" 		: "[data-start]",
				"{dateEnd}" 		: "[data-end]",
				"{dataCondition}" 	: "[data-condition]",

                '{frmDistance}' : '[data-distance]',
                '{frmAddress}' : '[data-address]',
                '{frmLatitude}' : '[data-latitude]',
                '{frmLongitude}' : '[data-longitude]',

				//used for notice message if there is any
				"{dataNotice}" : "[data-criteria-notice]",

				"{locationLabel}" : "[data-location-label]",
				"{textField}" : "[data-location-textfield]",

				// loading gif
				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function()
				{
					self.element.addController(EasySocial.Controller.Search.Map);

					if (self.frmAddress().val() != undefined && self.frmAddress().val() != '') {
                    	self.textField().val(self.frmAddress().val());
                    	self.locationLabel().removeClass('hide');
					}
				},

				"{itemOperator} change" : function()
				{
					criteria 	= self.itemCriteria().find( 'select' );
					datakey		= self.itemDataKey().find( 'select' );
					operator 	= self.itemOperator().find( 'select' );

					var data 	= criteria.val().split( '|' );

					var key 	= data[0];
					var type 	= data[1];

					var opValue     = operator.val();
					var datakey		= datakey.val();

					if( opValue == 'blank' || opValue == 'notblank' )
					{
						self.itemCondition().hide();
					}
					else
					{
						if( opValue == 'between' )
						{
							self.getConditions( type, opValue, datakey );
						}
						else
						{
							if( type == 'datetime' || type == 'birthday' )
							{
								if( self.dateStart().length > 0 )
								{
									self.getConditions( type, opValue, datakey );
								}
							}
						}

						if( self.itemCondition().is( ":hidden" ) )
						{
							self.itemCondition().show();
						}

					}

				},

				getConditions : function( type, opValue, datakey )
				{
						// lets call ajax to get the value.
						EasySocial.ajax(
							"site/controllers/search/getConditions",
							{
								"element"	: type,
								"operator" 	: opValue,
								"datakey" 	: datakey
							})
							.done(function( conditions ) {

								self.itemCondition().show();

								// adding new condtions
								self.itemCondition().remove();
								var contents = $.buildHTML( conditions );
								contents.insertAfter( self.itemOperator() );

							})
							.fail( function( messageObj ){
								return messageObj;
							})
							.always(function(){

							});
				},

				"{frmDistance} change" : function() {

                    var distance = self.frmDistance().val();
                    var address = self.frmAddress().val();
                    var lat = self.frmLatitude().val();
                    var lng = self.frmLongitude().val();

                    var computedVal = distance + '|' + lat + '|' + lng + '|' + address;
                    self.dataCondition().val(computedVal);
				},

				"{dateStart} change" : function( el )
				{
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

				"{dateEnd} change" : function()
				{
					start 	= self.dateStart().val();
					end 	= self.dateEnd().val();

					var data = start;
					data = data + '|' + end;

					// update value
					self.dataCondition().val( data );
				},

				"{itemCriteria} change" : function()
				{
					select = self.itemCriteria().find( 'select' );

					if( select.val() != '' )
					{
						var data = select.val().split( '|' );

						var key 	= data[0];
						var type 	= data[1];

						//lets hide the notice message.
						self.dataNotice().addClass('hide');

						// lets get the correct operators and condition.

						EasySocial.ajax(
							"site/controllers/search/getDataKeys",
							{
								"key"		: key,
								"element"	: type
							})
							.done(function( datakeys, operators, conditions ) {

								self.itemDataKey().remove();
								self.itemOperator().remove();
								self.itemCondition().remove();

								// adding new operators
								// self.itemOperator().remove();
								var contents = $.buildHTML( operators );
								contents.insertAfter( self.itemConditionDiv() );

								// adding new operators
								if (datakeys != '') {
									var contents = $.buildHTML( datakeys );
									contents.insertAfter( self.itemConditionDiv() );
								}

								// adding new condtions
								// self.itemCondition().remove();
								var contents = $.buildHTML( conditions );
								contents.insertAfter( self.itemOperator() );

							})
							.fail( function( messageObj ){
								return messageObj;
							})
							.always(function(){

							});
					}

					// console.log( select.val() );
				},

				"{itemDataKey} change" : function()
				{
					select = self.itemCriteria().find( 'select' );

					if( select.val() != '' )
					{
						var data = select.val().split( '|' );

						var key 	= data[0];
						var type 	= data[1];

						//attempt to get the selected datakey
						var datakeys = self.itemDataKey().find( 'select' );
						var datakey = datakeys.val();

						// lets get the correct operators and condition.
						EasySocial.ajax(
							"site/controllers/search/getOperators",
							{
								"key"		: key,
								"element"	: type,
								"datakey" 	: datakey
							})
							.done(function( operators, conditions ) {

								// adding new operators
								self.itemOperator().remove();
								var contents = $.buildHTML( operators );
								contents.insertAfter( self.itemDataKey() );

								// adding new condtions
								self.itemCondition().remove();
								var contents = $.buildHTML( conditions );
								contents.insertAfter( self.itemOperator() );

								if (datakey == 'distance') {
									self.locationLabel().removeClass('hide');
								} else {
									self.locationLabel().addClass('hide');
								}

							})
							.fail( function( messageObj ){
								return messageObj;
							})
							.always(function(){

							});


					}

					// console.log( select.val() );
				},




				"{removeButton} click" : function()
				{
					// If this is the last search item, do not allow removing
					self.element.remove();
				}




			} //return
		});

		module.resolve();

	});

});
