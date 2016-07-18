EasySocial.require()
.script( 'admin/profiles/profiles' , 'admin/grid/grid' )
.done(function($){

	// Implement grid item.
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	var profileList = $( '[data-profiles]' ).addController( EasySocial.Controller.Profiles );

	<?php if( !$callback ){ ?>
	$.Joomla("submitbutton", function(task)
	{
		if( task == 'form' )
		{
			window.location 	= 'index.php?option=com_easysocial&view=profiles&layout=form';

			return;
		}

		if( task == 'delete' )
		{

			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/profiles/confirmDelete' , {} ),
				bindings 	:
				{
					"{deleteButton} click" : function()
					{
						$.Joomla( 'submitform' , ['delete' ] );
					}
				}
			});

			return false;
		}

		$.Joomla("submitform", [task]);

	});

	<?php } else { ?>

		<?php if( JRequest::getVar( 'jscallback' ) ){ ?>
			$( '[data-profile-insert]' ).on('click', function( event )
			{
				event.preventDefault();

				// Supply all the necessary info to the caller
				var id 		= $( this ).data( 'id' ),
					avatar 	= $( this ).data( 'avatar' ),
					title	= $( this ).data( 'title' ),
					alias	= $(this).data( 'alias' );

					obj 	= {
								"id"	: id,
								"title"	: title,
								"avatar" : avatar,
								"alias"	: alias
							  };
					args 	= [ obj <?php echo JRequest::getVar( 'callbackParams' ) != '' ? ',' . FD::json()->encode( JRequest::getVar( 'callbackParams' ) ) : '';?>];

				window.parent["<?php echo JRequest::getCmd( 'jscallback' );?>" ].apply( obj , args );
			});
		<?php } else { ?>
			$( '[data-profile-insert]' ).on('click', function( event )
			{
				event.preventDefault();

				var id 	= $( this ).data( 'id' );

				window.parent["<?php echo JRequest::getCmd( 'callback' );?>" ]( id );
			});
		<?php } ?>

	<?php } ?>

});
