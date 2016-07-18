
EasySocial
.require()
.script( 'admin/users/users' , 'admin/grid/grid' )
.done( function($){
	
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$( '[data-pending-users]' ).implement( EasySocial.Controller.Users.Pending );
	
	$.Joomla( 'submitbutton' , function( task ){

		var selected 	= new Array;

		$( '[data-table-grid]' ).find( 'input[name=cid\\[\\]]:checked' ).each( function( i , el  ){
			selected.push( $( el ).val() );
		});

		if( task == 'remove' )
		{
			EasySocial.dialog( 
			{
				content 	: EasySocial.ajax( 'admin/views/users/confirmDelete' , { "id" : selected })
			});

			return false;
		}
		
		if( task == 'reject' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/users/confirmReject' , { "id" : selected } )
			});

			return false;
		}

		if( task == 'approve' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/users/confirmApprove' , { "id" : selected } ),
				bindings	:
				{
					"{approveButton} click" : function()
					{
						this.approveUserForm().submit();
					}
				}
			});

			return false;
		}

		$.Joomla( 'submitform' , [task] );
	});
})