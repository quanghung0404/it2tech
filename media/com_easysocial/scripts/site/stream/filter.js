EasySocial.module( 'site/stream/filter' , function($){

	var module 				= this;

	EasySocial.require()
	.script( 'site/stream/sidebar' )
	.language(
		'COM_EASYSOCIAL_STREAM_FILTER_WARNING_TITLE_EMPTY',
		'COM_EASYSOCIAL_STREAM_FILTER_WARNING_HASHTAG_EMPTY'
	)
	.done(function($){

		EasySocial.Controller(
			'Stream.Filter',
			{
				defaultOptions:
				{
					"{heading}"			: "[data-filter-heading]",
					"{sidebar}"			: "[data-sidebar-item]",
					"{content}"			: "[data-filter-real-content]"

				}
			},
			function(self){

				return{

					init: function()
					{
						// Implement sidebar controller.
						self.sidebar().implement(EasySocial.Controller.Stream.Filter.Sidebar, {
							"{parent}"	: self
						});

					},

					/**
					 * Responsible to update the heading area in the dashboard.
					 */
					updateHeading: function( title , description )
					{
						self.heading().find( '[data-heading-title]' ).html( title );
						self.heading().find( '[data-heading-desc]' ).html( description );
					},

					/**
					 * Add a loading icon on the content layer.
					 */
					updatingContents: function()
					{
						self.element.addClass("loading");
					},

					/**
					 * Responsible to update the content area in the dashboard.
					 */
					updateContents : function( contents )
					{
						self.element.removeClass("loading");

						// Hide the content first.
						self.content().html( contents );
					}
				}
			});

		EasySocial.Controller(
			'Stream.Filter.Item',
			{
				defaultOptions:
				{
					"{title}"	: "[data-filter-name]",
					"{hashtag}"	: "[data-filter-hashtag]",
					"{saveBtn}"	: "[data-stream-filter-save]",
					"{deleteBtn}"	: "[data-stream-filter-delete]",
					"{form}"	: "[data-filter-inputForm]",

					"{notice}"	: "[filter-form-notice]"
				}
			}, function(self) {

				return{
					init: function()
					{
					},

					"{deleteBtn} click" : function( el )
					{
						var fid = el.data('id');
						var uid = el.data('uid');
						var utype = el.data('utype');

						var controllerPath = 'site/controllers/stream/deleteFilter';

						if( uid )
						{
							var controllerPath = 'site/controllers/' + utype + 's/deleteFilter';
						}

						EasySocial.dialog({
							content		: EasySocial.ajax( 'site/views/stream/confirmFilterDelete' ),
							bindings	:
							{
								"{deleteButton} click" : function()
								{
									EasySocial.ajax( controllerPath,
									{
										"id"		: fid,
										"uid" 		: uid,
										"utype"		: utype
									})
									.done(function( html )
									{
										self.element.fadeOut();

										// close dialog box.
										EasySocial.dialog().close();
									});
								}
							}
						});
					},

					"{saveBtn} click" : function()
					{

						$('div.control-group').removeClass( 'error' );
						self.notice().html();
						self.notice().hide();

						if( self.title().val() == '' )
						{
							self.title().parents('div.control-group').addClass( 'error' );

							self.notice().html( $.language( 'COM_EASYSOCIAL_STREAM_FILTER_WARNING_TITLE_EMPTY' ) );
							self.notice().show();

							return false;
						}

						if( self.hashtag().val() == '' )
						{
							self.hashtag().parents('div.control-group').addClass( 'error' );

							self.notice().html( $.language( 'COM_EASYSOCIAL_STREAM_FILTER_WARNING_HASHTAG_EMPTY' ) );
							self.notice().show();

							return false;
						}

						self.form().submit();
					}


				}

			});

		module.resolve();
	});

});
