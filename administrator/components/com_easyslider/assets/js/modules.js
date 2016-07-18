/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
void function( $ ) {
    $(window).load(function(){
        var sliderSelectBox     = $('#jform_params_slider_id'),
            editSlider          = $('#edit-slider'),
            addNewSlider        = $('#new-slider'),
            modal               = $('.jsn-modal');

        modal.addClass('hidden');
        var sliderID = sliderSelectBox.val();
        if(sliderID != 0 && sliderID != '') {
            editSlider
                .removeClass('disabled')
                .attr('href', 'index.php?option=com_easyslider&view=slider&layout=edit&slider_id=' + sliderID);
        }
        sliderSelectBox.on('change', function(){
            var sliderID = $(this).val();
            if(sliderID != 0 && sliderID != '') {
                editSlider
                    .removeClass('disabled')
                    .attr('href', 'index.php?option=com_easyslider&view=slider&layout=edit&slider_id=' + sliderID);
            }
            else {
                editSlider
                    .addClass('disabled')
                    .attr('href', 'javascript:void(0);');
            }
        });

        function showModal(selector){
        	var link = selector.attr('href');
            if(link != 'javascript:void(0);')
            {
				var rand	= Math.floor((Math.random()*100)+1);
				var iframeID = 'jsn-es-iframe-slider-setting-page-' + rand;

				var modal = new $.JSNEasySliderModal({
	 				frameId: iframeID,
	 				title: 'Slider Settings',
	 				url: link,
	 				autoOpen: true,
	 				buttons: [				
		 				{
		 					'text'	: 'Close',
		 					'id'	: 'close',
		 					'class' : 'btn btn-default ui-button ui-widget ui-corner-all ui-button-text-only',
		 					'click'	: function () {
		 						modal.close();
		 					}
		 				}
	 				],
	 				loaded: function (obj, iframe) {
	 					
	 				},
	 				
	 				scrollable: true,
	 				width: $(window).width()*0.95,
	 				height: $(window).height()*0.85,
				});
				modal.show();
            }
        }

       /*
        addNewSlider.click(function(e){
            e.preventDefault();
            if($(this).attr('href') != 'javascript:void(0);'){
                showModal($(this));
            }
        });*/
    });
    
    $(window).load(function (){
    	
    	$('#jform_params_slider_id').parent().find("div").hide();
    	$('#jform_params_slider_id').show();
    });

}((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);