EasySocial.module("site/repost/repost", function($){

	var module 	= this;

	EasySocial.require()
	.view(
		"site/friends/suggest.item",
		"site/friends/suggest.hint.search",
		"site/friends/suggest.hint.empty",
		"site/hashtags/suggest.item",
		"site/hashtags/suggest.hint.search",
		"site/hashtags/suggest.hint.empty"
	)
	.library( 'mentions' )
	.done(function()
	{
		$(document)
			.on("click.es.repost.action", "[data-repost-action]", function(){

				var button = $(this),
					data = {
						id     : button.data('id'),
						element: button.data('element'),
						group  : button.data('group'),
						clusterId  : button.data('clusterid'),
						clusterType  : button.data('clustertype'),
					},
					key = data.element + '-' + data.group + '-' + data.id;


				EasySocial.dialog(
				{
					content	: EasySocial.ajax( 'site/views/repost/form' , data ),
					bindings:
					{
						init: function()
						{
							this.setMentionsLayout();
						},
						setMentionsLayout: function()
						{
							var textbox		= this.textbox(),
								mentions	= textbox.controller("mentions");

							if (mentions)
							{
								mentions.cloneLayout();
								return;
							}

							var header = this.header();

							textbox
								.mentions({
									triggers: {
									    "@": {
											type: "entity",
											wrap: false,
											stop: "",
											allowSpace: true,
											finalize: true,
											query: {
												loadingHint: true,
												searchHint: $.View("easysocial/site/friends/suggest.hint.search"),
												emptyHint: $.View("easysocial/site/friends/suggest.hint.empty"),
												data: function(keyword) {

													var task = $.Deferred();

													EasySocial.ajax("site/controllers/friends/suggest", {search: keyword})
														.done(function(items){

															if (!$.isArray(items)) task.reject();

															var items = $.map(items, function(item){
																item.title = item.screenName;
																item.type = "user";
																item.menuHtml = $.View( 'easysocial/site/friends/suggest.item' , true, { item: item, name: "uid[]" });
																return item;
															});

															task.resolve(items);
														})
														.fail(task.reject);

													return task;
												},
												use: function(item) {
													return item.type + ":" + item.id;
												}
										    }
										},
										"#": {
										    type: "hashtag",
										    wrap: true,
										    stop: " #",
										    allowSpace: false,
											query: {
												loadingHint: true,
												searchHint: $.View("easysocial/site/hashtags/suggest.hint.search"),
												emptyHint: $.View("easysocial/site/hashtags/suggest.hint.empty"),
												data: function(keyword) {

													var task = $.Deferred();

													EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword})
														.done(function(items){

															if (!$.isArray(items)) task.reject();

															var items = $.map(items, function(item){
																item.title = "#" + item.title;
																item.type = "hashtag";
																item.menuHtml = $.View( 'easysocial/site/hashtags/suggest.item' , true, { item: item, name: "uid[]" });
																return item;
															});

															task.resolve(items);
														})
														.fail(task.reject);

													return task;
												}
										    }
										}
									},
									plugin: {
										autocomplete: {
											id: "fd",
											component: "es",
											modifier: "es-story-mentions-autocomplete",
											sticky: true,
											shadow: true,
											position: {
												my: 'left top',
												at: 'left bottom',
												of: header,
												collision: 'none'
											},
											size: {
												width: function() {
													return header.outerWidth(true);
												}
											}
										}
									}
								});
						},
						"{sendButton} click": function(sendButton)
						{
							var dialog = this.parent,
								content = $.trim(this.repostContent().val());

							// Add data content
							data.content = content;

							var mentions = this.textbox().mentions("controller").toArray();

							data.mentions = $.map(mentions, function(mention){
								if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
									mention.value = mention.value.title.slice(1);
								}
								return JSON.stringify(mention);
							});

							dialog.loading( true );

							EasySocial.ajax("site/controllers/repost/share", data )
								.done(function(content, isHidden, count, streamHTML)
								{
									var content = $.buildHTML(content);

									actionContent =
										$('[data-repost-' + key + ']')
											.toggleClass("hide", isHidden)
											.toggle(!isHidden);

									actionContent.find("span.repost-counter")
										.html(content);

									button.trigger("create", [streamHTML]);
								})
								.fail(function(message)
								{
									dialog.clearMessage();
									dialog.setMessage( message );
								})
								.always(function()
								{
									dialog.loading( false );
									dialog.close();
								});
						}
					}
				});
			});

		EasySocial.module("repost/authors", function(){

			this.resolve(function(popbox){

				var repost = popbox.button.parents("[data-repost-content]")
					data = {
						id     : repost.data("id"),
						element: repost.data("element")
					};

				return {
					content: EasySocial.ajax('site/controllers/repost/getSharers', data),
					id: "fd",
					component: "es",
					type: "repost",
					position: "bottom-right"
				}
			});
		});

		module.resolve();
	});
});
