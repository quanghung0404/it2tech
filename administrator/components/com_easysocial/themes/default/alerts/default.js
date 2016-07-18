
EasySocial.require()
.script( 'admin/grid/grid' )
.done( function($){

	// Bind the badges grid
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );
});