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
    $(document).ready(function() {
        var sliderSelectBox     = $('#filter_slider_id'),
            editSlider          = $('#edit-slider'),
            addNewSlider        = $('#new-slider'),
            presentationMethod  = $('#presentation_method'),
            goALink             = $('#jsn-go-link'),
            
            menuType            = $('#menutype');

        menuType.hide();
        
        sliderSelectBox.change(function(){
            var sliderID = $(this).val();
            if (sliderID != 0 && sliderID != '') 
            {
                editSlider
                    .removeClass('disabled')
                    .attr('href', 'index.php?option=com_easyslider&view=slider&layout=edit&slider_id=' + sliderID + '&tmpl=component');
                presentationMethod.removeAttr('disabled');
            }
            else 
            {
                editSlider
                    .addClass('disabled')
                    .attr('href', 'javascript:void(0);');
                presentationMethod.attr('disabled', 'disabled');
            }
        });

        presentationMethod.change(function() 
        {
            var value = $(this).val();
            if(value == 'module'){
                goALink
                    .attr('href', 'index.php?option=com_easyslider&task=launchAdapter&type=module&slider_id=' + sliderSelectBox.val())
                    .removeClass('disabled');
            }
            else if( value == 'menu'){
                menuType.show();
            }
        });

        function showModal(selector)
        {
            var link = selector.attr('href');
            var title = selector.attr('rel');
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

        editSlider.click(function(e) {
            e.preventDefault();          
            if($(this).attr('href') != 'javascript:void(0);'){
                showModal($(this));
            }
        });

        addNewSlider.click(function(e) {
            e.preventDefault();
            if($(this).attr('href') != 'javascript:void(0);') {
                showModal($(this));
            }
        });
    });
    
    $(window).load(function(){
        $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
        $("body").append($("<div/>", {
            "class":"jsn-modal-overlay",
            "style":"z-index: 1000; display: inline;"
        })).append($("<div/>", {
            "class":"jsn-modal-indicator",
            "style":"display:block"
        })).addClass("jsn-loading-page");

        $(".jsn-modal-overlay,.jsn-modal-indicator").delay(1200).queue(function () {
            $(this).remove();
            $("#jsn-launchpad").removeClass("jsn-easyslider-hide");
        });
        
    });
}( jQuery );