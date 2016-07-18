/**
 * Created by phong on 8/28/15.
 */

void function ( exports, $, _, Backbone ) {

	var ES_Timer, ES_Tween, ES_Timeline;
	var easings = ES_Easings;
	var now;

	ES_Timer = exports.ES_Timer = function ES_Timer( duration ) {

		if ( !(this instanceof ES_Timer) )
			return new ES_Timer(duration)

		_.bindAll(this, '_requestFrame', '_cancelFrame', '_renderFrame');

		this.duration = duration;
		this.reverse = false;
		this._lapsed = 0;

		this._started = false;
		this._running = false;
		this._paused = false;
		this._ended = false;

		this._startTime = 0;
		this._pauseTime = 0;

		return this
	}
	_(ES_Timer).setPrototypeOf(Backbone.Events)
	_(ES_Timer).extendPrototype({

		_requestFrame: function () {
			if ( this._ended )
				return;
			this.frameID = window.requestAnimationFrame(this._renderFrame);
		},
		_cancelFrame: function () {
			window.cancelAnimationFrame(this.frameID);
		},
		_renderFrame: function () {
			now = _.now();
			this._lapsed = now - this._startTime;

			// When timer reach its duration
			if ( this.duration && this._lapsed > this.duration ) {
				this.end();
			}
			else {
				this._tick()
				this._requestFrame()
			}
		},
		_tick: function ( options ) {
			this.trigger('tick', this.currentTime(), options || {})
		},

		currentTime: function () {
			return Math.max(this.reverse ? this.duration - this._lapsed : this._lapsed, 0);
		},

		seek: function ( time, options ) {
			var self = this;
			time = parseInt(time);
			self._startTime -= (time - self._lapsed);
			self._lapsed = time;

			if ( self._lapsed >= self.duration ) {
				this.frameID = window.requestAnimationFrame(function () {
					self.end();
					self.trigger('seek')
				})
			}
			//else if ( self._lapsed <= 0 ) {
			//	this.frameID = window.requestAnimationFrame(function () {
			//		self.stop().start().pause();
			//		self.trigger('seek')
			//	})
			//}
			else {
				self._started = true;
				self._ended = false;
				self._paused = self._running ? false : true;
				self._cancelFrame()
				this.frameID = window.requestAnimationFrame(function () {
					self._tick(options)
					self.trigger('seek')
				})
			}
			return this;
		},
		start: function () {
			if ( this._started )
				this.stop();
			this._started = true;
			this._running = true;
			this._ended = false;
			this._startTime = _.now()
			this._requestFrame()
			this._tick()
			this.trigger('start')
			return this;
		},
		pause: function () {
			if ( !this._started || !this._running || this._ended )
				return this;
			this._running = false;
			this._paused = true;
			this._pauseTime = _.now();
			this._lapsed = Math.max(this._pauseTime - this._startTime, 0);
			this._cancelFrame()
			//this._tick()
			this.trigger('pause')
			return this
		},
		resume: function () {
			if ( !this._started || !this._paused || this._ended )
				return this;
			this._startTime += _.now() - this._pauseTime;
			this._paused = false;
			this._running = true;
			this._requestFrame();
			this.trigger('resume')
			return this
		},
		stop: function () {
			if ( !this._started )
				return this;
			this._started = false;
			this._running = false;
			this._paused = false;
			this._ended = false;
			this._lapsed = 0;
			this._cancelFrame()
			this._tick()
			this.trigger('stop')
			return this;
		},
		end: function () {
			this._cancelFrame();
			this._started = false;
			this._running = false;
			this._paused = false;
			this._ended = true;
			this._lapsed = this.duration;
			//this._tick()
			this.trigger('end');
			return this;
		}
	})

	ES_Tween = exports.ES_Tween = function ES_Tween( selector, options ) {

		if ( !(this instanceof ES_Tween) )
			return new ES_Tween(selector, options)

		this.$el = $(selector);
		var existingTween = this.$el.data('es-tween-data');
		var options = options || {};
		if ( existingTween && existingTween.tween ) {
			if ( !options.from )
				options.from = existingTween.tween;
			if ( !options.to )
				options.to = existingTween.tween;
		}
		this._setOptions(options)
		this._setTimer(new ES_Timer)
		this.options.from = parseTweenProps(this.options.from);
		this.options.to = parseTweenProps(this.options.to);
		mapTweenObject(this.options.from, this.options.to)

		if ( !this.parent && !this.options.paused )
			_.defer(_.bind(this.start, this))
		return this
	}
	_(ES_Tween).setPrototypeOf(Backbone.Events)
	_(ES_Tween).extendPrototype({

		_defaultOptions: {
			from: {},
			to: {},
			loop: 0,
			delay: 0,
			duration: 1000,
			easing: 'linear'
		},
		_setOptions: function ( options ) {
			this.options = _.defaults({}, options, this._defaultOptions);
			_([ 'duration', 'delay', 'easing' ]).each(function ( key ) {
				Object.defineProperty(this, key, {
					get: function () {
						return this.options[ key ];
					},
					set: function ( value ) {
						this.options[ key ] = value;
						if ( this.timer )
							switch ( key ) {
								case 'delay':
								case 'duration':
									this.timer.duration = this.options.delay + this.options.duration;
									this.parent && this.parent.refreshDuration();
									break;
							}
					}
				})
			}, this)
		},
		_setTimer: function ( timer ) {
			this._clearTimer()
			this.timer = timer;
			this.timer.duration = this.delay + this.duration;
			this.on('tick', function ( time, options ) {
				if ( options.render !== false )
					this._render(time)
				this.parent && this.parent.seek(time, _.extend({ render: false }, options))
			})
			this.on('end', function () {
				this._render(this.timer._lapsed);
				if ( this._loopCount == -1 )
					this.play()
				else {
					if ( this._loopCount && --this._loopCount > 0 )
						this.play()
					else
						this._looping = false;
				}
			})
			this.listenTo(this.timer, 'all', this.trigger)
		},
		_clearTimer: function () {
			if ( this.timer )
				this.stopListening(this.timer)
			delete this.timer
		},
		_render: function ( time ) {
			if ( this.tweens ) {
				//_.invoke(this.tweens, '_render', time + this.delay)
				_.invoke(this.tweens, '_render', time - this.delay)
				return;
			}
			if ( time <= this.delay ) {
				//if ( this.phase == 'waiting' )
				//	return;
				this.phase = 'waiting';
				var tween = this.options.from;
			}
			else if ( time >= this.delay + this.duration ) {
				if ( this.phase == 'ended' )
					return;
				this.phase = 'ended';
				var tween = this.options.to;
			}
			else {
				if ( this.phase != 'tweening' ) {
					this.phase = 'tweening';
				}
				var a = this.options.from;
				var b = this.options.to;
				var c = time - this.delay;
				var d = this.duration;
				var e = this.easing;
				var tween = getTweenObject(a, _.omit(b, 'opacity'), c, d, e);
				_.extend(tween, getTweenObject(a, _.pick(b, 'opacity'), c, d, 'linear'))
			}
			if ( tween ) {
				this.tween = tween;
				this.$el.each(function () {
					this.style.opacity = tween.opacity;
					transform(this, toTransformCSS(tween));
				})
				this.trigger('render', tween)
			}
			return this;
		},

		set: function ( type, props ) {
			this.options[ type ] = parseTweenProps(props)
		},

		end: function () {
			this.timer.end();
			return this;
		},
		start: function () {
			if ( !this._looping ) {
				this._loopCount = this.options.loop;
				this._looping = true;
			}
			this.phase = null;
			this.timer.start();
			return this;
		},
		stop: function () {
			this.timer.stop();
			return this;
		},
		pause: function () {
			this.timer.pause();
			return this;
		},
		resume: function () {
			this.timer.resume();
			return this;
		},
		seek: function ( time, options ) {
			this.timer.seek(time, options);
			return this;
		},
		seekPercent: function ( percent, options ) {
			return this.seek(percent * this.duration, options)
		},
		play: function ( from ) {
			this.stop().start().pause().seek(!_.isUndefined(from) ? from : this.delay).resume();
			return this;
		},
		position: function() {
			return this.timer._lapsed;
		}

	})
	_(ES_Tween).extend({
		from: function ( selector, props, options ) {
			return new ES_Tween(selector, _.chain(props)
					.pick(defaultOptionKeys)
					.extend(options)
					.extend({
						from: _.omit(props, defaultOptionKeys)
					})
					.value()
			)
		},
		to: function ( selector, props, options ) {
			return new ES_Tween(selector, _.chain(props)
					.pick(defaultOptionKeys)
					.extend(options)
					.extend({
						to: _.omit(props, defaultOptionKeys)
					})
					.value()
			)
		},
		fromTo: function ( selector, fromProps, toProps, options ) {
			return new ES_Tween(selector, _.extend({ from: fromProps, to: toProps }, options));
		},
		progress: function ( duration, iterator ) {
			return new ES_Tween(null, {
				from: { value: 0 },
				to: { value: 1 },
				duration: duration,
				easing: 'linear',
			})
				.on('tick', function () {
					iterator.call(this, this.tween.value, false);
				})
				.once('end', function () {
					iterator.call(this, 1, true);
				})
		}
	})

	ES_Timeline = exports.ES_Timeline = function ES_Timeline( options ) {

		if ( !(this instanceof ES_Timeline) )
			return new ES_Timeline(options)

		this.tweens = [];
		this._setOptions(options)
		this._setTimer(new ES_Timer)

		//if (!this.parent && !this.options.paused)
		//	this.start()

		return this
	}
	_(ES_Timeline).setPrototypeOf(ES_Tween.prototype)
	_(ES_Timeline).extendPrototype({

		_defaultOptions: {
			loop: 0,
			delay: 0,
			duration: 0,
			align: 'sequence'
		},
		refreshDuration: function () {
			var duration = 0;
			_(this.tweens).each(function ( tween ) {
				duration = Math.max(duration, tween.delay + tween.duration);
			})
			this.duration = duration;
			this.parent && this.parent.refreshDuration()
		},
		add: function ( tween ) {
			tween.stop();
			switch ( this.options.align ) {
				// Don't change tween delay
				// Set this duration to the highest duration
				case 'normal':
					this.duration = Math.max(this.duration, tween.delay + tween.duration);
					break;
				// Set tween delay at previous tween end
				case 'sequence':
					var delay = tween.delay;
					var duration = tween.duration;
					if ( this.lastAddedTween && this.lastAddedTween.$el && tween.$el )
						if ( this.lastAddedTween.$el.get(0) == tween.$el.get(0) )
							tween.options.from = this.lastAddedTween.options.to;
					// this duration is previous tween end
					tween.delay += this.duration;
					this.duration += delay + duration;
					this.lastAddedTween = tween;
					break;
			}
			tween.parent = this;
			// Use unshift so that when invoking
			// tween that got added first get rendered last
			// First tween will override last tween at the first frame.
			this.tweens.unshift(tween);
			return this
		},
		from: function () {
			this.add(ES_Tween.from.apply(null, arguments));
			return this;
		},
		to: function () {
			this.add(ES_Tween.to.apply(null, arguments));
			return this;
		},
		staggerFrom: function () {
			this.add(ES_Timeline.staggerFrom.apply(null, arguments));
			this.refreshDuration();
			return this;
		},
		staggerTo: function () {
			this.add(ES_Timeline.staggerTo.apply(null, arguments));
			this.refreshDuration();
			return this;
		}
	})
	_(ES_Timeline).extend({
		staggerFrom: function ( selector, delay, props, options ) {
			var timeline = ES_Timeline(_.extend({}, props, { align: 'normal' }, options));
			var delayAt = timeline.delay;
			timeline.delay = 0;
			//var delayAt = 0;
			//console.log(timeline)
			$(selector).each(function () {
				var tween = ES_Tween(this, _.chain(props)
						.pick(defaultOptionKeys)
						.extend(options)
						.extend({
							delay: delayAt,
							from: _.omit(props, defaultOptionKeys)
						})
						.value()
				)
				timeline.add(tween)
				delayAt += delay;
			});
			return timeline
		},
		staggerTo: function ( selector, delay, props, options ) {
			var timeline = ES_Timeline(_.extend({}, props, { align: 'normal' }, options));
			var delayAt = timeline.delay;
			var delayAt = 0;
			$(selector).each(function () {
				timeline.add(ES_Tween(this, _.chain(props)
						.pick(defaultOptionKeys)
						.extend(options)
						.extend({
							delay: delayAt,
							to: _.omit(props, defaultOptionKeys)
						})
						.value()
				))
				delayAt += delay;
			});
			return timeline
		}
	})

	/* Utils */

	var defaultProps = {
		opacity: 1,
		origin: { x: 0.5, y: 0.5, z: 0 },
		translate: { x: 0, y: 0, z: 0 },
		rotate: { x: 0, y: 0, z: 0 },
		scale: { x: 1, y: 1, z: 1 },
		skew: { x: 0, y: 0, z: 0 },
	};
	var defaultOptionKeys = [ 'delay', 'duration', 'easing', 'loop' ];

	function parseTweenProps( obj ) {
		var props = _.deepExtend({}, defaultProps, obj);
		if ( _.isNumber(props.scale) ) {
			props.scale = { x: props.scale, y: props.scale, z: props.scale };
		}
		if ( _.isNumber(props.rotate) ) {
			props.rotate = { z: props.rotate, x: 0, y: 0 };
		}
		_.has(props, 'x') && (props.translate.x = props.x), (delete props.x);
		_.has(props, 'y') && (props.translate.y = props.y), (delete props.y);
		_.has(props, 'z') && (props.translate.z = props.z), (delete props.z);
		return props;
	}

	function mapTweenObject( from, to ) {
		_(from).each(function ( v, key ) {
			if ( _.isObject(from[ key ]) ) {
				mapTweenObject(from[ key ], to[ key ] = to[ key ] || {});
			}
			else {
				if ( !to[ key ] ) to[ key ] = 0;
			}
		}, this)
	}

	function getTweenObject( from, to, time, duration, easing ) {
		from = (from || {});
		to = (to || {});
		return _.mapObject(to, function ( toValue, key ) {
			var fromValue = from[ key ];
			if ( _.isObject(toValue) )
				return getTweenObject(fromValue, toValue, time, duration, easing);
			if ( _.isEqual(fromValue, toValue) )
				return toValue;
			toValue = (toValue || 0);
			fromValue = (fromValue || 0);
			var timing = (easing && easings[ easing ]) ? easings[ easing ] : easings.linear;
			return fromValue + ( ( toValue - fromValue ) * timing(time / duration) );
		})
	}

	function toTransformCSS( tx ) {
		var result = '';
		tx.translate && ( result += 'translate3d(' + (tx.translate.x || 0) + 'px, ' + (tx.translate.y || 0) + 'px, ' + (tx.translate.z || 0) + 'px) ' );
		tx.rotate && typeof tx.rotate.x !== 'undefined' && ( result += 'rotate3d(1,0,0,' + (tx.rotate.x || 0) + 'deg) ' );
		tx.rotate && typeof tx.rotate.y !== 'undefined' && ( result += 'rotate3d(0,1,0,' + (tx.rotate.y || 0) + 'deg) ' );
		tx.rotate && typeof tx.rotate.z !== 'undefined' && ( result += 'rotate3d(0,0,1,' + (tx.rotate.z || 0) + 'deg) ' );
		tx.scale && ( result += 'scale3d(' + (tx.scale.x || 1) + ', ' + (tx.scale.y || 1) + ', ' + (tx.scale.z || 1) + ') ' );
		tx.skew && ( result += 'skew(' + tx.skew.x + 'deg, ' + tx.skew.y + 'deg) ' );
		return result;
	}

	var transformKey, transformKeys = [ "transform", "msTransform", "webkitTransform", "mozTransform", "oTransform" ];

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

}(this, jQuery, _, Backbone);