
EasySocial.require()
.script( 'admin/grid/grid' )
.done( function($){

	// Bind the badges grid
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	<?php if( $this->tmpl != 'component' ){ ?>
	$.Joomla( 'submitbutton' , function( task ){

		if( task == 'remove' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/badges/confirmDelete' ),
				bindings	:
				{
					"{deleteButton} click" : function()
					{
						$.Joomla( 'submitform' , [task] );
					}
				}
			})

			return false;
		}

		$.Joomla( 'submitform' , [task] );
	});
	<?php } else { ?>

		$( '[data-badge-insert]' ).on('click', function( event )
		{
			event.preventDefault();

			// Supply all the necessary info to the caller
			var id 		= $( this ).data( 'id' ),
				avatar 	= $( this ).data( 'avatar' ),
				title	= $( this ).data( 'title' ),
				alias	= $(this).data( 'alias' ),
				obj 	= {
							"id"	: id,
							"title"	: title,
							"avatar" : avatar,
							"alias"	: alias
						  },
				args 	= [ obj <?php echo JRequest::getVar( 'callbackParams' ) != '' ? ',' . FD::json()->encode( JRequest::getVar( 'callbackParams' ) ) : '';?>];

			window.parent["<?php echo JRequest::getCmd( 'jscallback' );?>" ].apply( obj , args );
		});

	<?php } ?>
});
