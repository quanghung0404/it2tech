ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var options = {
        limit: 0,
        editable: false,
        types: {
            'image': ["jpg","png","gif"],
            'archive': ["zip","rar","gz","gzip"],
            'pdf': ["pdf"]
        }
    };

    var wrapper = $('[<?php echo $editorId;?>] [data-ed-attachments]');

    // Clone the form once
    var clonedForm = wrapper.find('[data-ed-attachment-form]').clone();

    // Get the file input
    var fileInput = wrapper.find('[data-attachment-item-input]');

    // Get the attachment limit
    var limitEnabled = <?php echo $this->config->get('enable_attachment_limit'); ?>;
    var limit = <?php echo $this->config->get('attachment_limit'); ?>;

    var info = wrapper.find('[data-ed-attachment-info]');

    var list = wrapper.find('[data-ed-attachments-list]');

    fileInput.live('change', function() {
        var el = $(this);
        var form = el.parents('[data-ed-attachment-form]');

        // Insert a new item on the result
        insertAttachment(form);
    });

    // When a reply form is edited / replied, reset the form
    $(document)
    .on('composer.form.reset', '[data-ed-composer-form]', function(){

        // If there is attachment form, remove it
        wrapper.find('[data-ed-attachment-form]').remove();
        
        // get back the cloned form
        var form = clonedForm.clone();

        // Re-append a new form
        wrapper.append(form);

        // Reset the info dom
        info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
     
    });

    var getAttachmentType = function(filename) {

        var extension = filename.substr((filename.lastIndexOf('.') + 1));
        var type = 'default';

        // Image type
        if ($.inArray(extension, options.types.image) != -1) {
            type = 'image';
        }

        // Archive type
        if ($.inArray(extension, options.types.archive) != -1) {
            type = 'archive';
        }

        // Archive type
        if ($.inArray(extension, options.types.pdf) != -1) {
            type = 'pdf';
        }

        return type;
    };

    var insertAttachment = function(form) {

        var fileInput = form.find("input:not(:hidden)");

        // Get the file attributes
        var file = {
            title: fileInput.val(),
            type: getAttachmentType(fileInput.val())
        };

        // Chrome fix
        if (file.title.match(/fakepath/)) {
            file.title = file.title.replace(/C:\\fakepath\\/i, '');
        }

        // Set the file title
        var title = form.children('[data-ed-attachment-item-title]');
        title.html(file.title);

        // Add the attachment type class
        form
            .removeClass('ed-attachment-form')
            .addClass('attachment-type-' + file.type)

        // Add it into the list
        form.appendTo(list);

        var itemCount = list.find('.attachment-item');

        // if reached the limit, don't reset form
        if (itemCount.length < limit || !limitEnabled) {
            // Once it is added, we want to attach a new form to the list
            resetAttachmentForm();
        } else {
            info.html('<?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT') ?>');

        }
        
    };

    var resetAttachmentForm = function() {

        var form = clonedForm.clone();

        // Re-append a new form
        wrapper.append(form);
    };


    // Removing an attachment item
    var removeItem = wrapper.find('[data-ed-attachment-item-remove]');

    removeItem.live('click', function() {

        var item = $(this);
        var itemWrapper = item.parents('.attachment-item');
        var id = item.data('id');

        if (!id) {
            itemWrapper.remove();

            // Get the attachment count
            var itemCount = list.find('.attachment-item');
            var diff = limit - itemCount.length;

            // if it does not reach the limit, add the form
            if (diff == 1 && limitEnabled) {
                // Once it is removed, we want to attach a new form to the list
                resetAttachmentForm();
                
                info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
            } 
            return;
        }

        EasyDiscuss.dialog({
            "content": EasyDiscuss.ajax('site/views/attachments/confirmDelete', {"id": id}),
            "bindings": {
                "{submitButton} click": function() {

                    // Hide the dialog
                    EasyDiscuss.dialog().close();

                    // Remove the item
                    EasyDiscuss.ajax('site/views/attachments/delete', {
                        "id": id
                    }).done(function(){
                        itemWrapper.remove();

                        // Get the attachment count
                        var itemCount = list.find('.attachment-item');
                        var diff = limit - itemCount.length;

                        // if it does not reach the limit, add the form
                        if (diff == 1 && limitEnabled) {
                            // Once it is removed, we want to attach a new form to the list
                            resetAttachmentForm();
                            
                            info.html('<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>');
                        } 
                    });
                }
            }
        });

    });

});
