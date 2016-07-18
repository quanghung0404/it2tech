/**
 * @version    $Id$
 * @package    JSN_PageBuilder
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

/**
 * This file includes entry functions to
 * activate the JSN PageBuilder layout
 * for Article Editor.
 */

(function ($) {
    /**
     * Apply visual Builder to a textarea
     */
    $.JSNPageBuilder = function (source_id, params) {
        var JSNPageBuilder = this;
        var sourceObject;
        var editorHelper = new $.JSNPbEditorHelper();
        var layoutCustomizer = new JSNPbLayoutCustomizer();
        JSNPageBuilder.init = function () {
            var _default = {};
            // TODO: extend the params

            this.source = $('#' + source_id);	// This is the source object which will be transformed to PageBuilder layout.
            if (typeof(this.source.parents('form[name="adminForm"]')) != undefined) {
                this.container = this.source.parents('form[name="adminForm"]');
            } else {
                this.container = this.source.parents('.adminform');
            }

            if (typeof(this.container) == undefined) {
                return;
            }

            this.source_value = this.source.val();
            if (typeof(this.builder_wrapper) == 'undefined') {
                this.builder_wrapper = $("<div/>", {
                    'id': 'jsnpagebuilder-' + source_id,
                    'class': 'jsn-pb-builder-wrapper jsn-bootstrap'
                });

                // Append the wrapper to DOM right after the source object
                this.source.before(this.builder_wrapper);
            } else {
                this.builder_wrapper.show();
            }
            this.transformSourceValueToHTML();

            $('body').bind('on_after_convert', function(e, shortcodeName, params, modalParams) {
            	// Check if there is an element that being editted yet
            	var isEdit = $('.jsn-element.active-shortcode', $('#form-container')).length > 0 ? true : false;
            	
                setTimeout(function(){
                    $.HandleElement._showSettingModal(shortcodeName, params, false, isEdit, modalParams, 'convert');
                    $.HandleElement.extraAction = 'convert';
                }, 200);
            });

            // Update source content when any change is made
            $('body').bind('jsnpb_changed', function (e) {
                layoutCustomizer.sortableElement();
                layoutCustomizer.sortableColumn("#form-container .jsn-row-container");
                editorHelper.updateSource(JSNPageBuilder.source, JSNPageBuilder.builder_wrapper);
            });
        };

        JSNPageBuilder.transformToSource = function () {
            editorHelper.transformToSource(this.source, this.builder_wrapper);
        };

        /**
         * Method to transform source value to HTML
         * which can be used to generate to PageBuilder,
         * the returned HTML is placed into the PageBuilder Wrapper.
         */
        JSNPageBuilder.transformSourceValueToHTML = function () {
            var loading = $('<div><div class="jsn-modal-overlay"></div><div class="jsn-modal-indicator"></div></div>');
            loading.appendTo('body');
            $('.jsn-modal-overlay', loading).show();
            $('.jsn-modal-indicator', loading).show();

            $.post(JSNPbParams.rootUrl + 'administrator/index.php?option=com_pagebuilder&task=builder.html',
                {form_data: this.source.val(), article_id: $('#jform_id').val()},
                function (data) {
                    // Hide all children div and replace by JSN PageBuilder
                    // Under code only affects on Joomla 3.2
                    editorHelper.hideEditor(JSNPageBuilder.container, source_id);
                    JSNPageBuilder.builder_wrapper.html(data);

                    JSNPageBuilder.builder_wrapper.show();
                    layoutCustomizer.init($("#form-container .jsn-row-container"));
                    JSNPageBuilder.handleBuilderLayout();
                    loading.remove();
                }
            );
        };

        /**
         * Method to register all actions
         * which will be fired on
         * PageBuilder visual layout's elements
         */
        JSNPageBuilder.handleBuilderLayout = function () {
            // Assign shortcode setting popup
            // when click on Add Element button.
            $.HandleElement.initAddElement();
            $.HandleElement.initEditElement();
            $.HandleElement.initDeleteElement();
            $.HandleElement.initCloneElement();

        }

        JSNPageBuilder.init();
        return JSNPageBuilder;
    }
})(JoomlaShine.jQuery);
