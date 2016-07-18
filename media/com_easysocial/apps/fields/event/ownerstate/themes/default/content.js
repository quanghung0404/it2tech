EasySocial.ready(function($) {
    var base = $('[data-field-<?php echo $field->id; ?>]'),
        guestStates = base.find('[data-guest-state]'),
        value = base.find('[data-guest-state-value]');

    guestStates.on('click', function() {
        $.each(guestStates, function(index, element) {
            var el = $(element);

            el.removeClass(el.data('guestStateClass'));
        });

        var self = $(this);

        self.addClass(self.data('guestStateClass'));

        value.val(self.data('guestState'));
    });
});
