EasySocial.module('story/polls', function($) {
    var module = this;

    EasySocial.require()
    .library('datetimepicker')
    .view('site/loading/small')
    .language('COM_EASYSOCIAL_STORY_EVENT_INSUFFICIENT_DATA', 'COM_EASYSOCIAL_STORY_EVENT_INVALID_START_END_DATETIME')
    .done(function() {
        EasySocial.Controller('Story.Polls', {
            defaultOptions: {
                '{base}': '[data-story-event-base]',

                '{category}': '[data-story-event-category]',
                '{form}': '[data-story-event-form]',

                '{timezone}': '[data-event-timezone]',

                '{datetimeForm}': '[data-event-datetime-form]',

                '{datetime}': '[data-event-datetime]',

                '{title}': '[data-event-title]',
                '{description}': '[data-event-description]',

                view: {
                    loading: 'site/loading/small'
                }
            }
        }, function(self) {
            return {
                init: function() {

                },

                '{story} save': function(element, event, save) {

                    if (save.currentPanel != 'polls') {
                        return;
                    }

                    var pollController = element.find('[data-polls]').controller('EasySocial.Controller.Polls');

                    self.options.name = 'polls';

                    var task = save.addTask('validatePollsForm');
                    self.save(task, pollController);
                },

                save: function(task, pollController) {

                    var valid = pollController.validateForm();

                    if (! valid) {
                        return task.reject('Error validating polls inputs. Please make sure all the required fields are filled in.');
                    }

                    var data = pollController.toData();
                    task.save.addData(self, data);

                    task.resolve();
                }
            }
        });

        module.resolve();
    });
});
