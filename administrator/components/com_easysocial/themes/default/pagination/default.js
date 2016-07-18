
EasySocial.require()
.script( 'pagination' )
.done( function( $ ){

	$( '[data-grid-pagination]' ).implement( EasySocial.Controller.Pagination );

});