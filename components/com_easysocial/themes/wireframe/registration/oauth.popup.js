<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

// Reload parent's window
window.opener.location 	= "<?php echo $redirect;?>";

// Close current popup.
// window.close();


// this timeout is to fix window close issue in chrome.
setTimeout(function(){
    window.close();
}, 1);

