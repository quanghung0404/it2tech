void function ( exports, $, _, Backbone ) {

	exports.ES_Item_Style = B.Model({

		//visible: true,

		background: ES_Background,

		position: B.Model({ x: 0.5, y: 0.5 }),
		offset: B.Model({ x: 0, y: 0, z: 0 }),
		rotate: B.Model({ x: 0, y: 0, z: 0 }),

		width: 0,
		height: 0,

		opacity: 1,

		line_height: 1,
		letter_spacing: 0,

		align_h: 'center',
		align_v: 'middle',

		flex: B.Model({
			wrap: 'nowrap',
			shrink: false,
			grow: false,
			direction: 'column',
			alignItems: 'center',
			alignContent: 'space-around',
			justifyContent: 'space-around',
			alignStart: B.Compute({
				deps: [ 'direction' ],
				get: function () {
					//row-reverse
					return this.get('direction').indexOf('column') >= 0 ? 'Left' : 'Top';
				},
				set: function ( value ) {
					//this.set('direction', Math.round(value * 100) * 10);
				}
			}),
			alignCenter: B.Compute({
				deps: [ 'direction' ],
				get: function () {
					return this.get('direction').indexOf('column') >= 0 ? 'Center' : 'Middle';
				},
				set: function ( value ) {
					//this.set('direction', Math.round(value * 100) * 10);
				}
			}),
			alignEnd: B.Compute({
				deps: [ 'direction' ],
				get: function () {
					return this.get('direction').indexOf('column') >= 0 ? 'Right' : 'Bottom';
				},
				set: function ( value ) {
					//this.set('direction', Math.round(value * 100) * 10);
				}
			})
		}),
		padding: B.Model({
			top: 0,
			left: 0,
			right: 0,
			bottom: 0
		}),
		border: B.Model({
			width: 0,
			style: 'none',
			color: '',
			radius: 0,
			borderStyleNotNone: B.Compute([ 'style' ], function ( style ) {
				if ( style ) {
					return style == 'none' ? true : false;
				}
			})
		}),
		font: B.Model({
			family: 'Helvetica',
			size: 24,
			weight: 400,
			style: 'normal',
			color: '#000'
		}),
		box_shadows: B.Collection(
			B.Model({
				inset: false, x: 0, y: 0, blur: 0, color: '',
				isInset: B.Compute({
					deps: ['inset'],
					get: function(arg) {
						return arg ? 'inset' : 'outset';
					},
					set: function(value) {
                        value && this.set('inset', (value == 'inset' ? true : false))
					}
				})
			})
		),
		text_shadows: B.Collection(
			B.Model({ x: 0, y: 0, blur: 0, color: '' })
		),
		smartAlign: B.Compute({
			deps: [ 'align_h', 'flex' ],
			get: function ( align_h ) {
				return align_h;
			},
			set: function(value) {

				switch (value) {
					case 'left':
						this.set('flex.direction', 'column');
						this.set('flex.alignItems', 'flex-start');
						this.set('align_h', 'left');
						break;
					case 'center':
						this.set('flex.direction', 'column');
						this.set('flex.alignItems', 'center');
						this.set('align_h', 'center');
						break;
					case 'right':
						this.set('flex.direction', 'column');
						this.set('flex.alignItems', 'flex-end');
						this.set('align_h', 'right');
						break;
				}
			}
		})
	}, {
		initialize: function () {
			if ( !_.has(this.attributes, 'visible') ) {
				this.set('visible', true, { silent: true });
			}
		}
	});

	exports.ES_Item_Animation_Transform = B.Model({
		opacity: 0,
		origin: B.Model({ x: 0.5, y: 0.5, z: 0 }),
		translate: B.Model({ x: 0, y: 0, z: 0 }),
		rotate: B.Model({ x: 0, y: 0, z: 0 }),
		scale: B.Model({ x: 1, y: 1, z: 1 }),
		skew: B.Model({ x: 0, y: 0, z: 0 }),
	})

	exports.ES_Item_Animation = B.Model({

		enable: true,
		auto: true,

		effect: 'none',
		easing: 'linear',

		delay: 0,
		duration: 500,

		split: 0,
		splitDelay: 50,

		transform: ES_Item_Animation_Transform,

		delaySeconds: B.Compute({
			deps: ['delay'],
			get: function() {
				return (Math.round(this.get('delay') / 100) / 10).toFixed(1);
			},
			set: function ( value ) {
				this.set('delay', Math.round(value * 100) * 10);
			}
		}),
		durationSeconds: B.Compute({
			deps: [ 'duration' ],
			get: function () {
				return (Math.round(this.get('duration') / 100) / 10).toFixed(1);
			},
			set: function ( value ) {
				this.set('duration', Math.round(value * 100) * 10);
			}
		})

	}, {
		initialize: function () {
			this.on('change:transform', function ( model, value, options ) {
				if ( !options.chained )
					this.set('effect', 'custom');
			})
		},
		getTweenObj: function () {
			return _.extend(this.get('transform').toJSON(), this.pick('delay', 'duration', 'easing'))
		}
	});

	exports.ES_Item = B.Model({

		id: null,
		index: 0,
		name: '',
		group: '',

		selected: false,
		locked: false,
		hidden: false,

		content: '<div></div>',
		dynamicName: B.Compute({
			deps: [ 'name', 'content' ],
			get: function () {
				//var name = this.get('name');
				//if (name)
				//	return name;
				//
				//var content = this.get

				return this.get('name') || $(this.get('content')).map(function () {
						return $(this).text()
					}).toArray().join(' ')
			},
			set: function ( value ) {
				this.set('name', value)
			}
		}),

		aspectRatio: false,
		parallax_depth: 0,

		attr: ES_Attributes,

		style: B.Compute({
			deps: [ 'style_desktop', 'style_laptop', 'style_tablet', 'style_mobile' ],
			get: function () {
				var mode = this.root.get('state.view_mode');
				return this.get('style_' + ( mode || 'desktop'));
			},
			set: function ( value, options ) {
				var mode = this.root.get('state.view_mode');
				var model = this.attributes['style_' +( mode||'desktop')];
				return model && model.set(value, options);
			}
		}),

		style_desktop: ES_Item_Style,
		style_laptop: B.Model({}),
		style_tablet: B.Model({}),
		style_mobile: B.Model({}),

		animation: B.Model({
			'in': ES_Item_Animation,
			'out': ES_Item_Animation
		}),

	}, {
		initialize: function ( attrs ) {

			this.get('type') == 'item' && this.set('type', 'box');

			this.get('style_laptop').alias(this.get('style_desktop'));
			this.get('style_tablet').alias(this.get('style_desktop'));
			this.get('style_mobile').alias(this.get('style_desktop'));

			this.on('change:locked', function ( model, locked ) {
				!locked || this.set('selected', false);
			})
			this.on('change:animation.out.delay', function ( model, outDelay ) {
				var inDelay = this.get('animation.in.delay');
				var inDuration = this.get('animation.in.duration');
				if ( inDelay + inDuration > outDelay )
					this.set('animation.out.delay', inDelay + inDuration)
			})
			this.on('change:animation.in.effect', function ( model, name ) {
				this.setEffect('in', name);
			})
			this.on('change:animation.out.effect', function ( model, name ) {
				this.setEffect('out', name);
			})
			this.on('change:style.position', this.updateOffset)
		},
		setEffect: function ( type, name ) {
			var easing;
			var animation = ES_ANIMATIONS_INDEX[ name ];
			if ( !animation )
				return;
			switch ( type ) {
				case 'in':
					easing = 'easeOut' + (animation.easing || 'Cubic');
					break;
				case 'out':
					easing = 'easeIn' + (animation.easing || 'Cubic');
					break;
			}
			this.set('animation.' + type, {
				easing: easing,
				transform: animation.transform
			}, {
				preset: true
			});
		},
		updateOffset: function () {
			var pos_x = this.get('style.position.x');
			switch ( parseFloat((pos_x)) ) {
				case 0:
					this.set('style.offset.x', 0);
					break;
				case 0.5:
					this.set('style.offset.x', parseInt(this.get('style.width')) / -2);
					break;
				case 1:
					this.set('style.offset.x', -this.get('style.width'));
					break;
			}
			var pos_y = this.get('style.position.y');
			switch ( parseFloat((pos_y)) ) {
				case 0:
					this.set('style.offset.y', 0);
					break;
				case 0.5:
					this.set('style.offset.y', this.get('style.height') / -2);
					break;
				case 1:
					this.set('style.offset.y', -this.get('style.height'));
					break;
			}
		}

	}, {
		DEFAULT_ANIM_IN: {
			delay: 0,
			duration: 500,
			transform: {
				opacity: 0
			}
		},
		DEFAULT_ANIM_OUT: {
			delay: 5000,
			duration: 500,
			transform: {
				opacity: 0
			}
		},
		DEFAULT_BOX: {},
		DEFAULT_TEXT: {},
		DEFAULT_IMAGE: {},
		DEFAULT_VIDEO: {}
	});

	exports.ES_Items = B.Collection(ES_Item);

	exports.ES_ANIMATIONS = {

		'default': {
			'none': {},
		},
		'bounce': {
			'bounce': {
				easing: 'Elastic',
				scale: { x: 0.5, y: 0.5 }
			},
			'bounce-top': {
				easing: 'Elastic',
				translate: { y: -200 }
			},
			'bounce-bottom': {
				easing: 'Elastic',
				translate: { y: 200 }
			},
			'bounce-left': {
				easing: 'Elastic',
				translate: { x: -200 }
			},
			'bounce-right': {
				easing: 'Elastic',
				translate: { x: 200 }
			},
		},
		'slide': {
			'slide-left': {
				easing: 'Quad',
				translate: { x: -100 }
			},
			'slide-left-big': {
				translate: { x: -2000 }
			},
			'slide-left-small': {
				translate: { x: -50 }
			},
			'slide-right': {
				translate: { x: 100 }
			},
			'slide-right-big': {
				translate: { x: 2000 }
			},
			'slide-right-small': {
				translate: { x: 50 }
			},
			'slide-top': {
				translate: { y: -100 }
			},
			'slide-top-big': {
				translate: { y: -2000 }
			},
			'slide-top-small': {
				translate: { y: -50 }
			},
			'slide-bottom': {
				translate: { y: 100 }
			},
			'slide-bottom-big': {
				translate: { y: 2000 }
			},
			'slide-bottom-small': {
				translate: { y: 50 }
			},
		},
		'roll': {
			'roll-left': {
				translate: { x: -400 },
				rotate: { z: -360 },
			},
			'roll-right': {
				translate: { x: 400 },
				rotate: { z: 360 },
			}
		},
		'skew': {
			'skew-left': {
				easing: 'Back',
				translate: { x: -200 },
				skew: { x: 30 }
			},
			'skew-right': {
				easing: 'Back',
				translate: { x: 200 },
				skew: { x: -30 }
			}
		},
		'flip': {
			'flip-left': {
				rotate: { y: -180 }
			},
			'flip-right': {
				rotate: { y: 180 }
			},
			'flip-top': {
				rotate: { x: 180 }
			},
			'flip-bottom': {
				rotate: { x: -180 }
			},
		},
		'rotate': {
			'rotate-left-360': {
				rotate: { z: 360 }
			},
			'rotate-left-180': {
				rotate: { z: 180 }
			},
			'rotate-left-90': {
				rotate: { z: 90 }
			},

			'rotate-right-360': {
				rotate: { z: -360 }
			},
			'rotate-right-180': {
				rotate: { z: -180 }
			},
			'rotate-right-90': {
				rotate: { z: -90 }
			},
		},
		'scale': {
			'scale': {
				scale: { x: 0, y: 0 }
			},
			'scale-X': {
				scale: { x: 0, y: 1 }
			},
			'scale-Y': {
				scale: { x: 1, y: 0 }
			},
		},
		'Other': {
			'custom': {}
		}
	};

	exports.ES_ANIMATIONS_INDEX = {};
	_(ES_ANIMATIONS).each(function ( animations, group ) {
		_(animations).each(function ( animation, name ) {
			ES_ANIMATIONS_INDEX[ name ] = {
				easing: animation.easing,
				transform: (new ES_Item_Animation_Transform(_.omit(animation, 'easing'))).toJSON()
			};
		})
	})

}(this, jQuery, _, JSNES_Backbone);