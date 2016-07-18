EasySocial.module( 'privacy' , function($) {

var module = this;

	EasySocial.require()
	.library( 'dialog' )
	.view( 'admin/profiles/form.privacy.custom.item' )
	.done(function($){

		EasySocial.Controller(
				'Profiles.Form.Privacy',
				{
					defaultOptions: {

						path: 'admin',

						// Elements
						"{selection}"			: ".privacySelection",
						"{browseButton}"    	: ".browseButton",
						"{userDeleteButton}"	: ".userDeleteButton",
						"{customContainer}"		: ".customContainer",

						view: {
							customItem : 'admin/profiles/form.privacy.custom.item'
						}

					}
				},
				function( self ){

					return {

						init: function()
						{

						},

						/**
						 * Binds the privacy's rule type select.
						 */
						"{selection} change" : function( el , event ){

							var selected	= $(el).val();
							var eleName		= $(el).attr( 'name' );

							if( selected == 'custom' )
							{
								self.customContainer().show();
							}
							else
							{
								self.customContainer().hide();
							}

						},

						"{userDeleteButton} click" : function( el, event ) {
							$(el).parents('li').remove();
						},

						"{browseButton} click" : function( el, event ) {


							var eleId		= $(el).attr( 'id' );
							var eleIndex 	= $(el).data('index');

							var userlistingpath = $.rootPath + 'administrator/index.php?option=com_easysocial&view=users&layout=listing&show=iframe';

							if( self.options.path == 'site' )
								userlistingpath = $.rootPath + 'index.php?option=com_easysocial&view=friends&layout=listing&show=iframe';

 							$.dialog({
 								title: 'Browse Users & Groups',
 								content: userlistingpath,
					            body: {
					                css: {
					                    width: 400,
					                    height: 300
					                }
					            },
								buttons: [
									{
										name : 'Assign',
										click : function(){

											var users = $('.foundryDialog').find('iframe').contents().find('input:checked');

											if( users.length > 0 )
											{
												for(var i = 0; i < users.length; i++)
												{
													var eleName = $(users[i]).attr('name');

													if( eleName == 'toggle')
														continue;

													var userId = $(users[i]).val();
													var userName = $('.foundryDialog').find('iframe').contents().find('input[name="user_' +userId+ '"]').val();

													var addedUsers = $('ul#privacy_ul' + eleIndex + ' input:hidden');
													var doAdd      = true;

													if( addedUsers.length > 0 )
													{
														for( var j=0; j < addedUsers.length; j++ )
														{
															if( $(addedUsers[j]).val() == userId )
															{
																doAdd = false;
																break;
															}
														}
													}

													if( doAdd )
													{
														html = self.view.customItem({
														 	userName : userName,
															eleName : eleId,
															userId  : userId
														});

														$('ul#privacy_ul' + eleIndex ).append( html );
													}
												}
											}

											//$("#google").contents().find("#hplogo").remove());

											$.dialog().close();
										}
									},
									{
										name : 'Close',
										click : function(){
											$.dialog().close();
										}
									}

								]
 							});



						}


					} //end return
				}//end function(self)
		);

		module.resolve();
	});

});
