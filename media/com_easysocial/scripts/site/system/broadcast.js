EasySocial.module('site/system/broadcast', function($){

	var module 				= this;

	EasySocial.require()
	.library('gritter')
	.done(function($){

		EasySocial.Controller(
			'System.Broadcast',
			{
				defaultOptions: {
					interval: 30,
					sticky: false,
					period: 8
				}
			},
			function(self){
				return {

					init: function()
					{
						self.startMonitoring();
					},
					
					startMonitoring: function()
					{
						var interval = self.options.interval * 1000;

						self.options.state	= setTimeout(self.check, interval);
					},

					stopMonitoring: function()
					{
						clearTimeout(self.options.state);
					},

					check: function()
					{
						// Stop monitoring so that there wont be double calls at once.
						self.stopMonitoring();

						var interval = self.options.interval * 1000;

						// Needs to run in a loop since we need to keep checking for new notification items.
						setTimeout(function(){

							EasySocial.ajax('site/controllers/notifications/getBroadcasts')
							.done(function(items){

								if (items) {

									$(items).each(function(i, item) {

										$.gritter.add({
											title: item.title,
											text: item.content,
											image: item.authorAvatar,
											sticky: self.options.sticky,
											time: self.options.period * 1000,
											class_name: 'es-broadcast'
										});

									});
								}

								// Continue monitoring.
								self.startMonitoring();
							});

						}, interval);

					},
				}
			});

		module.resolve();
	});

});
