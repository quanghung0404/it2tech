EasySocial.module("story/broadcast", function($){

	var module = this;

	EasySocial.require()
		.done(function(){

			EasySocial.Controller("Story.Broadcast",
				{
					defaultOptions: {
						"{profile}" : "[data-broadcast-profile]",
						"{title}" : "[data-broadcast-title]",
						"{link}" : "[data-broadcast-link]",
						"{message}": "[data-broadcast-message]",
						"{type}": "[data-broadcast-type]"
					}
				},
				function(self)
				{
					return {

					init: function()
					{
					},

					"{story} save": function(element, event, save)
					{
						// Determines which profile we should broadcast to
						var profileId = self.profile().val(),
							title = self.title().val(),
							link = self.link().val(),
							type = self.type().val();

						var data = {"broadcast": "1", "profileId" : profileId, "title" : title, "link" : link, "type" : type};

						save.addData(self, data);
					},

					"{story} beforeSubmit": function(element, event, save)
					{
						if (save.currentPanel != 'broadcast') {
							return;
						}

						save.data.content = self.message().val();
					}
				}}
			);

			// Resolve module
			module.resolve();

		});
});
