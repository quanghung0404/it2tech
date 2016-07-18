/**
 * @version     $Id: showlist.js 16115 2012-09-18 05:21:34Z giangnd $
 * @package     JSN.ImageShow
 * @subpackage  Html
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
define([
	'jquery', 
	'jsn/libs/modal',
],
function ($, JSNModal) 
{
	var JSNISShowcase = function (params) {
		this.params = $.extend({
		}, params);
		this.self			 = null;
		this.initialize();
		this.registerEvents();
	};
	
	JSNISShowcase.prototype = {
		initialize: function () {
		},

		registerEvents: function () {		
		}		
	};

	return JSNISShowcase;
});