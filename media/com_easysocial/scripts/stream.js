EasySocial.module( 'stream' , function(){

	var module	= this;


	EasySocial.require()
	.library( 'dialog' )
	.script( 'comment' )
	.view( 'site/likes/item' )
	.language(
		'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_UNSUBSCRIBE',
		'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_SUBSCRIBE',
		'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_OK',
		'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_SUBMIT',
		'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_CANCEL',
		'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_UNSUBSCRIBE',
		'COM_EASYSOCIAL_SUBSCRIPTION_ARE_YOU_SURE_UNSUBSCRIBE',
		'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_SUBSCRIBE',
		'COM_EASYSOCIAL_STREAM_DIALOG_FEED',
		'COM_EASYSOCIAL_STREAM_BUTTON_CLOSE'
	)
	.done(function($){

		EasySocial.Controller(
			'Stream.Item',
			{
				defaultOptions: {
					id : '',

					'{streamItem}' : '.streamItem',
					'{streamData}' : '.streamData',

					'{streamResponds}' : '.stream-responds',

					'{likeItem}' : '.likeItem',
					'{likeItemList}' : '.likeItemList',

					'{commentLink}' : '.commentLink',
					'{commentFrame}' : '.commentFrame',
					'{commentInput}' : '.commentInput',

					'{followItem}' : '.followItem',
					'{unfollowItem}' : '.unfollowItem',

					'{hideItem}' : '.hideItem',
					'{unhideItem}' : '.unhideItem'
				}
			},
			function( self ){ return {

				init: function(){
					self.commentFrame().implement('EasySocial.Controller.Comments', {						
						uid: self.element.data('id'),
						pagination: new CommentPagination({
							total: self.commentFrame().data('total')
						}),
						commentlist: new Comment.List()
					});
				},

				"{likeItem} click" : function(){
					EasySocial.ajax( 'site:/controllers/likes/toggle' ,
						{
							'id' 		: self.element.data('id'),
							'type'		: 'stream'
						} ,
						{
							success: function( obj ){

								var content = '';

								if( obj.likeCount > 0 )
								{
									content = self.view.likeitem({
									 	likeCount : obj.likeCount
									});

									// temp solution bcos ejs cannot process html code.
									content.find(".likeText").html(obj.message);
								}

								// update the like text
								self.likeItemList().html(content);

								// update the label
								self.likeItem().text( obj.label );

							},
							fail: function(){

							}
						});

				},

				"{unfollowItem} click" : function(){

					var subId = self.element.data('sid');

					if( subId )
					{
						// perform unsubscription.
						$.dialog({
							title: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_UNSUBSCRIBE' ),
							content: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_ARE_YOU_SURE_UNSUBSCRIBE' ),
							buttons:
							[
								{
									name: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_UNSUBSCRIBE' ),
									click: function(){

										EasySocial.ajax( 'site:/controllers/subscriptions/remove' ,
											{
												'id' 				: subId
											} ,
											{
												success: function( obj ){
													$.dialog({
														title: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_UNSUBSCRIBE' ),
														content: obj.message,
														buttons: [
															{
																name: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_OK' ),
																click: function(){
																	self.unfollowItem().removeClass( 'unfollowItem' );
																	$.dialog().close();
																}
															}

														]
													});
												},
												fail: function(){

												}
											});

									}

								},
								{
									name: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_CANCEL' ),
									click: function(){
										$.dialog().close();
									}
								}
							]
						});

					}

				},

				"{followItem} click" : function(){

					// perform subscription.
					EasySocial.ajax( 'site:/controllers/subscriptions/form' ,
						{
							'contentId' 		: self.element.data('id'),
							'contentType'		: 'stream'
						} ,
						{
							success: function( obj ){

								if( obj.message != '' )
								{
									$.dialog({
										title: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_SUBSCRIBE' ),
										content: obj.message,
										buttons: [
											{
												name: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_CANCEL' ),
												click: function(){
													$.dialog().close();
												}
											}
										]
									});

									return;
								}

								$.dialog({

									title: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_SUBSCRIBE' ),
									content: obj.htmlform,
									buttons: [
										{
											name : $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_SUBMIT' ),
											click : function(){

												var fullname 	= $('#esfullname').val() ;
												var email 		= $('#email').val();

												EasySocial.ajax( 'site:/controllers/subscriptions/add' ,
													{
														'contentId' 		: self.element.data('id'),
														'contentType'		: 'stream',
														'esfullname'		: fullname,
														'email'				: email
													} ,
													{
														success: function( obj ){
															$.dialog({
																title: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_DIALOG_SUBSCRIBE' ),
																content: obj.message,
																buttons: [
																	{
																		name: $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_OK' ),
																		click: function(){
																			self.followItem().removeClass( 'followItem' );
																			$.dialog().close();
																		}
																	}

																]
															});
														},
														fail: function(){

														}
													});

											}
										},
										{
											name : $.language( 'COM_EASYSOCIAL_SUBSCRIPTION_BUTTON_CANCEL' ),
											click : function(){
												$.dialog().close();
											}
										}
									]
								});
							},
							fail: function(){

							}
						});

				},

				"{hideItem} click" : function(){
					EasySocial.ajax( 'site:/controllers/stream/hide' ,
						{
							'id' 		: self.element.data('id')
						} ,
						{
							success: function( obj )
							{
								var content = '<div>' + obj.message + '</div>';

								self.streamData().hide();
								self.element.append(content);
							},
							fail: function( obj )
							{
								$.dialog({
									title: $.language( 'COM_EASYSOCIAL_STREAM_DIALOG_FEED' ),
									content: obj.message,
									buttons: [
										{
											name: $.language( 'COM_EASYSOCIAL_STREAM_BUTTON_CLOSE' ),
											click: function(){
												$.dialog().close();
											}
										}
									]
								});
							}
						});

				},


				"{unhideItem} click" : function(){
					EasySocial.ajax( 'site:/controllers/stream/unhide' ,
						{
							'id' 		: self.element.data('id')
						} ,
						{
							success: function( obj )
							{
								self.streamData().show();
								self.element.children().last().remove();

							},
							fail: function( obj )
							{
								$.dialog({
									title: $.language( 'COM_EASYSOCIAL_STREAM_DIALOG_FEED' ),
									content: obj.message,
									buttons: [
										{
											name: $.language( 'COM_EASYSOCIAL_STREAM_BUTTON_CLOSE' ),
											click: function(){
												$.dialog().close();
											}
										}
									]
								});
							}
						});
				},

				"{commentLink} click" : function(){
					self.commentInput().focus();

				}

			} }
		);


		module.resolve();
	});
});