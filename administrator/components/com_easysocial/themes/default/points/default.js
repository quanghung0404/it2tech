
EasySocial.require()
.script( 'admin/grid/grid' )
.done(function($)
{
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	<?php if( $this->tmpl != 'component' ){ ?>
	$.Joomla( 'submitbutton' , function( action )
	{
		if( action == 'remove' )
		{
			EasySocial.dialog(
			{
				content		: EasySocial.ajax( 'admin/views/points/confirmDelete' ),
				bindings 	:
				{
					"{deleteButton} click" : function()
					{
						$.Joomla( 'submitform' , [ action ] );
					}
				}
			});

			return false;
		}

		$.Joomla( 'submitform' , [ action ] );
	});

	<?php } else { ?>
		$( '[data-points-insert]' ).on('click', function( event )
		{
			event.preventDefault();

			// Supply all the necessary info to the caller
			var id 		= $( this ).data( 'id' ),
				title	= $( this ).data( 'title' ),
				alias	= $(this).data( 'alias' ),
				obj 	= {
							"id"	: id,
							"title"	: title,
							"alias"	: alias
						  },
				args 	= [ obj <?php echo JRequest::getVar( 'callbackParams' ) != '' ? ',' . FD::json()->encode( JRequest::getVar( 'callbackParams' ) ) : '';?>];

			window.parent["<?php echo JRequest::getCmd( 'jscallback' );?>" ].apply( obj , args );
		});
	<?php } ?>
});
