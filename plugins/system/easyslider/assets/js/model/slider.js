void function ( exports, $, _, Backbone ) {


	exports.ES_SLIDER_TYPE_STANDARD = 1;
	exports.ES_SLIDER_TYPE_CAROUSEL = 2;

	exports.ES_Slider = B.Model({

		id: null,
		name: 'Untitled', // name identifier
		group: '', // group identifier

		connect_with: '', // css selector string

		custom_js: '',
		custom_css: '',

		slides: ES_Slides,
		items: ES_Items,
		attr: ES_Attributes,

		background: ES_Background,

		state: B.Model({
			view_mode: 'desktop'
		}),
		layout: B.Model({

			type: ES_SLIDER_TYPE_STANDARD, // 1: standard, 2: carousel, 3: scene

			mode: 'slide', // slide, cube, carousel, polygon, coverflow
			isNotCoverflow: B.Compute(['mode'], function(arg){
				return arg == 'coverflow' ? false : true;
			}),

			flow: 'x',
			axis: 'x',

			desktop_w: 1000,
			desktop_h: 400,

			laptop: false,
			laptop_w: 900,
			laptop_h: 400,
			laptop_under: 1366,

			tablet: false,
			tablet_w: 700,
			tablet_h: 300,
			tablet_under: 1024,

			mobile: false,
			mobile_w: 480,
			mobile_h: 200,
			mobile_under: 767,

			full_w: false,
			full_h: false,

			auto_w: false,
			auto_h: false,

			spacing: 0,
			padding: 0,

			isCarousel: B.Compute(['type'], function(type) {
				return type == ES_SLIDER_TYPE_CAROUSEL;
			}),

			preset: B.Compute({
				deps: [ 'full_w', 'full_h'],
				get: function ( full_w, full_h ) {
					if (full_w && full_h)
						return 'full-screen';
					else if (full_w)
						return 'full-width';
					else
						return 'auto-width';
				},
				set: function ( value ) {
					switch (value) {
						case 'full-screen':
							this.set({
								full_w: true,
								full_h: true,
								auto_w: true,
								auto_h: true,
							});
							return value;

						case 'full-width':
							this.set({
								full_w: true,
								full_h: false,
								auto_w: true,
								auto_h: false,
							});
							return value;

						case 'auto-width':
							this.set({
								full_w: false,
								full_h: false,
								auto_w: true,
								auto_h: false,
							});
							return value;
					}
				}
			})
		}),

		parallax: 0, /* 0: none , 1: scrolling , 2: follow mouse , 3: device tilt */

		style: B.Model({

			border_radius: 0,

			margin: B.Model({
				top: 50,
				left: 0,
				right: 0,
				bottom: 50,
			})
		}),
		interactive: B.Model({
			enable: true,
		}),
		nav: B.Model({
			enable: true,
			style: 'circlepop',
			hover_show: true,
			touch_show: true,
		}),
		pagination: B.Model({
			enable: true,
			hover: true,
			style: 'smalldotstroke',
			spacing: 15,
			size: 12,
			hover_show: true,
			touch_show: true,
		}),
		timeline: B.Model({
			mode: 1, // 1: Simple, 2: Advanced
		}),
		grid: B.Model({
			show: true,
			color: 'rgba(100,100,100,0.3)',
			size: 50,
			gutter: 5
		}),
		repeat: B.Model({
			enable: true
		}),
		autoSlide: B.Model({
			enable: true
		}),
		visibleRepeat:  B.Compute(['layout', 'autoSlide'], function(layout, autoSlide){
			return layout && autoSlide && layout.get('type') == 1 && autoSlide.get('enable') ? true : false;  // enable repeat option when layout.type == 1 : standard
		}),
		actions: B.Collection([
			B.Model({
				selector: '.btn-foo',
				action: 'click',
				target: '.slide-foo',
				type: 'animationin',
				arg: '0'
			})
		]),

		width: B.Compute({
			deps: [ 'state.view_mode', 'layout' ],
			get: function () {
				var mode = this.get('state.view_mode');
				return this.get('layout').get(mode + '_w');
			},
			set: function ( value ) {
				//console.log(this)
				var mode = this.get('state.view_mode');
				this.set('layout.' + mode + '_w', value, { silent: true });
				//this.get('layout').set(mode + '_w', value, { silent: true });
				return value;
			}
		}),
		height: B.Compute({
			deps: [ 'state.view_mode', 'layout' ],
			get: function () {
				var mode = this.get('state.view_mode');
				return this.get('layout').get(mode + '_h');
			},
			set: function ( value ) {
				var mode = this.get('state.view_mode');
				this.set('layout.' + mode + '_h', value, { silent: true });
				//this.get('layout').set(mode + '_h', value, { silent: true });
				return value;
			}
		}),

	}, {
		constructor: function ES_Slider( data ) {
			//delete data.slides[0].items[0].style;
			B.Model.call(this, data)
			//B.Model.call(this, {})
			//console.log(data)
			//this.set(data);
		},
		initialize: function ( attrs ) {
			_.bindAll(this, 'save');
			this.on('change:layout.mode', this.changeLayoutMode)
		},
        changeLayoutMode: function(){
            if ( this.get('layout.mode') == 'coverflow' ) {
                this.set('layout.flow', 'x');
            }
        },
		clear: function ( options ) {
			B.Model.prototype.clear.call(this, { silent: true });
			this.set({ version: 2 })
			return this;
		},
		save: function () {
			var self = this;
			this.get('slides').sort();
			self.trigger('save:request');
			$.ajax({
				url: self.get('id') ? ES_Config.URL.UPDATE_SLIDER : ES_Config.URL.CREATE_SLIDER,
				type: 'POST',
				dataType: 'json',
				data: {
					slider_id: self.get('id'),
					slider_title: self.get('title'),
					slider_data: JSON.stringify(self.toCompactJSON()),
					//slider_data: JSON.stringify(self.toJSON()),
				},
				success: function ( response ) {
					response.error ?
						self.trigger('save:error', response.message) :
							( self.get('id') ? self.trigger('save:success', response.message) : '');
					if ( response.slider_id ) {
                        if ( response.first && response.slider_id == 1) {
                            $('.add-text-btn').trigger('click')
                            $('.slider-quick-tour-btn').trigger('click')
                        }
						self.set('id', response.slider_id, { silent: true });
						history.pushState({}, "New Slider State", window.location.toString().replace(/^(.*)(?:&slider_id=.+)$/, '$1') + '&slider_id=' + response.slider_id);

                    }

				},
				error: function ( req ) {
					self.trigger('save:error', req.status + ': ' + req.statusText);
				}
			});
			//console.log("\n\n---------------------\n\n");
			//console.log(JSON.stringify(self.toCompactJSON()));
			//console.log("\n\n---------------------\n\n");
			//console.log(JSON.stringify(self.toJSON()));
			return this;
		},
	});

}(this, jQuery, _, JSNES_Backbone);