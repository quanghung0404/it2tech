EasySocial.module( 'story/tasks', function($)
{
	var module 	= this;

	EasySocial.require()
	.view( 'apps/group/tasks/story/attachment.item' )
	.done(function()
	{
		EasySocial.Controller( 'Story.Tasks',
		{
			defaultOptions:
			{
				view:
				{
					taskItem: "apps/group/tasks/story/attachment.item"
				},

				'{form}'	: '[data-story-tasks-form]',
				'{input}'	: '[data-story-tasks-input]',
				'{list}'	: '[data-story-tasks-list]',
				'{milestone}'	: '[data-story-tasks-milestone]'
			}
		},
		function( self )
		{
			return {
				init: function()
				{
					self.implementForm();
				},

				implementForm: function()
				{
					self.form().implement( EasySocial.Controller.Story.Tasks.Form , { '{parent}' : self });
				},

				createInputRow: function( currentElement )
				{
					var item  = self.view.taskItem();

					$( item )
						.implement( EasySocial.Controller.Story.Tasks.Form , { '{parent}' : self })
						.appendTo( self.list() );


					// After appending, set the focus to the new item's input
					$( item ).find( 'input' ).focus();
				},

				"{story} save": function(element, event, save)
				{
					var values 	= new Array();

					$.each( self.input() , function( i , item )
					{
						if( $( item ).val() != '' )
						{
							values.push( $(item).val() );	
						}
					});

					var data = {
									items 		: values,
									milestone 	: self.milestone().val()
								};

					save.addData( self , data );
				},

				"{story} clear": function()
				{
					// self.linkInput().val("");

					// self.removeLink();
				}
			}
		});

		EasySocial.Controller( 'Story.Tasks.Form',
		{
			defaultOptions:
			{
				'{input}'	: '[data-story-tasks-input]',
				'{remove}'	: '[data-story-tasks-remove]'
			}
		},
		function( self )
		{
			return {
				init: function()
				{
				},

				'{input} keyup' : function( el , event )
				{
					// Enter key
					if(event.keyCode == 13)
					{
						self.parent.createInputRow( self.element );
					}
				},

				'{remove} click' : function( el , event )
				{
					self.element.remove();
				}
			}
		});

		module.resolve();
	});
});