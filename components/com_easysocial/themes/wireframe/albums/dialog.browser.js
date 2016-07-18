<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

EasySocial.require()
	.script("albums/browser")
	.done(function($){

		$("[data-album-browser=<?php echo $uuid; ?>]")
			.addController(
				"EasySocial.Controller.Albums.Browser",
				{
					itemRenderOptions: {
						layout      : "dialog",
						limit       : 20,
						canUpload   : 0,
						showToolbar : 0,
						showInfo    : 0,
						showStats   : 0,
						showResponse: 0,
						showTags    : 0,
						showForm    : 0,

						photoItem: {
							showToolbar: 0,
							showStats: 0,
							showForm: 0,
							openInPopup: 0
						}
					}
				});
	});
