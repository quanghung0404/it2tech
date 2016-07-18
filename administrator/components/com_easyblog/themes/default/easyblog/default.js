
EasyBlog.require()
.library('flot')
.done(function($) {
    $.Joomla('submitbutton', function(task) {
        
        if (task == 'new') {
            EasyBlog.ComposerLauncher.open('<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=composer&tmpl=component');
            return false;
        }

        if (task == 'purgeCache') {
            EasyBlog.dialog({
                content: EasyBlog.ajax('admin/views/easyblog/confirmPurgeCache')
            });
        }

        return false;
    });

    $('[data-approve-post]').on('click', function(){
        var id = $(this).data('id');

        EasyBlog.dialog({
            content: EasyBlog.ajax('admin/views/blogs/confirmAccept', {"id" : id})
        });
    });

    $('[data-reject-post]').on('click', function(){

        var id = $(this).data('id');
        
        EasyBlog.dialog({
            content: EasyBlog.ajax('admin/views/blogs/confirmReject', {"id" : id})
        });
    });

    var comments = <?php echo $commentsCreated;?>;
    var commentsData = [{ data: comments, label: "<?php echo JText::_('COM_EASYBLOG_CHART_COMMENTS', true);?>" }];

    $('[data-chart-comments]').plot(commentsData, {

        series: {
            lines: { show: true,
                    lineWidth: 1,
                    fill: true, 
                    fillColor: { colors: [ { opacity: 0.1 }, { opacity: 0.13 } ] }
                 },
            points: { show: true, 
                     lineWidth: 2,
                     radius: 3
                 },
            shadowSize: 0,
            stack: true
        },
        grid: { 
            hoverable: true, 
            clickable: true, 
            tickColor: "#f9f9f9",
            borderWidth: 0,
            backgroundColor: "#fff",
        },
        colors: ["#a7b5c5", "#30a0eb"],
        xaxis: {
            min: 0.0,
            max: 6,
            //mode: null,
            ticks: <?php echo $commentsTicks;?>,
            tickLength: 0, // hide gridlines
            axisLabelUseCanvas: true,
            tickDecimals: 0,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5
        },
        yaxis: {
            tickDecimals: 0
        },
        shadowSize: 0
    });

    var posts = <?php echo $postsCreated;?>;
    var data = [{ data: posts, label: "<?php echo JText::_('COM_EASYBLOG_CHART_POSTS', true);?>" }];

    $('[data-chart-posts]').plot(data, {

        series: {
            lines: { show: true,
                    lineWidth: 1,
                    fill: true, 
                    fillColor: { colors: [ { opacity: 0.1 }, { opacity: 0.13 } ] }
                 },
            points: { show: true, 
                     lineWidth: 2,
                     radius: 3
                 },
            shadowSize: 0,
            stack: true
        },
        grid: { 
            hoverable: true, 
            clickable: true, 
            tickColor: "#f9f9f9",
            borderWidth: 0,
            backgroundColor: "#fff",
        },
        // legend: {
        //     sorted: "asc",
        //     container: $("[data-chart-posts-legend]" ),
        //     backgroundColor: "#fff",
        //     backgroundOpacity: 1
        // },
        colors: ["#a7b5c5", "#30a0eb"],
        xaxis: {
            min: 0.0,
            max: 6,
            //mode: null,
            ticks: <?php echo $postsTicks;?>,
            tickLength: 0, // hide gridlines
            axisLabelUseCanvas: true,
            tickDecimals: 0,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5
        },
        yaxis: {
            tickDecimals: 0
        },
        shadowSize: 0
    });

});