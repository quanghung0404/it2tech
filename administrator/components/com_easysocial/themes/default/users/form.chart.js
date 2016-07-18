
EasySocial.require()
.library( 'flot' )
.done(function($){

	var data 	= new Array();

	<?php foreach( $stats->items as $obj ){ ?>
		var tmpArray 	= new Array;

		<?php for( $x = 0; $x < count( $obj->items ); $x++ ){ ?>
			tmpArray.push( [<?php echo $x;?> , <?php echo $obj->items[ $x ];?> ] );
		<?php } ?>

		var tmpObject 	= { data : tmpArray , label : "<?php echo $obj->title;?>" };

		data.push( tmpObject );
	<?php } ?>

	$( '[data-form-chart]' ).plot( data ,
	{
		lines:
		{
			show			: true,
			fill			: false,
			lineWidth		: 2
		},
		legend:
		{
			sorted: "asc",
			noColumns: 7,
			container: $("[data-form-chart-legend]" ),
			backgroundColor: "#fff",
			backgroundOpacity: 1
		},
		xaxis:
		{
			ticks:
			[
				<?php for( $i = 0; $i < count( $stats->friendlyDates ); $i++ ){ ?>
				[ <?php echo $i;?> , '<?php echo $stats->friendlyDates[ $i ];?>' ]<?php echo ($i + 1) != 7 ? ',' : '';?>
				<?php } ?>
			]
		},
		yaxis: {
			min: 0,
			tickSize: 5,
			tickDecimals: 0
		},
		points:
		{
			show		: true,
			fill 		: true,
			lineWidth 	: 4
		},

		grid:
		{
			clickable: true,
			hoverable: true,
			autoHighlight: true,
			mouseActiveRadius: 10,
			aboveData: true,
			backgroundColor: "#fff",
			borderWidth: 0,
			borderColor: "#f4f4f4",
			minBorderMargin: 25,
		},
		// colors: [ "rgba(77,175,140,0.5)", "rgba(178,189,199,0.5)",  "#609", "#900"],
		shadowSize: 0

	});

	$( '[data-form-chart]' ).bind( 'plothover' , function( event , pos , item )
	{
		if( item )
		{
			if( previousPoint != item.dataIndex || previousLabel != item.series.label )
			{
				previousPoint	= item.dataIndex;
				previousLabel 	= item.series.label;

				$("#tooltip").remove();

				var x 			= item.datapoint[0],
					y			= item.datapoint[1],
					tooltips	= new Array;

				<?php foreach( $stats->items as $obj ){ ?>
					tooltips.push( "<?php echo $this->html( 'string.escape' , $obj->title );?>" );
				<?php } ?>

				window.showTooltip( item.pageX, item.pageY, y + ' ' + tooltips[ item.seriesIndex ] );
			}
		}
		else
		{
			$( '#tooltip' ).remove();

			previousPoint 	= null;
		}
	}); 

	// Show tooltip
	window.showTooltip = function( x , y , contents )
	{
		$( '<div id="tooltip">' + contents + '</div>' )
			.css(
			{
				position: "absolute",
				display: "none",
				top: y + 5,
				left: x + 5,
				padding: "5px",
				'background-color' : '#000',
				'font-size'			: '10',
				color: '#fff',
				opacity: 0.80
			}).appendTo( 'body' ).fadeIn( 200 );
	};
});
