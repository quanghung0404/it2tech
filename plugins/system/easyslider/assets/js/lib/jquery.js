void function ( exports, $, _, Backbone ) {

	$("body").on("click", "[type=toggle]", function ( e ) {
		e.preventDefault();
		$(this).attr('data-on', $(this).attr('data-on') == "true" ? false : true).trigger('change');
	});

	$.fn.getBoundingBox = function () {
		var top = 0, left = 0, right = 0, bottom = 0, width = 0, height = 0;
		var rects = this.map(function () {
			return this.getBoundingClientRect()
		}).toArray();
		if (rects.length) {
			top = Math.min.apply(null, _.pluck(rects, 'top'));
			left = Math.min.apply(null, _.pluck(rects, 'left'));
			right = Math.max.apply(null, _.pluck(rects, 'right'));
			bottom = Math.max.apply(null, _.pluck(rects, 'bottom'));
			height = bottom - top;
			width = right - left;
		}
		return {
			top: top,
			left: left,
			width: width,
			height: height
		};
	};
	$.getImageThumb = function( src, width, height, callback ) {
		if (!src)
			return;
		var canvas = document.createElement('canvas');

		canvas.width = width;
		canvas.height = height || width;

		var context = ccc=canvas.getContext('2d');
		var imageObj = new Image();

		imageObj.onload = function() {
			// draw cropped image

			var sourceWidth = this.width;
			var sourceHeight = this.height;

			var destWidth = canvas.width;
			var destHeight = canvas.height;

			var destX = 0.5;
			var destY = 0.5;

			var sourceRatio = sourceWidth / sourceHeight;
			var destRatio = destWidth / destHeight;

			if (sourceRatio > destRatio) {
				destWidth *= destRatio;
			}
			else {
				destHeight *= destRatio;
			}
			destX = canvas.width/2 - destWidth/2 + 0.5;
			destY = canvas.height/2 - destHeight/2 + 0.5;

			context.drawImage(imageObj, 0, 0, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight);
			callback && callback(canvas.toDataURL());
		};
		imageObj.onerror = function() {
			//console.log('error', src)
		}
		imageObj.crossOrigin = 'anonymous';
		imageObj.src = src;
	}

	$.fn.clickOutside = function ( callback, context ) {
		return typeof callback != 'function' ? this : this.each(function ( index, element ) {
			$(window).on('mousedown', function clickHandler ( e ) {
				if ( !$(element).is(e.target) && !$(e.target).parents('.' + $(element).attr('class').replace(/\s+/g, '.')).length )
					$(window).off('mousedown', clickHandler),
						callback.call(context, e);
			});
		});
	}
	$.fn.selectText = function () {
		var range, selection;
		return this.each(function () {
			if ( document.body.createTextRange ) {
				range = document.body.createTextRange();
				range.moveToElementText(this);
				range.select();
			} else if ( window.getSelection ) {
				selection = window.getSelection();
				range = document.createRange();
				range.selectNodeContents(this);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		});
	};
	$.fn.deselectText = function () {
		var selection;
		return this.each(function () {
			if ( window.getSelection ) {
				if ( window.getSelection().empty ) {  // Chrome
					window.getSelection().empty();
				} else if ( window.getSelection().removeAllRanges ) {  // Firefox
					window.getSelection().removeAllRanges();
				}
			} else if ( document.selection ) {  // IE?
				document.selection.empty();
			}
		});
	};
	$.fn.setCaretPosition = function ( pos ) {
		var range;
		return this.each(function () {
			var elem = this;
			if ( elem.createTextRange ) {
				range = elem.createTextRange();
				range.move('character', pos);
				range.select();
			} else {
				elem.focus();
				if ( typeof elem.selectionStart !== 'undefined' ) {
					elem.setSelectionRange(pos, pos);
				}
			}
		});
	}
	$.fn.getCaretPosition = function () {
		if ( !this.length )
			return null;
		var input = this.get(0);
		if ( !input ) return; // No (input) element found
		if ( document.selection ) {
			// IE
			input.focus();
		}
		return 'selectionStart' in input ? input.selectionStart : '' || Math.abs(document.selection.createRange().moveStart('character', -input.value.length));
	}
	$.fn.ES_WrapLetters = function( before, after ) {
		return this.each( function() {
			$( this ).html( $( this ).text().replace( /(<[^>]*>)?([^<]*)(<[^>]*>)?/g, function( string, x, text, y ) {
				return _(!text.trim() ? '' : (x || '') + text.trim().split( '' )).map( function( letter ) {
						return before + letter + after;
					} ).join( '' ) + (y || '');
			} ) );
		} );
	};
	$.fn.ES_WrapWords = function( before, after ) {
		return this.each( function() {
			$( this ).html( $( this ).text().replace( /(<[^>]*>)?([^<]*)(<[^>]*>)?/g, function( string, x, text, y ) {
				return _(!text.trim() ? '' : (x || '') + text.trim().split( /\s+/ )).map( function( word ) {
						return before + word + after;
					} ).join( ' ' ) + (y || '');
			} ) );
		} );
	};

}(this, jQuery, _, Backbone);

void function ( exports, $, _, Backbone ) {

	function setCaretPosition ( elem, pos ) {
		var range;
		if ( elem.createTextRange ) {
			range = elem.createTextRange();
			range.move('character', pos);
			range.select();
		} else {
			elem.focus();
			if ( typeof elem.selectionStart !== 'undefined' ) {
				elem.setSelectionRange(pos, pos);
			}
		}
	}

	function getCaretPosition ( input ) {
		if ( document.selection ) {
			// IE
			input.focus();
		}
		return 'selectionStart' in input ? input.selectionStart : '' || Math.abs(document.selection.createRange().moveStart('character', -input.value.length));
	}

	function parseNumberUnit ( str ) {
		str += '';
		var match = str.replace(/\s+/, ' ').match(/([-0-9.]+)\s?([^\s]*)?/);
		return match && {
				number: parseFloat(match[ 1 ]),
				unit: match[ 2 ]
			}
	}

	function spinValue ( $el, direction, page ) {
		var value = $el.val();
		var min = parseFloat($el.attr('min'));
		var max = parseFloat($el.attr('max'));
		var step = Math.abs($el.attr('step') || 1);
		var decimal = parseInt($el.attr('decimal'));
		if ( !decimal ) {
			decimal = step == step.toFixed() ? 0 : step.toString().split('.')[ 1 ].length;
		}

		var parsedValue = parseNumberUnit(value);
		if ( !parsedValue ) {
			parsedValue = {
				num: 0, unit: ''
			}
		}

		var num = parsedValue.number || 0;
		var unit = parsedValue.unit || $el.attr('unit') || '';
		if ( typeof num !== 'number' ) return;

		!page || (step *= 10);

		switch ( direction ) {
			case 'up':
				num += step;
				break;
			case 'down':
				num -= step;
				break;
		}
		if ( direction == 'down' && min !== NaN && num < min )
			num = min;
		if ( direction == 'up' && max !== NaN && num > max )
			num = max;
		!decimal || (num = num.toFixed(decimal))
		!unit || (num += unit);
		$el.val(num).trigger('change');
	}

	$.fn.ES_NumberInput = function () {
		return this.each(function () {
			var $el = $(this);
			var $parent = $el.parent();
			var $stepper = $el.next('.stepper');

			if ( !$stepper.length ) {
				$stepper = $('<span class="input-group-addon stepper"><a class="up"></a><a class="down"></a></span>');
			}
			if ( !$parent.is('.input-group') ) {
				$parent = $('<div class="input-group">');
				$el.before($parent);
			}

			$parent.addClass('input-number-wrapper')
				.on('mousedown', '.up', function ( e ) {
					e.preventDefault();
					spinValue($el, 'up');
				})
				.on('mousedown', '.down', function ( e ) {
					e.preventDefault();
					spinValue($el, 'down')
				});

			$el.addClass('input-number')
				.attr('type', 'text')
				.attr('spellcheck', 'false')
				.attr('autocomplete', 'off')
				.on('keydown', function ( e ) {
					var caretPos = getCaretPosition(this);
					switch ( e.keyCode ) {
						case 38:
							spinValue($el, 'up', e.shiftKey);
							break;
						case 40:
							spinValue($el, 'down', e.shiftKey);
							break;
					}
					if ( e.keyCode == 38 || e.keyCode == 40 ) {
						e.preventDefault();
						setCaretPosition(this, caretPos);
					}
				});

			$el.appendTo($parent);
			$el.after($stepper);
		});
	};

	$.fn.ES_ColorInput = function () {
		return this.each(function () {
			var $el = $(this);
			var $parent = $('<div class="input-group input-color-wrapper">');
			var $remove = $('<span class="input-group-btn remove-color"><a class="btn btn-default btn-sm"><span class="fa fa-close"></span></a></span>');

			$remove.on('click', function ( e ) {
				e.preventDefault();
				$el.val('').trigger('change:color');
			});

			$el.before($parent)
				.appendTo($parent)
				.after($remove);
		});
	};

	$.fn.ES_ImageInput = function () {
		return this.each(function () {
			var $input = $(this);
			var $parent = $('<div class="input-group input-image-wrapper">');
			var $select = $('<span class="input-group-btn"><a class="btn btn-default btn-sm input-image-select-btn"><span class="fa fa-folder-open"></span></a></span>');
			var $upload = $('<span class="input-group-btn"><a class="btn btn-default btn-sm input-image-upload-btn"><span class="fa fa-upload"></span></a></span>');
			var $clear = $('<span class="input-group-btn"><a class="btn btn-default btn-sm input-image-clear-btn"><span class="fa fa-close"></span></a></span>');

			$clear.click(function () {
				$input.val() && $input.val('').trigger('change');
			});
			$select.click(function () {
				$.ES_MediaSelector(function ( url ) {
					$input.val(url).trigger('change');
				});
			});
			$input
				.before($parent)
				.appendTo($parent)
				.after($clear)
				//.after($upload)
				.after($select);
		});
	};

	$.fn.ES_VideoInput = function () {

	};

	$.fn.ES_SelectBox = function () {

	};

	$.ES_Prompt = function ( message, callback ) {
		var reponse = prompt(message);
		typeof callback == 'function' && callback(reponse);
	};

	$.ES_MediaSelector = function ( callback ) {

		window.jInsertEditorText = function ( value ) {
			var regex = /<img.*?src="(.*?)"/;
			var url = regex.exec(value)[ 1 ];
			typeof callback == 'function' && callback(url, { value: value });
		};

		window.jModalClose = function () {
			$('.es-media-selector').addClass('hidden');
		}

		var $iframe = $('.es-media-selector iframe');
		if ( !$iframe.prop('src') )
			$iframe.prop('src', 'index.php?option=com_media&view=images&tmpl=component');
		else {
			$($iframe.get(0).contentDocument)
				.find('#system-message-container')
				.remove()
				.trigger('load');
		}
		$iframe
			.off('load')
			.on('load', function () {
				var frameHeight = Math.min(window.innerHeight, this.contentDocument.body.scrollHeight);
				var maxHeight = (frameHeight == this.contentDocument.body.scrollHeight);
				$(this.contentDocument.body)
					.css({
						'margin': '0',
						'padding': '10px',
						'overflow': maxHeight ? 'hidden' : 'scroll',
						'box-sizing': 'border-box'
					});
				$('.es-media-selector')
					.css('height', frameHeight + 'px')
			});

		$('.es-media-selector')
			.removeClass('hidden')
			.clickOutside(window.jModalClose);
	};

}(this, jQuery, _, Backbone);