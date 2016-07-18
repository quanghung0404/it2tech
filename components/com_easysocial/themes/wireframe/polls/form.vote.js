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
.script( 'site/polls/polls' )
.done(function($){
	$('[data-polls-vote]').implement("EasySocial.Controller.Polls.Vote", {
        "isMultiple" : <?php echo ($poll->multiple) ? 'true' : 'false'; ?>
    });
});
