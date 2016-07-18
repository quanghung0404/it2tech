/**
 * @version     $Id$
 * @package     JSN ImageShow
 * @subpackage  ThemePile
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
(function($) {
    $.fn.adminPhotoPile = function (wrapper, settingWrapper, settings) {
        settings = typeof settings == 'undefined' ? {} : settings;
        var showShadow = settingWrapper.find('input[name="show_shadow"]:checked').val() == '0' ? false : true;
        var defaultSetting = {
            thumbWidth : settingWrapper.find('input#image-width').val(),
            thumbHeight : settingWrapper.find('input#image-height').val(),
            thumbOverlap : settingWrapper.find('input#thumbnail-overlap').val(),
            thumbRotation : settingWrapper.find('input#thumbnail-rotation').val(),
            thumbBorderWidth : settingWrapper.find('input#thumbnail-border-width').val(),
            thumbBorderColor : settingWrapper.find('input#thumbnail-border-color').val(),
            thumbBorderHover : settingWrapper.find('input#thumbnail-border-hover').val(),
            thumbShadow: showShadow,
            thumbShadowColor: settingWrapper.find('input#thumbnail-shadow-color').val(),
            clickAction: 'no_action',
            resetRotation: false,
			rootURL: $('#jsn-is-root-url').val(),
            showInfo : false
        };
		
        defaultSetting = $.extend(true, defaultSetting, settings);
        photopile.scatter(wrapper, defaultSetting);  // ### initialize the photopile ###
    };

    $(document).ready(function () {
        var wrapper = $('.jsn-themepile-wrapper');
        var paramWrapper = $('#jsn-theme-parameters-wrapper');
		$('input[name="general_overall_height"]').parent().parent().hide();
        if (wrapper.length > 0) {
            $.fn.adminPhotoPile(wrapper, paramWrapper);
            $('input#image-width').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#image-height').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#thumbnail-overlap').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#thumbnail-rotation').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper, {resetRotation: true});
            });
            $('input#thumbnail-border-width').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#thumbnail-border-color').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#thumbnail-border-hover').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input#thumbnail-shadow-color').on('change', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            $('input[name="show_shadow"]').on('click', function () {
                $.fn.adminPhotoPile(wrapper, paramWrapper);
            });
            wrapper.on('click', function () {
                $('#jsn-is-themepile').tabs({'selected' : 0});
            });
        }
    });
})(jsnThemePilejQuery);