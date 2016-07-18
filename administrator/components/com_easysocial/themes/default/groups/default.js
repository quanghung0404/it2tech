EasySocial.require()
.script( 'admin/grid/grid' )
.done(function($){

	// Implement grid item.
	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	<?php if( $this->tmpl != 'component' ){ ?>
	$.Joomla('submitbutton', function(task) {

		var selected = new Array;

		$('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i, el) {
			var val = $(el).val();
			selected.push(val);
		});

		if (task == 'makeFeatured' || task == 'removeFeatured') {

			$('[data-table-grid-task]').val(task);

			$('[data-table-grid]').submit();

			return false;
		}

		if (task == 'create') {
			
			EasySocial.dialog({
				content 	: EasySocial.ajax( 'admin/views/groups/createDialog' , {} ),
				bindings	:
				{
					"{continueButton} click" : function()
					{
						var categoryId 	= this.category().val();

						window.location.href	= '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easysocial&view=groups&layout=form&category_id=' + categoryId;

						return false;
					}
				}
			});

			return false;
		}

		if( task == 'switchOwner' )
		{
			EasySocial.dialog(
			{
				content		: EasySocial.ajax( 'admin/views/groups/switchOwner' , { "ids" : selected } )
			});
			return false;
		}

		if( task == 'delete' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/groups/deleteConfirmation' , {} ),
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

		if (task === 'switchCategory') {
			EasySocial.dialog({
				content: EasySocial.ajax('admin/views/groups/switchCategory', {
					ids: selected
				})
			});

			return false;
		}

		$.Joomal( 'submitform' , [task] );
	});

	window.switchOwner	= function( user , groupIds )
	{
		EasySocial.dialog(
		{
			content		: EasySocial.ajax( 'admin/views/groups/confirmSwitchOwner' , { "id" : groupIds , "userId" : user.id} ),
			bindings 	:
			{

			}
		});
	}

	<?php } else { ?>
		$( '[data-group-insert]' ).on('click', function( event )
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

			window.parent["<?php echo JRequest::getCmd( 'jscallback' );?>" ]( obj );
		});
	<?php } ?>
});
