

EasySocial.require()
	.script( 'avatar' , 'cover' )
	.done(function($)
	{
		$( '[data-group-avatar]' ).implement( EasySocial.Controller.Avatar ,
			{
				"uid"	: "<?php echo $group->id;?>",
				"type"	: "<?php echo SOCIAL_TYPE_GROUP;?>",
				"redirectUrl" : "<?php echo $group->getPermalink( false );?>"
			}
		);

		$( '[data-group-cover]' ).implement( EasySocial.Controller.Cover , 
			{
				"uid"	: "<?php echo $group->id;?>",
				"type"	: "<?php echo SOCIAL_TYPE_GROUP;?>"
			}
		);


		$( '[data-es-group-delete]' ).on( 'click' , function()
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'site/views/groups/confirmDelete' , { "id" : "<?php echo $group->id;?>" } )
			});
		});

		$( '[data-es-group-unpublish]' ).on( 'click' , function()
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'site/views/groups/confirmUnpublishGroup' , { "id" : "<?php echo $group->id;?>" } )
			});
		});
	});
