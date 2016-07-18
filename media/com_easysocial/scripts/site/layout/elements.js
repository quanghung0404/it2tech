EasySocial.module('site/layout/elements', function($){

	var module = this;
	var tooltipLoaded = false;

	// Initialize yes/no buttons.
	$(document).on( 'click.button.data-bs-api', '[data-bs-toggle-value]', function() {

		var button = $(this),
			siblings = button.siblings("[data-bs-toggle-value]"),
			parent = button.parents('[data-bs-toggle="radio-buttons"]');

		if(parent.hasClass('disabled')) {
			return;
		}

		// This means that this toggle value belongs to a radio button
		if (parent.length > 0) {

			// Get the current button that's clicked.
			var value = $(this).data('bs-toggle-value');

			button.addClass("active");
			siblings.removeClass("active");

			// Set the value here.
			// Have to manually trigger the change event on the input
			parent.find('input[type=hidden]').val(value).trigger('change');
			return;
		}
	});


	function isMobile() {
	  try{ document.createEvent("TouchEvent"); return true; }
	  catch(e){ return false; }
	}

	// Tooltips
	// detect if mouse is being used or not.
	var mouseCount = 0;
	window.onmousemove = function() {

		mouseCount++;

		addTooltip();
	};

	var addTooltip = $.debounce(function(){

	    if (!tooltipLoaded && mouseCount > 10) {

			tooltipLoaded = true;
			mouseCount = 0;

			$(document).on('mouseover.tooltip.data-es-api', '[data-es-provide=tooltip]', function() {

				$(this)
					.tooltip({
						delay: {
							show: 200,
							hide: 100
						},
						animation: false,
						template: '<div id="fd" class="es tooltip tooltip-es"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
						container: 'body'
					})
					.tooltip("show");
			});
	    } else {
	    	mouseCount = 0;
	    }
	}, 500);

	// TODO: Update to [data-es-provide=tooltip]
	if (! isMobile()) {
		$(document).on('mouseover.tooltip.data-es-api', '[data-es-provide=tooltip]', function() {

			$(this)
				.tooltip({
					delay: {
						show: 200,
						hide: 100
					},
					animation: false,
					template: '<div id="fd" class="es tooltip tooltip-es"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
					container: 'body'
				})
				.tooltip("show");
		});
	}

	// Popovers
	// TODO: Update to [data-es-provide=popover]
	$(document).on('mouseover.popover.data-es-api', '[data-es-provide=popover]', function() {
		$(this)
			.popover({
				delay: {
					show: 200,
					hide: 100
				},
				animation: false,
				trigger: 'hover',
				container: 'body'
			})
			.popover("show");
	});


	var ly = function(yr) { return (yr%400)?((yr%100)?((yr%4)?false:true):false):true; };

	$(document).on("keyup", "[data-date-form] [data-date-day]", function(){

		if (!$.trim($(this).val())) return;

		var year   = parseInt($(this).siblings("[data-date-year]").val()  || $(this).siblings("[data-date-year]").data("dateDefault")),

		    month  = parseInt($(this).siblings("[data-date-month]").val() || $(this).siblings("[data-date-month]").data("dateDefault")),

		    day    = parseInt($(this).val() || $(this).data("dateDefault")),

			maxDay = /1|3|5|7|8|10|12/.test(month) ? 31 : 30;

			if (month==2) maxDay = ly(year) ? 29 : 28;

			if (day < 1) day = 1;

			if (day > maxDay) day = maxDay;

			if ($.isNumeric(day)) {
				$(this).val(day);
			} else {
				$(this).val("");
			}
	});

	$(document).on("keyup", "[data-date-form] [data-date-year]", function(){

		if (!$.trim($(this).val())) return;

		var year = parseInt($(this).val());
		if (year < 1) year = 1;

		if ($.isNumeric(year)) {
			$(this).val(year);
		} else {
			$(this).val("");
		}
	});

	module.resolve();

});
