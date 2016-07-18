
EasySocial.require()
.library( 'sparkline' )
.done(function($)
{
	$( '[data-kunena-posts-chart]' ).sparkline( 'html',
		{ 

			lineWidth	: 3,
			lineColor 	: "#2b8c69",
			barColor	: "rgba(178,189,199,1)",
			zeroColor	: "rgba(228,123,121,1)",
			spotRadius	: 5,
			type		: 'bar',
			barWidth 	: '10px',
			barSpacing	: '5px',
			chartRangeMin : 0,
			tooltipFormatter : function( sparkline , options, fields )
			{
				var field	= fields[0];

				return '<span style="color: ' + field.color + '">&#9679;</span> <strong>' + field.value + '</strong> <?php echo JText::_( 'APP_KUNENA_CHART_TOPICS_TOOLTIP' ); ?>';
			}
		});
});