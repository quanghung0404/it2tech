<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
$.Joomla("submitbutton", function(action){
EasySocial.require()
.script( 'privacy' )
.view('site://privacy.default.custom.item')
.done(function($){

	$( '.privacyItem' ).implement(
		EasySocial.Controller.Profiles.Form.Privacy,
		{
			path: 'site',

			view: {
				customItem: "site://privacy.default.custom.item"
			}
			// Override options here.
		}, function(){
		}
	);

});
