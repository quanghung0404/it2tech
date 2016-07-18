EasyBlog.module('comments/list', function($) {

    var module = this;

    EasyBlog.require()
    .library('markitup')
    .done(function($) {

        EasyBlog.Controller('Comments.List', {
            defaultOptions: {
                "{item}": "[data-comment-item]",
                "{list}": "[data-comment-list]",

                "{edit}": "[data-comment-edit]",
                
                "{editor}" : "[data-comment-edit-editor]",
                "{body}": "[data-comment-body]",
                "{cancelEdit}": "[data-comment-edit-cancel]",
                "{cancelReply}": "[data-comment-reply-cancel]", 
                "{update}": "[data-comment-edit-update]",
                "{preview}": "[data-comment-preview]",
                "{reply}": "[data-comment-reply]",
                "{like}": "[data-comment-like]",
                "{unlike}": "[data-comment-unlike]",

                "{deleteButton}": "[data-comment-delete]",
                "{likeCounter}": "[data-comment-like-counter]",
                "{likeTooltip}": "[data-comment-like-tooltip]",
                "{rawContent}": "[data-comment-edit-raw]",
                "{empty}": "[data-comment-empty]"
            }
        }, function(self, opts, base) {
            return {
                init: function()
                {
                },
                updatePreview: function(html)
                {
                    self.preview().html(html);
                },
                "{self} cancelEdit": function(el, event, item, formattedContents, rawContents)
                {
                    self.options.editing = false;
                    
                    // Hide the editor
                    item.find(self.editor.selector).addClass('hide');

                    // Display the body
                    item.find(self.body.selector).removeClass('hide');

                    // Restore the default value
                    item.find(self.editor.selector).find('textarea').val(item.find(self.rawContent.selector).html());
                },

                "{self} updateComment": function(el, event, item, formattedContents, rawContents)
                {
                    self.options.editing = false;

                    // Update the raw contents
                    item.find(self.rawContent.selector).html(rawContents);

                    // Update the textarea
                    item.find(self.editor.selector + ' textarea').val(rawContents);

                    // Update the preview
                    item.find(self.preview.selector).html(formattedContents);

                    // Hide the editor
                    item.find(self.editor.selector).addClass('hide');

                    // Display the body
                    item.find(self.body.selector).removeClass('hide');
                },

                "{self} commentAdded": function(el, event, commentItem, data)
                {
                    // Always hide the empty comment placeholder.
                    self.empty().hide();

                    // Check if this comment is a reply
                    if (data.parentId != 0) {

                        // Append this item to be after the parent.
                        var selector = self.item.selector + '[data-id="' + data.parentId + '"]';
                        
                        // Trigger the cancel reply because the comment was already posted on the site
                        $(selector).find(self.cancelReply.selector).click();

                        $(selector).after(commentItem);

                        return;
                    }

                    self.list().append(commentItem);
                },

                "{edit} click": function(editButton)
                {
                    var item = self.item.of(editButton),
                        itemId = item.data("id");

                    if (self.options.editing) {
                        self.options.editing = false;
                        item.find(self.editor.selector).addClass('hide');
                        item.find(self.body.selector).removeClass('hide');
                        return;
                    }

                    self.options.editing = true;
                    item.find(self.body.selector).addClass('hide');
                    item.find(self.editor.selector).removeClass('hide');

                    // Implement mark it up on editor
                    if (!item.find(self.editor.selector + ' textarea').hasClass('markItUpEditor')) {
                        item.find(self.editor.selector + ' textarea').show().markItUp(window.EasyBlogBBCodeSettings);    
                    }
                },

                "{update} click": function(updateButton)
                {
                    var item = self.item.of(updateButton),
                        itemId = item.data("id"),
                        value = item.find(self.editor.selector).find('textarea').val();

                    EasyBlog.ajax('site/views/comments/update', {"id" : itemId, "message" : value})
                    .done(function(formattedContents, rawContents)
                    {
                        item.find(self.preview.selector).html(formattedContents);
                        
                        self.trigger('updateComment', [item, formattedContents, rawContents]);
                    });
                },

                "{cancelReply} click": function(cancelReply)
                {
                    var item = self.item.of(cancelReply),
                        itemId = item.data("id");


                    // Hide the reply button
                    $(cancelReply).addClass('hide');

                    // Show the cancel reply button
                    item.find(self.reply.selector).removeClass('hide');

                    self.trigger('cancelReply', [item, itemId]);
                },

                "{cancelEdit} click": function(cancelButton)
                {
                    var item = self.item.of(cancelButton),
                        itemId = item.data("id");

                    self.trigger('cancelEdit', [item]);
                },

                "{reply} click": function(replyButton)
                {
                    var item = self.item.of(replyButton),
                        itemId = item.data("id"),
                        commentDepth = replyButton.data('depth');

                    self.trigger('replyComment', [item, itemId, commentDepth]);

                    // Hide the reply button
                    $(replyButton).addClass('hide');

                    // Show the cancel reply button
                    item.find(self.cancelReply.selector).removeClass('hide');
                },
                
                "{deleteButton} click": function(deleteButton) {

                    var item = self.item.of(deleteButton),
                        itemId = item.data("id");


                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/comments/confirmDelete', {"id" : itemId})
                    });
                },

                "{like} click": function(likeButton) {
                    var item = self.item.of(likeButton),
                        itemId = item.data('id');

                    EasyBlog.ajax('site/views/comments/like', {"id": itemId})
                    .done(function(str, count) {
                        
                        // Add liked class on the item
                        item.addClass('is-like');

                        // Update the tooltip
                        var counter = item.find(self.likeCounter.selector);

                        // Update the counter
                        counter.html(count);

                        // self.likeTooltip().data('original-title', str);
                    });
                },

                "{unlike} click": function(unlikeButton)
                {
                    var item = self.item.of(unlikeButton),
                        itemId = item.data('id');

                    EasyBlog.ajax('site/views/comments/unlike', {"id": itemId})
                    .done(function(str, count) {
                            
                        // Remove like class from the comment wrapper
                        item.removeClass('is-like');

                        // Update the tooltip
                        // self.likeTooltip().data('original-title', str);

                        // Update the tooltip
                        var counter = item.find(self.likeCounter.selector);

                        // Update the counter
                        counter.html(count);

                        // Update the counter
                        // self.likeCounter().html(count);
                    });
                    
                }
            }
        });

        module.resolve();
    });
});