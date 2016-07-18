
EasyBlog.require()
.done(function($){

    $(document).on('click.search.reset', '[data-reset-search]', function() {

        $('[data-search-tag]').val('');
        $('[data-tags-form]').submit();
    });

    $(document).on('click.tags.sort', '[data-sort-item]', function() {

        var type = $(this).data('sort-type');

        $('[data-sort-value]').val(type);

        $('[data-tags-form]').submit();
    });

	$(document).on('click.tags.remove', '[data-tag-remove]', function() {

		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/dashboard/confirmDeleteTag', {"id": id})
		});
		
	});

    $('.eb-head-popover').popover();
});