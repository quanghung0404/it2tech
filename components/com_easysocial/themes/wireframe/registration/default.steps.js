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
.script( 'site/registrations/registrations' )
.done(function($){

	$( '[data-registration-form]' ).implement(
		EasySocial.Controller.Registrations.Form ,
		{
			"previousLink"	: "<?php echo FRoute::registration( array( 'layout' => 'steps' , 'step' => ( $currentStep - 1 ) ) , false );?>"
		}
	);

});
