EasySocial.module( 'admin/users/privacy' , function($){

	var module 	= this;

	EasySocial.require()
	.library( 'textboxlist' )
	.view( 'site/loading/small' )
	.done(function($){

		EasySocial.Controller(
			'Profile.Privacy',
			{
				defaultOptions:
				{
					userId	: '',

					"{privacyItem}" : "[data-privacy-item]",

					//input form
					"{privacyForm}" : "[data-profile-privacy-form]",

					view :
					{
						loading : "site/loading/small"
					}
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						self.privacyItem().implement( EasySocial.Controller.Profile.Privacy.Item ,
						{
							"{parent}"	: self
						});
					}
				}
			}
		);


		EasySocial.Controller(
			'Profile.Privacy.Item',
			{
				defaultOptions :
				{
					"{selection}"		: "[data-privacy-select]",
					"{hiddenCustom}" 	: "[data-hidden-custom]",
					"{customForm}" 		: "[data-privacy-custom-form]",

					"{customTextInput}" : "[data-textfield]",
					"{customItems}"		: "input[]",
					"{customHideBtn}"	: "[data-privacy-custom-hide-button]",
					"{customInputItem}"	: "[data-textboxlist-item]",
					"{customEditBtn}"   : "[data-privacy-custom-edit-button]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.customTextInput().textboxlist(
							{
								component: 'es',
								unique: true,

								plugin: {
									autocomplete: {
										exclusive: true,
										minLength: 2,
										cache: false,
										query: function( keyword ) {

											var users = self.getTaggedUsers();

											var ajax = EasySocial.ajax("site/views/privacy/getfriends",
												{
													q: keyword,
													userid: self.parent.options.userId,
													exclude: users
												});
											return ajax;
										}
									}
								}
							}
						);

						self.textboxlistLib = self.customTextInput().textboxlist("controller");
					},

					getTaggedUsers: function()
					{
						var users = [];
						var items = self.customInputItem();

						if( items.length > 0 )
						{
							$.each( items, function( idx, element ) {
								users.push( $( element ).data('id') );
							});
						}

						return users;
					},

					// event listener for adding new name
					"{customTextInput} addItem": function(el, event, data) {

						// lets get the exiting ids string
						var ids    = self.hiddenCustom().val();
						var values = '';

						if( ids == '')
						{
							values = data.id;
						}
						else
						{
							var idsArr = ids.split(',');
							idsArr.push( data.id );

							values = idsArr.join(',');
						}

						//now update the customhidden value.
						self.hiddenCustom().val( values );
					},

					// event listener for removing name
					"{customTextInput} removeItem": function(el, event, data ) {
						// lets get the exiting ids string
						var ids    = self.hiddenCustom().val();
						var values = '';
						var newIds = [];

						var idsArr = ids.split(',');

						for( var i = 0; i < idsArr.length; i++ )
						{
							if( idsArr[i] != data.id )
							{
								newIds.push( idsArr[i] );
							}
						}

						if( newIds.length <= 0 )
						{
							values = '';
						}
						else
						{
							values = newIds.join(',');
						}

						//now update the customhidden value.
						self.hiddenCustom().val( values );
					},

					"{customEditBtn} click" : function( el )
					{
						self.customForm().toggle();
					},

					"{selection} change" : function( el )
					{
						var selected = el.val();

						if( selected == 'custom' )
						{
							self.customForm().show();
							self.customEditBtn().show();
						}
						else
						{
							self.customForm().hide();
							self.customEditBtn().hide();
						}

						return;
					},

					"{customHideBtn} click" : function()
					{
						self.customForm().hide();
						self.customEditBtn().show();

						self.textboxlistLib.autocomplete.hide();

						return;
					}
				}
			});


		module.resolve();
	});

});
