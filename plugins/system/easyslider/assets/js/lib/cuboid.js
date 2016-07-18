void function($) {

	$.fn.ES_Cuboid = function( width, height, depth ) {
		return this.each( function() {

			width = (width || $(this).width());
			height = (height || $(this).height());

			depth || (depth = width);

			var halfWidth = width / 2;
			var halfHeight = height / 2;
			var halfDepth = depth / 2;
			var setback = 0;

			var $cube = $('.jsn-es-cuboid', this);
			if (!$cube.length) {
				$cube = $( '<div class="jsn-es-cuboid">' )
					.appendTo( this )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-front">' )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-back">' )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-left">' )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-right">' )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-top">' )
					.append( '<div class="jsn-es-cuboid-face jsn-es-cuboid-bottom">' )
			}
			var $front = $cube.children('.jsn-es-cuboid-front');
			var $back = $cube.children('.jsn-es-cuboid-back');
			var $left = $cube.children('.jsn-es-cuboid-left');
			var $right = $cube.children('.jsn-es-cuboid-right');
			var $top = $cube.children('.jsn-es-cuboid-top');
			var $bottom = $cube.children('.jsn-es-cuboid-bottom');

			$cube.css( {
					width: width + 'px',
					height: height + 'px'
				} );

			$( this ).css( {
				transform: 'translateZ(' + (-halfDepth) + 'px)'
			} )

			$front.css( {
					transform: 'translateZ(' + setback + 'px) translateZ(' + (halfDepth) + 'px)',
					width: width + 'px',
					height: height + 'px'
				} );
			$back.css( {
					transform: 'translateZ(' + setback + 'px) rotateY(180deg) translateZ(' + (halfDepth) + 'px)',
					width: width + 'px',
					height: height + 'px'
				} );
			$left.css( {
					transform: 'translateZ(' + setback + 'px) rotateY(-90deg) translateZ(' + (halfWidth) + 'px)',
					marginLeft: -halfDepth + 'px',
					width: depth + 'px',
					height: height + 'px'
				} );
			$right.css( {
					transform: 'translateZ(' + setback + 'px) rotateY(90deg) translateZ(' + (halfWidth) + 'px)',
					marginLeft: -halfDepth + 'px',
					width: depth + 'px',
					height: height + 'px'
				} );
			$top.css( {
					transform: 'translateZ(' + setback + 'px) rotateX(90deg) translateZ(' + halfHeight + 'px)',
					marginTop: -halfDepth + 'px',
					width: width + 'px',
					height: depth + 'px'
				} );
			$bottom.css( {
					transform: 'translateZ(' + setback + 'px) rotateX(-90deg) translateZ(' + halfHeight + 'px)',
					marginTop: -halfDepth + 'px',
					width: width + 'px',
					height: depth + 'px'
				} );
		} );
	};

}( jQuery )