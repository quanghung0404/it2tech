/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: jsn_is_gridthemelightbox.js 16892 2012-10-11 04:07:40Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
(function($) {
	$.fn.gridtheme.lightbox = function(options) {
		var allow = false;
		var bottom_panel = desc = gallery = caption = literal = '';
		var objAllow = $('#data_allow_grid_' + options.rand).val();
		if (objAllow) {
			objAllow = JSON.parse(objAllow);
			if (objAllow.show_caption == 'yes') {
				caption = '<div class="ppt">&nbsp;</div>';
			} else {
				caption = '<div class="ppt jsn-themegrid-hidden">&nbsp;</div>';
			}
			if (objAllow.show_description == 'yes') {
				desc	= '<p class="pp_description"></p>';
			} else {
				desc	= '<p class="pp_description jsn-themegrid-hidden"></p>';
			}
			if (objAllow.show_close == 'yes') {
				literal = '<a class="pp_close" href="#">Close</a>';
			} else {
				literal = '<a class="pp_close jsn-themegrid-hidden" href="#">Close</a>';
			}
			
			bottom_panel = '<div class="pp_details ' + ((objAllow.show_description != 'yes' && objAllow.show_close != 'yes') ? 'jsn-themegrid-hidden' : '') +'"> \
				' + desc + ' \
				' + literal + ' \
			</div>';
			
			if (objAllow.show_thumbs == 'yes') {
				gallery = '<div class="pp_gallery"> \
					<a href="#" class="pp_arrow_previous">Previous</a> \
					<div> \
						<ul> \
							{gallery} \
						</ul> \
					</div> \
					<a href="#" class="pp_arrow_next">Next</a> \
				</div>';
			}
			
		}
		$("div.jsn-themegrid-container-"+options.rand+" div[class^=\"jsn-themegrid-box\"]:not(.jsn-themegrid-hide) a[rel^='prettyPhoto']").prettyPhoto({
			animation_speed: 'fast', /* fast/slow/normal */
			slideshow: objAllow.slideshow, /* false OR interval time in ms */
			autoplay_slideshow: objAllow.autoplay_slideshow, /* true/false */
			opacity: 0.80, /* Value between 0 and 1 */
			show_title: true, /* true/false */
			allow_resize: true, /* Resize the photos bigger than viewport. true/false */
			default_width: 500,
			default_height: 344,
			counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
			theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
			horizontal_padding: 20, /* The padding on each side of the picture */
			hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
			wmode: 'opaque', /* Set the flash wmode attribute */
			autoplay: true, /* Automatically start videos: True/False */
			modal: false, /* If set to true, only the close button will close the window */
			deeplinking: false, /* Allow prettyPhoto to update the url to enable deeplinking. */
			overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
			keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
			changepicturecallback: function(){}, /* Called everytime an item is shown/changed */
			callback: function(){}, /* Called when prettyPhoto is closed */
			ie6_fallback: true,
			markup: '<div class="pp_pic_holder"> \
						' + caption + ' \
						<div class="pp_top"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
						<div class="pp_content_container"> \
							<div class="pp_left"> \
							<div class="pp_right"> \
								<div class="pp_content"> \
									<div class="pp_loaderIcon"></div> \
									<div class="pp_fade"> \
										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
										<div class="pp_container jsn-themegrid"> \
											<a class="pp_next" href="#">next</a> \
											<a class="pp_previous" href="#">previous</a> \
										</div> \
										<div id="pp_full_res"></div> \
										' + bottom_panel + ' \
									</div> \
								</div> \
							</div> \
							</div> \
						</div> \
						<div class="pp_bottom"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
					</div> \
					<div class="pp_overlay"></div>',
			gallery_markup: gallery,				
		});
	}	
})(jsnThemeGridjQuery);