EasyBlog.module('admin/maintenance/form', function($) {
    var module = this;

    EasyBlog.Controller('Maintenance.Form', {
        defaultOptions: {
            '{row}': '[data-row]'
        }
    }, function(self) {
        return {
            init: function() {
                self.runscript();
            },

            counter: 0,

            success: 0,

            fail: 0,

            runscript: function() {
                var row = self.row().eq(self.counter);

                if (row.length === 0) {
                    return self.completed();
                }

                var key = row.data('key');

                EasyBlog.ajax('admin/controllers/maintenance/runscript', {
                    key: key
                }).done(function() {
                    self.setStatus(row, 1);
                    self.success++;
                }).fail(function() {
                    self.setStatus(row, 0);
                    self.fail++;
                }).always(function() {
                    self.counter++;
                    self.runscript();
                });
            },

            completed: function() {
                if (self.fail < 1) {
                    window.location = 'index.php?option=com_easyblog&view=maintenance&success=' + self.success;
                }
            },

            setStatus: function(row, state) {
                var status = row.find('[data-status]'),
                    icon = row.find('[data-icon]'),
                    statuses = ['label-danger', 'label-success', 'label-warning'],
                    icons = ['ies-warning-2', 'ies-checkmark', 'ies-wrench-3'];

                for (i = 0; i < 3; i++) {
                    status.toggleClass(statuses[i], state == i);
                    icon.toggleClass(icons[i], state == i);
                }
            }
        }
    });

    module.resolve();
});
