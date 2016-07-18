EasyBlog.module("authors", function($) {

var module = this;

// require: start
EasyBlog.require()
	.done(function($){

	// controller: start

	EasyBlog.Controller('Authors.Item', {
		defaultOptions: {
			"{subscribe}" : "[data-author-subscribe]",
			"{unsubscribe}" : "[data-author-unsubscribe]"
		}
	}, function(self) {
		return {
			init: function()
			{
				self.options.id = self.element.data('id');
			},

			"{subscribe} click" : function()
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/form', {
						"id" : self.options.id,
						"type" : "blogger"
					})
				});
			},
			"{unsubscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/unsubscribe', {
						"id" : self.options.id,
						"type" : "blogger",
						"email" : $(el).data('email')
					})
				});
			}
		}
	});

	EasyBlog.Controller('Authors.Listing', {
		defaultOptions: {
			"{item}" : "[data-author-item]"
		}
	}, function(self) {
		return {

			init: function()
			{
				self.implementAuthor();
			},

			implementAuthor: function()
			{
				self.item().implement(EasyBlog.Controller.Authors.Listing.Item);
			}
		}
	});

	EasyBlog.Controller('Authors.Listing.Item', {
		defaultOptions: {

			"{feature}" : "[data-author-feature]",
			"{unfeature}" : "[data-author-unfeature]",
			"{featuredTag}" : "[data-featured-tag]",
			"{subscribe}" : "[data-author-subscribe]",
			"{unsubscribe}" : "[data-author-unsubscribe]"
		}
	}, function(self) {
		return {

			init: function()
			{
				self.options.id = self.element.data('id');
			},
			featureItem: function()
			{
				EasyBlog.ajax('site/views/featured/makeFeatured', {
					"type" : "blogger",
					"id": self.options.id
				}).done(function(){
					// Switch the button
					self.feature().addClass('hide');
					self.unfeature().removeClass('hide');

					// Display the star icon
					self.featuredTag().removeClass('hide');
				});
			},

			"{subscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/form', {
						"id" : self.options.id,
						"type" : "blogger"
					})
				});
			},
			"{unsubscribe} click" : function(el, event)
			{
				EasyBlog.dialog({
					content: EasyBlog.ajax('site/views/subscription/unsubscribe', {
						"id" : self.options.id,
						"type" : "blogger",
						"email" : $(el).data('email')
					})
				});
			},

			"{feature} click" : function(el, event)
			{
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/confirm', {
						"type": "blogger",
						"id": self.options.id
					}),
					bindings: {
						"{submitButton} click" : function()
						{
							self.featureItem();

							// Hide dialog now
							EasyBlog.dialog().close();
						}
					}
				});
			},
			"{unfeature} click" : function(el, event)
			{
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/removeFeatured', {
						"type": "blogger",
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							self.unfeature().addClass('hide');
							self.feature().removeClass('hide');

							self.featuredTag().addClass('hide');

							EasyBlog.dialog().close();
						}
					}
				});
			}
		}
	});

	module.resolve();

	// controller: end

	});

	// require: end
});
