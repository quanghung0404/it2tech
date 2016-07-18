ed.require(['edq', 'easydiscuss', 'jquery.fancybox'], function($, EasyDiscuss) {

    // Cancel button
    var wrapper = $('[<?php echo $editorId;?>]');
    var cancelButton = wrapper.find('[data-ed-reply-cancel]');

    if (cancelButton.length > 0) {
        cancelButton.live('click', function() {

            var container = wrapper.parents('[data-ed-reply-editor]');

            // Remove the html codes
            container.html('');

        });
    }

	// Reply submit
	var replyButton = wrapper.find('[data-ed-reply-submit]');

    // Reply counter
    var replyCounter = $('[data-ed-post-reply-counter]');

    // content highlighter syntax
    var syntaxHighlighter = <?php echo $this->config->get('main_syntax_highlighter') ? 'true' : 'false'; ?>;


    // create a function namespace attachments.initGallery()
    var attachments = {
        // reload attachment section
        initGallery: function(options) {
            $('.attachment-image-link').fancybox(options);
        },
        clear: function(el, ev) {

        }
    };

    var resetForm = function(form) {

        // Trigger reset form
        $(form).trigger('composer.form.reset');


        // Empty contents of editor
        form.find('textarea[name=dc_content]').val('');

        // Clear out CKEditor's content
        if (window.CKEDITOR) {
            try {
                window.CKEDITOR.instances['content'].setData('');
            } catch (e) {}
        }

        // Clear off attachments
        // var attachmentItem = el.parent('[data-ed-attachment-form]');
        // attachmentItem.empty();

        // // Polls
        // var pollController = $(form).find('.polls-tab').controller();

        // if (pollController) {
        //     $(form).find('.polls-tab').controller().resetPollForm();
        // }
    };

    var increaseReplyCount = function() {
        // Update the counter.
        var count = replyCounter.html();

        count = parseInt(count) + 1;

        replyCounter.html(count);
    };

    var setAlert = function(alert, message, state) {

        var newClass = state == 'error' ? 'o-alert--danger' : 'o-alert--success';

        alert
            .html(message)
            .removeClass('o-alert--success')
            .removeClass('o-alert--danger')
            .addClass(newClass);
    };

    function formValidateInputs(wrapper)
    {
        var notification = wrapper.find('[data-ed-composer-alert]');
        var content = wrapper.find('[data-ed-editor]');

        if(content.val() == '') {
            notification.removeClass('t-hidden');
            setAlert(notification, '<?php echo JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY'); ?>', 'error');
            return false;
        }

        // Get custom field data attribute
        var textbox = wrapper.find('[data-ed-textbox-fields]');
        var textarea = wrapper.find('[data-ed-textarea-fields]');
        var radio = wrapper.find('[data-ed-radio-fields]');
        var checkbox = wrapper.find('[data-ed-checkbox-fields]');
        var selectList = wrapper.find('[data-ed-select-fields]');
        var selectMultipleList = wrapper.find('[data-ed-select-multiple-fields]');

        // Check whether custom fields is it set as required
        var fields = wrapper.find('[data-ed-custom-fields-required]');

        var fieldsWrapper = fields.parents('[data-ed-custom-fields]');
        var fieldTab = fieldsWrapper.children('[data-ed-custom-fields-tab]');
        // Get the field required state
        var fieldsRequiredWrapper = fieldTab.siblings().find('[data-ed-custom-fields-required]');
        // Get all the custom field form groups
        var fieldGroup = fieldsRequiredWrapper.parents('[data-ed-custom-fields-required-group]');

        var inputRequired = false;

        // highlight the field tab
        var tabField = wrapper.find('[data-ed-ask-tabs]');
        var tabFieldError = tabField.children('[data-ed-tab-field-heading]');

        // re-check again if required field value already fill in
        tabFieldError.removeClass('has-error');
        fieldGroup.removeClass('has-error');

        if (fieldGroup.length > 0) {

            // Get each of the existing required custom fields
            fieldGroup.each(function(idx, el) {

                var field = $(el);
                var fType = field.data('field-type');

                if (fType == 'text' && textbox.val() == '') {
                    inputRequired = true;
                    field.addClass('has-error');
                }

                if (fType == 'area' && textarea.val() == '') {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'radio' && !radio.is(':checked')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'check' && !checkbox.is(':checked')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'select' && !selectList.is(':selected')) {
                    inputRequired = true;
                     field.addClass('has-error');
                }

                if (fType == 'multiple' && !selectMultipleList.is(':selected')) {
                    inputRequired = true;
                    field.addClass('has-error');
                }
            });

            // if there are some required field still empty value
            if (inputRequired) {

                tabFieldError.addClass('has-error');

                notification.removeClass('t-hidden');
                setAlert(notification, '<?php echo JText::_('COM_EASYDISCUSS_FIELDS_REQUIRED_FIELDS_NOT_PROVIDED'); ?>', 'error');
                return false;
            }
        }

        return true;
    };

    // Click reply submit
	replyButton.live('click', function() {

        // This is the reply button
        var replyButton = $(this);

        // Find the wrapper for the reply form
        var wrapper = replyButton.parents('[data-ed-composer-wrapper]');

        // Get the session token from the site to prevent CSRF attacks
        var token = $('[data-ed-token]').val();
        var target = '<?php echo JURI::root();?>index.php?option=com_easydiscuss&view=post&layout=<?php echo $operation == 'editing' ? 'update' : 'reply';?>&format=ajax&tmpl=component&' + token + '=1';

        // Given the wrapper, we need to find the form now
        var form = wrapper.find('[data-ed-composer-form]');
        var formDom = form[0];

        // Get the notification bar
        var notification = wrapper.find('[data-ed-composer-alert]');

        // Always hide alerts when they submit
        notification.addClass('t-hidden');

        //validate inputs
        if (! formValidateInputs(wrapper)){
            return;
        }

        // Create a temporary iframe to simulate postings as we have attachments
        var iframe = document.createElement('iframe');
        iframe.setAttribute('id', 'upload_iframe');
        iframe.setAttribute('name', 'upload_iframe');
        iframe.setAttribute('width', '0');
        iframe.setAttribute('height', '0');
        iframe.setAttribute('border', '0');
        iframe.setAttribute('style', 'width: 0; height: 0; border: none;');

        // Append the iframe into the <form>
        formDom.parentNode.appendChild(iframe);

        // assign name/id of the iframe
        window.frames['upload_iframe'].name = 'upload_iframe';
        var iframeId = document.getElementById('upload_iframe');

        // Temporarily disable the submit button.
        $(this).prop('disabled', true);

        // Get the replies wrapper
        var repliesWrapper = $('[data-ed-post-replies-wrapper]');
        var replies = $('[data-ed-post-replies]');

		// Add event handling for the iframe
        var eventHandler = function() {

            var content;

            if (iframeId.detachEvent) {
                iframeId.detachEvent('onload', eventHandler);
            } else {
            	// load event
                iframeId.removeEventListener('load', eventHandler, false);
            }

            // Message from server...
            if (iframeId.contentDocument) {
            	// assign the document into content variable
                content = iframeId.contentDocument;

            } else if (iframeId.contentWindow) {
                content = iframeId.contentWindow.document;

            } else if (iframeId.document) {
                content = iframeId.document;
            }

            // Search inside the document is it got 'ajaxResponse' = id="ajaxResponse"
            content = $(content).find('script#ajaxResponse').html();

            var result = $.parseJSON(content);

            // Update the alert
            setAlert(notification, result.message, result.type);

            // For editing success
            if (result.type == 'success.edit') {
                var parent = wrapper.parent();

                // Remove the editor
                parent.remove();

                // Now we need to replace the html contents back
                var replyItem = replies.find('[data-ed-reply-item][data-id=' + result.id + ']');
                replyItem.replaceWith(result.html);
            }

            // For success cases
			if (result.type == 'success') {

                // Remove any empty classes
                repliesWrapper.removeClass('is-empty');

                // Update the counter.
                increaseReplyCount();

                // What if the current sorting is oldest / latest ?
                // Append the result to the list.
                replies.append(result.html);

                // Reload the syntax highlighter.
                if (result.script != 'undefined') {
                    eval(result.script);
                }

                // Reset the form
                formDom.reset();

                // Clear the form.
                resetForm(form);
			};

			if (result.type == 'error.captcha') {
                setAlert(notification, result.message, 'error');
			};

            // Reload the lightbox for new contents
            attachments.initGallery({
                type: 'image',
                helpers: {
                    overlay: null
                }
            });

            // Check the syntax highlighter is it enable or not
            if (syntaxHighlighter) {
                Prism.highlightAll();
            }

            // Display the notification now
            notification.removeClass('t-hidden');

            // Activate the reply button
            replyButton.removeAttr('disabled');

            // Delete the iframe...
            setTimeout(function() {
                $(iframeId).remove();
            }, 250);
        };

        $(iframeId).load(eventHandler);

        // Set properties of form...
        formDom.setAttribute('target', 'upload_iframe');
        formDom.setAttribute('action', target);
        formDom.setAttribute('method', 'post');
        formDom.setAttribute('enctype', 'multipart/form-data');
        formDom.setAttribute('encoding', 'multipart/form-data');

        // Submit the form...
        formDom.submit();
	});

});
