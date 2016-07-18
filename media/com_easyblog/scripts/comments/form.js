EasyBlog.module('comments/form', function($) {

    var module = this;

    EasyBlog.require()
    .script('comments/captcha', 'comments/list')
    .library('markitup')
    .done(function($) {

        EasyBlog.Controller('Comments.Form', {
            defaultOptions: {

                "{formWrapper}": "[data-comment-form-wrapper]",
                "{form}": "[data-comment-form]",
                "{title}": "[data-comment-title]",
                "{name}": "[data-comment-name]",
                "{username}": "[data-comment-username]",
                "{password}": "[data-comment-password]",
                "{email}": "[data-comment-email]",
                "{register}": "[data-comment-register]",
                "{website}": "[data-comment-website]",
                "{counter}": "[data-comment-counter]",
                "{subscribe}" : "[data-comment-subscribe]",
                "{terms}": "[data-comment-terms]",
                "{tncLink}": "[data-comment-tnc]",
                "{parentId}" : "[data-comment-parent-id]",
                "{commentDepth}": "[data-comment-depth]",
                "{blogId}" : "[data-comment-blog-id]",
                "{depth}": "[data-comment-depth]",
                "{notice}": "[data-comment-notice]",
                "{editor}": "[data-comment-editor]",
                "{submit}": "[data-comment-submit]",
                "{formToken}": "[data-comment-token]",

                "{recaptcha}": "[data-recaptcha-item]"
            }
        }, function(self, opts, base) {

            return {

                init: function() {

                    self.initEditor();

                    self.list = self.addPlugin('list');

                    // If recaptcha is enabled, we should skip the normal captcha
                    var recaptcha = self.recaptcha.inside(self.element).length;

                    if (recaptcha < 1) {
                        self.captcha = self.addPlugin('captcha');
                    }
                },

                initEditor: function() {
                    if (self.editor().data('comment-bbcode') == 1) {
                        self.editor().markItUp(window.EasyBlogBBCodeSettings);
                    }
                },

                setNotice: function(message, type) {
                    var className = '';

                    if (type == 'error') {
                        className = 'alert-danger';
                    }

                    if (type == 'success') {
                        className = 'alert-success';
                    }

                    if (type == 'info') {
                        className = 'alert-info';
                    }

                    self.notice()
                        .removeClass('hide')
                        .addClass('alert ' + className)
                        .html(message);
                },

                resetForm: function() {
                    // If the comment form has a parent id, we need to reposition the comment form back.
                    var parentId = self.parentId().val();

                    if (parentId != 0) {
                        self.form().appendTo(self.formWrapper());
                    }

                    // Reset the form
                    self.username().val('');
                    self.password().val('');
                    self.subscribe().attr('checked', false);
                    self.editor().val('');
                    self.website().val('');
                    self.name().val('');
                    self.depth().val(0);
                    self.parentId().val(0);

                    self.trigger('resetForm');

                    // Determine if recaptcha is available
                    var recaptcha = self.recaptcha.inside(self.element);

                    // Get recaptcha's response
                    if (recaptcha.length > 0) {
                        grecaptcha.reset();
                    }
                },

                resetNotice: function() {
                    self.notice()
                        .removeClass('info error')
                        .html('');
                },

                "{self} replyComment": function(el, event, commentItem, commentId, commentDepth) {
                    // Hide notices in the reply form
                    self.notice().addClass('hide');

                    // When user tries to reply to an existing comment, move the form next to the level of the comment item
                    commentItem.after(self.form());

                    self.depth().val(commentDepth);

                    // Set the new parent id to the comment's id
                    self.parentId().val(commentId);
                },

                "{self} cancelReply": function(el, event, commentItem, commentId) {
                    // Set the parent id to 0
                    self.parentId().val(0);

                    // Reset the comment depth back to 0
                    self.depth().val(0);

                    // Relocate the form back to it's origin
                    self.formWrapper().html(self.form());
                },

                "{self} commentAdded": function()
                {
                    // Increment the counter
                    var count = self.counter().html();
                        count = parseInt(count) + 1;

                    self.counter().html(count.toString());
                    self.resetForm();
                },

                getValues: function() {

                    var data = {

                        title: self.title().val(),
                        name: self.name().val(),
                        email: self.email().val(),
                        username: self.username().val(),
                        password: self.password().val(),
                        website: self.website().val(),
                        subscribe: self.subscribe().is(':checked') ? 1 : 0,
                        register: self.register().is(':checked') ? 1 : 0,
                        comment: self.editor().val(),
                        terms: self.terms().is(':checked') ? 1 : 0,
                        depth: self.depth().val(),
                        parentId: self.parentId().val(),
                        blogId: self.blogId().val()
                    };

                    // token
                    // data[self.formToken().attr('name')] = 1;

                    // Determine if recaptcha is available
                    var recaptcha = self.recaptcha.inside(self.element);

                    // Get recaptcha's response
                    if (recaptcha.length > 0) {
                        data.recaptcha = grecaptcha.getResponse();
                    }

                    self.trigger('submitComment', [data]);
                    return data;
                },

                "{tncLink} click": function() {
                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/comments/terms')
                    })
                },

                "{submit} click" : function(el, event) {

                    event.preventDefault();

                    // Reset notices
                    self.resetNotice();

                    // Add loading indicator on the button
                    $(el).attr('disabled', true);

                    var tmp = $(el).html();

                    $(el).html('<i class="fa fa-repeat fa-spin"></i>');

                    // Get the form values
                    var data = self.getValues();

                    // Perform an ajax call to submit the comment
                    EasyBlog.ajax('site/views/comments/save', data)
                        .done(function(output, message, state) {

                            self.setNotice(message, state);
                            self.trigger('commentAdded',[output, data]);
                        })
                        .fail(function(message) {
                            self.setNotice(message, 'error');
                        })
                        .always(function(){
                            $(el).removeAttr('disabled');
                            $(el).html(tmp);

                            self.trigger('reloadCaptcha');
                        });

                    return false;
                }
            }
        });

        module.resolve();
    });
});
