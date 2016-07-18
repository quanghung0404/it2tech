EasySocial.require()
.library('sparkline')
.language('COM_EASYSOCIAL_GROUPS_TOTAL_GROUP_CREATED_SINGULAR', 'COM_EASYSOCIAL_GROUPS_TOTAL_GROUP_CREATED_PLURAL')
.done(function($)
{
	$( '[data-category-gravity-chart]' ).sparkline( 'html',
		{
			width				: '100%',
			height				: '120px',
			lineWidth			: 3,
			lineColor 			: "#4a8fcf",
			fillColor			: "#d8e8f7",
			highlightLineColor	: "#4a8fcf",
			maxSpotColor		: "#D44950",
			minSpotColor		: "#D44950",
			highlightSpotColor	: "#D44950",
			spotRadius			: 4,
			chartRangeMin 		: 0,
			drawNormalOnTop		: true,
			tooltipFormatter : function( sparkline , options, field )
			{
				var string = field.y > 1 ? 'COM_EASYSOCIAL_GROUPS_TOTAL_GROUP_CREATED_PLURAL' : 'COM_EASYSOCIAL_GROUPS_TOTAL_GROUP_CREATED_SINGULAR';

				return '<span style="color: ' + field.color + '">&#9679;</span> ' + $.language(string, field.y);
			}
		});
});
