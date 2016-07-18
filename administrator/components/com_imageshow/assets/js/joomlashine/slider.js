/**
 * @version     $Id$
 * @package     JSN.ImageShow
 * @subpackage  Html
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
(function($){
	$.fn.JSNISSlider = function(btnPrev,btnNext,itemPerSlide,itemNumber){
		var self		= this;
		var currentSlide	= 0;
		var slideNumber		= 0;
		var slideContent	= null;
		var nextButton		= $('#'+btnNext);
		var prevButton		= $('#'+btnPrev);

		self.nextContent = function(){
			nextButton.bind('click',function() {
				currentSlide++;
				offset	= itemPerSlide*slideContent.children().width();
				slideContent.animate({"left": "-="+offset+"px"}, "fast");
				if(currentSlide == slideNumber)
				{
					$(this).addClass('disabled');
					$(this).unbind('click');
					if(prevButton.hasClass('disabled'))
					{
						prevButton.removeClass('disabled');
						self.previousContent();
					}
				}	
			});
		};
		self.previousContent = function(){
			prevButton.bind('click',function() {
				currentSlide--;
				offset	= itemPerSlide*slideContent.children().width();
				slideContent.animate({"left": "+="+offset+"px"}, "fast");
				if(currentSlide == 0)
				{
					$(this).addClass('disabled');
					$(this).unbind('click');
					if(nextButton.hasClass('disabled'))
					{
						nextButton.removeClass('disabled');
						self.nextContent();
					}
				}
			});
		};
		
		self.initialize = function(){
			slideContent		= this.children(":first");
			var heightOfSlider	= slideContent.children().height();
			slideNumber			= parseInt((itemNumber-1)/itemPerSlide);
			this.css({'overflow':'hidden','height':heightOfSlider+'px'});
			slideContent.css({'position':'absolute','height':heightOfSlider+'px'});
			prevButton.addClass('disabled');
			self.nextContent();
		
			$(window).resize(function() {
				offset	= itemPerSlide*slideContent.children().width()*currentSlide;
				slideContent.css({"left": "-"+offset+"px"});
			});
		};
		self.initialize();
	};
})(jQuery);