EasySocial.module('site/events/item', function($) {
    var module = this;

    EasySocial.template('info/item', '<li data-sidebar-item><a class="ml-20" href="[%= url %]" title="[%= title %]" data-info-item data-info-index="[%= index %]"><i class="fa fa-info-circle mr-5"></i> [%= title %]</a></li>');

    EasySocial
    .require()
    .script('site/friends/suggest', 'site/events/guestState')
    .view('site/loading/small')
    .library('history')
    .done(function($) {
        EasySocial.Controller('Events.Item', {
            defaultOptions: {
                id: null,

                '{filterStreamList}': '[data-filter-stream-list]',

                '{sidebarItem}': '[data-sidebar-item]',

                '{addFilter}': '[data-filter-add]',

                '{editFilter}': '[data-filter-edit]',

                '{filterStream}': '[data-filter-stream]',

                '{filterApp}': '[data-filter-app]',

                '{showAllFilters}': '[data-filter-showall]',

                '{apps}': '[data-app-item]',

                '{content}': '[data-content]',

                '{saveHashtag}': '[data-hashtag-filter-save]',

                '{invite}': '[data-action-invite]',

                '{unpublish}': '[data-action-unpublish]',

                '{delete}': '[data-action-delete]',

                '{guestStateWrap}': '[data-guest-state-wrap]',

                '{info}': '[data-info]',

                '{infoItem}': '[data-info-item]',

                "{menuItem}"    : "[data-dashboardSidebar-menu]",

                view: {
                    infoItem: 'info/item',
                    loading: 'site/loading/small'
                }
            }
        }, function(self) {
            return {
                init: function() {
                    self.initGuestStates();
                },

                initGuestStates: function() {
                    self.guestStateWrap().addController('EasySocial.Controller.Events.GuestState');
                },

                setActive: function(el) {
                    self.sidebarItem().removeClass('active');

                    el.parents(self.sidebarItem.selector).addClass('active');
                },

                setLoading: function(el) {
                    self.content().html('');

                    self.element.addClass('loading');
                },

                updateContents: function(html) {
                    self.element.removeClass('loading');

                    self.content().html(html);
                },

                '{showAllFilters} click': function(el, ev) {
                    el.hide();

                    self.sidebarItem().show();
                },

                '{editFilter} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setLoading();

                    self.getFilter(el.data('id'));

                    self.setActive(el.parents('[data-sidebar-item]'));
                },

                '{addFilter} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    self.getFilter(0);
                },

                getFilter: function(id) {
                    EasySocial.ajax('site/controllers/events/getFilter', {
                        filterId: id,
                        eventId: self.options.id
                    }).always(function(contents) {
                        self.updateContents(contents);
                    });
                },

                '{filterApp} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    self.getStream(el.data('id'), 'apps');
                },

                '{filterStream} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    self.getStream(el.data('id'), el.data('type'));
                },

                "{menuItem} click" : function( el , event )
                {
                    // Remove all active class.
                    self.menuItem().removeClass( 'active' );

                    // Add active class on this item.
                    $( el ).addClass( 'active' );
                },

                getStream: function(id, type) {
                    EasySocial.ajax('site/controllers/events/getStream', {
                        id: id,
                        type: type,
                        view: "events",
                        eventId: self.options.id
                    }).always(function(contents) {
                        self.updateContents(contents);
                    });
                },

                '{saveHashtag} click': function(el) {
                    var tag = el.data('tag');

                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/stream/confirmSaveFilter', {
                            tag: tag
                        }),
                        bindings: {
                            '{saveButton} click': function() {
                                this.inputWarning().hide();

                                var filterName = this.inputTitle().val();

                                if (filterName == '') {
                                    this.inputWarning().show();
                                    return;
                                }

                                EasySocial.ajax('site/controllers/events/addFilter', {
                                    title: filterName,
                                    tag: tag,
                                    id: self.options.id
                                }).done(function(html, msg) {
                                    var item = $.buildHTML(html);

                                    self.filterStreamList().append(item);

                                    EasySocial.dialog(msg);

                                    setTimeout(function() {
                                        EasySocial.dialog().close();
                                    }, 2000);
                                });
                            }
                        }
                    });
                },

                '{apps} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    EasySocial.ajax('site/controllers/events/getAppContents', {
                        appId: el.data('app-id'),
                        eventId: self.options.id
                    }).always(function(contents) {
                        self.updateContents(contents);
                    });
                },

                '{info} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    var loaded = el.data('loaded');

                    if (loaded) {
                        self.infoItem().eq(0).trigger('click');
                        return;
                    }

                    if (el.enabled()) {
                        el.disabled(true);

                        EasySocial.ajax('site/controllers/events/initInfo', {
                            eventId: self.options.id
                        }).done(function(steps) {
                            el.data('loaded', 1);

                            var parent = el.parent('[data-sidebar-item]');

                            // Append all the steps
                            $.each(steps.reverse(), function(index, step) {
                                if (!step.hide) {
                                    parent.after(self.view.infoItem({
                                        url: step.url,
                                        title: step.title,
                                        index: step.index
                                    }));
                                }

                                if (step.html) {
                                    self.updateContents(step.html);
                                    self.content().find('[data-field]').trigger('onShow');
                                }
                            });

                            var item = self.infoItem().eq(0);

                            self.setActive(item);

                            // Have to set the title
                            $(document).prop('title', item.attr('title'));

                            el.enabled(true);
                        }).fail(function(error) {
                            el.enabled(true);
                            self.updateContents(error.message);
                        });
                    }
                },

                '{infoItem} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.setActive(el);

                    self.setLoading();

                    var index = el.data('info-index');

                    EasySocial.ajax('site/controllers/events/getInfo', {
                        eventId: self.options.id,
                        index: index
                    }).done(function(contents) {
                        self.updateContents(contents);

                        self.content().find('[data-field]').trigger('onShow');
                    }).fail(function(error) {
                        self.updateContents(error.message);
                    });
                },

                '{invite} click': function(el, ev) {
                    EasySocial.dialog({
                        content: self.view.loading(),
                        width: 400,
                        heigth: 150
                    });

                    EasySocial.ajax('site/views/events/inviteFriendsDialog', {
                        'id' : self.options.id
                    }).done(function(content) {
                        EasySocial.dialog({
                            content: content
                        });
                    });
                },

                '{unpublish} click': function(el, ev) {
                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/events/unpublishEventDialog', {
                            id: self.options.id
                        })
                    });
                },

                '{delete} click': function(el, ev) {
                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/events/deleteEventDialog', {
                            id: self.options.id
                        })
                    });
                }
            }
        });

        module.resolve();
    });
});
