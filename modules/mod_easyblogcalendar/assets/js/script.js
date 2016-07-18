/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

EasyBlog.ready(function($) {

	(function()
	{

	var defaultOptions = {
		my: 'center bottom',
		at: 'center top',
		show: {
			when: 'mouseenter',
			delay: 0
		},
		hide: {
			when: 'mouseleave',
			delay: 0
		},
		offset: 0,
		collision: 'flip flip',
		queue: ''
	};

	/* Internal function */
	var _tooltip = {
		initialize: function()
		{
			// $('.stackTip:has(> script[type="text/x-json"])').stackTip();

			// TODO: Another way to determine uninitialized tooltip
			$('.stackTip').stackTip();
		},
		attach: function(options)
		{
			// Overwrite default options with given options
			var options = $.extend({}, defaultOptions, options);

			var tipIsFocused= false;
			options.tooltip
				.bind('mouseover', function()
				{
					tipIsFocused = true;
				})
				.bind('mouseout', function()
				{
					tipIsFocused = false;

					options.queue = setTimeout(
						function(){
							options.of.trigger(options.hide.when);
						}, 100);
				});

			// Attach tooltip
			options.of
				.bind(options.show.when, function()
				{
					clearTimeout(options.queue);

					options.queue = setTimeout(
						function()
						{
							options.tooltip
								.show(0, function()
								{
									options.tooltip.position(options);
								});

						}, options.show.delay);
				})
				.bind(options.hide.when, function()
				{
					setTimeout(
						function()
						{
							if (tipIsFocused) return;

							clearTimeout(options.queue);

							options.queue = setTimeout(
								function()
								{
									options.tooltip
										.hide();
								}, options.hide.delay);
						}, 100);
				});
		}
	}

	$.fn.stackTip = function(_options)
	{
		this.each(function()
		{
			var tooltip = $(this);

			// Perform sanity checks on tooltip options before attaching tooltip to it
			var options = _options;

			// If options are not given,
			if (!options)
			{
				// try to retrieve tooltip options embedded in data tag.
				options = tooltip.data('options');

				// TODO: Reevaluate logic
				if (!options) {
					var script = tooltip.find('> script[type="text/x-json"]');

					// try to retrieve tooltip options embedded in script tag.
					try { options = eval(script.html()); } catch(err) {};

					// If embedded options is still invalid, exit.
					if (!options) return;

					// Remove tooltip options embedded in script tag.
					script.remove();
				}
			}

			// If tooltip's target is not given, exit.
			if (!options.of) return;

			// Assume jQuery element
			var target = options.of;
			if (!target.jquery)
			{

				// else, assume traversal function
				if (target.traverseUsing)
				{
					target = tooltip[target.traverseUsing].call(tooltip, target.withFilter);
				// else, assume selector
				} else {
					target = $(target);
				}
			}

			// If tooltip target not found, exit;
			if (target.length < 0) return;

			// Reduce target to one and attach it back to options.of
			options.of = target.first();

			// Move tooltip container to end of body
			tooltip.appendTo('body');

			// Add reference to tooltip in options
			options.tooltip = tooltip;

			// Attach tooltip to target
			_tooltip.attach(options);
		});
	};

	/*
	.hasTooltip exists only when new html (usually loaded through
	ajax) is inserted into the document.

	.hasTooltip class indicates that this target has tooltip
	that has not been attached.

	When a .hasTooltip is discovered, _tooltip.initialize()
	is called again to search for all other new targets where
	tooltip hasn't been attached to yet.

	Targets where tooltip has been attached will have
	this classname is removed.
	*/

	$(document).on("mouseover", '.hasTooltip', function(){
		_tooltip.initialize();
		$('.hasTooltip').removeClass('hasTooltip');
	});

	$(document).ready(function()
	{
		_tooltip.initialize();
	});

	})();

	window.mod_easyblogcalendar = {
		itemId: '0',
		calendar: {
			reload: function(view, func, position, itemId, size, type, timestamp) {
			    if( itemId == '0')
			        itemId = mod_easyblogcalendar.itemId;

				$('#easyblogcalendar-module-wrapper').html('<div style="text-align:center;"><img src="'+siteurl+'/components/com_easyblog/assets/images/loader.gif" /></div>');
				ejax.load( view , func, position, itemId, size, type, timestamp);
			},

			//show the tooltips.
			showtooltips : function(id)
			{
				$('.easyblog_calendar_tooltips').hide();
				$('#mod_easyblog_calendar_day_'+id).show();
			},

			setItemId: function ( myId ) {
			    if( myId != 'undefined' && myId != '')
			    {
	            	mod_easyblogcalendar.itemId = myId;
	            }
			}
		}

	}
});
