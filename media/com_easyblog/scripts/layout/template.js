EasyBlog.module('layout/template', function($) {

    var module = this;

    var self = EasyBlog.template = function(name) {

        if (!name) return;

        if (self.cache.hasOwnProperty(name)) {
            return self.cache[name];
        }

        var templateSelector = '.eb-template[data-name="' + name + '"]';
        var template = $.trim($(templateSelector).detach().html());

        if (template) {
            self.cache[name] = template;
        }

        return template;
    }

    self.cache = {};

    module.resolve();

});
