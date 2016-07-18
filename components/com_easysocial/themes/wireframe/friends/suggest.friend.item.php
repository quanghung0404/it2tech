<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<img src="<?php echo $user->getAvatar();?>" width="16" height="16" /> <?php echo $user->getName();?>
<input type="hidden" name="<?php echo $inputName;?>" value="<?php echo $user->id;?>" />
