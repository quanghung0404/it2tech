EasySocial.module("story/quickpost", function($){

	var module = this;

	EasySocial.Controller("Story.Quickpost", {
		defaultOptions: {
			"{submitButton}": "[data-quickpost-submit]",
			"{content}": "[data-quickpost-content]",
			"{message}": "[data-quickpost-message]",
			"{privacyButton}": "[data-story-privacy]",
			"{footer}": "[data-story-footer]"
		}
	}, function(self) { return {

		init: function() {
		},

		"{submitButton} click": function(el) {
			id = el.data('quickpost-userid');
            content = self.content().val();
            
            EasySocial.ajax('site/controllers/story/createFromModule', {
                'target': id,
                'privacy': 'public',
                'content': content
            }).done(function(successfull, message, html, id) {

            	// If not successfull we should just display the message as html codes
            	if (!successfull) {
            		self.message()
            			.html(message)
            			.addClass('alert fade in alert-warning');
            		return;
            	}

            	self.message().html(message)
            		.addClass('alert fade in alert-success');

            	// Clear the textfield.
            	self.content().val('');
            	
                self.trigger("create", [html, id]);

            }).fail(function(result) {
                self.message().html(result.message);
                self.message().addClass('alert fade in alert-warning');                
            });
		},

		"{privacyButton} click": function(el) {
			
			setTimeout(function(){
				var isActive = el.find("[data-es-privacy-container]").hasClass("active");
				self.footer().toggleClass("allow-overflow", isActive);
			}, 1);
		}
	}});

	// Resolve module
	module.resolve();
});
