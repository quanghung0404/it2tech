/*------------------------------------------------------------------------
# Full Name of JSN UniForm
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
# @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
# @version $Id: emailsettings.js 14957 2012-08-10 11:47:52Z thailv $
-------------------------------------------------------------------------*/
define([
    'jquery',
    'jquery.ui'
    ], 
    function($)
    {
	var lang ;
	function JSNImageShowDialogEdition  (params) {
	    this.params = params;
	    lang   = params.language;
	}
	JSNImageShowDialogEdition.createDialogLimitation = function(_this, message, title, button_title) {
	    $("#dialog-limitation").remove();
	    $($(_this)).after(
		$("<div/>",{
		    "id":"dialog-limitation"
		}).append(
		    $("<div/>",{
			"class":"ui-dialog-content-inner jsn-bootstrap"
		    }).append(
			$("<p/>").append(message)
			).append(
			$("<div/>",{
			    "class":"form-actions"
			}).append(
			    $("<button/>",{
				"class":"btn",
				"id":"btn-upgade-edition",
				"text": button_title
			    }).click(function(){
				document.location.href="index.php?option=com_imageshow&view=upgrade";
			    })
			    )
			)
		    )
		);
	    $("#dialog-limitation").dialog({
		height: 300,
		width: 500,
		title: title,
		draggable: false,
		resizable: false,
		autoOpen: true,
		modal: true,
		buttons: {
		    Close: function() {
			$( this ).dialog( "close" );
		    }
		}
	    });
	}
	
	return JSNImageShowDialogEdition;
});
