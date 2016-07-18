
EasySocial.module('utilities/alias', function($) {

	var module = this;

	EasySocial.Controller('Utilities.Alias', {
			defaultOptions: {
				// Should be overriden by the caller.
				"{target}"	: "",
				"{source}"	: ""
			}
		},function(self) { 
			return {
				init: function() {
				},

				convertToPermalink: function(title) {
					return title.replace(/\s/g, '-').replace(/[^\w/-]/g, '').toLowerCase();
				},

				"{source} keyup" : function(input, event) {
						
					var permalink = self.convertToPermalink(self.source().val());

					// Update the target when the source is change.
					self.target()
						.val(permalink);
				},

				"{target} keyup" : function(input, event) {

					var permalink = self.convertToPermalink(self.target().val());

					self.target()
						.val(permalink);
				}
			}
		});

	module.resolve();
});
