EasySocial.module('validate', function ($) {
/*
<div data-check>
	<div>
		<label>Text</label>
		<input data-check-type="text" data-check-field />
	</div>
	<div data-check-notice></div>
</div>
<div data-check>
	<div>
		<label>Checkboxes</label>
		<div data-check-type="checkbox" data-check-field data-check-required>
			<input type="checkbox" name="group[]" value="1" />
			<input type="checkbox" name="group[]" value="1" />
		</div>
	</div>
</div>
<div data-check>
	<div>
		<label>Validate format</label>
		<input data-check-field data-check-validate="regex" />
	</div>
	<div data-check-notice></div>
</div>
*/

	var module = this;

	if(!$.isController('EasySocial.Controller.Validator')) {
		EasySocial.Controller('Validator', {
			defaultOptions: {
				mode			: null,

				checks			: ['required', 'validate'],

				typeAttr		: 'data-check-type',
				formatAttr		: 'data-check-format',
				modifierAttr	: 'data-check-modifier',

				errorTrigger	: 'onError',
				submitTrigger	: 'onSubmit',

				'{container}'	: '[data-check]',
				'{notice}'		: '[data-check-notice]',

				'{required}'	: '[data-check-required]',
				'{validate}'	: '[data-check-validate]',
			}
		}, function(self) {
			return {
				// temporary variables
				vars		: {},

				// register of elements returned by fields
				register	: [],

				// deferreds return by elements
				deferreds	: [],

				// errors return by elements
				errors		: [],

				// state of validator
				state		: $.Deferred(),

				init: function () {

				},

				reset: function() {
					self.vars		= {};

					self.register	= [];
					self.deferreds	= [];
					self.errors		= [];

					self.state		= $.Deferred();

					self.container().removeClass('error');
				},

				start: function() {
					self.reset();

					$.each(self.container(), function(i, container) {
						self.vars.container = container = $(container);

						container.trigger(self.options.submitTrigger, [self.register, self.options.mode]);

						$.each(self.getFields(), function(j, field) {
							self.vars.field = field = $(field);

							$.each(self.options.checks, function(i, check) {
								self.vars.check = check;

								self[check + 'Check']();
							});
						});
					});

					$.each(self.register, function(i, result) {
						if($.isDeferred(result)) {
							self.deferreds.push(result);
						} else if($.isPlainObject(result)) {
							$.each(result, function(key, value) {
								if(value === false) {
									self.errors.push(i);
									return true;
								}
							})
						} else {
							if(result === false) {
								self.errors.push(i);
								return true;
							}
						}
					});

					// If have static errors, then reject state
					if(self.errors.length > 0) {
						self.state.reject();
					} else {
						// If no static errors, then check if have deferreds
						if(self.deferreds.length > 0) {
							// This is because $.when accepts n amount of parameters instead of array, so we use .apply to pass in the array
							$.when.apply(null, self.deferreds)
								.done(function() {
									self.state.resolve();
								})
								.fail(function() {
									self.state.reject();
								});
						} else {
							// If no deferreds, then just resolve
							self.state.resolve();
						}
					}

					return self.state;
				},

				getFields: function() {
					return $.merge(self.vars.container.find(self.required.selector), self.vars.container.find(self.validate.selector));
				},

				requiredCheck: function() {
					if(self.vars.field.is(self.required.selector)) {
						var fieldType = self.vars.field.attr(self.options.typeAttr) || self.vars.field.attr('type') || 'text';

						if(fieldType === 'text' && $.trim(self.vars.field.val()) == '' ) {
							self.raiseError();
						}

						if(fieldType === 'checkbox' && self.vars.field.find('input[type="checkbox"]').filter(':checked').length < 1) {
							self.raiseError();
						}
					}
				},

				validateCheck: function() {
					if(self.vars.field.attr(self.options.formatAttr) !== undefined) {

						var value = self.vars.field.val();

						if($.isEmpty(value)) {
							return;
						}

						var format = self.vars.field.attr(self.options.formatAttr) || '';

						var modifier = self.vars.field.attr(self.options.modifierAttr) || '';

						var regex = new RegExp(format, modifier);

						if(!regex.test(value)) {
							self.raiseError();
						}
					}
				},

				raiseError: function () {
					self.vars.container.addClass('has-error');

					self.vars.container.trigger(self.options.errorTrigger, [self.vars.check, self.vars.field]);

					self.register.push(false);
				}
			};
		});
	}

	$.fn.validate = function(options){
		var element = this;

		if(element.length > 0) {
			var controller = this.addController("EasySocial.Controller.Validator", options);
			return controller.start();
		}

		return false;
	};

	module.resolve();
});
