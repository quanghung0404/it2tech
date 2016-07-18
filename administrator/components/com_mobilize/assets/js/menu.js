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

define([
        'jquery'
    ],
    function ($) {

        $('.mobilize-language').change(function () {
            var self = this;
            var optionValue = $(this).val();
            $(this).parents('tr.jsnhover').attr('data-language', $(this).val())

            if (optionValue == 'all')
            {
                $('.mobilize-language')
                    .filter(function(){
                        return self == this ? false : true;
                    })
                    .attr('disabled', 'disabled').parents('tr.jsnhover').attr('data-language', '')
                ;
            }
            else {
                $('.mobilize-language').removeAttr('disabled');
            }

            if(optionValue != ''){
                $('.mobilize-language')
                    .filter(function(){
                        return self == this ? false : true;
                    })
                    .find('option[value=' + optionValue + ']')
                    .attr('disabled', 'disabled');
            }
            else{
                $('.mobilize-language option').removeAttr('disabled');
            }
        });

    });