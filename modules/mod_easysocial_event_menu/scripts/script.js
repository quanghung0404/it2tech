EasySocial.require().library('dialog').done(function($)
{
    var pendingList = $('.es-event-menu-pending');

    $('[data-event-menu-approve]').on('click',function() {
        var el = $(this),
            id = el.data('id');

        EasySocial.dialog({
            content: EasySocial.ajax('site/views/events/confirmApproveGuest', {
                "id": id
            }),
            bindings: {
                '{approveButton} click': function() {
                    EasySocial.ajax('site/controllers/events/approveGuest', {
                        'id': id
                    })
                    .done(function() {
                        EasySocial.dialog().close();

                        // Remove guest from the pending list
                        el.parents('li').remove();

                        pendingList.find('li').length === 0 && pendingList.remove();
                    });
                }
            }
        });
    });

    $('[data-event-menu-reject]').on('click',function() {
        var el = $(this),
            id = el.data('id');

        EasySocial.dialog({
            content: EasySocial.ajax('site/views/events/confirmRejectGuest', {
                "id": id
            }),
            bindings: {
                '{approveButton} click': function() {
                    EasySocial.ajax('site/controllers/events/rejectGuest', {
                        'id': id
                    })
                    .done(function() {
                        EasySocial.dialog().close();

                        // Remove guest from the pending list
                        el.parents('li').remove();

                        pendingList.find('li').length === 0 && pendingList.remove();
                    });
                }
            }
        });
    });
});
