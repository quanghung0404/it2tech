/**
 * @version    $Id$
 * @package    JSN_Sample
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

define([
    'jquery'
],
    function ($) {
        var JSNMobilizeMenuSelectView = function () {
            this.init();
        };

        JSNMobilizeMenuSelectView.prototype = {
            init:function () {
                var self = this;
                $("table.table-popup tr.jsnhover").click(function () {
                    window.parent.jQuery.changeModuleMenuIcon($(this).attr("data-id"), $(this).attr("data-title"), 'update');
                })
            }
        }
        return JSNMobilizeMenuSelectView;
    });