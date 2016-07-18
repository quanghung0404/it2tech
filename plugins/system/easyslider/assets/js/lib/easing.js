/**
 * Created by phong on 8/28/15.
 */

void function ( exports, $, _, Backbone) {

	var easings = exports.ES_Easings = {
		linear: function( p ) {
			return p;
		}
	};
	var baseEasings = {};

	_.each( [ "Quad", "Cubic", "Quart", "Quint", "Expo" ], function( name, i ) {
		baseEasings[ name ] = function( p ) {
			return Math.pow( p, i + 2 );
		};
	} );
	_.extend( baseEasings, {
		Sine: function( p ) {
			return 1 - Math.cos( p * Math.PI / 2 );
		},
		Circ: function( p ) {
			return 1 - Math.sqrt( 1 - p * p );
		},
		Elastic: function( p ) {
			return p === 0 || p === 1 ? p :
			-Math.pow( 2, 8 * (p - 1) ) * Math.sin( ( (p - 1) * 80 - 7.5 ) * Math.PI / 15 );
		},
		Back: function( p ) {
			return p * p * ( 3 * p - 2 );
		},
		Bounce: function( p ) {
			var pow2,
				bounce = 4;
			while ( p < ( ( pow2 = Math.pow( 2, --bounce ) ) - 1 ) / 11 ) {
			}
			return 1 / Math.pow( 4, 3 - bounce ) - 7.5625 * Math.pow( ( pow2 * 3 - 2 ) / 22 - p, 2 );
		}
	} );
	_.each( baseEasings, function( easeIn, name ) {
		easings[ "easeIn" + name ] = easeIn;
		easings[ "easeOut" + name ] = function( p ) {
			return 1 - easeIn( 1 - p );
		};
		easings[ "easeInOut" + name ] = function( p ) {
			return p < 0.5 ?
			easeIn( p * 2 ) / 2 :
			1 - easeIn( p * -2 + 2 ) / 2;
		};
	} );

} ( this, jQuery, _, Backbone );