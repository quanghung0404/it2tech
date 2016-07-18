EasySocial.require()
.script( 'admin/grid/grid', 'admin/groups/groups')
.done(function($){

	// Implement grid item.
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' , function( task )
	{
		var selected 	= new Array;

		$( '[data-table-grid]' ).find( 'input[name=cid\\[\\]]:checked' ).each( function( i , el  ){
			selected.push( $( el ).val() );
		});

		if( task == 'reject' )
		{
			EasySocial.dialog(
			{
				content		: EasySocial.ajax( 'admin/views/groups/rejectGroup' , { "ids" : selected } )
			});

			return false;
		}

		if( task == 'approve' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/groups/approveGroup' , { "ids" : selected } )
			});

			return false;
		}

		$.Joomla( 'submitform' , [task] );
	});

	$('[data-grid-row]').implement(EasySocial.Controller.Groups.Pending.Item);
});
