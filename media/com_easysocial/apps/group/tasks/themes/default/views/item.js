
EasySocial.require()
.library( 'sparkline' )
.script( 'apps/group/tasks' )
.done(function($)
{
	// Apply controller
	$( '[data-group-tasks-item]' ).implement( 'EasySocial.Controller.Groups.Apps.Tasks' ,
		{
			"redirect"	: "<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() ) );?>"
		});

	$( '[data-chart-milestone]' ).sparkline( 'html' ,
		{
			type 	: "pie",
			width	: "120px",
			height	: "120px",
			sliceColors: [ "#2b94c5" , "#BE1F23" ],
			tooltipFormatter : function( sparkline , options, field )
			{
				var closed 	= '<?php echo JText::_( 'APP_GROUP_TASKS_CHART_CLOSED_TASKS' , true );?>',
					open 	= '<?php echo JText::_( 'APP_GROUP_TASKS_CHART_OPEN_TASKS' , true );?>',
					message	= field.offset == 1 ? open : closed;

				return '<span style="color: ' + field.color + '">&#9679;</span> <strong>' + field.value + '</strong> ' + message;
			}
		});
});
