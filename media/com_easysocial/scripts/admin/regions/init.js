EasySocial.module('admin/regions/init', function($) {
    var module = this;

    EasySocial.Controller('Region.Init', {
        defaultOptions: {
            callback: function() {},

            '{startButton}': '[data-start]',
            '{table}': '[data-table]',
            '{tableBody}': '[data-table-body]',
            '{row}': '[data-table-row]'
        }
    }, function(self) {
        return {
            init: function() {
                self.row().addController('EasySocial.Controller.Region.Init.Row');
            },

            '{startButton} click': function(el, ev) {
                el.hide();

                self.table().show();

                self.counter = 0;
                self.progress = $.Deferred()
                    .done(function() {
                        self.options.callback();
                    });

                self.process();
            },

            process: function() {
                var row = self.row().eq(self.counter);

                if (row.length === 0) {
                    return self.progress.resolve();
                }

                row.show();

                EasySocial.ajax('admin/controllers/regions/initialise', {
                    key: row.data('key')
                }).done(function() {
                    row.trigger('updateStatus', [1]);

                    self.counter++;

                    self.process();
                });
            }
        }
    });

    EasySocial.Controller('Region.Init.Row', {
        defaultOptions: {
            '{title}': '[data-row-title]',
            '{status}': '[data-row-status]',
            '{icon}': '[data-row-icon]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            statuses: ['label-danger', 'label-success', 'label-warning'],
            icons: ['fa-exclamation-triangle', 'fa-check', 'fa-wrench'],

            '{self} updateStatus': function(el, ev, state) {
                var status = self.status(),
                    icon = self.icon();

                for (i = 0; i < 3; i++) {
                    status.toggleClass(self.statuses[i], state == i);
                    icon.toggleClass(self.icons[i], state == i);
                }
            }
        }
    });

    module.resolve();
});
