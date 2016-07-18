void function ( exports, $, _, Backbone ) {

	exports.ES_Events = Object.create(Backbone.Events);

	exports.ES_Slider_Controller = Backbone.View.extend({
		constructor: function ES_Slider_Controller( slider, options ) {
			Backbone.View.call(this, _.extend({ el: slider.el, slider: slider }, options));
		}
	})

	exports.ES_Standard_Controller = ES_Slider_Controller.extend({
		initialize: function ( options ) {

			var slider = options.slider;
			var slides = slider.slides.subViews;
			var autoSlide = slider.model.get('autoSlide.enable');
			//if (slides.length == 1)
			//	return;

			var animation, transition, transition_next, transition_prev;
			var dragOUT, dragIN_next, dragIN_prev, drag_direction;

			// Hide all slides
			_.invoke(slides, 'hide');

			// Listen to slider events
			slider
				.on('change', handleChange)
				.on('pause', handlePause)
				.on('pause:transition', handlePauseTransition)
				.on('resume', handleResume);

			// TODO: disable for 2.0 release. This has some bugs related to drag drop problem
			//slides.length > 1 && slider.$el
			//	.addClass('jsn-es-draggable')
			//	.on('es_dragstart', handleDrag)
			//	.on('es_dragmove', handleDrag)
			//	.on('es_dragstop', handleDrag);

			slider.$slides
				.css('overflow', 'hidden')
				//.css('perspective', '1000px')
				.css('transform-style', 'preserve-3d');

			function stopAnimations() {
				if ( animation ) {
					animation.pause();
					animation = null;
				}
				if ( transition ) {
					transition.end();
					transition = null;
				}
				if ( transition_next ) {
					transition_next.stop();
					transition_next = null;
				}
				if ( transition_prev ) {
					transition_prev.stop();
					transition_prev = null;
				}
			};
			function triggerAnimations( IN ) {
				animation = ES_Tween.progress(IN.duration, function ( progress, ended ) {
					IN.renderAnimation(progress);
					if ( autoSlide && ended ) {
						slider.next();
						animation = null;
					}
				});
			};

			function handlePause() {
				if (animation)
					animation.pause();
				if (transition)
					transition.pause();
			};
			function handlePauseTransition() {
				if (animation) {
					//console.log(animation)
					//console.log('pause')

					//animation.seek(animation.duration - 1).pause();
				}
			};
			function handleResume() {
				if (animation)
					animation.resume();
				if (transition)
					transition.resume();
			};
			function handleChange( index, direction ) {

				var IN = this.getSlideAt(index);
				var OUT = this.getActiveSlide();
				stopAnimations();
				if (IN) {
					IN.prepare();
					slider.setActiveSlide(index);

					if ( OUT && OUT !== IN ) {
						transition = new ES_Transition(IN, OUT, direction)
								.once('complete', function () {
									transition = null;
									OUT.deactivate();
									triggerAnimations(IN);
								});
					}
					else {
						triggerAnimations(IN);
					}
				}

			};
			function handleDrag( e, drag ) {
				switch ( e.type ) {
					case 'es_dragstart':

						stopAnimations();

						dragOUT = slider.getActiveSlide();
						dragIN_next = slider.getNextSlide().prepare();
						dragIN_prev = slider.getPrevSlide().prepare();
						drag_direction = 'none';

						transition_next = new ES_Transition(dragIN_next, dragOUT, 'next')
							.pause()
							.once('complete', function () {
								dragOUT.deactivate();
								dragIN_prev.deactivate();
								stopAnimations();
								triggerAnimations(dragIN_next);
							})
							.once('cancel', function () {
								dragIN_next.deactivate();
								dragIN_prev.deactivate();
								anim = animation;
								stopAnimations();
								if (anim)
									anim.resume();
							});

						transition_prev = new ES_Transition(dragIN_prev, dragOUT, 'prev')
							.pause()
							.once('complete', function () {
								dragOUT.deactivate();
								dragIN_next.deactivate();
								stopAnimations();
								triggerAnimations(dragIN_prev);
							})
							.once('cancel', function () {
								dragIN_next.deactivate();
								dragIN_prev.deactivate();
								anim = animation;
								stopAnimations();
								if (anim)
									anim.resume();
							});

						break; // Case 'es_dragstart'

					case 'es_dragmove':

						if ( drag.axis == 'x' ) {
							e.preventDefault();
							e.stopPropagation();
							if ( slider.model.get('layout.preset') == 'full-screen' )
								window.scrollTo(0, slider.el.offsetTop);
						}
						if ( drag.moveX > 0 )
							drag_direction = 'right';
						else if ( drag.moveX < 0 )
							drag_direction = 'left';
						else
							drag_direction = 'none';

						var progress = Math.min(1, Math.abs(drag.moveX) / slider.stage_width);

						switch ( drag_direction ) {

							case 'right': // Transition to prev
								dragIN_next.hide();
								dragIN_prev.show();
								slider.setActiveSlide(progress > 0.3 ? dragIN_prev.index : dragOUT.index);

								transition_prev &&
								transition_prev.seekPercent(progress);
								break;

							case 'left': // Transition to next
								dragIN_prev.hide();
								dragIN_next.show();
								slider.setActiveSlide(progress > 0.3 ? dragIN_next.index : dragOUT.index);

								transition_next &&
								transition_next.seekPercent(progress);
								break;
						}
						break; // Case 'es_dragmove'

					case 'es_dragstop':

						var progress = Math.min(1, Math.abs(drag.moveX) / slider.stage_width);

						switch ( drag_direction ) {
							case 'right': // Transition to prev
								progress > 0.3 ?
									(transition_prev && transition_prev.resume()) :
									(transition_prev && transition_prev.stop().trigger('cancel'));
								break;
							case 'left': // Transition to next
								progress > 0.3 ?
									(transition_next && transition_next.resume()) :
									(transition_next && transition_next.stop().trigger('cancel'));
								break;
						}

						break; // Case 'es_dragstop'
				}
			};

			function isReverse( IN, OUT, direction ) {
				if ( direction === 'next' )
					return 1;
				if ( direction === 'prev' )
					return -1;
				if ( OUT.index === 0 && IN.index == slides.length - 1 && IN.index != 1 )
					return -1;
				if ( IN.index === 0 && OUT.index == slides.length - 1 )
					return 1;
				return (OUT.index < IN.index) ? 1 : -1;
			};

			function ES_Transition( IN, OUT, direction ) {
				var vector = isReverse(IN, OUT, direction);
				var transition = ( vector == 1 ? OUT : IN ).model.get('transition');
				var effect = transition.get('effect');
				var duration = transition.get('duration');
				var easing = transition.get('easing');
				var timing = ES_Easings[ easing ] ? ES_Easings[ easing ] : ES_Easings.linear;
				var flow = transition.get('flow');
				var width = slider.stage_width;
				var size = flow == 'x' ? slider.stage_width : slider.stage_height;
				var axis = flow;
				var render = _.noop;
				var cleanup = _.noop;

				OUT.$el.add(IN.el)
					.css('transform-origin', '50% 50% 0')
				slider.$stage
					.css('overflow', 'hidden');

				function switchIndex() {
					OUT.el.style.zIndex = 2;
					IN.el.style.zIndex = 1;
				}
				function revertIndex() {
					OUT.el.style.zIndex = 1;
					IN.el.style.zIndex = 2;
				}
				OUT.el.style.zIndex = 2;
				IN.el.style.zIndex = 1;
				OUT.el.style.opacity = 1;
				IN.el.style.opacity = 0; // Set opacity to 0 so the slide remain hidden until animation start

				switch ( effect ) {
					case 'cube':
						revertIndex();
						IN.el.style.opacity = 1;
						OUT.el.style.opacity = 1;
						//slider.$stage.css('perspective', width * 2 + 'px');
						axis = flow == 'x' ? 'y' : 'x';
						render = function ( progress ) {
							render_Cube(IN.el, OUT.el, axis, size, progress, vector);
						}
						break;
					case 'switch':
						//slider.$stage.css('perspective', width * 2 + 'px');
						// IMPORTANT: perspective can not be set in container. this will cause text blurriness
						// on mobile safari.
						axis = flow == 'x' ? 'y' : 'x';
						render = function ( progress ) {
							progress > 0.5 ?
								revertIndex() :
								switchIndex();
							render_Switch(IN.el, OUT.el, axis, size, progress, vector);
						}
						break;
					case 'slide':
						revertIndex();
						render = function ( progress ) {
							render_Move(IN.el, OUT.el, axis, size, size, progress, vector);
						}
						break;
					case 'parallax':
						revertIndex();
						render = function ( progress ) {
							render_Move(IN.el, OUT.el, axis, size, size / 4, progress, vector);
						}
						break;
					case 'slide-over':
						switchIndex();
						switch ( vector ) {
							case 1:
								render = function ( progress ) {
									revertIndex();
									render_Move(IN.el, OUT.el, axis, size, 0, progress, 1);
								};
								break;
							case -1:
								render = function ( progress ) {
									//switchIndex();
									render_Move(IN.el, OUT.el, axis, 0, size, progress, -1);
								};
								break;
						}
						break;
					case 'slide-out':
						revertIndex();
						switch ( vector ) {
							case 1:
								switchIndex();
								render = function ( progress ) {
									render_Move(IN.el, OUT.el, axis, 0, size, progress, -1);
								};
								break;
							case -1:
								//revertIndex();
								render = function ( progress ) {
									render_Move(IN.el, OUT.el, axis, size, 0, progress, 1);
								};
								break;
						}
						break;
					case 'blur':
						revertIndex();
						IN.el.style.opacity = 0;
						cleanup = _.compose(cleanup, function () {
							filter(OUT.el, 'none');
							filter(IN.el, 'none');
						});
						render = function ( progress ) {
							filter(OUT.el, 'blur(' + (progress * 100) + 'px)');
							filter(IN.el, 'blur(' + ((1 - progress) * 100) + 'px)');
							IN.el.style.opacity = progress;
							OUT.el.style.opacity = 1 - progress;
						}
						break;
					default:
						switchIndex();
						render = function ( progress ) {
							OUT.el.style.opacity = 1-progress;
							IN.el.style.opacity = progress;
						}
				}

				cleanup = _.compose(cleanup, function () {
					transform(IN.el, '');
					transform(OUT.el, '');
					IN.el.style.opacity = 1;
					OUT.el.style.opacity = 0;
				});

				return ES_Tween.progress(duration, function ( progress, ended ) {
					render(timing(progress));
				})
					// Only show the in element when transition tween started
					// to provent flicking on slow machines
					.once('start', function() {
						IN.el.style.opacity = 1;
					})
					.once('end', function () {
						cleanup();
						revertIndex();
						this.trigger('complete');
					})
					.once('cancel', function () {
						cleanup();
						revertIndex();
						IN.el.style.opacity = 0;
						OUT.el.style.opacity = 1;
					});
			};
			function render_Move( IN, OUT, axis, distanceIn, distanceOut, progress, vector ) {
				var inValue = (1 - progress) * distanceIn * vector;
				var outValue = progress * -distanceOut * vector;
				switch ( axis ) {
					case 'x':
						transform(IN, 'translate3d(' + inValue + 'px,0,0)');
						transform(OUT, 'translate3d(' + outValue + 'px,0,0)');
						break;
					case 'y':
						transform(IN, 'translate3d(0,' + inValue + 'px,0)');
						transform(OUT, 'translate3d(0,' + outValue + 'px,0)');
						break;
				}
			};
			function render_Cube( IN, OUT, axis, size, progress, vector ) {
				var perspective = size * 2;
				var outRotation = progress * -90 * vector;
				var inRotation = outRotation + (90 * vector);
				var rotationVector = axis == 'y' ? '0,1,0' : '1,0,0';
				var radius = size / 2;
				if ( progress < 0.25 )
					var scale = 1 - (progress * 4 * 0.25)
				else if ( progress < 0.75 )
					var scale = 0.75;
				else
					var scale = 0.75 + (progress - 0.75) * 4 * 0.25;

				transform(OUT, 'perspective('+perspective+'px) scale(' + scale + ')' +
					'translate3d(0,0,-' + radius + 'px)' +
					'rotate3d(' + rotationVector + ',' + outRotation + 'deg)' +
					'translate3d(0,0,' + radius + 'px)');
				transform(IN, 'perspective('+perspective+'px) scale(' + scale + ')' +
					'translate3d(0,0,-' + radius + 'px)' +
					'rotate3d(' + rotationVector + ',' + inRotation + 'deg)' +
					'translate3d(0,0,' + radius + 'px)');
			};
			function render_Switch( IN, OUT, axis, size, progress, vector ) {
				var perspective = size * 2;
				var outRotation = progress * -180 * vector;
				var inRotation = (180 - Math.abs(outRotation)) * vector;
				var rotationVector = axis == 'y' ? '0,1,0' : '1,0,0';
				var radius = size / 2;
				transform(OUT, 'perspective('+perspective+'px) translate3d(0,0,-' + radius + 'px) ' +
					'rotate3d(' + rotationVector + ',' + (outRotation) + 'deg) ' +
					'translate3d(0,0,' + radius + 'px) ' +
					'rotate3d(' + rotationVector + ',' + (-outRotation) + 'deg)');
				transform(IN, 'perspective('+perspective+'px) translate3d(0,0,-' + radius + 'px) ' +
					'rotate3d(' + rotationVector + ',' + (inRotation) + 'deg) ' +
					'translate3d(0,0,' + radius + 'px) ' +
					'rotate3d(' + rotationVector + ',' + (-inRotation) + 'deg)');
			};
		}
	})

	exports.ES_Interactive_Controller = ES_Slider_Controller.extend({
		initialize: function ( options ) {
			var slider = options.slider;
			var autoSlide = slider.model.get('autoSlide.enable');
			//if (slider.model.get('slides').length == 1)
			//	return;

			var direction, velocity, distance, delta, vector, size, totalSize, spacing, animation;
			var activeSlide;
			var preset = slider.model.get('layout.preset');

			if (preset == 'auto-width')
				slider.model.set('layout.auto_w', false);
			else
				slider.model.set('layout.auto_w', true);

			var mode = slider.model.get('layout.mode');
			var flow = slider.model.get('layout.flow');
			var auto_w = slider.model.get('layout.auto_w');
			var auto_h = slider.model.get('layout.auto_h');
			var spacing = slider.model.get('layout.spacing');

			var duration = 1000;
			var multiplier = 1;
			var moving;
			var offset = 0;
			var offsetIndex = 0;
			var offsetFactor = 0;
			var activeIndex = 0;
			var progress = 0;
			var slides = slider.slides.subViews;
			var isCube, $cube, cube = {
				faces: {},
				faceRotation: {
					0: 'front',
					90: 'left',
					180: 'back',
					270: 'right',
					360: 'front'
				}
			};
			var isCarousel, carousel = {};

			slider
				.on('change', change)
				.on('resize', resize)
				.on('ready', ready);

			slider.model.get('interactive.enable') && slider.$el
				.addClass('jsn-es-draggable')
				.on('es_dragstart', handleDrag)
				.on('es_dragmove', handleDrag)
				.on('es_dragstop', handleDrag);

			_.each(slides, function ( slide ) {
				slide.$swiper = slide.$('.jsn-es-swiper');
				slide.$el.before(slide.$swiper);
				slide.$swiper.append(slide.el);
			});

			//_.defer(resize);

			switch ( mode ) {
				case 'cube':
					ES_Cube_Controller(slider);
					break;
				case 'carousel':
					slider.$el.addClass('jsn-es-flat-slider')
					ES_Carousel_Controller(slider);
					break;
				case 'coverflow':
					ES_Coverflow_Controller(slider);
					break;
				case 'polygon':
					ES_Polygon_Controller(slider);
					break;
				default:
					slider.$el.addClass('jsn-es-flat-slider')
					break;
			}

			function ready() {
				handleAnimation();
				this.trigger('change', 1);
				this.defer(function() {
					this.trigger('change', 0);
				});
			}

			function change( index ) {
				if ( animation ) {
					animation.pause();
					animation = null;
				}
				var index = givenIndex = slider.getOffsetIndex(index, 0);
				if ( activeIndex == slides.length - 1 && index == 0 )
					index += slides.length;
				else if ( index == slides.length - 1 && activeIndex == 0 )
					index = -1;
				else if ( index - activeIndex > (slides.length) / 2 )
					index -= slides.length;
				handleTransition((offsetIndex + index - activeIndex) * -size)
			}

			function resize() {
				if ( animation ) {
					animation.pause();
					animation = null;
				}

                size = slider.stage_size = flow == 'x' ?
					(auto_w ? slider.stage_width : slider.width) :
					(auto_h ? slider.stage_height : slider.height);

				switch ( mode ) {
					case 'slide':
						size += spacing;
						break;
				}

				offset = offsetIndex * size;
				totalSize = size * slides.length - 1;

				slider.stageOffset = slider.$stage.offset();
				slider.stageOffset.start = flow == 'x' ? slider.stageOffset.left : slider.stageOffset.top;
				slider.stageOffset.end = flow == 'x' ? slider.stageOffset.left + slider.$stage.width() : slider.stageOffset.top + slider.$stage.height();
				slider.stageOffset.center = flow == 'x' ? slider.stageOffset.left + slider.$stage.width() / 2 : slider.stageOffset.top + slider.$stage.height() / 2;

				requestAnimationFrame(handleFrame);

				_.each(slides, function ( slide ) {
					slide.background.resizeVideo(true)
				})
			}

			function handleDrag( e, data ) {
				if ( data.axis != flow && e.type != 'es_dragstart' )
					return;

				if ( data.axis == flow ) {
					e.preventDefault();
					e.stopPropagation();
				}
				switch ( e.type ) {
					case 'es_dragstart':
						if ( animation ) {
							animation.pause();
							if ( animation.tween && animation.tween.offset )
								offset = Math.round(animation.tween.offset);
							animation = null;
						}
						else {
							offset || (offset = 0)
						}
						requestAnimationFrame(handleFrame)
						break;
					case 'es_dragmove':
						velocity = flow == 'x' ? data.velocityX : data.velocityY;
						distance = flow == 'x' ? data.moveX : data.moveY;
						delta = flow == 'x' ? data.deltaX : data.deltaY;
						direction = data.direction;
						vector = distance < 0 ? -1 : 1;
						offset += delta;
						moving = true;
						requestAnimationFrame(handleFrame)
						break;
					case 'es_dragstop':
						moving = false;
						handleTransition();
						break;
				}
			}

			function handleTransition( toOffset ) {
				var fromOffset = offset;
				var d = Math.max(Math.min(size / 4, 1000), 100)
				var toDuration = duration;
				if ( typeof toOffset !== 'number' ) {
					var throwDistance = Math.max(Math.min(velocity / 2, 10), 1) * d * vector;
					var toOffset = round(offset + throwDistance, size);
					var percent = 1 || Math.round(offset % size - throwDistance / size);
					var toDuration = Math.min(duration, Math.max(1, duration * percent));
				}
				else {
					var toOffset = round(toOffset, size)
				}
				if ( getOffsetIndex(toOffset) !== activeIndex ) {

				}
				animation = ES_Tween(null, {
					from: { offset: fromOffset },
					to: { offset: toOffset },
					duration: toDuration,
					easing: 'easeOutCubic',
				})
					.on('tick', function () {
						if ( !this.tween )
							return;
						offset = Math.round(this.tween.offset);
						moving = true;
						handleFrame();
					})
					.on('end', function () {
						animation = null;
						moving = false;
						handleAnimation();
					})
			}

			function handleFrame() {
				var index = getOffsetIndex(offset, 0)
				if ( index != activeIndex ) {
					activeIndex = index;
					activeSlide = slider.setActiveSlide(index);
					offsetFactor = offset / -size;
					offsetIndex = Math.round(offsetFactor);
					activeSlide.order = 0;
					activeSlide.offsetIndex = offsetIndex;
					activeSlide.offsetStart = offsetIndex * size;
				}
				// Set the translate offset for slides wrapper element
				translate3d(slider.$wrapper, flow, offset);
				var i = 1, a = 1, b = -1;
				var last, next;
				var lastNext = lastPrev = index;
				while ( i < slides.length ) {
					if ( i++ < slides.length ) {
						next = slider.getSlideAt(lastNext = slider.getOffsetIndex(1, lastNext));
						next.order = a++;
						next.offsetIndex = offsetIndex + next.order;
						next.offsetStart = next.offsetIndex * size;
						translate3d(next.$swiper, flow, next.offsetStart);
					}
					if ( i++ < slides.length ) {
						last = slider.getSlideAt(lastPrev = slider.getOffsetIndex(-1, lastPrev));
						last.order = b--;
						last.offsetIndex = offsetIndex + last.order;
						last.offsetStart = last.offsetIndex * size;
						translate3d(last.$swiper, flow, last.offsetStart);
					}
				}

				_.each(slides, function ( slide, i ) {
					slide.offset = slide.$swiper.offset();
					slide.offset.start = flow == 'x' ? slide.offset.left : slide.offset.top;
					slide.offset.end = flow == 'x' ? slide.offset.left + slide.$swiper.width() : slide.offset.top + slide.$swiper.height();
					slide.offset.center = flow == 'x' ? slide.offset.left + slide.$swiper.width() / 2 : slide.offset.top + slide.$swiper.height() / 2;
					//	return log(i, 'outside');
					if ( slide.offset.end > slider.stageOffset.start && slide.offset.center < slider.stageOffset.center ) {
						if ( !slide.onStage ) {
							slide.trigger('transition:enter');
							slide.moveDirection = 'in';
						}
						if ( slide.onStage && slide.onStageSide != 'left' ) {
							//slide.trigger('position', 'left', slide.onStageSide);
							slide.moveDirection = 'out';
						}
						slide.onStage = true;
						slide.onStageSide = 'left';
					}
					else if ( slide.offset.start < slider.stageOffset.end && slide.offset.center > slider.stageOffset.center ) {
						if ( !slide.onStage ) {
							slide.trigger('transition:enter');
							slide.moveDirection = 'in';
						}
						if ( slide.onStage && slide.onStageSide != 'right' ) {
							//slide.trigger('position', 'right', slide.onStageSide);
							slide.moveDirection = 'out';
						}
						slide.onStage = true;
						slide.onStageSide = 'right';
					}
					else if ( slide.offset.start == slider.stageOffset.start || slide.offset.center == slider.stageOffset.center ) {
						if ( slide.onStageSide != 'center' ) {
							slide.trigger('focus');
						}
						slide.onStage = true;
						slide.onStageSide = 'center';
						slide.moveDirection = 'none';
					}
					//if ( slide.offset.start > window.innerWidth || slide.offset.end < 0 )
					else {
						if ( slide.onStage ) {
							slide.trigger('transition:leave');
						}
						slide.onStage = false;
						slide.onStageSide = 'off';
						slide.moveDirection = 'off';
					}
					var transitionProgress = slide.onStageSide == 'left' ?
					(slider.stageOffset.start - slide.offset.start) / (size - spacing) :
					(slider.stageOffset.end - slide.offset.end) / (size - spacing);
					slide.transitionProgress = Math.max(Math.min(Math.abs(transitionProgress), 1), 0);
					//slide.index == 0 && log(slide.offset.start, slider.stageOffset.start,slider.stageOffset.end,slide.transitionProgress.toFixed(2))
					if ( moving ) {
						if ( slide.onStage && slide.onStageSide != 'center' && slide.moveDirection != 'off' ) {
							slide.trigger('transition:' + slide.moveDirection, slide.onStageSide, slide.transitionProgress);
							slide.trigger('transition', slide.moveDirection, slide.onStageSide, slide.transitionProgress);
						}
					}
				});
				slider.trigger('progress', -offset % totalSize / totalSize);
			}

			function handleLayout() {
				if ( !activeSlide )
					return;
				if ( isCarousel ) {
				}
				if ( isCube ) {
					cube.rotation = offset / size * 90;
					var facingAngle = floor((cube.rotation % 360 + 360) % 360, 90);
					var nextAngle = (facingAngle - 90 + 360) % 360;
					var prevAngle = (facingAngle + 90 + 360) % 360;
					var activeFace = cube.faceRotation[ facingAngle ];
					var nextFace = cube.faceRotation[ nextAngle ];
					var prevFace = cube.faceRotation[ prevAngle ];
					//log( prevFace, activeFace, nextFace)
					//log( prevSlide.index, activeSlide.index, nextSlide.index)
					activeSlide.$swiper.show().appendTo(cube.faces[ activeFace ]);
					nextSlide.$swiper.show().appendTo(cube.faces[ nextFace ]);
					prevSlide.$swiper.show().appendTo(cube.faces[ prevFace ]);
					slider.$slides
						.not(activeSlide.$swiper)
						.not(nextSlide.$swiper)
						.not(prevSlide.$swiper)
						.hide();
				}
			}

			function handleTransform() {
				switch ( mode ) {
					case 'slide':
						break;
					case 'cube':
						cube.rotation = Math.round(offset / size * 90);
						rotate3d($cube, flow == 'x' ? 'y' : 'x', cube.rotation);
						break;
					case 'carousel':
						var reverse = 1;
						var hideFaceBeyond = 120;
						var rotation = Math.round(((offset) / size) * 360) * multiplier * reverse;
						var slideRotation = 360 / slides.length;
						var depth = Math.max(size, slider.outer_width - size) * reverse;
						slider.$wrapper.css({
							transform: 'scale3d(1,1,1) translate3d(0,0,' + -depth + 'px)  rotate3d(0,1,0,' + rotation + 'deg)'
						})
						_.each(slides, function ( slide, i ) {
							var centerRotation = slideRotation * i;
							var faceRotation = centerRotation + rotation;
							var ab = Math.abs(faceRotation % 360);
							if ( reverse == -1 )
								faceRotation = 0
							if ( ab < hideFaceBeyond || ab > 360 - hideFaceBeyond || slides.length < 3 )
								slide.$swiper.css({
									transform: 'rotate3d(0,1,0, ' + centerRotation + 'deg) translate3d(0,0,' + depth + 'px) rotate3d(0,1,0, ' + -faceRotation + 'deg)'
								})
							else
								slide.$swiper.css({
									transform: 'scale(0)'
								})
						})
						break;
				}
			}

			function handleAnimation() {
				var active = slider.getActiveSlide();
				var duration = active.duration;

				animation && animation.pause();

				animation = ES_Tween.progress(duration, function ( progress, ended ) {
					autoSlide && ended && slider.next();
				});
			}

			function translate3d( el, axis, value, depth ) {
				switch ( axis ) {
					case 'x':
					case 'X':
						$(el).css('transform', 'translate3d( ' + value + 'px, 0px, 0px )');
						break;
					case 'y':
					case 'Y':
						$(el).css('transform', 'translate3d( 0px, ' + value + 'px, 0px )');
						break;
					case 'z':
					case 'Z':
						$(el).css('transform', 'translate3d( 0px, 0px, ' + value + 'px )');
						break;
				}
			}

			function rotate3d( el, axis, value ) {
				switch ( axis ) {
					case 'x':
					case 'X':
						$(el).css('transform', 'rotate3d(1,0,0,' + cube.rotation + 'deg)');
						break;
					case 'y':
					case 'Y':
						$(el).css('transform', 'rotate3d(0,1,0,' + cube.rotation + 'deg)');
						break;
					case 'z':
					case 'Z':
						$(el).css('transform', 'rotate3d(0,0,1,' + cube.rotation + 'deg)');
						break;
				}
			}

			function getOffsetIndex( toOffset, from ) {
				return slider.getOffsetIndex(Math.round(toOffset / -size), from || 0)
			}

			function getIndexOffset( toIndex, from ) {
				return getOffsetIndex(toIndex, from) * size;
			}
		}
	});

	exports.ES_Cube_Controller = function ( slider ) {

        var flow = slider.model.get('layout.flow');
		var slides = slider.slides.subViews;
		var $cube = $('<div class="jsn-es-cube">').appendTo(slider.$stage)

		slider.$el.addClass('jsn-es-cube-slider');
		slider.$('.jsn-es-viewport').css('overflow', 'visible');
		slider.on('ready', function () {
			slider.getActiveSlide().trigger('transition:enter');
		});
		//slider.on('resize', function () {
		//	slider.setPerspective(slider.stage_width * 2)
		//});
		slider.on('slide:transition:enter', function ( slide ) {
			slide.$el.css('visibility', 'visible');
		});
		slider.on('slide:transition:leave', function ( slide ) {
			if (slide !== slider.activeSlide)
				slide.$el.css('visibility', 'hidden');
		});
		slider.on('slide:transition', function ( slide, direction, side, progress ) {
			var perspective = slider.stage_size * 2;
			var width = slider.stage_width;
			var height = slider.stage_height;
            var size = flow == 'y' ? height : width;
			switch ( direction ) {

				case 'out': // The active slide facing front
					var progress = side == 'left' ? -progress : progress;
					var rotation = (90 * progress);

                    if ( flow == 'x' || flow == 'X') {
                        slide.$el.css('transform', 'perspective(0px) translate3d(0,0,' + (size / 2) + 'px)');
                        $cube.css('transform', 'perspective('+perspective+'px) translate3d(0,0,' + (-size / 2) + 'px) rotate3d(0,1,0,' + rotation + 'deg)');
                    }
                    else if (flow == 'y' || flow == 'Y') {
                        slide.$el.css('transform', 'perspective(0px) translate3d(0,0,' + (size / 2) + 'px)');
                        $cube.css('transform', 'perspective('+perspective+'px) translate3d(0,0,' + (-size / 2) + 'px) rotate3d(1,0,0,' + -rotation + 'deg)');
                    }

					break;

				case 'in': // The next slide facing left or right
					var faceRotation = side == 'left' ? -90 : 90;
					var faceOffsetX = (side == 'left' ? -size : size) / 2;
                    switch (flow) {
                        case 'x':
                        case 'X':
                            slide.$el.css('transform', 'perspective(0px) translate3d(' + faceOffsetX + 'px,0,0) rotate3d(0,1,0,' + faceRotation + 'deg)');
                            break
                        case 'y':
                        case 'Y':
                            slide.$el.css('transform', 'perspective(0px) translate3d(0,' + faceOffsetX + 'px,0) rotate3d(1,0,0,' + -faceRotation + 'deg)');
                            break
                    }

					break;
			}
		});
		slider.on('resize', _.debounce(function () {
			slider.activeSlide.$el.css('visibility', 'visible');
		}, 50));

		_.chain(slides)
			.invoke('trigger', 'transition:leave')
			.pluck('$el')
			.invoke('appendTo', $cube);
	}

	exports.ES_Carousel_Controller = function ( slider ) {

		var radius;
		var slides = slider.slides.subViews;
		var $carousel = $('<div class="jsn-es-carousel">').appendTo(slider.$stage);
        var flow = slider.model.get('layout.flow');

		slider.$el
			.addClass('jsn-es-carousel-slider')
			.css('overflow', 'visible');
		slider.$viewport
			.css('overflow', 'visible')
		_.each(slides, function ( slide, index ) {
			slide.angle = index / slides.length * 360;
			slide.$el.appendTo($carousel);
			slide.load()
		})
		slider.on('resize', function () {
			$carousel.width(slider.stage_width).height(slider.stage_height);
			radius = getOuterRadius(slider.stage_size, slides.length);
		});
		slider.on('progress', function ( progress ) {
			var perspective = slider.stage_size * 2;
			var rotation = slider.rotation = 360 - progress * 360;
            switch ( flow.toLowerCase() ) {
                case 'x':
                    $carousel.css({
                        transform: 'perspective('+perspective+'px) translate3d(0,0,-' + radius + 'px) rotate3d(0,1,0,' + rotation + 'deg)'
                    });
                    _.each(slides, function ( slide ) {
                        slide.$el.css({
                            transform: 'perspective(0px) rotate3d(0,1,0,' + (slide.angle) + 'deg) translate3d(0,0,' + radius + 'px) rotate3d(0,1,0,' + (0 - slide.angle - rotation) + 'deg)',
                            //transform: 'rotate3d(0,1,0,'+ (slide.angle) +'deg) translate3d(0,0,'+radius+'px)',
                        });
                    })
                    break;
                case 'y':
                    $carousel.css({
                        transform: 'perspective('+perspective+'px) translate3d(0,0,-' + radius + 'px) rotate3d(1,0,0,' + -rotation + 'deg)'
                    });
                    _.each(slides, function ( slide ) {
                        slide.$el.css({
                            transform: 'perspective(0px) rotate3d(1,0,0,' + (-slide.angle) + 'deg) translate3d(0,0,' + radius + 'px) rotate3d(1,0,0,' + (slide.angle + rotation) + 'deg)',
                            //transform: 'rotate3d(0,1,0,'+ (slide.angle) +'deg) translate3d(0,0,'+radius+'px)',
                        });
                    })
                    break;
            }

		})

		function getOuterRadius( size, n ) {
			return Math.round(size / 2 / Math.sin(Math.PI / n));
		}

		function getInnerRadius( size, n ) {
			return Math.round(size / 2 / Math.tan(Math.PI / n));
		}
	}

	exports.ES_Polygon_Controller = function ( slider ) {

		var radius;
		var slides = slider.slides.subViews;
		var $carousel = $('<div class="jsn-es-carousel">').appendTo(slider.$stage);

		//slider.model
		//	.set('layout.padding', 0);
		slider.$global.hide();
		slider.$el.addClass('jsn-es-carousel-slider');
		_.each(slides, function ( slide, index ) {
			slide.angle = index / slides.length * 360;
			slide.$el.appendTo($carousel);
			slide.load()
		})
		slider.on('resize', function () {
			$carousel.width(slider.stage_width).height(slider.stage_height);
			radius = getInnerRadius(slider.stage_width, slides.length);
			//slider.setPerspective(slider.stage_width * 2)
		});
		slider.on('progress', function ( progress ) {
			var perspective = slider.stage_size * 2;
			var rotation = slider.rotation = 360 - progress * 360;
			$carousel.css({
				transform: 'perspective('+perspective+'px) translate3d(0,0,-' + radius + 'px) rotate3d(0,1,0,' + rotation + 'deg)'
			});
			_.each(slides, function ( slide ) {
				slide.$el.css({
					transform: 'perspective(0px) rotate3d(0,1,0,' + (slide.angle) + 'deg) translate3d(0,0,' + radius + 'px)',
				});
			})
		})

		function getOuterRadius( size, n ) {
			return Math.round(size / 2 / Math.sin(Math.PI / n));
		}

		function getInnerRadius( size, n ) {
			return Math.round(size / 2 / Math.tan(Math.PI / n));
		}
	}

	exports.ES_Coverflow_Controller = function ( slider ) {

		var slides = slider.slides.subViews;
		var angle = 60;
		var depth = 500;

		//slider.model
		//	.set('layout.padding', 0);
		slider.$viewport.css('perspective-origin', '50% 60%')

		slider.on('ready', function () {

		})
		slider.on('resize', function () {
			//slider.setPerspective(slider.outer_width)
			var perspective = slider.outer_width;
			depth = slider.outer_width;
			slider.$stage.css('transform', 'perspective('+perspective+'px) translate3d(0,0,-' + depth + 'px)')
			slider.getActiveSlide().$el.css('transform', 'perspective(0px) translate3d(0,0,' + depth + 'px)')
			//slider.change(slider.activeIndex)
		});
		slider.on('slide:transition', function ( slide, direction, side, progress ) {
			var perspective = slider.outer_width;
			if ( slider.activeIndex == slide.index ) {
				_.each(slides, function ( otherSlide ) {
					if ( otherSlide.offsetIndex > slide.offsetIndex + 1 )
						otherSlide.$el.css('transform', 'perspective(0px) rotate3d(0,1,0,-' + angle + 'deg)');
					if ( otherSlide.offsetIndex < slide.offsetIndex - 1 )
						otherSlide.$el.css('transform', 'perspective(0px) rotate3d(0,1,0,' + angle + 'deg)');
				})
			}
			var rotation = progress * angle;
			var position = (1 - progress) * depth;
			switch ( side ) {
				case 'left':
					slide.$el.css('transform', 'perspective(0px) translate3d(0,0,' + position + 'px) rotate3d(0,1,0,' + (rotation) + 'deg)');
					break;
				case 'right':
					slide.$el.css('transform', 'perspective(0px) translate3d(0,0,' + position + 'px) rotate3d(0,1,0,' + (-rotation) + 'deg)');
					break;
			}
		});
	}

	exports.ES_Scroll_Controller = function ( slider ) {
		var bounds, height, offset, offsetY, bottomY, ready;
		var parallaxDepth = parseFloat(slider.model.get('background.parallax_depth'));
		slider.on('resize', function () {
			ready = true;
			height = slider.$background.height();
			offset = slider.$background.offset();
			offsetY = offset.top;
			bottomY = offsetY + height;
		})
		slider.listenTo(ES_Events, 'window.scroll', function ( e, data ) {
			if ( !ready )
				return;
			var scrollY = window.scrollY;
			if ( scrollY < offsetY )
				return slider.$background.css('top', 0);
			if ( scrollY > bottomY )
				return slider.$background.css('top', height * 0.5);
			var parallaxOffset = (scrollY - offsetY) * parallaxDepth;
			slider.$background.css('top', parallaxOffset);
		})
	};

	exports.ES_Parallax_Controller = function ( slider ) {
	};

	$(window).scroll(function ( e, data ) {
		requestAnimationFrame(function () {
			ES_Events.trigger('window.scroll', e, data)
		})
	});

	//var log = log.bind(console)

	var transformKey, transformKeys = [ "transform", "msTransform", "webkitTransform", "mozTransform", "oTransform" ];
	var filterKey, filterKeys = [ "filter", "msFilter", "webkitFilter", "mozFilter", "oFilter" ];

	function transform( el, value ) {
		if ( !transformKey && document.body && document.body.style ) {
			while ( !transformKey && transformKeys.length ) {
				var key = transformKeys.pop();
				if ( typeof document.body.style[ key ] !== 'undefined' )
					transformKey = key;
			}
		}
		el.style[ transformKey ] = value;
	}

	function filter( el, value ) {
		if ( !filterKey && document.body && document.body.style ) {
			while ( !filterKey && filterKeys.length ) {
				var key = filterKeys.pop();
				if ( typeof document.body.style[ key ] !== 'undefined' )
					filterKey = key;
			}
		}
		el.style[ filterKey ] = value;
	}

	function round( n, closest ) {
		_.isUndefined(closest) && (closest = 1);
		return Math.round(n / closest) * closest;
	};
	function floor( n, closest ) {
		_.isUndefined(closest) && (closest = 1);
		return Math.floor(n / closest) * closest;
	};
	function ceil( n, closest ) {
		_.isUndefined(closest) && (closest = 1);
		return Math.ceil(n / closest) * closest;
	};

}(this, jQuery, _, JSNES_Backbone)