
EasySocial.require()
.library('dialog')
.done(function($)
{
	$('[data-group-menu-approve]').on('click',function()
	{
		var id 		= $(this).data('id'),
			groupId	= $(this).data('group-id');

		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'site/views/groups/confirmApprove' , { "id" : groupId , "userId" : id } )
		});
	});

	$('[data-group-menu-reject]').on('click',function()
	{
		var id 		= $(this).data('id'),
			groupId	= $(this).data('group-id');
			
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'site/views/groups/confirmReject' , { "id" : groupId , "userId" : id } )
		});
	});
});