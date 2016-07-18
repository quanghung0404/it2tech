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
    	'imageshow/joomlashine/dialogedition',
    ], 
    function($,JSNModal, JSNImageShowDialogEdition)
    {
	function JSNISShowlists  (params) {
	    this.params = params;
	    this.lang   = params.language;
	    this.init();
	}
	JSNISShowlists.prototype = {
	    
	    //Create modal box email list select 
	    init: function () {
			var self = this;
			$(".jsn-popup-upgrade").click(function() {
				JSNImageShowDialogEdition.createDialogLimitation($(this) , self.params.language["JSN_IMAGESHOW_SHOWLIST_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SHOWLISTS_IN_FREE_EDITION"], self.params.language["CPANEL_UPGRADE_TO_PRO_EDITION_FOR_MORE"], self.params.language["UPGRADE_TO_PRO_EDITION"]);
			})
	    }
	}
	
	return JSNISShowlists;
});
