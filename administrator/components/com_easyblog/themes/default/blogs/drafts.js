
EasyBlog.require()
.script('admin/grid')
.done(function($)
{
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$.Joomla("submitbutton", function(action) {


        // Get selected list items.
        var selected    = new Array;

        $('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
            selected.push($(el).val());
        });

		$.Joomla("submitform", [action]);

	});

});
