
EasySocial.ready(function($)
{

	$( '[data-task-<?php echo $stream->uid;?>-checkbox]' ).on( 'change' , function()
	{
		var taskId 		= $( this ).val(),
			parentItem	= $( this ).parents( 'li' );

		if( $( this ).is( ':checked' ) )
		{
			EasySocial.ajax( 'apps/group/tasks/controllers/tasks/resolve' ,
			{
				"id" : taskId, 
				"groupId" : "<?php echo $group->id;?>"
			})
			.done(function()
			{
				$( parentItem ).addClass( 'completed' );
			});
		}
		else
		{
			EasySocial.ajax( 'apps/group/tasks/controllers/tasks/unresolve' ,
			{
				"id" 		: taskId, 
				"groupId"	: "<?php echo $group->id;?>"
			})
			.done(function()
			{
				$( parentItem ).removeClass( 'completed' );
			});
		}

	});
});