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
.script( 'site/dashboard/dashboard' )
.done(function($){

	$( '[data-dashboard]' ).implement( EasySocial.Controller.Dashboard ,
	{
		<?php if( JFactory::getApplication()->getMenu()->getActive() ){ ?>
			pageTitle 	: "<?php echo JFactory::getApplication()->getMenu()->getActive()->params->get( 'page_title' );?>"
		<?php } ?>
	});
});
