EasySocial.module('admin/maintenance/database', function($) {
    var module = this;

    EasySocial.Controller('Maintenance.Database', {
        defaultOptions: {
            '{start}': '[data-start]',

            '{progress}': '[data-progress]',

            '{progressBox}': '[data-progress-box]',

            '{progressBar}': '[data-progress-bar]',

            '{progressPercentage}': '[data-progress-percentage]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{start} click': function(el) {
                el.hide();

                self.progress().show();

                self.process();
            },

            counter: 0,

            versions: [],

            process: function() {
                self.getStats().done(function(versions) {
                    self.versions = versions;

                    self.execute();
                });
            },

            getStats: function() {
                return EasySocial.ajax('admin/controllers/maintenance/getDatabaseStats');
            },

            execute: function() {
                if (self.versions[self.counter] === undefined) {
                    return self.completed();
                }

                EasySocial.ajax('admin/controllers/maintenance/synchronizeDatabase', {
                    version: self.versions[self.counter]
                }).done(function() {
                    self.counter++;

                    var percentage = Math.floor((self.counter/self.versions.length) * 100) + '%';

                    self.progressBar().css('width', percentage);

                    self.progressPercentage().text(percentage);

                    self.execute();
                });
            },

            completed: function() {
                self.progressBar().css('width', '100%');

                self.progressPercentage().text('100%');

                self.progressBox()
                    .removeClass('progress-info')
                    .addClass('progress-success');
            }
        }
    });

    $('[data-base]').addController('EasySocial.Controller.Maintenance.Database');

    module.resolve();
});
