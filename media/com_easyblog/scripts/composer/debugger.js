EasyBlog.module("composer/debugger", function($){

var module = this;

EasyBlog.Controller("Composer.Debugger",
{
    defaultOptions: {

        "{nestableBlock}": EBD.nestableBlock
    }
},
function(self, opts, base, composer, console) { return {

    id: 1,

    init: function() {

        // Globals
        composer = self.composer;
        console = self.console;

        // Simulate console methods but with the ability
        // to mute them when debugger is not turned on.
        for (method in window.console) {
            (function(method){
                self.console[method] = function() {
                    self.active && window.console[method].apply(window.console, arguments);
                }
            })(method);
        }
return;
        var composerEvents = [

            "composerReady",
            "composerDocumentReady",
            "composerTitleChange",
            "composerValidate",
            "composerSave",
            "composerSaveSuccess",
            "composerSaveError",
            "composerSaveTemplate",

            // Workarea
            "composerArtboardShow",
            "composerArtboardHide",

            // Document
            "composerDocumentRefresh",
            "composerDocumentScroll",
            "composerDocumentBlur",

            // Block
            // "composerBlockHoverIn",
            // "composerBlockHoverOut",
            "composerBlockBeforeDrag",
            "composerBlockDrag",
            "composerBlockBeforeDrop",
            "composerBlockDrop",
            "composerBlockBeforeRelease",
            "composerBlockRelease",
            "composerBlockBeforeAdd",
            "composerBlockAdd",
            "composerBlockInit",
            "composerBlockCreate",
            "composerBlockConstruct",
            "composerBlockBeforeActivate",
            "composerBlockActivate",
            "composerBlockActivateError",
            "composerBlockDeactivate",
            "composerBlockChange",
            "composerBlockExport",
            "composerBlockRemove",
            "composerBlockMechanicsChange",
            "composerBlockNestIn",
            "composerBlockNestOut",
            "composerBlockNestChange",
            "composerBlockResizeStart",
            "composerBlockBeforeResize",
            "composerBlockResize",
            "composerBlockResizeStop",

            // Text
            "composerTextSelect",
            "composerTextDeselect",

            // Panel
            "composerPanelActivate",
            "composerPanelDeactivate",

            // Debugger
            "composerDebugActivate",
            "composerDebugDeactivate"
        ];

        var sortEvents = [
            // "sort",
            "sortstart",
            "sortchange",
            "sortactivate",
            "sortdeactivate",
            "sortout",
            "sortover",
            "sortupdate",
            "sortreceive",
            "sortremove",
            "sortstop"
        ];

        var dragEvents = [
            // "drag"
            "dragcreate",
            "dragstart",
            "dragstop"
        ];

        var dropEvents = [
            "drop",
            "dropactivate",
            "dropcreate",
            "dropdeactivate",
            "dropout",
            "dropover"
        ];

        var resizeEvents = [
            "resize",
            "resizecreate",
            "resizestart",
            "resizestop"
        ];

        // Composer events
        $.each(composerEvents, function(i, composerEvent) {
            base.on(composerEvent, function(){
                self.console.log(composerEvent, arguments);
            });
        });

        // Root sort events
        $.each(sortEvents, function(i, sortEvent) {
            base.on(sortEvent, EBD.root, function() {
                self.console.log("root/" + sortEvent, arguments);
            });
        });

        // Nest sort events
        $.each(sortEvents, function(i, sortEvent) {
            base.on(sortEvent, EBD.root, function() {
                self.console.log("nest/" + sortEvent, arguments);
            });
        });

        // Drag events
        $.each(dragEvents, function(i, dragEvent) {
            base.on(dragEvent, EBD.block, function(){
                self.console.log(dragEvent, arguments);
            });
        });

        // Drop events
        $.each(dropEvents, function(i, dropEvent) {
            base.on(dropEvent, EBD.dropzone, function(){
                self.console.log(dropEvent, arguments);
            });
        });

        // Resize events
        $.each(resizeEvents, function(i, resizeEvent) {
            base.on(resizeEvent, function(){
                self.console.log(resizeEvent, arguments);
            });
        });
    },

    console: {},

    active: false,

    activate: function() {

        self.active = true;

        // composer.frame().addClass("is-debugging");

        self.trigger("composerDebugActivate");
    },

    deactivate: function() {

        self.active = false;

        // composer.frame().removeClass("is-debugging");

        self.trigger("composerDebugDeactivate");
    },

    toggle: function() {

        self.active ? self.activate() : self.deactivate();
    }

}});

module.resolve();

});
