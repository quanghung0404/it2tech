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

(function($){ 
	$.JSNESSlider = function(options) {
		this.options  			= $.extend({}, options);	
		self.req				= false;
		this.self 				= null;
		this.initialize = function ()
		{
			this.self = this;
		};
		
		this.refreshSession = function()
		{
			var self = this.self;
			self.req = false;
			if(window.XMLHttpRequest && !(window.ActiveXObject)) {
				try {
					self.req = new XMLHttpRequest();
				} catch(e) {
					self.req = false;
				}
				// branch for IE/Windows ActiveX version
			} else if(window.ActiveXObject) {
				try {
					self.req = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
					try {
						self.req = new ActiveXObject("Microsoft.XMLHTTP");
					} catch(e) {
						self.req = false;
					}
				}
			}
			if(self.req) 
			{
				self.req.onreadystatechange = function () {
					// only if req shows "loaded"
					if(self.req.readyState == 4) {
						// only if "OK"
						if(self.req.status == 200) {
							// TODO: think what can be done here
						} else {
							// TODO: think what can be done here
							//alert("There was a problem retrieving the XML data: " + req.statusText);
						}
					}				
				};
				
				self.req.open("HEAD", self.options.pathRoot, true);
				self.req.send();
			}
		};
	};
})(jQuery);
