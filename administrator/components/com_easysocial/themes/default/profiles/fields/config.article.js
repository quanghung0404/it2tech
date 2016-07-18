
EasySocial.require()
.library('dialog')
.done(function($) {

    var browserButton = $('[data-article-browser]');
    var articleTitle = $('[data-article-title]');
    var removeButton = $('[data-article-remove]');
    var articleValue = $('[data-fields-config-param-field-<?php echo $name;?>]');

    window.selectArticle = function(data) {

        articleTitle.val(data.title);
        articleValue.val(data.id);
        articleValue.trigger('change');

        EasySocial.dialog().close();
    };

    browserButton.on('click', function(event) {
        event.preventDefault();

        EasySocial.dialog({
            content: EasySocial.ajax('admin/views/articles/browse', {'jscallback': 'selectArticle'})
        });

    });

});