/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: help.js 19013 2012-11-28 04:48:47Z thailv $
 -------------------------------------------------------------------------*/
define([
	'jquery',
	'jsn/libs/modal',
	'jquery.ui'],
function($, JSNModal) {
	var JSNMobilizeHelpView = function() {
		this.init();
	}
	JSNMobilizeHelpView.prototype = {
		//Create modal box email list select 
		init: function() {
			var self = this;
			$("#jsn-help").click(function() {
				self.createModalHelp();
			})
			// close modal box
			$.closeModalBoxHelp = function() {
				self.jsnMobilizeModal.close();
				$(".jsn-modal").remove();
			}
		},
		createModalHelp: function() {
			var height = $(window).height();
			var width = $(window).width();
			var buttons = {};
			buttons["Close"] = $.proxy(function() {
				$.closeModalBoxHelp();
			}, this);
			this.jsnMobilizeModal = new JSNModal({
				url: 'index.php?option=com_mobilize&view=help&tmpl=component',
				title: "Help",
				buttons: buttons,
				height: height * (95 / 100),
				width: width * (95 / 100),
				scrollable: true
			});
			this.jsnMobilizeModal.show();
		}
	}
	return JSNMobilizeHelpView;
});