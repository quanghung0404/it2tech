void function ( exports, $, _, Backbone ) {

	exports.ES_Slide = B.Model({

		id: null,
		index: 0,
		name: '',
		group: '',

		active: false,
		hide: false,

		parallax: false,
		parallax_type: 0,

		duration: 4000,

		attr: ES_Attributes,
		thumb: ES_Image,
		items: ES_Items,

		background: B.Model(ES_Background, {
			video: B.Model(ES_Video, {
				autoplay: true,
				controls: false,
				loop: true,
				mute: true,
			}),

		}),

		transition: B.Model({
			flow: 'x',
			effect: 'fade',
			easing: 'easeInOutSine',
			duration: 1000,
		}),

		totalDuration: B.Compute([ 'duration' ], function ( duration ) {
			return duration;
		}),

	}, {
		initialize: function () {
			this.on('change:active', function ( model, active ) {
				active && _(this.collection.without(this)).invoke('set', 'active', false);
			})
			this.on('change:duration', function ( model, duration ) {
				var lastDuration = this._previousAttributes.duration;
				if ( duration < 1000 )
					model.set('duration', 1000, { silent: true });

				this.get('items').each(function(item) {
					var delay = item.get('animation.out.delay');
					var dur = item.get('animation.out.duration');
					if (dur == 0 && delay == lastDuration) {
						item.set('animation.out.delay', model.get('duration'));
					}
				}, this);
			})
		},
		index: function () {
			return this.collection.indexOf(this);
		},
		next: function(offset) {
            console.log('slide get owner next')
			return this.collection.next(offset, this.index())
		},
		prev: function(offset) {
			return this.collection.prev(offset, this.index())
		}
	}, {
		NEW_SLIDE_DEFAULTS: {

		}
	})
	exports.ES_Slides = B.Collection(ES_Slide, {
		initialize: function () {
			this.on('change:active', function ( model ) {
				if ( model.get('active') ) {
					_(this.where({ active: true })).chain().without(model).invoke('set', 'active', false)
				}
				this.checkActive()
			});
			this.on('remove', this.checkEmpty);
			this.checkActive()
		},
		checkActive: function () {
			//this.checkEmpty();
			this.length && !this.findWhere({ active: true }) && this.first().set('active', true);
		},
		checkEmpty: function () {
			!this.length && this.addNew()
		},

		offsetIndex: function ( offset, from ) {
			var active = this.findWhere({ active: true });

			var from = typeof from == 'number' ? from : (active.index() || 0);
			var length = this.length;
			var toIndex = (from + offset) % length;
			while ( toIndex < 0 ) toIndex += length;
			return toIndex;
		},
		nextIndex: function ( offset, from ) {
			return this.offsetIndex( typeof offset == 'number' ? offset : 1, from);
		},
		prevIndex: function ( offset, from ) {
			return this.offsetIndex( typeof offset == 'number' ? -offset : -1, from);
		},
		next: function( offset, from ) {

			return this.at(this.nextIndex(offset, from));
		},
		prev: function( offset, from ) {
			return this.at(this.prevIndex(offset, from));
		}
	});

}(this, jQuery, _, JSNES_Backbone);