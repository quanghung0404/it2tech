/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: jsn_is_gridtheme.js 16892 2012-10-11 04:07:40Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
(function($) {
	$.fn.extend( {
		initGrid:function(container,options){
			if(options==undefined){
				themeId					 = '';
				layout					 = $('#img_layout').val();
				thumbnail_width 		 = $('#thumbnail_width').val(); 
				thumbnail_height		 = $('#thumbnail_height').val();
				thumbnail_space			 = $('#thumbnail_space').val();
				thumbnail_border		 = $('#thumbnail_border').val();
				thumbnail_rounded_corner = $('#thumbnail_rounded_corner').val();
				thumbnail_border_color	 = $('#thumbnail_border_color').val();
				thumbnail_shadow		 = $('#thumbnail_shadow').val();
				background_color		 = $('#background_color').val();
				if ($('input[name="container_transparent_background"]:checked').val() == 'no')
				{
					container.css('background-color', background_color);
				}	
				else
				{
					container.css('background', 'none');
				}					
			}else{
				themeId					 = options.key;
				layout					 = options.layout;
				thumbnail_width 		 = options.thumbnail_width;
				thumbnail_height		 = options.thumbnail_height;
				thumbnail_space			 = options.thumbnail_space;
				thumbnail_border		 = options.thumbnail_border;
				thumbnail_rounded_corner = options.thumbnail_rounded_corner;
				thumbnail_border_color	 = options.thumbnail_border_color;
				thumbnail_shadow		 = options.thumbnail_shadow;
				background_color		 = options.background_color;
				container_transparent_background = options.container_transparent_background;

				if (!container_transparent_background)
				{
					container.css('background-color',background_color);
				}
				else
				{
					container.css('background', 'none');
				}					
			}
			
			$(this).initThumbnailSpace(container,thumbnail_space);
			$(this).initThumbnailBorder(container,themeId,thumbnail_border,thumbnail_border_color);
			$(this).initThumbnailRoundedCorner(container,thumbnail_rounded_corner);
			$(this).initThumbnailShadow(container,thumbnail_shadow);
			$(this).initLayout(container,layout,thumbnail_width,thumbnail_height);
			if (options && options.navigation_type == 'load_more') {
				
				$(this).loadMore(container,layout,thumbnail_width,thumbnail_height, options, thumbnail_space, thumbnail_border);
			}
		},
		loadMore: function(container, layout,thumbnail_width,thumbnail_height, options, thumbnail_space, thumbnail_border){
			
			var size = $('.jsn-themegrid-container-'+options.key+' .jsn-themegrid-box').size();
			var item_per_page =  parseInt(options.item_per_page);
			var item_width = parseInt(thumbnail_width) + parseInt(2*thumbnail_space) + parseInt(2*thumbnail_border);
			
			function jsnGridLoadMore()
			{
				var total_element = $('.jsn-themegrid-container-'+options.key+' div[class^=\"jsn-themegrid-box\"]:not(.jsn-themegrid-hide)').length;
				var container_width = container.innerWidth();
				var limit_item = Math.floor(container_width / item_width);
				var element_width = limit_item * item_width;
				if ( total_element < limit_item )
				{
					container.find('.jsn-themegrid-items').css( 'width', 'auto');
				}
				else
				{
					container.find('.jsn-themegrid-items').css( 'width', element_width);
				}
			}
			
			jsnGridLoadMore();
			$( window ).resize(function() {
				jsnGridLoadMore();
			});			
			
			$('.load-more-'+options.key+' #load_more_'+options.key+'').click(function(){	
				var container_width = container.innerWidth();
				var limit_item = Math.floor(container_width / item_width);
				var element_width = limit_item * item_width;
				
	    		item_per_page = (item_per_page <= size) ? item_per_page+parseInt(options.item_per_page) : size;
	    		container.find('.jsn-themegrid-box img').removeAttr( 'style' );
	    		$('.jsn-themegrid-container-'+options.key+' .jsn-themegrid-box:lt('+item_per_page+')').removeClass("jsn-themegrid-hide");
	    		$('.jsn-themegrid-container-'+options.key+' .jsn-themegrid-box:lt('+item_per_page+')').fadeIn("slow");
	    		
	    		$(this).initLayout(container,layout,thumbnail_width,thumbnail_height);
				$('.jsn-themegrid-container-'+options.key).gridtheme.lightbox({rand:options.key});
		
				var total_element = $('.jsn-themegrid-container-'+options.key+' div[class^=\"jsn-themegrid-box\"]:not(.jsn-themegrid-hide)').length;
				if ( total_element >= limit_item )
				{
					container.find('.jsn-themegrid-items').css( 'width', element_width);
				}
				if (total_element == size) {
		    		 $('#load_more_'+options.key).hide();
				}
			});
		},
		changeValueVisual:function(container,name,value){
			switch(name){
				case 'thumbnail_width':
				case 'thumbnail_height':	
				case 'img_layout':
					layout				= $('#img_layout').val();
					thumbnail_width		= $('#thumbnail_width').val();
					thumbnail_height	= $('#thumbnail_height').val();
					$(this).initLayout(container,layout,thumbnail_width,thumbnail_height);
					break;
				case 'thumbnail_space':
					$(this).initThumbnailSpace(container,value);
					break;
				case 'thumbnail_border':
					thumbnail_border		= value;
					themeId					= '';
					thumbnail_border_color	= $('#thumbnail_border_color').val();
					$(this).initThumbnailBorder(container,themeId,thumbnail_border,thumbnail_border_color);
					break;
				case 'thumbnail_rounded_corner':
					$(this).initThumbnailRoundedCorner(container,value);
					break;
				case 'thumbnail_shadow':
					$(this).initThumbnailShadow(container,value);
					break;
				case 'container_transparent_background':
					if ($('input[name="container_transparent_background"]:checked').val() == 'no')
					{
						container.css('background-color', $('#background_color').val());
					}	
					else
					{
						container.css('background', 'none');
					}					
			}
		},
		initLayout:function(container,layout,thumbnail_width,thumbnail_height){
	    	var imgId		= 0;
	    	var imgElement	= '';
	    	var width		= 0;
	    	var height		= 0;
			switch(layout){
				case 'fixed':
					
					container.find(".jsn-themegrid-image img").each(function(){
						imgId = $(this).attr('id');
						imgElement = document.getElementById(imgId);
						width = imgElement.clientWidth;
						height = imgElement.clientHeight;
						
						$(this).scaleResize(thumbnail_width,thumbnail_height,width,height,imgId);
					});
					container.find(".jsn-themegrid-box").css({width:thumbnail_width,height : thumbnail_height});
					if(document.getElementById('thumbnail_height') != undefined){
						$('#thumbnail_height').attr('readonly', false);
					}
					break;
				case 'fluid':
					container.find(".jsn-themegrid-image img").each(function(){
						$(this).css({width:thumbnail_width,height : "",top:0,left:0});
					});
					container.find(".jsn-themegrid-box").css({width:thumbnail_width,height : ""});
					if(document.getElementById('thumbnail_height') != undefined){
						$('#thumbnail_height').attr('readonly', true);
					}	
					break;
			}
		},
		initThumbnailWidth:function(width){
			thumbnail_width = width;
			$(this).initLayout();
		},
		initThumbnailHeight:function(height){
			thumbnail_height = height;
			$(this).initLayout();
		},
		initThumbnailSpace:function(container,thumbnail_space){
			var key = '';
			var numberImg = container.find('.jsn-themegrid-box').length;
			for(var i=1;i<=numberImg;i++){
				key = (themeId=='')?i:themeId+'_'+i;
				document.getElementById(key).style.marginLeft = thumbnail_space+"px";
				document.getElementById(key).style.marginRight = thumbnail_space+"px";
				document.getElementById(key).style.marginTop = thumbnail_space+"px";
				document.getElementById(key).style.marginBottom = thumbnail_space+"px";
			}
		},
		initThumbnailBorder:function(container,themeId,thumbnail_border,thumbnail_border_color){
			var key = '';
			var numberImg = container.find('.jsn-themegrid-box').length;
			for(var i=1;i<=numberImg;i++){
				key = (themeId=='')?i:themeId+'_'+i;
				document.getElementById(key).style.border = thumbnail_border+"px solid "+thumbnail_border_color;
			}
		},
		initThumbnailRoundedCorner:function(container,thumbnail_rounded_corner){
			container.find(".jsn-themegrid-box").css({
				'border-radius': thumbnail_rounded_corner+'px',
				'-moz-border-radius': thumbnail_rounded_corner+'px',
				'-webkit-border-radius': thumbnail_rounded_corner+'px'
			});
		},
		initThumbnailShadow:function(container,thumbnail_shadow){
			switch(thumbnail_shadow){
				case "0":
					container.find(".jsn-themegrid-box").css({
						'box-shadow' : '0px 0px 0px #888888',
						'-moz-box-shadow' : '0px 0px 0px #888888',
						'-webkit-box-shadow' : '0px 0px 0px #888888',
					}); 
					break;
				case "1":
					container.find(".jsn-themegrid-box").css({
						'box-shadow' : '0px 0px 3px 1px #aaa',
						'-moz-box-shadow' : '0px 0px 3px 1px #aaa',
						'-webkit-box-shadow' : '0px 0px 3px 1px #aaa',
					}); 
					break;
				case "2":
					container.find(".jsn-themegrid-box").css({
						'box-shadow' : '0 2px 5px 0 #888',
						'-moz-box-shadow' : '0 2px 5px 0 #888',
						'-webkit-box-shadow' : '0 2px 5px 0 #888',
					});
			}
		},
		scaleResize: function(thumbnail_width,thumbnail_height,imageW, imageH, imgID)
		{
			var imageElement 	= document.getElementById(imgID);
			var imageRatio   	= imageW/imageH;
			var imageBoxRatio	= thumbnail_width/thumbnail_height;
			var leftOffset		= 0;
			var topOffset		= 0;
			if(imageRatio < imageBoxRatio){
				//cut top and bottom of image
				imageElement.style.width = thumbnail_width+'px';
				var imageHeight	= Math.floor(thumbnail_width/imageRatio);
				topOffset		= Math.floor((thumbnail_height - imageHeight)/2);
				imageElement.style.height	= imageHeight+'px';
				imageElement.style.top		= topOffset+'px';				
			}else{
				//cut left and right of image
				imageElement.style.height = thumbnail_height+'px';
				var imageWidth	= Math.floor(thumbnail_height*imageRatio);
				leftOffset		= Math.floor((thumbnail_width - imageWidth)/2);
				imageElement.style.width	= imageWidth+'px';	
			}
			imageElement.style.top		= topOffset+'px';
			imageElement.style.left		= leftOffset+'px';
		}
	});
	$.fn.gridtheme = function(options) {
		
		var heightOfContainer = (options==undefined)?'345':options.height;
		var container	=  this;
		
		if (heightOfContainer != undefined)
		{
			container.imagesLoaded( function(){
				container.initGrid(container,options);
				if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) 
				{
					container.find('.jsn-themegrid-items').masonry({
						itemSelector : '.jsn-themegrid-box',
						isFitWidth: true
					});
				}
				else
				{
					container.find('.jsn-themegrid-items').masonry({
						itemSelector : '.jsn-themegrid-box',
						isFitWidth: true
					}),(container.css("height",heightOfContainer));
				}
	  		});
		}
		else
		{
			container.imagesLoaded( function(){
				container.initGrid(container,options);
				if (options.navigation_type != 'load_more') {
					container.find('.jsn-themegrid-items').masonry({
						itemSelector : '.jsn-themegrid-box',
						isFitWidth: true
					});	
				}
				
				
	  		});			
		}	
		if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) 
		{
			
		}
		else
		{
			this.kinetic();
		}
    	if(options==undefined){
	    	$('.imagePanel').change( function() {
	    		$(this).changeValueVisual(container,$(this).attr('name'),$(this).val());
	    		container.find('.jsn-themegrid-items').masonry({
		    		itemSelector : '.jsn-themegrid-box',
		    		isFitWidth: true
		    	}),(container.css("height",heightOfContainer));
	    	});
    	}
	};
})(jsnThemeGridjQuery);