ed.require(['edq', 'chartjs'], function($) {

	// // Plot the chart with posts
	// var data = [{ data: <?php echo $postsCreated;?>, label: "<?php echo JText::_('COM_EASYDISCUSS_CHART_POSTS', true);?>" }];

 //    $('[data-chart-posts]').plot(data, {

 //        series: {
 //            lines: { show: true,
 //                    lineWidth: 1,
 //                    fill: true, 
 //                    fillColor: { colors: [ { opacity: 0.1 }, { opacity: 0.13 } ] }
 //                 },
 //            points: { show: true, 
 //                     lineWidth: 2,
 //                     radius: 3
 //                 },
 //            shadowSize: 0,
 //            stack: true
 //        },
 //        grid: { 
 //            hoverable: true, 
 //            clickable: true, 
 //            tickColor: "#f9f9f9",
 //            borderWidth: 0,
 //            backgroundColor: "#fff",
 //        },
 //        colors: ["#a7b5c5", "#30a0eb"],
 //        xaxis: {
 //            min: 0.0,
 //            max: 6,
 //            //mode: null,
 //            ticks: <?php echo $postsTicks;?>,
 //            tickLength: 0, // hide gridlines
 //            axisLabelUseCanvas: true,
 //            tickDecimals: 0,
 //            axisLabelFontSizePixels: 12,
 //            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
 //            axisLabelPadding: 5
 //        },
 //        yaxis: {
 //            tickDecimals: 0
 //        },
 //        shadowSize: 0
 //    });

    var data = {
        labels: <?php echo $postsTicks; ?>,
        datasets: [
            {
                label: "My Created Posts",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: <?php echo $postsCreated; ?>
            }
        ]
    };

    var options ={
        bezierCurve : false,
        responsive: true,
        maintainAspectRatio: false,
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> Post",
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
    }

    var ctx = document.getElementById("graph-area").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data, options);

    var legend = myLineChart.generateLegend();

    $('#graph-legend').append(legend);


    // Tabs animation
    jQuery(document).ready(function() {
        jQuery('.db-activity .db-activity-filter a').on('click', function(e)  {
            var currentAttrValue = jQuery(this).attr('href');
     
            // Show/Hide Tabs
            jQuery('.db-activity ' + currentAttrValue).show().siblings().hide();
     
            // Change/remove current tab to active
            jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
     
            e.preventDefault();

            if (currentAttrValue == '#month') { monthPie(); };
            if (currentAttrValue == '#category') { categoryPie(); };
            if (currentAttrValue == '#posts') { postsPie(); };
        });
    });    

    var postsPie = function() {
        var pieData = <?php echo $postsPie; ?>;

        if (pieData.length == 0) {
            pieData = pieDefault;
        };

        fixedPosition("chart-area2");
       
        var ctx2 = document.getElementById("chart-area2").getContext("2d");
        window.myPie = new Chart(ctx2).Pie(pieData, options);
        document.getElementById('js-legend2').innerHTML = myPie.generateLegend();
    };

    var monthPie = function() {
        var pieData = <?php echo $monthPie; ?>;

        if (pieData.length == 0) {
            pieData = pieDefault;
        };

        fixedPosition("chart-area3");

        var ctx2 = document.getElementById("chart-area3").getContext("2d");
        window.myPie = new Chart(ctx2).Pie(pieData, options);
        document.getElementById('js-legend3').innerHTML = myPie.generateLegend();
    };

    var categoryPie = function() {
        var pieData = <?php echo $categoryPie; ?>;

        if (pieData.length == 0) {
            pieData = pieDefault;
        };

        fixedPosition("chart-area4");

        var ctx2 = document.getElementById("chart-area4").getContext("2d");
        window.myPie = new Chart(ctx2).Pie(pieData, options);
        document.getElementById('js-legend4').innerHTML = myPie.generateLegend();
    };

    var pieDefault = [{
        value: 1,
        color: '#CFCFCF',
        highlight: "#e2e2e2",
        label: "No Data Currently"
    }];

    var options = {
        animateRotate: true,
        // animateScale: true,
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> Post",
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span class=\"chartjs-legend-label\" style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
    }

    var fixedPosition = function(element) {
        document.getElementById(element).width = "300";
        document.getElementById(element).height = "200";
    }

    $(document).ready(function() {
        postsPie();
        monthPie();
        categoryPie();
    });
});