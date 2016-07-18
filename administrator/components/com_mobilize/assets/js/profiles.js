define([
    'jquery',
    'mobilize/help',
    'mobilize/dialogedition',
    'jquery.tipsy',
    'jquery.ui'],
    function ($, JSNHelp,JSNMobilizeDialogEdition) {
        var JSNMobilizeProfilesView = function (params) {
            this.params = params;
            this.lang = params.language;
            this.init();
        }
        JSNMobilizeProfilesView.prototype = {
            init:function () {
                this.JSNHelp = new JSNHelp();
                var self = this;
                this.JSNMobilizeDialogEdition = new JSNMobilizeDialogEdition(this.params);
                $(".jsn-popup-upgrade").click(function () {
                    JSNMobilizeDialogEdition.createDialogLimitation($(this), self.lang["JSN_MOBILIZE_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0"]);
                })
                $('.jsn-tipsy').tipsy({
                    gravity:'w',
                    html:true,
                    fade:true
                });
            }
        }
        return JSNMobilizeProfilesView;
    })