
EasySocial.require()
.script('admin/grid/grid')
.done(function($) {

	// Implement the grid system
	$('[data-table-grid]').implement(EasySocial.Controller.Grid);


    <?php if ($this->tmpl == 'component') { ?>

        $('[data-video-insert]').on('click', function(event) {
            
            event.preventDefault();

            // Supply all the necessary info to the caller
            var element = $(this);
            var data = {
                        "id": element.data('id'),
                        "title" : element.data('title'),
                        "alias" : element.data('alias')
                    };

            window.parent["<?php echo JRequest::getCmd('jscallback');?>" ](data);
        });
        
    <?php } else { ?>
        $.Joomla('submitbutton', function(task){

            var ids = [];

            $('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i, el) {
                var val = $(el).val();
                ids.push(val);
            });

            if (task == 'remove') {
                EasySocial.dialog({
                    "content": EasySocial.ajax('admin/views/videos/confirmDelete', {"ids": ids})
                });

                return;
            }

            $.Joomla('submitform', [task]);
        });
    <?php } ?>

});