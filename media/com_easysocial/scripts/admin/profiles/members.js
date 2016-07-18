EasySocial.module( 'admin/profiles/members' , function($) {

	var module = this;

	EasySocial
	.require()
	.language( 
		'COM_EASYSOCIAL_CANCEL_BUTTON',
		'COM_EASYSOCIAL_ASSIGN_BUTTON',
		'COM_EASYSOCIAL_PROFILES_ASSIGN_USER_DIALOG_TITLE'
	)
	.done( function($)
	{
		EasySocial.Controller(
			'Profiles.Members',
			{
				defaultOptions :
				{
					"{addUser}"	: "[data-profiles-addUser]",
					"{row}"		: "[data-profiles-members-row]"
				}
			},
			function(self)
			{
				return {

					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					"{memberList} userSelected": function( el , event , id , name )
					{
						EasySocial.ajax( 'admin/controllers/profiles/insertMember', 
						{
							"id"			: id,
							"profile_id"	: self.options.id
						})
						.done( function( row )
						{
							self.row().append( row );

							// Close the dialog.
							EasySocial.dialog().close();
						});
					},

					"{addUser} click" : function()
					{
						var callbackId 	= $.callback( function(memberList){
							self.addPlugin( 'memberList' , memberList );
						});

						var url 		= $.indexUrl + "?option=com_easysocial&view=users&tmpl=component&callback=" + callbackId;

						EasySocial.dialog({
							title 		: $.language( 'COM_EASYSOCIAL_PROFILES_ASSIGN_USER_DIALOG_TITLE' ),
							content		: url,
							showOverlay	: false,
							width 		: 700,
							height 		: 600,
							buttons		:
							[
								{
									"name"			: $.language( "COM_EASYSOCIAL_CANCEL_BUTTON" ),
									"classNames"	: "btn btn-es",
									"click"			: function()
									{
										EasySocial.dialog().close();
									}
								}
							]
						});
					}

				}
			});

		module.resolve();

	});

});