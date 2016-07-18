EasyBlog.module("composer/panels", function($) {

var module = this;

EasyBlog.Controller("Composer.Panels",
{
    hostname: "panels",

    defaultOptions: {

        "{panel}": "[data-eb-composer-panel]",
        "{panelTab}": "[data-eb-composer-panel-tab]",
        "{showDrawer}": "[data-eb-composer-panel-show-drawer]",
        "{fieldset}": ".eb-composer-fieldset",
        "{fieldsetToggle}": ".eb-composer-fieldset-toggle input"
    }
},
function(self, opts, base) { return {

    init: function() {
        var plugins = [
            "autopost",
            "association",
            "seo",
            "post",
            "category",
            "authorship"
        ];

        // Install plugins
        $.each(plugins, function(i, plugin){
            self.addPlugin(plugin);
        });
    },

    panel: {

        get: $.memoize(function(panelId) {

            return self.panel().where("id", panelId);
        })
    },

    panelTab: {

        get: $.memoize(function(panelId){

            return self.panelTab().where("id", panelId);
        })
    },

    fieldset: {

        get: function(name) {

            return self.fieldset().where("name", name);
        },

        enable: function(name, val) {

            val===undefined && (val = true);

            self.fieldset.get(name)
                .toggleClass("is-disabled", !val)
                .find(self.fieldsetToggle)
                .prop("checked", !!val);
        },

        disable: function(name, val) {

            val===undefined && (val = false);
            self.fieldset.enable(name, val);
        },

        show: function(name) {

            self.fieldset.get(name)
                .removeClass("is-hidden");
        },

        hide: function(name) {

            self.fieldset.get(name)
                .addClass("is-hidden");
        },

        toggle: function(name, val) {

            self.fieldset.get(name)
                .toggleClass("is-hidden", val===undefined ? undefined : !val);
        }
    },

    activate: function(panelId) {

        self.deactivate();

        self.panel.get(panelId)
            .addClass("active");

        self.panelTab.get(panelId)
            .addClass("active");

        self.trigger("composerPanelActivate", [panelId]);
    },

    deactivate: function(panelId) {

        // If no panelId is given, deactivate current active panel.
        if (!panelId) {
            panelId = self.panel(".active").data("id");
        }

        self.panel.get(panelId)
            .removeClass("active");

        self.panelTab.get(panelId)
            .removeClass("active");

        self.trigger("composerPanelDeactivate", [panelId]);
    },

    "{panelTab} click": function(panelTab) {

        var panelId = panelTab.data("id");

        self.activate(panelId);
    },

    "{showDrawer} click": function() {

       $('[data-eb-composer-frame]').toggleClass('show-drawer');

    },

    "{fieldsetToggle} change": function(fieldsetToggle) {

        self.fieldset.of(fieldsetToggle)
            .toggleClass("is-disabled", !fieldsetToggle.is(":checked"));
    }

}});

module.resolve();

});
