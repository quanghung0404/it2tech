EasySocial.module('site/events/browser', function($) {
    var module = this;

    EasySocial
    .require()
    .library('popbox')
    .script('site/events/guestState')
    .language('COM_EASYSOCIAL_EVENTS_DETECTING_LOCATION')
    .view('site/loading/small')
    .done(function() {
        EasySocial.Controller('Events.Browser', {
            defaultOptions: {
                '{filters}': '[data-events-filters] > li',
                '{content}': '[data-events-content]',
                '{list}': '[data-events-list]',

                '{items}': '[data-events-item]',

                '{sort}': '[data-events-sorting] a',
                '{calendar}': '[data-events-calendar]',

                '{pastFilter}': '[data-events-past]',
                '{pastLink}': '[data-events-past-link]',

                '{prevDate}': '[data-events-nav-prevdate]',

                '{nextDate}': '[data-events-nav-nextdate]',

                '{radius}': '[data-events-radius]',

                '{nearbyTitle}': '[data-events-nearby-title]',

                filter: null,
                categoryid: 0,

                delayed: false,

                includePast: false,
                ordering: 'start',

                hasLocation: false,
                userLatitude: '',
                userLongitude: '',

                distance: 10,

                group: null,

                view: {
                    loadingContent: 'site/loading/small'
                }
            }
        }, function(self) {
            return {
                init: function() {
                    self.options.filter = self.element.data('filter');
                    self.options.categoryid = self.element.data('categoryid');

                    // Render the calendar
                    self.renderCalendar();

                    self.initItems();

                    if (self.options.delayed) {
                        self.delayedInit();
                    }
                },

                delayedInit: function() {
                    // It is possible that view is flagging it as "delayed" in order for javascript to make an ajax call to retrieve the data instead

                    // delayed init will have some preset parameter coming from url, hence we don't use the filterbynearby method

                    if (self.options.filter === 'nearby') {
                        var getEvents = function() {
                            EasySocial.ajax('site/controllers/events/getEvents', {
                                filter: self.options.filter,
                                latitude: self.options.userLatitude,
                                longitude: self.options.userLongitude,
                                distance: self.options.distance,
                                ordering: self.options.ordering,
                                includePast: self.options.includePast
                            }).done(function(contents) {
                                self.element.removeClass('loading');

                                self.content().html(contents);

                                self.initItems();
                            });
                        }

                        if (self.options.hasLocation && self.options.userLatitude && self.options.userLongitude) {
                            return getEvents();
                        }

                        // If no location, then we need to resolve a location first

                        // Show the image of "detecting location"

                        EasySocial.require().library('gmaps').done(function() {
                            $.GMaps.geolocate({
                                success: function(position) {
                                    self.options.userLatitude = position.coords.latitude;

                                    self.options.userLongitude = position.coords.longitude;

                                    return getEvents();
                                }
                            });
                        });
                    }
                },

                renderCalendar: function() {
                    EasySocial.ajax('site/views/events/renderCalendar', {})
                        .done(function(html) {
                            self.calendar()
                                .html(html)
                                .addController('EasySocial.Controller.Events.Browser.Calendar', {
                                    '{parent}': self
                                });

                            self.calendar().trigger('calendarLoaded');
                        });
                },

                initItems: function() {
                    self.items().addController('EasySocial.Controller.Events.Browser.Item', {
                        '{parent}': self
                    });
                },

                '{filters} click': function(el, event) {
                    event.preventDefault();

                    self.filters().removeClass('active');

                    el.addClass('active');

                    self.content().html('&nbsp;');

                    // Update the url in the address bar
                    el.find('a').route();

                    self.options.filter = el.data('events-filters-type'),
                    self.options.categoryid = el.data('events-filters-categoryid');

                    // Nearby requires separate processing
                    if (self.options.filter == 'nearby') {
                        return self.filterByNearby();
                    }

                    // Add loading class on container
                    self.element.addClass('loading');

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: self.options.filter,
                        categoryid: self.options.categoryid
                    }).done(function(contents) {

                        // Remove the loading from the container
                        self.element.removeClass('loading');

                        self.content().html(contents);

                        self.initItems();
                    });
                },

                filterByNearby: function() {
                    var getEvents = function() {
                        EasySocial.ajax('site/controllers/events/getEvents', {
                            filter: self.options.filter,
                            latitude: self.options.userLatitude,
                            longitude: self.options.userLongitude
                        }).done(function(contents) {
                            self.element.removeClass('loading');

                            self.content().html(contents);

                            self.initItems();
                        });
                    }

                    if (self.options.hasLocation && self.options.userLatitude && self.options.userLongitude) {
                        self.element.addClass('loading');

                        return getEvents();
                    }

                    // If no location, then we need to resolve a location first

                    // Show a detecting location
                    self.content().html('<div class="es-detecting-location"><i class="fa fa-globe es-muted"></i> ' + $.language('COM_EASYSOCIAL_EVENTS_DETECTING_LOCATION') + ' <i class="icon-loader"></i></div>');

                    // Show the image of "detecting location"

                    EasySocial.require().library('gmaps').done(function() {
                        $.GMaps.geolocate({
                            success: function(position) {
                                self.options.userLatitude = position.coords.latitude;

                                self.options.userLongitude = position.coords.longitude;

                                self.options.hasLocation = true;

                                return getEvents();
                            }
                        });
                    });
                },

                '{sort} click': function(el, event) {
                    event.preventDefault();

                    self.sort().removeClass('active');

                    el.addClass('active');

                    // self.element.addClass('loading');

                    self.list().html(self.view.loadingContent());

                    var ordering = el.data('ordering'),
                        filter = el.data('filter'),
                        categoryid = el.data('categoryid'),
                        includePast = self.pastFilter().is(':checked') ? 1 : 0;

                    self.setPastLink();

                    self.setSortLink();

                    el.route();

                    if (filter === 'nearby') {
                        EasySocial.ajax('site/controllers/events/getEvents', {
                            filter: self.options.filter,
                            latitude: self.options.userLatitude,
                            longitude: self.options.userLongitude,
                            distance: self.options.distance,
                            ordering: ordering,
                            sort: 1,
                            includePast: includePast
                        }).done(function(contents) {
                            self.list().html(contents);

                            self.initItems();
                        });

                        return;
                    }

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: filter,
                        categoryid: categoryid,
                        ordering: ordering,
                        sort: 1,
                        includePast: includePast,
                        group: self.options.group
                    }).done(function(contents) {
                        self.list().html(contents);

                        self.initItems();
                    });
                },

                '{pastFilter} change': function(el) {
                    var activeSort = self.sort('.active'),
                        includePast = el.is(':checked') ? 1 : 0,
                        ordering = activeSort.data('ordering'),
                        filter = activeSort.data('filter'),
                        categoryid = activeSort.data('categoryid');

                    self.list().html(self.view.loadingContent());

                    self.pastLink().route();

                    self.setPastLink();

                    self.setSortLink();

                    if (filter === 'nearby') {
                        EasySocial.ajax('site/controllers/events/getEvents', {
                            filter: self.options.filter,
                            latitude: self.options.userLatitude,
                            longitude: self.options.userLongitude,
                            distance: self.options.distance,
                            ordering: ordering,
                            sort: 1,
                            includePast: includePast
                        }).done(function(contents) {
                            self.list().html(contents);

                            self.initItems();
                        });

                        return;
                    }

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: filter,
                        categoryid: categoryid,
                        ordering: ordering,
                        sort: 1,
                        includePast: includePast,
                        group: self.options.group
                    }).done(function(contents) {
                        self.list().html(contents);

                        self.initItems();
                    });
                },

                '{pastLink} click': function(el, ev) {
                    ev.preventDefault();

                    self.pastFilter().trigger('click');
                },

                setPastLink: function() {
                    var pastLink = self.pastLink(),
                        includePast = self.pastFilter().is(':checked') ? 1 : 0,
                        ordering = self.sort('.active').data('ordering');

                    var link = pastLink.data(ordering + '-' + (includePast ? 'nopast' : 'past'));

                    pastLink.attr('href', link);
                },

                setSortLink: function() {
                    var includePast = self.pastFilter().is(':checked') ? 1 : 0;

                    $.each(self.sort(), function(i, el) {
                        var el = $(el);
                        el.attr('href', self.pastLink().data(el.data('ordering') + '-' + (includePast ? 'past' : 'nopast')));
                    });
                },

                '{prevDate} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.filters().removeClass('active');

                    self.element.addClass('loading');

                    self.content().html('&nbsp;');

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: 'date',
                        date: el.data('events-nav-prevdate')
                    }).done(function(contents, options) {

                        // Remove the loading from the container
                        self.element.removeClass('loading');

                        self.content().html(contents);

                        self.initItems();

                        if (options.isToday) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="date"]').addClass('active');
                        }

                        if (options.isTomorrow) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="tomorrow"]').addClass('active');
                        }

                        if (options.isCurrentMonth) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="month"]').addClass('active');
                        }

                        if (options.isCurrentYear) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="year"]').addClass('active');
                        }
                    });
                },

                '{nextDate} click': function(el, ev) {
                    ev.preventDefault();

                    el.route();

                    self.filters().removeClass('active');

                    self.element.addClass('loading');

                    self.content().html('&nbsp;');

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: 'date',
                        date: el.data('events-nav-nextdate')
                    }).done(function(contents, options) {

                        // Remove the loading from the container
                        self.element.removeClass('loading');

                        self.content().html(contents);

                        self.initItems();

                        if (options.isToday) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="date"]').addClass('active');
                        }

                        if (options.isTomorrow) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="tomorrow"]').addClass('active');
                        }

                        if (options.isCurrentMonth) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="month"]').addClass('active');
                        }

                        if (options.isCurrentYear) {
                            self.filters().removeClass('active');

                            self.filters('[data-events-filters-type="year"]').addClass('active');
                        }
                    });
                },

                '{radius} change': function(el, ev) {
                    var activeSort = self.sort('.active'),
                        includePast = self.pastFilter().is(':checked') ? 1 : 0,
                        ordering = activeSort.data('ordering'),
                        filter = activeSort.data('filter'),
                        categoryid = activeSort.data('categoryid'),
                        distance = el.val();

                    self.options.distance = distance;

                    self.list().html(self.view.loadingContent());

                    // self.pastLink().route();

                    // self.setPastLink();

                    // self.setSortLink();

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: 'nearby',
                        latitude: self.options.userLatitude,
                        longitude: self.options.userLongitude,
                        distance: self.options.distance,
                        ordering: ordering,
                        includePast: includePast,
                        sort: 1
                    }).done(function(contents, options) {
                        self.list().html(contents);

                        self.initItems();

                        History.pushState({state:1}, document.title, options.hrefs[ordering][includePast ? 'past' : 'nopast']);

                        self.pastLink().attr('href', options.hrefs[ordering][includePast ? 'nopast' : 'past']);

                        $.each(self.sort(), function(i, el) {
                            var el = $(el);
                            el.attr('href', options.hrefs[el.data('ordering')][includePast ? 'past' : 'nopast']);

                            self.pastLink().attr('data-' + el.data('ordering') + '-past', options.hrefs[el.data('ordering')]['past']);
                            self.pastLink().attr('data-' + el.data('ordering') + '-nopast', options.hrefs[el.data('ordering')]['nopast']);
                        });

                        self.nearbyTitle().text(options.title);
                    });
                }
            }
        });

        EasySocial.Controller('Events.Browser.Item', {
            defaultOptions: {
                id: null,

                '{action}': '[data-item-action]',

                '{unfeature}': '[data-item-unfeature]',
                '{feature}': '[data-item-feature]',
                '{unpublish}': '[data-item-unpublish]',
                '{delete}': '[data-item-delete]',

                '{guestStateWrap}': '[data-guest-state-wrap]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.options.id = self.element.data('id');

                    self.initGuestStates();
                },

                initGuestStates: function() {
                    self.guestStateWrap().addController('EasySocial.Controller.Events.GuestState');
                },

                '{action} click': function(el) {
                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/events/itemActionDialog', {
                            id: self.options.id,
                            action: el.data('item-action'),
                            from: 'list'
                        })
                    });
                },

                '{delete} click': function() {
                    EasySocial.dialog({
                        content: EasySocial.ajax('site/views/events/deleteEventDialog', {
                            id: self.options.id
                        })
                    });
                }
            }
        });

        EasySocial.Controller('Events.Browser.Calendar', {
            defaultOptions: {
                '{nav}': '[data-calendar-nav]',

                '{day}': '.day',

                '{month}': '[data-month]',

                view: {
                    loading: 'site/loading/small'
                }
            }
        }, function(self) {
            return {
                init: function() {

                },

                '{self} calendarLoaded': function() {
                    self.day('.has-events').each(function(index, el) {
                        el = $(el);

                        var content = el.find('.event-details').html();

                        el.popbox({
                            content: content,
                            id: 'fd',
                            component: 'es',
                            type: 'events-calendar-filter',
                            position: 'bottom-left',
                            toggle: 'hover'
                        });
                    });

                    var month = self.month('.has-events');

                    if (month.length > 0) {
                        var content = month.find('.event-details').html();
                        month.popbox({
                            content: content,
                            id: 'fd',
                            component: 'es',
                            type: 'events-calendar-filter',
                            position: 'bottom-left',
                            toggle: 'hover'
                        });
                    }
                },

                '{nav} click': function(el, ev) {
                    var date = el.data('calendar-nav');

                    self.element.html(self.view.loading());

                    EasySocial.ajax('site/views/events/renderCalendar', {
                        date: date
                    }).done(function(html) {
                        self.element
                            .html(html)
                            .trigger('calendarLoaded');
                    });
                },

                '{day} click': function(el, ev) {
                    ev.preventDefault();

                    // Update the url in the address bar
                    el.find('a[data-route]:first').route();

                    self.loadEvents(el.data('date'));
                },

                '{day} popboxActivate': function(el, ev, popbox) {
                    popbox.tooltip.find('a[data-route]').on('click', function(event) {
                        event.preventDefault();

                        $(this).route();

                        self.loadEvents($(this).data('date'));
                    });
                },

                '{month} click': function(el, ev) {
                    ev.preventDefault();

                    // Update the url in the address bar
                    el.find('a[data-route]:first').route();

                    self.loadEvents(el.data('month'));
                },

                '{month} popboxActivate': function(el, ev, popbox) {
                    popbox.tooltip.find('a[data-route]').on('click', function(event) {
                        event.preventDefault();

                        $(this).route();

                        self.loadEvents($(this).data('month'));
                    });
                },

                loadEvents: function(date) {
                    self.parent.filters().removeClass('active');

                    // Add loading class on container
                    self.parent.element.addClass('loading');
                    self.parent.content().html('&nbsp;');

                    EasySocial.ajax('site/controllers/events/getEvents', {
                        filter: 'date',
                        date: date
                    }).done(function(contents, options) {

                        // Remove the loading from the container
                        self.parent.element.removeClass('loading');

                        self.parent.content().html(contents);

                        self.parent.initItems();

                        if (options.isToday) {
                            self.parent.filters().removeClass('active');

                            self.parent.filters('[data-events-filters-type="date"]').addClass('active');
                        }

                        if (options.isTomorrow) {
                            self.parent.filters().removeClass('active');

                            self.parent.filters('[data-events-filters-type="tomorrow"]').addClass('active');
                        }

                        if (options.isCurrentMonth) {
                            self.parent.filters().removeClass('active');

                            self.parent.filters('[data-events-filters-type="month"]').addClass('active');
                        }

                        if (options.isCurrentYear) {
                            self.parent.filters().removeClass('active');

                            self.parent.filters('[data-events-filters-type="year"]').addClass('active');
                        }
                    });
                }
            }
        });

        module.resolve();
    });
});
